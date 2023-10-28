<?php

final class RequestReg
{
    public string $email = "";
    public string $pass = "";
    public string $passConfirm = "";
    public bool $agreement = false;
    public bool $privatePolicy = false;

    public function __construct(array $data)
    {
        if (isset($data[FieldEmail])) {
            $this->email = trim($data[FieldEmail]);
        }
        if (isset($data[FieldPassword])) {
            $this->pass = trim($data[FieldPassword]);
        }
        if (isset($data[FieldPasswordConfirm])) {
            $this->passConfirm = trim($data[FieldPasswordConfirm]);
        }
        if (isset($data[FieldAgreement])) {
            $this->agreement = !empty($data[FieldAgreement]);
        }
        if (isset($data[FieldPrivacyPolicy])) {
            $this->privatePolicy = !empty($data[FieldPrivacyPolicy]);
        }
    }
}