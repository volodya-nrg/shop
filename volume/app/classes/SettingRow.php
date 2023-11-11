<?php declare(strict_types=1);

final class SettingRow
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