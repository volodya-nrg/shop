<?php

class ControllerProfile extends ControllerBase
{
    public string $title = DicAdministration;
    public string $description = "";

    public function index(array $args): Response
    {
        return new Response(ViewPageProfile);
    }
}