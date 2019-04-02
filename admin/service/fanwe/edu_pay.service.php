<?php

class  edu_payService
{
    const AUTH_TYPE_TEACHER = 1;//教师认证类型
    const AUTH_TYPE_ORG = 2;//机构认证类型

    /*线下约课支付
     * $param['order_data']
     * $param['msg_title']
     * $param['class_user_id']
     * */
    public function offline_pay($param)
    {
        $pInTrans = $GLOBALS['db']->StartTrans();
        $order_data = $param['order_data'];
        $msg_title = $param['msg_title'];
        $class_user_id = intval($param['class_user_id']);
        try {
            $insert_data['user_id'] = intval($order_data['user_id']);
            $insert_data['class_id'] = intval($order_data['class_id']);
            $insert_data['pay'] = $order_data['pay'];
            $insert_data['mobile'] = strim($order_data['mobile']);
            $insert_data['contacts'] = strim($order_data['contacts']);
            $insert_data['create_time'] = NOW_TIME;

            $GLOBALS['db']->autoExecute(DB_PREFIX . 'edu_offline_order', $insert_data);
            $order_id = $GLOBALS['db']->insert_id();
            if ($order_id) {
                if ($insert_data['pay'] > 0) {
                    //扣秀豆
                    $pay_diamonds_status = $this->pay_diamonds(array(
                        'ext_id' => $order_id,
                        'pay_diamonds' => $insert_data['pay'],
                        'user_id' => $insert_data['user_id'],
                        'msg' => "线下课程'" . $msg_title . "'预约支付:" . $order_id,
                        'public_user_msg' => "会员" . $insert_data['user_id'] . "预约您的'" . $msg_title . "'成功:" . $order_id,
                        'public_user_id' => $class_user_id,
                        'type' => 2,
                    ));

                    if (!$pay_diamonds_status) {
                        $GLOBALS['db']->query("delete from " . DB_PREFIX . "edu_offline_order where id= " . $order_id . "");
                        return 0;
                    }
                }
                //更新机构售课数量
                $sql = "update " . DB_PREFIX . "edu_org set sale_count = sale_count +1  where user_id = " . $class_user_id . "";
                $GLOBALS['db']->query($sql);
                //更新售课数量
                $offline_sql = "update " . DB_PREFIX . "edu_class_offline set sale_count = sale_count +1  where id = " . $insert_data['class_id'] . "";
                $GLOBALS['db']->query($offline_sql);
            }

            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {

            $GLOBALS['db']->Rollback($pInTrans);
            $order_id = 0;
        }

        //更新redis中用户的 diamonds
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        user_deal_to_reids(array($insert_data['user_id']));
        //$account_diamonds = $user_redis->getOne_db($insert_data['user_id'], 'diamonds');

        return $order_id;

    }

    /*课程购买支付
     * $param['order_data']
     * $param['msg_title']
     * $param['authentication_type']
     *  $param['course_user_id']
     * */
    public function course_pay($param)
    {

        $pInTrans = $GLOBALS['db']->StartTrans();
        $order_data = $param['order_data'];
        $msg_title = $param['msg_title'];
        try {
            $insert_data['user_id'] = intval($order_data['user_id']);
            $insert_data['course_id'] = intval($order_data['course_id']);
            $insert_data['group_id'] = intval($order_data['group_id']);
            $insert_data['class_id'] = intval($order_data['class_id']);
            $insert_data['pay'] = $order_data['pay'];
            $insert_data['buy_type'] = intval($order_data['buy_type']);
            $insert_data['description'] = strim($order_data['description']);
            $insert_data['create_time'] = NOW_TIME;

            $GLOBALS['db']->autoExecute(DB_PREFIX . 'edu_class_order', $insert_data);
            $order_id = intval($GLOBALS['db']->insert_id());

            if ($order_id) {
                if ($insert_data['pay'] > 0) {
                    //扣秀豆
                    $pay_diamonds_status = $this->pay_diamonds(array(
                        'ext_id' => $order_id,
                        'pay_diamonds' => $insert_data['pay'],
                        'user_id' => $insert_data['user_id'],
                        'msg' => "购买课程'" . $msg_title . "'支付:" . $order_id,
                        'public_user_msg' => "会员" . $insert_data['user_id'] . "购买您的'" . $msg_title . "'成功:" . $order_id,
                        'public_user_id' => $param['course_user_id'],
                        'type' => 1,
                    ));

                    if (!$pay_diamonds_status) {
                        $GLOBALS['db']->query("delete from " . DB_PREFIX . "edu_class_order where id= " . $order_id . "");
                        return 0;
                    }
                }

                //buy_type:购买类型：1课程 2.完整视频 3课时
                if ($insert_data['buy_type'] == 1 || $insert_data['buy_type'] == 3) {

                    //更新课程售课数量
                    $sql = "update " . DB_PREFIX . "edu_courses set sale_count = sale_count +1  where id = " . $insert_data['course_id'] . "";
                    $GLOBALS['db']->query($sql);

                    //更新机构/教师售课数量
                    if ($insert_data['buy_type'] == 1) {
                        $class = $GLOBALS['db']->getRow("select sum(long_time) as all_long_time,count(id) as class_count from " . DB_PREFIX . "edu_class where course_id= " . $insert_data['course_id'] . "");
                        $all_long_time = $class['all_long_time'];
                        $class_count = $class['class_count'];
                    } else {
                        $all_long_time = $GLOBALS['db']->getOne("select long_time from " . DB_PREFIX . "edu_class where course_id= " . $insert_data['course_id'] . " and id=" . $insert_data['class_id'] . "");
                        $class_count = 1;
                    }

                    if ($param['authentication_type'] == self::AUTH_TYPE_TEACHER) {
                        //教师
                        $sql = "update " . DB_PREFIX . "edu_teacher set sale_count = sale_count +" . intval($class_count) . ",teaching_time_count = teaching_time_count+" . intval($all_long_time) . "  where user_id = " . $param['course_user_id'] . "";
                        $GLOBALS['db']->query($sql);
                    } elseif ($param['authentication_type'] == self::AUTH_TYPE_ORG) {
                        //机构
                        $sql = "update " . DB_PREFIX . "edu_org set sale_count = sale_count +" . intval($class_count) . ",teaching_time_count = teaching_time_count+" . intval($all_long_time) . "  where user_id = " . $param['course_user_id'] . "";
                        $GLOBALS['db']->query($sql);
                    }
                }
            }

            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {
            $GLOBALS['db']->Rollback($pInTrans);
            $order_id = 0;
        }

        //更新redis中用户的 diamonds
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        user_deal_to_reids(array($insert_data['user_id']));
        //$account_diamonds = $user_redis->getOne_db($insert_data['user_id'], 'diamonds');
        return $order_id;
    }

    /*线下约课支付
 * $param['order_data']
 * $param['msg_title']
 * $param['class_user_id']
 * */
    public function online_pay($param)
    {
        $pInTrans = $GLOBALS['db']->StartTrans();
        $order_data = $param['order_data'];
        $msg_title = $param['msg_title'];
        $class_user_id = intval($param['class_user_id']);
        try {

            $insert_data['user_id'] = intval($order_data['user_id']);
            $insert_data['class_id'] = intval($order_data['class_id']);
            $insert_data['pay'] = $order_data['pay'];
            $insert_data['create_time'] = NOW_TIME;

            //先更新一对一约课课程状态为已预约
            $sql = "update " . DB_PREFIX . "edu_class_booking set is_booked = 1, booked_user_id=" . $insert_data['user_id'] . " where id = " . $order_data['class_id'] . " and is_booked=0";
            $GLOBALS['db']->query($sql);
            if (!$GLOBALS['db']->affected_rows()) {
                return 0;
            }

            //插入订单
            $GLOBALS['db']->autoExecute(DB_PREFIX . 'edu_booking_order', $insert_data);
            $order_id = $GLOBALS['db']->insert_id();
            if ($order_id) {
                if ($insert_data['pay']) {
                    //扣秀豆
                    $pay_diamonds_status = $this->pay_diamonds(array(
                        'ext_id' => $order_id,
                        'pay_diamonds' => $insert_data['pay'],
                        'user_id' => $insert_data['user_id'],
                        'msg' => "一对一约课'" . $msg_title . "'预约支付:" . $order_id,
                        'public_user_msg' => "会员" . $insert_data['user_id'] . "预约您的'" . $msg_title . "'成功:" . $order_id,
                        'public_user_id' => $class_user_id,
                        'type' => 3,

                    ));

                    if (!$pay_diamonds_status) {
                        //删除订单
                        $GLOBALS['db']->query("delete from " . DB_PREFIX . "edu_booking_order where id= " . $order_id . "");
                        //更新一对一约课课程状态为成未预约
                        $GLOBALS['db']->query("update " . DB_PREFIX . "edu_class_booking set is_booked = 0 and booked_user_id=0 where id = " . $order_data['class_id'] . " ");
                        return 0;
                    }
                }

                //更新教师售课数量
                $sql = "update " . DB_PREFIX . "edu_teacher set sale_count = sale_count +1  where user_id = " . $class_user_id . "";
                $GLOBALS['db']->query($sql);
            }

            $GLOBALS['db']->Commit($pInTrans);
        } catch (Exception $e) {

            $GLOBALS['db']->Rollback($pInTrans);
            $order_id = 0;
        }

        //更新redis中用户的 diamonds
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        user_deal_to_reids(array($insert_data['user_id']));
        //$account_diamonds = $user_redis->getOne_db($insert_data['user_id'], 'diamonds');

        return $order_id;

    }

    public function pay_diamonds($param)
    {
        $pay_diamonds = intval($param['pay_diamonds']);
        $user_id = intval($param['user_id']);
        $type = intval($param['type']);//1课程购买,2线下约课,3线上约课
        $ext_id = intval($param['ext_id']);
        $msg = strim($param['msg']);
        $public_user_msg = strim($param['public_user_msg']);

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
                'type' => $type,//1课堂购买   2线下预约   3线上约课
            );

            $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_user_diamonds_log", $diamonds_log_data);
            //写入用户日志
            $data = array();
            $data['diamonds'] = $pay_diamonds;
            $param['type'] = 20;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 20教育
            $log_msg = $msg;//备注
            account_log_com($data, $user_id, $log_msg, $param);

            //增加发布课程会员秀票数
            $ticket = $pay_diamonds;
            $public_user_id = intval($param['public_user_id']);
            $GLOBALS['db']->query("update " . DB_PREFIX . "user set ticket = ticket + " . $ticket . "  where id = " . $public_user_id . " ");

            //发布课程会员账户 秀票变更日志表
            $ticket_log_data = array(
                'user_id' => $public_user_id,
                'ext_id' => $ext_id,
                'ticket' => 0,//变更秀票数
                'memo' => $public_user_msg,//备注
                'create_time' => $time,
                'create_date' => to_date($time, 'Y-m-d H:i:s'),
                'create_time_ymd' => to_date($time, 'Y-m-d'),
                'create_time_y' => to_date($time, 'Y'),
                'create_time_m' => to_date($time, 'm'),
                'create_time_d' => to_date($time, 'd'),
                'type' => $type,//1课堂购买   2线下预约   3线上约课
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_user_diamonds_log", $ticket_log_data);

            //写入用户日志
            $data = array();
            $data['ticket'] = $ticket;
            $param['type'] = 20;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 20教育
            $log_msg = $public_user_msg;//备注
            account_log_com($data, $public_user_id, $log_msg, $param);

            $status = 1;
        } else {
            $status = 0;
        }

        return $status;
    }
}
