<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

function checkBasicData(TestCase $t, int $expectedCode, MyResponse $resp, int $countData, EnumViewFile $view = null): void
{
    $t->assertEquals($expectedCode, $resp->getHttpCode());
    $t->assertCount($countData, $resp->data);

    if ($view !== null) {
        $t->assertEquals($view, $resp->getView());
    }

    if ($expectedCode >= 200 && $expectedCode < 300) {
        $t->assertArrayNotHasKey(EnumField::Error->value, $resp->data);
    } else {
        $t->assertArrayHasKey(EnumField::Error->value, $resp->data);
    }
}

function randomIP(): string
{
    $result = long2ip(rand(0, 4294967295));
    if ($result === false) {
        return "127.0.0.1";
    }

    return $result;
}