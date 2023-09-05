<?php

final class ControllerMain extends ControllerBase
{
    public string $title = DicPageMain;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        return new MyResponse(ViewPageMain);
    }
}
