<?php

final class ControllerProfile extends ControllerBase
{
    public string $title = EnumDic::Administration->value;
    public string $description = "";

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $err = $this->checkRule();
        if ($err instanceof Error) {
            throw new Exception($err->getMessage());
        }
    }

    public function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::PageProfile);
    }

    private function checkRule(): Error|null
    {
        if (empty($_SESSION[EnumField::Profile->value])) {
            return new Error(EnumErr::NotHasAccess->value);
        }

        return null;
    }
}