<?php

final class ServiceSettings extends ServiceDB
{
    protected string $table = "settings";

    public function __construct(array $fields)
    {
        parent::__construct();
        $this->fields = $fields;
    }
}