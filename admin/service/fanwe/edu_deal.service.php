<?php

class  edu_dealService
{
    public function get_deal($param)
    {
        $deal_id = intval($param['id']);
        $user_id = intval($param['user_id']);

        $sql = "select d.id,d.name,d.image,d.user_id,d.tags,d.price,d.limit_num,d.support_count" .
            ",d.begin_time,d.end_time,d.video_begin_time,d.is_success,d.description,d.is_effect,d.deal_status" .
            ",d.specialty,d.address,u.nick_name as teacher,u.head_image,u.introduce_video,u.desc_image" .
            " from " . DB_PREFIX . "edu_deal as d " .
            " left join " . DB_PREFIX . "user as u on u.id =d.user_id" .
            " where d.is_delete = 0 and d.id= " . $deal_id;

        $deal = $GLOBALS['db']->getRow($sql);

        if ($deal) {
            $end_time = to_timespan($deal['end_time']) + 24 * 60 * 60;
            $begin_time = to_timespan($deal['begin_time']);
            $deal = $this->get_deal_common($deal);
            $deal['end_time'] = to_date(to_timespan($deal['end_time']) + 24 * 60 * 60, 'Y年n月j日');

            if ($user_id && $user_id != $deal['user_id']) {
                $order_num = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "edu_deal_order where deal_id= " . $deal_id . " and user_id=" . $user_id . " and order_status=1"));
                $deal['is_join'] = $order_num > 0 ? 1 : 0;
            } else {
                $deal['is_join'] = 0;
            }

            if ($user_id && $user_id == $deal['user_id']) {
                if ($deal['deal_status'] == 0) {
                    $deal['d_status_name'] = "待提交";
                } elseif ($deal['deal_status'] == 2) {
                    $deal['d_status_name'] = "审核中";
                } elseif ($deal['deal_status'] == 3) {
                    $deal['d_status_name'] = "未通过";
                } elseif ($deal['deal_status'] == 1 && $deal['is_effect'] == 0) {
                    $deal['d_status_name'] = "未上架";
                } elseif ($begin_time > NOW_TIME) {
                    $deal['d_status_name'] = "未开始";
                } elseif ($begin_time <= NOW_TIME && $end_time >= NOW_TIME && $deal['is_success'] == 0) {
                    $deal['d_status_name'] = "众筹中";
                } elseif ($begin_time <= NOW_TIME && $end_time < NOW_TIME && $deal['is_success'] == 0) {
                    $deal['d_status_name'] = "已失败";
                } elseif ($deal['is_success'] == 1) {
                    $deal['d_status_name'] = "已成功";
                }
            }

            $orders = $GLOBALS['db']->getAll("select user_id, create_time from " . DB_PREFIX . "edu_deal_order where deal_id = {$deal_id} and order_status = 1 order by create_time desc");
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $deal['students'] = $user_redis->get_m_user(array_column($orders, 'user_id'));

            fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
            $user_redis = new UserFollwRedisService($user_id);
            $keys = $user_redis->following();
            foreach ($deal['students'] as &$v) {
                if (in_array($v['user_id'], $keys)) {
                    $v['follow_id'] = $v['user_id'];
                } else {
                    $v['follow_id'] = 0;
                }
            }
            unset($v);
        }

        return $deal;
    }

    public function get_deal_common($param)
    {
        $deal = $param;

        if (defined('ORDER_ZC') && ORDER_ZC == 1) {
            $image = json_decode($deal['image'],true);
            if (is_array($image) && count($image) > 0) {
                foreach ($image as &$value) {
                    $value = get_spec_image($value, 710, 300);
                }
            }else{
                if(!is_json($deal['image']) && !empty($deal['image'])){
                    $image = array(get_spec_image($deal['image']));
                }else{
                    $image = array();
                }
            }
            $deal['image'] = $image;
            $deal['introduce_video'] = replace_public($deal['introduce_video']);
            $deal['desc_image'] = get_spec_image($deal['desc_image']);
        } else {
            if(is_json($deal['image'])) {
                $deal['image'] = json_decode($deal['image'], true);
                if (is_array($deal['image']) && count($deal['image']) > 0) {
                    foreach ($deal['image'] as &$value) {
                        $value = get_spec_image($value, 710, 300);
                    }
                } else {
                    if (!empty($deal['image'])) {
                        $deal['image'] = array(get_spec_image($deal['image']));
                    } else {
                        $deal['image'] = null;
                    }
                }
            }else{
                $deal['image'] = get_spec_image($deal['image'], 710, 300);
            }

        }

        $deal['head_image'] = get_spec_image($deal['head_image']);
        if ($deal['tags'] != '') {
            $deal['tags'] = explode(',', $deal['tags']);
        } else {
            $deal['tags'] = array();
        }

        //百分数
        $deal['percent'] = intval($deal['support_count'] / $deal['limit_num'] * 100);

        //状态，剩余时间
        $end_time = to_timespan($deal['end_time']) + 24 * 60 * 60;
        $begin_time = to_timespan($deal['begin_time']);
        $deal['d_status'] = 0;
        $deal['remaining_time'] = '0天';
        $deal['end_time_percent'] = 0;
        if ($begin_time <= NOW_TIME && $end_time >= NOW_TIME && $deal['is_success'] == 0 && $deal['deal_status'] == 1) {
            $deal['d_status'] = 1;//众筹中
            $deal['d_status_name'] = '众筹中';

            //剩余天数
            $remaining_time_s = $end_time - NOW_TIME;
            $remaining_day = ceil($remaining_time_s / 86400);
            $deal['remaining_time'] = $remaining_day . "天";

            //剩余时间百分比
            $deal['end_time_percent'] = intval(($end_time - NOW_TIME) / ($end_time - $begin_time) * 100);
        } elseif (($deal['is_success'] == 1 || ($end_time < NOW_TIME && $deal['is_success'] == 0) && $deal['deal_status'] == 1)) {
            $deal['remaining_time'] = '0天';
            $deal['end_time_percent'] = 100;
            $deal['d_status'] = 2;//已结束
            $deal['d_status_name'] = '已结束';
        }

        return $deal;
    }

    /*下单
     * $param['order_data']
     * $param['msg_title']
     * */
    public function order_pay($param)
    {
        $pInTrans = $GLOBALS['db']->StartTrans();//开始事务
        try {
            $msg_title = $param['msg_title'];
            $order_data = $param['order_data'];

            $insert_data['deal_id'] = intval($order_data['deal_id']);
            $insert_data['user_id'] = intval($order_data['user_id']);
            $insert_data['pay'] = intval($order_data['deal_price']);
            $insert_data['deal_price'] = intval($order_data['deal_price']);
            $insert_data['order_status'] = 0;
            $insert_data['create_time'] = NOW_TIME;
            $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_deal_order", $insert_data);
            $order_id = $GLOBALS['db']->insert_id();

            if (!$order_id) {
                return 0;
            }

            //订单处理插入成功，开始处理订单

            //先更新支持数量
            $GLOBALS['db']->query("update " . DB_PREFIX . "edu_deal set support_count=support_count+1 where support_count<limit_num and id=" . intval($insert_data['deal_id']));
            $re = intval($GLOBALS['db']->affected_rows());

            //支持数量增加失败
            if (!$re) {
                //删除订单，返回0
                $GLOBALS['db']->query("delete from " . DB_PREFIX . "edu_deal_order where id= " . $order_id . "");
                return 0;
            }

            //支持数量增加成功，有库存，扣除会员秀豆
            if ($insert_data['pay']) {
                //扣秀豆
                $pay_diamonds_status = $this->pay_diamonds(array(
                    'ext_id' => $order_id,
                    'pay_diamonds' => $insert_data['pay'],
                    'user_id' => $insert_data['user_id'],
                    'msg' => "众筹直播课'" . $msg_tite . "'购买支付:" . $order_id,
                ));

                if (!$pay_diamonds_status) {
                    //删除订单
                    $GLOBALS['db']->query("delete from " . DB_PREFIX . "edu_deal_order where id= " . $order_id . "");
                    //支持数量减一
                    $GLOBALS['db']->query("update " . DB_PREFIX . "edu_deal set support_count=support_count-1 where where id=" . intval($insert_data['deal_id']));
                    return 0;
                }
            }

            //扣除会员秀豆成功，更新订单状态
            $GLOBALS['db']->query("update " . DB_PREFIX . "edu_deal_order set order_status=1,pay_time=" . NOW_TIME . " where id= " . $order_id . " and order_status=0");

            //更新众筹项目状态
            $this->syn_deal_status($insert_data['deal_id']);

            //更新众筹项目信息
            $this->syn_deal($insert_data['deal_id']);

            $GLOBALS['db']->Commit($pInTrans);//事务确认
        } catch (Exception $e) {

            $GLOBALS['db']->Rollback($pInTrans); //事务回滚
            $order_id = 0;
        }

        //更新redis中用户的 diamonds
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        user_deal_to_reids(array($insert_data['user_id']));

        return $order_id;
    }

    public function pay_diamonds($param)
    {
        $pay_diamonds = intval($param['pay_diamonds']);
        $user_id = intval($param['user_id']);
        $ext_id = intval($param['ext_id']);
        $msg = strim($param['msg']);

        $time = NOW_TIME;

        //减少用户秀豆
        $sql = "update " . DB_PREFIX . "user set diamonds = diamonds - " . $pay_diamonds . ",use_diamonds = use_diamonds + " . $pay_diamonds . "  where id = '" . $user_id . "' and diamonds >= " . $pay_diamonds;
        $GLOBALS['db']->query($sql);
        if ($GLOBALS['db']->affected_rows()) {
            $account_diamonds = $GLOBALS['db']->getOne("select diamonds from " . DB_PREFIX . "user where id= " . $user_id . " ");
            //会员账户 秀豆变更日志表
            $diamonds_log_data = array(
                'user_id' => $user_id,
                'ext_id' => $ext_id,
                'diamonds' => $pay_diamonds,//变更数额
                'account_diamonds' => $account_diamonds,//账户余额
                'memo' => $msg,//备注
                'create_time' => $time,
                'create_date' => to_date($time, 'Y-m-d H:i:s'),
                'create_time_ymd' => to_date($time, 'Y-m-d'),
                'create_time_y' => to_date($time, 'Y'),
                'create_time_m' => to_date($time, 'm'),
                'create_time_d' => to_date($time, 'd'),
                'type' => 5,//1课堂购买 2线下预约 3线上约课 4红包兑换 5众筹直播支持　
            );

            $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_user_diamonds_log", $diamonds_log_data);
            //写入用户日志
            $data = array();
            $data['diamonds'] = $pay_diamonds;
            $param['type'] = 20;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 20教育
            $log_msg = $msg;//备注
            account_log_com($data, $user_id, $log_msg, $param);

            $status = 1;
        } else {
            $status = 0;
        }

        return $status;
    }

    public function syn_deal_status($deal_id)
    {
        $deal_info = $GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "edu_deal where id=$deal_id");
        $GLOBALS['db']->query("update " . DB_PREFIX . "edu_deal set is_success = 1,success_time = " . get_gmtime() . " where id = " . $deal_id . " and is_effect= 1 and is_delete = 0 and support_count >= limit_num and is_success<>1");
    }

    function syn_deal($deal_id)
    {
        $deal_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "edu_deal where id = " . $deal_id);

        if ($deal_info) {
            $deal_info['support_count'] = intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "edu_deal_order where deal_id = " . $deal_info['id'] . " and order_status=1 and is_refund=0"));
            $deal_info['support_amount'] = $GLOBALS['db']->getOne("select sum(deal_price) as total_num from " . DB_PREFIX . "edu_deal_order where deal_id = " . $deal_info['id'] . " and order_status=1 and is_refund=0");

            if ($deal_info['pay_radio'] > 0) {
                $deal_info['pay_amount'] = $deal_info['support_amount'] * (1 - $deal_info['pay_radio']);
            } else {
                $deal_info['pay_amount'] = ($deal_info['support_amount'] * (1 - app_conf("PAY_RADIO")));
            }

            if ($deal_info['support_count'] >= $deal_info['limit_num']) {
                $deal_info['is_success'] = 1;
            } else {
                $deal_info['is_success'] = 0;
            }

            if ($deal_info['is_success'] == 1) {
                $paid_money = $GLOBALS['db']->getOne("select sum(money) from " . DB_PREFIX . "edu_deal_pay_log where deal_id=" . $deal_id);
                $deal_info['left_money'] = $deal_info['pay_amount'] - floatval($paid_money);
            }

            $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_deal", $deal_info, $mode = 'UPDATE', "id=" . $deal_info['id'],
                $querymode = 'SILENT');
        }

    }

    //众筹直播的直播列表
    public function get_deal_video($param)
    {
        $deal_id = intval($param['deal_id']);
        $user_id = intval($param['user_id']);
        $where = "ev.deal_id=" . $deal_id . " ";
        if ($user_id > 0) {
            $where .= " and v.user_id=" . $user_id . "";
        }
        //video表中记录
        $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.create_type, v.room_type,
				(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, u.head_image,u.thumb_head_image,v.live_image, v.xpoint,v.ypoint,ev.tags,
				ev.is_verify,ev.deal_id,ev.edu_cate_id,ev.booking_class_id,u.v_type, u.v_icon, u.nick_name,u.user_level,v.is_live_pay,v.live_pay_type,v.live_fee,
				u.create_time as user_create_time FROM " . DB_PREFIX . "video v LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id  LEFT JOIN " . DB_PREFIX . "edu_video_info as ev ON ev.video_id = v.id 
				where " . $where . " order by v.id desc";
        $list = $GLOBALS['db']->getAll($sql, true, true);
        //video_history表中的记录
        $sql2 = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.create_type, v.room_type,
				(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, u.head_image,u.thumb_head_image,v.live_image, v.xpoint,v.ypoint,ev.tags,
				ev.is_verify,ev.deal_id,ev.edu_cate_id,ev.booking_class_id,u.v_type, u.v_icon, u.nick_name,u.user_level,v.is_live_pay,v.live_pay_type,v.live_fee,
				u.create_time as user_create_time FROM " . DB_PREFIX . "video_history v LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id  LEFT JOIN " . DB_PREFIX . "edu_video_info as ev ON ev.video_id = v.id 
				where " . $where . " and v.group_id!='' and  v.is_delete = 0 and v.is_del_vod = 0 order by v.id desc";
        $list2 = $GLOBALS['db']->getAll($sql2, true, true);

        $list = array_merge($list, $list2);
        foreach ($list as $k => $v) {
            //判断用户是否为今日创建的新用户，是：1，否：0
            if (date('Y-m-d') == date('Y-m-d', $list[$k]['user_create_time'] + 3600 * 8)) {
                $list[$k]['today_create'] = 1;
            } else {
                $list[$k]['today_create'] = 0;
            }

            if ($v['live_image'] == '') {
                $list[$k]['live_image'] = get_spec_image($v['head_image']);
                $list[$k]['head_image'] = get_spec_image($v['head_image']);
            } else {
                $list[$k]['live_image'] = get_spec_image($v['live_image']);
                $list[$k]['head_image'] = get_spec_image($v['head_image'], 150, 150);
            }
            if ($v['thumb_head_image'] == '') {
                $list[$k]['thumb_head_image'] = get_spec_image($v['head_image'], 150, 150);
            } else {
                //$list[$k]['thumb_head_image'] = get_abs_img_root($v['thumb_head_image']);
                $list[$k]['thumb_head_image'] = get_spec_image($v['thumb_head_image'], 150, 150);
            }

            if (!$v['tags']) {
                $list[$k]['tags'] = array();
            } else {
                $list[$k]['tags'] = explode(',', $v['tags']);
            }

            $list[$k]['is_verify'] = intval($v['is_verify']);
            $list[$k]['deal_id'] = intval($v['deal_id']);
            $list[$k]['edu_cate_id'] = intval($v['edu_cate_id']);

        }

        return $list;
    }

    //判断是否序列化
    function is_serialized( $data ) {
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
            return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                    return true;
                break;
        }
        return false;
    }
}
