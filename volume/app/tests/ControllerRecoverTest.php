<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

class ControllerRecoverTest extends TestCase
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
        $requestedEmail = randomString(10);
        $tplFn = function (Response $response, int $code) {
            $this->assertEquals(ViewPageRecover, $response->getViewName());
            $this->assertEquals($code, $response->getHttpCode());
        };

        $this->client->recover(function (Response $response) use ($tplFn, &$requestedEmail) { // Ok
            $tplFn($response, 200);
            $this->assertCount(0, $response->data);

            $_POST[FieldEmail] = $requestedEmail; // зададим не правильный е-мэйл
        })->recover(function (Response $response) use ($tplFn, &$requestedEmail) { // Err
            $tplFn($response, 400);
            $this->assertCount(1, $response->data[FieldErrors]);
            $this->assertEquals(ErrEmailNotCorrect, $response->data[FieldErrors][0]);

            $requestedEmail = randomEmail();
            $_POST[FieldEmail] = $requestedEmail;
        })->recover(function (Response $response) use ($tplFn, &$requestedEmail) { // Ok
            $tplFn($response, 200);
            $this->assertArrayNotHasKey(FieldErrors, $response->data);
            $this->assertEquals(sprintf(DicRecoverDataSendMsgTpl, $requestedEmail), $response->data[FieldDataSendMsg]);
        })->run();
    }
}
