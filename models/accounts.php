<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 30.08.2017
 * Time: 15:46
 */

function pay_accounts_model($app, $data)
{
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