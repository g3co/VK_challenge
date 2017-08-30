<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:26
 */

function create_tasks_task_action()
{
    if (!check_request_data(['task_name', 'task_descr', 'price'])) {
        return ['error' => true, 'message' => 'Incomplete data'];
    }

    if ($_SESSION['user']['type'] != USER_TYPE_CUSTOMER) {
        return ['error' => true, 'message' => 'Current user can\'t create task'];
    }

    if (($result = model_call('create', 'task', [
            'task_name' => $_REQUEST['task_name'],
            'task_descr' => $_REQUEST['task_descr'],
            'price' => $_REQUEST['price'],
            'author_id' => $_SESSION['user']['id'],
        ])) === true
    ) {
        return true;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}

function get_list_tasks_task_action()
{
    if (!isset($_REQUEST['quantity']) || $_REQUEST['quantity'] > TASK_MAX_QUANTITY_FOR_LOAD) {
        $_REQUEST['quantity'] = TASK_MAX_QUANTITY_FOR_LOAD;
    }

    if (!isset($_REQUEST['last_task']) || !isset($_REQUEST['first_task'])) {
        $result = model_call('get_first_list', 'task', [
            'quantity' => $_REQUEST['quantity'],
        ]);
    } else {
        $result = model_call('get_list', 'task', [
            'last_task' => $_REQUEST['last_task'],
            'first_task' => $_REQUEST['first_task'],
            'quantity' => $_REQUEST['quantity'],
        ]);
    }


    if ($result !== false) {
        return $result;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}

function get_tasks_task_action()
{
    if (!check_request_data(['task_id'])) {
        return ['error' => true, 'message' => 'Incomplete data'];
    }


    $result = model_call('get', 'task', [
        'task_id' => $_REQUEST['task_id'],
    ]);

    if ($result !== false) {
        return $result;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}

function hold_tasks_task_action()
{
    if (!check_request_data(['task_id'])) {
        return ['error' => true, 'message' => 'Incomplete data'];
    }

    if ($_SESSION['user']['type'] != USER_TYPE_DEVELOPER) {
        return ['error' => true, 'message' => 'Current user can\'t hold task'];
    }

    $task_id = (int)$_REQUEST['task_id'];

    if (!$task_id) {
        return ['error' => true, 'message' => 'Incorrect task_id'];
    }

    lock_resource('lock:hold_task:' . $task_id);

    if ($task = model_call('get', 'task', ['task_id' => $task_id])) {
        if ((int)$task['state'] === TASK_STATE_NEW) {
            if (model_call('hold', 'task', ['task_id' => $task_id,'dev_id' => $_SESSION['user']['id']])) {
                $user_reward = $task['price'] - $task['price'] * SYSTEM_FEE / 100;
                if (model_call('pay', 'accounts', ['task_id' => $task_id, 'dev_id' => $_SESSION['user']['id'],
                    'author_id' => $task['author_id'], 'reward' => $user_reward, 'price' => $task['price']])) {
                    release_resource('lock:hold_task:' . $task_id);
                    return true;
                } else {
                    model_call('release', 'task', ['task_id' => $task_id]);
                }
            }
        }
    }


    release_resource('lock:hold_task:' . $task_id);
    return ['error' => true, 'message' => 'Task can\'t be hold'];
}

function close_tasks_task_action()
{
    if (!check_request_data(['task_id'])) {
        return ['error' => true, 'message' => 'Incomplete data'];
    }

    if ($_SESSION['user']['type'] != USER_TYPE_CUSTOMER) {
        return ['error' => true, 'message' => 'Current user can\'t hold task'];
    }

    $task_id = (int)$_REQUEST['task_id'];

    if (!$task_id) {
        return ['error' => true, 'message' => 'Incorrect task_id'];
    }

    if (model_call('close', 'task', ['task_id' => $task_id, 'author_id' => $_SESSION['user']['id']])) {
        return true;
    } else {
        return ['error' => true, 'message' => 'Task had not been closed'];
    }
}