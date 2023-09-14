<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerRegTest extends TestCase
{
    private TestApiClient $client;

    protected function setUp(): void
    {
        $this->client = new TestApiClient();
        $_SERVER[FieldModeIsTest] = true;
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
    }

    public function testIndex(): void
    {
        $req = new RequestReg();
        $fnTpl = function (int $expectedCode, MyResponse $resp, int $countData): void {
            $this->assertEquals(ViewPageReg, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
                $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
                $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
                $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);
            }
        };
        $password = "12345";
        $profile = getRandomUser($password);
        $profile->emailHash = "x";

        // открываем страницу
        $this->client->reg(null, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $resp, 0);

            $req->setEmail(randomString(10));
            $req->setPass(randomString(PassMinLen - 1));
            $req->setPassConfirm(randomString(PassMinLen));
            $req->setAgreement(false);
            $req->setPrivatePolicy(false);

            // e-mail не правильный, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);

            $req->setEmail(randomEmail());

            // пароль не верный, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);

            $req->setPass(randomString(PassMinLen));

            // пароли не равны между собой, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);

            $req->setPassConfirm($req->getPass());

            // не выбран agreement, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrAcceptAgreement, $resp->data[FieldError]);

            $req->setAgreement(true);

            // не выбран privatePolicy, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrAcceptPrivatePolicy, $resp->data[FieldError]);

            $req->setPrivatePolicy(true);

            // создадим профиль с не подтвержденным е-мэйлом
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($profile->email);

            // пользователь с таким е-мэйлом уже есть, е-мэйл не подтвержден, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $profile) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrCheckYourEmail, $resp->data[FieldError]);

            $profile->emailHash = "";

            // обновим профиль (подтверждаем е-мэйл)
        })->createOrUpdateProfile($profile, function (MyResponse $resp) {
            $this->assertEquals(200, $resp->getHttpCode());

            // пользователь с таким е-мэйлом уже есть, е-мэйл подтвержден, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 4);
            $this->assertEquals(ErrUserAlreadyHas, $resp->data[FieldError]);

            $req->setEmail(randomEmail());

            // успешная регистрация
        })->reg($req, function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(200, $resp, 0);
        })->run();
    }
}
