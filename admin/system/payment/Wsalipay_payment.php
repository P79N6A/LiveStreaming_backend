<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
    'name' => '万胜（支付宝WAP支付）',
    'mchid' => '商户编号',
    'key' => '商户密钥'
);
$config = array(
    'mchid' => array(
        'INPUT_TYPE' => '0'
    ),
    'key' => array(
        'INPUT_TYPE' => '0'
    )
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Wsalipay';

    /* 名称 */
    $module['name'] = $payment_lang['name'];

    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '4';

    /* 配送 */
    $module['config'] = $config;

    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}
require_once APP_ROOT_PATH . 'system/libs/payment.php';
class Wsalipay_payment implements payment
{
    public function get_payment_code($payment_notice_id)
    {
        $pay = array();
        $pay['is_wap'] = 1; //
        $pay['is_without'] = 1; //跳转外部浏览器
        $pay['class_name'] = "Wsalipay";
        $pay['url'] = SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Wsalipay&notice_id=' . $payment_notice_id;
        $pay['sdk_code'] = array("pay_sdk_type" => "fuwap", "config" => array(
            "url" => SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Wsalipay&notice_id=' . $payment_notice_id,
            "is_wap" => 1
        ));
        return $pay;
    }
    public function notify($request)
    {
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Wsalipay'");
        $payment['config'] = unserialize($payment['config']);
        require_once APP_ROOT_PATH . 'system/payment/Wspay/Wspay.php';
        try {
            $wsalipay = new Wspay([
                'mchid' => $payment['config']['mchid'],
                'key' => $payment['config']['key']
            ]);
            $result = $wsalipay->verify($request);
            if ($result !== false) {
                $payment_notice_sn = $result['corp_flow_no'];
                $outer_notice_sn = $result['reqMsgId'];
                $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");
                // log_file('$payment_notice', 'gf');
                // log_file($payment_notice, 'gf');
                require_once APP_ROOT_PATH . "system/libs/cart.php";
                $rs = payment_paid($payment_notice['notice_sn'], $outer_notice_sn);
                if ($rs['status'] == 1) {
                    echo "00"; // 像服务返回 "success";
                } else {
                    echo "fails"; // 像服务返回 "fails"
                }
            } else {
                echo "fails";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
    public function response($request)
    {
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5"><title></title></head><body><div style="width:120px;height:40px;line-height:40px;font-size:14px;text-align:center;background:#ff4d7f;color:#fff;margin:20px auto;border-radius:5px;" >关闭当前页面</div></body></html>';
        exit;
    }
    public function get_display_code()
    {
        return "万胜支付宝支付";
    }

    public function display_code($payment_notice_id)
    {
        if ($payment_notice_id) {
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $payment_notice_id);
            //$order = $GLOBALS['db']->getRow("select order_sn,user_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
            $money = round($payment_notice['money'], 2);
            $payment_info = $GLOBALS['db']->getRow("select id,config,logo from " . DB_PREFIX . "payment where id=" . intval($payment_notice['payment_id']));
            $payment_info['config'] = unserialize($payment_info['config']);

            require_once APP_ROOT_PATH . 'system/payment/Wspay/Wspay.php';
            try {
                // $data_return_url = SITE_DOMAIN . APP_ROOT . '/callback/payment/wsalipay_response.php';
                $data_notify_url = SITE_DOMAIN . APP_ROOT . '/callback/payment/wsalipay_notify.php';
                $wsalipay = new Wspay([
                    'mchid' => $payment_info['config']['mchid'],
                    'key' => $payment_info['config']['key'],
                    'model' => 'ZFBZF',
                    'notify_url' => $data_notify_url
                    // 'return_url' => $data_return_url
                ]);
                $m_config = load_auto_cache("m_config");
                $title_name = $m_config['ticket_name'];
                if ($title_name == '') {
                    $title_name = '虚拟印币';
                }
                if (empty($title_name)) {
                    $title_name = "充值" . round($payment_notice['money'], 2) . "元";
                }
                $url = $wsalipay->create([
                    'amount' => $money,
                    'goods_desc' => $title_name,
                    'out_trade_no' => $payment_notice['notice_sn']
                ]);
                header('Location:' . $url);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            return '';
        }

    }
}