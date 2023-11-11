<?php declare(strict_types=1);

final class CatRow
{
    public int $cat_id = 0;
    public string $name = "";
    public string $slug = "";
    public int $parent_id = 0;
    public int $pos = 0;
    public int $is_disabled = 0;
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}