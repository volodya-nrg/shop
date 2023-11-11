<?php declare(strict_types=1);

final class RecoverRow
{
    public string $hash = "";
    public int $user_id = 0;
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}