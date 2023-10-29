<?php

final class RequestPaginator
{
    public int $limit = 0;
    public int $offset = 0;

    public function toArray(): array
    {
        return [
            FieldLimit => $this->limit,
            FieldOffset => $this->offset,
        ];
    }
}