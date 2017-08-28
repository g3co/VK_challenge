<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 25.08.2017
 * Time: 16:53
 */

function get_user_user_model($app, $data)
{
    /** @var PDO $user_db */
    $user_db = $app['db']('user');
    $stmt = $user_db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    $stmt->execute();
    if ($output = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $output;
    } else {
        return null;
    }
}

function check_auth_user_model($app, $data)
{
    /** @var PDO $user_db */
    $user_db = $app['db']('user');
    $stmt = $user_db->prepare('SELECT * FROM users WHERE email = :email AND password = md5(md5(concat(:password, salt))) LIMIT 1');
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR, 128);
    $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR, 128);
    $stmt->execute();
    if ($output = $stmt->fetch(PDO::FETCH_ASSOC)) {
        return $output;
    } else {
        return false;
    }
}

function create_user_model($app, $data)
{
    /** @var PDO $user_db */
    $user_db = $app['db']('user');
    $stmt = $user_db->prepare(
        'INSERT INTO `users` (`nick_name`, `email`, `password`, `salt`, `type`) ' .
        'VALUES (:nick_name, :email, md5(md5(concat(:password,:salt))), :salt, :type)'
    );
    $stmt->bindParam(':nick_name', $data['nick_name'], PDO::PARAM_STR, 128);
    $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR, 128);
    $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR, 256);
    $stmt->bindParam(':salt', $data['salt'], PDO::PARAM_STR, 256);
    $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR, 256);

//    $error_info = $stmt->errorInfo();

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}