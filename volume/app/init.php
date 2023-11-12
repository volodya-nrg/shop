<?php declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('max_execution_time', '3600');
ini_set('memory_limit', '-1');
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 0);

session_start();
error_reporting(E_ALL);
$_SERVER[EnumField::ModeIsProd->value] = false;

// получим переменные окружения
$envRows = explode(PHP_EOL, file_get_contents(dirname(__FILE__) . "/../.env"));
foreach ($envRows as $row) {
    if (empty($row)) {
        continue;
    }
    putenv($row);
}

// подключим начальные файлы (константы, enum-ы, ф-ии)
foreach (glob(dirname(__FILE__) . "/deps/*.php") as $filepath) {
    require_once $filepath;
}

// подключимся к базе данных
try {
    $PDOPattern = "mysql:host=%s;dbname=%s;charset=%s";
    $PDOConn = sprintf($PDOPattern, DB_HOST, DB_NAME, DB_CHARSET);
    $PDO = new \PDO($PDOConn, DB_USER, DB_PASS,
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
    );
    $PDO->exec("SET NAMES " . DB_CHARSET);
    $GLOBALS["PDO"] = $PDO; // явно регисрируем чтоб было видно глобально (global $PDO)
} catch (\PDOException $e) {
    http_response_code(500);
    error_log(sprintf(EnumErr::InWhenTpl->value, __FILE__, "PDO", $e->getMessage()));
    die(EnumErr::NotConnectToDatabase->value);
}