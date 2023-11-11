<?php declare(strict_types=1);
header('Content-type: text/css');

function compressCSS($buffer)
{
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   ', '    ', '     '), '', $buffer);
    return $buffer;
}

ob_start("compressCSS");

include("./css/styles.css");
include("./css/form.css");
include("./css/notice.css");
include("./css/module-item.css");
include("./css/module-cart-item.css");
include("./css/module-counter.css");
include("./css/module-breakcrumbs.css");
include("./css/module-catalog-menu.css");
include("./css/module-paginator.css");
include("./css/page-cat.css");
include("./css/page-item.css");
include("./css/page-cart.css");
include("./css/page-order.css");
include("./css/page-order-ok.css");

ob_end_flush();