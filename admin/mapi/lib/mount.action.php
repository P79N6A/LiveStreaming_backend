<?php

/**
 * 坐骑
 */
class mountModule extends baseModule
{
    /**
     * 用户充值界面
     */
    public function mount_rule_list()
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
            $root['diamonds'] = $user_redis->getOne_db($user_id, 'diamonds');
            $root['coin'] = $user_redis->getOne_db($user_id, 'coin');
            $mount_rule_list = load_auto_cache("mount_rule_list");

            $my_mounts = load_auto_cache("user_mounts", array('user_id' => $user_id, 'no_expired' => true));
            $my_mounts = array_combine(array_column($my_mounts, 'mount_id'), $my_mounts);
            foreach ($mount_rule_list as &$value) {
                $value['rules'] = array_map(function ($v) use (&$my_mounts) {
                    $time = time();
                    if (array_key_exists($v['mount_id'], $my_mounts) && $my_mounts[$v['mount_id']]['end_time'] > $time) {
                        $v['end_time_desc'] = date('Y年m月d日', $my_mounts[$v['mount_id']]['end_time'] + ($v['day_length'] * 24 * 3600));
                    } else {
                        $v['end_time_desc'] = date('Y年m月d日', $time + ($v['day_length'] * 24 * 3600));
                    }
                    return $v;
                }, $value['rules']);
            }
            $root['mount_rule_list'] = $mount_rule_list;
        }
        ajax_return($root);
    }

    /**
     * 用户充值支付
     */
    public function pay()
    {

        $root = array();
        $root['status'] = 1;
        //$GLOBALS['user_info']['id'] = 1;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $mount_id = intval($_REQUEST['mount_id']); //守护的类型
            $rule_id = intval($_REQUEST['rule_id']); //支付项目id
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $mount_rule = load_auto_cache("mount_rule_list", array('mount_id' => $mount_id, 'rule_id' => $rule_id));
            if (empty($mount_rule)) {
                ajax_return(array('error' => '支付id无效', 'status' => 0));
            }
            if (empty($mount_rule[0]['rules'])) {
                ajax_return(array('error' => '项目id无效', 'status' => 0));
            }
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $mount = $mount_rule[0];
            $rule = $mount_rule[0]['rules'][0];
            $total_ticket = $rule['ticket'];
            try {
                $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds - " . $rule['diamonds'] . ",use_diamonds = use_diamonds + " . $rule['diamonds'] . ", score = score + " . $rule['score'] . " WHERE id = '" . $user_id . "' AND diamonds >= " . $rule['diamonds'];
                $GLOBALS['db']->query($sql);
                if ($GLOBALS['db']->affected_rows()) {
                    user_mount_syn($user_id, $rule);
                    $data = array();
                    $data['diamonds'] = $rule['diamonds'];
                    $data['score'] = $rule['score'];
                    $data['video_id'] = 0;
                    account_log_com($data, $user_id, '购买坐骑[' . $mount['name'] . ']x' . $rule['day_length'] . '天', array('type' => 30));
                    //更新用户等级
                    $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'nick_name', 'login_type', 'ticket', 'refund_ticket', 'anchor_level'));
                    user_leverl_syn($user_info);
                } else {
                    $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                    $root['status'] = 0;
                }
            } catch (Exception $e) {
                //异常回滚
                $root['error'] = $e->getMessage();
                $root['status'] = 0;

            }
        }
        //暮橙定制: IM推送用户等级和经验信息
        push_level_info($user_id);
        ajax_return($root);
    }

    /**
     * [my_mounts 我的坐骑]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-22T15:39:39+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function my_mounts()
    {
        $root = array();
        $root['status'] = 1;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $mount_info = $user_redis->getRow_db($user_id, array('default_mount'));
            $mount_id = $mount_info['default_mount']; //用户ID
            $root['mounts_list'] = load_auto_cache('user_mounts', array('user_id' => $user_id));
            foreach ($root['mounts_list'] as &$value) {
                $value['is_expired'] = (int) ($value['end_time'] < time());
                $value['is_use'] = (int) ($value['mount_id'] == $mount_id);
                $value['end_time_desc'] = date('Y年m月d日', $value['end_time']);
            }
            $root['status'] = 1;
        }
        ajax_return($root);
    }

    public function use_mount()
    {
        $root = array();
        $root['status'] = 1;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $mount_id = intval($_REQUEST['mount_id']); //用户ID
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $mount_info = $user_redis->getRow_db($user_id, array('default_mount'));
            if ($mount_info['default_mount'] == $mount_id) {
                $mount_id = 0;
            } else {
                $user_mount = load_auto_cache('user_mounts', array('user_id' => $user_id, 'mount_id' => $mount_id));
                if (empty($user_mount)) {
                    ajax_return(array('error' => '您还没有购买该坐骑哦！', 'status' => 0));
                }
            }
            $GLOBALS['db']->autoExecute(DB_PREFIX . 'user', array('default_mount' => $mount_id), 'UPDATE', "id = {$user_id}");
            if ($GLOBALS['db']->affected_rows()) {

                $user_redis->update_db($user_id, array('default_mount' => $mount_id));
                $root['status'] = 1;
            } else {
                $root['status'] = 0;
                $root['error'] = "设置失败";
            }
        }
        ajax_return($root);
    }
}
