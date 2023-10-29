<?php

final class ControllerLogout extends ControllerBase
{
    public function index(array $args): MyResponse
    {
        if (isset($_SESSION[FieldProfile])) {
            unset($_SESSION[FieldProfile]);
        }
        if (isset($_SESSION[FieldAdmin])) {
            unset($_SESSION[FieldAdmin]);
        }
        if (!$_SERVER[FieldModeIsTest]) {
            redirect("/");
        }

        return new MyResponse("");
    }
}