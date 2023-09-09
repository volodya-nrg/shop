<?php

final class Prop
{
    public int $propId = 0;
    public string $name = "";

    public function parse(array $data): void
    {
        $this->propId = $data["prop_id"];
        $this->name = $data["name"];
    }
}