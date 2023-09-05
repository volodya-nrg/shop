<?php

class ControllerBase
{
    protected string $title = "";
    protected string $description = "";

    protected function index(array $args): MyResponse
    {
        return new MyResponse("");
    }

    protected function view(string $tplPath, array $aData = []): string
    {
        return template($tplPath, $aData);
    }

    protected function json(array $aData, $code = 0): string
    {
        if ($code) {
            http_response_code($code);
        }

        header("Content-type: application/json");
        return json_encode($aData);
    }

    protected function xml(string $data, $code = 0): string
    {
        if ($code) {
            http_response_code($code);
        }

        header("Content-Type: text/xml");
        header("Content-Length: " . strlen($data));
        return $data;
    }
}