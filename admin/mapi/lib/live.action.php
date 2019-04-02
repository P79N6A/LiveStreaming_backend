<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: shaoyv(172231343@qq.com)
// +----------------------------------------------------------------------
class liveModule extends baseModule
{
    /**
     * 切换付费（主播端）
     */
    public function live_pay()
    {
        $root = array('status' => 1, 'error' => '');
        $data = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置

            if (!isset($m_config)) {
                $root['error'] = "初始化手机端配置错误";
                $root['status'] = 0;
                ajax_return($root);
            }

            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $room_id = intval($_REQUEST['room_id']); //直播ID 也是room_id
            $live_fee = intval($_REQUEST['live_fee']); //直播收取的费用 （秀豆/分钟）
            $pay_type = intval($_REQUEST['live_pay_type']); //收费类型 0按时收费，1按场次收费
            //提档
            $is_mention = intval($_REQUEST['is_mention']); //提档 0不提档 1 提档
            //按时
            $live_pay_max = intval($m_config['live_pay_max']); //付费直播收费最高
            $live_pay_min = intval($m_config['live_pay_min']); //付费直播收费最低
            //按场
            $live_pay_scene_max = intval($m_config['live_pay_scene_max']); //付费直播收费最高
            $live_pay_scene_min = intval($m_config['live_pay_scene_min']); //付费直播收费最低

            if ($pay_type == 0 && (defined('LIVE_PAY') && LIVE_PAY == 0)) {
                $root['error'] = "按时付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($pay_type == 1 && (defined('LIVE_PAY_SCENE') && LIVE_PAY_SCENE == 0)) {
                $root['error'] = "按场付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }

            if ($pay_type == 0 && $live_pay_max < $live_fee && $is_mention == 0 && $live_pay_max > 0) {
                $root['error'] = "按时收费不能高于" . $live_pay_max . "秀豆";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($pay_type == 0 && $live_pay_min > $live_fee && $is_mention == 0) {
                $root['error'] = "按时收费不能低于" . $live_pay_min . "秀豆";
                $root['status'] = 0;
                ajax_return($root);
            }

            if ($pay_type == 1 && $live_pay_scene_max < $live_fee && $is_mention == 0 && $live_pay_scene_max > 0) {
                $root['error'] = "按场收费不能高于" . $live_pay_scene_max . "秀豆";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($pay_type == 1 && $live_pay_scene_min > $live_fee && $is_mention == 0) {
                $root['error'] = "按场收费不能低于" . $live_pay_scene_min . "秀豆";
                $root['status'] = 0;
                ajax_return($root);
            }

            //判断付费是否开启
            $pay_info = $this->get_pay_info();
            if ($pay_type == 0 && intval($pay_info['live_pay']) == 0) {
                $root['error'] = "按时付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($pay_type == 1 && intval($pay_info['live_pay_scene']) == 0) {
                $root['error'] = "按付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $fields = array('group_id', 'live_fee', 'live_is_mention', 'is_live_pay', 'user_id');
            $video_info = $video_redis->getRow_db($room_id, $fields);
            $live_pay_type = intval($video_info['live_pay_type']);
            //实际付费人数
            if ($live_pay_type == 0) {
                $times = get_gmtime() - 60;
                $sql = "select count(*) from " . DB_PREFIX . "live_pay_log where video_id =" . $room_id . " and pay_time_end>=" . $times;
                $live_viewer = $GLOBALS['db']->getOne($sql, true, true);
            } else {
                $live_viewer = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "live_pay_log where video_id =" . $room_id, true, true);
            }
            $group_id = $video_info['group_id'];

            if (!$room_id) {
                $root['error'] = "直播ID不存在";
                $root['status'] = 0;
                ajax_return($root);
            }
            $m_config['live_count_down'] = intval($m_config['live_count_down']) ? intval($m_config['live_count_down']) : 120;
            $live_pay_time = intval(NOW_TIME + $m_config['live_count_down']);
            //加入直播意外终止的问题
            $p_user_id = $video_info['user_id'];
            //获取支付信息
            $sql = "select id,user_id,live_pay_type,live_fee from " . DB_PREFIX . "video  where user_id = " . $p_user_id . " and live_pay_type=" . $pay_type . "  and is_live_pay =1 and is_aborted = 1 and live_in !=3";
            $live_old_info = $GLOBALS['db']->getRow($sql);
            $live_old_id = intval($live_old_info['id']); //被服务器异常终止结束(主要是心跳超时)
            $pay_room_id = 0;
            if ($live_old_id > 0) {
                $pay_room_id = $live_old_id;
            }
            //提档流程
            if ($is_mention == 1 && $pay_type == 0) {
                if (intval($video_info['is_live_pay']) == 1 && intval($video_info['live_fee']) > 0 && intval($video_info['live_fee']) == 0) {
                    $root['status'] = 0;
                    ajax_return($root);
                }
                if (intval($m_config['live_pay_fee']) == 0) {
                    $root['status'] = 0;
                    $root['error'] = "提档参数不存在";
                    ajax_return($root);
                }

                $live_fee = intval($m_config['live_pay_fee'] + $video_info['live_fee']);
                //更新付费信息
                $sql = "update " . DB_PREFIX . "video set live_is_mention =1,live_fee = " . $live_fee . ",live_pay_type = " . $pay_type . ",pay_room_id = " . $pay_room_id . " where live_pay_type=0 and is_live_pay =1 and live_in =1 and id = " . $room_id . " and user_id = " . $user_id;
            } else {
                if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                } else {
                    if (intval($video_info['is_live_pay']) == 1 && intval($video_info['live_fee']) > 0) {
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
                if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                    $sql = "update " . DB_PREFIX . "video set live_fee = '" . $live_fee . "',public_screen = 0,live_pay_type = '" . $pay_type . "',is_live_pay =1,live_pay_time = '" . $live_pay_time . "',pay_room_id = " . $pay_room_id . " where is_live_pay =1 and  live_in =1 and public_screen=1 and id = " . $room_id . " and user_id = " . $user_id;
                    $public_screen = $GLOBALS['db']->getOne("SELECT public_screen FROM  " . DB_PREFIX . "video WHERE user_id=" . $user_id . " and live_in=1");
                } else {
                    $sql = "update " . DB_PREFIX . "video set live_fee = '" . $live_fee . "',live_pay_type = '" . $pay_type . "',is_live_pay =1,live_pay_time = '" . $live_pay_time . "',pay_room_id = " . $pay_room_id . " where is_live_pay =0 and  live_in =1 and id = " . $room_id . " and user_id = " . $user_id;
                }
            }
            $GLOBALS['db']->query($sql);
            if ($GLOBALS['db']->affected_rows()) {
                $data['live_fee'] = $live_fee;
                $data['live_viewer'] = intval($live_viewer);
                $data['pay_type'] = $pay_type;
                $data['count_down'] = $m_config['live_count_down'];
                $root = $data;
                $root['status'] = 1;
                $root['error'] = '';
                sync_video_to_redis($room_id, '*', false);
                if ($is_mention == 0) {
                    //=========================================================
                    //广播：开始进入收费直播
                    //发送广播：收费直播
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();
                    $ext = array();
                    if ($pay_type == 1) {
                        $ext['type'] = 40; //40：按场收费直播（全体推送的，用于通知用户即将进入收费直播）
                    } else {
                        $ext['type'] = 32; //32：按时收费直播（全体推送的，用于通知用户即将进入收费直播）
                    }
                    $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应

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
                    //32：收费直播
                    //$api->group_send_group_msg2($group_id, $msg_content);
                    //$api->openim_push($user_id, $msg_content,0);
                    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                    //=========================================================
                    if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                        //10002 系统错误，请再次尝试或联系技术客服。
                        log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                        $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                    }

                    if ($ret['ActionStatus'] == 'FAIL') {
                        $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                    } else {
                        if ($pay_type == 1) {
                            $root['error'] = '已切换为付费直播，' . $live_fee . '秀豆/场';
                        } else {
                            $root['error'] = '已切换为付费直播，' . $live_fee . '秀豆/分钟';
                        }

                    }
                } else {
                    if ($pay_type == 0 && $is_mention == 1) {
                        $root['error'] = '提档成功，' . $live_fee . '秀豆/分钟';
                    }
                }
            } else {
                $root['status'] = 0;
                $root['error'] = '切换失败';
            }
        }
        ajax_return($root);
    }
    /**
     * 扣费（观众端）
     */
    public function live_pay_deduct()
    {
        $root = array('status' => 1, 'error' => '');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $room_id = intval($_REQUEST['room_id']); //直播ID 也是room_id
            $is_agree = intval($_REQUEST['is_agree']); //是否同意

            if (!$room_id) {
                $root['error'] = "直播ID不存在";
                $root['status'] = 0;
                ajax_return($root);
            }

            $user_info = $GLOBALS['user_info'];

/*            //子房间
$child_id = 0;
if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
$child_room = new child_room();
$parent_id = $child_room->parent_id($room_id);
if ($parent_id != $room_id) {
$child_id = $room_id;
$room_id = $parent_id;
}
}*/

            /*fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $fields = array('id','is_aborted','end_time','live_in','user_id','group_id','live_pay_time','live_pay_type','live_fee','live_is_mention','pay_room_id');
            $video_info = $video_redis->getRow_db($room_id,$fields);*/

            $sql = "select id,is_aborted,end_time,live_in,user_id,group_id,live_pay_time,live_pay_type,live_fee,live_is_mention,pay_room_id from " . DB_PREFIX . "video  where id =" . $room_id;
            $video_info = $GLOBALS['db']->getRow($sql);
            //判断直播结束
            if (empty($video_info)) {
                $video_info = $GLOBALS['db']->getRow($sql);
            }
            if (intval($video_info['live_in']) == 0 && $video_info['end_time'] == '' && $video_info['is_aborted'] != 1) {
                $root['error'] = "直播已结束不存在";
                $root['status'] = 0;
                ajax_return($root);
            }
            //判断付费是否开启
            $pay_info = $this->get_pay_info();
            if ($video_info['live_pay_type'] == 0 && intval($pay_info['live_pay']) == 0) {
                $root['error'] = "按时付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($video_info['live_pay_type'] == 1 && intval($pay_info['live_pay_scene']) == 0) {
                $root['error'] = "按场付费未开启";
                $root['status'] = 0;
                ajax_return($root);
            }

            //观众在直播间内
            if (0) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
                $video_viewer_redis = new VideoViewerRedisService();
                $video_viewer = $video_viewer_redis->existence_viewer_list($room_id, $user_id);
                $in_room = $video_viewer['video_viewer_level_score'];
            } else {
                $in_room = 1;
            }

            if (intval($video_info['live_fee']) != 0 && $in_room && $video_info['user_id'] != $user_id) {
                $data = $this->pay_deduct($video_info, $user_info, $is_agree);
                $root = $data;
            }
        }

