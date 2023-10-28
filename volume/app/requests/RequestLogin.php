<?php

final class RequestLogin implements InterfaceConstructData
{
    public string $email = "";
    public string $pass = "";

    public function __construct(array $data)
    {
        if (isset($data[FieldEmail])) {
            $this->email = trim($data[FieldEmail]);
        }
        if (isset($data[FieldPassword])) {
            $this->pass = trim($data[FieldPassword]);
        }
    }
}