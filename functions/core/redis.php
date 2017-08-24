<?php


$redis = new Redis();
$redis->connect(REDIS_SERVER, REDIS_PORT, REDIS_CONNECTION_TIMEOUT);

function lock_resource($res) {
    global $redis;


    // На случай если процесс завалится не успев выставить TTl ключу
    // Один из REDIS_RELEASE_LOCK_PROBABILITY запросов на блокировку выставит REDIS_LOCK_TTL
    if(!rand(0,REDIS_RELEASE_LOCK_PROBABILITY) && $redis->ttl($res) < 0){
        $redis->expire($res, REDIS_LOCK_TTL);
    }

    while(!($redis->setnx($res, 1))) {
        usleep(REDIS_LOCK_WAITING_TIME_US);
    }

    $redis->expire($res, REDIS_LOCK_TTL);
}

function release_resource($res) {
    global $redis;
    $redis->del($res);
}

