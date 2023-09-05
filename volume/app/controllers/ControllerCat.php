<?php

final class ControllerCat extends ControllerBase
{
    public string $title = DicCatalog;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageCat);
    }
}