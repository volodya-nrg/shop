<?php

final class ControllerNotFound extends ControllerBase
{
    public string $title = DicPageNotFound;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageNotFound, 404);
    }
}