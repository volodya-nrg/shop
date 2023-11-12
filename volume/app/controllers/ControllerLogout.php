<?php declare(strict_types=1);

final class ControllerLogout extends ControllerBase
{
    public function index(array $args): MyResponse
    {
        if (isset($_SESSION[EnumField::Profile->value])) {
            unset($_SESSION[EnumField::Profile->value]);
        }
        if (isset($_SESSION[EnumField::Admin->value])) {
            unset($_SESSION[EnumField::Admin->value]);
        }
        if ($_SERVER[EnumField::ModeIsProd->value]) {
            $this->redirect("/");
        }

        return new MyResponse(EnumViewFile::Default);
    }
}