<?php

class User
{
    public int $userId = 0;
    public string $email = "";
    public string $pass = "";
    // (не)проверен ли е-мэйл опереляется по наличию или нет хеша в поле
    public string $emailHash = "";
    public ?string $avatar = null;
    // день и месяц рождения необходим в качестве доп. рекламы сайта
    public ?int $birthdayDay = null;
    public ?int $birthdayMon = null;
    public ?string $role = null;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function parse(array $data): void
    {
        $this->userId = $data["user_id"];
        $this->email = $data["email"];
        $this->pass = $data["pass"];
        $this->emailHash = $data["email_hash"];
        $this->avatar = $data["avatar"];
        $this->birthdayDay = $data["birthday_day"];
        $this->birthdayMon = $data["birthday_mon"];
        $this->role = $data["role"];
        $this->updatedAt = $data["updated_at"];
        $this->createdAt = $data["created_at"];
    }
}