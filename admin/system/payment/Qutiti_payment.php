<?php

$payment_lang = array(
    'name' => 'Qutiti支付',
    'cusid' => '商户号(平台分配)',
    'key' => '密钥KEY(平台分配)',
);

$config = array(
    'cusid' => array(
        'INPUT_TYPE' => '0',
    ),

    'key' => array(
        'INPUT_TYPE' => '0'
    ),
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true) {
    $module['class_name'] = 'Qutiti';

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

// 支付宝手机支付模型
require_once(APP_ROOT_PATH . 'system/libs/payment.php');

class Qutiti_payment implements payment
{
    /*
     * 接收从/mapi/app/pay.action.php发来的请求
     * */
    public function get_payment_code($payment_notice_id)
    {
        $pay = array();
        $pay['is_wap'] = 1;//
        $pay['class_name'] = "Qutiti";
        $pay['is_without'] = 1;
        $pay['url'] = SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Qutiti&notice_id=' . $payment_notice_id;
        $pay['sdk_code'] = array("pay_sdk_type" => "zfbwap2", "config" =>
            array(
                "url" => SITE_DOMAIN . APP_ROOT . '/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Qutiti&notice_id=' . $payment_notice_id,
                "is_wap" => 1
            )
        );
        return $pay;

    }

    public function response($request)
    {
    }

    public function notify($data)
    {
        //log_result($data);
        //$data = urlencode($data);
        $payment = $GLOBALS['db']->getRow("select id,config from " . DB_PREFIX . "payment where class_name='Qutiti'");
        $payment['config'] = unserialize($payment['config']);

        $appKey = $payment['config']['key'];

        $params = array();
        foreach($data as $key=>$val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
            $params[$key] = $val;
        }
        if(count($params)<1){//如果参数为空,则不进行处理
            echo "FAIL";
            exit();
        }
        //log_result($params);
        if(ValidSign($params, $appKey)){//验签成功
            //log_result("验签成功");
            $payment_notice_sn = $data['prdOrdNo'];
            $outer_notice_sn = $data['payId'];
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where notice_sn = '" . $payment_notice_sn . "'");
            require_once APP_ROOT_PATH . "system/libs/cart.php";
            $rs = payment_paid($payment_notice['notice_sn'], $outer_notice_sn);
            //log_result("处理结果<br>".$rs);
            echo "SUCCESS";
        }
        else{
            echo "FAIL";
        }
    }

    function get_display_code()
    {

    }


    public function display_code($payment_notice_id)
    {
        if ($payment_notice_id) {
            $payment_notice = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "payment_notice where id = " . $payment_notice_id);
            $payment_info = $GLOBALS['db']->getRow("select id,config,logo from " . DB_PREFIX . "payment where id=" . intval($payment_notice['payment_id']));
            $payment_info['config'] = unserialize($payment_info['config']);
            $cusid = $payment_info['config']['cusid'];
            $appKey = $payment_info['config']['key'];
            $goodsName = "秀豆Diamonds";
            $asyn_notify_url = "http://" . $_SERVER["HTTP_HOST"] . "/callback/payment/qutiti_notify.php";
            $syn_notify_url = "http://" . $_SERVER["HTTP_HOST"] . "/callback/payment/qutiti_notify_syn.php";
            $money = round($payment_notice['money'], 2);
            $out_trade_no = $payment_notice['notice_sn'];


            $params = array();
            $params["versionId"] = '1.0';//服务版本号
            $params["orderAmount"] = $money*100;//注意单位是分
            $params["orderDate"] = date('YmdHis', time());//订单提交时间
            $params["currency"] = 'RMB';//货币类型
            $params["accountType"] = "1";//银行卡种类：0-借记卡 1-贷记卡
            $params["transType"] = "008";//交易类别
            $params["asynNotifyUrl"] = $asyn_notify_url;//异步通知url
            $params["synNotifyUrl"] = $syn_notify_url;//同步返回url
            $params["signType"] = "MD5";//加密方式
            $params["merId"] = $cusid;//商户号
            $params["prdOrdNo"] = $out_trade_no;//商户订单号
            $params["payMode"] = "0";//支付方式：0-收银台接口专用
            $params["prdName"] = $goodsName;//商品名称
            $params["prdDesc"] = "Vip";//商品描述
            $params["pnum"] = "1";//商品数量
            $params["signData"] = SignArray($params, $appKey);//签名

            $url = "http://online.qutiti.com/pay/payment/payapply";

            $payLinks = "<form id='payForm' name='payForm' action='".$url."' method='post'>";
            foreach ($params as $key => $val){
                $payLinks .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
            }
            $payLinks .= '</form>';
            $payLinks .= '<script type="text/javascript">document.getElementById("payForm").submit();</script>';
            //echo htmlentities($payLinks, ENT_QUOTES, "UTF-8");
            return $payLinks;

        } else {
            return '';
        }
    }
}

function SignArray(array $array,$appkey){
    ksort($array);
    $array['key'] = $appkey;//key不参与排序
    $blankStr = ToUrlParams($array);
    //var_dump($blankStr);die;
    $sign = strtoupper(md5($blankStr));
    return $sign;
}

function ToUrlParams(array $array)
{
    $buff = "";
    foreach ($array as $k => $v)
    {
        if($v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

/**
 * 校验签名
 * @param array 参数
 * @param unknown_type appkey
 */
function ValidSign(array $array,$appkey){
    $sign = $array['signData'];
    unset($array['signData']);
    $mySign = SignArray($array, $appkey);
    return strtoupper($sign) == $mySign;
}

?>