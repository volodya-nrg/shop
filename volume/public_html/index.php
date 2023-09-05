<?php
session_start();
const DEV_MODE = true;

if (DEV_MODE) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('max_execution_time', '3600');
    ini_set('memory_limit', '-1');
    error_reporting(E_ALL);
}

$filename_env = ".env";
foreach (explode(PHP_EOL, file_get_contents("../{$filename_env}")) as $row) {
    if (empty($row)) {
        continue;
    }
    putenv($row);
}

require_once "../app/init.php";

try {
    $PDOPattern = "mysql:host=%s;dbname=%s;charset=%s";
    $PDOConn = sprintf($PDOPattern, DB_HOST, DB_NAME, DB_CHARSET);
    $PDO = new \PDO($PDOConn, DB_USER, DB_PASS,
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
    );
    $PDO->exec("SET NAMES " . DB_CHARSET);
} catch (\PDOException $e) {
    http_response_code(500);
    die(DEV_MODE ? $e->getMessage() : ErrNotConnectToDatabase);
}

$aURLData = parse_url($_SERVER['REQUEST_URI']);
$aURLPath = explode("/", $aURLData["path"]);
array_shift($aURLPath);
$class = "Controller" . ucfirst(!empty($aURLPath[0]) ? trim(str_replace("-", "", $aURLPath[0])) : "main");
$method = !empty($aURLPath[1]) ? trim(str_replace("-", "_", $aURLPath[1])) : "index";
$aArgs = count($aURLPath) > 2 ? array_slice($aURLPath, 2) : [];

try {
    if (!class_exists($class)) {
        throw new Exception(ErrNotFoundClass);
    }
    if ($class === "ControllerItem") {
        if ($method === "index" || count($aArgs)) {
            throw new Exception(ErrNotFoundMethod);
        }
        $aArgs = [$method];
        $method = "index";
    } else if (!method_exists($class, $method)) {
        throw new Exception(ErrNotFoundMethod);
    }

    $oPage = new $class();
    if (!is_callable([$oPage, $method])) { // проверим можно ли вызывать (public, protected). Если private, то не получится вызвать.
        throw new Exception(ErrMethodNotAllowed);
    }
    $resp = call_user_func([$oPage, $method], $aArgs);

//    // если json, xml
//    foreach (headers_list() as $val) {
//        if (stristr($val, 'Content-Type: application/json') || stristr($val, 'Content-Type: text/xml')) {
//            exit($output);
//        }
//    }
} catch (Exception $e) {
    $oPage = new ControllerNotFound();
    $resp = $oPage->index($aArgs);

    if (DEV_MODE) {
        echo $e->getMessage();
    }
}

http_response_code($resp->getHttpCode());
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="<?php echo $oPage->description; ?>">

    <title><?php echo $oPage->title ?></title>

    <link type="image/x-icon" rel="icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css.php"/>
    <script src="/js.php"></script>
</head>
<body id="body">
<header class="header">
    <div class="layer-center">
        <div class="header-block">
            <a class="header-block__block-left" href="/">
                <img src="/images/logo.png"/>
            </a>
            <div class="header-block__block-center">
                search
            </div>
            <div class="header-block__block-right">
                <a class="header-block__login" href="/login">Вход</a>
                <a class="header-block__cart sx-inverse" href="/cart">
                    <img class="header-block__cart-img" src="/images/cart-shopping-solid.svg"/>
                    <span>0</span>
                </a>
            </div>
        </div>
    </div>
</header>
<main class="main">
    <div class="layer-center">
        <?php echo template(DIR_TEMPLATES . "/{$resp->getViewName()}", $resp->data); ?>
    </div>
</main>
<footer class="footer">
    <div class="layer-center">
        ...
    </div>
</footer>
</body>
</html>