<?php

class ControllerMain extends ControllerBase
{
    public string $title = DicPageMain;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageMain);
    }
}
