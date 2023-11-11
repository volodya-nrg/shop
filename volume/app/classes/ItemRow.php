<?php declare(strict_types=1);

final class ItemRow extends XRow
{
    public int $item_id = 0;
    public string $title = "";
    public string $slug = "";
    public int $cat_id = 0;
    public ?string $description = null;
    public int $price = 0;
    public int $is_disabled = 0;
    public string $created_at = "";
    public string $updated_at = "";
}