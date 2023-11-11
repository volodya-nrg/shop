<?php declare(strict_types=1);

final class Tab
{
    public string $name;
    public string $link;
    public bool $isActive;

    public function __construct(string $name = "", string $link = "", bool $isActive = false)
    {
        $this->name = $name;
        $this->link = $link;
        $this->isActive = $isActive;
    }
}