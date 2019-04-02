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
    protected static function getUserId()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return(array(
                'error'             => '用户未登陆,请先登陆.',
                'status'            => 0,
                'user_login_status' => 0,
            ));
        }
        return intval($GLOBALS['user_info']['id']);
    }
    /**
     * 支付列表
     * @return [type] [description]
     */
    public function recharge()
    {
        $user_id = self::getUserId();

        $field    = 'id,name,class_name,logo';
        $table    = DB_PREFIX . 'payment';
        $where    = 'is_effect=1 and online_pay=1';
        $order    = 'sort';
        $pay_list = $GLOBALS['db']->getAll("SELECT $field FROM $table WHERE $where ORDER BY $order", 1, 1);

        $field     = 'id,name,money,(diamonds+gift_diamonds) as diamonds,iap_money';
        $table     = DB_PREFIX . 'recharge_rule';
        $where     = 'is_effect=1 and is_delete=0';
        $rule_list = $GLOBALS['db']->getAll("SELECT $field FROM $table WHERE $where ORDER BY $order", 1, 1);

        $m_config = load_auto_cache("m_config");

        api_ajax_return(array(
            'status'     => 1,
            'show_other' => 1,
            'pay_list'   => $pay_list,
            'rule_list'  => $rule_list,
            'rate'       => intval($m_config['diamonds_rate']),
        ));
    }
    public function pay()
    {
        $user_id = self::getUserId();
        $pay_id  = intval($_REQUEST['pay_id']);
        $rule_id = intval($_REQUEST['rule_id']);
        $money   = floatval($_REQUEST['money']);
        if ($pay_id <= 0) {
            ajax_return(array(
                'error'  => '支付id无效',
                'status' => 0,
            ));
        }
        if ($rule_id == 0 && $money == 0) {
            ajax_return(array(
                'error'  => '项目id无效或充值金额不能为0',
                'status' => 0,
            ));
        }
        $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where is_effect = 1 and online_pay = 1 and id =" . $pay_id;
        $pay = $GLOBALS['db']->getRow($sql, true, true);

        if ($rule_id > 0) {
            $sql  = "select money,name,iap_money,product_id,(diamonds+gift_diamonds) as diamonds from " . DB_PREFIX . "recharge_rule where is_effect = 1 and is_delete = 0 and id =" . $rule_id;
            $rule = $GLOBALS['db']->getRow($sql, true, true);

            if ($pay['class_name'] == 'Iappay') {
                $money = $rule['iap_money'];
            } else {
                $money = $rule['money'];
            }

            $diamonds = $rule['diamonds'];

        } else if ($money > 0) {
            $m_config      = load_auto_cache("m_config");
            $diamonds_rate = intval($m_config['diamonds_rate']);
            $diamonds      = intval($money * $diamonds_rate);
        } else {
            $pay   = null;
            $money = 0;
        }
        if (!$pay || $money == 0) {
            ajax_return(array(
                'error'  => '支付id或 项目id无效',
                'status' => 0,
                'rule'   => $rule,
                'pay'    => $pay,
                'money'  => $money,
            ));
        }
        if ($pay['class_name'] != 'Iappay') {
            $payment_notice['create_time'] = NOW_TIME;
            $payment_notice['user_id']     = $user_id;
            $payment_notice['payment_id']  = $pay_id;
            $payment_notice['money']       = $money;
            $payment_notice['diamonds']    = $diamonds; //充值时,获得的秀豆数量

            //$payment_notice['bank_id'] = '';//strim($_REQUEST['bank_id']);
            if ($rule_id > 0) {
                $payment_notice['recharge_id']   = $rule_id;
                $payment_notice['recharge_name'] = $rule['name'];
                $payment_notice['product_id']    = $rule['product_id'];
            } else {
                $payment_notice['recharge_name'] = '自定义充值';
            }

            do {
                $payment_notice['notice_sn'] = to_date(NOW_TIME, "YmdHis") . rand(100, 999);
                $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");
                $notice_id = $GLOBALS['db']->insert_id();
            } while ($notice_id == 0);
        } else {
            $notice_id = $rule['product_id'];
        }

        $class_name = $pay['class_name'] . "_payment";
        // fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
        // $o   = new $class_name;
        // $pay = $o->get_payment_code($notice_id);

        // ajax_return(array(
        //     'status' => 1,
        //     'pay'    => $pay,
        // ));
        fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
        $o   = new $class_name();
        ajax_return($o->get_payment_code($notice_id));

    }
    public function exchange()
    {
        $root     = array('status' => 1, 'error' => '');
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        if (!$GLOBALS['user_info']) {
            $root['error']             = "用户未登陆,请先登陆.";
            $root['status']            = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $exchange_rules         = $GLOBALS['db']->getAll("select er.id,er.diamonds,er.ticket from " . DB_PREFIX . "exchange_rule as er where is_effect=1 and is_delete=0 order by er.diamonds");
            $root['exchange_rules'] = $exchange_rules;

            //$user =  $GLOBALS['db']->getRow("select ticket,diamonds from ".DB_PREFIX."user where id=".$GLOBALS['user_info']['id'],true,true);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user       = $user_redis->getRow_db($GLOBALS['user_info']['id'], array('ticket', 'diamonds', 'refund_ticket'));

            $GLOBALS['user_info']['ticket'] = intval($user['ticket']);
            $root['ticket']                 = intval($user['ticket']);

            $GLOBALS['user_info']['refund_ticket'] = intval($user['refund_ticket']);
            $root['refund_ticket']                 = intval($user['refund_ticket']); //已使用的秀票

            $GLOBALS['user_info']['diamonds'] = intval($user['diamonds']);
            $root['diamonds']                 = intval($user['diamonds']);
            $root['useable_ticket']           = intval($user['ticket'] - $user['refund_ticket']);
            //兑换规则
            //$ratio = floatval(app_conf('TICKET_CATTY_RATIO'));
            $ratio         = $m_config['exchange_rate'];
            $root['ratio'] = $ratio;

            $m_config      = load_auto_cache("m_config");
            $exchange_rate = floatval($m_config['exchange_rate']);
            //兑换最低票数
            if ($exchange_rate > 0) {
                $min_ticket         = floatval(1 / $exchange_rate);
                $root['min_ticket'] = $min_ticket;
            } else {
                $root['min_ticket'] = 0;
            }
        }
        api_ajax_return($root);
    }
    public function do_exchange()
    {
        $user_id = self::getUserId();
        $rule_id = intval($_REQUEST['rule_id']);
        $ticket  = intval($_REQUEST['ticket']);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user       = $user_redis->getRow_db($user_id, array('ticket', 'diamonds', 'refund_ticket'));
        $m_config   = load_auto_cache("m_config");

        if ($rule_id) {
            $exchange_rule = $video_new = $GLOBALS['db']->getRow("select er.* from " . DB_PREFIX . "exchange_rule as er where is_effect=1 and is_delete=0 and id = " . $rule_id);
            if ($exchange_rule) {
                $diamonds = intval($exchange_rule['diamonds']);

                $ticket = intval($exchange_rule['ticket']);
            } else {
                ajax_return(array(
                    'status'        => 0,
                    'error'         => '兑换出错',
                    'ticket'        => $user['ticket'],
                    'refund_ticket' => $user['refund_ticket'],
                    'diamonds'      => $user['diamonds'],
                ));
            }
        } else if ($ticket) {
            $ratio    = $m_config['exchange_rate'];
            $diamonds = intval($ticket * $ratio);
        } else {
            ajax_return(array(
                'status' => 0,
                'error'  => '参数错误',
            ));
        }
        if ($user['ticket'] - $user['refund_ticket'] < $ticket) {
            ajax_return(array(
                'status'        => 0,
                'error'         => $m_config['ticket_name'] . '不足',
                'ticket'        => $user['ticket'],
                'refund_ticket' => $user['refund_ticket'],
                'diamonds'      => $user['diamonds'],
            ));
        }
        if ($diamonds > 0) {
            //使用兑换列表的值
            $table = DB_PREFIX . 'user';
            $GLOBALS['db']->query("UPDATE $table set refund_ticket = refund_ticket + $ticket , diamonds = diamonds + $diamonds where ticket >= refund_ticket + $ticket and id = $user_id");
            if ($GLOBALS['db']->affected_rows()) {
                //redis 更新信息
                user_deal_to_reids(array($user_id));
                $exchange_log = array(
                    'user_id'     => $user_id,
                    'rule_id'     => $rule_id,
                    'diamonds'    => $diamonds,
                    'ticket'      => $ticket,
                    'create_time' => get_gmtime(),
                    'is_success'  => 1,
                );
                $GLOBALS['db']->autoExecute(DB_PREFIX . "exchange_log", $exchange_log, "INSERT", "", "SILENT");
                //写入用户日志
                $data = array(
                    'diamonds' => intval($diamonds),
                    'ticket'   => intval($ticket),
                );
                $ticket_name = $m_config['ticket_name'] != '' ? $m_config['ticket_name'] : '秀票';
                $log_msg     = $ticket . $ticket_name . '兑换成' . $diamonds . '秀豆';
                //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票
                account_log_com($data, $user_id, $log_msg, array('type' => 3));
            } else {
                $GLOBALS['db']->autoExecute(DB_PREFIX . "exchange_log", array('is_success' => 0), "INSERT", "", "SILENT");
                ajax_return(array(
                    'status'        => 0,
                    'error'         => '兑换失败',
                    'ticket'        => $user['ticket'],
                    'refund_ticket' => $user['refund_ticket'],
                    'diamonds'      => $user['diamonds'],
                ));
            }
        }
        $user = $user_redis->getRow_db($user_id, array('ticket', 'diamonds', 'refund_ticket'));
        ajax_return(array(
            'status'         => 1,
            'error'          => '兑换成功',
            'ticket'         => $user['ticket'],
            'refund_ticket'  => $user['refund_ticket'],
            'diamonds'       => $user['diamonds'],
            'useable_ticket' => intval($user['ticket'] - $user['refund_ticket']),
        ));
    }
}
