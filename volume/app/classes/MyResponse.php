<?php declare(strict_types=1);

final class MyResponse
{
    private EnumViewFile $view;
    private int $httpCode;
    public array $data; // сюда помещаются все данные, каторые используются во view

    public function __construct(EnumViewFile $view, int $httpCode = 200, array $data = [])
    {
        $this->view = $view;
        $this->httpCode = $httpCode;
        $this->data = $data;
    }

    public function getView(): EnumViewFile
    {
        return $this->view;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function setHttpCode(int $value): void
    {
        $this->httpCode = $value;
    }
}