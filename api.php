<?php
session_start();
const MAIN_PATH = __DIR__;
include_once (MAIN_PATH . '/bootstrap/bootstrap.php');

$route = router();

header('Content-Type: application/json');

if (!$route) {
    header("HTTP/1.0 404 Not Found");
    die;
}
if($route['access_without_auth'] === false) {
    //TODO: Check auth
}

echo json_encode(call_user_func($route['function']));