<?php

class Cat
{
    public int $catId = 0;
    public string $name = "";
    public string $slug = "";
    public int $parentId = 0;
    public int $pos = 0;
    public bool $isDisabled = false;

    public function parse(array $data): void
    {
        $this->catId = $data["cat_id"];
        $this->name = $data["name"];
        $this->slug = $data["slug"];
        $this->parentId = $data["parent_id"];
        $this->pos = $data["pos"];
        $this->isDisabled = $data["is_disabled"];
    }
}