<?php

/**
 * 超级管理
 */
class adminModule extends baseModule
{

    private $user_id;

    public function __construct()
    {
        parent::__construct();
        if (!$GLOBALS['user_info']) {
            return ajax_return(array('status' => 0, 'error' => '用户未登陆,请先登陆.', 'user_login_status' => 0));
        }
        $this->user_id = intval($GLOBALS['user_info']['id']); //用户ID

        $is_admin = $GLOBALS['db']->getOne('SELECT is_admin FROM `' . DB_PREFIX . 'user` WHERE `id`=' . $this->user_id);
        if ($is_admin == 0) {
            return ajax_return(array('status' => 0, 'error' => '您不是管理，没有权限'));
        }
    }

    /**
     * [shut_up 禁言]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-29T10:17:35+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function shut_up()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //
            $to_user_id = strim($_REQUEST['to_user_id']); //被禁言的用户id
            $group_id = strim($_REQUEST['group_id']); //群组ID
            $second = intval($_REQUEST['second']); //禁言时间，单位为秒; 为0时表示取消禁言
            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user where id = " . $to_user_id, true, true);
            if (intval($is_nospeaking) == 1) {
                $root['error'] = "该用户已被im全局禁言.";
                ajax_return($root);
            }
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db_ByGroupId($group_id, array('id', 'user_id'));

            $podcast_id = intval($video['user_id']);
            $room_id = intval($video['id']);

            //查看自己
            if ($to_user_id == $user_id) {
                ajax_return(array('status' => 0, 'error' => '不能自己给自己禁言'));
            }

            $sql = "select is_robot,nick_name from " . DB_PREFIX . "user where id = '" . $to_user_id . "'";
            $user = $GLOBALS['db']->getRow($sql, true, true);
            $nick_name = $user['nick_name'];

            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
            $api = createTimAPI();
            //设置：禁言(second>0)，取消禁言(second = 0)
            if ($user['is_robot'] != 1) {
                $ret = $api->group_forbid_send_msg($group_id, (string) $to_user_id, $second);
            }
            if ($ret['ActionStatus'] == 'OK' || $user['is_robot'] == 1) {

                //$ret = $api->get_group_shutted_uin($group_id);

                if ($second > 0) {
                    /*
                    $forbid_send_msg = array();
                    $forbid_send_msg['group_id'] = $group_id;
                    $forbid_send_msg['user_id'] = $to_user_id;
                    $forbid_send_msg['shut_up_time'] = NOW_TIME + $second;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."video_forbid_send_msg", $forbid_send_msg,"INSERT");
                     */
                    //禁言到期时间
                    if ($second > 86400) {
                        // 大于一天则永禁言
                        $msg = ($nick_name) . " 被永久禁言"; //.print_r($ret,1).';user_id:'.$user_id.";second:".$second.";group_id:".$group_id;
                        $second = 99999999;
                    } else {
                        $msg = ($nick_name) . " 被禁言" . get_live_time_len2($second);
                    }
                    $root['is_forbid'] = 1;

