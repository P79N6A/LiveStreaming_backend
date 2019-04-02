<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class dealModule
{
    /**
     * 送礼物
     */
    public function pop_prop()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            $prop_id = intval($_REQUEST['prop_id']); //礼物id
            $num = max(intval($_REQUEST['num']), 1); //礼物数量
            $is_plus = intval($_REQUEST['is_plus']); //1显示连续;
            $video_id = strim($_REQUEST['room_id']); //直播ID 也是room_id
            $from = strim($_REQUEST['from']); //判断发送来源 pc或者app
            $is_coins = isset($_REQUEST['is_coins']) ? intval($_REQUEST['is_coins']) : 0; //0是秀豆，1是游戏币

            $child_id = isset($_REQUEST['child_id']) ? intval($_REQUEST['child_id']) : $video_id; //子房间

            $is_backpack = isset($_REQUEST['is_backpack']) ? intval($_REQUEST['is_backpack']) : 0; //0不是背包的礼物，1是背包的礼物

            $coordinate = isset($_REQUEST['coordinate']) ? (json_decode($_REQUEST['coordinate'], true) ?: array()): array();

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $group = $video_redis->getRow_db($video_id, array('group_id'));

            //子房间模块
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $video_id = $child_room->child_pop_prop($user_id, $video_id);
            }
            //
            //$sql = "select id,user_id,group_id from ".DB_PREFIX."video where id = ".$video_id;
            //$video = $GLOBALS['db']->getRow($sql);

            $video = $video_redis->getRow_db($video_id, array('id', 'user_id', 'group_id', 'prop_table', 'room_type'));

            if ($group) {
                $group_id = strim($group['group_id']); //群组ID
            } else {
                $group_id = strim($video['group_id']); //群组ID
            }
            $podcast_id = intval($video['user_id']); //送给谁，有群组ID(group_id)，除了红包外其它的都是送给：群主
            $room_type = intval($video['room_type']); //直播间类型 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）

            //子房间
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $parent_id = $child_room->parent_id($video_id);
                $p_video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
                if (empty($group_id)) {
                    $group_id = $p_video['group_id'];
                }
            }

            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id, true, true);
            if ($is_nospeaking) {
                $root['status'] = 0;
                $root['error'] = "被im全局禁言，不能发礼物";
                api_ajax_return($root);
            }

            if ($user_id == $podcast_id) {
                $root['error'] = "不能发礼物给自己";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            //检查测试账号不能发礼物给真实主播
            $sql = "select mobile from " . DB_PREFIX . "user where id = '" . $podcast_id . "'";
            $podcast_mobile = $GLOBALS['db']->getOne($sql);
            if (($GLOBALS['user_info']['mobile'] == '13888888888' && $podcast_mobile != '13999999999') || $GLOBALS['user_info']['mobile'] == '13999999999' && $podcast_mobile != '13888888888') {
                $root['error'] = "测试账号不能发礼物给真实主播";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            //以后需要从缓存中读取
            //$sql = "select id,name,score,diamonds,icon,ticket,is_much,sort,is_red_envelope,is_animated from ".DB_PREFIX."prop where id = '".$prop_id."'";
            //$prop = $GLOBALS['db']->getRow($sql);

            $prop = load_auto_cache("prop_id", array('id' => $prop_id));
            if ($prop['is_special'] == 1) {
                $root['error'] = "特殊道具不能直接赠送";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            if ($num <= 0 || ($prop['is_red_envelope'] == 1)) {
                $num = 1;
            }

            if ($is_coins == 0) {
                $total_diamonds = bcmul($num, $prop['diamonds']);
            } else {
                $total_diamonds = bcmul($num, $prop['coins']);
            }

            $total_score = bcmul($num, $prop['score']);
            $total_ticket = bcmul($num, $prop['ticket']);
            $total_society_ticket = bcmul($num, $prop['society_ticket']);
            $robot_diamonds = intval($prop['robot_diamonds']);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            // //车行定制 倒计时期间不能发红包
            // if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
            //     if ($prop['is_red_envelope'] == 1) {
            //         $prop_red_envelope = $GLOBALS['db']->getRow("SELECT id,prop_id,video_id,from_user_id,to_user_id,create_time FROM " . $video['prop_table'] . " WHERE is_red_envelope=1  ORDER BY id DESC limit 1");
            //         $prop_time = $prop_red_envelope['create_time'] + 45;
            //         if ($prop_time > NOW_TIME) {
            //             $root['error'] = "红包倒计时期间不能发红包，请于" . intval($prop_time - NOW_TIME) . "秒后再发红包！";
            //             $root['status'] = 0;
            //             api_ajax_return($root);
            //         }
            //     }
            // }
            $root = $this->pack_prop($video['prop_table'], $video_redis, $total_diamonds, $total_score, $total_ticket, $num, $prop, $is_plus, $video_id, $user_id, $prop_id, $podcast_id, $group_id, $room_type, $from, $robot_diamonds, $is_coins, $child_id, $is_backpack, $coordinate, $total_society_ticket);
            // PK 的礼物消息
        }
        api_ajax_return($root);
    }

    /**
     * 邀请码分销
     * @param $user_id
     * @param $total_ticket
     * @param $prop_id
     */
    private function check_invite($user_id, $total_ticket, $prop_id)
    {
        if (!defined('OPEN_INVITE_CODE') || OPEN_INVITE_CODE != 1) {
            return;
        }
        $m_config = load_auto_cache('m_config');
        if ($m_config['invite_ratio'] <= 0 || $m_config['invite_ratio'] > 1) {
            return;
        }

        $ratio = round($total_ticket * $m_config['invite_ratio'], 2);
        if ($ratio <= 0) {
            return;
        }

        $invite_by_user_id = $GLOBALS['db']->getOne("select invite_by_user_id from fanwe_user_invite where user_id = {$user_id}");
        if ($invite_by_user_id <= 0) {
            return;
        }

        $invite_user = $GLOBALS['db']->getRow("select is_effect,is_authentication from " . DB_PREFIX . "user where id = {$invite_by_user_id}");
        if (!$invite_user['is_effect'] && $invite_user['is_authentication'] == 2) {
            return;
        }

        $GLOBALS['db']->query("update " . DB_PREFIX . "user set ticket = ticket + " . $ratio . " where id = " . $invite_by_user_id);
        $GLOBALS['db']->autoExecute(DB_PREFIX . "invite_distribution_log", array(
            'from_user_id' => $user_id,
            'to_user_id' => $invite_by_user_id,
            'create_date' => "'" . to_date(NOW_TIME, 'Y-m-d') . "'",
            'prop_id' => $prop_id,
            'ticket' => $ratio,
            'create_time' => NOW_TIME,
            'create_ym' => to_date(NOW_TIME, 'Ym'),
            'create_d' => to_date(NOW_TIME, 'd'),
            'create_w' => to_date(NOW_TIME, 'W')
        ));

        user_deal_to_reids(array($invite_by_user_id));
    }

    /**
     * 送礼物封装
     */
    public function pack_prop($table, $video_redis, $total_diamonds, $total_score, $total_ticket, $num, $prop, $is_plus, $video_id, $user_id, $prop_id, $podcast_id, $group_id, $room_type, $from, $robot_diamonds, $is_coins = 0, $child_id = 0, $is_backpack = 0, $coordinate = array(), $total_society_ticket = 0)
    {
        $pInTrans = $GLOBALS['db']->StartTrans();
        try
        {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            // fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            // $video_redis = new VideoRedisService();
            // $pk_video = $video_redis->getRow_db($video_id, array('in_livepk', 'pk_ticket'));
            //免费礼物
            if ($total_diamonds == 0 && $total_score == 0 && $total_ticket == 0) {
                if ($is_backpack == 1) {
                    $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                    $GLOBALS['db']->query($sql);
                    if ($GLOBALS['db']->affected_rows()) {
                        //提交事务,不等 消息推送,防止锁太久
                        $GLOBALS['db']->Commit($pInTrans);
                        $pInTrans = false; //防止，下面异常时，还调用：Rollback
                    } else {
                        $GLOBALS['db']->Rollback($pInTrans);
                        return array('status' => 0, 'error' => '用户背包道具不足');
                    }
                }
                $type = 1;
                //普通会员收到的提示内容;
                $desc = "我送了" . $num . "个" . $prop['name'];
                //礼物接收人（主播）收到的提示内容
                $desc2 = $desc;

                $ext = array();
                $ext['type'] = $type; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束; 8:红包
                $ext['num'] = $num;
                $ext['is_plus'] = $is_plus; //1：数量连续叠加显示;0:不叠加;这个值是从客户端上传过来的
                $ext['svg_file'] = $prop['svg_file']; //1:序列帧礼物
                $ext['drawn_time'] = $prop['drawn_time']; //1:手绘礼物动画时间
                $ext['is_much'] = $prop['is_much']; //1:可以连续发送多个;用于小金额礼物
                $ext['room_id'] = $video_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['is_animated'] = $prop['is_animated']; //1:动画；0：未动画
                $ext['coordinate'] = $coordinate;
                //消息发送者
                $sender = array();
                $user_info = $user_redis->getRow_db($user_id, array('id', 'nick_name', 'v_icon', 'head_image', 'user_level', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                $sender['user_id'] = $user_id; //发送人昵称
                $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                $sender['user_level'] = $user_info['user_level']; //用户等级
                $sender['v_icon'] = $user_info['v_icon']; //认证图标
                $ext['sender'] = $sender;
                if ($type == 1) {
                    $ext['prop_id'] = intval($prop_id); //礼物id
                    $ext['icon'] = get_spec_image($prop['icon']); //图片，是否要: 大中小格式？
                    $ext['total_ticket'] = intval($user_redis->getOne_db($podcast_id, 'ticket')); //用户总的：秀票数
                    // $ext['total_pk_ticket'] = $pk_video['in_livepk'] ? $pk_video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                    $ext['to_user_id'] = $podcast_id; //礼物接收人（主播）
                    $ext['fonts_color'] = ''; //字体颜色
                    $ext['desc'] = $desc; //普通群员收到的提示内容;
                    $ext['desc2'] = $desc2; //礼物接收人（主播）收到的提示内容;
                    $ext['anim_type'] = $prop['anim_type']; //大型道具类型;
                    $ext['top_title'] = $sender['nick_name'] . "送了" . $prop['name']; //大型道具类型，标题;
                    $ext['anim_cfg'] = $prop['anim_cfg'];
                }

                //车行定制 ljz 身份等级颜色区分
                if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                    fanwe_require(APP_ROOT_PATH . "mapi/car/core/common_car.php");
                    $res = video_status_effect($user_info, $podcast_id); //用户信息，主播id
                    foreach ($res as $key => $val) {
                        $ext[$key] = $val;
                    }
                }

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

                if (isset($_REQUEST['is_debug'])) {
                    $root['error'] = '';
                    $root['status'] = 1;
                } else {
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();

                    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                    if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                        //10002 系统错误，请再次尝试或联系技术客服。
                        log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                        $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                    }

                    //$videoGift_redis->update_db($user_prop_id, $ret);

                    if ($ret['ActionStatus'] == 'FAIL') {
                        $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                        $root['status'] = 0;
                    } else {
                        $root['error'] = '';
                        $root['status'] = 1;
                    }
                }
            } else {
                //私密直播间送红包不加经验
                if ($prop['is_red_envelope'] == 1 && $room_type == 1) {
                    $total_score = 0;
                }
                if ($is_backpack == 1) {
                    $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                } else {
                    if ($is_coins == 0) {
                        //减少用户秀豆
                        $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds - " . $total_diamonds . ", use_diamonds = use_diamonds + " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and diamonds >= " . $total_diamonds;
                    } else {
                        //减少用户游戏币
                        $sql = "UPDATE " . DB_PREFIX . "user SET coin = coin - " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and coin >= " . $total_diamonds;
                    }
                }
                $GLOBALS['db']->query($sql);
                if ($GLOBALS['db']->affected_rows()) {

                    if ($is_coins) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/Model.class.php');
                        Model::$lib = dirname(__FILE__);
                        $user_model = Model::build('user');
                        $account_diamonds = $user_model->coin($user_id);
                        Model::build('coin_log')->addLog($user_id, -1, -$total_diamonds, $account_diamonds, '送礼物消费游戏币');
                    }
                    //记录：秀豆 减少日志
                    if ($total_ticket > 0) {
                        $update = '';
                        if ($prop['is_red_envelope'] == 1) {
                            //主播增加：秀豆 数量
                            //$user_redis->lock_diamonds($podcast_id,$total_ticket);
                            $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds + " . $total_ticket . " WHERE id = " . $podcast_id;
                            $GLOBALS['db']->query($sql);
                        } else {
                            $m_config = load_auto_cache("m_config"); //初始化手机端配置
                            if (intval(OPEN_REWARD_GIFT)) {
                                if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                                    $list_award = get_award_info();
                                }
                            }
                            if (defined("robot_gifts") && robot_gifts == 1) {
                                $roboter = $GLOBALS['db']->getOne("SELECT roboter FROM " . DB_PREFIX . "user WHERE roboter=1 AND id=" . $user_id . " LIMIT 1"); //查询是否特殊权限用户
                                if ($roboter) {
                                    //增加：不可提现秀票
                                    $sql = "UPDATE " . DB_PREFIX . "user SET no_ticket = no_ticket + " . $total_ticket . " WHERE id = " . $podcast_id . " LIMIT 1";
                                    $GLOBALS['db']->query($sql);
                                } else {
                                    //增加：用户秀票
                                    $sql = "UPDATE " . DB_PREFIX . "user SET ticket = ticket + " . $total_ticket . " WHERE id = " . $podcast_id . " LIMIT 1";
                                    $GLOBALS['db']->query($sql);
                                }
                            } else {
                                //增加：用户秀票
                                $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "user SET ticket = ticket + " . $total_ticket . " WHERE id = " . $podcast_id . " LIMIT 1");
                                if (intval(OPEN_CAR_MODULE) && (intval($prop['is_heat']) == 1)) {
                                    //热度礼物 原热度
                                    $update = ", heat_value=heat_value+" . $total_ticket;
                                }
                            }
                        }
                        // 邀请码分销
                        $this->check_invite($user_id, $total_ticket, $prop_id);
                        /**
                         * 记录在redis中
                         */
                        //当前直播获得秀票数
                        // $sql = ;
                        $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "video SET vote_number = vote_number + " . $total_ticket . " $update WHERE id =" . $video_id);
                    }

                    //=========数据库更新成功后,处理redis数据==========

                    //插入:送礼物表 修改礼物直接写入 mysql @by slf
                    $video_prop = array();
                    $video_prop['prop_id'] = intval($prop_id);
                    $video_prop['prop_name'] = "'" . $prop['name'] . "'";
                    $video_prop['is_red_envelope'] = $prop['is_red_envelope'];
                    $video_prop['total_score'] = $total_score;
                    $video_prop['total_diamonds'] = $total_diamonds;
                    $video_prop['total_ticket'] = intval($total_ticket); //is_red_envelope=1时,为主播获得的：秀豆 数量
                    $video_prop['from_user_id'] = $user_id;
                    $video_prop['to_user_id'] = $podcast_id;
                    $video_prop['create_time'] = NOW_TIME;
                    $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                    $video_prop['num'] = $num;
                    $video_prop['video_id'] = $video_id;
                    $video_prop['group_id'] = "'" . $group_id . "'";

                    $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                    $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                    $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                    $video_prop['from_ip'] = "'" . get_client_ip() . "'";

                    $video_prop['is_coin'] = 0;
                    $video_prop['is_rocket'] = $prop['is_rocket'];
                    if ($is_coins == 1) {
                        $video_prop['is_coin'] = 1;
                    }
                    $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
                    if (!$table_info) {
                        $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
                    }
                    $table_version = $GLOBALS['db']->getRow("Describe " . $table . " is_coin", true, true);
                    if (!$table_version) {
                        $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `is_coin` varchar(255) NOT NULL  COMMENT '双币礼物，0是秀豆，1是游戏币'");
                    }
                    $table_version_is_rocket = $GLOBALS['db']->getRow("Describe " . $table . " is_rocket", true, true);
                    if (!$table_version_is_rocket) {
                        $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `is_rocket` int(1) NOT NULL  COMMENT '是否火箭榜礼物 1是 0否'");
                    }
                    if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                        $table_version_red_envelope_type = $GLOBALS['db']->getRow("Describe " . $table . " red_envelope_type", true, true);
                        if (!$table_version_red_envelope_type) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `red_envelope_type` int(1) NOT NULL  COMMENT '红包类型 0 普通红包 、1 全省红包 、 2全服红包'");
                        }

                        $table_version_is_heat = $GLOBALS['db']->getRow("Describe " . $table . " is_heat", true, true);
                        if (!$table_version_is_heat) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `is_heat` int(1) NOT NULL  COMMENT '是否热度礼物 1是 0否'");
                        }
                    }

                    if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                        $table_version_is_award = $GLOBALS['db']->getRow("Describe " . $table . " is_award", true, true);
                        if (!$table_version_is_award) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `is_award` varchar(255) NOT NULL  COMMENT '是否为可中奖礼物 1为 是、0为否'");
                        }
                    }
                    //将礼物写入mysql表中
                    $field_arr = array('prop_id', 'prop_name', 'is_red_envelope', 'total_score', 'total_diamonds', 'total_ticket', 'from_user_id', 'to_user_id', 'create_time', 'create_date', 'num', 'video_id', 'group_id', 'create_ym', 'create_d', 'create_w', 'from_ip', 'is_coin', 'is_rocket');
                    if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                        $field_arr[] = 'is_award';
                        $video_prop['is_award'] = intval($prop['is_award']);
                    }
                    if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                        $field_arr[] = 'red_envelope_type';
                        $video_prop['red_envelope_type'] = intval($prop['red_envelope_type']);
                        $field_arr[] = 'is_heat';
                        $video_prop['is_heat'] = intval($prop['is_heat']);
                    }

                    $fields = implode(",", $field_arr);
                    $valus = implode(",", $video_prop);
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
                    //$table = createPropTable();
                    $sql = "INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $valus . ")";
                    $GLOBALS['db']->query($sql);
                    $user_prop_id = $GLOBALS['db']->insert_id();
                    //写入总表
                    $award_res = array('status' => 0, 'is_award' => 0);
                    if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                        $table_all = createPropAllTable();
                        $table_version = $GLOBALS['db']->getRow("Describe " . $table_all . " is_coin", true, true);
                        if (!$table_version) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table_all . " ADD COLUMN `is_coin` varchar(255) NOT NULL  COMMENT '双币礼物，0是秀豆，1是游戏币'");
                        }
                        $sql = "INSERT INTO " . $table_all . " (" . $fields . ") VALUES (" . $valus . ")";
                        $GLOBALS['db']->query($sql);
                        if (intval(OPEN_REWARD_GIFT) && intval($prop['is_award']) == 1 && intval($list_award['is_open_award']) == 1) {
                            //礼物中奖逻辑处理
                            $award_info = array();
                            $award_info['video_id'] = $video_id;
                            $award_info['group_id'] = $group_id;
                            $award_info['from_ip'] = "'" . get_client_ip() . "'";
                            $award_info['user_id'] = $user_id;
                            $award_info['podcast_id'] = $podcast_id;
                            $award_result = gift_award($award_info, $prop, $total_diamonds);
                            if ($award_result['status']) {
                                $award_res = $award_result; // 是否中奖
                            }
                        }
                    }
                    $root['award'] = $award_res; //

                    //提交事务,不等 消息推送,防止锁太久
                    $GLOBALS['db']->Commit($pInTrans);
                    $pInTrans = false; //防止，下面异常时，还调用：Rollback

                    //子房间
                    if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                        $child_room = new child_room();
                        $child_room->child_room_prop($child_id, $video_id, $video_prop, $fields, $user_id, $total_ticket);
                    }
                    if (intval($prop['is_red_envelope']) == 0 && $total_ticket > 0) {
                        // if (intval($prop['is_heat']) == 0) {
                        //贡献榜
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                        $videoCont_redis = new VideoContributionRedisService();
                        $videoCont_redis->insert_db($user_id, $podcast_id, $video_id, $total_ticket, $total_society_ticket);
                        // }
                    }
                    //分销功能 计算抽成
                    if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1 && $prop['is_red_envelope'] == 0 && $total_ticket > 0) {
                        $this->distribution_calculate($user_id, $total_ticket);
                    }
                    if ($prop['is_red_envelope'] == 0 && $total_ticket > 0) {
                        $this->distribution($podcast_id, $video_id, $total_ticket);
                    }

                    user_deal_to_reids(array($user_id, $podcast_id));

                    //更新用户等级
                    $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'nick_name', 'login_type', 'ticket', 'refund_ticket', 'anchor_level'));
                    user_leverl_syn($user_info);

                    /*
                    $sql = "select diamonds,use_diamonds,score,ticket,user_level,refund_ticket from ".DB_PREFIX."user where id = ".$user_id;
                    $user_data = $GLOBALS['db']->getRow($sql);
                    $user_redis->update_db($user_id, $user_data);

                    $sql = "select diamonds,use_diamonds,score,ticket,user_level,refund_ticket from ".DB_PREFIX."user where id = ".$podcast_id;
                    $user_data = $GLOBALS['db']->getRow($sql);
                    $user_redis->update_db($podcast_id, $user_data);
                     */

                    //=================发送:礼物=================================

                    $type = 1;
                    //普通会员收到的提示内容;
                    if ($prop['is_red_envelope'] == 1) {
                        $desc = "我给大家送了{$num}个红包";
                        $type = 8;
                    } else {
                        $desc = "我送了{$num}个{$prop['name']}";
                    }

                    //礼物接收人（主播）收到的提示内容
                    $desc2 = $desc;

                    $ext = array();
                    $ext['type'] = $type; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束; 8:红包
                    $ext['num'] = $num;
                    $ext['is_plus'] = $is_plus; //1：数量连续叠加显示;0:不叠加;这个值是从客户端上传过来的
                    $ext['svg_file'] = $prop['svg_file']; //1:序列帧礼物
                    $ext['drawn_time'] = $prop['drawn_time']; //1:手绘礼物动画时间
                    $ext['is_much'] = $prop['is_much']; //1:可以连续发送多个;用于小金额礼物
                    $ext['room_id'] = $video_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                    $ext['award'] = $award_res; //
                    $ext['coordinate'] = $coordinate;

                    //车行定制 ljz 身份等级颜色区分
                    if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                        fanwe_require(APP_ROOT_PATH . "mapi/car/core/common_car.php");
                        $res = video_status_effect($user_info, $podcast_id); //用户信息，主播id
                        foreach ($res as $key => $val) {
                            $ext[$key] = $val;
                        }
                    }

                    if ($prop['is_much']) {
                        // 计算连发次数，兼容 PC 端
                        $key = "user:prop:{$user_id}:{$video_id}:{$prop_id}";
                        $user_prop = $GLOBALS['cache']->get($key, true);
                        if ($user_prop && $user_prop['time'] > NOW_TIME) {
                            $plus_num = $user_prop['num'] + 1;
                            if ($from == 'pc' && $plus_num > 1) {
                                $is_plus = 1;
                            }
                        } else {
                            $plus_num = 1;
                        }
                        $ext['plus_num'] = $plus_num;
                        // app 上传 is_plus 视为连发
                        $GLOBALS['cache']->set($key, array('time' => NOW_TIME + 5, 'num' => $plus_num), 5, true);
                        //APP
                        $key = "user:prop:{'app'}{$user_id}:{$video_id}:{$prop_id}";
                        $app_user_prop = $GLOBALS['cache']->get($key, true);
                        if ($app_user_prop && $is_plus == 1) {
                            $app_plus_num = $app_user_prop['num'] + 1;
                        } else {
                            $app_plus_num = 1;
                        }
                        $ext['app_plus_num'] = $app_plus_num;
                        // app 上传 is_plus 视为连发
                        $GLOBALS['cache']->set($key, array('num' => $app_plus_num), 86400, true);

                    } else {
                        $ext['app_plus_num'] = 1;
                    }

                    $ext['is_animated'] = $prop['is_animated']; //1:动画；0：未动画

                    //消息发送者
                    $sender = array();
                    $user_info = $user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon'));
                    $sender['user_id'] = $user_id; //发送人昵称
                    $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                    $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                    $sender['user_level'] = $user_info['user_level']; //用户等级
                    $sender['v_icon'] = $user_info['v_icon']; //认证图标

                    $ext['sender'] = $sender;

                    if ($type == 1) {
                        $ext['prop_id'] = intval($prop_id); //礼物id
                        //$ext['animated_url'] = $prop['animated_url'];//动画播放url
                        $ext['icon'] = get_spec_image($prop['icon']); //图片，是否要: 大中小格式？
                        //$ext['is_red_envelope'] = $prop['is_red_envelope'];//是否是：红包；1:红包
                        $ext['user_prop_id'] = intval($user_prop_id); //红包时用到，抢红包的id
                        //$ext['show_num'] = $show_num;//显示连续送的礼物数量;

                        $fields = array('ticket', 'no_ticket', 'refund_ticket', 'head_image');
                        $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                        $ext['total_ticket'] = $user_info['ticket'] + $user_info['no_ticket']; //用户总的：秀票数
                        //直播间显示主播实际可提现秀票（客户定制，标准版保留此功能）
                        if (0) {
                            $ext['total_ticket'] = $ext['total_ticket'] - intval($user_info['refund_ticket']);
                        }
                        // $ext['total_pk_ticket'] = $pk_video['in_livepk'] ? $pk_video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                        $ext['to_user_id'] = $podcast_id; //礼物接收人（主播）
                        $ext['fonts_color'] = ''; //字体颜色
                        $ext['desc'] = $desc; //普通群员收到的提示内容;
                        $ext['desc2'] = $desc2; //礼物接收人（主播）收到的提示内容;
                        $ext['anim_type'] = $prop['anim_type']; //大型道具类型;

                        $ext['top_title'] = $sender['nick_name'] . "送了" . $prop['name']; //大型道具类型，标题;

                        /*
                        if ($ext['is_animated'] == 1){
                        //要缓存getAllCached
                        $sql = "select id,url,play_count,delay_time,duration,show_user,type from ".DB_PREFIX."prop_animated where prop_id = ".$prop_id." order by sort desc";
                        $anim_list = $GLOBALS['db']->getAll($sql);
                        $ext['anim_cfg'] = $anim_list;
                        //$ext['sql'] = $sql;
                        }else{
                        $ext['anim_cfg'] = array();
                        }
                         */

                        $ext['anim_cfg'] = $prop['anim_cfg'];
                    } else {
                        $ext['prop_id'] = intval($prop_id); //礼物id
                        //$ext['animated_url'] = $prop['animated_url'];//动画播放url
                        $ext['icon'] = get_spec_image($prop['icon']); //图片，是否要: 大中小格式？
                        //$ext['is_red_envelope'] = $prop['is_red_envelope'];//是否是：红包；1:红包
                        $ext['user_prop_id'] = intval($user_prop_id); //红包时用到，抢红包的id
                        //$ext['show_num'] = $show_num;//显示连续送的礼物数量;
                        $ext['total_ticket'] = intval($user_redis->getOne_db($podcast_id, 'ticket')); //用户总的：秀票数
                        // $ext['total_pk_ticket'] = $pk_video['in_livepk'] ? $pk_video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                        $ext['to_user_id'] = $podcast_id; //礼物接收人（主播）
                        $ext['to_diamonds'] = $total_ticket; //礼物接收人（主播）,获得的：秀豆 数量
                        $ext['fonts_color'] = ''; //字体颜色
                        $ext['desc'] = $desc; //普通群员收到的提示内容;
                        $ext['desc2'] = $desc2; //礼物接收人（主播）收到的提示内容;

                        $allot_diamonds = 0;
                        if ($prop['is_red_envelope'] == 1 && $room_type == 1) {

                        } else {
                            if ($robot_diamonds > 0) {
                                //优先分配给：观众列表中的机器人
                                $robot_list = $video_redis->get_robot($video_id);

                                $robot_num = count($robot_list);
                                if ($robot_num > 0) {
                                    //给一半以上的机器 人分配
                                    $robot_num = mt_rand(ceil($robot_num / 2), $robot_num);
                                    //可分配的秀豆小于机器人数1.3倍时,减少分配人数
                                    if ($robot_num * 1.3 > $robot_diamonds) {
                                        $robot_num = ceil($robot_diamonds / 2);
                                    }

                                    $diamonds_list = $this->red_rand_list2($robot_diamonds, $robot_num);

                                    while (count($diamonds_list) > 0) {
                                        $money = $diamonds_list[0];
                                        array_splice($diamonds_list, 0, 1);

                                        $robot_num = count($robot_list) - 1;
                                        $r = mt_rand(0, $robot_num);
                                        $robot_userid = $robot_list[$r];

                                        array_splice($robot_list, $r, 1);

                                        //实际分配的
                                        $allot_diamonds = $allot_diamonds + $money;

                                        allot_red_to_user($user_prop_id, $robot_userid, $money);
                                    };
                                }
                            }
                        }

                        //生成一个随机红包队列（观众可抢秀豆=diamonds-ticket-robot_diamods)
                        $money_list = $this->red_rand_list($total_diamonds - $total_ticket - $allot_diamonds);
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
                        $videoRed_redis = new VideoRedRedisService();
                        $videoRed_redis->push_red($user_prop_id, $money_list);

                        //记录直播间发的：红包 记录;
                        $video_redis->add_red($video_id, $user_prop_id);
                        if (intval(FULLSERVER_RED_ENVELOPE) == 1 && intval(OPEN_CAR_MODULE) == 1) //判断是否开启全服红包
                        {
                            $root['red_envelope_type'] = intval($prop['red_envelope_type']); //判断红包类型 0普通、1全省、2全服
                            if ($root['red_envelope_type'] > 0) {
                                //获取房间类型
                                $sql = "SELECT room_type,live_in FROM " . DB_PREFIX . "video WHERE id=" . $video_id;
                                $room_envelope = $GLOBALS['db']->getRow($sql, true, true);
                                $room_type = intval($room_envelope['room_type']);
                                $live_type = intval($room_envelope['live_in']);
                                if ($room_type != 1) //判定房间是否为私有房间或回播的房间
                                {
                                    if (file_exists(APP_ROOT_PATH . 'mapi/car/core/common_car.php')) {
                                        fanwe_require(APP_ROOT_PATH . 'mapi/car/core/common_car.php');
                                        $ext['head_image'] = get_spec_image($user_redis->getOne_db($podcast_id, 'head_image')); //主播头像
                                        $root[] = propNotify_red($sender, $ext, $prop); //执行全服通告
                                    }
                                    //log_file($root,'red_envelope_type');
                                }
                            }
                        }

                    }
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

                    if (isset($_REQUEST['is_debug'])) {
                        $root['error'] = '';
                        $root['status'] = 1;
                    } else {
                        fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                        $api = createTimAPI();

                        $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                        if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                            //10002 系统错误，请再次尝试或联系技术客服。
                            log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                            $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                        }

                        $GLOBALS['db']->autoExecute($table, $ret, 'UPDATE', 'id=' . $user_prop_id);

                        //$videoGift_redis->update_db($user_prop_id, $ret);

                        if ($ret['ActionStatus'] == 'FAIL') {
                            $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                            $root['status'] = 0;
                        } else {
                            if (defined('PROP_NOTIFY') && PROP_NOTIFY == 1) {
                                $m_config = load_auto_cache('m_config');
                                if (($prop['diamonds'] >= intval($m_config['valuable_gift_quota'])) || ($type == 1 && $prop['anim_type'] != '') || ($prop['is_full_push'] == 1) || (!empty($ext['award']['status']) && $ext['award']['status'] == 1)) //判断开关是否开启,是否为大型礼物
                                {
                                    $root['is_notify'] = intval($m_config['is_prop_notify']); //判断后台是否开启这个功能
                                    if ($root['is_notify']) {
                                        //获取房间类型
                                        $sql = "SELECT room_type,live_in FROM " . DB_PREFIX . "video WHERE id=" . $video_id;
                                        $video_info = $GLOBALS['db']->getRow($sql, true, true);
                                        $room_type = $video_info['room_type'];
                                        $live_type = $video_info['live_in'];

                                        if (intval($room_type) != 1 && intval($live_type) != 3) //判定房间是否为私有房间或回播的房间
                                        {
                                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/prop_notify.php');
                                            $ext['head_image'] = get_spec_image($user_redis->getOne_db($podcast_id, 'head_image')); //主播头像
                                            $nick_name = $user_redis->getOne_db($podcast_id, 'nick_name'); //主播昵称
                                            $desc = "豪气冲天的【" . $sender['nick_name'] . "】送了" . $prop['name'] . " x {$num} 给主播【" . $nick_name . "】";
                                            $ext['data_type'] = 1;
                                            $root[] = propNotify($sender, $ext, $prop, $desc); //执行全服通告
                                        }
                                    }
                                    $root['is_notify'] = intval($m_config['is_prop_notify']);
                                }
                            }

                            $root['error'] = '';
                            $root['status'] = 1;
                        }
                    }

                } else {
                    $GLOBALS['db']->Rollback($pInTrans);
                    if ($is_backpack) {
                        $root['error'] = '用户背包道具不足';
                    } else if ($is_coins) {
                        $root['error'] = '用户游戏币不足';
                    } else {
                        $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                    }
                    $root['status'] = 0;
                    return $root;
                }
            }
            if ($prop['is_rocket'] == 1) {
                up_rocket_all($podcast_id);
            }
        } catch (Exception $e) {
            //异常回滚
            $root['error'] = $e->getMessage();
            $root['status'] = 0;

            $GLOBALS['db']->Rollback($pInTrans);
            return $root;
        }
        //暮橙定制: IM推送用户等级和经验信息
        push_level_info($user_id);

        if (defined('OPEN_MISSION') && OPEN_MISSION) {
            require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
            Model::$lib = dirname(__FILE__);
            Model::build('mission')->incProgress($user_id, 2);
        }
        return $root;
    }

    /**
     * 弹幕消息接口
     */
    public function pop_msg()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $room_id = strim($_REQUEST['room_id']); //直播ID 也是room_id
            $msg = strim($_REQUEST['msg']); //消息内容8J+UkQ==
            //$to_user_id = intval($_REQUEST['to_user_id']);//群主ID
            // $is_backpack = isset($_REQUEST['is_backpack']) ? intval($_REQUEST['is_backpack']) : 0; //0不是背包的礼物，1是背包的礼物

            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id,
                true, true);

            if ($is_nospeaking) {
                $root['status'] = 0;
                $root['error'] = "被im全局禁言，不能发消息";
                api_ajax_return($root);
            }

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($room_id, array('id', 'user_id', 'group_id', 'prop_table', 'in_livepk', 'pk_ticket'));

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            $group_id = strim($video['group_id']); //群组ID

            $podcast_id = intval($video['user_id']); //送给谁，有群组ID(group_id)，除了红包外其它的都是送给：群主
            //子房间
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $parent_id = $child_room->parent_id($room_id);
                $p_video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
                if (empty($group_id)) {
                    $group_id = $p_video['group_id'];
                }
            }

            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            // PK yicket
            $root['is_backpack'] = 0;

            //主播,自己发送 弹幕 消息,不扣秀豆
            if ($podcast_id == $user_id) {
                $ext = array();
                $ext['type'] = 2; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['num'] = 1;
                $ext['prop_id'] = 0; //礼物id
                //s$ext['animated_url'] = '';//动画播放url
                $ext['icon'] = ''; //图片，是否要: 大中小格式？
                //$ext['is_red_envelope'] = 0;//是否是：红包；1:红包
                $ext['user_prop_id'] = 0; //红包时用到，抢红包的id
                //$ext['show_num'] = 1;//显示连续送的礼物数量;
                $fields = array('ticket', 'no_ticket', 'refund_ticket');
                $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                $ext['total_ticket'] = intval($user_info['ticket']) + intval($user_info['no_ticket']); //用户总的：秀票数
                //直播间显示主播实际可提现秀票（客户定制，标准版保留此功能）
                // $ext['total_pk_ticket'] = $video['in_livepk'] ? $video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                // if (0) {
                // $ext['total_ticket'] = $ext['total_ticket'] - intval($user_info['refund_ticket']);
                // }
                $ext['to_user_id'] = 0; //礼物接收人（主播）
                $ext['fonts_color'] = ''; //字体颜色
                $ext['desc'] = $msg; //弹幕消息;
                $ext['desc2'] = $msg; //弹幕消息;

                //消息发送者
                $sender = array();
                $user_info = $user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon', 'id', 'v_icon', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                $sender['user_id'] = $user_id; //发送人昵称
                $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                $sender['user_level'] = $user_info['user_level']; //用户等级
                $sender['v_icon'] = $user_info['v_icon']; //认证图标

                $ext['sender'] = $sender;

                //车行定制 ljz 身份对应的弹幕颜色(主播)
                if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                    $ext['v_identity'] = 3;
                    $ext['v_identity_colour'] = "#FF0000"; //红色
                    $ext['v_speak_num'] = 0;
                    $ext['v_join_name'] = '';
                }

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

                if (intval($m_config['has_dirty_words']) == 1) {
                    //文档内容,用来过滤脏字
                    $msg_text_elem = array(
                        'MsgType' => 'TIMTextElem', //
                        'MsgContent' => array(
                            'Text' => $msg
                        )
                    );
                    array_push($msg_content, $msg_text_elem, $msg_content_elem);
                } else {
                    //将创建的元素$msg_content_elem, 加入array $msg_content
                    array_push($msg_content, $msg_content_elem);
                }

                if (isset($_REQUEST['is_debug'])) {
                    $root['error'] = '';
                    $root['status'] = 1;
                } else {
                    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                    $api = createTimAPI();
                    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                }

                if ($ret['ActionStatus'] == 'FAIL') {
                    log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                    if ($ret['ErrorCode'] == 80001) {
                        $root['error'] = '该词已被禁用';
                    } else {
                        $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                    }
                    $root['status'] = 0;
                } else {
                    $root['error'] = '';
                    $root['status'] = 0; //app端通过这个判断是否扣除秀豆，1为扣除
                }
            } else {

                //$sql = "select id from ".DB_PREFIX."video_forbid_send_msg where group_id='".$group_id."' and user_id = ".$user_id;
                //$has_forbid = $GLOBALS['db']->getOne($sql,true,true) > 0;

                $has_forbid = $video_redis->has_forbid_msg2($group_id, $user_id);
                if ($has_forbid) {
                    $root['error'] = "被禁言,不能发送消息";
                    $root['status'] = 0;
                } else {
                    //file_put_contents(APP_ROOT_PATH.'mapi/lib/msg.txt', $msg);

                    //$msg2 = unserialize(file_get_contents(APP_ROOT_PATH.'mapi/lib/msg2.txt'));
                    //$msg =$msg .'【'.base64_decode("8J+UkQ==").'】';

                    //子房间收益归主房间主播
                    $p_id = $podcast_id;
                    if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                        $child_room = new child_room();
                        $parent_id = $child_room->parent_id($room_id);
                        $p_video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
                        $p_id = $p_video['user_id'];
                    }
                    // 获取弹幕的配置信息
                    $prop = load_auto_cache("prop_id", array('id' => 2));

                    $total_diamonds = $prop['diamonds'];
                    $total_score = $prop['score'];
                    $total_ticket = $prop['ticket'];

                    $pInTrans = $GLOBALS['db']->StartTrans();
                    try {
                        $status = false;
                        $GLOBALS['db']->query('UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - 1 WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "2" AND num >= 1');
                        if ($GLOBALS['db']->affected_rows()) {
                            $root['is_backpack'] = 1;
                            $status = true;
                        } else {
                            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds - " . $total_diamonds . ",use_diamonds = use_diamonds + " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and diamonds >= " . $total_diamonds;
                            $GLOBALS['db']->query($sql);
                            if ($GLOBALS['db']->affected_rows()) {
                                $status = true;
                            }
                        }
                        if ($status) {
                            if ($total_ticket > 0) {
                                if (defined("robot_gifts") && robot_gifts == 1) {
                                    $roboter = $GLOBALS['db']->getOne("select roboter from " . DB_PREFIX . "user where roboter=1 and id=" . $user_id); //查询是否特殊权限用户
                                    if ($roboter) {
                                        //增加：不可提现秀票
                                        $sql = "update " . DB_PREFIX . "user set no_ticket = no_ticket + " . $total_ticket . " where id = " . $p_id;
                                        $GLOBALS['db']->query($sql);

                                    } else {
                                        //增加：用户秀票
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $p_id;
                                        $GLOBALS['db']->query($sql);
                                    }
                                } else {
                                    //增加：用户秀票
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $p_id;
                                    $GLOBALS['db']->query($sql);
                                }

                                //当前直播获得秀票数
                                $sql = "update " . DB_PREFIX . "video set vote_number = vote_number + " . $total_ticket . " where id =" . $room_id;
                                $GLOBALS['db']->query($sql);

                                //记录：用户秀票增加日志
                            }

                            $video_prop = array();
                            $video_prop['prop_id'] = 0;
                            $video_prop['prop_name'] = "'" . '弹幕' . "'";
                            $video_prop['is_red_envelope'] = 0;
                            $video_prop['total_score'] = $total_score;
                            $video_prop['total_diamonds'] = $total_diamonds;
                            $video_prop['total_ticket'] = intval($total_ticket);
                            $video_prop['from_user_id'] = $user_id;
                            $video_prop['to_user_id'] = $podcast_id;
                            $video_prop['create_time'] = NOW_TIME;
                            $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                            $video_prop['num'] = 1;
                            $video_prop['video_id'] = $room_id;
                            $video_prop['group_id'] = "'" . $group_id . "'";
                            $video_prop['msg'] = "'" . $msg . "'";

                            $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                            $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                            $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                            $video_prop['from_ip'] = "'" . get_client_ip() . "'";
                            $video_prop['is_rocket'] = 0;

                            //将礼物写入mysql表中
                            $field_arr = array(
                                'prop_id',
                                'prop_name',
                                'is_red_envelope',
                                'total_score',
                                'total_diamonds',
                                'total_ticket',
                                'from_user_id',
                                'to_user_id',
                                'create_time',
                                'create_date',
                                'num',
                                'video_id',
                                'group_id',
                                'msg',
                                'create_ym',
                                'create_d',
                                'create_w',
                                'from_ip',
                                'is_rocket'
                            );
                            $fields = implode(",", $field_arr);
                            $valus = implode(",", $video_prop);

                            $table = $video['prop_table'];
                            $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
                            if (!$table_info) {
                                $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
                            }
                            $sql = "insert into " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                            $GLOBALS['db']->query($sql);
                            $user_prop_id = $GLOBALS['db']->insert_id();

                            //写入总表
                            if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                                $table_all = createPropAllTable();
                                $sql = "INSERT INTO " . $table_all . " (" . $fields . ") VALUES (" . $valus . ")";
                                $GLOBALS['db']->query($sql);
                            }

                            //提交事务,不等 消息推送,防止锁太久
                            $GLOBALS['db']->Commit($pInTrans);
                            $pInTrans = false; //防止，下面异常时，还调用：Rollback

                            //子房间
                            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                                $child_room = new child_room();
                                $child_room->child_room_pop_msg($room_id, $video_prop, $fields, $user_id, $total_ticket);
                            }
                            if ($total_ticket > 0) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                                $videoCont_redis = new VideoContributionRedisService();
                                $videoCont_redis->insert_db($user_id, $podcast_id, $room_id, $total_ticket);
                            }

                            user_deal_to_reids(array($user_id, $podcast_id));

                            //更新用户等级
                            $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                            user_leverl_syn($user_info);

                            //分销功能 计算抽成
                            // if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1 && $total_ticket > 0) {
                            //     $this->distribution_calculate($user_id, $total_ticket);
                            // }
                            // $this->distribution($podcast_id, $room_id, $total_ticket);
                            //发送:礼物

                            $ext = array();
                            $ext['type'] = 2; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                            $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                            $ext['num'] = 1;
                            $ext['prop_id'] = 0; //礼物id
                            //s$ext['animated_url'] = '';//动画播放url
                            $ext['icon'] = ''; //图片，是否要: 大中小格式？
                            //$ext['is_red_envelope'] = 0;//是否是：红包；1:红包
                            $ext['user_prop_id'] = $user_prop_id; //红包时用到，抢红包的id
                            //$ext['show_num'] = 1;//显示连续送的礼物数量;
                            $fields = array('ticket', 'no_ticket');
                            $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                            $ext['total_ticket'] = $user_info['ticket'] + $user_info['no_ticket']; //用户总的：秀票数
                            // $ext['total_pk_ticket'] = $video['in_livepk'] ? $video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                            //                            $ext['total_ticket'] = intval($user_redis->getOne_db($podcast_id, 'ticket'));//用户总的：秀票数
                            $ext['to_user_id'] = 0; //礼物接收人（主播）
                            $ext['fonts_color'] = ''; //字体颜色
                            $ext['desc'] = $msg; //弹幕消息;
                            $ext['desc2'] = $msg; //弹幕消息;

                            //消息发送者
                            $sender = array();
                            $user_info = $user_redis->getRow_db($user_id,
                                array('id', 'nick_name', 'v_icon', 'head_image', 'user_level', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                            $sender['user_id'] = $user_id; //发送人昵称
                            $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                            $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                            $sender['user_level'] = $user_info['user_level']; //用户等级

                            $ext['sender'] = $sender;

                            //车行定制 ljz 身份对应的弹幕颜色(观众)
                            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                                fanwe_require(APP_ROOT_PATH . "mapi/car/core/common_car.php");
                                $res = video_status_effect($user_info, $podcast_id); //用户信息，主播id
                                $res['v_join_name'] = '';
                                foreach ($res as $key => $val) {
                                    $ext[$key] = $val;
                                }

                            }

                            #构造高级接口所需参数
                            $msg_content = array();
                            //创建array 所需元素
                            $msg_content_elem = array(
                                'MsgType' => 'TIMCustomElem', //自定义类型
                                'MsgContent' => array(
                                    'Data' => json_encode($ext),
                                    'Desc' => ''
                                )
                            );

                            if (intval($m_config['has_dirty_words']) == 1) {
                                //文档内容,用来过滤脏字
                                $msg_text_elem = array(
                                    'MsgType' => 'TIMTextElem', //
                                    'MsgContent' => array(
                                        'Text' => $msg
                                    )
                                );
                                array_push($msg_content, $msg_text_elem, $msg_content_elem);
                            } else {
                                //将创建的元素$msg_content_elem, 加入array $msg_content
                                array_push($msg_content, $msg_content_elem);
                            }

                            if (isset($_REQUEST['is_debug'])) {
                                $root['error'] = '';
                                $root['status'] = 1;
                            } else {
                                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                                $api = createTimAPI();

                                $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                                if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                                    //10002 系统错误，请再次尝试或联系技术客服。
                                    log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                                    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                                }

                                $GLOBALS['db']->autoExecute($table, $ret, 'UPDATE', 'id=' . $user_prop_id);

                                //$videoGift_redis->update_db($user_prop_id, $ret);

                                if ($ret['ActionStatus'] == 'FAIL') {
                                    if ($ret['ErrorCode'] == 80001) {
                                        $root['error'] = '该词已被禁用';
                                    } else {
                                        $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                                    }
                                    $root['status'] = 0;
                                } else {
                                    $root['error'] = '';
                                    $root['status'] = 1;
                                }
                            }
                        } else {
                            $GLOBALS['db']->Rollback($pInTrans);
                            $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                            $root['status'] = 0;
                        }

                    } catch (Exception $e) {
                        //异常回滚
                        $root['error'] = $e->getMessage();
                        $root['status'] = 0;

                        $GLOBALS['db']->Rollback($pInTrans);
                    }
                }
            }
        }
        api_ajax_return($root);
    }

    /**
     * [full_msg 全服喇叭]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-08-31T15:46:50+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function full_msg()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $room_id = strim($_REQUEST['room_id']); //直播ID 也是room_id
            $msg = strim($_REQUEST['msg']); //消息内容8J+UkQ==
            //$to_user_id = intval($_REQUEST['to_user_id']);//群主ID
            // $is_backpack = isset($_REQUEST['is_backpack']) ? intval($_REQUEST['is_backpack']) : 0; //0不是背包的礼物，1是背包的礼物

            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id,
                true, true);

            if ($is_nospeaking) {
                $root['status'] = 0;
                $root['error'] = "被im全局禁言，不能发消息";
                api_ajax_return($root);
            }

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($room_id, array('id', 'user_id', 'group_id', 'prop_table', 'in_livepk', 'pk_ticket'));

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            $group_id = strim($video['group_id']); //群组ID

            $podcast_id = intval($video['user_id']); //送给谁，有群组ID(group_id)，除了红包外其它的都是送给：群主
            //子房间
            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                $child_room = new child_room();
                $parent_id = $child_room->parent_id($room_id);
                $p_video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
                if (empty($group_id)) {
                    $group_id = $p_video['group_id'];
                }
            }

            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            // PK yicket
            $root['is_backpack'] = 0;
            //主播,自己发送 弹幕 消息,不扣秀豆
            if ($podcast_id == $user_id) {
                // $ext = array();
                // $ext['type'] = 2; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                // $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                // $ext['num'] = 1;
                // $ext['prop_id'] = 0; //礼物id
                // //s$ext['animated_url'] = '';//动画播放url
                // $ext['icon'] = ''; //图片，是否要: 大中小格式？
                // //$ext['is_red_envelope'] = 0;//是否是：红包；1:红包
                // $ext['user_prop_id'] = 0; //红包时用到，抢红包的id
                // //$ext['show_num'] = 1;//显示连续送的礼物数量;
                // $fields = array('ticket', 'no_ticket', 'refund_ticket');
                // $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                // $ext['total_ticket'] = intval($user_info['ticket']) + intval($user_info['no_ticket']); //用户总的：秀票数
                // //直播间显示主播实际可提现秀票（客户定制，标准版保留此功能）
                // // $ext['total_pk_ticket'] = $video['in_livepk'] ? $video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                // // if (0) {
                // // $ext['total_ticket'] = $ext['total_ticket'] - intval($user_info['refund_ticket']);
                // // }
                // $ext['to_user_id'] = 0; //礼物接收人（主播）
                // $ext['fonts_color'] = ''; //字体颜色
                // $ext['desc'] = $msg; //弹幕消息;
                // $ext['desc2'] = $msg; //弹幕消息;

                // //消息发送者
                // $sender = array();
                // $user_info = $user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon', 'id', 'v_icon', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                // $sender['user_id'] = $user_id; //发送人昵称
                // $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                // $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                // $sender['user_level'] = $user_info['user_level']; //用户等级
                // $sender['v_icon'] = $user_info['v_icon']; //认证图标

                // $ext['sender'] = $sender;

                // //车行定制 ljz 身份对应的弹幕颜色(主播)
                // if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                //     $ext['v_identity'] = 3;
                //     $ext['v_identity_colour'] = "#FF0000"; //红色
                //     $ext['v_speak_num'] = 0;
                //     $ext['v_join_name'] = '';
                // }

                // #构造高级接口所需参数
                // $msg_content = array();
                // //创建array 所需元素
                // $msg_content_elem = array(
                //     'MsgType' => 'TIMCustomElem', //自定义类型
                //     'MsgContent' => array(
                //         'Data' => json_encode($ext),
                //         'Desc' => ''
                //         //  'Ext' => $ext,
                //         //  'Sound' => '',
                //     )
                // );

                // if (intval($m_config['has_dirty_words']) == 1) {
                //     //文档内容,用来过滤脏字
                //     $msg_text_elem = array(
                //         'MsgType' => 'TIMTextElem', //
                //         'MsgContent' => array(
                //             'Text' => $msg
                //         )
                //     );
                //     array_push($msg_content, $msg_text_elem, $msg_content_elem);
                // } else {
                //     //将创建的元素$msg_content_elem, 加入array $msg_content
                //     array_push($msg_content, $msg_content_elem);
                // }

                // if (isset($_REQUEST['is_debug'])) {
                //     $root['error'] = '';
                //     $root['status'] = 1;
                // } else {
                //     fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                //     $api = createTimAPI();
                //     $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                // }

                // if ($ret['ActionStatus'] == 'FAIL') {
                //     log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                //     if ($ret['ErrorCode'] == 80001) {
                //         $root['error'] = '该词已被禁用';
                //     } else {
                //         $root['error'] = $ret['ErrorInfo'] . ":" . $ret['ErrorCode'];
                //     }
                //     $root['status'] = 0;
                // } else {
                //     $root['error'] = '';
                //     $root['status'] = 0; //app端通过这个判断是否扣除秀豆，1为扣除
                // }
                $root['error'] = '主播不能发送全服喇叭';
                $root['status'] = 0; //app端通过这个判断是否扣除秀豆，1为扣除
            } else {

                //$sql = "select id from ".DB_PREFIX."video_forbid_send_msg where group_id='".$group_id."' and user_id = ".$user_id;
                //$has_forbid = $GLOBALS['db']->getOne($sql,true,true) > 0;

                $has_forbid = $video_redis->has_forbid_msg2($group_id, $user_id);
                if ($has_forbid) {
                    $root['error'] = "被禁言,不能发送消息";
                    $root['status'] = 0;
                } else {

                    //file_put_contents(APP_ROOT_PATH.'mapi/lib/msg.txt', $msg);

                    //$msg2 = unserialize(file_get_contents(APP_ROOT_PATH.'mapi/lib/msg2.txt'));
                    //$msg =$msg .'【'.base64_decode("8J+UkQ==").'】';

                    //子房间收益归主房间主播
                    $p_id = $podcast_id;
                    if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                        fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                        $child_room = new child_room();
                        $parent_id = $child_room->parent_id($room_id);
                        $p_video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
                        $p_id = $p_video['user_id'];
                    }

                    $prop = load_auto_cache("prop_id", array('id' => 1));

                    $total_diamonds = $prop['diamonds'];
                    $total_score = $prop['score'];
                    $total_ticket = $prop['ticket'];

                    $pInTrans = $GLOBALS['db']->StartTrans();
                    try {
                        $status = false;
                        $GLOBALS['db']->query('UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - 1 WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "1" AND num >= 1');
                        if ($GLOBALS['db']->affected_rows()) {
                            $root['is_backpack'] = 1;
                            $status = true;
                        } else {
                            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds - " . $total_diamonds . ",use_diamonds = use_diamonds + " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and diamonds >= " . $total_diamonds;
                            $GLOBALS['db']->query($sql);
                            if ($GLOBALS['db']->affected_rows()) {
                                $status = true;
                            }
                        }
                        if ($status) {
                            if ($total_ticket > 0) {
                                if (defined("robot_gifts") && robot_gifts == 1) {
                                    $roboter = $GLOBALS['db']->getOne("select roboter from " . DB_PREFIX . "user where roboter=1 and id=" . $user_id); //查询是否特殊权限用户
                                    if ($roboter) {
                                        //增加：不可提现秀票
                                        $sql = "update " . DB_PREFIX . "user set no_ticket = no_ticket + " . $total_ticket . " where id = " . $p_id;
                                        $GLOBALS['db']->query($sql);

                                    } else {
                                        //增加：用户秀票
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $p_id;
                                        $GLOBALS['db']->query($sql);
                                    }
                                } else {
                                    //增加：用户秀票
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $p_id;
                                    $GLOBALS['db']->query($sql);
                                }

                                //当前直播获得秀票数
                                $sql = "update " . DB_PREFIX . "video set vote_number = vote_number + " . $total_ticket . " where id =" . $room_id;
                                $GLOBALS['db']->query($sql);

                                //记录：用户秀票增加日志
                            }

                            $video_prop = array();
                            $video_prop['prop_id'] = 0;
                            $video_prop['prop_name'] = "'" . '全服消息' . "'";
                            $video_prop['is_red_envelope'] = 0;
                            $video_prop['total_score'] = $total_score;
                            $video_prop['total_diamonds'] = $total_diamonds;
                            $video_prop['total_ticket'] = intval($total_ticket);
                            $video_prop['from_user_id'] = $user_id;
                            $video_prop['to_user_id'] = $podcast_id;
                            $video_prop['create_time'] = NOW_TIME;
                            $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                            $video_prop['num'] = 1;
                            $video_prop['video_id'] = $room_id;
                            $video_prop['group_id'] = "'" . $group_id . "'";
                            $video_prop['msg'] = "'" . $msg . "'";

                            $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                            $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                            $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                            $video_prop['from_ip'] = "'" . get_client_ip() . "'";
                            $video_prop['is_rocket'] = 0;

                            //将礼物写入mysql表中
                            $field_arr = array(
                                'prop_id',
                                'prop_name',
                                'is_red_envelope',
                                'total_score',
                                'total_diamonds',
                                'total_ticket',
                                'from_user_id',
                                'to_user_id',
                                'create_time',
                                'create_date',
                                'num',
                                'video_id',
                                'group_id',
                                'msg',
                                'create_ym',
                                'create_d',
                                'create_w',
                                'from_ip',
                                'is_rocket'
                            );
                            $fields = implode(",", $field_arr);
                            $valus = implode(",", $video_prop);

                            $table = $video['prop_table'];
                            $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
                            if (!$table_info) {
                                $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
                            }
                            $sql = "insert into " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                            $GLOBALS['db']->query($sql);
                            $user_prop_id = $GLOBALS['db']->insert_id();

                            //写入总表
                            if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                                $table_all = createPropAllTable();
                                $sql = "INSERT INTO " . $table_all . " (" . $fields . ") VALUES (" . $valus . ")";
                                $GLOBALS['db']->query($sql);
                            }

                            //提交事务,不等 消息推送,防止锁太久
                            $GLOBALS['db']->Commit($pInTrans);
                            $pInTrans = false; //防止，下面异常时，还调用：Rollback

                            //子房间
                            if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
                                $child_room = new child_room();
                                $child_room->child_room_pop_msg($room_id, $video_prop, $fields, $user_id, $total_ticket);
                            }
                            if ($total_ticket > 0) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                                $videoCont_redis = new VideoContributionRedisService();
                                $videoCont_redis->insert_db($user_id, $podcast_id, $room_id, $total_ticket);
                            }

                            user_deal_to_reids(array($user_id, $podcast_id));

                            //更新用户等级
                            $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                            user_leverl_syn($user_info);

                            //分销功能 计算抽成
                            // if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1 && $total_ticket > 0) {
                            //     $this->distribution_calculate($user_id, $total_ticket);
                            // }
                            // $this->distribution($podcast_id, $room_id, $total_ticket);
                            $msg = '📣【全服喇叭】' . $user_info['nick_name'] . ':' . $msg;

                            //发送:礼物
                            $ext = array();
                            $ext['type'] = 2; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                            $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                            $ext['num'] = 1;
                            $ext['prop_id'] = 0; //礼物id
                            //s$ext['animated_url'] = '';//动画播放url
                            $ext['icon'] = ''; //图片，是否要: 大中小格式？
                            //$ext['is_red_envelope'] = 0;//是否是：红包；1:红包
                            $ext['user_prop_id'] = intval($user_prop_id); //红包时用到，抢红包的id
                            //$ext['show_num'] = 1;//显示连续送的礼物数量;
                            $fields = array('ticket', 'no_ticket');
                            $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                            $ext['total_ticket'] = $user_info['ticket'] + $user_info['no_ticket']; //用户总的：秀票数
                            // $ext['total_pk_ticket'] = $video['in_livepk'] ? $video['pk_ticket'] : 0; //用户直播间获得PK秀票数
                            //                            $ext['total_ticket'] = intval($user_redis->getOne_db($podcast_id, 'ticket'));//用户总的：秀票数
                            $ext['to_user_id'] = 0; //礼物接收人（主播）
                            $ext['fonts_color'] = ''; //字体颜色
                            $ext['desc'] = $msg; //弹幕消息;
                            $ext['desc2'] = $msg; //弹幕消息;

                            //消息发送者
                            $sender = array();
                            $user_info = $user_redis->getRow_db($user_id,
                                array('id', 'nick_name', 'v_icon', 'head_image', 'user_level', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                            $sender['user_id'] = $user_id; //发送人昵称
                            $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                            $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                            $sender['user_level'] = $user_info['user_level']; //用户等级

                            $ext['sender'] = $sender;

                            $all_success_flag = 1; //所有群组IM发送都成功的标志位,默认置1

                            #构造rest API请求包
                            $msg_content = array();
                            //创建$msg_content 所需元素
                            $msg_content_elem = array(
                                'MsgType' => 'TIMCustomElem', //定义类型为普通文本型
                                'MsgContent' => array(
                                    'Data' => json_encode($ext) //转为JSON字符串
                                )
                            );
                            // $m_config = load_auto_cache("m_config"); //初始化手机端配置
                            if (intval($m_config['has_dirty_words']) == 1) {
                                //文档内容,用来过滤脏字
                                $msg_text_elem = array(
                                    'MsgType' => 'TIMTextElem', //
                                    'MsgContent' => array(
                                        'Text' => $msg
                                    )
                                );
                                array_push($msg_content, $msg_text_elem, $msg_content_elem);
                            } else {
                                //将创建的元素$msg_content_elem, 加入array $msg_content
                                array_push($msg_content, $msg_content_elem);
                            }

                            //引入IM API文件
                            fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                            $tim_api = createTimAPI();

                            //获取所有的群组ID
                            $group_id_all = $GLOBALS['db']->getAll("SELECT id,group_id,live_in FROM " . DB_PREFIX . "video WHERE live_in = 1", true, true);
                            //向所有群组发送消息
                            $ret = array(); //存放发送返回信息
                            foreach ($group_id_all as $key => &$value) {
                                $ret[$key] = $tim_api->group_send_group_msg2($user_id, $value['group_id'], $msg_content);
                                if ($ret[$key]['ActionStatus'] == 'FAIL' && $ret[$key]['ErrorCode'] == 80001) {
                                    $root['error'] = '该词已被禁用';
                                    $root['status'] = 0;
                                    api_ajax_return($root);
                                    return;
                                }
                                $idx = 'group' . $key;
                                $root[$idx] = $value['group_id'];
                            }

                            //遍历群组发送情况，对其中发送失败的群组且错误码为10002的，自动重发一次
                            foreach ($ret as $_key => &$_value) {
                                if ($_value['ActionStatus'] == 'FAIL' && $_value['ErrorCode'] == 10002) {
                                    //10002 系统错误，请再次尝试或联系技术客服。
                                    log_err_file(array(__FILE__, __LINE__, __METHOD__, $_value));
                                    /*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
                                    $_value = $tim_api->group_send_group_msg2($user_id, $group_id_arr[$_key]['group_id'], $msg_content);
                                    $root['repeat_test'] = 1;
                                }
                            }
                            reset($ret);
                            //查看是否全部发送成功,对于没发送成功的情况进行回馈
                            foreach ($ret as $_key_ => &$_value_) {
                                //定义对应信息的存放键值
                                $err_info = 'error_notify' . $_key_;
                                $status_info = 'status_notify' . $_key_;
                                //出错的写入对应位置
                                if ($ret[$i]['ActionStatus'] == 'FAIL') {
                                    $root[$err_info] = $_value_['ErrorInfo'] . ":" . $_value_['ErrorCode'];
                                    $root[$status_info] = 0;
                                    $all_success_flag = 0;
                                }
                            }
                            if ($all_success_flag) {
                                $root['status'] = 1;
                            } else {
                                $root['error'] = '群发失败';
                                $root['status'] = 0;
                            }
                            // $ext = array();
                            // $ext['type'] = 2; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                            // $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                            // $ext['num'] = 1;
                            // $ext['prop_id'] = 0; //礼物id
                            // $ext['is_plus'] = 0;
                            // $ext['is_much'] = 0;
                            // $ext['app_plus_num'] = 0;
                            // $ext['is_animated'] = 0;
                            // $ext['anim_type'] = '';
                            // $ext['icon'] = ''; //图片，是否要: 大中小格式？
                            // $ext['user_prop_id'] = $user_prop_id; //红包时用到，抢红包的id
                            // $fields = array('ticket', 'no_ticket');
                            // $user_info = $user_redis->getRow_db($podcast_id, $fields); //用户总的：秀票数
                            // $ext['to_user_id'] = 0; //礼物接收人（主播）
                            // $ext['head_image'] = get_spec_image($user_redis->getOne_db($podcast_id, 'head_image')); //主播头像
                            // //消息发送者
                            // $sender = array();
                            // $user_info = $user_redis->getRow_db($user_id,
                            //     array('id', 'nick_name', 'v_icon', 'head_image', 'user_level', 'is_robot', 'is_authentication', 'luck_num', 'mobile', 'login_type'));
                            // $sender['user_id'] = $user_id; //发送人昵称
                            // $sender['nick_name'] = ($user_info['nick_name']); //发送人昵称
                            // $sender['head_image'] = get_spec_image($user_info['head_image']); //发送人头像
                            // $sender['user_level'] = $user_info['user_level']; //用户等级
                            // $desc = '📣【全服喇叭】' . $user_info['nick_name'] . ':' . $msg;
                            // fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/prop_notify.php');
                            // $ext['sender'] = $sender;

                            // $root['data'] = propNotify($sender, $ext, null, $desc); //执行全服通告
                        } else {
                            $GLOBALS['db']->Rollback($pInTrans);
                            $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                            $root['status'] = 0;
                        }

                    } catch (Exception $e) {
                        //异常回滚
                        $root['error'] = $e->getMessage();
                        $root['status'] = 0;

                        $GLOBALS['db']->Rollback($pInTrans);
                    }
                }
            }
        }
        api_ajax_return($root);
    }
    /**
     * 预生成好，红包随机队列
     * @param unknown_type $total_diamonds
     *
     * 0    1
     * 1    20
     * 2    3
     *
     */
    public function red_rand_list($total_diamonds)
    {

        $list = array();
        while ($total_diamonds > 0) {
            $diamonds = mt_rand(1, 20); //随机取：1至20中的一个数字

            if ($total_diamonds >= $diamonds) {
                $total_diamonds = $total_diamonds - $diamonds;
                $list[] = $diamonds;
            } else {
                if ($total_diamonds >= 1) {
                    $diamonds = 1;
                    $total_diamonds = $total_diamonds - $diamonds;
                    $list[] = $diamonds;
                }
            }
        }

        return $list;
    }

    /**
     * 把$total_diamonds 生成指定数量$num的，随机列表数
     * @param unknown_type $total_diamonds
     * @param unknown_type $num
     * @return multitype:number
     */
    public function red_rand_list2($total_diamonds, $num)
    {
        $list = array();
        if ($num > $total_diamonds) {
            $num = $total_diamonds;
        }

        //先生成一批为：1 的
        for ($x = 0; $x < $num; $x++) {
            $list[] = 1;
            $total_diamonds = $total_diamonds - 1;
        }

        while ($total_diamonds > 0) {
            foreach ($list as $k => $v) {
                $diamonds = mt_rand(1, 19); //随机取：1至20中的一个数字

                if ($total_diamonds >= $diamonds) {
                    $total_diamonds = $total_diamonds - $diamonds;
                } else {
                    if ($total_diamonds >= 1) {
                        $diamonds = 1;
                        $total_diamonds = $total_diamonds - $diamonds;
                    }
                }

                $list[$k] = $v + $diamonds;

                if ($total_diamonds == 0) {
                    break;
                }
            }
        };

        return $list;
    }

    /**
     * 抢红包
     */
    public function red_envelope()
    {

        $root = array();
        $root['status'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            $user_prop_id = intval($_REQUEST['user_prop_id']); //红包id

            $m_config = load_auto_cache("m_config"); //初始化手机端配置

            //============================redis================================================
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
            $videoRed_redis = new VideoRedRedisService();

            //判断该用户没有抢过;
            $ret = $videoRed_redis->get_user_winning($user_prop_id, $user_id);
            if ($ret == false) {
                //判断是否还有可以抢的红包
                if ($videoRed_redis->red_exists($user_prop_id)) {
                    $money = $videoRed_redis->pop_red($user_prop_id);
                    if ($money > 0) {
                        allot_red_to_user($user_prop_id, $user_id, $money);
                        $root['diamonds'] = $money;

                        $root['error'] = "恭喜您抢到" . $money . "个" . $m_config['diamonds_name'];
                    } else {
                        $root['status'] = 0;
                        $root['error'] = "手慢了，未捡到";
                    }
                } else {
                    $root['status'] = 0;
                    $root['error'] = "手慢了，未捡到！";
                }
            } else {
                $root['diamonds'] = $ret;
                $root['error'] = "恭喜您抢到" . $ret . "个" . $m_config['diamonds_name'];
            }
        }

        ajax_return($root);
    }

    /**
     * 抢红包---》看看大家的手气
     */
    public function user_red_envelope()
    {

        $root = array();
        $root['status'] = 1;
        //$GLOBALS['user_info']['id'] = 278;
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_prop_id = intval($_REQUEST['user_prop_id']); //红包id

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedRedisService.php');
            $videoRed_redis = new VideoRedRedisService();

            $list = $videoRed_redis->get_winnings($user_prop_id);
            foreach ($list as $k => $v) {
                $list[$k]['nick_name'] = ($v['nick_name']);
            }

            $root['status'] = 1;

            $root['list'] = $list;

        }
        ajax_return($root);
    }

    /**
     * 送礼物给某人
     */
    public function send_prop()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆." . print_r($_COOKIE, 1);
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            $prop_id = intval($_REQUEST['prop_id']); //礼物id
            $num = intval($_REQUEST['num']); //礼物数量
            $to_user_id = strim($_REQUEST['to_user_id']); //送给谁
            $is_backpack = isset($_REQUEST['is_backpack']) ? intval($_REQUEST['is_backpack']) : 0; //0不是背包的礼物，1是背包的礼物

            $coordinate = isset($_REQUEST['coordinate']) ? (json_decode($_REQUEST['coordinate'], true) ?: array()): array();

            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id,
                true, true);
            if ($is_nospeaking) {
                $root['status'] = 0;
                $root['error'] = "被im全局禁言，不能发礼物";
                api_ajax_return($root);
            }

            if ($user_id == $to_user_id) {
                $root['error'] = "不能发礼物给自己";
                $root['status'] = 0;
                api_ajax_return($root);
            }
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //检查测试账号不能发礼物给真实主播
            $sql = "select mobile from " . DB_PREFIX . "user where id = '" . $to_user_id . "'";
            $podcast_mobile = $GLOBALS['db']->getOne($sql);
            if (($GLOBALS['user_info']['mobile'] == '13888888888' && $podcast_mobile != '13999999999') || $GLOBALS['user_info']['mobile'] == '13999999999' && $podcast_mobile != '13888888888') {
                $root['error'] = "测试账号不能发礼物给真实主播";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            $prop = load_auto_cache("prop_id", array('id' => $prop_id));
            if ($prop['is_special'] == 1) {
                $root['error'] = "特殊道具不能直接赠送";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            if ($num <= 0 || ($prop['is_red_envelope'] == 1)) {
                $num = 1;
            }


            $total_diamonds = bcmul($num, $prop['diamonds']);
            $total_score = bcmul($num, $prop['score']);
            $total_ticket = bcmul($num, $prop['ticket']);
            $total_society_ticket = bcmul($num, $prop['society_ticket']);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            $pInTrans = $GLOBALS['db']->StartTrans();
            try {
                //免费礼物
                if ($total_diamonds == 0 && $total_score == 0 && $total_ticket == 0) {
                    if ($is_backpack == 1) {
                        $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                        $GLOBALS['db']->query($sql);
                        if ($GLOBALS['db']->affected_rows()) {
                            //提交事务,不等 消息推送,防止锁太久
                            $GLOBALS['db']->Commit($pInTrans);
                            $pInTrans = false; //防止，下面异常时，还调用：Rollback
                        } else {
                            $GLOBALS['db']->Rollback($pInTrans);
                            return array('status' => 0, 'error' => '用户背包道具不足');
                        }
                    }
                    $m_config = load_auto_cache("m_config"); //初始化手机端配置
                    $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_ticket . $m_config['ticket_name'] . ",可以去个人主页>我的收益 查看哦";
                    $to_diamonds = 0;
                    $to_ticket = intval($total_ticket);
                    $root['from_msg'] = "送给你{$num}个" . $prop['name'];
                    $root['from_score'] = "你的经验值+" . $total_score;
                    $root['to_ticket'] = intval($to_ticket);
                    $root['to_diamonds'] = $to_diamonds; //可获得的：秀豆数；只有红包时，才有
                    $root['to_user_id'] = $to_user_id;
                    $root['prop_icon'] = $prop['icon'];
                    $root['status'] = 1;
                    $root['prop_id'] = intval($prop_id);
                    $root['total_ticket'] = intval($user_redis->getOne_db($to_user_id, 'ticket')); //用户总的：秀票数
                } else {
                    //私聊送红包礼物没有经验
                    if ($prop['is_red_envelope'] == 1) {
                        $total_score = 0;
                    }
                    if ($is_backpack == 1) {
                        $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                    } else {
                        if ($is_coins == 0) {
                            //减少用户秀豆
                            $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds - " . $total_diamonds . ", use_diamonds = use_diamonds + " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and diamonds >= " . $total_diamonds;
                        } else {
                            //减少用户游戏币
                            $sql = "UPDATE " . DB_PREFIX . "user SET coin = coin - " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and coin >= " . $total_diamonds;
                        }
                    }
                    $GLOBALS['db']->query($sql);
                    if ($GLOBALS['db']->affected_rows()) {
                        //将红包的秀豆，直接加给被送用户
                        if ($prop['is_red_envelope'] == 1) {
                            //$desc = '我给大家送了一个红包';
                            //$user_redis->lock_diamonds($to_user_id,$total_diamonds);

                            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . $total_diamonds . " where id = " . $to_user_id;
                            $GLOBALS['db']->query($sql);

                            $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_diamonds . $m_config['diamonds_name'] . ",可以去个人主页 查看哦";

                            $to_diamonds = $total_diamonds; //用户添加的：秀豆 数;
                            $to_ticket = 0;
                        } else {
                            $m_config = load_auto_cache("m_config"); //初始化手机端配置
                            $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id = " . $to_user_id);
                            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {
                                if ($m_config['society_pattern'] == 3) {
                                    //收益归公会长所有
                                    if ($society_info['society_id']) {
                                        $this->one_soceity_rake($to_user_id, $total_ticket, $user_id, $total_society_ticket);
                                    } else {
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                        $GLOBALS['db']->query($sql);

                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }

                                } elseif ($m_config['society_pattern'] == 2) {
                                    //收益归公会长所有
                                    if ($society_info['society_id']) {
                                        $this->soceity_rake($to_user_id, $total_ticket, $user_id);
                                    } else {
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                        $GLOBALS['db']->query($sql);

                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 13, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }

                                } elseif ($m_config['society_pattern'] == 1) {
                                    //有公会需要增加公会等级积分
                                    if ($society_info['society_id']) {
                                        society_level_syn($total_ticket, 0, $society_info['society_id']);
                                    }
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                    $GLOBALS['db']->query($sql);

                                    if ($society_info['society_chieftain']) {
                                        $GLOBALS['db']->query("update " . DB_PREFIX . "society set contribution = contribution + $total_ticket where id=" . $society_info['society_id']);
                                        //写入会长日志
                                        earnings_log($total_ticket, $society_info['society_id'], $user_id, 13, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    } else {
                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 13, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }
                                } else {
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                    $GLOBALS['db']->query($sql);

                                    //写入用户日志
                                    earnings_log($total_ticket, $society_info['society_id'], 0, 13, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                                }
                            } else {
                                $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                $GLOBALS['db']->query($sql);

                                //写入用户日志
                                earnings_log($total_ticket, $society_info['society_id'], 0, 13, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                            }

                            $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_ticket . $m_config['ticket_name'] . ",可以去个人主页>我的收益 查看哦";
                            $to_diamonds = 0;
                            $to_ticket = intval($total_ticket);
                        }

                        $this->check_invite($user_id, $total_ticket, $prop_id);

                        //插入:送礼物表
                        $video_prop = array();
                        $video_prop['prop_id'] = intval($prop_id);
                        $video_prop['prop_name'] = "'" . $prop['name'] . "'";
                        $video_prop['is_red_envelope'] = $prop['is_red_envelope'];
                        $video_prop['total_score'] = $total_score;
                        $video_prop['total_diamonds'] = $total_diamonds;
                        if ($prop['is_red_envelope'] == 1) {
                            $video_prop['total_ticket'] = intval($total_diamonds);
                        } else {
                            $video_prop['total_ticket'] = intval($total_ticket);
                        }
                        $video_prop['from_user_id'] = $user_id;
                        $video_prop['to_user_id'] = $to_user_id;
                        $video_prop['create_time'] = NOW_TIME;
                        $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                        $video_prop['num'] = $num;

                        $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                        $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                        $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                        $video_prop['from_ip'] = "'" . get_client_ip() . "'";
                        $video_prop['is_private'] = 1;
                        $video_prop['is_rocket'] = $prop['is_rocket'];

                        //将礼物写入mysql表中
                        $field_arr = array('prop_id', 'prop_name', 'is_red_envelope', 'total_score', 'total_diamonds', 'total_ticket', 'from_user_id', 'to_user_id', 'create_time', 'create_date', 'num', 'create_ym', 'create_d', 'create_w', 'from_ip', 'is_private', 'is_rocket');
                        if (intval(OPEN_REWARD_GIFT)) {
                            if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                                $list_award = get_award_info();
                            }
                        }
                        if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                            $field_arr[] = 'is_award';
                            $video_prop['is_award'] = intval($prop['is_award']);
                        }
                        $fields = implode(",", $field_arr);
                        $valus = implode(",", $video_prop);

                        $table = createPropTable();
                        $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
                        if (!$table_info) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
                        }
                        $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                        $GLOBALS['db']->query($sql);
                        $user_prop_id = $GLOBALS['db']->insert_id();
                        //写入总表
                        if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                            $table_all = createPropAllTable();
                            $sql = "INSERT INTO " . $table_all . " (" . $fields . ") VALUES (" . $valus . ")";
                            $GLOBALS['db']->query($sql);
                        }
                        //提交事务
                        $GLOBALS['db']->Commit($pInTrans);
                        $pInTrans = false;

                        if ($prop['is_red_envelope'] == 0) {
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                            $videoCont_redis = new VideoContributionRedisService();
                            $videoCont_redis->insert_db($user_id, $to_user_id, 0, $total_ticket);
                        }

                        //分销功能 计算抽成
                        if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1 && $prop['is_red_envelope'] == 0 && $total_ticket > 0) {
                            $this->distribution_calculate($user_id, $total_ticket);
                        }
                        $this->distribution($to_user_id, 0, $total_ticket);

                        user_deal_to_reids(array($user_id, $to_user_id));

                        //更新用户等级
                        $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                        user_leverl_syn($user_info);

                        $root['from_msg'] = "送给你{$num}个" . $prop['name'];
                        $root['from_score'] = "你的经验值+" . $total_score;
                        $root['to_ticket'] = intval($to_ticket);
                        $root['to_diamonds'] = $to_diamonds; //可获得的：秀豆数；只有红包时，才有
                        $root['to_user_id'] = $to_user_id;
                        $root['prop_icon'] = $prop['icon'];
                        $root['status'] = 1;
                        $root['prop_id'] = intval($prop_id);
                        $root['total_ticket'] = intval($user_redis->getOne_db($to_user_id, 'ticket')); //用户总的：秀票数

                    } else {
                        $GLOBALS['db']->Rollback($pInTrans);
                        if ($is_backpack) {
                            $root['error'] = '用户背包道具不足';
                        } else if ($is_coins) {
                            $root['error'] = '用户游戏币不足';
                        } else {
                            $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                        }
                        $root['status'] = 0;
                    }
                }
                //减少用户秀豆

            } catch (Exception $e) {
                //异常回滚
                $root['error'] = $e->getMessage();
                $root['status'] = 0;

                $GLOBALS['db']->Rollback($pInTrans);
            }

        }
        api_ajax_return($root);
    }

    /**
     * [svideo_prop 小视频送礼物]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-09-13T15:22:32+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @return    [type] [description]
     */
    public function svideo_prop()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆." . print_r($_COOKIE, 1);
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            $prop_id = intval($_REQUEST['prop_id']); //礼物id
            $num = intval($_REQUEST['num']); //礼物数量
            // $to_user_id = strim($_REQUEST['to_user_id']); //送给谁
            $sv_id = intval($_REQUEST['sv_id']); // 小视频iD

            $is_backpack = isset($_REQUEST['is_backpack']) ? intval($_REQUEST['is_backpack']) : 0; //0不是背包的礼物，1是背包的礼物

            $coordinate = isset($_REQUEST['coordinate']) ? (json_decode($_REQUEST['coordinate'], true) ?: array()): array();

            if (empty($sv_id)) {
                ajax_return(array('status' => 0, 'error' => "小视频ID不能为空"));
            }
            // $GLOBALS['db']->query("UPDATE " . DB_PREFIX . "weibo SET video_count=video_count+1 WHERE id = $sv_id");
            $svideo = load_auto_cache('videosmall_select_weibo_info', array('weibo_id' => $sv_id));
            $to_user_id = !empty($_REQUEST['to_user_id']) ? intval($_REQUEST['to_user_id']) : $svideo['sv_userid'];
            if (empty($svideo)) {
                ajax_return(array('status' => 0, 'error' => "小视频不存在"));
            }
            $is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id,
                true, true);
            if ($is_nospeaking) {
                $root['status'] = 0;
                $root['error'] = "被im全局禁言，不能发礼物";
                api_ajax_return($root);
            }

            if ($user_id == $to_user_id) {
                $root['error'] = "不能发礼物给自己";
                $root['status'] = 0;
                api_ajax_return($root);
            }
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //检查测试账号不能发礼物给真实主播
            $sql = "select mobile from " . DB_PREFIX . "user where id = '" . $to_user_id . "'";
            $podcast_mobile = $GLOBALS['db']->getOne($sql);
            if (($GLOBALS['user_info']['mobile'] == '13888888888' && $podcast_mobile != '13999999999') || $GLOBALS['user_info']['mobile'] == '13999999999' && $podcast_mobile != '13888888888') {
                $root['error'] = "测试账号不能发礼物给真实主播";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            $prop = load_auto_cache("prop_id", array('id' => $prop_id));
            if ($prop['is_special'] == 1) {
                $root['error'] = "特殊道具不能直接赠送";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            if ($num <= 0 || ($prop['is_red_envelope'] == 1)) {
                $num = 1;
            }

            $total_diamonds = bcmul($num, $prop['diamonds']);
            $total_score = bcmul($num, $prop['score']);
            $total_ticket = bcmul($num, $prop['ticket']);
            $total_society_ticket = bcmul($num, $prop['society_ticket']);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            $pInTrans = $GLOBALS['db']->StartTrans();
            try {
                //免费礼物
                if ($total_diamonds == 0 && $total_score == 0 && $total_ticket == 0) {
                    if ($is_backpack == 1) {
                        $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                        $GLOBALS['db']->query($sql);
                        if ($GLOBALS['db']->affected_rows()) {
                            //提交事务,不等 消息推送,防止锁太久
                            $GLOBALS['db']->Commit($pInTrans);
                            $pInTrans = false; //防止，下面异常时，还调用：Rollback
                        } else {
                            $GLOBALS['db']->Rollback($pInTrans);
                            return array('status' => 0, 'error' => '用户背包道具不足');
                        }
                    }
                    $m_config = load_auto_cache("m_config"); //初始化手机端配置
                    $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_ticket . $m_config['ticket_name'] . ",可以去个人主页>我的收益 查看哦";
                    $to_diamonds = 0;
                    $to_ticket = intval($total_ticket);
                    $root['from_msg'] = "送给你{$num}个" . $prop['name'];
                    $root['from_score'] = "你的经验值+" . $total_score;
                    $root['to_ticket'] = intval($to_ticket);
                    $root['to_diamonds'] = $to_diamonds; //可获得的：秀豆数；只有红包时，才有
                    $root['to_user_id'] = $to_user_id;
                    $root['prop_icon'] = $prop['icon'];
                    $root['status'] = 1;
                    $root['prop_id'] = intval($prop_id);
                    $root['total_ticket'] = intval($user_redis->getOne_db($to_user_id, 'ticket')); //用户总的：秀票数
                } else {
                    //私聊送红包礼物没有经验
                    if ($prop['is_red_envelope'] == 1) {
                        $total_score = 0;
                    }
                    if ($is_backpack == 1) {
                        $sql = 'UPDATE ' . DB_PREFIX . 'prop_backpack SET `num` = num - ' . $num . ' WHERE `user_id` = "' . $user_id . '" AND `prop_id` = "' . $prop_id . '" AND num >=' . $num;
                    } else {
                        if ($is_coins == 0) {
                            //减少用户秀豆
                            $sql = "UPDATE " . DB_PREFIX . "user SET diamonds = diamonds - " . $total_diamonds . ", use_diamonds = use_diamonds + " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and diamonds >= " . $total_diamonds;
                        } else {
                            //减少用户游戏币
                            $sql = "UPDATE " . DB_PREFIX . "user SET coin = coin - " . $total_diamonds . ", score = score + " . $total_score . " where id = '" . $user_id . "' and coin >= " . $total_diamonds;
                        }
                    }
                    $GLOBALS['db']->query($sql);
                    if ($GLOBALS['db']->affected_rows()) {
                        //将红包的秀豆，直接加给被送用户
                        if ($prop['is_red_envelope'] == 1) {
                            //$desc = '我给大家送了一个红包';
                            //$user_redis->lock_diamonds($to_user_id,$total_diamonds);

                            $sql = "update " . DB_PREFIX . "user set diamonds = diamonds + " . $total_diamonds . " where id = " . $to_user_id;
                            $GLOBALS['db']->query($sql);

                            $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_diamonds . $m_config['diamonds_name'] . ",可以去个人主页 查看哦";

                            $to_diamonds = $total_diamonds; //用户添加的：秀豆 数;
                            $to_ticket = 0;
                        } else {
                            $m_config = load_auto_cache("m_config"); //初始化手机端配置
                            $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id = " . $to_user_id);
                            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {
                                if ($m_config['society_pattern'] == 3) {
                                    //收益归公会长所有
                                    if ($society_info['society_id']) {
                                        $this->one_soceity_rake($to_user_id, $total_ticket, $user_id, $total_society_ticket);
                                    } else {
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                        $GLOBALS['db']->query($sql);

                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }

                                } elseif ($m_config['society_pattern'] == 2) {
                                    //收益归公会长所有
                                    if ($society_info['society_id']) {
                                        $this->soceity_rake($to_user_id, $total_ticket, $user_id);
                                    } else {
                                        $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                        $GLOBALS['db']->query($sql);

                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }

                                } elseif ($m_config['society_pattern'] == 1) {
                                    //有公会需要增加公会等级积分
                                    if ($society_info['society_id']) {
                                        society_level_syn($total_ticket, 0, $society_info['society_id']);
                                    }
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                    $GLOBALS['db']->query($sql);

                                    if ($society_info['society_chieftain']) {
                                        $GLOBALS['db']->query("update " . DB_PREFIX . "society set contribution = contribution + $total_ticket where id=" . $society_info['society_id']);
                                        //写入会长日志
                                        earnings_log($total_ticket, $society_info['society_id'], $user_id, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    } else {
                                        //写入用户日志
                                        earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                    }
                                } else {
                                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                    $GLOBALS['db']->query($sql);

                                    //写入用户日志
                                    earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                                }
                            } else {
                                $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $to_user_id;
                                $GLOBALS['db']->query($sql);

                                //写入用户日志
                                earnings_log($total_ticket, $society_info['society_id'], 0, 14, '小视频获取收益:' . $total_ticket . '秀票', $to_user_id);
                            }

                            $root['to_msg'] = "收到{$num}个" . $prop['name'] . ",获得" . $total_ticket . $m_config['ticket_name'] . ",可以去个人主页>我的收益 查看哦";
                            $to_diamonds = 0;
                            $to_ticket = intval($total_ticket);
                        }

                        $this->check_invite($user_id, $total_ticket, $prop_id);

                        //插入:送礼物表
                        $video_prop = array();
                        $video_prop['prop_id'] = intval($prop_id);
                        $video_prop['prop_name'] = "'" . $prop['name'] . "'";
                        $video_prop['is_red_envelope'] = $prop['is_red_envelope'];
                        $video_prop['total_score'] = $total_score;
                        $video_prop['total_diamonds'] = $total_diamonds;
                        if ($prop['is_red_envelope'] == 1) {
                            $video_prop['total_ticket'] = intval($total_diamonds);
                        } else {
                            $video_prop['total_ticket'] = intval($total_ticket);
                        }
                        $video_prop['from_user_id'] = $user_id;
                        $video_prop['to_user_id'] = $to_user_id;
                        $video_prop['create_time'] = NOW_TIME;
                        $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                        $video_prop['num'] = $num;

                        $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                        $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                        $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');
                        $video_prop['from_ip'] = "'" . get_client_ip() . "'";
                        $video_prop['is_private'] = 1;
                        $video_prop['is_rocket'] = $prop['is_rocket'];

                        //将礼物写入mysql表中
                        $field_arr = array('prop_id', 'prop_name', 'is_red_envelope', 'total_score', 'total_diamonds', 'total_ticket', 'from_user_id', 'to_user_id', 'create_time', 'create_date', 'num', 'create_ym', 'create_d', 'create_w', 'from_ip', 'is_private', 'is_rocket');
                        if (intval(OPEN_REWARD_GIFT)) {
                            if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                                $list_award = get_award_info();
                            }
                        }
                        if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                            $field_arr[] = 'is_award';
                            $video_prop['is_award'] = intval($prop['is_award']);
                        }
                        $fields = implode(",", $field_arr);
                        $valus = implode(",", $video_prop);

                        $table = createPropTable();
                        $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
                        if (!$table_info) {
                            $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
                        }
                        $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                        $GLOBALS['db']->query($sql);
                        $user_prop_id = $GLOBALS['db']->insert_id();
                        //写入总表
                        if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                            $table_all = createPropAllTable();
                            $sql = "INSERT INTO " . $table_all . " (" . $fields . ") VALUES (" . $valus . ")";
                            $GLOBALS['db']->query($sql);
                        }
                        //提交事务
                        $GLOBALS['db']->Commit($pInTrans);
                        $pInTrans = false;

                        if ($prop['is_red_envelope'] == 0) {
                            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                            $videoCont_redis = new VideoContributionRedisService();
                            $videoCont_redis->insert_db($user_id, $to_user_id, 0, $total_ticket);
                        }

                        //分销功能 计算抽成
                        if (defined('OPEN_DISTRIBUTION') && OPEN_DISTRIBUTION == 1 && $prop['is_red_envelope'] == 0 && $total_ticket > 0) {
                            $this->distribution_calculate($user_id, $total_ticket);
                        }
                        $this->distribution($to_user_id, 0, $total_ticket);

                        user_deal_to_reids(array($user_id, $to_user_id));

                        //更新用户等级
                        $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                        user_leverl_syn($user_info);

                        $root['from_msg'] = "送给你{$num}个" . $prop['name'];
                        $root['from_score'] = "你的经验值+" . $total_score;
                        $root['to_ticket'] = intval($to_ticket);
                        $root['to_diamonds'] = $to_diamonds; //可获得的：秀豆数；只有红包时，才有
                        $root['to_user_id'] = $to_user_id;
                        $root['prop_icon'] = $prop['icon'];
                        $root['status'] = 1;
                        $root['prop_id'] = intval($prop_id);
                        $root['total_ticket'] = intval($user_redis->getOne_db($to_user_id, 'ticket')); //用户总的：秀票数
                    } else {
                        $GLOBALS['db']->Rollback($pInTrans);
                        if ($is_backpack) {
                            $root['error'] = '用户背包道具不足';
                        } else if ($is_coins) {
                            $root['error'] = '用户游戏币不足';
                        } else {
                            $root['error'] = "用户" . $m_config['diamonds_name'] . "不足";
                        }
                        $root['status'] = 0;
                    }
                }
                //减少用户秀豆
                $GLOBALS['db']->query('UPDATE `' . DB_PREFIX . 'weibo` SET `gift_count` = gift_count + ' . $num . ' WHERE `id` = ' . $sv_id);
            } catch (Exception $e) {
                //异常回滚
                $root['error'] = $e->getMessage();
                $root['status'] = 0;

                $GLOBALS['db']->Rollback($pInTrans);
            }

        }
        api_ajax_return($root);
    }

    /**
     * 游戏分销
     * @param $podcast_id
     * @param $video_id
     * @param $total_ticket
     */
    private function distribution($podcast_id, $video_id, $total_ticket)
    {
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = dirname(__FILE__);
        if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
            Model::build('game_distribution')->addLog($podcast_id, $video_id, 0, $total_ticket, '直播礼物分销', 1);
        }
        if (defined('WEIXIN_DISTRIBUTION') && WEIXIN_DISTRIBUTION) {
            Model::build('weixin_distribution_log')->addLog($podcast_id, $total_ticket, '直播礼物分销(微信)', 1);
        }
    }

    /**
     *  分销抽成
     * @param $user_id
     * @param $total_ticket
     */
    private function distribution_calculate($user_id, $total_ticket)
    {
        $root = array();
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $table = DB_PREFIX . 'distribution_log';
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $to_user_id = $user_redis->getOne_db($user_id, 'p_user_id'); //用户总的：秀票数
        $ticket = 0;
        $result = 0;
        if (intval($to_user_id) > 0 && intval($m_config['distribution']) == 1 && $user_id > 0 && $total_ticket > 0) {
            $ticket = round($m_config['distribution_rate'] * 0.01 * $total_ticket, 2);
            $sql = "select id from " . $table . " where to_user_id = " . $to_user_id . " and from_user_id = " . $user_id;
            $distribution_id = $GLOBALS['db']->getOne($sql);
            if (intval($distribution_id) > 0) {
                $sql = "update " . $table . " set ticket = ticket + " . $ticket . " where id = " . $distribution_id;
                $GLOBALS['db']->query($sql);
                $result = 1;
            } else {
                //插入:分销日志
                $video_prop = array();
                $video_prop['from_user_id'] = $user_id;
                $video_prop['to_user_id'] = $to_user_id;
                $video_prop['create_date'] = "'" . to_date(NOW_TIME, 'Y-m-d') . "'";
                $video_prop['ticket'] = $ticket;
                $video_prop['create_time'] = NOW_TIME;
                $video_prop['create_ym'] = to_date($video_prop['create_time'], 'Ym');
                $video_prop['create_d'] = to_date($video_prop['create_time'], 'd');
                $video_prop['create_w'] = to_date($video_prop['create_time'], 'W');

                //将日志写入mysql表中
                $field_arr = array(
                    'from_user_id',
                    'to_user_id',
                    'create_date',
                    'ticket',
                    'create_time',
                    'create_ym',
                    'create_d',
                    'create_w'
                );
                $fields = implode(",", $field_arr);
                $valus = implode(",", $video_prop);

                $sql = "insert into " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                $GLOBALS['db']->query($sql);
                $result = $GLOBALS['db']->insert_id();
            }
            if (intval($result) > 0) {
                $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $ticket . " where id = " . $to_user_id;
                $GLOBALS['db']->query($sql);
            }

        }
    }

    //-------------无抽成模式，所得私信收益归公会长所有---------------
    /**
     * @param int $to_user_id   被送礼物用户ID
     * @param int $total_ticket 获得的收益
     * @param int $user_id      用户id
     */
    public function soceity_rake($to_user_id, $total_ticket, $user_id)
    {
        //判断是否有公会
        $user_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $to_user_id);
        $society_info = $GLOBALS['db']->getRow("select user_id,status from " . DB_PREFIX . "society where id=" . $user_info['society_id']);
        if ($user_info['society_id'] > 0 && $society_info['status'] == 1) {
            //主播的收益加到公会中
            $pInTrans = $GLOBALS['db']->StartTrans();
            try
            {
                //加入公会收益
                $sql = "update " . DB_PREFIX . "society set chairman_earnings=chairman_earnings+" . $total_ticket . " where user_id=" . $society_info['user_id'];
                $GLOBALS['db']->query($sql);

                //排除公会长
                if (!$user_info['society_chieftain']) {
                    //将用户上交的公会的秀票计入已提现
                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . ",refund_ticket = refund_ticket + " . $total_ticket . ",society_ticket=society_ticket+" . $total_ticket . " where id = " . $to_user_id . " and ticket >=refund_ticket";
                    $status = $GLOBALS['db']->query($sql);

                    //将收益汇入公会长
                    $sql_chieftain = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . " where id = " . $society_info['user_id'];
                    $GLOBALS['db']->query($sql_chieftain);
                    user_deal_to_reids(array($society_info['user_id']));

                    //写入用户日志
                    earnings_log($total_ticket, $user_info['society_id'], 0, 10, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                    //写入会长日志
                    earnings_log($total_ticket, $user_info['society_id'], $to_user_id, 10, '主播' . $to_user_id . '私信获取收益,产生公会收益' . $total_ticket . '秀票', $society_info['user_id']);

                } else {
                    $sql = "update " . DB_PREFIX . "user set ticket = ticket + " . $total_ticket . ",society_ticket=society_ticket+" . $total_ticket . " where id = " . $to_user_id . " and ticket >=refund_ticket";
                    $status = $GLOBALS['db']->query($sql);
                    //写入用户日志
                    earnings_log($total_ticket, $user_info['society_id'], $user_id, 10, '私信获取收益:' . $total_ticket . '秀票', $society_info['user_id']);
                }

                //公会等级积分的写入
                society_level_syn($total_ticket, 0, $user_info['society_id']);
                //提交事务
                $GLOBALS['db']->Commit($pInTrans);
            } catch (Exception $e) {
                //异常回滚
                $GLOBALS['db']->Rollback($pInTrans);
                //写入会长日志
                earnings_log($total_ticket, $user_info['society_id'], 0, 10, '公会成员' . $to_user_id . '贡献收益,' . $total_ticket . '秀票' . '失败', $society_info['user_id']);
            }
            //-----事务结束-----
        }
        return;
    }
    // 独立模式
    public function one_soceity_rake($to_user_id, $total_ticket, $user_id, $total_society_ticket)
    {
        //判断是否有公会
        $user_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $to_user_id);
        $society_info = $GLOBALS['db']->getRow("select user_id,status from " . DB_PREFIX . "society where id=" . $user_info['society_id']);
        if ($user_info['society_id'] > 0 && $society_info['status'] == 1) {
            //主播的收益加到公会中
            $pInTrans = $GLOBALS['db']->StartTrans();
            try
            {
                //加入公会收益
                $sql = "update " . DB_PREFIX . "society set new_chairman_earnings=new_chairman_earnings+" . $total_society_ticket . " where user_id=" . $society_info['user_id'];
                $GLOBALS['db']->query($sql);

                //排除公会长
                if (!$user_info['society_chieftain']) {
                    //将收益汇入公会长
                    $sql_chieftain = "UPDATE " . DB_PREFIX . "user SET ticket = ticket + " . $total_society_ticket . " where id = " . $society_info['user_id'];
                    $GLOBALS['db']->query($sql_chieftain);
                    user_deal_to_reids(array($society_info['user_id']));
                    //写入用户日志
                    earnings_log($total_ticket, $user_info['society_id'], 0, 10, '私信获取收益:' . $total_ticket . '秀票', $to_user_id);
                    //写入会长日志
                    earnings_log($total_society_ticket, $user_info['society_id'], $to_user_id, 10, '主播' . $to_user_id . '私信获取收益,平台发放公会收益' . $total_society_ticket . '秀票', $society_info['user_id']);
                } else {
                    $sql = "UPDATE " . DB_PREFIX . "user SET ticket = ticket + " . $total_ticket . " WHERE id = " . $to_user_id . " and ticket >=refund_ticket";
                    $status = $GLOBALS['db']->query($sql);
                    //写入用户日志
                    earnings_log($total_ticket, $user_info['society_id'], $user_id, 10, '私信获取收益:' . $total_ticket . '秀票', $society_info['user_id']);
                }
                //公会等级积分的写入
                society_level_syn($total_society_ticket, 0, $user_info['society_id']);
                //提交事务
                $GLOBALS['db']->Commit($pInTrans);
            } catch (Exception $e) {
                //异常回滚
                $GLOBALS['db']->Rollback($pInTrans);
                //写入会长日志
                earnings_log($total_society_ticket, $user_info['society_id'], 0, 10, '公会成员' . $to_user_id . '贡献收益,,平台发放公会收益' . $total_society_ticket . '秀票' . '失败', $society_info['user_id']);
            }
            //-----事务结束-----
        }
        return;
    }
}
