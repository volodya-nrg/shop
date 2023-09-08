<?php

foreach (explode(PHP_EOL, file_get_contents(dirname(__FILE__) . "/../.env")) as $row) {
    if (empty($row)) {
        continue;
    }
    putenv($row);
}

require_once dirname(__FILE__) . "/constants.php";
require_once dirname(__FILE__) . "/dictionary.php";
require_once dirname(__FILE__) . "/errors.php";
require_once dirname(__FILE__) . "/functions.php";

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
    die(DEV_MODE ? $e->getMessage() : ErrNotConnectToDatabase);
}