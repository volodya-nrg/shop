<?php

class RequestRecover
{
    private string $email;

    public function __construct(string $email = "")
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $val): void
    {
        $this->email = $val;
    }

    public function parsePOST(array $post = []): void
    {
        if (isset($post[FieldEmail])) {
            $this->email = trim($post[FieldEmail]);
        }
    }
}