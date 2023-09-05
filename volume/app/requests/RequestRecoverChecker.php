<?php

final class RequestRecoverChecker
{
    private string $pass;
    private string $passConfirm;

    public function __construct(string $pass = "", string $passConfirm = "")
    {
        $this->pass = $pass;
        $this->passConfirm = $passConfirm;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getPassConfirm(): string
    {
        return $this->passConfirm;
    }

    public function setPass(string $val): void
    {
        $this->pass = $val;
    }

    public function setPassConfirm(string $val): void
    {
        $this->passConfirm = $val;
    }

    public function parsePOST(array $post = []): void
    {
        if (isset($post[FieldPassword])) {
            $this->pass = trim($post[FieldPassword]);
        }
        if (isset($post[FieldPasswordConfirm])) {
            $this->passConfirm = trim($post[FieldPasswordConfirm]);
        }
    }
}