<?php declare(strict_types=1);

final class UserRow
{
    public int $user_id = 0;
    public string $email = "";
    public string $pass = "";
    // (не)проверен ли е-мэйл опереляется по наличию или нет хеша в поле
    public ?string $email_hash = null;
    public ?string $avatar = null;
    // день и месяц рождения необходим в качестве доп. рекламы сайта
    public ?int $birthday_day = null;
    public ?int $birthday_mon = null;
    public ?string $role = null;
    public string $created_at = "";
    public string $updated_at = "";
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}