<?php declare(strict_types=1);

final class ControllerAdm extends ControllerBase
{
    public string $title = EnumDic::Administration->value;
    public string $description = "";

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            throw new Exception($err->getMessage());
        }
    }

    public function index(array $args): MyResponse
    {
        // TODO пока временно сделаем редирект, до тех пор пока не сделаем dashboard

        if ($_SERVER[EnumField::ModeIsTest->value]) {
            $this->items($args);
        } else {
            redirect("/adm/items");
        }

        return new MyResponse(EnumViewFile::PageAdm);
    }

    public function items(array $args): MyResponse
    {
        global $PDO;
        $limit = DefaultLimit;
        $offset = 0;

        if (isset($_POST) && count($_POST)) {
            $req = new RequestPaginator($_POST);

            if ($req->limit > 0 && $req->limit < DefaultLimit) {
                $limit = $req->limit;
            }
            if ($req->offset > 0) {
                $offset = $req->offset;
            }
        }

        $serviceItems = new ServiceItems($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmItems);

        $result = $serviceItems->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->all", $result->getMessage()));
            return $resp;
        }

        $this->items = $result;

        $resp->data[EnumField::Items->value] = [];
        foreach ($result as $value) {
            $resp->data[EnumField::Items->value][] = get_object_vars($value);
        }

        $resp->data[EnumField::Tabs->value] = [
            new Tab("Items", "/adm/items", true),
            new Tab("Cats", "/adm/cats", false),
            new Tab("Infos", "/adm/infos", false),
            new Tab("Users", "/adm/users", false),
            new Tab("Orders", "/adm/orders", false),
            new Tab("Etc", "/adm/etc", false),
        ];

        return $resp;
    }

    public function item(array $args): MyResponse
    {
        global $PDO;
        $serviceItems = new ServiceItems($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmItem);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestItem($_POST);

            $item = new ItemRow();
            $item->item_id = $req->itemId;
            $item->title = $req->title;
            $item->slug = translit($item->title);
            $item->cat_id = $req->catId;
            $item->description = $req->description;
            $item->price = $req->price;
            $item->is_disabled = $req->isDisabled ? 1 : 0;

            if ($item->item_id == 0) {
                $result = $serviceItems->create($item);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->create", $result->getMessage()));
                    return $resp;
                }
                $item->item_id = $result;
            } else {
                $err = $serviceItems->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[EnumField::ItemId->value] = $item->item_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[EnumField::ItemId->value])) {
            $result = $_GET[EnumField::ItemId->value];

            $result = $serviceItems->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundRow->value;
                return $resp;
            }

            $item = $result;
            $resp->data[EnumField::Item->value] = get_object_vars($item); // явно в массив для передачи
        }

        return $resp;
    }

    public function cats(array $args): MyResponse
    {
        global $PDO;
        $offset = 0;
        $serviceCats = new ServiceCats($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmCats);

        if (isset($_GET) && count($_GET)) {
            $req = new RequestPaginator($_GET);
            $offset = $req->page * DefaultLimit;
        }

        $cats = $serviceCats->all(DefaultLimit, $offset);
        if ($cats instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceCats->all", $cats->getMessage()));
            return $resp;
        }

        $resp->data[EnumField::Items->value] = [];
        foreach ($cats as $pos => $cat) {
            $resp->data[EnumField::Items->value][] = new AdmListItem($pos + 1, $cat->name, $cat->cat_id);
        }

        $total = $serviceCats->total();
        if ($total instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceCats->total", $total->getMessage()));
            return $resp;
        }

        $resp->data[EnumField::Offset->value] = $offset;
        $resp->data[EnumField::Total->value] = $total;
        $resp->data[EnumField::Tabs->value] = [
            new Tab("Items", "/adm/items", false),
            new Tab("Cats", "/adm/cats", true),
            new Tab("Infos", "/adm/infos", false),
            new Tab("Users", "/adm/users", false),
            new Tab("Orders", "/adm/orders", false),
            new Tab("Etc", "/adm/etc", false),
        ];

        return $resp;
    }

    public function cat(array $args): MyResponse
    {
        global $PDO;
        $serviceCats = new ServiceCats($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmCat);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestCat($_POST);

            $cat = new CatRow();
            $cat->cat_id = $req->catId;
            $cat->name = $req->name;
            $cat->slug = translit($cat->name);
            $cat->parent_id = $req->parentId;
            $cat->pos = $req->pos;
            $cat->is_disabled = $req->isDisabled ? 1 : 0;

            if ($cat->cat_id == 0) {
                $result = $serviceCats->create($cat);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->create", $result->getMessage()));
                    return $resp;
                }
                $cat->cat_id = $result;
            } else {
                $err = $serviceCats->update($cat);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[EnumField::CatId->value] = $cat->cat_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[EnumField::CatId->value])) {
            $catId = $_GET[EnumField::CatId->value];

            $result = $serviceCats->one($catId);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceCats->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundRow->value;
                return $resp;
            }

            $cat = $result;
            $resp->data[EnumField::Item->value] = get_object_vars($cat); // явно в массив для передачи
        }

        return $resp;
    }

    public function users(array $args): MyResponse
    {
        global $PDO;
        $limit = DefaultLimit;
        $offset = 0;

        if (isset($_POST) && count($_POST)) {
            $req = new RequestPaginator($_POST);

            if ($req->limit > 0 && $req->limit < DefaultLimit) {
                $limit = $req->limit;
            }
            if ($req->offset > 0) {
                $offset = $req->offset;
            }
        }

        $serviceUsers = new ServiceUsers($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmUsers);

        $result = $serviceUsers->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->all", $result->getMessage()));
            return $resp;
        }

        $users = $result;
        $resp->data[EnumField::Users->value] = [];
        foreach ($users as $user) {
            $user->pass = ""; // скроем явно
            $resp->data[EnumField::Users->value][] = get_object_vars($user);
        }

        return $resp;
    }

    public function user(array $args): MyResponse
    {
        global $PDO;
        $serviceUsers = new ServiceUsers($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmUser);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestUser($_POST);

            $item = new UserRow();
            $item->user_id = $req->userId;
            $item->email = $req->email;
            $item->pass = password_hash($req->pass, PASSWORD_DEFAULT);
            $item->email_hash = $req->emailHash;
            $item->avatar = $req->avatar;
            $item->birthday_day = $req->birthdayDay;
            $item->birthday_mon = $req->birthdayMon;
            $item->role = $req->role;

            if ($item->user_id == 0) {
                $result = $serviceUsers->create($item);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->create", $result->getMessage()));
                    return $resp;
                }
                $item->user_id = $result;
            } else {
                $err = $serviceUsers->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[EnumField::UserId->value] = $item->user_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[EnumField::UserId->value])) {
            $result = $_GET[EnumField::UserId->value];

            $result = $serviceUsers->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundRow->value;
                return $resp;
            }

            $user = $result;
            $user->pass = ""; // скроем явно

            $resp->data[EnumField::User->value] = get_object_vars($user); // явно в массив для передачи
        }

        return $resp;
    }

    public function orders(array $args): MyResponse
    {
        global $PDO;
        $limit = DefaultLimit;
        $offset = 0;

        if (isset($_POST) && count($_POST)) {
            $req = new RequestPaginator($_POST);

            if ($req->limit > 0 && $req->limit < DefaultLimit) {
                $limit = $req->limit;
            }
            if ($req->offset > 0) {
                $offset = $req->offset;
            }
        }

        $serviceOrders = new ServiceOrders($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmOrders);

        $result = $serviceOrders->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceOrders->all", $result->getMessage()));
            return $resp;
        }

        $orders = $result;
        $resp->data[EnumField::Orders->value] = [];
        foreach ($orders as $order) {
            $resp->data[EnumField::Orders->value][] = get_object_vars($order);
        }

        return $resp;
    }

    public function order(array $args): MyResponse
    {
        global $PDO;
        $serviceOrders = new ServiceOrders($PDO);
        $resp = new MyResponse(EnumViewFile::PageAdmOrder);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestOrder($_POST);

            $item = new OrderRow();
            $item->order_id = $req->orderId;
            $item->user_id = $req->userId;
            $item->contact_phone = $req->contactPhone;
            $item->contact_name = $req->contactName;
            $item->comment = $req->comment;
            $item->place_delivery = $req->placeDelivery;
            $item->ip = $req->ip;
            $item->status = $req->status;

            if ($item->order_id == 0) {
                $item->ip = $_SERVER["REMOTE_ADDR"]; // HTTP_X_FORWARDED_FOR
                $item->status = EnumStatusOrder::Created->value;

                $result = $serviceOrders->create($item);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceOrders->create", $result->getMessage()));
                    return $resp;
                }
                $item->order_id = $result;
            } else {
                $err = $serviceOrders->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                    error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceOrders->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[EnumField::OrderId->value] = $item->order_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[EnumField::OrderId->value])) {
            $result = $_GET[EnumField::OrderId->value];

            $result = $serviceOrders->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceOrders->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundRow->value;
                return $resp;
            }

            $order = $result;
            $resp->data[EnumField::Order->value] = get_object_vars($order); // явно в массив для передачи
        }

        return $resp;
    }

    public function etc(array $args): MyResponse
    {
        global $PDO;
        $resp = new MyResponse(EnumViewFile::PageAdmEtc);

        if (!empty($_GET["addData"])) {
            //0. тут надо все почистить
            //1. добавить категории
            //2. добавить товары
            //3. добавить пользвателей
            //4. добавить заказы
            $serviceCats = new ServiceCats($PDO);
            $serviceItems = new ServiceItems($PDO);
            $serviceUsers = new ServiceUsers($PDO);
            $serviceOrders = new ServiceOrders($PDO);

            $result = $this->createCats();
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "createCats", $result->getMessage()));
                return $resp;
            }
            $aCatIds = $result;
        }

        $resp->data[EnumField::Tabs->value] = [
            new Tab("Items", "/adm/items", false),
            new Tab("Cats", "/adm/cats", false),
            new Tab("Infos", "/adm/infos", false),
            new Tab("Users", "/adm/users", false),
            new Tab("Orders", "/adm/orders", false),
            new Tab("Etc", "/adm/etc", true),
        ];

        return $resp;
    }

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[EnumField::Admin->value])) {
            return new Error(EnumErr::NotHasAccess->value);
        }

        return null;
    }

    /**
     * @return Error|int[]
     */
    private function createCats(): array|Error
    {
        global $PDO;

        $serviceCats = new ServiceCats($PDO);
        $aCatIdsLevel1 = [];
        $aCatIdsLevel2 = [];
        $aCatIdsLevel3 = [];

        $cats = $serviceCats->all();

        $PDO->beginTransaction();

        foreach ($cats as $cat) {
            $serviceCats->delete($cat->cat_id);
        }

        // создадим первый уровень
        for ($i = 0; $i < randomInt(3, 6); $i++) {
            $cat = new CatRow();
            $cat->name = randomString(10, false);
            $cat->slug = translit($cat->name);
            //$cat->parent_id = 0;
            $cat->pos = $i;
            $cat->is_disabled = randomInt(0, 1);

            $result = $serviceCats->create($cat);
            if ($result instanceof Error) {
                $PDO->rollBack();
                return $result;
            }

            $cat->cat_id = $result;
            $aCatIdsLevel1[] = $cat->cat_id;
        }
        // создадим второй уровень
        foreach ($aCatIdsLevel1 as $catId) {
            for ($i = 0; $i < randomInt(1, 5); $i++) {
                $cat = new CatRow();
                $cat->name = randomString(10, false);
                $cat->slug = translit($cat->name);
                $cat->parent_id = $catId;
                $cat->pos = $i;
                $cat->is_disabled = randomInt(0, 1);

                $result = $serviceCats->create($cat);
                if ($result instanceof Error) {
                    $PDO->rollBack();
                    return $result;
                }

                $cat->cat_id = $result;
                $aCatIdsLevel2[] = $cat->cat_id;
            }
        }
        // создадим третий уровень
        foreach ($aCatIdsLevel2 as $catId) {
            for ($i = 0; $i < randomInt(1, 10); $i++) {
                $cat = new CatRow();
                $cat->name = randomString(10, false);
                $cat->slug = translit($cat->name);
                $cat->parent_id = $catId;
                $cat->pos = $i;
                $cat->is_disabled = randomInt(0, 1);

                $result = $serviceCats->create($cat);
                if ($result instanceof Error) {
                    $PDO->rollBack();
                    return $result;
                }

                $cat->cat_id = $result;
                $aCatIdsLevel3[] = $cat->cat_id;
            }
        }

        $PDO->commit();

        return $aCatIdsLevel3;
    }
}