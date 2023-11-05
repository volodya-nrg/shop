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
        if (isset($post[EnumField::Email->value])) {
            $this->email = $post[EnumField::Email->value];
        }
        if (isset($post[EnumField::Password->value])) {
            $this->pass = $post[EnumField::Password->value];
        }
        if (isset($post[EnumField::PasswordConfirm->value])) {
            $this->passConfirm = $post[EnumField::PasswordConfirm->value];
        }
        if (isset($post[EnumField::Agreement->value])) {
            $this->agreement = $post[EnumField::Agreement->value];
        }
        if (isset($post[EnumField::PrivacyPolicy->value])) {
            $this->privatePolicy = $post[EnumField::PrivacyPolicy->value];
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::Email->value => $this->email,
            EnumField::Password->value => $this->pass,
            EnumField::PasswordConfirm->value => $this->passConfirm,
            EnumField::Agreement->value => $this->agreement,
            EnumField::PrivacyPolicy->value => $this->privatePolicy,
        ];
    }
}