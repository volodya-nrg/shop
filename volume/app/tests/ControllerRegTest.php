<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerRegTest extends TestCase
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
        $reqForUser = new RequestReg();
        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        // открываем страницу
        $this->client->reg(null, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageReg);

            $reqForUser->email = randomString(10);
            $reqForUser->pass = randomString(PassMinLen - 1);
            $reqForUser->passConfirm = randomString(PassMinLen);
            $reqForUser->agreement = false;
            $reqForUser->privatePolicy = false;

            // e-mail не правильный, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::EmailNotCorrect->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            $reqForUser->email = randomEmail();

            // пароль не верный, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen), $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            $reqForUser->pass = randomString(PassMinLen);

            // пароли не равны между собой, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::PasswordsNotEqual->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            $reqForUser->passConfirm = $reqForUser->pass;

            // не выбран agreement, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::AcceptAgreement->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            $reqForUser->agreement = true;

            // не выбран privatePolicy, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::AcceptPrivatePolicy->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            $reqForUser->privatePolicy = true;

            // успешная регистрация пользователя, е-мэйл не подтвержден
        })->reg($reqForUser, "", false, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageReg);
            $this->assertArrayHasKey(EnumField::Hash->value, $resp->data);
            $this->assertArrayHasKey(EnumField::UserId->value, $resp->data);
            $this->assertTrue(strlen($resp->data[EnumField::Hash->value]) > 0);
            $this->assertGreaterThan(0, $resp->data[EnumField::UserId->value]);

            // еще раз попробуем зарегистрировать того же самого пользователя, будет ошибка
        })->reg($reqForUser, "", false, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::CheckYourEmail->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);

            // успешная регистрация админа
        })->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageReg);
            $this->assertArrayHasKey(EnumField::Hash->value, $resp->data);
            $this->assertArrayHasKey(EnumField::UserId->value, $resp->data);
            $this->assertTrue(strlen($resp->data[EnumField::Hash->value]) > 0);
            $this->assertGreaterThan(0, $resp->data[EnumField::UserId->value]);

            // еще раз зарегистрируем админа, будет ошибка - такой пользователь уже есть с подтвержденным е-мэйлом
        })->reg($reqForAdmin, "", false, function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 4, EnumViewFile::PageReg);
            $this->assertEquals(EnumErr::UserAlreadyHas->value, $resp->data[EnumField::Error->value]);
            $this->assertArrayHasKey(EnumField::RequestedEmail->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedAgreement->value, $resp->data);
            $this->assertArrayHasKey(EnumField::RequestedPrivatePolicy->value, $resp->data);
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
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageRegCheck);

            $_GET[EnumField::Hash->value] = "x";

            // подкинем не верный хеш, будет ошибка
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 400, $resp, 1, EnumViewFile::PageRegCheck);
            $this->assertEquals(EnumErr::NotFoundRow->value, $resp->data[EnumField::Error->value]);

            // зарегистрируем пользователя
        })->reg($req, "", false, function (MyResponse $resp) use ($req) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageReg);
            $this->assertTrue(strlen($resp->data[EnumField::Hash->value]) > 0);

            $_GET[EnumField::Hash->value] = $resp->data[EnumField::Hash->value];

            // проверим хеш, ok
        })->regCheck(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageRegCheck);
            $this->assertArrayHasKey(EnumField::Msg->value, $resp->data);
            $this->assertEquals(EnumDic::EmailSuccessfullyConfirmed->value, $resp->data[EnumField::Msg->value]);
        })->run();
    }
}
