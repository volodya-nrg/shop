<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

class ControllerRecoverCheckerTest extends TestCase
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
        $hash = randomString();
        $requestedPass = randomString(PassMinLen-1);
        $requestedPassConfirm = randomString(PassMinLen-1);
        $tplFn = function (Response $response, int $code) {
            $this->assertEquals(ViewPageRecoverChecker, $response->getViewName());
            $this->assertEquals($code, $response->getHttpCode());
        };

        $this->client->recoverChecker(function (Response $response) use ($tplFn, $hash) { // Ok. Открываем просто страницу
            $tplFn($response, 200);
            $this->assertCount(1, $response->data);
            $this->assertEquals("", $response->data[FieldEmail]);

            $_GET[FieldHash] = $hash;
        })->recoverChecker(function (Response $response) use ($tplFn, $hash, &$requestedPass, &$requestedPassConfirm) { // Ok. Прислали хеш и он есть, соответственно и е-мэйл
            $tplFn($response, 200);
            $this->assertCount(1, $response->data);
            $this->assertNotEmpty($response->data[FieldEmail]);

            $_POST[FieldPassword] = $requestedPass;
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm;
        })->recoverChecker(function (Response $response) use ($tplFn, $hash, &$requestedPass, &$requestedPassConfirm) { // Err. Изменяют пароль, оба пароля разные и короткие
            $tplFn($response, 400);
            $this->assertCount(2, $response->data);
            $this->assertCount(1, $response->data[FieldErrors]);
            $this->assertEquals(ErrPassIsShort, $response->data[FieldErrors][0]);

            $requestedPass = randomString(PassMinLen);
            $requestedPassConfirm = randomString(PassMinLen);
            $_POST[FieldPassword] = $requestedPass;
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm;
        })->recoverChecker(function (Response $response) use ($tplFn, $hash, &$requestedPass, &$requestedPassConfirm) { // Err. Изменяют пароль, оба пароля валидные, но разные
            $tplFn($response, 400);
            $this->assertCount(2, $response->data);
            $this->assertCount(1, $response->data[FieldErrors]);
            $this->assertEquals(ErrPasswordsNotEqual, $response->data[FieldErrors][0]);

            $requestedPassConfirm = $requestedPass;
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm;
        })->recoverChecker(function (Response $response) use ($tplFn) { // Ok. Все хорошо
            $tplFn($response, 200);
            $this->assertCount(2, $response->data);
            $this->assertArrayNotHasKey(FieldErrors, $response->data);
            $this->assertNotEmpty($response->data[FieldEmail]);
            $this->assertEquals(DicPasswordChangedSuccessfully, $response->data[FieldSuccess]);
        })->run();
    }
}
