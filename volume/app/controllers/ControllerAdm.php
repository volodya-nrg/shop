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
            $item = new ItemTbl($_POST);

            // тут надо картинки еще подхватить

            $serviceItems = new ServiceItems($item);

            $itemId = $serviceItems->createOrUpdate($item);
            if ($itemId instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "createOrUpdate", $itemId->getMessage()));
                return $resp;
            }
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