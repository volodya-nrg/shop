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

    // проверим могут ли заходить пользователи и админ на гл. страницу админки
    public function testIndex(): void
    {
        $reqForUser = new RequestReg();
        $reqForUser->email = randomEmail();
        $reqForUser->pass = randomString(PassMinLen);
        $reqForUser->passConfirm = $reqForUser->pass;
        $reqForUser->agreement = true;
        $reqForUser->privatePolicy = true;

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginUser = new RequestLogin();
        $reqForLoginUser->email = $reqForUser->email;
        $reqForLoginUser->pass = $reqForUser->pass;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        // зарегистрируем юзера
        $this->client->reg($reqForUser, "", true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под юзером и запросим админку
        })->login($reqForLoginUser, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

        })->adm(function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

            // зарегистрируем админа
        })->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

        })->adm(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageAdm);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->run();
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
        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForCat = new RequestCat();
        $reqForCat->catId = 0;
        $reqForCat->name = randomString(10);
        $reqForCat->parentId = 0;
        $reqForCat->pos = 0;
        $reqForCat->isDisabled = false;

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

            // запросим чистую форму для создания категории
        })->admCat(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageAdmCat);

            // создадим категорию
        })->admCat($reqForCat, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            $_GET[FieldCatId] = $resp->data[FieldCatId];
            // получим категорию, данные вставятся в форму
        })->admCat(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $isHasCat = isset($resp->data[FieldItem]);

            $this->assertTrue($isHasCat);

            if ($isHasCat) {
                $cat = new CatRow($resp->data[FieldItem]);
                // TODO тут надо проверить св-ва категории
                ==========
            }

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->run();
    }
}
