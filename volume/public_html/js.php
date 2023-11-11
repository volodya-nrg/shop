<?php declare(strict_types=1);
header("Content-type: text/javascript");

function compressJS($buffer)
{
    $buffer = preg_replace("/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/", "", $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "   ", "    ", "     "), "", $buffer);
    return $buffer;
}

ob_start("compressJS");

include("./js/public.js");

ob_end_flush();