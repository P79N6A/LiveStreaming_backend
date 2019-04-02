<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
// fanwe_require(APP_ROOT_PATH.'mapi/lib/login.action.php');

class loginCModule  extends baseModule
{
	//登录
	public function do_login(){
		$root = array('status' => 0,'error'=>'');
		if(!$_REQUEST)
		{
			app_redirect(APP_ROOT."/");
		}
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = strim($v);
		}
		fanwe_require(APP_ROOT_PATH."system/libs/user.php");
		$result = do_login_user($_REQUEST['mobile'],$_REQUEST['verify_coder']);
		if($result['status'])
		{
			$root['user_id'] = $result['user']['id'];
			$root['status'] = 1;

			if($result['user']['head_image']==''||$result['user_info']['head_image']==''){
				//头像
				$m_config =  load_auto_cache("m_config");//初始化手机端配置
				$system_head_image = $m_config['pc_default_headimg'];

				if($system_head_image==''){
					$system_head_image = './public/images/defaulthead.png';
					syn_to_remote_image_server($system_head_image);
				}
				
				$data = array(
					'head_image' => $system_head_image,
					'thumb_head_image' => get_spec_image($system_head_image,40,40),
					);

				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$result['user']['id']);

				fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$user_redis->update_db($result['user']['id'],$data);

				//更新session
				$user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $result['user']['id']);
				es_session::set("user_info", $user_info);
			}
			$root['is_lack'] = $result['is_lack'];//是否缺少用户信息
			$root['is_agree'] = intval($result['user']['is_agree']);//是否同意直播协议 0 表示不同意 1表示同意
			$root['user_id'] = intval($result['user']['id']);
			$root['nick_name'] = $result['user']['nick_name'];
			$root['family_id']=intval($result['user']['family_id']);
			$root['family_chieftain']=intval($result['user']['family_chieftain']);
			$root['error'] = "登录成功";
			$root['user_info'] = $result['user_info'];
		}
		else
		{
			$root['error'] = $result['info'];
		}
		api_ajax_return($root);
		
	}
	
	//退出
	public function logout(){

		fanwe_require(APP_ROOT_PATH."system/libs/user.php");
		$result = loginout_user();

		es_session::delete("user_info");
		$root['status'] = 1;
		$root['error'] = "登出成功";

		api_ajax_return($root);
	}
	//发送手机验证码
	function send_mobile_verify(){
		$mobile = addslashes(htmlspecialchars(trim($_REQUEST['mobile'])));

		if(app_conf("SMS_ON")==0)
		{
			$root['status'] = 0;
			$root['error'] = "短信未开启";
			ajax_return($root);
		}

		if($mobile == '')
		{
			$root['status'] = 0;
			$root['error'] = "请输入你的手机号";
			ajax_return($root);
		}

		if(!check_mobile($mobile))
		{
			$root['status'] = 0;
			$root['error'] = "请填写正确的手机号码";
			ajax_return($root);
		}

		//添加：手机发送 防护
		$root = check_sms_send($mobile);
		if ($root['status'] == 0){
			ajax_return($root);
		}
		
		$result = array("status"=>1,"info"=>'');


		if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
		{
			$root['status'] = 0;
			$root['error'] = "发送速度太快了";
			ajax_return($root);
		}

		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and client_ip='".get_client_ip()."' and create_time>=".(get_gmtime()-60)." ORDER BY id DESC") > 0)
		{
			$root['status'] = 0;
			$root['error'] = "发送速度太快了";
			ajax_return($root);
		}
		$n_time=get_gmtime()-300;
		//删除超过5分钟的验证码
		$GLOBALS['db']->query("DELETE FROM ".DB_PREFIX."mobile_verify_code WHERE create_time <=".$n_time);
		//开始生成手机验证
		
		$code = rand(1000,9999);
		$GLOBALS['db']->autoExecute(DB_PREFIX."mobile_verify_code",array("verify_code"=>$code,"mobile"=>$mobile,"create_time"=>get_gmtime(),"client_ip"=>get_client_ip()),"INSERT");

		send_verify_sms($mobile,$code);
		$status = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_msg_list where dest = '".$mobile."' and code='".$code."'");

		if($status['is_success']){
			$root['status'] = 1;
			$root['time'] = 60;
			$root['error'] = $status['title'].$status['result'];
		}else{
			$root['status'] = 0;
			$root['time'] = 0;
			$root['error'] = "短信验证码发送失败";
		}

		api_ajax_return($root);
	}
	//微信登录
	public function wx_login(){
		
		$root = array('status'=>1,'error'=>'');
		$is_weixin=isWeixin();
		$is_weixin = 1;
		$code = $_REQUEST['code'];
		if(!$is_weixin){
            $root['status'] = 0;
            $root['error'] = '请使用微信扫描';
        }
		$m_config =  load_auto_cache("m_config");//手机端配置
		fanwe_require(APP_ROOT_PATH.'system/utils/weixin.php');
		$class = strtolower(strim($_REQUEST['ctl']))?strtolower(strim($_REQUEST['ctl'])):"index";
		$act2 = strtolower(strim($_REQUEST['act']))?strtolower(strim($_REQUEST['act'])):"index";
		$current_url =  url_mapi($class."#".$act2);
		if($is_weixin){
			$wx_status = (($m_config['wx_appid']&&$m_config['wx_secrit']))?1:0;
			if($_REQUEST['code']&&$_REQUEST['state']==1&&$wx_status){
			
				$weixin=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],get_domain().'/mapi'.$current_url.'&itype=app');
				$wx_info=$weixin->scope_get_userinfo($_REQUEST['code']);
			  	if($wx_info['errcode']>0){
					var_dump($wx_info);exit;
				}
				fanwe_require(APP_ROOT_PATH."system/libs/user.php");
				$root = wxxMakeUser($wx_info);
			}else{
		  		if($wx_status){
					$weixin_2=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],get_domain().'/mapi'.$current_url.'&itype=app');
	 				$wx_url=$weixin_2->scope_get_code();
	 				app_redirect($wx_url);
				}
		 	}
		 	
		 	if($wx_status){
				require_once APP_ROOT_PATH."system/utils/jssdk.php";
				
				$jssdk = new JSSDK($m_config['wx_appid'],$m_config['wx_secrit'],get_domain().'/mapi'.$current_url.'&itype=app');
				$signPackage = $jssdk->getSignPackage();
				$root['signPackage'] =$signPackage;
			}
		}
		api_ajax_return($root);
	}

	//微信登录
	public function weixin_login()
	{
		$state = intval($_REQUEST['pid']);

		$last = pathinfo($_SERVER["HTTP_REFERER"]);
		$back_url = SITE_DOMAIN.url("login#weixin_login_callback", array('last' => urlencode($last['basename'])));

		$m_config =  load_auto_cache("m_config");
		$root = array('status'=>1,'error'=>'');
		$root['appid'] = $m_config['wx_web_appid'];
		$root['back_url'] = urlencode($back_url);
		$root['state'] = $state;
		api_ajax_return($root);
	}

	//微信登录回调
	public function weixin_login_callback(){
		$code = strim($_REQUEST['code']);
		if(!$code) {
			$root = array('status' => 0, 'error' => '参数为空');
			api_ajax_return($root);
		}

		fanwe_require(APP_ROOT_PATH."system/utils/weixin.php");
		$m_config =  load_auto_cache("m_config");//初始化手机端配置

		$wx_appid = strim($m_config['wx_web_appid']);
		$wx_secrit = strim($m_config['wx_web_secrit']);
		//获取微信配置信息
		if($wx_appid==''||$wx_secrit==''){
			$root['status'] = 0;
			$root['error'] = "wx_appid或wx_secrit不存在";
			api_ajax_return($root);
		}

		$jump_url = SITE_DOMAIN.url("login#weixin_login_callback");

		$weixin=new weixin($wx_appid,$wx_secrit,$jump_url);
		if($_REQUEST['code']!=""){
			$wx_info = $weixin->scope_get_userinfo($code);
			fanwe_require(APP_ROOT_PATH."system/libs/user.php");
			$root = wxxMakeUser($wx_info);
			if(!$root['status']){
				api_ajax_return($root);
			}
		}else{
			$root['status'] = 0;
			$root['error'] = "微信登录失败";
			api_ajax_return($root);
		}

		$last = pathinfo(urldecode($_REQUEST['last']));
		app_redirect(empty($last) ? url('index#index') : $last['basename']);
	}

	//QQ登录
	public function qq_login(){
		fanwe_require(APP_ROOT_PATH."system/QQloginApi/qqConnectAPI.php");
		$qc = new QC();
		$last = pathinfo($_SERVER["HTTP_REFERER"]);
		$back_url = SITE_DOMAIN.url("login#qq_login_callback", array('last' => urlencode($last['basename'])));
		$qc->qq_login($back_url);
	}

	//QQ登录回调
	function qq_login_callback(){
		$m_config =  load_auto_cache("m_config");//初始化手机端配置

		$last = pathinfo(urldecode($_REQUEST['last']));
		$back_url = SITE_DOMAIN.url("login#qq_login_callback", array('last' => $last));
		fanwe_require(APP_ROOT_PATH."system/QQloginApi/qqConnectAPI.php");
		$qc = new QC();
		$access_token  = $qc->qq_callback($back_url);
		$openid = $qc->get_openid();
		$ret = $qc->get_pc_user_info($access_token, $openid);
		$ret['openid'] = $openid;
		fanwe_require(APP_ROOT_PATH."system/libs/user.php");
		$root = qqMakeUser($ret);
		if($root['status']){
			app_redirect(empty($last) ? url('index#index') : $last['basename']);
		}else{
			$root['basename']=$last['basename'];
			app_redirect( url('error#no_page',$root));
		}

	}
	//sina登录
	public function sina_login(){
		$m_config =  load_auto_cache("m_config");//初始化手机端配置

		if($m_config['sina_web_app_key']==''||$m_config['sina_web_app_secret']==''){
			$root['status'] = 0;
			$root['error'] = "sina_web_app_key或sina_web_app_secret不存在";
			ajax_return($root);
		}

		fanwe_require(APP_ROOT_PATH."system/WBloginApi/saetv2.ex.class.php");
		$o = new SaeTOAuthV2($m_config['sina_web_app_key'],$m_config['sina_web_app_secret']);

		$last = pathinfo($_SERVER["HTTP_REFERER"]);
		$app_url = SITE_DOMAIN.url("login#sina_login_callback", array('last' => urlencode($last['basename'])));
		$aurl = $o->getAuthorizeURL($app_url);
		app_redirect($aurl);
	}
	//sina登录回调
	public function sina_login_callback()
	{
		$m_config = load_auto_cache("m_config");//初始化手机端配置
		$code = trim($_REQUEST['code']);
		if ($code == '') {
			$root['status'] = 0;
			$root['error'] = "code不存在";
			ajax_return($root);
		}
		fanwe_require(APP_ROOT_PATH . "system/WBloginApi/saetv2.ex.class.php");

		if ($m_config['sina_web_app_key'] == '' || $m_config['sina_web_app_secret'] == '') {
			$root['status'] = 0;
			$root['error'] = "sina_web_app_key或sina_web_app_secret不存在";
			ajax_return($root);
		}

		$o = new SaeTOAuthV2($m_config['sina_web_app_key'], $m_config['sina_web_app_secret']);
		$keys = array();
		$keys['code'] = $code;
		$keys['redirect_uri'] = SITE_DOMAIN . url("login#sina_login_callback");
		$token = $o->getAccessToken('code', $keys);
		if (!$token['access_token']) {
			$root['status'] = 0;
			$root['error'] = "access_token不存在";
			ajax_return($root);
		}

		$c = new SaeTClientV2($m_config['sina_web_app_key'], $m_config['sina_web_app_secret'], $token['access_token']);
		$uid_get = $c->get_uid();
		$uid = $uid_get['uid'];
		$user_message = $c->show_user_by_id($uid);//根据ID获取用户等基本信息
		$user_message['sina_id'] = $uid;
		fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
		$root=sinaMakeUser($user_message);
		$last = pathinfo(urldecode($_REQUEST['last']));
		if($root['status']){
			app_redirect(empty($last) ? url('index#index') : $last['basename']);
		}else{
			$root['basename']=$last['basename'];
			app_redirect( url('error#no_page',$root));
		}
	}
		
	//弹窗登录
	public function pop(){
		$root = array('status'=>1,'error'=>'');
		api_ajax_return($root);
	}
}


?>
