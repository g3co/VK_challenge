<?php

include_once(MAIN_PATH . '/bootstrap/definitions.php');

function include_files($path)
{
    $files = scandir($path);

    unset($files[0]);
    unset($files[1]);

    foreach ($files as $file) {
        $file_path = $path . '/' . $file;
        if (is_dir($file_path)) {
            include_files($file_path);
        } else {
            include_once($file_path);
            //print_r($file_path . "\n");
        }
    }
}

if (DEV_MODE) {
    include_files(MAIN_PATH . '/functions');
} else {
    include_once(MAIN_PATH . '/functions/redis.php');
}

