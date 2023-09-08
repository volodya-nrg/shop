<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

final class ControllerRecoverCheckerTest extends TestCase
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
        $a = 5;
    }

    public function testIndex(): void
    {
        $req = new RequestRecoverChecker(randomString(PassMinLen - 1), randomString(10));
        $fnTpl = function (int $expectedCode, RequestRecoverChecker $req, MyResponse $resp, int $countData) {
            $this->assertEquals(ViewPageRecoverChecker, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };

        // GET 200
        $this->client->recoverChecker(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 0);

            $_GET[FieldHash] = randomString();

            // подкинем hash, e-mail появится в data-е
        })->recoverChecker(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 1);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldEmail]) > 0);

            $_POST[FieldPassword] = $req->getPass();
            $_POST[FieldPasswordConfirm] = $req->getPassConfirm();

            // пароль короткий, будет ошибка
        })->recoverChecker(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 2);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldEmail]) > 0);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);

            $req->setPass(randomString(PassMinLen));
            $req->setPassConfirm(randomString(PassMinLen));
            $_POST[FieldPassword] = $req->getPass();
            $_POST[FieldPasswordConfirm] = $req->getPassConfirm();

            // пароли не верны между собой, будет ошибка
        })->recoverChecker(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 2);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldEmail]) > 0);
            $this->assertEquals(ErrPasswordsNotEqual, $resp->data[FieldError]);

            $req->setPassConfirm($req->getPass());
            $_POST[FieldPasswordConfirm] = $req->getPassConfirm();

            // ok
        })->recoverChecker(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 2);
            $this->assertArrayHasKey(FieldEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldEmail]) > 0);
            $this->assertArrayHasKey(FieldSuccess, $resp->data);
            $this->assertEquals(DicPasswordChangedSuccessfully, $resp->data[FieldSuccess]);
        })->run();
    }
}
