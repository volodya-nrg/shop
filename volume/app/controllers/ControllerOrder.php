<?php

class ControllerOrder extends ControllerBase
{
    public string $title = DicOrder;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageOrder);
    }
    public function ok(array $args): MyResponse
    {
        return new MyResponse(ViewPageOrderOk);
    }
}