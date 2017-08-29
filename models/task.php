<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:46
 */

function create_task_model($app, $data)
{
    /** @var PDO $user_db */
    $user_db = $app['db']('user');

    $stmt = $user_db->prepare(
        'INSERT INTO `tasks` (`task_name`, `task_descr`, `author_id`) VALUES (:task_name, :task_descr, :author_id);'
    );
    $stmt->bindParam(':task_name', $data['task_name'], PDO::PARAM_STR, 512);
    $stmt->bindParam(':task_descr', $data['task_descr'], PDO::PARAM_STR);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return $stmt->errorInfo();
    }
}

