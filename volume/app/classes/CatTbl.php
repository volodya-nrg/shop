<?php

final class CatTbl implements InterfaceConstructData
{
    public array $fields = ["cat_id", "name", "slug", "parent_id", "pos", "is_disabled"];
    public int $catId = 0;
    public string $name = "";
    public string $slug = "";
    public int $parentId = 0;
    public int $pos = 0;
    public bool $isDisabled = false;

    public function __construct(array $data)
    {
        $this->catId = $data[$this->fields[0]];
        $this->name = $data[$this->fields[1]];
        $this->slug = $data[$this->fields[2]];
        $this->parentId = $data[$this->fields[3]];
        $this->pos = $data[$this->fields[4]];
        $this->isDisabled = $data[$this->fields[5]];
    }
}