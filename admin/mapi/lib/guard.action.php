<?php
/**
 * 守护module
 */
class guardModule extends baseModule
{
    /**
     * 用户充值界面
     */
    public function guard_rule_list()
    {
        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $anchor_id = floatval($_REQUEST['anchor_id']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $root['diamonds'] = $user_redis->getOne_db($user_id, 'diamonds');
            $root['coin'] = $user_redis->getOne_db($user_id, 'coin');
            $guard_rule_list = load_auto_cache("guard_rule_list");
            $ua = user_guard_syn_redis($user_id, $anchor_id);
            if (!empty($ua)) {
                foreach ($guard_rule_list as $key => &$value) {
                    if ($ua['guard_id'] != $value['id']) {
                        unset($guard_rule_list[$key]);
                    }
                }
            }
            $root['guard_rule_list'] = array_values($guard_rule_list);
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
            $guard_id = intval($_REQUEST['guard_id']); //守护的类型
            $rule_id = intval($_REQUEST['rule_id']); //支付项目id
            $anchor_id = intval($_REQUEST['anchor_id']);
            $ua = user_guard_syn_redis($user_id, $anchor_id);
            if (!empty($ua) && $ua['guard_id'] != $guard_id) {
                ajax_return(array('error' => '您的守护到期以前不能购买其它守护', 'status' => 0));
            }
            if ($user_id == $anchor_id) {
                ajax_return(array('error' => '不能守护自己', 'status' => 0));
            }
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data = $user_redis->getRow_db($anchor_id, array('id', 'nick_name'));
            if ($user_data['id'] === false) {
                ajax_return(array('error' => '不存在该主播', 'status' => 0));
            }
            $guard_rule = load_auto_cache("guard_rule_list", array('guard_id' => $guard_id, 'rule_id' => $rule_id));
            if (empty($guard_rule)) {
                ajax_return(array('error' => '支付id无效', 'status' => 0));
            }
            if (empty($guard_rule[0]['rules'])) {
                ajax_return(array('error' => '项目id无效', 'status' => 0));
            }

            $m_config = load_auto_cache("m_config"); //初始化手机端配置

            $guard = $guard_rule[0];
            $rule = $guard_rule[0]['rules'][0];
            $total_ticket = $rule['ticket'];
            try {
                $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds - " . $rule['diamonds'] . ",use_diamonds = use_diamonds + " . $rule['diamonds'] . ", score = score + " . $rule['score'] . " WHERE id = '" . $user_id . "' AND diamonds >= " . $rule['diamonds'];
                $GLOBALS['db']->query($sql);
                if ($GLOBALS['db']->affected_rows()) {
                    //记录：秀豆 减少日志
                    if ($total_ticket > 0) {
                        //增加：用户秀票
                        $sql = "UPDATE " . DB_PREFIX . "user SET ticket = ticket + " . $total_ticket . " WHERE id = " . $anchor_id;
                        $GLOBALS['db']->query($sql);
                    }
                    // // 写记录
                    // $sql = 'REPLACE INTO ' . DB_PREFIX . 'guardian_record (`user_id`, `anchor_id`, `start_time`, `end_time`) VALUES (' . $user_id . ', ' . $anchor_id . ', ' . time() . ', ' . (time() + (3600 * 24 * $rule['day_length'])) . ')';
                    // $GLOBALS['db']->query($sql);
                    //提交事务,不等 消息推送,防止锁太久
                    user_guard_syn($user_id, $anchor_id, $rule);
                    user_deal_to_reids(array($user_id, $anchor_id));

                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
                    $video_viewer = new VideoViewerRedisService();
                    $video_id = $video_viewer->memberid_to_videoid($user_id) ?: 0;
                    $data = array();
                    $data['diamonds'] = $rule['diamonds'];
                    $data['score'] = $rule['score'];
                    $data['to_user_id'] = $anchor_id;
                    $data['video_id'] = $video_id;
                    account_log_com($data, $user_id, '购买[' . $guard['name'] . ']守护主播' . $anchor_id . 'x' . $rule['day_length'] . '天', array('type' => 31));
                    //更新用户等级
                    $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'nick_name', 'login_type', 'ticket', 'refund_ticket', 'anchor_level', 'v_icon'));
                    user_leverl_syn($user_info);
                    if (!empty($video_id)) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                        $video_redis = new VideoRedisService();
                        $group_id = $video_redis->getOne_db($video_id, 'group_id');
                        $anchor_info = $user_redis->getRow_db($anchor_id, array('id', 'score', 'online_time', 'user_level', 'nick_name', 'login_type', 'ticket', 'refund_ticket', 'anchor_level'));
                        $desc = '感谢' . $user_info['nick_name'] . '为' . $anchor_info['nick_name'] . '开通了' . $rule['day_length'] . '天' . $guard['name'] . '守护，一路上有你，TA不再孤单！';
                        SendVideoMsg($video_id, $desc, array(
                            'user_id' => $user_info['id'], //发送人昵称
                            'nick_name' => $user_info['nick_name'], //发送人昵称
                            'head_image' => get_spec_image($user_info['head_image']), //发送人头像
                            'user_level' => $user_info['user_level'] //用户等级
                        ));

                        $data = array(
                            'type' => 150, // 开通守护的动画
                            'room_id' => (string) $video_id,
                            'is_animated' => 1,
                            'sender' => array(
                                'user_id' => $user_info['id'], //发送人昵称
                                'nick_name' => $user_info['nick_name'], //发送人昵称
                                'head_image' => get_spec_image($user_info['head_image']), //发送人头像
                                'user_level' => $user_info['user_level'], //用户等级
                                'v_icon' => $user_info['v_icon']
                            ),
                            'icon' => get_spec_image($guard['icon']),
                            'to_user_id' => $anchor_id,
                            'fonts_color' => '',
                            'desc' => $desc,
                            'desc2' => $desc,
                            'anim_type' => '',
                            'top_title' => '',
                            'anim_cfg' => array(array(
                                'url' => get_spec_image($guard['open_gif']),
                                'play_count' => '1',
                                'delay_time' => '0',
                                'duration' => '2000',
                                'show_user' => '0',
                                'type' => '2',
                                'gif_gift_show_style' => null
                            ))
                        );
                        if (!empty($guard['open_svg_file'])) {
                            $data['is_animated'] = 3;
                            $data['svg_file'] = get_spec_image($guard['open_svg_file']);
                            $data['anim_cfg'] = array();
                        }
                        #构造高级接口所需参数
                        $msg_content = array();
                        //创建array 所需元素
                        $msg_content_elem = array(
                            'MsgType' => 'TIMCustomElem', //自定义类型
                            'MsgContent' => array(
                                'Data' => json_encode($data),
                                'Desc' => ''
                                //  'Ext' => $ext,
                                //  'Sound' => '',
                            )
                        );
                        //将创建的元素$msg_content_elem, 加入array $msg_content
                        array_push($msg_content, $msg_content_elem);
                        fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                        $api = createTimAPI();

                        $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                        if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                            //10002 系统错误，请再次尝试或联系技术客服。
                            log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                            $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                        }
                        $root['data'] = $ret;
                    }
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
     * [guard_list 主播调用守护自己的用户]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-13T14:33:08+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function guard_list()
    {
        $root = array();
        $root['status'] = 1;
        //$GLOBALS['user_info']['id'] = 1;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
            $video_viewer_redis = new VideoViewerRedisService();

            $anchor_id = isset($_REQUEST['anchor_id']) ? intval($_REQUEST['anchor_id']) : intval($GLOBALS['user_info']['id']); //用户ID
            $room_id = isset($_REQUEST['room_id']) ? intval($_REQUEST['room_id']) : 0;

            $list = load_auto_cache('guardian_record_list', array('anchor_id' => $anchor_id));
            foreach ($list as &$value) {
                $video_viewer = $video_viewer_redis->existence_viewer_list($room_id, $value['user_id']);
                $value['in_room'] = (int) ($video_viewer['video_viewer_level_score'] > 0);
            }
            $root['list'] = $list;
            $root['status'] = 1;
        }
        ajax_return($root);
    }
}
