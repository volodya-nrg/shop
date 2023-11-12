<?php declare(strict_types=1);

final class ControllerContacts extends ControllerBase
{
    public string $title = EnumDic::Contacts->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageContacts);
    }
}