<?php

function getRandomUser(string $pass, string $role = ""): User
{
    $user = new User();
    $user->userId = 0;
    $user->email = randomEmail();
    $user->pass = password_hash($pass, PASSWORD_DEFAULT);
    $user->emailHash = "";
    $user->avatar = "/images/external/logo.png";
    $user->birthdayDay = random_int(1, 31);
    $user->birthdayMon = random_int(1, 12);
    $user->role = $role;
    $user->updatedAt = $user->createdAt = date(DatePattern, time());

    return $user;
}