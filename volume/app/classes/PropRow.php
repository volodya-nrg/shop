<?php declare(strict_types=1);

final class PropRow
{
    public int $prop_id = 0;
    public string $name = "";
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}