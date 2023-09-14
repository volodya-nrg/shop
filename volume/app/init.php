<?php
session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('max_execution_time', '3600');
ini_set('memory_limit', '-1');

error_reporting(E_ALL);

$envContent = file_get_contents(dirname(__FILE__) . "/../.env");
$envRows = explode(PHP_EOL, $envContent);
foreach ($envRows as $row) {
    if (empty($row)) {
        continue;
    }
    putenv($row);
}

require_once dirname(__FILE__) . "/consts.php";
require_once dirname(__FILE__) . "/dict.php";
require_once dirname(__FILE__) . "/errs.php";
require_once dirname(__FILE__) . "/funcs.php";

try {
    $PDOPattern = "mysql:host=%s;dbname=%s;charset=%s";
    $PDOConn = sprintf($PDOPattern, DB_HOST, DB_NAME, DB_CHARSET);
    $PDO = new \PDO($PDOConn, DB_USER, DB_PASS,
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
    );
    $PDO->exec("SET NAMES " . DB_CHARSET);
    $GLOBALS["PDO"] = $PDO;
} catch (\PDOException $e) {
    http_response_code(500);
    error_log(sprintf(ErrInWhenTpl, "init.php", "PDO", $e->getMessage()));
    die(ErrNotConnectToDatabase);
}