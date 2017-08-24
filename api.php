<?php
session_start();
const MAIN_PATH = __DIR__;
include_once (MAIN_PATH . '/definitions.php');
include_once (MAIN_PATH . '/core/bootstrap.php');

$route = router();

if (!$route) {
    error_code(404);
}

if($route['access_without_auth'] === false) {
    //TODO: Check auth
}

header('Content-Type: application/json');

$application_file = MAIN_PATH . '/application/' . $route['file'];

if (!file_exists($application_file)) {
    error_code(403);
}

include_once($application_file);

if (!function_exists($route['function'])) {
    error_code(403);
}

echo json_encode(call_user_func($route['function']));