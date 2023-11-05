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
        $_SERVER[EnumField::ModeIsTest->value] = true;
        $_SERVER["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"; // HTTP_X_FORWARDED_FOR, REMOTE_ADDR
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        $_SESSION = [];
        unset($_SERVER["REMOTE_ADDR"]);
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
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

        })->adm(function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

            // зарегистрируем админа
        })->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

        })->adm(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageAdm);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->run();
    }

    public function testCats(): void
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

        $reqForCat1 = new RequestCat();
        $reqForCat1->catId = 0;
        $reqForCat1->name = randomString(10);
        $reqForCat1->parentId = 0;
        $reqForCat1->pos = 0;
        $reqForCat1->isDisabled = false;

        $reqForCat2 = new RequestCat();
        $reqForCat2->catId = 0;
        $reqForCat2->name = randomString(10);
        $reqForCat2->parentId = 0;
        $reqForCat2->pos = 0;
        $reqForCat2->isDisabled = false;

        $reqPaginator = new RequestPaginator();

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим список
        })->admCats(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCats);
            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);

            // создадим категорию
        })->admCat($reqForCat1, function (MyResponse $resp) use ($reqForCat1, $reqForCat2) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            $reqForCat1->catId = $resp->data[EnumField::CatId->value];
            $reqForCat2->parentId = $reqForCat1->catId;

        })->admCat($reqForCat2, function (MyResponse $resp) use ($reqForCat2) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            $reqForCat2->catId = $resp->data[EnumField::CatId->value];

            // получим список категорий
        })->admCats($reqPaginator, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCats);
            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);
            $this->assertGreaterThanOrEqual(2, count($resp->data[EnumField::Items->value]));

            $reqPaginator->limit = 1;

        })->admCats($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCats);
            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);
            $this->assertCount(1, $resp->data[EnumField::Items->value]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admCats(new RequestPaginator(), function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
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
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим чистую форму для создания категории
        })->admCat(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageAdmCat);

            // создадим категорию
        })->admCat($reqForCat, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            $_GET[EnumField::CatId->value] = $resp->data[EnumField::CatId->value];

            // получим категорию, данные вставятся в форму
        })->admCat(null, function (MyResponse $resp) use ($reqForCat) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $isHasCat = isset($resp->data[EnumField::Item->value]);

            $this->assertTrue($isHasCat);

            if ($isHasCat) {
                $cat = new CatRow($resp->data[EnumField::Item->value]);
                $this->assertTrue($cat->cat_id > 0);
                $this->assertEquals($reqForCat->name, $cat->name);
                $this->assertEquals($reqForCat->parentId, $cat->parent_id);
                $this->assertEquals($reqForCat->pos, $cat->pos);
                $this->assertEquals($reqForCat->isDisabled, $cat->is_disabled);

                $reqForCat->catId = $cat->cat_id;
                $reqForCat->name = randomString(10);
                $reqForCat->parentId = random_int(1, 100);
                $reqForCat->pos = random_int(1, 100);
                $reqForCat->isDisabled = true;
            }

            // изменим категорию
        })->admCat($reqForCat, function (MyResponse $resp) {
            // после одновления получаем catId и т.к. есть GET, то и саму категорию
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            // получим категорию, данные вставятся в форму
        })->admCat(null, function (MyResponse $resp) use ($reqForCat) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $isHasCat = isset($resp->data[EnumField::Item->value]);

            $this->assertTrue($isHasCat);

            if ($isHasCat) {
                $cat = new CatRow($resp->data[EnumField::Item->value]);
                $this->assertEquals($reqForCat->catId, $cat->cat_id);
                $this->assertEquals($reqForCat->name, $cat->name);
                $this->assertEquals($reqForCat->parentId, $cat->parent_id);
                $this->assertEquals($reqForCat->pos, $cat->pos);
                $this->assertEquals($reqForCat->isDisabled, $cat->is_disabled);
            }
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->admCat(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }

    public function testItems(): void
    {
        $reqPaginator = new RequestPaginator();

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForItem1 = new RequestItem();
        $reqForItem1->itemId = 0;
        $reqForItem1->title = randomString(10);
        $reqForItem1->catId = 0;
        $reqForItem1->description = randomString(10);
        $reqForItem1->price = random_int(100, 1000);
        $reqForItem1->isDisabled = false;

        $reqForItem2 = new RequestItem();
        $reqForItem2->itemId = 0;
        $reqForItem2->title = randomString(10);
        $reqForItem2->catId = 0;
        $reqForItem2->description = randomString(10);
        $reqForItem2->price = random_int(100, 1000);
        $reqForItem2->isDisabled = false;

        $reqForCat = new RequestCat();
        $reqForCat->catId = 0;
        $reqForCat->name = randomString(10);
        $reqForCat->parentId = 0;
        $reqForCat->pos = 0;
        $reqForCat->isDisabled = false;

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим список
        })->admItems(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItems);
            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);

            // создадим категорию
        })->admCat($reqForCat, function (MyResponse $resp) use ($reqForItem1, $reqForItem2) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            $reqForItem1->catId = $reqForItem2->catId = $resp->data[EnumField::CatId->value];

            // создадим item
        })->admItem($reqForItem1, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItem);

        })->admItem($reqForItem2, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItem);

            // получим список
        })->admItems(null, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItems);

            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);
            $this->assertGreaterThanOrEqual(1, $resp->data[EnumField::Items->value]);

            $reqPaginator->limit = 1;

            // получим список
        })->admItems($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItems);
            $this->assertArrayHasKey(EnumField::Items->value, $resp->data);
            $this->assertCount(1, $resp->data[EnumField::Items->value]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admItem(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }

    public function testItem(): void
    {
        $dt = date_create();

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForItem = new RequestItem();
        $reqForItem->itemId = 0;
        $reqForItem->title = randomString(10);
        $reqForItem->catId = 0;
        $reqForItem->description = randomString(10);
        $reqForItem->price = random_int(100, 1000);
        $reqForItem->isDisabled = false;

        $reqForCat = new RequestCat();
        $reqForCat->catId = 0;
        $reqForCat->name = randomString(10);
        $reqForCat->parentId = 0;
        $reqForCat->pos = 0;
        $reqForCat->isDisabled = false;

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим чистую форму
        })->admItem(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageAdmItem);

            // попытаемся создать, но будет ошибка, т.к. категории нет
        })->admItem($reqForItem, function (MyResponse $resp) {
            checkBasicData($this, 500, $resp, 1, EnumViewFile::PageAdmItem);

        })->admCat($reqForCat, function (MyResponse $resp) use ($reqForCat, $reqForItem) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmCat);
            $this->assertArrayHasKey(EnumField::CatId->value, $resp->data);

            $reqForItem->catId = $reqForCat->catId = $resp->data[EnumField::CatId->value];
            sleep(2);

            // создадим item
        })->admItem($reqForItem, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItem);
            $this->assertArrayHasKey(EnumField::ItemId->value, $resp->data);

            $_GET[EnumField::ItemId->value] = $resp->data[EnumField::ItemId->value];

            // получим item, для вставки данных в форму
        })->admItem(null, function (MyResponse $resp) use ($reqForItem, $dt) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItem);
            $isHasItem = isset($resp->data[EnumField::Item->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new ItemRow($resp->data[EnumField::Item->value]);
                $this->assertTrue($item->item_id > 0);
                $this->assertEquals($reqForItem->title, $item->title);
                $this->assertTrue(strlen($item->slug) > 0);
                $this->assertEquals($reqForItem->catId, $item->cat_id);
                $this->assertEquals($reqForItem->description, $item->description);
                $this->assertEquals($reqForItem->price, $item->price);
                $this->assertEquals($reqForItem->isDisabled, $item->is_disabled);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($dt->format(DatePattern), $item->created_at);

                $reqForItem->itemId = $item->item_id;
                $reqForItem->title = randomString(10);
                $reqForItem->description = randomString(10);
                $reqForItem->price = random_int(100, 1000);
                $reqForItem->isDisabled = true;

                sleep(2);
            }

            // изменим
        })->admItem($reqForItem, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageAdmItem);
            $this->assertArrayHasKey(EnumField::ItemId->value, $resp->data);

            // получим
        })->admItem(null, function (MyResponse $resp) use ($reqForItem) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmItem);
            $isHasItem = isset($resp->data[EnumField::Item->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new ItemRow($resp->data[EnumField::Item->value]);
                $this->assertEquals($reqForItem->itemId, $item->item_id);
                $this->assertEquals($reqForItem->title, $item->title);
                $this->assertTrue(strlen($item->slug) > 0);
                $this->assertEquals($reqForItem->catId, $item->cat_id);
                $this->assertEquals($reqForItem->description, $item->description);
                $this->assertEquals($reqForItem->price, $item->price);
                $this->assertEquals($reqForItem->isDisabled, $item->is_disabled);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertTrue(strlen($item->updated_at) > 0);
                $this->assertNotEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($item->created_at, $item->updated_at);
            }
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->admItem(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }

    public function testUsers(): void
    {
        $reqPaginator = new RequestPaginator();

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForUser1 = new RequestUser();
        $reqForUser1->userId = 0;
        $reqForUser1->email = randomEmail();
        $reqForUser1->pass = randomString(10);
        $reqForUser1->emailHash = randomString(10);
        $reqForUser1->birthdayDay = random_int(1, 31);
        $reqForUser1->birthdayMon = random_int(1, 12);
        $reqForUser1->role = "";

        $reqForUser2 = new RequestUser();
        $reqForUser2->userId = 0;
        $reqForUser2->email = randomEmail();
        $reqForUser2->pass = randomString(10);
        $reqForUser2->emailHash = randomString(10);
        $reqForUser2->birthdayDay = random_int(1, 31);
        $reqForUser2->birthdayMon = random_int(1, 12);
        $reqForUser2->role = "";

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим список
        })->admUsers(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUsers);
            $this->assertArrayHasKey(EnumField::Users->value, $resp->data);

            // создадим user1
        })->admUser($reqForUser1, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUser);

            // создадим user2
        })->admUser($reqForUser2, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUser);

            // получим список
        })->admUsers(null, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUsers);

            $this->assertArrayHasKey(EnumField::Users->value, $resp->data);
            $this->assertGreaterThanOrEqual(1, $resp->data[EnumField::Users->value]);

            $reqPaginator->limit = 1;

            // получим список
        })->admUsers($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUsers);
            $this->assertArrayHasKey(EnumField::Users->value, $resp->data);
            $this->assertCount(1, $resp->data[EnumField::Users->value]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admUser(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }

    public function testUser(): void
    {
        $dt = date_create();

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForLoginUser = new RequestLogin();

        $reqForUser = new RequestUser();
        $reqForUser->userId = 0;
        $reqForUser->email = randomEmail();
        $reqForUser->pass = randomString(10);
        $reqForUser->emailHash = randomString(10);
        $reqForUser->birthdayDay = random_int(1, 31);
        $reqForUser->birthdayMon = random_int(1, 12);
        $reqForUser->role = "";

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим чистую форму
        })->admUser(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageAdmUser);
            sleep(2);

            // создадим
        })->admUser($reqForUser, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUser);
            $this->assertArrayHasKey(EnumField::UserId->value, $resp->data);

            $_GET[EnumField::UserId->value] = $resp->data[EnumField::UserId->value];

            // получим, для вставки данных в форму
        })->admUser(null, function (MyResponse $resp) use ($reqForUser, $dt, $reqForLoginUser) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUser);
            $isHasItem = isset($resp->data[EnumField::User->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new UserRow($resp->data[EnumField::User->value]);
                $this->assertTrue($item->user_id > 0);
                $this->assertEquals($reqForUser->email, $item->email);
                $this->assertEquals("", $item->pass);
                $this->assertNull($item->email_hash);
                $this->assertEquals($reqForUser->avatar, $item->avatar);
                $this->assertEquals($reqForUser->birthdayDay, $item->birthday_day);
                $this->assertEquals($reqForUser->birthdayMon, $item->birthday_mon);
                $this->assertEquals($reqForUser->role, $item->role);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($dt->format(DatePattern), $item->created_at);

                $reqForUser->userId = $item->user_id;
                $reqForUser->email = randomEmail();
                $reqForUser->pass = randomString(10);
                $reqForUser->emailHash = randomString(10);
                $reqForUser->birthdayDay = random_int(1, 31);
                $reqForUser->birthdayMon = random_int(1, 12);
                $reqForUser->role = randomString(10);

                $reqForLoginUser->email = $reqForUser->email;
                $reqForLoginUser->pass = $reqForUser->pass;

                sleep(2);
            }

            // изменим
        })->admUser($reqForUser, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageAdmUser);
            $this->assertArrayHasKey(EnumField::UserId->value, $resp->data);

            // получим
        })->admUser(null, function (MyResponse $resp) use ($reqForUser) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmUser);
            $isHasItem = isset($resp->data[EnumField::User->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new UserRow($resp->data[EnumField::User->value]);
                $this->assertEquals($reqForUser->userId, $item->user_id);
                $this->assertEquals($reqForUser->email, $item->email);
                $this->assertEquals("", $item->pass);
                $this->assertNull($item->email_hash);
                $this->assertEquals($reqForUser->avatar, $item->avatar);
                $this->assertEquals($reqForUser->birthdayDay, $item->birthday_day);
                $this->assertEquals($reqForUser->birthdayMon, $item->birthday_mon);
                $this->assertEquals($reqForUser->role, $item->role);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertTrue(strlen($item->updated_at) > 0);
                $this->assertNotEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($item->created_at, $item->updated_at);
            }
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->admUser(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);

            // аунтентифицируемся под user-ом
        })->login($reqForLoginUser, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);
        })->run();
    }

    public function testOrders(): void
    {
        $reqPaginator = new RequestPaginator();

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForOrder1 = new RequestOrder();
        $reqForOrder1->orderId = 0;
        $reqForOrder1->userId = random_int(1, 100);
        $reqForOrder1->contactPhone = randomString(10);
        $reqForOrder1->contactName = randomString(10);
        $reqForOrder1->comment = randomString(10);
        $reqForOrder1->placeDelivery = randomString(10);

        $reqForOrder2 = new RequestOrder();
        $reqForOrder2->orderId = 0;
        $reqForOrder2->userId = random_int(1, 100);
        $reqForOrder2->contactPhone = randomString(10);
        $reqForOrder2->contactName = randomString(10);
        $reqForOrder2->comment = randomString(10);
        $reqForOrder2->placeDelivery = randomString(10);

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим список
        })->admOrders(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrders);
            $this->assertArrayHasKey(EnumField::Orders->value, $resp->data);

            // создадим order1
        })->admOrder($reqForOrder1, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrder);

            // создадим order2
        })->admOrder($reqForOrder2, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrder);

            // получим список
        })->admOrders(null, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrders);

            $this->assertArrayHasKey(EnumField::Orders->value, $resp->data);
            $this->assertGreaterThanOrEqual(1, $resp->data[EnumField::Orders->value]);

            $reqPaginator->limit = 1;

            // получим список
        })->admOrders($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrders);
            $this->assertArrayHasKey(EnumField::Orders->value, $resp->data);
            $this->assertCount(1, $resp->data[EnumField::Orders->value]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admOrder(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }

    public function testOrder(): void
    {
        $dt = date_create();
        //$a = Suit::Clubs->value;

        $reqForAdmin = new RequestReg();
        $reqForAdmin->email = randomEmail();
        $reqForAdmin->pass = randomString(PassMinLen);
        $reqForAdmin->passConfirm = $reqForAdmin->pass;
        $reqForAdmin->agreement = true;
        $reqForAdmin->privatePolicy = true;

        $reqForLoginAdmin = new RequestLogin();
        $reqForLoginAdmin->email = $reqForAdmin->email;
        $reqForLoginAdmin->pass = $reqForAdmin->pass;

        $reqForOrder = new RequestOrder();
        $reqForOrder->orderId = 0;
        $reqForOrder->userId = random_int(1, 100);
        $reqForOrder->contactPhone = randomString(10);
        $reqForOrder->contactName = randomString(10);
        $reqForOrder->comment = randomString(10);
        $reqForOrder->placeDelivery = randomString(10);

        // зарегистрируем админа
        $this->client->reg($reqForAdmin, EnumField::Admin->value, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageLogin);

            // запросим чистую форму
        })->admOrder(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, EnumViewFile::PageAdmOrder);
            sleep(2);

            // создадим
        })->admOrder($reqForOrder, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrder);
            $this->assertArrayHasKey(EnumField::OrderId->value, $resp->data);

            $_GET[EnumField::OrderId->value] = $resp->data[EnumField::OrderId->value];

            // получим, для вставки данных в форму
        })->admOrder(null, function (MyResponse $resp) use ($reqForOrder, $dt) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrder);
            $isHasItem = isset($resp->data[EnumField::Order->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new OrderRow($resp->data[EnumField::Order->value]);
                $this->assertTrue($item->order_id > 0);
                $this->assertTrue($item->user_id > 0);
                $this->assertEquals($reqForOrder->contactPhone, $item->contact_phone);
                $this->assertEquals($reqForOrder->contactName, $item->contact_name);
                $this->assertEquals($reqForOrder->comment, $item->comment);
                $this->assertEquals($reqForOrder->placeDelivery, $item->place_delivery);
                $this->assertEquals($_SERVER["REMOTE_ADDR"], $item->ip);
                $this->assertEquals(EnumStatusOrder::Created->value, $item->status);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($dt->format(DatePattern), $item->created_at);

                $reqForOrder->orderId = $item->order_id;
                $reqForOrder->userId = null;
                $reqForOrder->contactPhone = randomString(10);
                $reqForOrder->contactName = null;
                $reqForOrder->comment = null;
                $reqForOrder->placeDelivery = null;
                $reqForOrder->ip = randomIP();
                $reqForOrder->status = EnumStatusOrder::Finished->value;

                sleep(2);
            }

            // изменим
        })->admOrder($reqForOrder, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2, EnumViewFile::PageAdmOrder);
            $this->assertArrayHasKey(EnumField::OrderId->value, $resp->data);

            // получим
        })->admOrder(null, function (MyResponse $resp) use ($reqForOrder) {
            checkBasicData($this, 200, $resp, 1, EnumViewFile::PageAdmOrder);
            $isHasItem = isset($resp->data[EnumField::Order->value]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new OrderRow($resp->data[EnumField::Order->value]);
                $this->assertEquals($reqForOrder->orderId, $item->order_id);
                $this->assertNull($item->user_id);
                $this->assertEquals($reqForOrder->contactPhone, $item->contact_phone);
                $this->assertNull($item->contact_name);
                $this->assertNull($item->comment);
                $this->assertNull($item->place_delivery);
                $this->assertEquals($reqForOrder->ip, $item->ip);
                $this->assertEquals(EnumStatusOrder::Finished->value, $item->status);
                $this->assertTrue(strlen($item->created_at) > 0);
                $this->assertTrue(strlen($item->updated_at) > 0);
                $this->assertNotEquals($item->created_at, $item->updated_at);
                $this->assertGreaterThan($item->created_at, $item->updated_at);

                $reqForOrder->status = "x";
            }

            // изменим с неправильным статусом, будет ошибка
        })->admOrder($reqForOrder, function (MyResponse $resp) {
            checkBasicData($this, 500, $resp, 1, EnumViewFile::PageAdmOrder);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::InternalServer->value, $resp->data[EnumField::Error->value]);
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->admOrder(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, EnumViewFile::PageAccessDined);
            $this->assertArrayHasKey(EnumField::Error->value, $resp->data);
            $this->assertEquals(EnumErr::NotHasAccess->value, $resp->data[EnumField::Error->value]);
        })->run();
    }
}
