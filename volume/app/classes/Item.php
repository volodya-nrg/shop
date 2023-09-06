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
}