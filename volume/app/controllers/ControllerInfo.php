<?php

final class ControllerInfo extends ControllerBase
{
    public string $title = EnumDic::Administration->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageInfo);
    }
}