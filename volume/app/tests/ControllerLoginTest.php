<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

class ControllerLoginTest extends TestCase
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
        $requestedPass = randomString(PassMinLen - 1);
        $tplFn = function (Response $response, int $code) {
            $this->assertEquals(ViewPageLogin, $response->getViewName());
            $this->assertEquals($code, $response->getHttpCode());
        };

        $this->client->login(function (Response $response) use ($tplFn, &$requestedEmail, &$requestedPass) { // Ok. Просто открыли страницу
            $tplFn($response, 200);
            $this->assertCount(0, $response->data);

            $_POST[FieldEmail] = $requestedEmail; // зададим не правильный е-мэйл
            $_POST[FieldPassword] = $requestedPass; // зададим короткий пароль
        })->login(function (Response $response) use ($tplFn, &$requestedEmail) { // Err. Е-мэйл и пароль не валидны
            $tplFn($response, 400);
            $this->assertCount(2, $response->data[FieldErrors]);
            $this->assertEquals(ErrEmailNotCorrect, $response->data[FieldErrors][0]);
            $this->assertEquals(ErrPassIsShort, $response->data[FieldErrors][1]);

            $requestedEmail = randomEmail();
            $_POST[FieldEmail] = $requestedEmail;
        })->login(function (Response $response) use ($tplFn, &$requestedEmail, &$requestedPass) { // Err. Пароль не валидный
            $tplFn($response, 400);
            $this->assertCount(1, $response->data[FieldErrors]);
            $this->assertEquals(ErrPassIsShort, $response->data[FieldErrors][0]);
            $this->assertEquals($requestedEmail, $response->data[FieldRequestedEmail]);

            $requestedPass = randomString(10);
            $_POST[FieldPassword] = $requestedPass;
        })->login(function (Response $response) use ($tplFn) { // Ok
            $tplFn($response, 200);
            $this->assertArrayNotHasKey(FieldErrors, $response->data);
        })->run();
    }
}
