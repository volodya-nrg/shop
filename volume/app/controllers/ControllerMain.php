<?php

final class ControllerMain extends ControllerBase
{
    public string $title = EnumDic::PageMain->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageMain);
    }
}
