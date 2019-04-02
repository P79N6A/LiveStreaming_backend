<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/pay.action.php');

class payCModule extends payModule
{
    /**
     * 用户支付
     */
//    public function h5_pay()
    //    {
    //
    //        /*$user_id = intval($GLOBALS['user_info']['id']);
    //        if ($user_id == 0) {
    //            $root['status'] = 10007;
    //            $root['error']  = "请先登录";
    //            api_ajax_return($root);
    //        }*/
    //
    //        $diamond  = 0;
    //        $purchase_type = intval($_REQUEST['purchase_type']); //0表示买给自己、1表示买给主播
    //        $order_id = intval($_REQUEST['order_id']); //订单id
    //
    //        if ($order_id) {
    //            $table = '`' . DB_PREFIX . 'goods_order`';
    //            $field = '`pai_id`,`order_status`,`total_diamonds`,`order_sn`,`viewer_id`';
    //            //$where = "`id`= $order_id and `viewer_id` = $user_id";
    //            $where = "`id`= $order_id ";
    //            $sql   = "SELECT $field FROM $table WHERE $where";
    //            $order = $GLOBALS['db']->getRow($sql);
    //            // 1:待付款 2:待发货 3:待收货(主播确认约会)  4:已收货(观众确认约会) 5:退款成功 6未付款 7结单
    //            if ($order['order_status'] != 1) {
    //                api_ajax_return(array(
    //                    'status' => 0,
    //                    'error'  => '订单信息错误',
    //                ));
    //            }
    //            $diamond = floatval($order['total_diamonds']);
    //        }
    //
    //        $user_id=intval($order['viewer_id']);
    //        if (!$diamond) {
    //            api_ajax_return(array(
    //                'status' => 0,
    //                'error'  => '订单金额错误',
    //            ));
    //        }
    //
    //        $table = '`' . DB_PREFIX . 'user`';
    //        $sql   = "UPDATE $table SET `diamonds` = `diamonds` - $diamond  WHERE `id` = $user_id AND `diamonds` >= $diamond";
    //        $GLOBALS['db']->query($sql);
    //        if (!$GLOBALS['db']->affected_rows()) {
    //            api_ajax_return(array(
    //                'status' => 10062,
    //                'error'  => '金额不足,请先充值',
    //            ));
    //        }
    //        user_deal_to_reids(array($user_id));
    //
    //        $time              = NOW_TIME;
    //        $user_redis        = new UserRedisService();
    //        $account_diamonds  = $user_redis->getOne_db(intval($user_id), 'diamonds');
    //        $log_msg           = 'H5支付' . $diamond . '秀豆';
    //        $diamonds_log_data = array(
    //            'pai_id'           => 0,
    //            'user_id'          => $user_id,
    //            'diamonds'         => $diamond,
    //            'account_diamonds' => $account_diamonds,
    //            'memo'             => $log_msg,
    //            'create_time'      => $time,
    //            'create_date'      => to_date($time, 'Y-m-d H:i:s'),
    //            'create_time_ymd'  => to_date($time, 'Y-m-d'),
    //            'create_time_y'    => to_date($time, 'Y'),
    //            'create_time_m'    => to_date($time, 'm'),
    //            'create_time_d'    => to_date($time, 'd'),
    //            'type'             => 2, //竞拍订单订单付款
    //        );
    //        $GLOBALS['db']->autoExecute(DB_PREFIX . "user_diamonds_log", $diamonds_log_data);
    //        $log_id = $GLOBALS['db']->insert_id();
    //        // 更新订单状态
    //        $table = DB_PREFIX . "goods_order";
    //        $sql   = "UPDATE $table SET order_status = '2' WHERE `id`=$order_id";
    //        $GLOBALS['db']->query($sql);
    //        //写入用户日志
    //        //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益
    //        account_log_com(array('diamonds' => $diamond), $user_id, $log_msg, array('type' => 5));
    //
    //        $head_args['orderNo'] = $order['order_sn'];
    //        $ret = third_interface($user_id, 'http://gw1.yimile.cc/V1/Order.json?action=OrderPaySuccess', $head_args);
    //
    //        if (isset($ret['code']) && $ret['code'] == 0) {
    //
    //            if($purchase_type == 0){
    //                $url = SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pay&act=pay_success&user_id='.$user_id.'&order_sn='.$order['order_sn'];
    //                $root=array();
    //            }else{
    //
    //                $podcast_id = $GLOBALS['db']->getOne("select podcast_id from " . DB_PREFIX . "goods_order where order_sn='".$order['order_sn']."'");
    //                $head_args['orderNo']=$order['order_sn'];
    //                $head_args['userId']=$user_id;
    //
    //                $ret=third_interface($user_id,'http://gw1.yimile.cc/V1/Order.json?action=OrderSuccessDetailByZhubo',$head_args);
    //                if($ret['code'] == 0){
    //                    $root['quantity'] = $ret['data']['quantity'];
    //                    $root['goods_logo'] = $ret['data']['commodityLogo'];
    //                    $root['goods_name'] = $ret['data']['commodityName'];
    //                    $root['order_sn'] = $ret['data']['orderNo'];
    //                    $root['empirivalue'] = $ret['data']['empiriValue'];
    //                }
    //                $ext = array();
    //                $ext['goods'] = $root;
    //
    //                $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $podcast_id . " and live_in =1");
    //                $root['roomId'] = intval($video_info['id']);
    //                $root['groupId'] = $video_info['group_id'];
    //                $root['createrId'] = $video_info['user_id'];
    //                $root['loadingVideoImageUrl'] = get_abs_img_root($video_info['head_image']);
    //                $root['video_type'] = $video_info['video_type'];
    //
    //                $ext['type']    = 37;
    //                $ext['room_id'] = intval($video_info['id']);
    //                $ext['post_id'] = $user_id;
    //                $ext['desc'] = "您购买了“" .$root['goods_name']. "”";
    //                $ext['is_self'] = 0;
    //                $ext['score'] = $root['empirivalue'];
    //
    //                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
    //                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
    //                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    //                $user_redis = new UserRedisService();
    //                $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
    //                $ext['user'] = $user_redis->getRow_db($user_id, $fields);
    //                $ext['user']['user_id']    = $user_id;
    //                $ext['user']['head_image'] = get_abs_img_root($ext['user']['head_image']);
    //
    //                $user_score = $GLOBALS['db']->query("update ".DB_PREFIX."user set score=score+".intval($root['empirivalue'])." where id = ".$user_id);
    //                $podcast_score = $GLOBALS['db']->query("update ".DB_PREFIX."user set score=score+".intval($root['empirivalue'])." where id = ".$podcast_id);
    //                if($user_score && $podcast_score){
    //                    //更新经验
    //                    $user_info = $user_redis->getRow_db($user_id,array('id','score','online_time','user_level'));
    //                    user_leverl_syn($user_info);
    //                    $podcast_info = $user_redis->getRow_db($user_id,array('id','score','online_time','user_level'));
    //                    user_leverl_syn($podcast_info);
    //                }
    //
    //                #构造高级接口所需参数
    //                $tim_data = array();
    //                $tim_data['ext'] = $ext;
    //                $tim_data['podcast_id'] = strim($user_id);
    //                $tim_data['group_id'] = strim($video_info['group_id']);
    //                $tim_data['score'] = intval($root['empirivalue']);
    //                get_tim_api($tim_data);
    //
    //            }
    //
    //            api_ajax_return(array(
    //                'status' => 1,
    //                'url'    => $url,
    //                'error'  => '支付成功',
    //                "root"=>$root
    //            ));
    //        } else {
    //            // 回滚
    //            $table = '`' . DB_PREFIX . 'user`';
    //            $sql   = "UPDATE $table SET `diamonds` = `diamonds` + $diamond  WHERE `id` = $user_id";
    //            $GLOBALS['db']->query($sql);
    //            $table = DB_PREFIX . "user_diamonds_log";
    //            $sql   = "DELETE FROM $table WHERE `id`=$log_id";
    //            $GLOBALS['db']->query($sql);
    //            $table = DB_PREFIX . "goods_order";
    //            $sql   = "UPDATE $table SET order_status = '1' WHERE `id`=$order_id";
    //            $GLOBALS['db']->query($sql);
    //            user_deal_to_reids(array($user_id));
    //            api_ajax_return(array(
    //                'status' => 0,
    //                'error'  => '第三方请求失败',
    //                'ret'    => $ret,
    //            ));
    //        }
    //    }

