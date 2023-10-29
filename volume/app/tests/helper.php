<?php

use PHPUnit\Framework\TestCase;

function randomUser(string $pass, string $role = ""): UserTbl
{
    $user = new UserTbl();
    $user->userId = 0;
    $user->email = randomEmail();
    $user->pass = password_hash($pass, PASSWORD_DEFAULT);
    $user->emailHash = null;
    $user->avatar = "/images/external/logo.png";
    $user->birthdayDay = random_int(1, 31);
    $user->birthdayMon = random_int(1, 12);
    $user->role = $role;
    $user->updatedAt = $user->createdAt = date(DatePattern, time());

    return $user;
}

function randomItem($catId): ItemTbl
{
    $item = new ItemTbl();
    $item->itemId = 0;
    $item->title = lorem(10);
    $item->slug = "";
    $item->catId = $catId;
    $item->description = lorem(20);
    $item->price = random_int(1, 1000);
    $item->isDisabled = false;
    $item->updatedAt = $item->createdAt = date(DatePattern, time());

    return $item;
}

function checkBasicData(TestCase $t, int $expectedCode, MyResponse $resp, int $countData, string $viewName = null): void
{
    $t->assertEquals($expectedCode, $resp->getHttpCode());
    $t->assertCount($countData, $resp->data);

    if ($viewName !== null) {
        $t->assertEquals($viewName, $resp->getViewName());
    }

    if ($expectedCode >= 200 && $expectedCode < 300) {
        $t->assertArrayNotHasKey(FieldError, $resp->data);
    } else {
        $t->assertArrayHasKey(FieldError, $resp->data);
    }
}