                    $shutup_time = NOW_TIME + $second;
                    $video_redis->set_forbid_msg($group_id, $to_user_id, $shutup_time);

                } else {
                    $msg = ($nick_name) . " 取消禁言";
                    $video_redis->unset_forbid_msg($group_id, $to_user_id);
                    $root['is_forbid'] = 0;
                    //$sql = "delete from ".DB_PREFIX."video_forbid_send_msg where group_id='".$group_id."' and user_id = '".$to_user_id."'";
                    //$GLOBALS['db']->query($sql);
                }

                $root['status'] = 1;
            } else {
                $root['status'] = 0;
                $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
            }

            if ($root['status'] == 1) {
                if (!$api) {
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();
                }

                //群播一个：禁言通知
                $ext = array();
                $ext['type'] = 4; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['fonts_color'] = ''; //字体颜色
                $ext['desc'] = $msg; //禁言通知消息;
                $ext['desc2'] = $msg; //禁言通知消息;

                //消息发送者
                $sender = array();
                $sender['user_id'] = $GLOBALS['user_info']['id']; //发送人昵称
                $sender['nick_name'] = ($GLOBALS['user_info']['nick_name']); //发送人昵称
                $sender['head_image'] = $GLOBALS['user_info']['head_image']; //发送人头像
                $sender['user_level'] = $GLOBALS['user_info']['user_level']; //用户等级

                $ext['sender'] = $sender;

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem', //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => ''
                        //    'Ext' => $ext,
                        //    'Sound' => '',
                    )
                );
                //将创建的元素$msg_content_elem, 加入array $msg_content
                array_push($msg_content, $msg_content_elem);

                $ret = $api->group_send_group_msg2($GLOBALS['user_info']['id'], $group_id, $msg_content);
            }

        }
        ajax_return($root);
    }

    /**
     * [get_out 踢人]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-29T10:32:56+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function get_out()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $video_id = intval($_REQUEST['room_id']);
            $user_ids = strim($_REQUEST['user_ids']); //字符串类型的私聊好友id 23,123,3455 以英文逗号分割的字符串 只有私聊时才需要上传这个参数
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($video_id);
            // $sql = "SELECT id,user_id from " . DB_PREFIX . "video WHERE live_in = 1 and id =" . $video_id;
            // $video = $GLOBALS['db']->getRow($sql);
            if (!empty($video)) {
                //将选中的：私聊 数据添加到数据库中
                $user_ids = explode(',', $user_ids);
                if (count($user_ids) > 0) {
                    $ext = array();
                    $ext['type'] = 17;
                    $ext['room_id'] = $video_id;
                    $ext['desc'] = '您被踢出房间';
                    #构造高级接口所需参数
                    $msg_content = array();
                    //创建array 所需元素
                    $msg_content_elem = array(
                        'MsgType' => 'TIMCustomElem', //自定义类型
                        'MsgContent' => array(
                            'Data' => json_encode($ext),
                            'Desc' => ''
                            //    'Ext' => $ext,
                            //    'Sound' => '',
                        )
                    );
                    //将创建的元素$msg_content_elem, 加入array $msg_content
                    array_push($msg_content, $msg_content_elem);
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();

                    foreach ($user_ids as $k => $v) {
                        if (($v == $video['user_id']) || ($v == $user_id)) {
                            // ajax_return(array('status' => 0, 'error' => '主播不能被踢'));
                            continue;
                        }
                        $ret = $api->openim_send_msg2((string) $user_id, $v, $msg_content);

                        /*
                        $video_private =array();
                        $video_private['ActionStatus'] = $ret['ActionStatus'];
                        $video_private['ErrorCode'] = $ret['ErrorCode'];
                        $video_private['ErrorInfo'] = $ret['ErrorInfo'];
                        $video_private['status'] = 0;
                        $GLOBALS['db']->autoExecute(DB_PREFIX."video_private", $video_private,'UPDATE'," user_id = ".$v. " and video_id =".$video_id);
                         */

                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
                        $video_private_redis = new VideoPrivateRedisService();
                        $video_private_redis->drop_user($video_id, $v);

                        $root['data'][] = $ret;
                    }
                }
            } else {
                $root['status'] = 0;
                $root['error'] = '踢人失败！';
            }
        }
        ajax_return($root);
    }

    /**
     * [close_video 关闭直播]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-29T10:51:14+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function close_video()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $room_id = trim($_REQUEST['room_id']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($room_id, array('id', 'user_id'));

            if (!empty($video)) {
                $m_config = load_auto_cache("m_config");
                $system_user_id = $m_config['tim_identifier']; //系统消息
                $podcast_id = $video['user_id'];
                if ($video['user_id'] == $user_id) {
                    ajax_return(array('status' => 0, 'error' => '不能关闭自己的直播'));
                }
                $ext = array();
                $ext['type'] = 17;
                $ext['desc'] = '违规直播，立即关闭直播';
                $ext['room_id'] = $room_id;
                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem', //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => ''
                        //  'Ext' => $ext,
                        //  'Sound' => '',
                    )
                );
                //将创建的元素$msg_content_elem, 加入array $msg_content
                array_push($msg_content, $msg_content_elem);
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->openim_send_msg2($system_user_id, $podcast_id, $msg_content);
                //结束直播
                // $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,video_vid,video_type from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $podcast_id;
                // $video = $GLOBALS['db']->getRow($sql, true, true);
                //同时关闭子房间
                if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                    $child_id = $GLOBALS['db']->getAll("SELECT child_id FROM " . DB_PREFIX . "child_room WHERE parent_id =" . $room_id);
                }
                if ($ret['ActionStatus'] == 'OK') {
                    $result = admin_do_end_video($video, $video['video_vid'], 0, $video['cate_id']);
                    if (!empty($child_id)) {
                        foreach ($child_id as $item) {
                            $child_ids[] = $item['child_id'];
                        }
                        $child_ids = implode(',', $child_ids);
                        $child_video = $GLOBALS['db']->getAll("SELECT id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,video_vid,video_type FROM " . DB_PREFIX . "video WHERE id in (" . $child_ids . ")");
                        if (!empty($child_video)) {
                            foreach ($child_video as $value) {
                                $child_res = admin_do_end_video($value, $value['video_vid'], 0, $value['cate_id']);
                            }
                        }
                    }
                    $room_id = $video['id'];
                    if ($video['group_id'] != '' && $result) {
                        //=========================================================
                        //广播：直播结束
                        $ext = array();
                        $ext['type'] = 18; //18：直播结束（全体推送的，用于更新用户列表状态）
                        $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                        //18：直播结束（全体推送的，用于更新用户列表状态）
                        $api->group_send_group_system_notification($m_config['on_line_group_id'], json_encode($ext), null);
                        //=========================================================
                    }
                    ajax_return(array('status' => 1, 'error' => '关闭直播成功！'));
                }
                ajax_return(array('status' => 0, 'error' => '关闭直播失败！', 'data' => $ret));
            } else {
                ajax_return(array('status' => 0, 'error' => '关闭直播失败！'));
            }
        }
        ajax_return($root);
    }

    /**
     * [banned_account 封号]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-29T11:12:21+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function banned_account()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            $to_user_id = strim($_REQUEST['to_user_id']); //被禁言的用户id
            if ($to_user_id == $user_id) {
                ajax_return(array('status' => 0, 'error' => '不能封禁自己'));
            }
            $GLOBALS['db']->query('UPDATE `' . DB_PREFIX . 'user` SET `is_effect` = 0 WHERE `id` = ' . $to_user_id);
            if ($GLOBALS['db']->affected_rows()) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($id, array('is_effect' => 0));
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->kick($to_user_id);
                $root['data'] = $ret;
            } else {
                ajax_return(array('status' => 0, 'error' => '封禁用户失败！'));
            }
        }
        ajax_return($root);
    }

    /**
     * [banned_device 封设备]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-29T12:04:35+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function banned_device()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $m_config = load_auto_cache("m_config");
            $system_user_id = $m_config['tim_identifier']; //系统消息
            $room_id = trim($_REQUEST['room_id']);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($room_id);

            if (!empty($video)) {
                $podcast_id = $video['user_id'];

                $GLOBALS['db']->query('UPDATE `' . DB_PREFIX . 'user` SET `ban_type` = 2 WHERE `id` = ' . $to_user_id);
                if ($GLOBALS['db']->affected_rows()) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_redis->update_db($id, array('ban_type' => 2));
                    // $room_id = intval($video['id']);
                    if ($video['user_id'] == $user_id) {
                        ajax_return(array('status' => 0, 'error' => '不能封禁自己设备'));
                    }
                    $ext = array();
                    $ext['type'] = 17;
                    $ext['desc'] = '违规直播，立即关闭直播';
                    $ext['room_id'] = $room_id;
                    #构造高级接口所需参数
                    $msg_content = array();
                    //创建array 所需元素
                    $msg_content_elem = array(
                        'MsgType' => 'TIMCustomElem', //自定义类型
                        'MsgContent' => array(
                            'Data' => json_encode($ext),
                            'Desc' => ''
                            //  'Ext' => $ext,
                            //  'Sound' => '',
                        )
                    );
                    //将创建的元素$msg_content_elem, 加入array $msg_content
                    array_push($msg_content, $msg_content_elem);
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();
                    $ret = $api->openim_send_msg2($system_user_id, $podcast_id, $msg_content);
                    //结束直播
                    // $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,video_vid,video_type from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $podcast_id;
                    // $video = $GLOBALS['db']->getRow($sql, true, true);
                    //同时关闭子房间
                    if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                        $child_id = $GLOBALS['db']->getAll("SELECT child_id FROM " . DB_PREFIX . "child_room WHERE parent_id =" . $room_id);
                    }
                    if ($video && $ret['ActionStatus'] == 'OK') {
                        $result = admin_do_end_video($video, $video['video_vid'], 0, $video['cate_id']);
                        if ($child_id) {
                            foreach ($child_id as $item) {
                                $child_ids[] = $item['child_id'];
                            }
                            $child_ids = implode(',', $child_ids);
                            $child_video = $GLOBALS['db']->getAll("SELECT id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,video_vid,video_type FROM " . DB_PREFIX . "video WHERE id in (" . $child_ids . ")");
                            if (!empty($child_video)) {
                                foreach ($child_video as $value) {
                                    $child_res = admin_do_end_video($value, $value['video_vid'], 0, $value['cate_id']);
                                }
                            }
                        }
                        $room_id = $video['id'];
                        if ($video['group_id'] != '' && $result) {
                            //=========================================================
                            //广播：直播结束
                            $ext = array();
                            $ext['type'] = 18; //18：直播结束（全体推送的，用于更新用户列表状态）
                            $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                            //发送广播：直播结束
                            //18：直播结束（全体推送的，用于更新用户列表状态）
                            $api->group_send_group_system_notification($m_config['on_line_group_id'], json_encode($ext), null);
                            //=========================================================
                        }
                        ajax_return(array('status' => 1, 'error' => '封禁设备成功！'));
                    }
                    ajax_return(array('status' => 0, 'error' => '封禁设备失败！'));
                } else {
                    ajax_return(array('status' => 0, 'error' => '封禁设备失败！'));
                }
            } else {
                ajax_return(array('status' => 0, 'error' => '封禁设备失败！'));
            }
        }
        ajax_return($root);
    }
}
