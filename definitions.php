<?php

/** APP */
const DEV_MODE = true;

/** REDIS */
const REDIS_SERVER = '127.0.0.1';
const REDIS_PORT = '6379';
const REDIS_CONNECTION_TIMEOUT = '3';
const REDIS_LOCK_WAITING_TIME_US =  '1000';
const REDIS_RELEASE_LOCK_PROBABILITY =  '10';
const REDIS_LOCK_TTL = 3;

/** DATABASE - tasks*/
const DB_HOST_TASKS = 'localhost';
const DB_USER_TASKS = 'root';
const DB_PASS_TASKS = 'X21N795M6brEZAlp';
const DB_PORT_TASKS = '3306';
const DB_NAME_TASKS = 'vk_challenge';

const DB_INSTANCE_USER = 'user';
const DB_INSTANCE_TASK = 'task';
const DB_INSTANCE_ACCOUNTS = 'accounts';

/** USER ENTITY */
const USER_TYPE_CUSTOMER = 1;
const USER_TYPE_DEVELOPER = 2;

/** TASK ENTITY */
const TASK_MAX_QUANTITY_FOR_LOAD = 30;

const TASK_STATE_NEW = 0;
const TASK_STATE_HOLD = 1;
const TASK_STATE_CLOSED = 2;

/** SYSTEM DATA */
const SYSTEM_FEE = 5;