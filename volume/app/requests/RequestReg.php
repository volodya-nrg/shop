<?php

final class RequestReg
{
    private string $email;
    private string $pass;
    private string $passConfirm;
    private bool $agreement;
    private bool $privatePolicy;

    public function __construct(string $email = "", string $pass = "", string $passConfirm = "", bool $agreement = false, bool $privatePolicy = false)
    {
        $this->email = $email;
        $this->pass = $pass;
        $this->passConfirm = $passConfirm;
        $this->agreement = $agreement;
        $this->privatePolicy = $privatePolicy;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getPassConfirm(): string
    {
        return $this->passConfirm;
    }

    public function getAgreement(): bool
    {
        return $this->agreement;
    }

    public function getPrivatePolicy(): bool
    {
        return $this->privatePolicy;
    }

    public function setEmail(string $val): void
    {
        $this->email = $val;
    }

    public function setPass(string $val): void
    {
        $this->pass = $val;
    }

    public function setPassConfirm(string $val): void
    {
        $this->passConfirm = $val;
    }

    public function setAgreement(bool $val): void
    {
        $this->agreement = $val;
    }

    public function setPrivatePolicy(bool $val): void
    {
        $this->privatePolicy = $val;
    }

    public function parsePOST(array $post = []): void
    {
        if (isset($post[FieldEmail])) {
            $this->email = trim($post[FieldEmail]);
        }
        if (isset($post[FieldPassword])) {
            $this->pass = trim($post[FieldPassword]);
        }
        if (isset($post[FieldPasswordConfirm])) {
            $this->passConfirm = trim($post[FieldPasswordConfirm]);
        }
        if (isset($post[FieldAgreement])) {
            $this->agreement = $post[FieldAgreement] === "on";
        }
        if (isset($post[FieldPrivacyPolicy])) {
            $this->privatePolicy = $post[FieldPrivacyPolicy] === "on";
        }
    }
}