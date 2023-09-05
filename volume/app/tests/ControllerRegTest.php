<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

final class ControllerRegTest extends TestCase
{
    private TestApiClient $client;

    protected function setUp(): void
    {
        $this->client = new TestApiClient();
        $_GET = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
    }

    public function testIndex(): void
    {
        $req = new RequestReg(randomString(10), randomString(PassMinLen - 1), randomString(PassMinLen), false, false);
        $fnTpl = function (int $expectedCode, RequestReg $req, MyResponse $resp, int $countData): void {
            $this->assertEquals(ViewPageReg, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };

        // GET 200
        $this->client->reg(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 0);

            $_POST[FieldEmail] = $req->getEmail();
            $_POST[FieldPassword] = $req->getPass();
            $_POST[FieldPasswordConfirm] = $req->getPassConfirm();
            $_POST[FieldAgreement] = $req->getAgreement();
            $_POST[FieldPrivacyPolicy] = $req->getPrivatePolicy();

            // e-mail не правильный, будет ошибка
        })->reg(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 4);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setEmail(randomEmail());
            $_POST[FieldEmail] = $req->getEmail();

            // пароль не верный, будет ошибка
        })->reg(function (MyResponse $resp) use (&$req, &$fnTpl) {
            $fnTpl(400, $req, $resp, 4);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            // зададим верный пароль
            $req->setPass(randomString(PassMinLen));
            $_POST[FieldPassword] = $req->getPass();

            // пароли не равны между собой, будет ошибка
        })->reg(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 4);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setPassConfirm($req->getPass());
            $_POST[FieldPasswordConfirm] = $req->getPassConfirm();

            // не выбран agreement, будет ошибка
        })->reg(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 4);
            $this->assertEquals(ErrAcceptAgreement, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setAgreement(true);
            $_POST[FieldAgreement] = "on";

            // не выбран privatePolicy, будет ошибка
        })->reg(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 4);
            $this->assertEquals(ErrAcceptPrivatePolicy, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);

            $req->setPrivatePolicy(true);
            $_POST[FieldPrivacyPolicy] = "on";

            // ok
        })->reg(function (MyResponse $resp) use (&$req, &$fnTpl) {
            $fnTpl(200, $req, $resp, 3);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertArrayHasKey(FieldRequestedAgreement, $resp->data);
            $this->assertArrayHasKey(FieldRequestedPrivatePolicy, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
            $this->assertTrue($resp->data[FieldRequestedAgreement]);
            $this->assertTrue($resp->data[FieldRequestedPrivatePolicy]);
        })->run();
    }
}
