<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 25.08.2017
 * Time: 15:31
 */

/**
 * @param array $app
 * @return bool
 */
function user_user_get_action($app)
{
    if (!check_request_data(['user_id'])) {
        return ['error'=> true, 'message' => 'user_id is not defined'];
    }

    return model_call('get_user', 'user', ['id' => $_REQUEST['user_id']]);
}