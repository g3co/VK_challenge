<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 30.08.2017
 * Time: 15:46
 */

const REDIS_CACHE_PREFIX_ACCOUNT = 'cache:account:';

function pay_accounts_model($app, $data)
{

    /** @var Redis $redis */
    $redis= $app['redis']();
    $redis->delete(REDIS_CACHE_PREFIX_ACCOUNT . $data['author_id']);
    $redis->delete(REDIS_CACHE_PREFIX_ACCOUNT . $data['dev_id']);

    /** @var PDO $account_db */
    $account_db = $app['db'](DB_INSTANCE_ACCOUNTS);

    $stmt = $account_db->prepare(
        'INSERT INTO `accounts` (user_id, current_account, account_change, task_id)
            (SELECT :author_id, current_account - :price, -:price, :task_id FROM `accounts` WHERE `user_id` = :author_id ORDER BY id DESC LIMIT 1)
            UNION
            (SELECT :dev_id, current_account + :reward, :reward, :task_id FROM `accounts` WHERE `user_id` = :dev_id ORDER BY id DESC LIMIT 1)
            UNION
            (SELECT 0, current_account + :price - :reward, :price - :reward, :task_id FROM `accounts` WHERE `user_id` = 0 ORDER BY id DESC LIMIT 1)'
    );
    $stmt->bindParam(':task_id', $data['task_id'], PDO::PARAM_INT);
    $stmt->bindParam(':dev_id', $data['dev_id'], PDO::PARAM_INT);
    $stmt->bindParam(':author_id', $data['author_id'], PDO::PARAM_INT);
    $stmt->bindParam(':reward', $data['reward']);
    $stmt->bindParam(':price', $data['price']);

    return $stmt->execute();
}

function initial_accounts_model($app, $data)
{
    /** @var PDO $account_db */
    $account_db = $app['db'](DB_INSTANCE_ACCOUNTS);

    $stmt = $account_db->prepare(
        'INSERT INTO `accounts` (`user_id`, `current_account`) VALUES (:user_id, :account)'
    );
    $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':account', $data['account']);

    return $stmt->execute();
}

function user_accounts_model($app, $data)
{
    /** @var Redis $redis */
    $redis= $app['redis']();

    $redis_cache_key = REDIS_CACHE_PREFIX_ACCOUNT . $data['user_id'];

    if ($cache = $redis->get($redis_cache_key)) {
        return unserialize($cache);
    }

    /** @var PDO $account_db */
    $account_db = $app['db'](DB_INSTANCE_ACCOUNTS);

    $stmt = $account_db->prepare(
        'SELECT `task_id`, `current_account`, `account_change`, `date`
        FROM `accounts` WHERE `user_id` = :user_id
        ORDER BY `id` DESC LIMIT 10'
    );
    $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $redis->set($redis_cache_key, serialize($result), REDIS_CACHE_ITEM_TTL);
    return $result;
}

