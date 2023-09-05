<?php

final class ControllerCart extends ControllerBase
{
    public string $title = DicAdministration;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageCart);
    }
}