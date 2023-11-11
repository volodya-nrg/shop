<?php declare(strict_types=1);

final class AdmListItem
{
    public int $pos = 0;
    public string $title = "";
    public int $id = 0;

    public function __construct($pos, $title, $id)
    {
        $this->pos = $pos;
        $this->title = $title;
        $this->id = $id;
    }
}