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
function app_router()
{
    $routes = [
        'POST@user/auth' => ['user_auth', 'authorize', true],
        'POST@user/user' => ['user_user', 'create', true],
        'GET@user/user' => ['user_user', 'get', true],
    ];

    $allowed_request_methods = [
        'GET' => 'get',
        'POST' => 'post',
        'DELETE' => 'delete',
        'PUT' => 'put',
    ];


    if (isset($_GET['method'])) {
        if (isset($allowed_request_methods[$_SERVER['REQUEST_METHOD']]) && isset($routes[$_SERVER['REQUEST_METHOD'] . '@' . $_GET['method']])) {
            $route = $routes[$_SERVER['REQUEST_METHOD'] . '@' . $_GET['method']];
            return [
                'function' => $route[1] . '_' . $route[0] . '_action',
                'file' => $route[0],
                'access_without_auth' => $route[2]
            ];
        }
    }

    return false;
}