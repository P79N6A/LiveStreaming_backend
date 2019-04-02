<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
    'name' => '国付（微信WAP支付）',
    'partner_id' => '商户编号',
    'merchantPrivateKey' => '商户密钥'
);
$config = array(
    'partner_id' => array(
        'INPUT_TYPE' => '0'
    ),
    'merchantPrivateKey' => array(
        'INPUT_TYPE' => '0'
    )
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Gfwxpay';

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
class Gfwxpay_payment implements payment
{
    public function get_payment_code($payment_notice_id)
    {
        log_file(111, 'gf');
        $pay = array();
        $pay['is_wap'] = 1; //
        $pay['is_without'] = 1; //跳转外部浏览器
        $pay['class_name'] = "Gfwxpay";
        $pay['url'] = SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Gfwxpay&notice_id=' . $payment_notice_id;
        $pay['sdk_code'] = array("pay_sdk_type" => "yjwap", "config" => array(
            "url" => SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Gfwxpay&notice_id=' . $payment_notice_id,
            "is_wap" => 1
        )
        );
        log_file($pay, 'gf');
        return $pay;
    }
    public function notify($request)
    {
        log_file('$request', 'gf');
        log_file($request, 'gf');
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Jubaopay'");
        $payment['config'] = unserialize($payment['config']);

        require_once APP_ROOT_PATH . 'system/payment/Gfwxpay/gfpay.php';
        $gfpay = new gfpay($payment['config']['partner_id'], $payment['config']['merchantPrivateKey']);
        $result = $gfpay->get_md5($request);
        if ($result['status']) {
            if ($result['md5'] == $request['signature'] || 1) {
                $payment_notice_sn = $request['traceno'];
                $outer_notice_sn = $request['refno'];
                $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");
                log_file('$payment_notice', 'gf');
                log_file($payment_notice, 'gf');
                require_once APP_ROOT_PATH . "system/libs/cart.php";
                $rs = payment_paid($payment_notice['notice_sn'], $outer_notice_sn);
                if ($rs['status'] == 1) {
                    echo "success"; // 像服务返回 "success";
                } else {
                    echo "fails"; // 像服务返回 "fails"
                }
            } else {
                echo "fails";
            }
        } else {
            echo "fails";
        }
    }
    public function response($request)
    {
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Jubaopay'");
        $payment['config'] = unserialize($payment['config']);

        require_once APP_ROOT_PATH . 'system/payment/Gfwxpay/gfpay.php';
        $gfpay = new gfpay($payment['config']['partner_id'], $payment['config']['merchantPrivateKey']);
        if ($request) {
            $payment_notice_sn = $request['traceno'];
            $outer_notice_sn = $request['refno'];
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");
            $user_diamonds = $GLOBALS['db']->getOne("select diamonds from " . DB_PREFIX . "user where id = '" . $payment_notice['user_id'] . "'");
            require_once APP_ROOT_PATH . "system/libs/cart.php";
            $rs = payment_paid($payment_notice['notice_sn'], $outer_notice_sn);
            if ($rs['status'] == 1) {
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5"><title></title></head><body><div style="width:120px;height:40px;line-height:40px;font-size:14px;text-align:center;background:#ff4d7f;color:#fff;margin:20px auto;border-radius:5px;" >付款成功！当前余额：' . $user_diamonds . '</div></body></html>';
                exit;
            } else {
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5"><title></title></head><body><div style="width:120px;height:40px;line-height:40px;font-size:14px;text-align:center;background:#ff4d7f;color:#fff;margin:20px auto;border-radius:5px;" >回调失败！关闭当前页面</div></body></html>';
                exit;
            }
        } else {
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5"><title></title></head><body><div style="width:120px;height:40px;line-height:40px;font-size:14px;text-align:center;background:#ff4d7f;color:#fff;margin:20px auto;border-radius:5px;" >' . $request['msg'] . '关闭当前页面</div></body></html>';
            exit;
        }
    }
    public function get_display_code()
    {

    }

    public function display_code($payment_notice_id)
    {

        if ($payment_notice_id) {
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $payment_notice_id);
            //$order = $GLOBALS['db']->getRow("select order_sn,user_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
            $money = round($payment_notice['money'], 2);
            $payment_info = $GLOBALS['db']->getRow("select id,config,logo from " . DB_PREFIX . "payment where id=" . intval($payment_notice['payment_id']));
            $payment_info['config'] = unserialize($payment_info['config']);

            require_once APP_ROOT_PATH . 'system/payment/Gfwxpay/gfpay.php';
            $gfpay = new gfpay($payment_info['config']['partner_id'], $payment_info['config']['merchantPrivateKey']);

            //$data_return_url = SITE_DOMAIN.APP_ROOT.'/callback/payment/gfpay_response.php';
            $data_notify_url = SITE_DOMAIN . APP_ROOT . '/callback/payment/gfwxpay_notify.php';
            $payType = '2'; //wap支付 不支持支付宝
            $settleType = '1'; //wap支付不支持t0
            $notifyUrl = $data_notify_url; //商户后台系统回调地址，前后台的回调结果一样
            $traceno = $payment_notice['notice_sn'];
            $m_config = load_auto_cache("m_config");
            $title_name = $m_config['ticket_name'];
            if ($title_name == '') {
                $title_name = '虚拟印币';
            }

            if (empty($title_name)) {
                $title_name = "充值" . round($payment_notice['money'], 2) . "元";
            }
            $amount = $money;
            $post_data = array(
                "amount" => $amount,
                'payType' => $payType,
                'settleType' => $settleType,
                'merchno' => $payment_info['config']['partner_id'],
                'traceno' => $traceno, //自定义流水号
                'notifyUrl' => $notifyUrl,
                'goodsName' => $title_name,
                'remark' => "remark"
            );
            $reveiveData = $gfpay->get_reveiveData($post_data);
            $data = iconv('GBK//IGNORE', 'UTF-8', $gfpay->get_url($reveiveData));
            $data = $this->object_array(json_decode($data));
            if ($data['barCode'] == '') {
                echo $data['message'];
            } else {
                header('Location:' . $data['barCode']);
            }
        } else {
            return '';
        }

    }

    public function object_array($array)
    {
        if (is_object($array)) {
            $array = (array) $array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

}
