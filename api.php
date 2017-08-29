<?php
/**
 * @var array $app
 */
session_start();

const MAIN_PATH = __DIR__;
include_once (MAIN_PATH . '/definitions.php');
include_once (MAIN_PATH . '/core/bootstrap.php');

$route = app_router();

header('Content-Type: application/json');

if (!$route) {
    error_code(404);
}

if($route['access_without_auth'] === false) {
    if (!isset($_SESSION['user'])) {
        error_code(401);
    }
}


app_file_load($route['file']);

echo json_encode(app_call_func($route['function']));