<?php
$payment_lang = array(
    'name' => '快接支付宝支付',
    'kjpay_api_url' => 'API地址',
    'kjpay_app_no' => '应用编号',
    'kjpay_appid' => '商户ID',
    'kjpay_key' => '商户密钥(key)',
);
$config = array(
    // 接口地址
    'kjpay_api_url' => array(
        'INPUT_TYPE' => '0',
    ),
    // 应用编号
    'kjpay_app_no' => array(
        'INPUT_TYPE' => '0',
    ),
    // 聚宝配置文件中的密钥
    'kjpay_appid' => array(
        'INPUT_TYPE' => '0',
    ),
    // 聚宝配置文件中的密钥
    'kjpay_key' => array(
        'INPUT_TYPE' => '0',
    ),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Kjpay';

    /* 名称 */
    $module['name'] = $payment_lang['name'];

    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '3';

    /* 配送 */
    $module['config'] = $config;

    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://merchant.kj-pay.com/';
    return $module;
}

require_once APP_ROOT_PATH . 'system/libs/payment.php';

require_once APP_ROOT_PATH . 'system/payment/Kjpay/init.php';

class Kjpay_payment implements payment
{
    public function get_payment_code($payment_notice_id)
    {
        $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $payment_notice_id);
        $money = round($payment_notice['money'], 2);
        $payment_info = $GLOBALS['db']->getRow("select id,config,logo from " . DB_PREFIX . "payment where id=" . intval($payment_notice['payment_id']));
        $payment_info['config'] = unserialize($payment_info['config']);
        $m_config = load_auto_cache("m_config");
        $title_name = $m_config['ticket_name'];
        if ($title_name == '') {
            $title_name = '虚拟印币';
        }

        $subject = msubstr($title_name, 0, 40);
        $notify_url = SITE_DOMAIN . '/callback/payment/kjpay_notify.php';
        try {
            $_pay = (new \Pay\Pay(['kjalipay' => [
                // APi地址
                'api_url' => $payment_info['config']['kjpay_api_url'],
                // 应用编号
                'app_no' => $payment_info['config']['app_no'],
                // 商户号
                'merchantid' => $payment_info['config']['kjpay_appid'],
                // 商户密钥
                'merchantkey' => $payment_info['config']['kjpay_key'],
                // 支付成功通知地址
                'notify_url' => $notify_url,
                // 网页支付回跳地址
                'return_url' => $notify_url,
                // 缓存目录配置
                'cache_path' => '',
            ]]))->driver('kjalipay')->gateway('app')->apply([
                'merchant_order_no' => $payment_notice['notice_sn'], // 商户订单号
                'trade_amount' => round($payment_notice['money'], 2), // 支付金额
                'goods_name' => $title_name, // 支付订单描述
                'goods_desc' => '用户消费', // 支付订单描述
            ]);
            if ($_pay['status'] == 1) {
                return array(
                    'pay_info' => '',
                    'payment_name' => '',
                    'class_name' => 'Kjpay',
                    'config' => array("trade_no" => $_pay['data']['trade_no'], "order_spec" => $_pay['data']['pay_url'], "sign" => $_pay['data']['sign'], 'sign_type' => 'RSA'),
                    'sdk_code' => array("pay_sdk_type" => "Kjpay", "config" => array("trade_no" => $_pay['data']['trade_no'], "order_spec" => $_pay['data']['pay_url'], "sign" => $_pay['data']['sign'], 'sign_type' => 'RSA')),
                );
            } else {
                ajax_return(["status" => 0, 'error' => $_pay['info'] . $_pay['data']['error']]);
            }
        } catch (Exception $e) {
            ajax_return(["status" => 0, 'error' => $e->getMessage()]);
        }
    }
    public function notify($request)
    {
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Kjpay'");
        $payment['config'] = unserialize($payment['config']);
        try {
            if (($result = (new \Pay\Pay(['kjalipay' => [
                // APi地址
                'api_url' => $payment['config']['kjpay_api_url'],
                // 应用编号
                'app_no' => $payment['config']['app_no'],
                // 商户号
                'merchantid' => $payment['config']['kjpay_appid'],
                // 商户密钥
                'merchantkey' => $payment['config']['kjpay_key'],
                // 支付成功通知地址
                'notify_url' => $notify_url,
                // 网页支付回跳地址
                'return_url' => $notify_url,
                // 缓存目录配置
                'cache_path' => '',
            ]]))->driver('kjalipay')->gateway('app')->verify($request)) !== false) {
                // 订单异常
                if ($result['status'] != 'Success') {
                    return 'Fail';
                } else {
                    $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $result['merchant_order_no'] . "'");

                    //file_put_contents(APP_ROOT_PATH."/alipaylog/payment_notice_sn_3.txt",$payment_notice_sn);

                    //$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
                    require_once APP_ROOT_PATH . "system/libs/cart.php";
                    payment_paid($payment_notice['notice_sn'], $result['trade_no']);
                    echo 'Success';
                }
            } else {
                echo 'Fail';
            }
        } catch (Exception $e) {
            echo 'Fail';
        }
    }
    //响应通知
    public function response($request)
    {}

    //获取接口的显示
    public function get_display_code()
    {
        return "快接支付宝支付";
    }
}
