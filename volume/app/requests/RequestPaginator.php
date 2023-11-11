<?php declare(strict_types=1);

final class RequestPaginator
{
    public int $page = 0;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Page->value])) {
            $pageLoc = (int)$post[EnumField::Page->value];
            if ($pageLoc > 0) {
                $this->page = $pageLoc - 1;
            }
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::Page->value => $this->page,
        ];
    }
}