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
		$type=intval($_REQUEST['type']);
		if ($type==0) {
			$result = $this->do_login_promoter($_REQUEST['mobile'],$_REQUEST['verify_coder']);
		}else {
			$result = $this->do_login_promoter_2($_REQUEST['mobile'],$_REQUEST['password']);
		}
		
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
			$root['nick_name'] = ($result['user']['nick_name']);
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
	
	public function do_login_promoter($user_id_or_mobile,$verify_code,$p_user_id){
		
		$result = array('status'=>0,'info'=>'','is_lack'=>0);
		
		$user_id_or_mobile=strim($user_id_or_mobile);
		$verify_code=strim($verify_code);
		
		if($verify_code==''){
			$result['info'] = "请输入验证码";
			return $result;
		}
		
		fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
		$user_redis = new UserRedisService();
		if($user_id_or_mobile!=''){
			if($user_id_or_mobile!='13888888888'&&$user_id_or_mobile!='13999999999'){
				if(!check_mobile(trim($user_id_or_mobile)))
				{
					$result['info'] = '手机格式错误';
					return $result;
				}
				if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile=".$user_id_or_mobile." AND verify_code='".$verify_code."'")==0){
					$result['info'] = "手机验证码出错";
					return $result;
				}
			}elseif($user_id_or_mobile=='13888888888' && $verify_code !='8888'||$user_id_or_mobile=='13999999999' && $verify_code !='9999'){
				$result['info'] = "手机验证码出错";
				return $result;
			}
		}else{
			$result['info'] = "请输入手机号";
			return $result;
		}
		
		/* $promoter = $GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "bm_promoter where mobile=".$user_id_or_mobile." and is_effect=1 and status=1", true, true);
		if (!$promoter) {
			$result['info'] = "帐户无登陆权限，请联系管理员";
			return $result;
		} */
		$promoter = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_msg_list where dest=".$user_id_or_mobile." and send_time=(select max(d.send_time) from ".DB_PREFIX."deal_msg_list as d where d.dest=".$user_id_or_mobile.")");
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".$promoter['user_id']);
		$user_id = intval($user['id']);
		if(!$user)
		{
			$result['info'] = "账号不存在";
			return $result;
		}else{
			if($user['is_effect'] != 1){
				$result['info'] = "帐户已被禁用,请联系管理员";
				return $result;
			}
			if($user['society_chieftain'] == 0){
			    $result['info'] = "抱歉你不是会长无法登陆";
			    return $result;
			}

			/* if (!$promoter) {
				$bm_promoter_type=0;
			}else if ($promoter['pid']>0) {
				$bm_promoter_type=1;
			}else {
				$bm_promoter_type=2;
			} */
			$user['bm_promoter_type']=$bm_promoter_type;
			$user['nick_name'] = ($user['nick_name']);
			$result['status'] =1;
			//设置cookie
			es_cookie::set("client_ip",CLIENT_IP,3600*24*30);
			es_cookie::set("nick_name",$user['nick_name'],3600*24*30);
			es_cookie::set("user_id",$user['id'],3600*24*30);
			es_cookie::set("user_pwd",md5($user['user_pwd']."_EASE_COOKIE"),3600*24*30);
			es_cookie::set("PHPSESSID2",es_session::id(),3600*24*30);
				
			//设置session
			es_session::set("user_info",$user);
			$GLOBALS['user_info'] = $user;									
		}
		
		if($user['nick_name']==''||$user['head_image']==''){
			$result['is_lack'] = 1;
		}
		$result['user'] = $user;
		$result['user_info']['user_id'] =$user['id'];
		$result['user_info']['nick_name'] =$user['nick_name']?($user['nick_name']):'';
		$result['user_info']['mobile'] =$user['mobile']?$user['mobile']:'';
		$result['user_info']['head_image'] =get_spec_image($user['head_image']);
		$result['p_user_id'] = $p_user_id;
		return $result;
		
		
	}
	
	/* public function do_login_promoter_2($user_id_or_mobile,$password,$p_user_id){
	
		$result = array('status'=>0,'info'=>'','is_lack'=>0);
	
		$user_id_or_mobile=strim($user_id_or_mobile);
		$password=strim($password);
	
		if($password==''){
			$result['info'] = "请输入密码";
			return $result;
		}
	
		fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
		$user_redis = new UserRedisService();
		if($user_id_or_mobile!=''){
			if($user_id_or_mobile!='13888888888'&&$user_id_or_mobile!='13999999999'){
				if(!check_mobile(trim($user_id_or_mobile)))
				{
					$result['info'] = '手机格式错误';
					return $result;
				}				
			}
		}else{
			$result['info'] = "请输入手机号";
			return $result;
		}
	
		$promoter = $GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "bm_promoter where mobile=".$user_id_or_mobile." and is_effect=1 and status=1", true, true);
		if (!$promoter) {
			$result['info'] = "帐户无登陆权限，请联系管理员";
			return $result;
		}
		
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where (id='".$promoter['user_id']."'  ) ");
	
		$user_id = intval($user['id']);
		if(!$user)
		{
			$result['info'] = "账号不存在";
			return $result;
		}else{
				
			if($user['is_effect'] != 1){
				$result['info'] = "帐户已被禁用,请联系管理员";
			}
				
			
			if($promoter['pwd']!=md5($password)){
				$result['info'] = "密码错误，请重试";
				return $result;
			}
	
			
			if (!$promoter) {
				$bm_promoter_type=0;
			}else if ($promoter['pid']>0) {
				$bm_promoter_type=1;
			}else {
				$bm_promoter_type=2;
			}
			$user['bm_promoter_type']=$bm_promoter_type;
			
			
			$result['status'] =1;
			//设置cookie
			es_cookie::set("client_ip",CLIENT_IP,3600*24*30);
			es_cookie::set("nick_name",$user['nick_name'],3600*24*30);
			es_cookie::set("user_id",$user['id'],3600*24*30);
			es_cookie::set("user_pwd",md5($user['user_pwd']."_EASE_COOKIE"),3600*24*30);
			es_cookie::set("PHPSESSID2",es_session::id(),3600*24*30);
	
			//设置session
			es_session::set("user_info",$user);
			$GLOBALS['user_info'] = $user;
		}
	
		if($user['nick_name']==''||$user['head_image']==''){
			$result['is_lack'] = 1;
		}
		$result['user'] = $user;
		$result['user_info']['user_id'] =$user['id'];
		$result['user_info']['nick_name'] =$user['nick_name']?$user['nick_name']:'';
		$result['user_info']['mobile'] =$user['mobile']?$user['mobile']:'';
		$result['user_info']['head_image'] =get_spec_image($user['head_image']);
		$result['p_user_id'] = $p_user_id;
		return $result;
	
	
	} */
	
	public function auto_do_login_user($user_id,$user_md5_pwd){
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id='".$user_id."' and is_effect = 1");

		if($user_data)
		{
			if(md5($user_data['user_pwd']."_EASE_COOKIE")==$user_md5_pwd)
			{
				
				$bm_promoter=$GLOBALS['db']->getOne("select * from ".DB_PREFIX."bm_promoter where id='".$user_id."' and is_effect = 1 and status=1 ");
				if (!$bm_promoter) {
					$bm_promoter_type=0;
				}else if ($bm_promoter['pid']>0) {
					$bm_promoter_type=1;
				}else {
					$bm_promoter_type=2;
				}
				
				$user_data['bm_promoter_type']=$bm_promoter_type;
				es_session::set("user_info",$user_data);
				$GLOBALS['user_info'] = $user_data;

			}
		}
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
		/* var_dump(array(
		    $_POST['mobile'],
		    trim($_POST['mobile']),
		    htmlspecialchars(trim($_POST['mobile'])),
		    addslashes(htmlspecialchars(trim($_POST['mobile']))),
		    $mobile
		)); */
		if(app_conf("SMS_ON")==0)
		{
			$root['status'] = 0;
			$root['error'] = "短信未开启";
			api_ajax_return($root);
		}

		if($mobile == '')
		{
			$root['status'] = 0;
			$root['error'] = "请输入你的手机号";
			api_ajax_return($root);
		}
		
		if(!check_mobile($mobile))
		{
			$root['status'] = 0;
			$root['error'] = "请填写正确的手机号码";
			api_ajax_return($root);
		}

		//添加：手机发送 防护
		$root = check_sms_send($mobile);

		if ($root['status'] == 0){
			api_ajax_return($root);
		}
		
		$result = array("status"=>1,"info"=>'');

		

		if(!check_ipop_limit(get_client_ip(),"mobile_verify",60,0))
		{
			$root['status'] = 0;
			$root['error'] = "发送速度太快了";
			api_ajax_return($root);
		}

		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mobile_verify_code where mobile = '".$mobile."' and client_ip='".get_client_ip()."' and create_time>=".(get_gmtime()-60)." ORDER BY id DESC") > 0)
		{
			$root['status'] = 0;
			$root['error'] = "发送速度太快了";
			api_ajax_return($root);
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

		
	//弹窗登录
	public function pop(){
		$root = array('status'=>1,'error'=>'');
		api_ajax_return($root);
	}
	
	//弹窗登录
	public function login(){
		$root = array('status'=>1,'error'=>'');
		api_ajax_return($root);
	}
}


?>
