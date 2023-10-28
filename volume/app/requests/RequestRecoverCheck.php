<?php

final class RequestRecoverCheck implements InterfaceConstructData
{
    public string $pass = "";
    public string $passConfirm = "";

    public function __construct(array $data)
    {
        if (isset($data[FieldPassword])) {
            $this->pass = trim($data[FieldPassword]);
        }
        if (isset($data[FieldPasswordConfirm])) {
            $this->passConfirm = trim($data[FieldPasswordConfirm]);
        }
    }
}