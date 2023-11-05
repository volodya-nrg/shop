<?php

final class ControllerOrder extends ControllerBase
{
    public string $title = EnumDic::Order->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageOrder);
    }
    public function ok(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageOrderOk);
    }
}