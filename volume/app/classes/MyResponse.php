<?php

class MyResponse
{
    private string $viewName;
    private int $httpCode;
    public array $data; // сюда помещаются все данные, каторые используются во view

    public function __construct(string $viewName, int $httpCode = 200, array $data = [])
    {
        $this->viewName = $viewName;
        $this->httpCode = $httpCode;
        $this->data = $data;
    }

    public function getViewName(): string
    {
        return $this->viewName;
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