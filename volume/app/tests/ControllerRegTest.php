<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";

class ControllerRegTest extends TestCase
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
        $requestedPassConfirm = randomString(PassMinLen - 1);
        $requestedAgreement = randomString(2);
        $requestedPrivatePolicy = randomString(2);
        $tplFn = function (Response $response, int $code) {
            $this->assertEquals(ViewPageReg, $response->getViewName());
            $this->assertEquals($code, $response->getHttpCode());
        };

        $this->client->reg(function (Response $response) use ($tplFn, &$requestedEmail, &$requestedPass, &$requestedPassConfirm) { // Ok. Простая загрузка страницы
            $tplFn($response, 200);
            $this->assertCount(0, $response->data);

            $_POST[FieldEmail] = $requestedEmail; // зададим не правильный е-мэйл
            $_POST[FieldPassword] = $requestedPass; // зададим короткий пароль
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm; // зададим короткий пароль и другой
        })->reg(function (Response $response) use ($tplFn, &$requestedEmail, &$requestedPass, &$requestedPassConfirm) { // Err. Не правильный е-мэйл и пароли
            $tplFn($response, 400);
            $this->assertCount(4, $response->data[FieldErrors]);
            $this->assertEquals(ErrEmailNotCorrect, $response->data[FieldErrors][0]);
            $this->assertEquals(ErrPassIsShort, $response->data[FieldErrors][1]);
            $this->assertEquals(ErrAcceptAgreement, $response->data[FieldErrors][2]);
            $this->assertEquals(ErrAcceptPrivatePolicy, $response->data[FieldErrors][3]);

            $requestedEmail = randomEmail();
            $requestedPass = randomString(PassMinLen);
            $requestedPassConfirm = randomString(PassMinLen);
            $_POST[FieldEmail] = $requestedEmail; // зададим правильный е-мэйл
            $_POST[FieldPassword] = $requestedPass; // зададим валидиный пароль
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm; // зададим валидный пароль, но другой
        })->reg(function (Response $response) use ($tplFn, &$requestedPass, &$requestedPassConfirm) { // Err. Пароли валидные, но разные и не хватает чекбоксов
            $tplFn($response, 400);
            $this->assertCount(3, $response->data[FieldErrors]);
            $this->assertEquals(ErrPasswordsNotEqual, $response->data[FieldErrors][0]);
            $this->assertEquals(ErrAcceptAgreement, $response->data[FieldErrors][1]);
            $this->assertEquals(ErrAcceptPrivatePolicy, $response->data[FieldErrors][2]);

            $requestedPassConfirm = $requestedPass;
            $_POST[FieldPasswordConfirm] = $requestedPassConfirm; // зададим валидный пароль
        })->reg(function (Response $response) use ($tplFn, &$requestedAgreement, &$requestedPrivatePolicy) { // Err. Не хватает чекбоксов
            $tplFn($response, 400);
            $this->assertCount(2, $response->data[FieldErrors]);
            $this->assertEquals(ErrAcceptAgreement, $response->data[FieldErrors][0]);
            $this->assertEquals(ErrAcceptPrivatePolicy, $response->data[FieldErrors][1]);

            $_POST[FieldAgreement] = $requestedAgreement; // зададим подтверждение соглашения
            $_POST[FieldPrivacyPolicy] = $requestedPrivatePolicy; // зададим подтверждение приват. инф-ии
        })->reg(function (Response $response) use ($tplFn) { // Ok
            $tplFn($response, 200);
            $this->assertArrayNotHasKey(FieldErrors, $response->data);
        })->run();
    }
}
