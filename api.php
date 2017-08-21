<?php

session_start();
const MAIN_PATH = __DIR__;
include_once (MAIN_PATH . '/bootstrap/bootstrap.php');

lock_resource('xxx');
echo date('H:i:s') . "\n";
sleep(3);
echo date('H:i:s') . "\n";
release_resource('xxx');