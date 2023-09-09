<?php

class Info
{
    public int $userId = 0;
    public string $email = "";
    public string $pass = "";
    // (не)проверен ли е-мэйл опереляется по наличию или нет хеша в поле
    public string $hashForCheckEmail = "";
    public ?string $avatar = null;
    // день и месяц рождения необходим в качестве доп. рекламы сайта
    public ?int $birthdayDay = null;
    public ?int $birthdayMon = null;
    public string $updatedAt = "";
    public string $createdAt = "";

    public function parse(array $data): void
    {
        $this->userId = $data["user_id"];
        $this->email = $data["email"];
        $this->pass = $data["pass"];
        $this->hashForCheckEmail = $data["hash_for_check_email"];
        $this->avatar = $data["avatar"];
        $this->birthdayDay = $data["birthday_day"];
        $this->birthdayMon = $data["birthday_mon"];
        $this->updatedAt = $data["updated_at"];
        $this->createdAt = $data["created_at"];
    }
}