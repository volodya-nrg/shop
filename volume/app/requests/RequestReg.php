<?php

final class RequestReg
{
    public string $email = "";
    public string $pass = "";
    public string $passConfirm = "";
    public bool $agreement = false;
    public bool $privatePolicy = false;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldEmail])) {
            $this->email = $post[FieldEmail];
        }
        if (isset($post[FieldPassword])) {
            $this->pass = $post[FieldPassword];
        }
        if (isset($post[FieldPasswordConfirm])) {
            $this->passConfirm = $post[FieldPasswordConfirm];
        }
        if (isset($post[FieldAgreement])) {
            $this->agreement = $post[FieldAgreement];
        }
        if (isset($post[FieldPrivacyPolicy])) {
            $this->privatePolicy = $post[FieldPrivacyPolicy];
        }
    }

    public function toArray(): array
    {
        return [
            FieldEmail => $this->email,
            FieldPassword => $this->pass,
            FieldPasswordConfirm => $this->passConfirm,
            FieldAgreement => $this->agreement,
            FieldPrivacyPolicy => $this->privatePolicy,
        ];
    }
}