<?php declare(strict_types=1);

class ControllerBase
{
    protected string $title = "";
    protected string $description = "";

    protected function index(array $args): MyResponse
    {
        return new MyResponse(EnumViewFile::Default);
    }

    protected function view(EnumViewFile $viewFile, string $err = "", array $aData = []): string
    {
        return template($viewFile, $err, $aData);
    }

//    protected function json(array $aData, $code = 0): string
//    {
//        if ($code) {
//            http_response_code($code);
//        }
//
//        header("Content-type: application/json");
//        return json_encode($aData);
//    }
//
//    protected function xml(string $data, $code = 0): string
//    {
//        if ($code) {
//            http_response_code($code);
//        }
//
//        header("Content-Type: text/xml");
//        header("Content-Length: " . strlen($data));
//        return $data;
//    }
    protected function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }
}