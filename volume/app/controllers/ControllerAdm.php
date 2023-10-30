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

        // вытащить из базы список

        // тут список
        return new MyResponse(ViewPageAdmItems);
    }

    public function item(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageAdmItem);

        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }

        if (isset($_POST) && count($_POST)) {
            $item = new ItemRow($_POST);

            // тут надо картинки еще подхватить

            $serviceItems = new ServiceItems();

            $itemId = $serviceItems->createOrUpdate($item);
            if ($itemId instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "createOrUpdate", $itemId->getMessage()));
                return $resp;
            }
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

            $itemId = $serviceCats->createOrUpdate($cat);
            if ($itemId instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "createOrUpdate", $itemId->getMessage()));
                return $resp;
            }

            $cat->cat_id = $itemId;

            $resp->data = []; // т.к. происходит далее редирект, то data нам не нужен
            $resp->data[FieldCatId] = $cat->cat_id; // нужен для теста
        }

        // если запрашивают конкретную запись, то получим ее
        if (!empty($_GET[FieldCatId])) {
            $catId = $_GET[FieldCatId];

            $result = $serviceCats->one($catId);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
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

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[FieldAdmin])) {
            return new Error(ErrNotHasAccess);
        }

        return null;
    }
}