        ajax_return($root);
    }
    /**
     * 扣费
     */
    public function pay_deduct($video_info, $user_info, $is_agree)
    {
        $data = array('status' => 1, 'error' => '');
        $user_id = $user_info['id'];
        $room_id = $video_info['id'];
        $new_room_id = 0;
        if (intval($video_info['pay_room_id']) > 0) {
            $room_id = $video_info['pay_room_id'];
            $new_room_id = $video_info['id'];
        }

        //

        //扣费开始
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $m_config['pay_interval'] = 1; //扣费间隔时间 （分）
        //开始扣费剩余时间
        $data['is_live_pay'] = 1;
        $data['live_pay_type'] = intval($video_info['live_pay_type']);
        $data['is_agree'] = $is_agree;
        $data['live_fee'] = $video_info['live_fee'];

        if ($data['live_pay_type'] == 0) {
            $live_time = $video_info['live_pay_time'] - NOW_TIME;
            $live_time = $live_time > 0 ? intval($live_time) : 0;
        } else {
            $live_time = 0;
        }

        $now_time = NOW_TIME;
        //倒计时结束 and 同意扣费

        if (floatval($m_config['uesddiamonds_to_score']) <= 0) {
            $data = array('status' => 0, 'error' => 'uesddiamonds_to_score参数错误');
            return $data;
        }
        //增加：主播秀票
        if (floatval($m_config['ticket_to_rate']) <= 0) {
            $data = array('status' => 0, 'error' => 'ticket_to_rate参数错误');
            return $data;
        }

        if ($is_agree) {
//
            $data['on_live_pay'] = 1; //收费中
            $data['count_down'] = 0;
            $total_time = intval($m_config['pay_interval'] * 60); //本次观看时间

            if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                $sql = "select id,pay_time_next,total_time,total_diamonds,pay_type from " . DB_PREFIX . "live_pay_log  where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                $live_pay_log_info = $GLOBALS['db']->getRow($sql);
                if (intval($live_pay_log_info['id']) == 0) {
                    $sql = "select id,pay_time_next,total_time,total_diamonds,pay_type from " . DB_PREFIX . "live_pay_log_history  where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                    $live_pay_log_info = $GLOBALS['db']->getRow($sql);
                }
            } else {
                //获取支付信息
                $sql = "select id,pay_time_next,total_time,total_diamonds from " . DB_PREFIX . "live_pay_log  where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                $live_pay_log_info = $GLOBALS['db']->getRow($sql);
                if (intval($live_pay_log_info['id']) == 0) {
                    $sql = "select id,pay_time_next,total_time,total_diamonds from " . DB_PREFIX . "live_pay_log_history  where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                    $live_pay_log_info = $GLOBALS['db']->getRow($sql);
                }
            }

            //开始扣费的条件
            //一、按时扣费 => 1.未扣费过；2.扣费过  and 达到扣费时间
            //二、按场收费 => 1.未扣费过；

            $allow = 0;

            //可扣费 =》 按时扣费 AND 扣费过 AND 达到扣费时间
            if (intval($live_pay_log_info['id']) > 0 && $live_pay_log_info['pay_time_next'] <= $now_time && intval($video_info['live_pay_type']) == 0) {
                $allow = 1;
            } elseif (intval($live_pay_log_info['id']) == 0) {
                //可扣费 =》 未扣费过
                $allow = 1;
            } else {
                $public_screen = $GLOBALS['db']->getOne("SELECT public_screen FROM  " . DB_PREFIX . "video WHERE user_id=" . $video_info['user_id'] . " and live_in=1");
                if ($live_pay_log_info['pay_type'] == 1 && $public_screen == 0 && $video_info['live_pay_type'] == 1) {
                    $allow = 1;
                }
            }

            if ($allow) {
                //-----事务开始-----
                $pInTrans = $GLOBALS['db']->StartTrans();
                try
                {
                    //扣除观众的秀豆，增加经验
                    $total_score = intval($video_info['live_fee'] * floatval($m_config['uesddiamonds_to_score']));
                    if ($live_time == 0) {
                        $sql = "update " . DB_PREFIX . "user set diamonds = diamonds-" . $video_info['live_fee'] . ",use_diamonds = use_diamonds+" . $video_info['live_fee'] . ", score = score + " . $total_score . " where diamonds >= " . $video_info['live_fee'] . " and id = " . $user_id;
                        $GLOBALS['db']->query($sql);
                    }

                    if ($GLOBALS['db']->affected_rows() || intval($live_time) > 0) {
                        $podcast_user_id = $video_info['user_id'];
                        $public_screen = $GLOBALS['db']->getOne("SELECT public_screen FROM  " . DB_PREFIX . "video WHERE user_id=" . $podcast_user_id . " and live_in=1");
                        if (intval($public_screen != 1)) {
                            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                                $child_room = new child_room();
                                $parent_id = $child_room->parent_id($room_id);
                                if ($room_id != $parent_id) {
                                    $podcast_user_id = $GLOBALS['db']->getOne("SELECT user_id FROM  " . DB_PREFIX . "video WHERE id=" . $parent_id . " and live_in=1");
                                    $room_id = $parent_id;
                                }
                            }
                            //主播获得的秀票
                            $total_ticket = round($video_info['live_fee'] * floatval($m_config['ticket_to_rate']), 2);
                            if ($total_ticket > 0 && $live_time == 0) {
                                $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $podcast_user_id;
                                $GLOBALS['db']->query($sql);
                                //更新主播直播间获得秀票
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                                $videoCont_redis = new VideoContributionRedisService();
                                $videoCont_redis->insert_db($user_id, $podcast_user_id, $room_id, $total_ticket);
                            }
                            $room_id = $video_info['id'];
                        }

                        //第一次付费
                        if (intval($live_pay_log_info['id']) == 0) {
                            $live_pay_log = array();
                            $live_pay_log['total_diamonds'] = $video_info['live_fee']; //秀豆（from_user_id减少的秀豆）合计
                            $live_pay_log['from_user_id'] = $user_id; //观众
                            $live_pay_log['to_user_id'] = $video_info['user_id']; //主播
                            $live_pay_log['create_time'] = $now_time; //时间
                            $live_pay_log['create_date'] = to_date($now_time); //日期字段，按日期归档；
                            $live_pay_log['create_ym'] = to_date($now_time, 'Ym'); //年月 如:201610
                            $live_pay_log['create_d'] = to_date($now_time, 'd'); //日
                            $live_pay_log['create_w'] = to_date($now_time, 'w'); //周
                            $live_pay_log['live_fee'] = $video_info['live_fee']; //收取费用（秀豆/分钟
                            if (intval($live_time)) {
                                $live_pay_time = $video_info['live_pay_time'] + intval($live_time);
                            } else {
                                $live_pay_time = $video_info['live_pay_time'];
                            }

                            $live_pay_log['live_pay_time'] = $live_pay_time; //直播间开始收费时间
                            $live_pay_log['live_pay_date'] = to_date($live_pay_time); //直播间开始收费 日期字段
                            $live_pay_log['video_id'] = $room_id; //直播ID
                            $live_pay_log['group_id'] = $video_info['group_id']; //群组ID
                            $live_pay_log['pay_time_end'] = $live_pay_time; //最后一次扣款时间
                            $live_pay_log['live_pay_type'] = intval($video_info['live_pay_type']); //直播类型
                            $live_pay_log['total_ticket'] = $total_ticket; //主播获得的秀票
                            $live_pay_log['total_score'] = $total_score; //积分（from_user_id可获得的积分）合计
                            $live_pay_log['uesddiamonds_to_score'] = intval($m_config['uesddiamonds_to_score']); //观众（from_user_id）获得积分的转换比例
                            $live_pay_log['ticket_to_rate'] = intval($m_config['ticket_to_rate']); //主播（to_user_id）获得的秀票转换比例
                            if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                                if ($public_screen == 1) {
                                    $live_pay_log['pay_type'] = 1; //是否为公屏收费记录
                                }
                            }
                            if (intval($video_info['live_pay_type']) == 0) {
                                $live_pay_log['total_time'] = 0; //累计时间（from_user_id付费观看的时间）合计
                                if (intval($live_time)) {
                                    $now_live_time = intval($live_time);
                                    $live_pay_log['pay_time_next'] = $now_time + $now_live_time; //下次扣款时间
                                } else {
                                    $live_pay_log['pay_time_next'] = $now_time + 60 * intval($m_config['pay_interval']); //下次扣款时间
                                }
                            }

                            $GLOBALS['db']->autoExecute(DB_PREFIX . "live_pay_log", $live_pay_log, "INSERT");

//                            if (defined('CHILD_ROOM') && CHILD_ROOM == 1 && $child_id > 0) {
                            //                                $child_live_pay_log = $live_pay_log;
                            //                                $child_live_pay_log['video_id'] = $child_id;
                            //                                $GLOBALS['db']->autoExecute(DB_PREFIX . "child_live_pay_log", $child_live_pay_log,
                            //                                    "INSERT");
                            //                            }
                            $count_down = $video_info['live_pay_time'] - $now_time > 0 ? $video_info['live_pay_time'] - $now_time : 0;

                            //更新主播扣费人数
                            $sql = "update " . DB_PREFIX . "video set live_pay_count = live_pay_count + 1 where is_live_pay =1 and id = " . $room_id . " and user_id = " . $user_id;
                            $GLOBALS['db']->query($sql);

                        } else {

                            $pay_time_end = $live_pay_log_info['pay_time_next'];
                            $pay_time_next = $pay_time_end + 60; //下次扣款时间

                            if ($now_time - $pay_time_end >= 30) {
                                $pay_time_next = $now_time + 60;
                            }

                            if ($video_info['live_is_mention']) {
                                //提档
                                $live_is_mention_time = $now_time; //提档开始时间
                                $live_is_mention_pay = $video_info['live_fee']; //提档前扣费
                                $sql = "update " . DB_PREFIX . "live_pay_log set ticket_to_rate = " . intval($m_config['ticket_to_rate']) . ",uesddiamonds_to_score = " . intval($m_config['uesddiamonds_to_score']) . ",total_score = total_score + " . $total_score . ", total_ticket = total_ticket + " . $total_ticket . ", new_room_id=" . $new_room_id . ",live_is_mention_time =" . $live_is_mention_time . ",live_is_mention_pay =" . $live_is_mention_pay . ",total_diamonds=total_diamonds+" . $live_is_mention_pay . ",total_time=total_time+" . $total_time . ", pay_time_end = " . $pay_time_end . ",pay_time_next = " . $pay_time_next . " where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                            } else {
                                $sql = "update " . DB_PREFIX . "live_pay_log set ticket_to_rate = " . intval($m_config['ticket_to_rate']) . ",uesddiamonds_to_score = " . intval($m_config['uesddiamonds_to_score']) . ",total_score = total_score + " . $total_score . ", total_ticket = total_ticket + " . $total_ticket . ", new_room_id=" . $new_room_id . ",total_diamonds=total_diamonds+" . $video_info['live_fee'] . ",total_time=total_time+" . $total_time . ", pay_time_end = " . $pay_time_end . ",pay_time_next = " . $pay_time_next . " where from_user_id = " . $user_id . " and to_user_id = " . $video_info['user_id'] . " and video_id=" . $room_id;
                            }
                            $GLOBALS['db']->query($sql);

                        }
                        if ($live_time == 0) {
                            //写入用户日志
                            $log_data = array();
                            $log_data['diamonds'] = intval($video_info['live_fee']);
                            $log_data['log_admin_id'] = 0;
                            $param['type'] = 6; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票  4 分享获得秀票 5 登录赠送积分 6 观看付费直播
                            $log_msg = '观看付费直播间' . $room_id . '，消费' . $video_info['live_fee'] . '秀豆';
                            account_log_com($log_data, $user_id, $log_msg, $param);
                        }

                        //获取用户当前的秀豆数量
                        $sql = "select diamonds from " . DB_PREFIX . "user  where id = " . $user_id;
                        $diamonds = $GLOBALS['db']->getOne($sql);
                        $data['msg'] = "";
                        //判断用户是否足够观看5分钟
                        if (intval($video_info['live_pay_type']) == 0) {
                            if ($diamonds > $video_info['live_fee'] * 5) {
                                $data['is_diamonds_low'] = 0;
                                $data['is_recharge'] = 0;
                            } else {
                                $data['is_diamonds_low'] = 1;
                                $data['is_recharge'] = 0;
                                $surplus_time = intval($diamonds / $video_info['live_fee']);
                                $data['msg'] = "您当前的秀豆数量过少，剩余观看" . $surplus_time . "分钟";
                            }
                        } else {
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                            $user_follw_redis = new UserFollwRedisService($user_id);
                            if (!$user_follw_redis->is_following($video_info['user_id'])) {
                                if (intval($video_info['pay_room_id']) > 0) {
                                    $room_id = $video_info['id'];
                                }

                                //关注主播
                                $root = redis_set_follow($user_id, $video_info['user_id'], false, $room_id);
                                clear_auto_cache("playback_list", array("user_id" => $user_id));
                            }

                        }
                    } else {
                        $data['is_diamonds_low'] = 1;
                        $data['is_recharge'] = 1;
                        $data['msg'] = "您当前的秀豆数量过少，是否前往充值？";
                        $data['count_down'] = 0;
                    }
                    //同步信息
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_infos = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                    user_leverl_syn($user_infos); //更新用户等级

                    user_deal_to_reids(array($user_id, $video_info['user_id'])); //同步user信息到redis

                    sync_video_to_redis($room_id, '*', false); //将mysql数据,同步一份到redis中
                    //提交事务
                    $GLOBALS['db']->Commit($pInTrans);
                } catch (Exception $e) {
                    //异常回滚
                    $root['error'] = $e->getMessage();
                    $root['status'] = 0;

                    $GLOBALS['db']->Rollback($pInTrans);
                }
                //-----事务结束-----

                $total_times = intval($live_pay_log_info['total_time']); //累计观看时间
                $total_diamonds = $video_info['live_fee'] + intval($live_pay_log_info['total_diamonds']); //累计消费秀豆

            } else {
                $total_times = intval($live_pay_log_info['total_time'] - $total_time); //累计观看时间
                $total_diamonds = intval($live_pay_log_info['total_diamonds']); //累计消费秀豆
            }

            $data['total_time'] = intval($total_times) <= 0 ? 0 : intval($total_times);
            $data['total_diamonds'] = intval($total_diamonds);

        } else {

            $data['is_popup'] = 1;

            if ($live_time == 0) {
                $data['on_live_pay'] = 1; //倒计时结束；已经开始收费
            } else {
                $data['on_live_pay'] = 0; //倒计时未结束；未实际收费
            }
            $data['count_down'] = $live_time; //倒计时
        }

        $sql = "select diamonds from " . DB_PREFIX . "user  where id = " . $user_id;
        $diamonds = $GLOBALS['db']->getOne($sql);
        $data['diamonds'] = intval($diamonds);

        //直播间主播获得的秀票和秀豆
        $sql = "select ticket from " . DB_PREFIX . "user  where id = " . $video_info['user_id'];
        $ticket = $GLOBALS['db']->getOne($sql);
        $data['ticket'] = intval($ticket);
        return $data;
    }

    /*
     * 付费直播 消费榜
     */
    public function pay_cont()
    {
        $root = array('status' => 1, 'error' => '');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {

            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $type = intval($_REQUEST['type']); //类型 0消费，1收入
            $page = intval($_REQUEST['p']); //取第几页数据
            $live_pay_type = intval($_REQUEST['live_pay_type']); //付费直播 类型 0按时 1按场
            if (intval(LIVE_PAY_TIME) == 0) {
                $live_pay_type = 1;
            }
//兼容客户端

            if ($page == 0) {
                $page = 1;
            }

            $page_size = 30;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $where = " and live_pay_type = " . $live_pay_type;

            if ($type) {
                $sql = "select lp.video_id as room_id,lp.total_time,lp.create_time,lp.total_diamonds,lp.from_user_id as user_id,u.head_image,u.user_level,u.sex,u.nick_name,lp.live_pay_type from " . DB_PREFIX . "live_pay_log_history lp left join " . DB_PREFIX . "user as u on u.id = lp.from_user_id where lp.to_user_id = " . $user_id . $where . "   order by lp.create_time desc limit " . $limit;
            } else {
                //$sql = "select lp.video_id as room_id,lp.total_time,lp.total_diamonds,lp.to_user_id as user_id,u.head_image,u.user_level,u.sex,u.nick_name from ".DB_PREFIX."live_pay_log lp left join ".DB_PREFIX."user as u on u.id = lp.from_user_id where lp.from_user_id = ".$user_id."  group by lp.video_id order by lp.total_diamonds limit ".$limit ;
                $sql = "select lp.video_id as room_id,lp.total_time,lp.create_time,lp.total_diamonds,lp.to_user_id as user_id,u.head_image,u.user_level,u.sex,u.nick_name,lp.live_pay_type from " . DB_PREFIX . "live_pay_log_history lp left join " . DB_PREFIX . "user as u on u.id = lp.to_user_id where lp.from_user_id = " . $user_id . $where . "   order by lp.create_time desc limit " . $limit;
            }

            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as $k => $v) {
                $list[$k]['head_image'] = get_spec_image($v['head_image'], 150, 150);
                $total_time_format = get_live_time_len($v['total_time']);
                $total_time_format = $total_time_format ? $total_time_format : "0分钟";
                $list[$k]['total_time_format'] = $total_time_format;
                $list[$k]['start_time'] = to_date($v['create_time']);
                $list[$k]['end_time'] = to_date($v['create_time']);
            }

            $rs_count = count($list);
            if ($page == 1) {
                $root['page'] = array('page' => $page, 'has_next' => 0);
            } else {
                $has_next = ($rs_count > $page * $page_size) ? '1' : '0';
                $root['page'] = array('page' => $page, 'has_next' => $has_next);
            }

            $root['data'] = $list;
        }
        ajax_return($root);
    }

    public function get_pay_info()
    {
        //付费开关
        $live_pay_info = $GLOBALS['db']->getAll("SELECT id,class FROM " . DB_PREFIX . "plugin WHERE is_effect=1 and type = 1");
        $live_pay = array();
        if ($live_pay_info) {
            foreach ($live_pay_info as $k => $v) {
                $live_pay[$v['class']] = $v['id'];
            }
        }
        return $live_pay;
    }
}
