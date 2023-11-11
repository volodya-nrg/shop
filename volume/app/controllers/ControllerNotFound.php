<?php declare(strict_types=1);

final class ControllerNotFound extends ControllerBase
{
    public string $title = EnumDic::PageNotFound->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageNotFound, 404);
    }
}