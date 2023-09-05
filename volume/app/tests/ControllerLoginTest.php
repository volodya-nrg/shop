<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

final class ControllerLoginTest extends TestCase
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
        $req = new RequestLogin(randomString(10), randomString(PassMinLen - 1));
        $fnTpl = function (int $expectedCode, RequestLogin $req, MyResponse $resp, int $countData): void {
            $this->assertEquals(ViewPageLogin, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };

        // GET 200
        $this->client->login(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 0);

            $_POST[FieldEmail] = $req->getEmail();
            $_POST[FieldPassword] = $req->getPass();

            // е-мэйл не верен, будет ошибка
        })->login(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 2);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setEmail(randomEmail());
            $_POST[FieldEmail] = $req->getEmail();

            // пароль не верен, будет ошибка
        })->login(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 2);
            $this->assertEquals(ErrPassIsShort, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setPass(randomString(PassMinLen));
            $_POST[FieldPassword] = $req->getPass();

            // ok
        })->login(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 1);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
        })->run();
    }
}
