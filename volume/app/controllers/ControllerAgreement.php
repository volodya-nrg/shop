<?php

class ControllerAgreement extends ControllerBase
{
    public string $title = DicAgreement;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageAgreement);
    }
}