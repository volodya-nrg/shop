<?php

final class RequestPaginator
{
    public int $limit = 0;
    public int $offset = 0;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldLimit])) {
            $this->limit = $post[FieldLimit];
        }
        if (isset($post[FieldOffset])) {
            $this->offset = $post[FieldOffset];
        }
    }

    public function toArray(): array
    {
        return [
            FieldLimit => $this->limit,
            FieldOffset => $this->offset,
        ];
    }
}