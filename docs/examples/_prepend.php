<?php
$basepath = dirname(__FILE__, 3);
include_once $basepath . '/vendor/autoload.php';




function vd(...$args)
{
    foreach ($args as $arg) {
        error_log(print_r($arg, true));
    }
}

