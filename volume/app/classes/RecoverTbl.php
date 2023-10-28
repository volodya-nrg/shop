<?php

final class RecoverTbl implements InterfaceConstructData
{
    public array $fields = ["hash", "user_id"];
    public string $hash = "";
    public int $userId = 0;

    public function __construct(array $data)
    {
        $this->hash = $data[$this->fields[0]];
        $this->userId = $data[$this->fields[1]];
    }
}