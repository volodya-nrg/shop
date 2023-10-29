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
        $reqForUser = new RequestReg();
        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        // открываем страницу
        $this->client->reg(null, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 200, $resp, 0, ViewPageReg);

            $reqForUser->email = randomString(10);
            $reqForUser->pass = randomString(PassMinLen - 1);
            $reqForUser->passConfirm = randomString(PassMinLen);
            $reqForUser->agreement = false;
            $reqForUser->privatePolicy = false;

            // e-mail не правильный, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $reqForUser->email = randomEmail();

            // пароль не верный, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $reqForUser->pass = randomString(PassMinLen);

            // пароли не равны между собой, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $reqForUser->passConfirm = $reqForUser->pass;

            // не выбран agreement, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrAcceptAgreement, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $reqForUser->agreement = true;

            // не выбран privatePolicy, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrAcceptPrivatePolicy, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $reqForUser->privatePolicy = true;

            // успешная регистрация пользователя, е-мэйл не подтвержден
        })->reg($reqForUser, "", false, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, ViewPageReg);
            $this->assertArrayHasKey(FieldHash, $resp->data);
            $this->assertArrayHasKey(FieldUserId, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldHash]) > 0);
            $this->assertGreaterThan(0, $resp->data[FieldUserId]);

            // еще раз попробуем зарегистрировать того же самого пользователя, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrCheckYourEmail, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            // успешная регистрация админа
        })->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, ViewPageReg);
            $this->assertArrayHasKey(FieldHash, $resp->data);
            $this->assertArrayHasKey(FieldUserId, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldHash]) > 0);
            $this->assertGreaterThan(0, $resp->data[FieldUserId]);

            // еще раз зарегистрируем админа, будет ошибка - такой пользователь уже есть с подтвержденным е-мэйлом
        })->reg($reqForAdmin, "", false, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 4, ViewPageReg);
            $this->assertEquals(ErrUserAlreadyHas, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);
        })->run();
    }

    public function testCheck(): void
    {
        $req = new RequestReg();
        $req->email = randomEmail();
        $req->pass = $req->passConfirm = randomString(PassMinLen);
        $req->agreement = true;
        $req->privatePolicy = true;

        // открываем страницу
        $this->client->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRegCheck);

            $_GET[FieldHash] = "x";

            // подкинем не верный хеш, будет ошибка
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 1, ViewPageRegCheck);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);

            // зарегистрируем пользователя
        })->reg($req, "", false, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 2, ViewPageReg);
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
