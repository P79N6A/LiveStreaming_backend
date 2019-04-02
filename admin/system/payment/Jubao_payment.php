<?php
$payment_lang = array(
    'name' => '聚宝聚合支付',
    'jubao_appid' => '商户ID',
    'jubap_psw' => '商户密钥(psw)',
    'jubao_cer' => '聚宝公钥(cer)',
    'jubao_pfx' => '商户私钥(pfx)',
);
$config = array(
    // 聚宝配置文件中的密钥
    'jubao_appid' => array(
        'INPUT_TYPE' => '0',
    ),
    // 聚宝配置文件中的密钥
    'jubap_psw' => array(
        'INPUT_TYPE' => '0',
    ),
    // 聚宝配置文件中的公钥
    'jubao_cer' => array(
        'INPUT_TYPE' => '4',
    ),
    //聚宝配置文件中的商户私钥
    'jubao_pfx' => array(
        'INPUT_TYPE' => '4',
    ),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Jubao';

    /* 名称 */
    $module['name'] = $payment_lang['name'];

    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '3';

    /* 配送 */
    $module['config'] = $config;

    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'https://www.jubaopay.com/buz/login.htm';
    return $module;
}

require_once APP_ROOT_PATH . 'system/libs/payment.php';

require_once APP_ROOT_PATH . 'system/payment/Jubaopay/jubaopay.php';

class Jubao_payment implements payment
{

    public function get_payment_code($payment_notice_id)
    {
        $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $payment_notice_id);
        $money = round($payment_notice['money'], 2);
        $user = $GLOBALS['db']->getRow("select nick_name from " . DB_PREFIX . "user where id = " . $payment_notice['user_id']);
        return array(
            'pay_info' => '',
            'payment_name' => '',
            'pay_money' => $money,
            'class_name' => 'Jubao',
            'config' => array("nick_name" => $user['nick_name'], "money" => $money, "payid" => $payment_notice['notice_sn']),
            'sdk_code' => array("pay_sdk_type" => "Jubao", "config" => array("product_id" => $payment_notice_id)),
        );
    }
    public function notify($request)
    {
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Jubao'");
        $payment['config'] = unserialize($payment['config']);
        $jubaopay = new jubaopay(array('pfx' => $payment['config']['jubao_pfx'], 'cer' => $payment['config']['jubao_cer'], 'pws' => $payment['config']['jubap_psw']));
        $message = $request["message"];
        $signature = $request["signature"];
        $jubaopay->decrypt($message);
        // 校验签名，然后进行业务处理
        if ($jubaopay->verify($signature) == 1) {
            $payment_notice_sn = $jubaopay->getEncrypt("payid");
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");

            //file_put_contents(APP_ROOT_PATH."/alipaylog/payment_notice_sn_3.txt",$payment_notice_sn);

            //$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
            require_once APP_ROOT_PATH . "system/libs/cart.php";
            payment_paid($payment_notice['notice_sn'], $jubaopay->getEncrypt("orderNo"));
            // 得到解密的结果后，进行业务处理
            // echo "payid=".$jubaopay->getEncrypt("payid")."<br />";
            // echo "mobile=".$jubaopay->getEncrypt("mobile")."<br />";
            // echo "amount=".$jubaopay->getEncrypt("amount")."<br />";
            // echo "remark=".$jubaopay->getEncrypt("remark")."<br />";
            // echo "orderNo=".$jubaopay->getEncrypt("orderNo")."<br />";
            // echo "state=".$jubaopay->getEncrypt("state")."<br />";
            // echo "partnerid=".$jubaopay->getEncrypt("partnerid")."<br />";
            // echo "modifyTime=".$jubaopay->getEncrypt("modifyTime")."<br />";
            echo "success"; // 像服务返回 "success"
        } else {
            echo "verify failed";
        }
    }
    //响应通知
    public function response($request)
    {}

    //获取接口的显示
    public function get_display_code()
    {
        return "聚宝聚合支付";
    }
}
