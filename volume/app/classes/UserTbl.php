<?php

final class UserTbl implements InterfaceConstructData
{
    public array $fields = ["user_id", "email", "pass", "email_hash", "avatar", "birthday_day", "birthday_mon", "role", "updated_at", "created_at"];
    public int $userId = 0;
    public string $email = "";
    public string $pass = "";
    // (не)проверен ли е-мэйл опереляется по наличию или нет хеша в поле
    public ?string $emailHash = null;
    public ?string $avatar = null;
    // день и месяц рождения необходим в качестве доп. рекламы сайта
    public ?int $birthdayDay = null;
    public ?int $birthdayMon = null;
    public ?string $role = null;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function __construct(array $data)
    {
        $this->userId = $data[$this->fields[0]];
        $this->email = $data[$this->fields[1]];
        $this->pass = $data[$this->fields[2]];
        $this->emailHash = $data[$this->fields[3]];
        $this->avatar = $data[$this->fields[4]];
        $this->birthdayDay = $data[$this->fields[5]];
        $this->birthdayMon = $data[$this->fields[6]];
        $this->role = $data[$this->fields[7]];
        $this->updatedAt = $data[$this->fields[8]];
        $this->createdAt = $data[$this->fields[9]];
    }
}