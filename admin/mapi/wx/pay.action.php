<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class payCModule extends baseModule
{

    /**
     * 用户购买vip
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

            $pay_id = intval($_REQUEST['pay_id']); //支付id
            $vip_id = intval($_REQUEST['vip_id']); //购买的vip_id
            $pid = intval($_REQUEST['pid']); //看的集id

            $sql = "select cost,name from " . DB_PREFIX . "vip where id =" . $vip_id;
            $vip_info = $GLOBALS['db']->getRow($sql, true, true); //vip项
            $money = $vip_info['cost']; //支付金额

            if ($pay_id == 0) {
                $root['error'] = "支付id无效";
                $root['status'] = 0;
            } elseif ($vip_id == 0 && $money == 0) {
                $root['error'] = "项目id无效或充值金额不能为0";
                $root['status'] = 0;
            } else {
                $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where is_effect = 1 and online_pay = 2 and id =" . $pay_id;
                $pay = $GLOBALS['db']->getRow($sql);

                if (!$pay || $money == 0) {
                    $root['money'] = $money;
                    $root['pay_id'] = $pay_id;
                    $root['error'] = "支付id或 项目id无效";
                    $root['status'] = 0;
                } else {
                    $qrcode_id = intval($_REQUEST['qrcode_id']);
                    if ($qrcode_id > 0) {

                        $payment_notice['qrcode_id'] = $qrcode_id;
                    }

                    $payment_notice['create_time'] = NOW_TIME;
                    $payment_notice['user_id'] = $user_id;
                    $payment_notice['payment_id'] = $pay_id;
                    $payment_notice['money'] = $money;
                    $payment_notice['diamonds'] = 0;
                    $payment_notice['recharge_name'] = '购买' . $vip_info['name'];
                    $payment_notice['deal_id'] = $vip_id;
                    $payment_notice['order_id'] = $pid;
                    do {
                        $payment_notice['notice_sn'] = to_date(NOW_TIME, "YmdHis") . rand(100, 999);
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");
                        $notice_id = $GLOBALS['db']->insert_id();
                    } while ($notice_id == 0);

                    $class_name = $pay['class_name'] . "_payment";
                    fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
                    $o = new $class_name;
                    $jsApiParameters = $o->get_payment_code($notice_id);

                    //app_redirect($jsApiParameters['notify_url']);

                    $root['jsApiParameters'] = $jsApiParameters;
                }
            }
        }

        api_ajax_return($root);

    }

    public function wx_jspay()
    {

        $notice_id = intval($_REQUEST['payment_notice_id']);
        //log_result("==notice_id==");
        //log_result($notice_id);
        //log_result($_REQUEST);
        //$notice_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$notice_id."   and user_id = ".intval($GLOBALS['user_info']['id']));
        $notice_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $notice_id);
        if ($notice_info['is_paid'] == 1) {
            $data['pay_status'] = 1;
            $data['pay_info'] = '已支付.';
            $data['show_pay_btn'] = 0;
            $data['pid'] = $notice_info['order_id'];
            //$data['deal_id'] = $notice_info['deal_id'];
            //$GLOBALS['tmpl']->assign('data',$data);
            api_ajax_return($data);
            //$GLOBALS['tmpl']->display('pay_order_index.html');
        } else {
            $payment_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment where id = " . $notice_info['payment_id']);
            $class_name = $payment_info['class_name'] . "_payment";
            require_once APP_ROOT_PATH . "system/payment/" . $class_name . ".php";
            $o = new $class_name;
            $pay = $o->get_payment_code($notice_id);
            //$GLOBALS['tmpl']->assign('jsApiParameters',$pay['parameters']);
            $data['jsApiParameters'] = $pay['parameters'];
            $notice_info['pay_status'] = 0;
            $notice_info['pay_info'] = '未支付.';
            $notice_info['show_pay_btn'] = 1;
            $data['pid'] = $notice_info['order_id'];
            //$notice_info['deal_id'] = $notice_info['deal_id'];
            $data['notice_info'] = $notice_info;
            $data['type'] = $payment_info['config']['type'];

            api_ajax_return($data);
        }
    }

    public function cart()
    {
        $root = array();
        $root['status'] = 1;
        //log_result('---cart---');
        $order_id = intval($_REQUEST['order_id']);
        $payment_notice_sn = trim($_REQUEST['out_trade_no']);

        $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");

        $out_trade_no = $payment_notice['notice_sn'];
        require_once APP_ROOT_PATH . "system/libs/cart.php";
        $rs = payment_paid($out_trade_no, $trade_no);
        api_ajax_return($rs);
    }

    // 扫码支付
    public function qrcode()
    {
        $root = array();
        api_ajax_return($root);
    }

}
