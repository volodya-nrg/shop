<?php

function template(string $__view, array $__data = [])
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

function redirect($url)
{
    header("Location: {$url}");
    exit;
}

function randomString(int $length = 20): string
{
    $characters = "abcdefghijklmnopqrstuvwxyz";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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
    } else if (file_exists(DIR_CLASSES . "/" . $file)) { // поищем в папке classes
        $aParts[] = DIR_CLASSES;
    }

    $aParts[] = $file;
    $filepath = implode("/", $aParts);

    if (file_exists($filepath)) {
        require_once $filepath;
    }
});