<?php declare(strict_types=1);

final class ControllerCheckout extends ControllerBase
{
    public string $title = EnumDic::Administration->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageCheckout);
    }
}