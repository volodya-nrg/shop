<?php

final class PropTbl implements InterfaceConstructData
{
    public array $fields = ["prop_id", "name"];
    public int $propId = 0;
    public string $name = "";

    public function __construct(array $data = [])
    {
        if (count($data)) {
            $this->propId = $data[$this->fields[0]];
            $this->name = $data[$this->fields[1]];
        }
    }
}