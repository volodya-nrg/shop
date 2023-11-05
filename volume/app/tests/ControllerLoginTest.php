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
        $_SERVER[EnumField::ModeIsTest->value] = true;
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
    }

    public function testIndex(): void
    {
        $reqForUser = new RequestReg();
        $reqForUser->email = randomEmail();
        $reqForUser->pass = randomString(PassMinLen);
        $reqForUser->passConfirm = $reqForUser->pass;
        $reqForUser->agreement = true;
        $reqForUser->privatePolicy = true;

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $req = new RequestLogin();

        // открываем страницу
        $this->client->login(null, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            $req->email = randomString(10);
            $req->pass = randomString(PassMinLen - 1);

            // е-мэйл не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageLogin);
            $this->assertEquals(EnumErr::EmailNotCorrect->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertTrue(strlen($resp->data[EnumField::RequestedEmail->value]) > 0);

            $req->email = randomEmail();

            // пароль не верен, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageLogin);
            $this->assertEquals(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen), $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertTrue(strlen($resp->data[EnumField::RequestedEmail->value]) > 0);

            $req->pass = randomString(PassMinLen);

            // пользователь не найден, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageLogin);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertTrue(strlen($resp->data[EnumField::RequestedEmail->value]) > 0);
            $this->assertEquals(EnumErr::NotFoundUser->value, $resp->data[EnumField::Error->value]);

            // создадим пользователя
        })->reg($reqForUser, "", true, function (MyResponse $resp) use ($req, $reqForUser) {
            checkBasicData($this, 200, $resp, 2);

            $req->email = $reqForUser->email;
            $req->pass = randomString();

            // аунтентификация под профилем с не верным паролем, будет ошибка
        })->login($req, function (MyResponse $resp) use ($req, $reqForUser) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageLogin);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertEquals(EnumErr::LoginOrPasswordNotCorrect->value, $resp->data[EnumField::Error->value]);

            $req->pass = $reqForUser->pass;

            // аунтентификация под профилем с верным паролем, ok
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);
            $this->assertArrayHasKey(EnumField::Profile->value, $_SESSION);
            $this->assertArrayNotHasKey(EnumField::Admin->value, $_SESSION);

            // выйдем
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

            // создадим админа
        })->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) use ($req, $reqForAdmin) {
            checkBasicData($this, 200, $resp, 2);

            $req->email = $reqForAdmin->email;
            $req->pass = $reqForAdmin->pass;

            // аунтентификация под админом
        })->login($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);
            $this->assertArrayHasKey(EnumField::Profile->value, $_SESSION);
            $this->assertArrayHasKey(EnumField::Admin->value, $_SESSION);

            // выйдем
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->run();
    }
}
