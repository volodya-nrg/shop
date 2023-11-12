<?php declare(strict_types=1);

final class ControllerSearch extends ControllerBase
{
    public string $title = EnumDic::Search->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageSearch);
    }
}