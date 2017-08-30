<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:46
 */

function create_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'INSERT INTO `tasks` (`task_name`, `task_descr`, `author_id`, `price`) VALUES (:task_name, :task_descr, :author_id, :price);'
    );
    $stmt->bindParam(':task_name', $data['task_name'], PDO::PARAM_STR, 512);
    $stmt->bindParam(':task_descr', $data['task_descr'], PDO::PARAM_STR);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);
    $stmt->bindParam(':price', $data['price'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function get_list_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        '(SELECT * FROM `tasks` WHERE state = ' . TASK_STATE_NEW . ' AND id > :first_task ORDER BY id DESC) UNION
         (SELECT * FROM `tasks` WHERE state = ' . TASK_STATE_NEW . ' AND id < :last_task ORDER BY id DESC LIMIT :quantity)'
    );
    $stmt->bindParam(':last_task', $data['last_task'], PDO::PARAM_INT);
    $stmt->bindParam(':first_task', $data['first_task'], PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_first_list_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE state = ' . TASK_STATE_NEW . ' ORDER BY id DESC LIMIT :quantity'
    );

    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE id = :task_id'
    );
    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function hold_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'UPDATE `tasks` SET `dev_id` = IF(state = ' . TASK_STATE_NEW . ', :dev_id, NULL), `state` = IF(state = ' . TASK_STATE_NEW . ', ' . TASK_STATE_HOLD . ', state) WHERE `id` = :task_id'
    );
    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);
    $stmt->bindParam(':dev_id', $data['dev_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->rowCount();
}

function release_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'UPDATE `tasks` SET `dev_id` = NULL, `state` = ' . TASK_STATE_NEW . ' WHERE `id` = :task_id'
    );
    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->rowCount();
}

function close_task_model($app, $data)
{
    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'UPDATE `tasks` SET `state` = ' . TASK_STATE_CLOSED . ' WHERE `id` = :task_id AND `author_id` = :author_id AND `state` = ' . TASK_STATE_HOLD
    );
    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    return $stmt->rowCount();
}