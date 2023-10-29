<?php

final class PropRow implements InterfaceConstructData
{
    public int $prop_id = 0;
    public string $name = "";

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}