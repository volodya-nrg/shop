<?php

require_once "../app/init.php";

$_SERVER[EnumField::ModeIsTest->value] = false;

$aURLData = parse_url($_SERVER['REQUEST_URI']);
$aURLPath = explode("/", $aURLData["path"]);
array_shift($aURLPath);
$class = "Controller" . ucfirst(!empty($aURLPath[0]) ? trim(str_replace("-", "", $aURLPath[0])) : "main");
$method = !empty($aURLPath[1]) ? trim(str_replace("-", "_", $aURLPath[1])) : "index";
$aArgs = count($aURLPath) > 2 ? array_slice($aURLPath, 2) : [];

try {
    if (!class_exists($class)) {
        throw new Exception(EnumErr::NotFoundClass->value);
    }

    if ($class === "ControllerItem") {
        if ($method === "index" || count($aArgs)) {
            throw new Exception(EnumErr::NotFoundMethod->value);
        }
        $aArgs = [$method];
        $method = "index";
    } elseif (!method_exists($class, $method)) {
        throw new Exception(EnumErr::NotFoundMethod->value);
    }

    $oPage = new $class();

    if (!is_callable([$oPage, $method])) { // проверим можно ли вызывать (public, protected). Если private, то не получится вызвать.
        throw new Exception(EnumErr::MethodNotAllowed->value);
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
    error_log(sprintf(EnumErr::InWhenTpl->value, "index.php", "call class-method", $e->getMessage()));
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

    <link type="image/x-icon" rel="icon" href="/images/internal/favicon.png">

    <!-- пока прикроем из-за дебага -->
    <!-- <link rel="stylesheet" href="/css.php"/> -->
    <!-- <script src="/js.php"></script> -->

    <link rel="stylesheet" href="/css/base.css"/>
    <link rel="stylesheet" href="/css/form.css"/>
    <link rel="stylesheet" href="/css/notice.css"/>
    <link rel="stylesheet" href="/css/module-item.css"/>
    <link rel="stylesheet" href="/css/module-cart-item.css"/>
    <link rel="stylesheet" href="/css/module-counter.css"/>
    <link rel="stylesheet" href="/css/module-breakcrumbs.css"/>
    <link rel="stylesheet" href="/css/module-catalog-menu.css"/>
    <link rel="stylesheet" href="/css/module-paginator.css"/>
    <link rel="stylesheet" href="/css/page-cat.css"/>
    <link rel="stylesheet" href="/css/page-item.css"/>
    <link rel="stylesheet" href="/css/page-cart.css"/>
    <link rel="stylesheet" href="/css/page-order.css"/>
    <link rel="stylesheet" href="/css/page-order-ok.css"/>

    <script src="/js/public.js"></script>
</head>
<body id="body">
<header class="header">
    <div class="layer-center">
        <div class="header-block">
            <a class="header-block_block-left" href="/">
                <img src="/images/internal/logo.png"/>
            </a>
            <div class="header-block_block-center">
                search
            </div>
            <div class="header-block_block-right">
                <?php if (isset($_SESSION[EnumField::Admin->value])): ?>
                    <a class="header-block_adm" href="/adm">Адм.</a>
                <?php endif; ?>

                <?php if (isset($_SESSION[EnumField::Profile->value])): ?>
                    <a class="header-block_logout" href="/logout">Выход</a>
                <?php else: ?>
                    <a class="header-block_login" href="/login">Вход</a>
                <?php endif; ?>

                <a class="header-block_cart sx-inverse" href="/cart">
                    <img class="header-block_cart-img" src="/images/internal/cart-shopping-solid.svg"/>
                    <span>0</span>
                </a>
            </div>
        </div>
    </div>
</header>
<main class="main">
    <div class="layer-center">
        <?php echo template($resp->getView(), $resp->data); ?>
    </div>
</main>
<footer class="footer">
    <div class="layer-center">
        ...
    </div>
</footer>
</body>
</html>