<?php

class User
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
}