<?php

final class RequestLogin
{
    private string $email;
    private string $pass;

    public function __construct(string $email = "", string $pass = "")
    {
        $this->email = $email;
        $this->pass = $pass;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setEmail(string $val): void
    {
        $this->email = $val;
    }

    public function setPass(string $val): void
    {
        $this->pass = $val;
    }

    public function parsePOST(array $post = []): void
    {
        if (isset($post[FieldEmail])) {
            $this->email = trim($post[FieldEmail]);
        }
        if (isset($post[FieldPassword])) {
            $this->pass = trim($post[FieldPassword]);
        }
    }
}