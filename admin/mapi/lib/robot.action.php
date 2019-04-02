<?php
class robotModule extends baseModule
{

    public function create_group()
    {
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $ret = $api->full_group_create(app_conf('FULL_GROUP_ID'));
        print_r($ret);exit;
    }

    public function get_group()
    {
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $group_id = $_REQUEST['id'];
        $ret = $api->group_get_group_info2(array('0' => app_conf($group_id)));
        print_r($ret);exit;
    }

    public function del_group()
    {
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $group_id = $_REQUEST['id'];
        $ret = $api->group_destroy_group(app_conf($group_id));
        print_r($ret);exit;
    }

    public function get_group_user()
    {
        $m_config = load_auto_cache("m_config");
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $group_id = $_REQUEST['id'];
        $ret = $api->group_get_group_member_info($m_config['on_line_group_id'], 0, 0);
        print_r($ret);exit;
    }

    public function get_full_group_user()
    {
        $m_config = load_auto_cache("m_config");
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $group_id = $_REQUEST['id'];
        $ret = $api->group_get_group_member_info($m_config['full_group_id'], 0, 0);
        print_r($ret);exit;
    }

    public function send_msg()
    {
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $ext = array();
        $ext['type'] = 18; //18：直播结束（全体推送的，用于更新用户列表状态）
        $ext['room_id'] = 61931; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应

        #构造高级接口所需参数
        $msg_content = array();
        //创建array 所需元素
        $msg_content_elem = array(
            'MsgType' => 'TIMCustomElem', //自定义类型
            'MsgContent' => array(
                'Data' => json_encode($ext)
            )
        );
        array_push($msg_content, $msg_content_elem);
        $ret = $api->group_send_group_msg2('', app_conf('FULL_GROUP_ID'), $msg_content);
        print_r($ret);exit;
    }

    public function add_group()
    {
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $member_id = $_REQUEST['id'];
        $ret = $api->group_add_group_member(app_conf('FULL_GROUP_ID'), $member_id, 1);
        print_r($ret);exit;
    }

    public function update_user()
    {

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService(103433);
        $user_redis->update_db(103433, array('online_time' => 0));

    }

    public function test()
    {
        $sql = "select id from " . DB_PREFIX . "user_log where user_id = 100989 and type = 5 and FROM_UNIXTIME(log_time,'%Y-%m-%d')='" . date('Y-m-d', NOW_TIME) . "'";
        print_r($sql);exit;
    }
    public function login_test()
    {
        $id = $_REQUEST['id'];
        $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=" . $id . " and is_effect = 1");
        es_session::set("user_info", $user_data);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user = $user_redis->getRow_db($id, '');
        print_r($user);exit;
    }

    public function login_w_test()
    {
        $id = $_REQUEST['id'];
        $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=" . $id, true, true);
        es_session::set("user_info", $user_data);
        print_r($user_data);exit;
    }
    //删除定时器加入直播的机器人列表

    public function del_user_robot()
    {
        $distribution_cfg = require '../public/directory_init.php';
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $video_con_keys = $user_redis->redis->keys($distribution_cfg["REDIS_PREFIX"] . 'user_robot');
        $video_con_count = $user_redis->redis->delete($video_con_keys);
        print_r($video_con_count);exit;
    }

    //同步机器人到redis
    public function robot()
    {
        $user_data = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "user where is_robot = 1");
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        foreach ($user_data as $k => $v) {
            $user_redis->insert_db($v['id'], $v);
            $ret[] = $v['id'];
        }
        print_r($ret);exit;

    }

    //同步机器人到im
    public function robot_im()
    {
        $user_data = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "user where is_robot = 1");
        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $api = createTimAPI();
        foreach ($user_data as $k => $v) {

            //添加成功，同步信息

            $ret = $api->account_import((string) $v['id'], $v['nick_name'], $v['head_image']);
            if ($ret['ErrorCode'] == 0) {
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set synchronize = 1 where id =" . $v['id']);
                $data['synchronize'] = 1;
                $user_redis->update_db($v['id'], $data);
                print_r($v['id']);
                print_r("  ");
            }
        }
        exit;
    }

    public function init()
    {
        $user_info = es_session::get("user_info");
        if ($user_info) {
            if (isset($user_info['id'])) {
                $login_root = login_prompt($user_info['id']);
                if ($login_root['first_login']) {
                    $root['first_login'] = $login_root['first_login'];
                }
                if ($login_root['new_level']) {
                    $root['new_level'] = $login_root['new_level'];
                }
            }
        }
    }
}
