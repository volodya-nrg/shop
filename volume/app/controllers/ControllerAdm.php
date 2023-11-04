<?php

final class ControllerAdm extends ControllerBase
{
    public string $title = DicAdministration;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }
        return new MyResponse(ViewPageAdm);
    }

    public function items(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

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
        $resp = new MyResponse(ViewPageAdmItems);

        $result = $serviceItems->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[FieldError] = ErrInternalServer;
            error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->all", $result->getMessage()));
            return $resp;
        }

        $resp->data[FieldItems] = [];
        foreach ($result as $value) {
            $resp->data[FieldItems][] = get_object_vars($value);
        }

        return $resp;
    }

    public function item(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

        $serviceItems = new ServiceItems();
        $resp = new MyResponse(ViewPageAdmItem);

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
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->create", $result->getMessage()));
                    return $resp;
                }
                $item->item_id = $result;
            } else {
                $err = $serviceItems->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[FieldItemId] = $item->item_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[FieldItemId])) {
            $result = $_GET[FieldItemId];

            $result = $serviceItems->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundRow;
                return $resp;
            }

            $item = $result;
            $resp->data[FieldItem] = get_object_vars($item); // явно в массив для передачи
        }

        return $resp;
    }

    public function cats(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

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
        $resp = new MyResponse(ViewPageAdmCats);

        $result = $serviceCats->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[FieldError] = ErrInternalServer;
            error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceCats->all", $result->getMessage()));
            return $resp;
        }

        $resp->data[FieldItems] = [];
        foreach ($result as $value) {
            $resp->data[FieldItems][] = get_object_vars($value);
        }

        return $resp;
    }

    public function cat(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

        $serviceCats = new ServiceCats();
        $resp = new MyResponse(ViewPageAdmCat);

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
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->create", $result->getMessage()));
                    return $resp;
                }
                $cat->cat_id = $result;
            } else {
                $err = $serviceCats->update($cat);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceItems->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[FieldCatId] = $cat->cat_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[FieldCatId])) {
            $catId = $_GET[FieldCatId];

            $result = $serviceCats->one($catId);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceCats->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundRow;
                return $resp;
            }

            $cat = $result;
            $resp->data[FieldItem] = get_object_vars($cat); // явно в массив для передачи
        }

        return $resp;
    }

    public function users(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

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
        $resp = new MyResponse(ViewPageAdmUsers);

        $result = $serviceUsers->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[FieldError] = ErrInternalServer;
            error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->all", $result->getMessage()));
            return $resp;
        }

        $users = $result;
        $resp->data[FieldUsers] = [];
        foreach ($users as $user) {
            $user->pass = ""; // скроем явно
            $resp->data[FieldUsers][] = get_object_vars($user);
        }

        return $resp;
    }

    public function user(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

        $serviceUsers = new ServiceUsers();
        $resp = new MyResponse(ViewPageAdmUser);

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
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->create", $result->getMessage()));
                    return $resp;
                }
                $item->user_id = $result;
            } else {
                $err = $serviceUsers->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[FieldUserId] = $item->user_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[FieldUserId])) {
            $result = $_GET[FieldUserId];

            $result = $serviceUsers->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundRow;
                return $resp;
            }

            $user = $result;
            $user->pass = ""; // скроем явно

            $resp->data[FieldUser] = get_object_vars($user); // явно в массив для передачи
        }

        return $resp;
    }

    public function orders(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

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
        $resp = new MyResponse(ViewPageAdmOrders);

        $result = $serviceOrders->all($limit, $offset);
        if ($result instanceof Error) {
            $resp->setHttpCode(500);
            $resp->data[FieldError] = ErrInternalServer;
            error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceOrders->all", $result->getMessage()));
            return $resp;
        }

        $orders = $result;
        $resp->data[FieldOrders] = [];
        foreach ($orders as $order) {
            $resp->data[FieldOrders][] = get_object_vars($order);
        }

        return $resp;
    }

    public function order(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

        $serviceOrders = new ServiceOrders();
        $resp = new MyResponse(ViewPageAdmOrder);

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
                $item->status = StatusOrderCreated;

                $result = $serviceOrders->create($item);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceOrders->create", $result->getMessage()));
                    return $resp;
                }
                $item->order_id = $result;
            } else {
                $err = $serviceOrders->update($item);
                if ($err instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceOrders->update", $err->getMessage()));
                    return $resp;
                }
            }

            $resp->data = [];
            $resp->data[FieldOrderId] = $item->order_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[FieldOrderId])) {
            $result = $_GET[FieldOrderId];

            $result = $serviceOrders->one($result);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceOrders->one", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundRow;
                return $resp;
            }

            $order = $result;
            $resp->data[FieldOrder] = get_object_vars($order); // явно в массив для передачи
        }

        return $resp;
    }

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[FieldAdmin])) {
            return new Error(ErrNotHasAccess);
        }

        return null;
    }
}