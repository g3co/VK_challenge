<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 24.08.2017
 * Time: 18:51
 */

/** Ленивая загрузка множества инстансов БД позволяет хранить приложение с разнесёнными по
 *  серверам таблицами
 */

//Lazy load
$db_store = [];
$app['db'] = function ($db_name) use (&$db_store) {
    if(!isset($db_store[$db_name])) {
        // TODO: chose credentials depends of db name

        try {
            $db_store[$db_name] = new PDO('mysql:host=' . DB_HOST_TASKS . ';charset=UTF8;dbname=' . DB_NAME_TASKS, DB_USER_TASKS, DB_PASS_TASKS);
        }
        catch( PDOException $Exception ) {
            error_code(500);
        }
    }
    return $db_store[$db_name];
};