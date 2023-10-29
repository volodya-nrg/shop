<?php

final class SettingRow implements InterfaceConstructData
{
    public int $setting_id = 0;
    public string $value = "";

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}