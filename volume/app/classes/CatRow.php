<?php

final class CatRow implements InterfaceConstructData
{
    public int $cat_id = 0;
    public string $name = "";
    public string $slug = "";
    public int $parent_id = 0;
    public int $pos = 0;
    public bool $is_disabled = false;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}