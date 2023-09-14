<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerRecoverTest extends TestCase
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
        $req = new RequestRecover();
        $fnTpl = function (int $expectedCode, MyResponse $resp, int $countData) {
            $this->assertEquals(ViewPageRecover, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
                $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
                $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
            }
        };
        $password = "12345";
        $profile = getRandomUser($password);

        // открываем страницу
        $this->client->recover(null, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $resp, 0);

            $req->setEmail(randomString(10));

            // е-мэйл не правильный, будет ошибка
        })->recover($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 2);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);

            $req->setEmail(randomEmail());

            // пользователь не найден, будет ошибка
        })->recover($req, function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(400, $resp, 2);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);

            // ok
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($profile->email);

            // ok
        })->recover($req, function (MyResponse $resp) use ($fnTpl, $profile) {
            $fnTpl(200, $resp, 2);
            $this->assertArrayHasKey(FieldDataSendMsg, $resp->data);
            $this->assertEquals(sprintf(DicRecoverDataSendMsgTpl, $profile->email), $resp->data[FieldDataSendMsg]);
            $this->assertArrayHasKey(FieldHash, $resp->data);
        })->run();
    }

    public function testCheck(): void
    {
        $reqForRecover = new RequestRecover();
        $req = new RequestRecoverCheck(randomString(PassMinLen - 1), randomString(10));
        $fnTpl = function (int $expectedCode, MyResponse $resp, int $countData) {
            $this->assertEquals(ViewPageRecoverCheck, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };
        $password = "12345";
        $profile = getRandomUser($password);

        // открываем страницу
        $this->client->recoverCheck(null, function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(200, $resp, 0);

            $_GET[FieldHash] = randomString();

            // подкинем не валидный hash, откроется страница в обычном режиме
        })->recoverCheck($req, function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(200, $resp, 0);

            // создадим профиль
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($reqForRecover, $profile) {
            $this->assertEquals(200, $resp->getHttpCode());

            $reqForRecover->setEmail($profile->email);

            // от профиля отправим данные для восстановления пароля
        })->recover($reqForRecover, function (MyResponse $resp) {
            $this->assertEquals(200, $resp->getHttpCode());
            $this->assertCount(2, $resp->data);
            $this->assertArrayHasKey(FieldHash, $resp->data);

            $_GET[FieldHash] = $resp->data[FieldHash];

            // подкидываем валидный хеш, но пароль короткий, будет ошибка
        })->recoverCheck($req, function (MyResponse $resp) use ($fnTpl, $req, $profile) {
            $fnTpl(400, $resp, 2);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals($profile->email, $resp->data[FieldEmail]);

            $req->setPass(randomString(PassMinLen));

            // пароли не верны между собой, будет ошибка
        })->recoverCheck($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 2);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);

            $req->setPassConfirm($req->getPass());

            // ok
        })->recoverCheck($req, function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(200, $resp, 1);
            $this->assertArrayHasKey(FieldSuccess, $resp->data);
            $this->assertEquals(DicPasswordChangedSuccessfully, $resp->data[FieldSuccess]);
        })->run();
    }
}
