<?php

final class SettingTbl implements InterfaceConstructData
{
    public array $fields = ["setting_id", "value"];
    public int $settingId = 0;
    public string $value = "";

    public function __construct(array $data)
    {
        $this->settingId = $data[$this->fields[0]];
        $this->value = $data[$this->fields[1]];
    }
}