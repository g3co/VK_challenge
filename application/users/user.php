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
function get_users_user_action()
{
    if (!check_request_data(['user_id'])) {
        return ['error' => true, 'message' => 'user_id is not defined'];
    }

    return model_call('get_user', 'user', ['id' => $_REQUEST['user_id']]);
}

function create_users_user_action()
{
    if (!check_request_data(['nick_name', 'email', 'password', 'type'])) {
        return ['error' => true, 'message' => 'Incomplete user data'];
    }

    $request_type = (int)$_REQUEST['type'];
    if ($request_type !== USER_TYPE_CUSTOMER && $request_type !== USER_TYPE_DEVELOPER) {
        return ['error' => true, 'message' => 'Incorrect user type'];
    }

    // TODO: validate email, pass and nick

    if ($user_id = model_call('create', 'user', ['nick_name' => $_REQUEST['nick_name'],'email' => $_REQUEST['email'],
            'password' => $_REQUEST['password'],'type' => $_REQUEST['type']])) {

        $account = 0;
        // Demo wallet for customers user
        if ($_REQUEST['type'] == USER_TYPE_CUSTOMER) {
            $account = 10000;
        }

        model_call('initial', 'accounts', ['user_id' => $user_id,'account' => $account]);
        return true;
    } else {
        return ['error' => true, 'message' => 'Duplicated user data'];
    }
}