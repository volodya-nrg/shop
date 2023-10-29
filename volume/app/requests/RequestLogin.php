<?php

final class RequestLogin
{
    public string $email = "";
    public string $pass = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldEmail])) {
            $this->email = $post[FieldEmail];
        }
        if (isset($post[FieldPassword])) {
            $this->pass = $post[FieldPassword];
        }
    }
    public function toArray(): array
    {
        return [
            FieldEmail => $this->email,
            FieldPassword => $this->pass,
        ];
    }
}