<?php

$app = [];

include_once(MAIN_PATH . '/core/redis.php');
include_once(MAIN_PATH . '/core/router.php');
include_once(MAIN_PATH . '/core/database.php');


function error_code($code = null)
{
    if (DEV_MODE) {
        print_r(debug_backtrace());
    } else {
        switch ($code) {
            case 403:
                header("HTTP/1.0 403 Forbidden");
                break;
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
            case 500:
                header("HTTP/1.0 500 Internal Server Error");
                break;
            default:
                header("HTTP/1.0 400 Bad Request");
                break;
        }
    }

    die;
}

function file_load($file_alias, $dir)
{
    $file_path = str_replace('_', '/', $file_alias);
    $application_file = MAIN_PATH . '/' . $dir . '/' . $file_path . '.php';
    if (file_exists($application_file)) {
        include_once($application_file);
    } else {
        error_code(500);
    }
}

function app_file_load($file_alias)
{
    file_load($file_alias, 'application');
}

function model_call($method, $file_alias, $data)
{
    global $app;
    file_load($file_alias, 'models');

    if (!is_array($data)) {
        $data = [$data];
    }

    $func_name = $method . '_' . $file_alias . '_model';

    if (!function_exists($func_name)) {
        error_code(500);
    }

    return call_user_func($func_name, $app, $data);
}

function app_call_func($func_name, $data = [])
{
    if (!function_exists($func_name)) {
        error_code(500);
    }

    return call_user_func($func_name, $data);
}

function check_request_data(array $data)
{
    foreach($data as $data_item){
        if(!isset($_REQUEST[$data_item])) {
            return false;
        }
    }

    return true;
}