<?php declare(strict_types=1);

function template(EnumViewFile $__view, string $__err = "", array $__data = []): string
{
    $output = "";

    if (ob_start()) {
        //extract($__data);
        require(DIR_VIEWS . "/{$__view->value}");
        $output = ob_get_contents();
        ob_end_clean(); // очистить, но не вывести в браузер
    }

    return $output;
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

function randomInt(int $min, int $max): int
{
    $result = 0;

    try {
        $result = random_int($min, $max);
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    return $result;
}

function lorem(int $words = 32): string
{
    $aWords = [];

    for ($i = 0; $i < $words; $i++) {
        $aWords[] = randomString(randomInt(2, 10));
    }

    return implode(" ", $aWords);
}

function randomEmail(): string
{
    return sprintf("%s@%s.%s", randomString(10), randomString(5), randomString(3));
}

function finePrice(int $price): string
{
    return sprintf("%d", $price);
}

function abort(string $msg): void
{
    error_log($msg);
    exit(1);
}

function translit(string $value)
{
    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    );

    $value = mb_strtolower($value);
    $value = strtr($value, $converter);
    $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
    $value = mb_ereg_replace('[-]+', '-', $value);
    $value = trim($value, '-');

    return $value;
}

/**
 * @return CatRowWithDeep[]
 */
function catsTreeAsList(CatsTree $catsTree, $deep = 0): array
{
    $result = [];

    foreach ($catsTree->childs as $catTree) {
        $result[] = new CatRowWithDeep($catTree->catRow, $deep);

        if (count($catTree->childs)) {
            $result = array_merge($result, catsTreeAsList($catTree, $deep + 1));
        }
    }

    return $result;
}

function classMethodArgs(): array
{
    $aURLData = parse_url($_SERVER['REQUEST_URI']);
    $aURLPath = explode("/", $aURLData["path"]);
    array_shift($aURLPath);
    $class = "Controller" . ucfirst(!empty($aURLPath[0]) ? trim(str_replace("-", "", $aURLPath[0])) : "main");
    $method = !empty($aURLPath[1]) ? trim(str_replace("-", "_", $aURLPath[1])) : "index";
    $aArgs = count($aURLPath) > 2 ? array_slice($aURLPath, 2) : [];

    return [$class, $method, $aArgs];
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