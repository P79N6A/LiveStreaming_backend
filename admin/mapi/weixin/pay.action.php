<?php

class payCModule extends baseModule
{
    public function weixin_pay()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $rule_id = intval($_REQUEST['rule_id']);
        if (!$rule_id) {
            api_ajax_return([
                'error' => '请选择套餐',
                'status' => 0,
            ]);
        }


        $sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where is_effect = 1 and class_name='Wwxjspay'";
        $pay = $GLOBALS['db']->getRow($sql, true, true);
        if (intval($pay['id']) == 0) {
            api_ajax_return([
                'error' => '未开启微信充值',
                'status' => 0,
            ]);
        }

        $user_id = $GLOBALS['user_info']['id'];

        $sql = "select id,money,name,iap_money,product_id,(diamonds+gift_diamonds) as diamonds from " . DB_PREFIX . "recharge_rule where is_effect = 1 and is_delete = 0 and id =" . $rule_id;
        $rule = $GLOBALS['db']->getRow($sql, true, true);

        $payment_notice = [];
        $payment_notice['create_time'] = NOW_TIME;
        $payment_notice['user_id'] = $user_id;
        $payment_notice['payment_id'] = $pay['id'];
        $payment_notice['money'] = $rule['money'];
        $payment_notice['diamonds'] = $rule['diamonds'];//充值时,获得的秀豆数量
        $payment_notice['recharge_id'] = $rule['id'];
        $payment_notice['recharge_name'] = $rule['name'];
        $payment_notice['product_id'] = $rule['product_id'];
        $payment_notice['notice_sn'] = to_date(NOW_TIME, "YmdHis") . rand(100, 999);
        $GLOBALS['db']->autoExecute(DB_PREFIX . "payment_notice", $payment_notice, "INSERT", "", "SILENT");

        $notice_id = $GLOBALS['db']->insert_id();

        $class_name = $pay['class_name'] . "_payment";
        fanwe_require(APP_ROOT_PATH . "system/payment/" . $class_name . ".php");
        $o = new $class_name;
        $pay_info = $o->get_payment_code($notice_id);

        api_ajax_return($pay_info);
    }
}