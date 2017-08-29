<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:26
 */

function create_tasks_task_action()
{
    if (!check_request_data(['task_name', 'task_descr'])) {
        return ['error'=> true, 'message' => 'Incomplete data'];
    }

    if ($_SESSION['user']['type'] != USER_TYPE_CUSTOMER) {
        return ['error'=> true, 'message' => 'Current user can\'t create task'];
    }

    if (($result = model_call('create', 'task', [
            'task_name' => $_REQUEST['task_name'],
            'task_descr' => $_REQUEST['task_descr'],
            'author_id' => $_SESSION['user']['id'],
        ])) === true) {
        return true;
    } else {
        return ['error'=> true, 'message' => $result[2]];
    }
}