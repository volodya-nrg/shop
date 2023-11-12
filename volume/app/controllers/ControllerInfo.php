<?php declare(strict_types=1);

final class ControllerInfo extends ControllerBase
{
    public string $title = EnumDic::Info->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageInfo);
    }
}