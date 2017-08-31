<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 29.08.2017
 * Time: 19:26
 */

/** Создание задачи
 * @required_params [
 *  task_name string имя задачи
 *  task_descr string описание задачи
 *  task_price float стоимость
 * ]
 * @return array|bool true или массив ошибки
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

/** Получение списка заказов с пагинацией
 * @optional_params [
 *  first_task int доступная задача с максимальным ID
 *  last_task int доступная задача с минимальным ID
 *  quantity int количество элементов в выгрузке
 * ]
 *
 * Параметры first_task и last_task взаимоисключающиеся
 * first_task - это максимальный Id таски полученный в прошлых выборках,
 * позволяющий подгрузить новые задача с момента последнего запроса
 *
 * last_task - это минимальный Id из доступных с прошлых выгрузок,
 * позволяет догрузить невыгруженные ранее задачи.
 *
 * @return mixed результат или массив ошибки
 */
function get_list_tasks_task_action()
{
    if (!isset($_REQUEST['quantity']) || $_REQUEST['quantity'] > TASK_MAX_QUANTITY_FOR_LOAD) {
        $quantity = TASK_MAX_QUANTITY_FOR_LOAD;
    } else {
        $quantity = (int)$_REQUEST['quantity'];
    }

    if (!empty($_REQUEST['last_task'])) {
        $result = model_call('get_list', 'task', [
            'last_task' => (int)$_REQUEST['last_task'],
            'quantity' => $quantity,
        ]);
    } elseif (!empty($_REQUEST['first_task'])) {
        $result = model_call('get_new_list', 'task', [
            'quantity' => TASK_MAX_QUANTITY_FOR_NEWS,
            'first_task' => (int)$_REQUEST['first_task'],
        ]);
    } else {
        $result = model_call('get_first_list', 'task', [
            'quantity' => $quantity,
        ]);
    }

    if ($result !== false) {
        return $result;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}

/** Позволяет получить информацию по одной конкретной задаче.
 * @required_params [
 *  task_id int Id задачи
 * ]
 * @return mixed результат или массив ошибки
 */
function get_tasks_task_action()
{
    if (!check_request_data(['task_id'])) {
        return ['error' => true, 'message' => 'Incomplete data'];
    }

    $result = model_call('get', 'task', [
        'task_id' => (int)$_REQUEST['task_id'],
    ]);

    if ($result !== false) {
        $developer = model_call('get_user', 'user', [
            'id' => $result['dev_id'],
        ]);

        if ($developer) {
            $result['developer']['nick_name'] = $developer['nick_name'];
            $result['developer']['email'] = $developer['email'];
        }

        return $result;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}
/** Получение всех тасок юзера
 *
 * @return mixed результат или массив ошибки
 */
function get_user_tasks_task_action()
{
    $result = model_call('get_by_user', 'task', [
        'user_id' => $_SESSION['user']['id'],
    ]);

    if ($result !== false) {
        return $result;
    } else {
        return ['error' => true, 'message' => 'Error DB query'];
    }
}

/** Метод позволяющий 'взять' задачу в исполнение
 * В этом методе происходит списание/зачисление денег
 *
 * Взять можно только задачу со статусом TASK_STATE_NEW
 *
 * @return array|bool true или массив ошибки
 */
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

/** Закрытие задачи. Закрыть может только создатель задачи. Закрытая задача не отображается в списке задач.
 *
 * Взять можно только задачу со статусом TASK_STATE_NEW
 *
 * @return array|bool true или массив ошибки
 */
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