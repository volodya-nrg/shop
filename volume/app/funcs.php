<?php

function template(string $__view, array $__data = []): string
{
    $output = "";

    if (ob_start()) {
        //extract($__data);
        require($__view);
        $output = ob_get_contents();
        ob_end_clean(); // очистить, но не вывести в браузер
    }

    return $output;
}

function redirect($url): never
{
    header("Location: {$url}");
    exit;
}

function randomString(int $len = 32, bool $isAttachNumber = false): string
{
    $chars = "abcdefghijklmnopqrstuvwxyz";

    if ($isAttachNumber) {
        $chars .= "0123456789";
    }

    $charsLen = strlen($chars);
    $result = "";

    for ($i = 0; $i < $len; $i++) {
        $result .= $chars[rand(0, $charsLen - 1)];
    }

    return $result;
}

function randomEmail(): string
{
    return sprintf("%s@%s.%s", randomString(10), randomString(5), randomString(3));
}

function finePrice(int $price): string
{
    return sprintf("%d", $price);
}

// ---------------------------------------------------------------------------------------------------------------------

spl_autoload_register(function ($className): void {
    $aParts = [];
    $file = "{$className}.php";

    if (substr($className, 0, mb_strlen("Controller")) === "Controller") {
        $aParts[] = DIR_CONTROLLERS;
    } else if (substr($className, 0, mb_strlen("Request")) === "Request") {
        $aParts[] = DIR_REQUESTS;
    } else if (substr($className, 0, mb_strlen("Service")) === "Service") {
        $aParts[] = DIR_SERVICES;
    } else if (substr($className, 0, mb_strlen("Interface")) === "Interface") {
        $aParts[] = DIR_INTERFACES;
    } else if (file_exists(DIR_CLASSES . "/" . $file)) { // иначе поищем в папке classes
        $aParts[] = DIR_CLASSES;
    }

    $aParts[] = $file;
    $filepath = implode("/", $aParts);

    if (file_exists($filepath)) {
        require_once $filepath;
    }
});