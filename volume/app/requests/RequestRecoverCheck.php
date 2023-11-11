<?php declare(strict_types=1);

final class RequestRecoverCheck
{
    public string $pass = "";
    public string $passConfirm = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[EnumField::Password->value])) {
            $this->pass = $post[EnumField::Password->value];
        }
        if (isset($post[EnumField::PasswordConfirm->value])) {
            $this->passConfirm = $post[EnumField::PasswordConfirm->value];
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::Password->value => $this->pass,
            EnumField::PasswordConfirm->value => $this->passConfirm,
        ];
    }
}