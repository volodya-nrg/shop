<?php

final class Item
{
    public int $itemId = 0;
    public string $name = "";
    public string $slug = "";
    public int $catId = 0;
    public ?string $description = "";
    public int $price = 0;
    public bool $isDisabled = false;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function parse(array $data): void
    {
        $this->itemId = $data["item_id"];
        $this->name = $data["name"];
        $this->slug = $data["slug"];
        $this->catId = $data["cat_id"];
        $this->description = $data["description"];
        $this->price = $data["price"];
        $this->isDisabled = $data["is_disabled"];
        $this->updatedAt = $data["updated_at"];
        $this->createdAt = $data["created_at"];
    }
}