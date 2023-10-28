<?php

final class RequestRecover implements InterfaceConstructData
{
    public string $email = "";

    public function __construct(array $data)
    {
        if (isset($data[FieldEmail])) {
            $this->email = trim($data[FieldEmail]);
        }
    }
}