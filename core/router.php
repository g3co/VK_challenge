<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 24.08.2017
 * Time: 18:57
 */

/**
 * @return array|bool
 */
function router()
{
    $routes = [
        'user/auth' => ['user_auth', 'GET|POST', true],
    ];

    $allowed_request_methods = [
        'GET' => 'get',
        'POST' => 'post',
        'DELETE' => 'delete',
        'PUT' => 'put',
    ];


    if (isset($_GET['method'])) {
        if (isset($allowed_request_methods[$_SERVER['REQUEST_METHOD']]) && isset($routes[$_GET['method']])) {
            if(stripos($routes[$_GET['method']][1], $_SERVER['REQUEST_METHOD']) !== false) {
                return [
                    'function' => $routes[$_GET['method']][0] . '_' . $allowed_request_methods[$_SERVER['REQUEST_METHOD']] . '_action',
                    'file' => str_replace('_', '/', $routes[$_GET['method']][0]) . '.php',
                    'access_without_auth' => $routes[$_GET['method']][2]
                ];
            }
        }
    }

    return false;
}