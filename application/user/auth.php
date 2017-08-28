<?php

function authorize_user_auth_action()
{
    if (!check_request_data(['email', 'password'])) {
        return ['error' => true, 'message' => 'Email or password had not been sent'];
    }

    if (isset($_SESSION['user']['id'])) {
        $user = $_SESSION['user'];
    } else {
        $user = model_call('check_auth', 'user', [
            'email' => $_REQUEST['email'],
            'password' => $_REQUEST['password'],
        ]);

        if ($user === false) {
            return ['error' => true, 'message' => 'Invalid credentials'];
        }

        $_SESSION['user'] = $user;
    }

    setcookie('user_id', $user['id']);

    return ['user_id' => (int)$user['id']];
}