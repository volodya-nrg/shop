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
        $password = "12345";
        $profile = getRandomUser($password);

        // открываем страницу
        $this->client->recover(null, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecover);

            $req->setEmail(randomString(10));

            // е-мэйл не правильный, будет ошибка
        })->recover($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecover);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertTrue(isset($resp->data[FieldRequestedEmail]) && strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setEmail(randomEmail());

            // пользователь не найден, будет ошибка
        })->recover($req, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecover);
            $this->assertEquals(ErrNotFoundUser, $resp->data[FieldError]);
            $this->assertTrue(isset($resp->data[FieldRequestedEmail]) && strlen($resp->data[FieldRequestedEmail]) > 0);

            // ok
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile) {
            checkBasicData($this, 200, $resp, 0);

            $req->setEmail($profile->email);

            // ok
        })->recover($req, function (MyResponse $resp) use ($profile) {
            checkBasicData($this, 200, $resp, 2, ViewPageRecover);
            $this->assertArrayHasKey(FieldDataSendMsg, $resp->data);
            $this->assertEquals(sprintf(DicRecoverDataSendMsgTpl, $profile->email), $resp->data[FieldDataSendMsg]);
            $this->assertArrayHasKey(FieldHash, $resp->data);
        })->run();
    }

    public function testCheck(): void
    {
        $reqForRecover = new RequestRecover();
        $req = new RequestRecoverCheck(randomString(PassMinLen - 1), randomString(10));
        $password = "12345";
        $profile = getRandomUser($password);

        // открываем страницу
        $this->client->recoverCheck(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecoverCheck);

            $_GET[FieldHash] = randomString();

            // подкинем не валидный hash, откроется страница в обычном режиме
        })->recoverCheck($req, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageRecoverCheck);

            // создадим профиль
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($reqForRecover, $profile) {
            checkBasicData($this, 200, $resp, 0);

            $reqForRecover->setEmail($profile->email);

            // от профиля отправим данные для восстановления пароля
        })->recover($reqForRecover, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, ViewPageRecover);
            $this->assertArrayHasKey(FieldHash, $resp->data);

            $_GET[FieldHash] = $resp->data[FieldHash];

            // подкидываем валидный хеш, но пароль короткий, будет ошибка
        })->recoverCheck($req, function (MyResponse $resp) use ($req, $profile) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecoverCheck);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals($profile->email, $resp->data[FieldEmail]);

            $req->setPass(randomString(PassMinLen));

            // пароли не верны между собой, будет ошибка
        })->recoverCheck($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, ViewPageRecoverCheck);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);

            $req->setPassConfirm($req->getPass());

            // ok
        })->recoverCheck($req, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageRecoverCheck);
            $this->assertArrayHasKey(FieldSuccess, $resp->data);
            $this->assertEquals(DicPasswordChangedSuccessfully, $resp->data[FieldSuccess]);
        })->run();
    }
}
