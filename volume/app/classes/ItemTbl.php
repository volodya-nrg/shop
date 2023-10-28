<?php

final class ItemTbl implements InterfaceConstructData
{
    public array $fields = ["item_id", "title", "slug", "cat_id", "description", "price", "is_disabled", "updated_at", "created_at"];
    public int $itemId = 0;
    public string $title = "";
    public string $slug = "";
    public int $catId = 0;
    public ?string $description = null;
    public int $price = 0;
    public bool $isDisabled = false;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function __construct(array $data)
    {
        $this->itemId = $data[$this->fields[0]];
        $this->title = $data[$this->fields[1]];
        $this->slug = $data[$this->fields[2]];
        $this->catId = $data[$this->fields[3]];
        $this->description = $data[$this->fields[4]];
        $this->price = $data[$this->fields[5]];
        $this->isDisabled = $data[$this->fields[6]];
        $this->updatedAt = $data[$this->fields[7]];
        $this->createdAt = $data[$this->fields[8]];
    }
}