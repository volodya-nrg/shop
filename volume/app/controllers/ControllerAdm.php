<?php

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
        if (!$_SERVER[EnumField::ModeIsTest->value]) {
            redirect("/adm/items");
        }

        return new MyResponse(EnumViewFile::PageAdm);
    }

    public function items(array $args): MyResponse
    {
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

        $serviceItems = new ServiceItems();
        $resp = new MyResponse(EnumViewFile::PageAdmItems);

        $result = $serviceItems->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceItems->all", $result->getMessage()));
            return $resp;
        }

        $resp->data[EnumField::Items->value] = [];
        foreach ($result as $value) {
            $resp->data[EnumField::Items->value][] = get_object_vars($value);
        }

        return $resp;
    }

    public function item(array $args): MyResponse
    {
        $serviceItems = new ServiceItems();
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
            $item->is_disabled = $req->isDisabled;

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

        $serviceCats = new ServiceCats();
        $resp = new MyResponse(EnumViewFile::PageAdmCats);

        $result = $serviceCats->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
            error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceCats->all", $result->getMessage()));
            return $resp;
        }

        $resp->data[EnumField::Items->value] = [];
        foreach ($result as $value) {
            $resp->data[EnumField::Items->value][] = get_object_vars($value);
        }

        return $resp;
    }

    public function cat(array $args): MyResponse
    {
        $serviceCats = new ServiceCats();
        $resp = new MyResponse(EnumViewFile::PageAdmCat);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestCat($_POST);

            $cat = new CatRow();
            $cat->cat_id = $req->catId;
            $cat->name = $req->name;
            $cat->slug = translit($cat->name);
            $cat->parent_id = $req->parentId;
            $cat->pos = $req->pos;
            $cat->is_disabled = $req->isDisabled;

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

        $serviceUsers = new ServiceUsers();
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
        $serviceUsers = new ServiceUsers();
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

        $serviceOrders = new ServiceOrders();
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
        $serviceOrders = new ServiceOrders();
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

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[EnumField::Admin->value])) {
            return new Error(EnumErr::NotHasAccess->value);
        }

        return null;
    }
}