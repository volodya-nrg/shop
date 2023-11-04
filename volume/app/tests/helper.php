<?php

use PHPUnit\Framework\TestCase;

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

function randomIP(): string
{
    $result = long2ip(rand(0, 4294967295));
    if ($result === false) {
        return "127.0.0.1";
    }

    return $result;
}