<?php
//Lazy load
$redis_store = null;
$app['redis'] = function () use (&$redis_store) {
    if(is_null($redis_store)) {
        $redis_store = new Redis();

        if (!$redis_store->connect(REDIS_SERVER, REDIS_PORT, REDIS_CONNECTION_TIMEOUT)) {
            error_code(500);
        }
    }
    return $redis_store;
};

/** Блокировка ресурса
 * @param $res string уникальное название ресурса
 */
function lock_resource($res) {
    /** @var Redis $redis */
    global $app;
    $redis = $app['redis']();

    // На случай если процесс завалится не успев выставить TTl ключу
    // Один из REDIS_RELEASE_LOCK_PROBABILITY запросов на блокировку выставит REDIS_LOCK_TTL
    if(!rand(0,REDIS_RELEASE_LOCK_PROBABILITY) && $redis->ttl($res) < 0){
        $redis->expire($res, REDIS_LOCK_TTL);
    }

    while(!($redis->setnx($res, 1)));

    $redis->expire($res, REDIS_LOCK_TTL);
}

/** Разблокировка
 * @param $res string уникальное название ресурса
 */
function release_resource($res) {
    /** @var Redis $redis */
    global $app;
    $redis = $app['redis']();
    $redis->del($res);
}

