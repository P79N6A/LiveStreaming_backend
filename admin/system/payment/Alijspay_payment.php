<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'支付宝WAP支付',
	'merchantaccount'	=>	'商户编号',
	'merchantPrivateKey'=>	'商户密钥',
);

$config = array(
	'merchantaccount'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户编号
	'merchantPrivateKey'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户私钥
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Alijspay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


	/* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app*/
    $module['online_pay'] = '4';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'http://www.alipay.com/';
    
    return $module;
}

// 易宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Alijspay_payment implements payment {
	
	public function get_payment_code($payment_notice_id)
	{
		/*$pay = array();
		$pay['is_wap'] = 1;//
		$pay['url'] =SITE_DOMAIN.APP_ROOT.'/mapi/index.php?ctl=pay&act=get_display_code&pay_code=Alijspay&notice_id='.$payment_notice_id;
		return $pay;*/

		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		$order_sn = $payment_notice['notice_sn'];
		$user_id = $payment_notice['user_id'];

		require_once(APP_ROOT_PATH.'system/payment/Alijspay/request.php');

		$alijspay = new Request($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPrivateKey']);

		$out_trade_no = $payment_notice['notice_sn'];//网页支付的订单在订单有效期内可以进行多次支付请求，但是需要注意的是每次请求的业务参数都要一致，交易时间也要保持一致。否则会报错“订单与已存在的订单信息不符”
		$body = $payment_notice['recharge_name']!=''?'购买：'.$payment_notice['recharge_name']:'购买秀豆：'.$payment_notice['diamonds'];//商品描述
		$total_fee = $money * 100;//订单金额单位为分
		$mch_create_ip = CLIENT_IP; //此参数不是固定的商户服务器ＩＰ，而是用户每次支付时使用的网络终端IP，否则的话会有不友好提示：“检测到您的IP地址发生变化，请注意支付安全”。
		$buyer_logon_id = '';
		$buyer_id = '2088012499266531';

		$date = array();
		$date['out_trade_no'] = $out_trade_no;
		$date['body'] = $body;
		$date['total_fee'] = intval($total_fee);
		$date['mch_create_ip'] = $mch_create_ip;
		$date['buyer_logon_id'] = $buyer_logon_id;
		$date['buyer_id'] = $buyer_id;
		log_file($date,'display_code');
		$result = $alijspay->submitOrderInfo($date);
		//$result['pay_info']=
		//$result['pay_url']=https://pay.swiftpass.cn/pay/prepay?token_id=fdb84ed5bca5dedaa76e4a59e2e398e4&trade_type=pay.alipay.jspayv3
		$pay = array();
		$pay['is_wap'] = 1;//
		$pay['url'] = $result['pay_url'];
		return $pay;
	}

	public function response($request)
	{

		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Yjwap'");
		$payment_info['config'] = unserialize($payment['config']);

		require_once(APP_ROOT_PATH.'system/payment/yeepay/yeepayMPay.php');

		/**
		 *此类文件是有关回调的数据处理文件，根据易宝回调进行数据处理

		 */
		$yeepay = new yeepayMPay($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPublicKey'],$payment_info['config']['merchantPrivateKey'],$payment_info['config']['yeepayPublicKey']);
		try {

			$return = $yeepay->callback($request['data'], $request['encryptkey']);

			// TODO:添加订单处理逻辑代码
			/*
            名称 	中文说明 	数据类型 	描述
            merchantaccount 	商户账户 	string
            yborderid 	易宝交易流水号 	string
            orderid 	交易订单 	String
            amount 	支付金额 	int 	以“分”为单位的整型
            bankcode 	银行编码 	string 	支付卡所属银行的编码，如ICBC
            bank 	银行信息 	string 	支付卡所属银行的名称
            cardtype 	卡类型 	int 	支付卡的类型，1为借记卡，2为信用卡
            lastno 	卡号后4位 	string 	支付卡卡号后4位
            status 	订单状态 	int 	1：成功
            */

			$payment_notice_sn = $return['orderid'];
			$money = intval($return['amount']/100);
			$outer_notice_sn = $return['yborderid'];

			if ($return['status'] == 1){
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
				require_once APP_ROOT_PATH."system/libs/cart.php";
				$rs = payment_paid($payment_notice['id'],$outer_notice_sn);
				if($rs)
				{
					showIpsInfo("支付成功");
				}
				else
				{
					showIpsInfo("支付完成");
				}
			}else{
				showIpsInfo("支付失败");
			}
		}catch (yeepayMPayException $e) {
			// TODO：添加订单支付异常逻辑代码
			showIpsInfo("验证失败");
		}
	}

	public function notify($request)
	{

		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Yjwap'");
		$payment_info['config'] = unserialize($payment['config']);

		include("yeepay/yeepayMPay.php");

		/**
		 *此类文件是有关回调的数据处理文件，根据易宝回调进行数据处理

		 */
		$yeepay = new yeepayMPay($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPublicKey'],$payment_info['config']['merchantPrivateKey'],$payment_info['config']['yeepayPublicKey']);
		try {
			//echo $request['data']."<br>";
			//echo $request['encryptkey']."<br>";
			$return = $yeepay->callback($request['data'], $request['encryptkey']);
			//print_r($return);

			//file_put_contents("./log/yjwap2_notify_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($return,true));
			// TODO:添加订单处理逻辑代码
			/*
            名称 	中文说明 	数据类型 	描述
            merchantaccount 	商户账户 	string
            yborderid 	易宝交易流水号 	string
            orderid 	交易订单 	String
            amount 	支付金额 	int 	以“分”为单位的整型
            bankcode 	银行编码 	string 	支付卡所属银行的编码，如ICBC
            bank 	银行信息 	string 	支付卡所属银行的名称
            cardtype 	卡类型 	int 	支付卡的类型，1为借记卡，2为信用卡
            lastno 	卡号后4位 	string 	支付卡卡号后4位
            status 	订单状态 	int 	1：成功
            */

			$payment_notice_sn = $return['orderid'];
			$money = intval($return['amount']/100);
			$outer_notice_sn = $return['yborderid'];

			if ($return['status'] == 1){
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
				$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
				require_once APP_ROOT_PATH."system/libs/cart.php";
				$rs = payment_paid($payment_notice['id'],$outer_notice_sn);

				if($rs)
				{
					echo 'success';

				}
				else
				{
					echo 'fail';
				}
			}else{
				echo 'fail';
			}
		}catch (yeepayMPayException $e) {
			// TODO：添加订单支付异常逻辑代码
			echo 'fail';
		}
	}

	function get_display_code(){

	}

	public function display_code($payment_notice_id)
	{
		if($payment_notice_id){
			////
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
			$money = round($payment_notice['money'],2);
			$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
			$payment_info['config'] = unserialize($payment_info['config']);

			$order_sn = $payment_notice['notice_sn'];
			$user_id = $payment_notice['user_id'];

			require_once(APP_ROOT_PATH.'system/payment/Alijspay/request.php');

			$alijspay = new Request($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPrivateKey']);

			$out_trade_no = $payment_notice['notice_sn'];//网页支付的订单在订单有效期内可以进行多次支付请求，但是需要注意的是每次请求的业务参数都要一致，交易时间也要保持一致。否则会报错“订单与已存在的订单信息不符”
			$body = $payment_notice['recharge_name']!=''?'购买：'.$payment_notice['recharge_name']:'购买秀豆：'.$payment_notice['diamonds'];//商品描述
			$total_fee = $money * 100;//订单金额单位为分
			$mch_create_ip = CLIENT_IP; //此参数不是固定的商户服务器ＩＰ，而是用户每次支付时使用的网络终端IP，否则的话会有不友好提示：“检测到您的IP地址发生变化，请注意支付安全”。
			$buyer_logon_id = '';
			$buyer_id = '2088012499266531';

			$date = array();
			$date['out_trade_no'] = $out_trade_no;
			$date['body'] = $body;
			$date['total_fee'] = intval($total_fee);
			$date['mch_create_ip'] = $mch_create_ip;
			$date['buyer_logon_id'] = $buyer_logon_id;
			$date['buyer_id'] = $buyer_id;
			log_file($date,'display_code');
			$alijspay->submitOrderInfo($date);
		}
		else
		{
			return '';
		}

	}

}
?>