<?php

final class ServiceSettings extends ServiceDB
{
    protected string $table = "settings";

    public function __construct(SettingTbl $item)
    {
        parent::__construct();
        $this->fields = $item->fields;
    }
}