<?php

final class RequestRecoverCheck
{
    public string $pass = "";
    public string $passConfirm = "";

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (isset($post[FieldPassword])) {
            $this->pass = $post[FieldPassword];
        }
        if (isset($post[FieldPasswordConfirm])) {
            $this->passConfirm = $post[FieldPasswordConfirm];
        }
    }

    public function toArray(): array
    {
        return [
            FieldPassword => $this->pass,
            FieldPasswordConfirm => $this->passConfirm,
        ];
    }
}