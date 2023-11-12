<?php declare(strict_types=1);

final class MyResponse
{
    public EnumViewFile $view;
    public string $err;
    public int $code;
    public array $data; // сюда помещаются все данные, каторые используются во view

    public function __construct(EnumViewFile $view, int $code = 200, array $data = [])
    {
        $this->view = $view;
        $this->code = $code;
        $this->data = $data;
    }
}