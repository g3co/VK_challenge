<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:46
 */

const REDIS_CACHE_PREFIX_TASKS = 'cache:tasks:';
const REDIS_CACHE_PREFIX_TASK = 'cache:task:';

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
        clear_tasks_cache($app);
        return true;
    } else {
        return false;
    }
}

function get_list_task_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_TASKS . 'paging:' . $data['last_task'] . ':' . $data['quantity'];

    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE state != ' . TASK_STATE_CLOSED . ' AND id < :last_task ORDER BY id DESC LIMIT :quantity'
    );

    $stmt->bindParam(':last_task', $data['last_task'], PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result), REDIS_CACHE_TTL);
    return $result;
}

function get_new_list_task_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_TASKS . 'new:' . $data['first_task'] . ':' . $data['quantity'];

    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE state != ' . TASK_STATE_CLOSED . ' AND id > :first_task ORDER BY id DESC LIMIT :quantity'
    );

    $stmt->bindParam(':first_task', $data['first_task'], PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result), REDIS_CACHE_TTL);
    return $result;
}

function get_by_user_task_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_TASKS . 'user_tasks:' . $_SESSION['user']['id'];

    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE author_id = :user_id OR dev_id = :user_id ORDER BY id DESC'
    );

    $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result), REDIS_CACHE_TTL);
    return $result;
}

function get_first_list_task_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_TASKS . 'first:' . $data['quantity'];
    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE state != ' . TASK_STATE_CLOSED . ' ORDER BY id DESC LIMIT :quantity'
    );

    $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result));
    return $result;
}

function get_task_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_TASK . $data['task_id'];

    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $task_db */
    $task_db = $app['db'](DB_INSTANCE_TASK);

    $stmt = $task_db->prepare(
        'SELECT * FROM `tasks` WHERE id = :task_id'
    );

    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result), REDIS_CACHE_ITEM_TTL);
    return $result;
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

    clear_cache($app, $data['task_id']);
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

    clear_cache($app, $data['task_id']);

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

    clear_cache($app, $data['task_id']);

    return $stmt->rowCount();
}

function clear_tasks_cache($app)
{
    /** @var Redis $redis */
    $redis = $app['redis']();
    $redis->delete($redis->keys(REDIS_CACHE_PREFIX_TASKS . '*'));
}

function clear_task_cache($app, $task_id)
{
    /** @var Redis $redis */
    $redis = $app['redis']();
    $redis->delete(REDIS_CACHE_PREFIX_TASK . $task_id);
}

function clear_cache($app, $task_id)
{
    clear_tasks_cache($app);
    clear_task_cache($app, $task_id);
}