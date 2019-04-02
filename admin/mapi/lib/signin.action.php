<?php

/**
 * 坐骑
 */
class signinModule extends baseModule
{
    public function signin_list()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {

            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $sign_info = $user_redis->getRow_db($user_id, array('sign_day', 'sign_time'));

            $root['status'] = 1;
            $root['error'] = '获取成功';
            $root['list'] = load_auto_cache('signin_list');
            // 计算最大的一天
            $max_day = (int) $GLOBALS['db']->getOne('SELECT MAX(`day`) FROM `' . DB_PREFIX . 'signin` WHERE is_effect = 1');
            if (($sign_info['sign_time'] < strtotime('-1 day 00:00:00')) || ($sign_info['sign_day'] >= $max_day)) {
                $sign_info['sign_day'] = 0;
            }
            foreach ($root['list'] as &$value) {
                if ($sign_info['sign_day'] >= $value['day']) {
                    $value['is_sign'] = 1;
                } else {
                    $value['is_sign'] = 0;
                }
            }
            // 今天是否已经签到
            if ($sign_info['sign_time'] >= strtotime('00:00:00')) {
                $root['now_is_sign'] = 1;
            } else {
                $root['now_is_sign'] = 0;
            }
        }
        ajax_return($root);
    }

    public function now_is_sign()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $sign_info = $user_redis->getRow_db($user_id, array('sign_day', 'sign_time'));

            $root['error'] = '获取成功';
            if ($sign_info['sign_time'] >= strtotime('00:00:00')) {
                $root['now_is_sign'] = 1;
            } else {
                $root['now_is_sign'] = 0;
            }
        }
        ajax_return($root);
    }

    public function do_sign_in()
    {
        $root = array();
        $root['status'] = 1;
        $root['error'] = '';

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            // 互斥锁
            $root = $user_redis->asyncWork($user_id, function () use (&$user_id, &$user_redis) {
                $sign_info = $user_redis->getRow_db($user_id, array('sign_day', 'sign_time'));
                if ($sign_info['sign_time'] >= strtotime('00:00:00')) {
                    return array('error' => '今天您已经签到！', 'status' => 0);
                }
                $max_day = (int) $GLOBALS['db']->getOne('SELECT MAX(`day`) FROM `' . DB_PREFIX . 'signin` WHERE is_effect = 1');
                // 判断是否连续的签到 小于昨天的凌晨 或已经签到达到或者超过最大天数
                if (($sign_info['sign_time'] < strtotime('-1 day 00:00:00')) || ($sign_info['sign_day'] >= $max_day)) {
                    $sign_info['sign_day'] = 1;
                } else {
                    $sign_info['sign_day'] += 1;
                }

                $info = load_auto_cache('signin_day', array('day' => $sign_info['sign_day']));
                $pInTrans = $GLOBALS['db']->StartTrans();
                try
                {
                    // 奖励不为空。执行奖励相关的
                    if (!empty($info)) {
                        $row = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "prop_backpack WHERE `user_id` = {$user_id} AND `prop_id` = {$info['prop_id']} LIMIT 1");
                        if ($row > 0) {
                            $sql = 'UPDATE ' . DB_PREFIX . "prop_backpack SET `num` = num+{$info['num']} WHERE `user_id` = {$user_id} AND `prop_id` = {$info['prop_id']}";
                        } else {
                            $sql = 'INSERT INTO ' . DB_PREFIX . "prop_backpack (`user_id`, `prop_id`, `num`) VALUES ({$user_id}, {$info['prop_id']}, {$info['num']})";
                        }
                        if ($GLOBALS['db']->query($sql) === false) {
                            $GLOBALS['db']->Rollback($pInTrans);
                            return array('error' => '道具加入背包失败', 'status' => 0);
                        }
                    }
                    // 执行签到的任务
                    $sign_info['sign_time'] = time();
                    if ($GLOBALS['db']->autoExecute(DB_PREFIX . 'user', $sign_info, 'UPDATE', 'id=' . $user_id)) {
                        $GLOBALS['db']->Commit($pInTrans);
                        $user_redis->update_db($user_id, $sign_info);
                        $pInTrans = false; //防止，下面异常时，还调用：Rollback
                        return array('error' => '签到成功', 'status' => 1, 'sign_info' => $info);
                    } else {
                        $GLOBALS['db']->Rollback($pInTrans);
                        return array('error' => '签到失败', 'status' => 0);
                    }
                } catch (Exception $e) {
                    $GLOBALS['db']->Rollback($pInTrans);
                    return array('error' => $e->getMessage(), 'status' => 0);
                }
            }, function () {
                return array('error' => '请不要频繁请求', 'status' => 0);
            });
        }
        ajax_return($root);
    }
}
