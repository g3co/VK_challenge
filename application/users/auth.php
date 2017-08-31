e<?php

function login_users_auth_action()
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
        session_write_close();
    }

    setcookie('user_id', $user['id']);
    setcookie('user_type', $user['type']);

    return [
        'user_id' => (int)$user['id'],
        'user_type' => (int)$user['type']
    ];
}

function logout_users_auth_action()
{
    unset($_SESSION['user']);
    session_write_close();

    setcookie('user_id', '', time() - 100);

    return true;
}