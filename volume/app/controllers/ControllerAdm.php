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
        // тут проверить на права
        return new MyResponse(ViewPageAdm);
    }

    public function items(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }
        // тут список
        return new MyResponse(ViewPageAdmItems);
    }

    public function item(array $args): MyResponse
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            return new MyResponse(ViewPageAccessDined, 401, [FieldError => $err->getMessage()]);
        }
        // тут конкретный товар
        return new MyResponse(ViewPageAdmItem);
    }

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[FieldAdmin])) {
            return new Error(ErrNotHasAccess);
        }

        return null;
    }
}