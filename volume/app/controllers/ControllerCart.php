<?php declare(strict_types=1);

final class ControllerCart extends ControllerBase
{
    public string $title = EnumDic::Cart->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageCart);
    }
}