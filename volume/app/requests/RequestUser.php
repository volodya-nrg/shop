<?php declare(strict_types=1);

final class RequestUser
{
    public int $userId = 0;
    public string $email = "";
    public string $pass = "";
    public ?string $emailHash = null;
    public ?string $avatar = null;
    public ?int $birthdayDay = null;
    public ?int $birthdayMon = null;
    public ?string $role = null;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (count($post)) {
            if (isset($post[EnumField::UserId->value])) {
                $this->userId = $post[EnumField::UserId->value];
            }
            if (isset($post[EnumField::Email->value])) {
                $this->email = $post[EnumField::Email->value];
            }
            if (isset($post[EnumField::Password->value])) {
                $this->pass = $post[EnumField::Password->value];
            }
            if (isset($post[EnumField::Avatar->value])) {
                $this->avatar = $post[EnumField::Avatar->value];
            }
            if (isset($post[EnumField::BirthdayDay->value])) {
                $this->birthdayDay = $post[EnumField::BirthdayDay->value];
            }
            if (isset($post[EnumField::BirthdayMon->value])) {
                $this->birthdayMon = $post[EnumField::BirthdayMon->value];
            }
            if (isset($post[EnumField::Role->value])) {
                $this->role = $post[EnumField::Role->value];
            }
        }
    }

    public function toArray(): array
    {
        return [
            EnumField::UserId->value => $this->userId,
            EnumField::Email->value => $this->email,
            EnumField::Password->value => $this->pass,
            EnumField::Avatar->value => $this->avatar,
            EnumField::BirthdayDay->value => $this->birthdayDay,
            EnumField::BirthdayMon->value => $this->birthdayMon,
            EnumField::Role->value => $this->role,
        ];
    }
}