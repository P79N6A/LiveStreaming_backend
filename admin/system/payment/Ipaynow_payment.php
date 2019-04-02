<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25
 * Time: 15:50
 */

$payment_lang = array(
    'name' => '现在支付(聚合支付)',

);
$config = array(

);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Ipaynow';

    /* 名称 */
    $module['name'] = $payment_lang['name'];

    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '3';

    /* 配送 */
    $module['config'] = $config;

    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}
/**
 * 此模块为现在支付（聚合支付）的支付类入口
 */
require_once APP_ROOT_PATH . 'system/libs/payment.php';

class Ipaynow_payment implements payment
{

    private $appId = '150097521195575'; //商户应用唯一标识

    private $privateKey = '8ZWbnEY3ecniG1qn4jQ5m9SLYO9HAAr7'; //密钥

    public $mhtOrderNo = ''; //商户订单号

    public $mhtOrderName = ''; //商户商品名称

    public $mhtOrderType = '01'; //商户交易类型,01 普通消费

    public $mhtCurrencyType = '156'; //商户订单币种类型,156 人民币

    public $mhtOrderAmt = ''; //商户订单交易金额

    public $mhtOrderDetail = ''; //商户订单详情

    public $mhtOrderTimeOut = '3600'; //商户订单超时时间

    public $mhtOrderStartTime = ''; //商户订单开始时间

    public $notifyUrl = '/callback/payment/ipaynow_notify.php';

    public $mhtCharset = 'UTF-8';

    public $payChannelType = '12'; //默认支付宝支付

    public $mhtLimitPay = '';

    public $mhtReserved = '';

    public $mhtSignType = 'MD5'; //不参与签名

    public $mhtSignature = ''; //不参与签名

    /**
     * 传入订单ID
     * 查询订单相关信息，MD5加密后返回
     * @param $payment_notice_id
     */
    public function get_payment_code($payment_notice_id)
    {
        // $data['status']             = 1;

        $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = '" . $payment_notice_id . "'");

        $m_config = load_auto_cache("m_config");
        $title_name = $m_config['ticket_name'];
        if ($title_name == '') {
            $title_name = '虚拟印币';
        }

        $pay = array();
        //$payment_notice['money']=0.01;
        $pay['pay_info'] = $title_name;
        $pay['payment_name'] = "现在支付";
        $pay['pay_money'] = $payment_notice['money'];
        $pay['class_name'] = "Ipaynow";
        $pay['config'] = array();

        $pay['config']['subject'] = $title_name;
        $pay['config']['body'] = $title_name;
        $pay['config']['total_fee'] = $payment_notice['money'];
        $pay['config']['total_fee_format'] = format_price($payment_notice['money']);
        $pay['config']['out_trade_no'] = $payment_notice['notice_sn'];
        $pay['config']['notify_url'] = SITE_DOMAIN . $this->notifyUrl;

        $pay['config']['payment_type'] = 1; //支付类型。默认值为：1（商品购买）。
        $pay['config']['service'] = 'mobile.securitypay.pay';
        $pay['config']['_input_charset'] = 'utf-8';

        // $dev_type = strim($_REQUEST['sdk_type']);
        //    if($dev_type == 'ios'){
        //IOS 需要原始的参数和加密过的字符串
        //1.去查询订单信息
        // $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_id."'");
        // print_r($payment_notice);die;
        //2.加密字符串
        $tmpArr['funcode'] = 'WP001';
        $tmpArr['version'] = '1.0.0';
        $tmpArr['appId'] = $this->appId;
        $tmpArr['mhtOrderNo'] = $payment_notice['notice_sn'];
        $tmpArr['mhtOrderName'] = $title_name;
        $tmpArr['mhtOrderType'] = $this->mhtOrderType;
        $tmpArr['mhtCurrencyType'] = $this->mhtCurrencyType;
        $tmpArr['mhtOrderTimeOut'] = $this->mhtOrderTimeOut;
        $tmpArr['mhtOrderAmt'] = $payment_notice['money'] * 100; //单位为 分，处理成 元
        $tmpArr['mhtOrderDetail'] = msubstr($title_name, 0, 40);
        $tmpArr['mhtOrderStartTime'] = date('YmdHis', time());
        $tmpArr['notifyUrl'] = SITE_DOMAIN . $this->notifyUrl;
        $tmpArr['mhtCharset'] = $this->mhtCharset;
        $tmpArr['payChannelType'] = $this->payChannelType;
        $tmpArr['deviceType'] = '01';
        $tmpArr['mhtSignType'] = $this->mhtSignType;

        //3.按键值升序排序，处理成表单字符串
        ksort($tmpArr);
        $tmpStr = '';
        foreach ($tmpArr as $k => $v) {
            $tmpStr .= $k . '=' . $v . '&';
        }

        //IOS需要未加密的字符串
        $res['tmpStr'] = rtrim($tmpStr, '&');

        //4.对密钥进行加密
        $privateKey = MD5($this->privateKey);

        //5.最后对 第一步中得到的表单字符串&第二步得到的密钥 MD5 值 做 MD5 签名
        $resStr = MD5($tmpStr . $privateKey);
        $res['Signature'] = $resStr;

        $res['total_fee'] = $payment_notice['money'];
        // }else{
        //安卓只需要几个参数
        //     $res['appId']              = $this->appId;
        //     $res['notifyUrl']          = SITE_DOMAIN.$this->notifyUrl;
        //     $res['payment_notice_id']  = $payment_notice_id;
        // }

        //公共需要的
        $res['privateKey'] = $this->privateKey;

        $pay['sdk_code'] = array("pay_sdk_type" => "ipaynow", "config" => $res);
        return $pay;
    }

    public function notify($request)
    {
        //引入文件
        fanwe_require(APP_ROOT_PATH . "system/payment/Nowpay/utils/Log.php");
        fanwe_require(APP_ROOT_PATH . "system/payment/Nowpay/services/Services.php");

        //处理验证参数
        $request = file_get_contents('php://input');
        // Log::outLog("通知接口", $request);

        parse_str($request, $request_form);
        // file_put_contents('request.txt',json_encode($request_form));
        if ($request_form['tradeStatus'] == 'A001') {
            /**
             * 在这里对数据进行处理
             */
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $request_form['mhtOrderNo'] . "'");
            $outer_notice_sn = $request_form['nowPayOrderNo'];
            require_once APP_ROOT_PATH . "system/libs/cart.php";
            $rs = payment_paid($payment_notice['notice_sn'], $outer_notice_sn);
            if ($rs) {
                echo "success=Y";
            }

        }

    }

    public function response($request)
    {

    }

    public function get_display_code()
    {
        # code...
    }
}
