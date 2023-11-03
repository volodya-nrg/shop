<?php

final class RequestUser
{
    public int $userId = 0;
    public string $email = "";
    public string $pass = "";
    public ?string $emailHash = null;
    public ?string $avatar = null;
    public ?string $birthdayDay = null;
    public ?string $birthdayMon = null;
    public ?string $role = null;

    public function __construct(array $post = []) // необходимо во время приема данных
    {
        if (count($post)) {
            if (isset($post[FieldUserId])) {
                $this->userId = $post[FieldUserId];
            }
            if (isset($post[FieldEmail])) {
                $this->email = $post[FieldEmail];
            }
            if (isset($post[FieldPassword])) {
                $this->pass = $post[FieldPassword];
            }
            if (isset($post[FieldAvatar])) {
                $this->avatar = $post[FieldAvatar];
            }
            if (isset($post[FieldBirthdayDay])) {
                $this->birthdayDay = $post[FieldBirthdayDay];
            }
            if (isset($post[FieldBirthdayMon])) {
                $this->birthdayMon = $post[FieldBirthdayMon];
            }
            if (isset($post[FieldRole])) {
                $this->role = $post[FieldRole];
            }
        }
    }

    public function toArray(): array
    {
        return [
            FieldUserId => $this->userId,
            FieldEmail => $this->email,
            FieldPassword => $this->pass,
            FieldAvatar => $this->avatar,
            FieldBirthdayDay => $this->birthdayDay,
            FieldBirthdayMon => $this->birthdayMon,
            FieldRole => $this->role,
        ];
    }
}