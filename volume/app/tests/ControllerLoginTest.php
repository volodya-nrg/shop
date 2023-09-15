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
        $password = "12345";
        $passwordWrong = "54321";
        $profile = getRandomUser($password);
        $admin = getRandomUser($password, "admin");

        // открываем страницу
        $this->client->login(null, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

            $req->setEmail(randomString(10));
            $req->setPass(randomString(PassMinLen - 1));

            // е-мэйл не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageLogin);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setEmail(randomEmail());

            // пароль не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageLogin);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setPass(randomString(PassMinLen));

            // пользователь не найден, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageLogin);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);

            // создадим пользователя
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile, $passwordWrong) {
            checkBasicData($this, 200, $resp, 0);

            $req->setEmail($profile->email);
            $req->setPass($passwordWrong);

            // аунтентификация под профилем с не верным паролем, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req, $password) {
            checkBasicData($this, 400, $resp, 2, ViewPageLogin);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertEquals(ErrLoginOrPasswordNotCorrect, $resp->data[FieldError]);

            $req->setPass($password);

            // аунтентификация под профилем с верным паролем, ok
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
            $this->assertArrayHasKey(FieldProfile, $_SESSION);
            $this->assertArrayNotHasKey(FieldAdmin, $_SESSION);

            // выйдем
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

            // создадим админа
        })->createOrUpdateProfile($admin, function (MyResponse $resp) use ($req, $admin, $password) {
            checkBasicData($this, 200, $resp, 0);

            $req->setEmail($admin->email);
            $req->setPass($password);

            // аунтентификация под админом
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
            $this->assertArrayHasKey(FieldProfile, $_SESSION);
            $this->assertArrayHasKey(FieldAdmin, $_SESSION);
        })->run();
    }
}
