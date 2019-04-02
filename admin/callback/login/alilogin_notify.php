<?php
require '../../system/mapi_init.php';
$m_config =  load_auto_cache("m_config");//初始化手机端配置
require_once(APP_ROOT_PATH . 'system/AlipayloginApi/aliConnectAPI.php');
$aliConnect = new aliConnectAPI($m_config['alipay_partner'],$m_config['alipay_key']);			
?>
<!DOCTYPE HTML>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$verify_result = $aliConnect->verifyreturn();
if($verify_result) {//验证成功
	fanwe_require(APP_ROOT_PATH."mapi/lib/base.action.php");
	fanwe_require(APP_ROOT_PATH."mapi/lib/user_center.action.php");
	
	$user_center = new user_centerModule();
	$user_center->authent_alipay($_REQUEST);
}else {
    //验证失败
    echo "验证失败";
}
?>
    <title>支付宝快捷登录接口</title>
	</head>
    <body>
    </body>
</html>

