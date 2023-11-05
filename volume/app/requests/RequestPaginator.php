<?php

final class RequestPaginator
{
    public int $limit = 0;
    public int $offset = 0;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Limit->value])) {
            $this->limit = $post[EnumField::Limit->value];
        }
        if (isset($post[EnumField::Offset->value])) {
            $this->offset = $post[EnumField::Offset->value];
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::Limit->value => $this->limit,
            EnumField::Offset->value => $this->offset,
        ];
    }
}