<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 25.08.2017
 * Time: 15:31
 */

/**
 * @param array $app
 * @return bool
 */
function get_user_user_action()
{
    if (!check_request_data(['user_id'])) {
        return ['error'=> true, 'message' => 'user_id is not defined'];
    }

    return model_call('get_user', 'user', ['id' => $_REQUEST['user_id']]);
}

function create_user_user_action()
{
    if (!check_request_data(['nick_name', 'email', 'password', 'type'])) {
        return ['error'=> true, 'message' => 'Incomplete user data'];
    }

    if (model_call('create', 'user', [
        'nick_name' => $_REQUEST['nick_name'],
        'email' => $_REQUEST['email'],
        'password' => $_REQUEST['password'],
        'type' => $_REQUEST['type'],
        'salt' => uniqid(mt_rand(), true),
    ])) {
        return true;
    } else {
        return ['error'=> true, 'message' => 'User had not been created'];
    }
}