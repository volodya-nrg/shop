<?php

class Recover
{
    public string $hash = "";
    public int $userId = 0;

    public function parse(array $data): void
    {
        $this->hash = $data["hash"];
        $this->userId = $data["user_id"];
    }
}