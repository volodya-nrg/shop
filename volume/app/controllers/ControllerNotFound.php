<?php

class ControllerNotFound extends ControllerBase
{
    public string $title = DicPageNotFound;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageNotFound, 404);
    }
}