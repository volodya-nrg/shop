<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerLoginTest extends TestCase
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
        $req = new RequestLogin();
        $fnTpl = function (int $expectedCode, MyResponse $resp, int $countData): void {
            $this->assertEquals(ViewPageLogin, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };
        $password = "12345";
        $passwordWrong = "54321";
        $profile = getRandomUser($password);
        $admin = getRandomUser($password, "admin");

        // открываем страницу
        $this->client->login(null, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $resp, 0);

            $req->setEmail(randomString(10));
            $req->setPass(randomString(PassMinLen - 1));

            // е-мэйл не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 2);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setEmail(randomEmail());

            // пароль не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 2);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setPass(randomString(PassMinLen));

            // пользователь не найден, будет ошибка
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $resp, 2);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);

            // создадим пользователя
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile, $passwordWrong) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($profile->email);
            $req->setPass($passwordWrong);

            // аунтентификация под профилем с не верным паролем, будет ошибка
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req, $password) {
            $fnTpl(400, $resp, 2);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertEquals(ErrLoginOrPasswordNotCorrect, $resp->data[FieldError]);

            $req->setPass($password);

            // аунтентификация под профилем с верным паролем, ok
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $resp, 0);
            $this->assertArrayHasKey(FieldProfile, $_SESSION);
            $this->assertArrayNotHasKey(FieldAdmin, $_SESSION);

            // выйдем
        })->logout(function (MyResponse $resp) {
            $this->assertEquals(200, $resp->getHttpCode());

            // создадим админа
        })->createOrUpdateProfile($admin, function (MyResponse $resp) use ($req, $admin, $password) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($admin->email);
            $req->setPass($password);

            // аунтентификация под админом
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $resp, 0);
            $this->assertArrayHasKey(FieldProfile, $_SESSION);
            $this->assertArrayHasKey(FieldAdmin, $_SESSION);
        })->run();
    }
}
