<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerRecoverTest extends TestCase
{
    private TestApiClient $client;

    protected function setUp(): void
    {
        $this->client = new TestApiClient();
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
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageRecover);

            $req->email = randomString(10);

            // пошлем не правильный е-мэйл, будет ошибка
        })->recover($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageRecover);
            $this->assertEquals(EnumErr::EmailNotCorrect->value, $resp->data[EnumField::Error->value]);
            $this->assertTrue(isset($resp->data[EnumField::RequestedEmail->value]) && strlen($resp->data[EnumField::RequestedEmail->value]) > 0);

            $req->email = randomEmail();

            // пользователь не найден, будет ошибка
        })->recover($req, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageRecover);
            $this->assertEquals(EnumErr::NotFoundRow->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);

            // зарегистрируем пользователя (е-мэйл не подтвержден)
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($req, $reqForUser) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageReg);

            $_GET[EnumField::Hash->value] = $resp->data[EnumField::Hash->value];
            $req->email = $reqForUser->email;

            // попробуем еще раз совершить запрос, будет ошибка, т.к. е-мэйл не подтвержден
        })->recover($req, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageRecover);
            $this->assertEquals(EnumErr::CheckYourEmail->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);

            // подтвердим ему е-мэйл
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageRegCheck);

            // пошлем корректные данные
        })->recover($req, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageRecover);
            $this->assertArrayHasKey(EnumField::DataSendMsg->value, $resp->data);
            $this->assertEquals(sprintf(EnumDic::RecoverDataSendMsgTpl->value, $req->email), $resp->data[EnumField::DataSendMsg->value]);
            $this->assertArrayHasKey(EnumField::Hash->value, $resp->data);
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
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageRecoverCheck);

            $_GET[EnumField::Hash->value] = randomString();

            // подкинем не валидный hash, откроется страница в обычном режиме. Т.к. хеша такого нет.
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageRecoverCheck);

            // зарегистриуем полностью пользователя
        })->reg($reqForUser, "", true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageReg);

            // от пользователя отправим данные для восстановления пароля
        })->recover($reqForRecover, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageRecover);

            $_GET[EnumField::Hash->value] = $resp->data[EnumField::Hash->value];

            // подкидываем валидный хеш, но пароль короткий, будет ошибка
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) use ($reqForUser, $reqForRecoverCheck) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageRecoverCheck);
            $this->assertEquals(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen), $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::Email->value, $resp->data);
            $this->assertEquals($reqForUser->email, $resp->data[EnumField::Email->value]);

            $reqForRecoverCheck->pass = randomString(PassMinLen);

            // пароли не верны между собой, будет ошибка
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) use ($reqForRecoverCheck) {
            checkBasicData($this, 400, $resp, 2, EnumViewFile::PageRecoverCheck);
            $this->assertArrayHasKey(EnumField::Email->value, $resp->data);
            $this->assertEquals(EnumErr::PasswordsNotEqual->value, $resp->data[EnumField::Error->value]);

            $reqForRecoverCheck->passConfirm = $reqForRecoverCheck->pass;

            // ok
        })->recoverCheck($reqForRecoverCheck, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageRecoverCheck);
            $this->assertArrayHasKey(EnumField::Success->value, $resp->data);
            $this->assertEquals(EnumDic::PasswordChangedSuccessfully->value, $resp->data[EnumField::Success->value]);
        })->run();
    }
}
