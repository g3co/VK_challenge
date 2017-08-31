<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 25.08.2017
 * Time: 15:31
 */

/**
 * Получение информации о юзере
 * @return array|mixed
 */
function get_users_user_action()
{
    $account = model_call('user', 'accounts', ['user_id' => $_SESSION['user']['id']]);
    $user_data['nick_name'] = $_SESSION['user']['nick_name'];
    $user_data['email'] = $_SESSION['user']['email'];
    $user_data['type'] = ($_SESSION['user']['type'] == USER_TYPE_CUSTOMER) ? 'Customer' : 'Developer';
    if ($account) {
        $user_data['balance'] =  $account[0]['current_account'];
        $user_data['account'] =  $account;
    }

    return $user_data;
}

/** Создание нового юзера
 * @required_params [
 *  nick_name string Имя пользователя
 *  email string email
 *  password string пароль
 *  type int тип USER_TYPE_CUSTOMER|USER_TYPE_DEVELOPER
 * ]
 * @return array|bool
 */
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