    /**
     * 购物用户支付
     */
    public function h5_pay()
    {
        $diamond = 0;
        $purchase_type = intval($_REQUEST['purchase_type']); //0表示买给自己、1表示买给主播
        $order_id = intval($_REQUEST['order_id']); //订单id
        $order_sn = intval($_REQUEST['order_sn']); //订单编号

        if ($order_id) {
            $table = '`' . DB_PREFIX . 'goods_order`';
            $field = '`pai_id`,`goods_id`,`number`,`order_status`,`total_diamonds`,`order_sn`,`viewer_id`,`podcast_id`';
            $sql = "SELECT $field FROM $table WHERE id=" . $order_id . " and order_sn=" . $order_sn;
            $order = $GLOBALS['db']->getRow($sql);
            // 1:待付款 2:待发货 3:待收货(主播确认约会)  4:已收货(观众确认约会) 5:退款成功 6未付款 7结单
            if ($order['order_status'] != 1) {
                api_ajax_return(array(
                    'status' => 0,
                    'error' => '订单信息错误'
                ));
            }
            $diamond = floatval($order['total_diamonds']);
        }

        $user_id = intval($order['viewer_id']);
        if (!$diamond) {
            api_ajax_return(array(
                'status' => 0,
                'error' => '订单金额错误'
            ));
        }

        $goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "goods where id='" . $order['goods_id'] . "'");
        if ($goods_info['inventory'] != 0) {

            $table = '`' . DB_PREFIX . 'user`';
            $sql = "UPDATE $table SET `diamonds` = `diamonds` - $diamond  WHERE `id` = $user_id AND `diamonds` >= $diamond";
            $GLOBALS['db']->query($sql);
            if (!$GLOBALS['db']->affected_rows()) {
                api_ajax_return(array(
                    'status' => 10062,
                    'error' => '金额不足,请先充值'
                ));
            }
            user_deal_to_reids(array($user_id));

            $time = NOW_TIME;
            $user_redis = new UserRedisService();
            $account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');
            $log_msg = 'H5支付' . $diamond . '秀豆';
            $diamonds_log_data = array(
                'pai_id' => 0,
                'user_id' => $user_id,
                'diamonds' => $diamond,
                'account_diamonds' => $account_diamonds,
                'memo' => $log_msg,
                'create_time' => $time,
                'create_date' => to_date($time, 'Y-m-d H:i:s'),
                'create_time_ymd' => to_date($time, 'Y-m-d'),
                'create_time_y' => to_date($time, 'Y'),
                'create_time_m' => to_date($time, 'm'),
                'create_time_d' => to_date($time, 'd'),
                'type' => 2 //竞拍订单订单付款
            );
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_diamonds_log", $diamonds_log_data);
            $log_id = $GLOBALS['db']->insert_id();
            // 更新订单状态
            $pay_time = date("Y-m-d H:i:s", time());
            $table = DB_PREFIX . "goods_order";
            $sql = "UPDATE $table SET order_status = '2',pay_time='" . $pay_time . "' WHERE `id`=$order_id";
            $GLOBALS['db']->query($sql);
            //写入用户日志
            //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益
            account_log_com(array('diamonds' => $diamond), $user_id, $log_msg, array('type' => 5));

            if ($purchase_type == 0) {
                $url = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pay&act=pay_success&user_id=' . $user_id . '&order_sn=' . $order['order_sn'];
                $root = array();
            } else {

                if ($goods_info) {
                    $root['quantity'] = intval($order['number']);
                    $root['goods_logo'] = json_decode(get_spec_image($goods_info['imgs']), true)[0];
                    $root['goods_name'] = $goods_info['name'];
                    $root['order_sn'] = $order['order_sn'];
                    $root['empirivalue'] = $goods_info['score'];
                }
                $ext = array();
                $ext['goods'] = $root;

                $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $order['podcast_id'] . " and live_in =1");
                $root['roomId'] = intval($video_info['id']);
                $root['groupId'] = $video_info['group_id'];
                $root['createrId'] = $video_info['user_id'];
                $root['loadingVideoImageUrl'] = get_abs_img_root($video_info['head_image']);
                $root['video_type'] = $video_info['video_type'];

                $ext['type'] = 37;
                $ext['room_id'] = intval($video_info['id']);
                $ext['post_id'] = $user_id;
                $ext['desc'] = "您购买了“" . $root['goods_name'] . "”";
                $ext['is_self'] = 0;
                $ext['score'] = $root['empirivalue'];

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
                $ext['user'] = $user_redis->getRow_db($user_id, $fields);
                $ext['user']['user_id'] = $user_id;
                $ext['user']['head_image'] = get_abs_img_root($ext['user']['head_image']);

                $user_score = $GLOBALS['db']->query("update " . DB_PREFIX . "user set score=score+" . intval($root['empirivalue']) . " where id = " . $user_id);
                $podcast_score = $GLOBALS['db']->query("update " . DB_PREFIX . "user set score=score+" . intval($root['empirivalue']) . " where id = " . $order['podcast_id']);
                if ($user_score && $podcast_score) {
                    //更新经验
                    $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                    user_leverl_syn($user_info);
                    $podcast_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                    user_leverl_syn($podcast_info);
                }

                #构造高级接口所需参数
                $tim_data = array();
                $tim_data['ext'] = $ext;
                $tim_data['podcast_id'] = strim($user_id);
                $tim_data['group_id'] = strim($video_info['group_id']);
                $tim_data['score'] = intval($root['empirivalue']);
                get_tim_api($tim_data);

            }

        } else {
            api_ajax_return(array(
                'status' => 0,
                'error' => '商品库存不足'
            ));
        }

        api_ajax_return(array(
            'status' => 1,
            'url' => $url,
            'error' => '支付成功',
            "root" => $root
        ));

    }

    /*
     * 购物RMB支付页面
     * */
    public function shop_h5_pay()
    {
        $pay_id = intval($_REQUEST['pay_id']); //2支付宝支付，6微信js支付，11苹果支付，12微信app支付
        $purchase_type = intval($_REQUEST['purchase_type']); //0表示买给自己、1表示买给主播
        $order_id = intval($_REQUEST['order_id']); //订单id
        $order_sn = intval($_REQUEST['order_sn']); //订单编号

        if ($order_id) {
            $table = '`' . DB_PREFIX . 'goods_order`';
            $field = '`pai_id`,`goods_id`,`number`,`order_status`,`total_diamonds`,`order_sn`,`viewer_id`,`podcast_id`';
            $sql = "SELECT $field FROM $table WHERE id=" . $order_id . " and order_sn=" . $order_sn;
            $order = $GLOBALS['db']->getRow($sql);
            // 1:待付款 2:待发货 3:待收货(主播确认约会)  4:已收货(观众确认约会) 5:退款成功 6未付款 7结单
            if ($order['order_status'] != 1) {
                api_ajax_return(array(
                    'status' => 0,
                    'error' => '订单信息错误'
                ));
            }
            $money = floatval($order['total_diamonds']);
        }
        $user_id = intval($order['viewer_id']);

        $goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "goods where id='" . $order['goods_id'] . "'");
        if ($goods_info['inventory'] != 0) {

            $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where is_effect = 1 and id =" . $pay_id;
            $pay = $GLOBALS['db']->getRow($sql, true, true);
            if (!$pay || $money == 0) {
                ajax_return(array(
                    'error' => '支付id或 项目id无效',
                    'status' => 0,
                    'rule' => '',
                    'pay' => $pay,
                    'money' => $money
                ));
            }

            $payment_notice['create_time'] = NOW_TIME;
            $payment_notice['user_id'] = $user_id;
            $payment_notice['order_id'] = $order_id;
            $payment_notice['payment_id'] = $pay_id;
            $payment_notice['money'] = $money;
            $payment_notice['diamonds'] = 0; //充值时,获得的秀豆数量
            $payment_notice['bank_id'] = ''; //strim($_REQUEST['bank_id']);
            $payment_notice['recharge_id'] = 0;
            $payment_notice['recharge_name'] = "购买" . $goods_info['name'] . "商品支付人民币：" . $money;
            $payment_notice['product_id'] = 0;

            do {
                $payment_notice['notice_sn'] = to_date(NOW_TIME, "YmdHis") . rand(100, 999);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");
                $notice_id = $GLOBALS['db']->insert_id();
            } while ($notice_id == 0);

            $class_name = $pay['class_name'] . "_payment";
            fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
            $o = new $class_name();
            $payment = $o->get_payment_code($notice_id);
            if ($payment['status'] == 1) {

                // 更新订单状态
                $pay_time = date("Y-m-d H:i:s", time());
                $table = DB_PREFIX . "goods_order";
                $sql = "UPDATE $table SET order_status = '2',pay_time='" . $pay_time . "' WHERE `id`=$order_id";
                $GLOBALS['db']->query($sql);

                if ($purchase_type == 0) {
                    $url = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pay&act=pay_success&user_id=' . $user_id . '&order_sn=' . $order['order_sn'];
                    $root = array();
                } else {

                    if ($goods_info) {
                        $root['quantity'] = intval($order['number']);
                        $root['goods_logo'] = json_decode(get_spec_image($goods_info['imgs']), true)[0];
                        $root['goods_name'] = $goods_info['name'];
                        $root['order_sn'] = $order['order_sn'];
                        $root['empirivalue'] = $goods_info['score'];
                    }
                    $ext = array();
                    $ext['goods'] = $root;

                    $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $order['podcast_id'] . " and live_in =1");
                    $root['roomId'] = intval($video_info['id']);
                    $root['groupId'] = $video_info['group_id'];
                    $root['createrId'] = $video_info['user_id'];
                    $root['loadingVideoImageUrl'] = get_abs_img_root($video_info['head_image']);
                    $root['video_type'] = $video_info['video_type'];

                    $ext['type'] = 37;
                    $ext['room_id'] = intval($video_info['id']);
                    $ext['post_id'] = $user_id;
                    $ext['desc'] = "您购买了“" . $root['goods_name'] . "”";
                    $ext['is_self'] = 0;
                    $ext['score'] = $root['empirivalue'];

                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
                    $ext['user'] = $user_redis->getRow_db($user_id, $fields);
                    $ext['user']['user_id'] = $user_id;
                    $ext['user']['head_image'] = get_abs_img_root($ext['user']['head_image']);

                    $user_score = $GLOBALS['db']->query("update " . DB_PREFIX . "user set score=score+" . intval($root['empirivalue']) . " where id = " . $user_id);
                    $podcast_score = $GLOBALS['db']->query("update " . DB_PREFIX . "user set score=score+" . intval($root['empirivalue']) . " where id = " . $order['podcast_id']);
                    if ($user_score && $podcast_score) {
                        //更新经验
                        $user_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                        user_leverl_syn($user_info);
                        $podcast_info = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
                        user_leverl_syn($podcast_info);
                    }

                    #构造高级接口所需参数
                    $tim_data = array();
                    $tim_data['ext'] = $ext;
                    $tim_data['podcast_id'] = strim($user_id);
                    $tim_data['group_id'] = strim($video_info['group_id']);
                    $tim_data['score'] = intval($root['empirivalue']);
                    get_tim_api($tim_data);

                }

            } else {
                api_ajax_return(array(
                    'status' => 0,
                    'error' => '支付失败'
                ));
            }

        } else {
            api_ajax_return(array(
                'status' => 0,
                'error' => '商品库存不足'
            ));
        }

        api_ajax_return(array(
            'status' => 1,
            'url' => $url,
            'error' => '支付成功',
            "root" => $root
        ));

    }

}
