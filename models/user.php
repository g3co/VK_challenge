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