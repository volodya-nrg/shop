<?php declare(strict_types=1);

final class RequestPaginator
{
    public int $page = 0;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Page->value])) {
            $this->page = (int)$post[EnumField::Page->value];

            if ($this->page) {
                $this->page--;
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