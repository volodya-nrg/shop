<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . "/../init.php";
require_once dirname(__FILE__) . "/helper.php";

final class ControllerAdmTest extends TestCase
{
    private TestApiClient $client;

    protected function setUp(): void
    {
        $this->client = new TestApiClient();
        $_SERVER[FieldModeIsTest] = true;
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
    }

    public function testIndex(): void
    {
        $req = new RequestLogin();
        $fnTpl = function (int $expectedCode, MyResponse $resp, int $countData): void {
            $this->assertEquals(ViewPageAdm, $resp->getViewName());
            $this->assertEquals($expectedCode, $resp->getHttpCode());
            $this->assertCount($countData, $resp->data);

            if ($expectedCode >= 200 && $expectedCode < 300) {
                $this->assertArrayNotHasKey(FieldError, $resp->data);
            } else {
                $this->assertArrayHasKey(FieldError, $resp->data);
            }
        };
        $fnTpl401 = function (MyResponse $resp): void {
            $this->assertEquals(ViewPageAccessDined, $resp->getViewName());
            $this->assertEquals(401, $resp->getHttpCode());
            $this->assertCount(1, $resp->data);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
        };
        $password = "12345";
        $profile = getRandomUser($password);
        $admin = getRandomUser($password, "admin");

        // открываем страницу под гостем
        $this->client->adm(function (MyResponse $resp) use ($fnTpl401) {
            $fnTpl401($resp);

            // создадим админа
        })->createOrUpdateProfile($admin, function (MyResponse $resp) use ($req, $admin, $password) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($admin->email);
            $req->setPass($password);

            // аунтентифицируемся под админом
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $this->assertEquals(200, $resp->getHttpCode());

            // зайдем еще раз на страницу
        })->adm(function (MyResponse $resp) use ($fnTpl) {
            $fnTpl(200, $resp, 0);

            // выйдем
        })->logout(function (MyResponse $resp) {
            $this->assertEquals(200, $resp->getHttpCode());

            // создадим профиля
        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile, $password) {
            $this->assertEquals(200, $resp->getHttpCode());

            $req->setEmail($profile->email);
            $req->setPass($password);

            // аунтентифицируемся под профилем
        })->login($req, function (MyResponse $resp) use ($fnTpl, $req) {
            $this->assertEquals(200, $resp->getHttpCode());

            // зайдем еще раз на страницу, будет ошибка
        })->adm(function (MyResponse $resp) use ($fnTpl401) {
            $fnTpl401($resp);
        })->run();
    }
}
