<?php declare(strict_types=1);

final class ControllerAgreement extends ControllerBase
{
    public string $title = EnumDic::Agreement->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageAgreement);
    }
}