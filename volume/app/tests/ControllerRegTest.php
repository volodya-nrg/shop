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
        $password = "12345";
        $profile = getRandomUser($password);
        $profile->emailHash = "x";

        // открываем страницу
        $this->client->reg(null, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageReg);

            $req->setEmail(randomString(10));
            $req->setPass(randomString(PassMinLen - 1));
            $req->setPassConfirm(randomString(PassMinLen));
            $req->setAgreement(false);
            $req->setPrivatePolicy(false);

            // e-mail не правильный, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setEmail(randomEmail());

            // пароль не верный, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setPass(randomString(PassMinLen));

            // пароли не равны между собой, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setPassConfirm($req->getPass());

            // не выбран agreement, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrAcceptAgreement, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setAgreement(true);

            // не выбран privatePolicy, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrAcceptPrivatePolicy, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setPrivatePolicy(true);

            // создадим профиль с не подтвержденным е-мэйлом
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile) {
            checkBasicData($this, 200, $resp, 0);

            $req->setEmail($profile->email);

            // пользователь с таким е-мэйлом уже есть, е-мэйл не подтвержден, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($profile) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrCheckYourEmail, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $profile->emailHash = "";

            // обновим профиль (подтверждаем е-мэйл)
        })->createOrUpdateProfile($profile, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

            // пользователь с таким е-мэйлом уже есть, е-мэйл подтвержден, будет ошибка
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrUserAlreadyHas, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setEmail(randomEmail());

            // успешная регистрация
        })->reg($req, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageReg);
            $this->assertArrayHasKey(FieldHash, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldHash]) > 0);
        })->run();
    }

    public function testCheck(): void
    {
        $pass = randomString(PassMinLen);
        $req = new RequestReg(randomEmail(), $pass, $pass, true, true);

        // открываем страницу
        $this->client->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRegCheck);

            $_GET[FieldHash] = "x";

            // подкинем не верный хеш, будет ошибка
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 1, ViewPageRegCheck);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);

            // зарегистрируем пользователя
        })->reg($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 1, ViewPageReg);
            $this->assertTrue(strlen($resp->data[FieldHash]) > 0);

            $_GET[FieldHash] = $resp->data[FieldHash];

            // проверим хеш, ok
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageRegCheck);
            $this->assertArrayHasKey(FieldMsg, $resp->data);
            $this->assertEquals(DicEmailSuccessfullyConfirmed, $resp->data[FieldMsg]);
        })->run();
    }
}
