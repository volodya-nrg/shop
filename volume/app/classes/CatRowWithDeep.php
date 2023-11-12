<?php

final class CatRowWithDeep
{
    public CatRow $catRow;
    public int $deep;

    public function __construct(CatRow $catRow, int $deep)
    {
        $this->catRow = $catRow;
        $this->deep = $deep;
    }
}