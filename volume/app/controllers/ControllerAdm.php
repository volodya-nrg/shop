<?php

final class ControllerAdm extends ControllerBase
{
    public string $title = DicAdministration;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        // тут проверить на права
        return new MyResponse(ViewPageAdm);
    }

    public function items(array $args): MyResponse
    {
        // тут список
        return new MyResponse(ViewPageAdmItems);
    }

    public function item(array $args): MyResponse
    {
        // тут конкретный товар
        return new MyResponse(ViewPageAdmItem);
    }
}