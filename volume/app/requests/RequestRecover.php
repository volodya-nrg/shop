<?php

final class RequestRecover
{
    public string $email = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldEmail])) {
            $this->email = $post[FieldEmail];
        }
    }

    public function toArray(): array
    {
        return [
            FieldEmail => $this->email,
        ];
    }
}