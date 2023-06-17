<?php

class ControllerAgreement extends ControllerBase
{
    public string $title = DicAgreement;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageAgreement);
    }
}