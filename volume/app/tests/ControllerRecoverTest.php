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

        $reqForUser = new RequestReg();
        $reqForUser->email = randomEmail();
        $reqForUser->pass = randomString(PassMinLen);
        $reqForUser->passConfirm = $reqForUser->pass;
        $reqForUser->agreement = true;
        $reqForUser->privatePolicy = true;

        // откроем страницу
        $this->client->recover(null, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecover);

            $req->email = randomString(10);

            // пошлем не правильный е-мэйл, будет ошибка
        })->recover($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecover);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertTrue(isset($resp->data[FieldRequestedEmail]) && strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->email = randomEmail();

            // пользователь не найден, будет ошибка
        })->recover($req, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecover);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);

            // зарегистрируем пользователя (е-мэйл не подтвержден)
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($req, $reqForUser) {
            checkBasicData($this, 200, $resp, 2, ViewPageReg);

            $_GET[FieldHash] = $resp->data[FieldHash];
            $req->email = $reqForUser->email;

            // попробуем еще раз совершить запрос, будет ошибка, т.к. е-мэйл не подтвержден
        })->recover($req, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecover);
            $this->assertEquals(ErrCheckYourEmail, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);

            // подтвердим ему е-мэйл
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageRegCheck);

            // пошлем корректные данные
        })->recover($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 2, ViewPageRecover);
            $this->assertArrayHasKey(FieldDataSendMsg, $resp->data);
            $this->assertEquals(sprintf(DicRecoverDataSendMsgTpl, $req->email), $resp->data[FieldDataSendMsg]);
            $this->assertArrayHasKey(FieldHash, $resp->data);
        })->run();
    }

    public function testCheck(): void
    {
        $reqForRecoverCheck = new RequestRecoverCheck();
        $reqForRecoverCheck->pass = randomString(PassMinLen - 1);
        $reqForRecoverCheck->passConfirm = randomString(10);

        $reqForUser = new RequestReg();
        $reqForUser->email = randomEmail();
        $reqForUser->pass = randomString(PassMinLen);
        $reqForUser->passConfirm = $reqForUser->pass;
        $reqForUser->agreement = true;
        $reqForUser->privatePolicy = true;

        $reqForRecover = new RequestRecover();
        $reqForRecover->email = $reqForUser->email;

            // открываем страницу
        $this->client->recoverCheck(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecoverCheck);

            $_GET[FieldHash] = randomString();

            // подкинем не валидный hash, откроется страница в обычном режиме. Т.к. хеша такого нет.
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecoverCheck);

            // зарегистриуем полностью пользователя
        })->reg($reqForUser, "", true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, ViewPageReg);

            // от пользователя отправим данные для восстановления пароля
        })->recover($reqForRecover, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, ViewPageRecover);

            $_GET[FieldHash] = $resp->data[FieldHash];

            // подкидываем валидный хеш, но пароль короткий, будет ошибка
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) use ($reqForUser, $reqForRecoverCheck) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecoverCheck);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals($reqForUser->email, $resp->data[FieldEmail]);

            $reqForRecoverCheck->pass = randomString(PassMinLen);

            // пароли не верны между собой, будет ошибка
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) use ($reqForRecoverCheck) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecoverCheck);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);

            $reqForRecoverCheck->passConfirm = $reqForRecoverCheck->pass;

            // ok
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageRecoverCheck);
            $this->assertArrayHasKey(FieldSuccess, $resp->data);
            $this->assertEquals(DicPasswordChangedSuccessfully, $resp->data[FieldSuccess]);
        })->run();
    }
}
