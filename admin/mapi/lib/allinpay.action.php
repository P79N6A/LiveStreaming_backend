<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class allinpayModule  extends baseModule
{
    /**
     * 用户充值界面
     */
    public function recharge(){

        $root = array();
        $root['status'] = 1;

        //$GLOBALS['user_info']['id'] = 320;
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else {
            $root['form_url'] = SITE_DOMAIN.'/mapi/index.php?ctl=allinpay&act=pay';
            $root['receiveUrl'] = SITE_DOMAIN.'/callback/payment/allinpay_notify.php';

        }
        api_ajax_return($root);
    }

    /**
     * 用户充值支付
     */
    public function pay(){

        $root = array();
        $root['status'] = 1;
        //$GLOBALS['user_info']['id'] = 1;
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{

        }

        api_ajax_return($root);
    }

    /**
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     *
    Array
    (
    [receipt] => Array
    (
    [original_purchase_date_pst] => 2016-07-30 02:53:14 America/Los_Angeles
    [purchase_date_ms] => 1469872394716
    [unique_identifier] => 062dcbb2491a2269fef255dc4edc9628e53796a9
    [original_transaction_id] => 1000000226680150
    [bvrs] => 4.1
    [transaction_id] => 1000000226680150
    [quantity] => 1
    [unique_vendor_identifier] => AAFA3025-9E75-4669-B80E-CEA9DD71E73F
    [item_id] => 1139138732
    [product_id] => 100001
    [purchase_date] => 2016-07-30 09:53:14 Etc/GMT
    [original_purchase_date] => 2016-07-30 09:53:14 Etc/GMT
    [purchase_date_pst] => 2016-07-30 02:53:14 America/Los_Angeles
    [bid] => com.fanwe.live
    [original_purchase_date_ms] => 1469872394716
    )

    [status] => 0
    )
    );
     */

    private function acurl($receipt_data,$sandbox) {
        //正式购买地址 沙盒购买地址
        $url_buy     = "https://buy.itunes.apple.com/verifyReceipt";
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $url = $sandbox ? $url_sandbox : $url_buy;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("receipt-data" => $receipt_data)));//$this->encodeRequest());
        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $errmsg   = curl_error($ch);
        curl_close($ch);
        if ($errno != 0) {
            //throw new Exception($errmsg, $errno);
            $data = array();
            $data['status'] = $errno;
            $data['error'] = $errmsg;

            return $data;
        }else{
            return json_decode($response,1);
        }
    }




}