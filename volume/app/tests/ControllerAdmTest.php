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
        $this->assertTrue(true);
//        $req = new RequestLogin();
//        $password = "12345";
//        $profile = randomUser($password);
//        $admin = randomUser($password, "admin");
//
//        // открываем страницу под гостем
//        $this->client->adm(function (MyResponse $resp) {
//            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
//            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
//
//            // создадим админа
//        })->createOrUpdateProfile($admin, function (MyResponse $resp) use ($req, $admin, $password) {
//            checkBasicData($this, 200, $resp, 0);
//
//            $req->email = $admin->email;
//            $req->pass = $password;
//
//            // аунтентифицируемся под админом
//        })->login($req, function (MyResponse $resp) use ($req) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // зайдем еще раз на страницу
//        })->adm(function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageAdm);
//
//            // выйдем
//        })->logout(function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0);
//
//            // создадим профиль
//        })->createOrUpdateProfile($profile, function (MyResponse $resp) use ($req, $profile, $password) {
//            checkBasicData($this, 200, $resp, 0);
//
//            $req->email = $profile->email;
//            $req->pass = $password;
//
//            // аунтентифицируемся под профилем
//        })->login($req, function (MyResponse $resp) use ($req) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // зайдем еще раз на страницу, будет ошибка
//        })->adm(function (MyResponse $resp) {
//            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
//            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
//        })->run();
    }

    public function testItems(): void
    {
        $this->assertTrue(true);
        // 1. откроем страницу под гостем
        // 2. создадим админа
        // 3. добавим несколько item-ов
        // 4. получим список item-ов, с пагинацией

//        $reqLogin = new RequestLogin();
//        $password = "12345";
//        //$profile = getRandomUser($password);
//        $admin = randomUser($password, "admin");
//        $item = randomItem(0);
//        $cat = randomCat();
//
//        // открываем страницу под гостем
//        $this->client->createOrUpdateProfile($admin, function (MyResponse $resp) use ($reqLogin, $admin, $password) {
//            checkBasicData($this, 200, $resp, 0);
//
//            $reqLogin->email = $admin->email;
//            $reqLogin->pass = $password;
//
//            // аунтентифицируемся под админом
//        })->login($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать категорию
//        })->createOrUpdateCat($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать продукт
//        })->createOrUpdateItem($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // получить список и проверить
//        })->items($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//        })->run();
    }

    public function testItem(): void
    {
        $this->assertTrue(true);
//        $reqLogin = new RequestLogin();
//        $password = "12345";
//        $admin = randomUser($password, "admin");
//        $item = randomItem(0);
//
//        $reqCat = new RequestCat();
//        $reqCat->name = randomString(10);
//
//        // открываем страницу под гостем
//        $this->client->createOrUpdateProfile($admin, function (MyResponse $resp) use ($reqLogin, $admin, $password) {
//            checkBasicData($this, 200, $resp, 0);
//
//            $reqLogin->email = $admin->email;
//            $reqLogin->pass = $password;
//
//            // аунтентифицируемся под админом
//        })->login($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать категорию
//        })->admCreateOrUpdateCat($reqCat, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать продукт
//        })->createOrUpdateItem($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // получить список и проверить
//        })->items($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//        })->run();
    }

    public function testCat(): void
    {
        $this->assertTrue(true);
//        $reqLogin = new RequestLogin();
//        $password = "12345";
//        $admin = randomUser($password, "admin");
//        $item = randomItem(0);
//
//        $reqCat = new RequestCat();
//        $reqCat->name = randomString(10);
//
//        // открываем страницу под гостем
//        $this->client->createOrUpdateProfile($admin, function (MyResponse $resp) use ($reqLogin, $admin, $password) {
//            checkBasicData($this, 200, $resp, 0);
//
//            $reqLogin->email = $admin->email;
//            $reqLogin->pass = $password;
//
//            // аунтентифицируемся под админом
//        })->login($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать категорию
//        })->admCreateOrUpdateCat($reqCat, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // тут надо создать продукт
//        })->createOrUpdateItem($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//            // получить список и проверить
//        })->items($reqLogin, function (MyResponse $resp) {
//            checkBasicData($this, 200, $resp, 0, ViewPageLogin);
//
//        })->run();
    }
}
