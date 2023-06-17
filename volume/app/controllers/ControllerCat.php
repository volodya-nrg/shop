<?php

class ControllerCat extends ControllerBase
{
    public string $title = DicCatalog;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageCat);
    }
}