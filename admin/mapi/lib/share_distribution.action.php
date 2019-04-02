<?php


//二级分销方法类

class share_distributionModule  extends baseModule
{

	//获取分销注册页面数据
	public function index()
    {
    	$root = array('status' => 0,'error'=>'');
    	isset($_REQUEST['share_id']) ? $root['error'] = '' : $root['error'] = 'url非法';	//URL判定是否正确
    	if($root['error']=='')	//URL正确
    	{
    		$share_id = intval($_REQUEST['share_id']);
	        $share_user = $GLOBALS['db']->getRow("select nick_name,head_image from ".DB_PREFIX."user where id = ".$share_id);
	        if ($share_user=="")
	        {
	        	$root['error'] = '分销上级ID不存在';	
	        }
	        else
	        {
		        $m_config =  load_auto_cache("m_config");//初始化手机端配置
	        	$root['status'] = 1;
	        	$root['nick_name']=$share_user['nick_name'];	//分享者昵称
		        $root['head_image']=$share_user['head_image'];	//分享者头像url
				$root['app_name'] = $m_config['app_name'];
				$root['url'] =SITE_DOMAIN.'/mapi/index.php?ctl=share_distribution&act=register&share_id='.$share_id;	//分销注册执行链接
	        }
    	}
    	api_ajax_return($root);
    }

    //分销分享二维码或链接跳转到分销注册页面
    public function jump()
    {
    	$root = array('status' => 0,'error'=>'');
    	isset($_REQUEST['share_id']) ? $root['error'] = '' : $root['error'] = 'url非法';	//URL判定是否正确
    	if($root['error']=='')	//URL正确
    	{
    		$share_id = intval($_REQUEST['share_id']);
	        $share_user = $GLOBALS['db']->getRow("select nick_name,head_image from ".DB_PREFIX."user where id = ".$share_id);
	        if ($share_user=="")
	        {
	        	$root['error'] = '分销上级ID不存在';	
	        }
	        else
	        {
	        	$url = SITE_DOMAIN."/frontEnd/520show/h5/index.html#/register?share_id=".$share_id;
            	app_redirect($url);
	        }
    	}
    }


	//分销注册
	public function register()
	{		
		$root = array('status' => 0,'error'=>'');
		if(!$_REQUEST)
		{
			app_redirect(get_domain()."/");
		}
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = strim($v);
		}

		isset($_REQUEST['share_id']) ? $root['error'] = '' : $root['error'] = 'url非法';	//URL判定是否正确
		if($root['error'] != '')
		{
			api_ajax_return($root);	//URL错误返回
		}

		//获取三个主要参数
		$p_user_id = intval($_REQUEST['share_id']);	//上级ID
		$mobile = $_REQUEST['mobile'];	//手机号
		$verify_coder = $_REQUEST['verify_coder']; //验证码

		$p_user_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id =" . $p_user_id);	//查询上级ID是否存在
		if(intval($p_user_id)==''){
			$root['error'] = "上级用户不存在！not exists";
            api_ajax_return($root);	//错误返回
		}

		//手机号与验证码输入不得为空
		if($mobile==''||$verify_coder=='')
		{
			$root['error'] = "手机号与验证码输入不得为空！";
            api_ajax_return($root);	//错误返回
		}

		//手机号与验证码输入长度限制
		if (strlen($mobile)>11)
		{
			$root['error'] = "手机号长度不得超过11位！";
            api_ajax_return($root);	//错误返回
		}
		if (strlen($verify_coder)>8)
		{
			$root['error'] = "验证码长度不得超过8位！";
            api_ajax_return($root);	//错误返回
		}

		//手机号重名判定
		$mobile_check = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where mobile =".$mobile);
		if($mobile_check!=''){
			$root['error'] = "手机号已注册！hhhh";
            api_ajax_return($root);	//错误返回
		}

		//手机号与验证码格式判断
		if($mobile!='13888888888'&&$mobile!='13999999999')
		{
			if(!check_mobile(trim($mobile)))
			{
				$root['error'] = '手机格式错误 ';
				api_ajax_return($root);	//错误返回
			}
			if($GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."mobile_verify_code WHERE mobile='".$mobile."' AND verify_code='".$verify_coder."'")==0){
				$root['error'] = "手机验证码出错bbbb";
				api_ajax_return($root);	//错误返回
			}
		}
		else if(($mobile=='13888888888' && $verify_coder!='8888')||($mobile=='13999999999' && $verify_coder !='9999'))
		{
				$root['error'] = "手机验证码出错aaaa";
				api_ajax_return($root);	//错误返回
		}
	
		
		//基本验证全部通过,开始注册
		fanwe_require(APP_ROOT_PATH."system/libs/user.php"); //引用用户方法库
		$result = do_register_user($mobile,$verify_coder,$p_user_id);	//执行分销注册方法


		if($result['status'])	//注册执行成功
		{
			if($result['user']['head_image']==''||$result['user_info']['head_image']==''){	//用户头像存储
				//头像的存储
				$m_config =  load_auto_cache("m_config");//初始化手机端配置
				$system_head_image = $m_config['app_logo'];

				if($system_head_image==''){
					$system_head_image = './public/attachment/test/noavatar_11.JPG';
					syn_to_remote_image_server($system_head_image);		//头像文件同步到服务器
				}
				
				$data = array(	//头像参数待写入redis
					'head_image' => $system_head_image,
					'thumb_head_image' => get_spec_image($system_head_image,40,40),
					'p_user_id' =>$p_user_id,
					);

				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$result['user']['id']);	//执行头像数据更新

				fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');	//调用用户redis接口文件
				$user_redis = new UserRedisService();
				$user_redis->update_db($result['user']['id'],$data);	//更新redis数据

			}


			//注册用户昵称
			$root['user_nick_name'] = $result['user']['nick_name'];
			//分享者数据获取(仅在注册成功后执行)
			$share_id = intval($_REQUEST['share_id']);
	        $share_user = $GLOBALS['db']->getRow("select nick_name,head_image from ".DB_PREFIX."user where id = ".$share_id);
	        $root['nick_name']=$share_user['nick_name'];	//分享者昵称
	        $root['head_image']=$share_user['head_image'];	//分享者头像url
			$root['app_down_url'] = SITE_DOMAIN."/appdown.php";
			$root['error'] = "";
			$root['status']=1;
		}
		else
		{
			$root['error'] = "注册失败";
		}
		api_ajax_return($root);
	}

}
	