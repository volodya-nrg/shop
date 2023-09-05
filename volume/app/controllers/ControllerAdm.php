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
}