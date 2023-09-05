<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

final class ControllerRecoverTest extends TestCase
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
        $req = new RequestRecover(randomString(10));
        $fnTpl = function (int $expectedCode, RequestRecover $req, MyResponse $resp, int $countData) {
            $this->assertEquals(ViewPageRecover, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };

        // GET 200
        $this->client->recover(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 0);

            $_POST[FieldEmail] = $req->getEmail();

            // е-мэйл не правильный, будет ошибка
        })->recover(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(400, $req, $resp, 2);
            $this->assertEquals(ErrEmailNotCorrect, $resp->data[FieldError]);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);

            $req->setEmail(randomEmail());
            $_POST[FieldEmail] = $req->getEmail();

            // ok
        })->recover(function (MyResponse $resp) use ($fnTpl, $req) {
            $fnTpl(200, $req, $resp, 2);
            $this->assertArrayHasKey(FieldRequestedEmail, $resp->data);
            $this->assertTrue(strlen($resp->data[FieldRequestedEmail]) > 0);
            $this->assertArrayHasKey(FieldDataSendMsg, $resp->data);

            $msg = sprintf(DicRecoverDataSendMsgTpl, $resp->data[FieldRequestedEmail]);
            $this->assertEquals($msg, $resp->data[FieldDataSendMsg]);
        })->run();
    }
}
