<?php declare(strict_types=1);

final class RequestRecover
{
    public string $email = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Email->value])) {
            $this->email = $post[EnumField::Email->value];
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::Email->value => $this->email,
        ];
    }
}