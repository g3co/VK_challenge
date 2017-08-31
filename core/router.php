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
        'POST@users/login' => ['login', 'users_auth', true],
        'POST@users/logout' => ['logout', 'users_auth', false],
        'POST@users/user' => ['create', 'users_user', true],
        'GET@users/user' => ['get', 'users_user', false],
        'GET@users/user/tasks' => ['get_user', 'tasks_task', false],
        'POST@tasks/task' => ['create', 'tasks_task', false],
        'GET@tasks/tasks' => ['get_list', 'tasks_task', false],
        'GET@tasks/task' => ['get', 'tasks_task', false],
        'POST@tasks/task/hold' => ['hold', 'tasks_task', false],
        'POST@tasks/task/close' => ['close', 'tasks_task', false],
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
                'function' => $route[0] . '_' . $route[1] . '_action',
                'file' => $route[1],
                'access_without_auth' => $route[2]
            ];
        }
    }

    return false;
}