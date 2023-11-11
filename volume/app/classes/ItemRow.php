<?php declare(strict_types=1);

final class ItemRow
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
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}