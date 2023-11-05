<?php

final class RequestLogin
{
    public string $email = "";
    public string $pass = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Email->value])) {
            $this->email = $post[EnumField::Email->value];
        }
        if (isset($post[EnumField::Password->value])) {
            $this->pass = $post[EnumField::Password->value];
        }
    }
    public function toArray(): array
    {
        return [
            EnumField::Email->value => $this->email,
            EnumField::Password->value => $this->pass,
        ];
    }
}