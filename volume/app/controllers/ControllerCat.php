<?php declare(strict_types=1);

final class ControllerCat extends ControllerBase
{
    public string $title = EnumDic::Catalog->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageCat);
    }
}