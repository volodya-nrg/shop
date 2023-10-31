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
        $this->client->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом и запросим админку
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

            // запросим список
        })->admCats(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCats);
            $this->assertArrayHasKey(FieldItems, $resp->data);

            // создадим категорию
        })->admCat($reqForCat1, function (MyResponse $resp) use ($reqForCat1, $reqForCat2) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            $reqForCat1->catId = $resp->data[FieldCatId];
            $reqForCat2->parentId = $reqForCat1->catId;

        })->admCat($reqForCat2, function (MyResponse $resp) use ($reqForCat2) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            $reqForCat2->catId = $resp->data[FieldCatId];

            // получим список категорий
        })->admCats($reqPaginator, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCats);
            $this->assertArrayHasKey(FieldItems, $resp->data);
            $this->assertGreaterThanOrEqual(2, count($resp->data[FieldItems]));

            $reqPaginator->limit = 1;

        })->admCats($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCats);
            $this->assertArrayHasKey(FieldItems, $resp->data);
            $this->assertCount(1, $resp->data[FieldItems]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admCats(new RequestPaginator(), function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
            $this->assertArrayHasKey(FieldError, $resp->data);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
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
        })->admCat(null, function (MyResponse $resp) use ($reqForCat) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $isHasCat = isset($resp->data[FieldItem]);

            $this->assertTrue($isHasCat);

            if ($isHasCat) {
                $cat = new CatRow($resp->data[FieldItem]);
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
            checkBasicData($this, 200, $resp, 2, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            // получим категорию, данные вставятся в форму
        })->admCat(null, function (MyResponse $resp) use ($reqForCat) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $isHasCat = isset($resp->data[FieldItem]);

            $this->assertTrue($isHasCat);

            if ($isHasCat) {
                $cat = new CatRow($resp->data[FieldItem]);
                $this->assertEquals($reqForCat->catId, $cat->cat_id);
                $this->assertEquals($reqForCat->name, $cat->name);
                $this->assertEquals($reqForCat->parentId, $cat->parent_id);
                $this->assertEquals($reqForCat->pos, $cat->pos);
                $this->assertEquals($reqForCat->isDisabled, $cat->is_disabled);
            }
        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);
        })->admCat(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
            $this->assertArrayHasKey(FieldError, $resp->data);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
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
        $this->client->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

            // запросим список
        })->admItems(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItems);
            $this->assertArrayHasKey(FieldItems, $resp->data);

            // создадим категорию
        })->admCat($reqForCat, function (MyResponse $resp) use ($reqForItem1, $reqForItem2) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            $reqForItem1->catId = $reqForItem2->catId = $resp->data[FieldCatId];

            // создадим item
        })->admItem($reqForItem1, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItem);

        })->admItem($reqForItem2, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItem);

            // получим список
        })->admItems(null, function (MyResponse $resp) use ($reqPaginator) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItems);

            $this->assertArrayHasKey(FieldItems, $resp->data);
            $this->assertGreaterThanOrEqual(1, $resp->data[FieldItems]);

            $reqPaginator->limit = 1;

            // получим список
        })->admItems($reqPaginator, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItems);
            $this->assertArrayHasKey(FieldItems, $resp->data);
            $this->assertCount(1, $resp->data[FieldItems]);

        })->logout(function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0);

        })->admItem(null, function (MyResponse $resp) {
            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
            $this->assertArrayHasKey(FieldError, $resp->data);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
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
        $this->client->reg($reqForAdmin, FieldAdmin, true, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 2);

            // аунтентифицируемся под админом
        })->login($reqForLoginAdmin, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageLogin);

            // запросим чистую форму
        })->admItem(null, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 0, ViewPageAdmItem);

            // попытаемся создать, но будет ошибка, т.к. категории нет
        })->admItem($reqForItem, function (MyResponse $resp) {
            checkBasicData($this, 500, $resp, 1, ViewPageAdmItem);

        })->admCat($reqForCat, function (MyResponse $resp) use ($reqForCat, $reqForItem) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmCat);
            $this->assertArrayHasKey(FieldCatId, $resp->data);

            $reqForItem->catId = $reqForCat->catId = $resp->data[FieldCatId];
            sleep(2);

            // создадим item
        })->admItem($reqForItem, function (MyResponse $resp) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItem);
            $this->assertArrayHasKey(FieldItemId, $resp->data);

            $_GET[FieldItemId] = $resp->data[FieldItemId];

            // получим item, для вставки данных в форму
        })->admItem(null, function (MyResponse $resp) use ($reqForItem, $dt) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItem);
            $isHasItem = isset($resp->data[FieldItem]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new ItemRow($resp->data[FieldItem]);
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
            checkBasicData($this, 200, $resp, 2, ViewPageAdmItem);
            $this->assertArrayHasKey(FieldItemId, $resp->data);

            // получим
        })->admItem(null, function (MyResponse $resp) use ($reqForItem) {
            checkBasicData($this, 200, $resp, 1, ViewPageAdmItem);
            $isHasItem = isset($resp->data[FieldItem]);

            $this->assertTrue($isHasItem);

            if ($isHasItem) {
                $item = new ItemRow($resp->data[FieldItem]);
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
            checkBasicData($this, 401, $resp, 1, ViewPageAccessDined);
            $this->assertArrayHasKey(FieldError, $resp->data);
            $this->assertEquals(ErrNotHasAccess, $resp->data[FieldError]);
        })->run();
    }
}
