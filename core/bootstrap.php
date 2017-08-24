<?php

include_once(MAIN_PATH . '/core/redis.php');
include_once(MAIN_PATH . '/core/router.php');
include_once(MAIN_PATH . '/core/database.php');

function error_code($code = null)
{
    switch ($code) {
        case 403:
            header("HTTP/1.0 403 Forbidden");
            break;
        case 404:
            header("HTTP/1.0 404 Not Found");
            break;
        default:
            header("HTTP/1.0 400 Bad Request");
            break;
    }

    die;
}