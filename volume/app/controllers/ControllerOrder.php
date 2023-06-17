<?php

class ControllerOrder extends ControllerBase
{
    public string $title = DicOrder;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageOrder);
    }
    public function ok(array $args): Response
    {
        return new Response(ViewPageOrderOk);
    }
}