<?php  
class share_distributionModule  extends baseModule
{

	//分销分享页面
	public function index()
    {

        //分销功能
        if((defined('DISTRIBUTION_MEMBER')&&DISTRIBUTION_MEMBER==1)&&intval($m_config['distribution'])==1){
            $root['register_url'] = SITE_DOMAIN.'/wap/index.php?ctl=share_distribution&act=init_register&user_id='.$share_id;
        }
        api_ajax_return($root);


    }

    //分销注册初始化
    public function init_register()
	{
		$root = array('status' => 0,'error'=>'');
        $share_id = intval($_REQUEST['user_id']);
		$root['url'] =SITE_DOMAIN.'/mapi/index.php?ctl=share_distribution&act=register&user_id='.$share_id;
        $root['page_title'] = '手机注册';
		$root['app_down_url'] = SITE_DOMAIN."/appdown.php";
		api_ajax_return($root);
	}

	//分销注册
	public function register()
	{		
		$root = array('status' => 1,'error'=>'');
		if(!$_REQUEST)
		{
			app_redirect(get_domain()."/");
		}
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = strim($v);
		}
		$p_user_id = intval($_REQUEST['user_id']);
		$mobile = $_REQUEST['mobile'];
		$p_user_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id =" . $p_user_id);
		$mobile = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where mobile =" . $mobile);

		if(intval($p_user_id)==0){
			$root['error'] = "上级用户不存在！";
            $root['status'] = 0;
		}
		if(1){
			$root['url'] = get_domain()."/appdown.php";
		}
		if($mobile!=''){
			$root['is_url'] = 1;
			$root['error'] = "手机号已注册！";
            $root['status'] = 0;
		}
		if($root['status']!=0){
			fanwe_require(APP_ROOT_PATH."system/libs/user.php");
			$result = do_login_user($_REQUEST['mobile'],$_REQUEST['verify_coder']);
		}
		
		if($result['status'])
		{
			$root['user_id'] = $result['user']['id'];
			$root['status'] = 1;
			if($result['user']['head_image']==''||$result['user_info']['head_image']==''){
				//头像
				$m_config =  load_auto_cache("m_config");//初始化手机端配置
				$system_head_image = $m_config['app_logo'];

				if($system_head_image==''){
					$system_head_image = './public/attachment/test/noavatar_11.JPG';
					syn_to_remote_image_server($system_head_image);
				}
				
				$data = array(
					'head_image' => $system_head_image,
					'thumb_head_image' => get_spec_image($system_head_image,40,40),
					'p_user_id' =>$p_user_id,
					);

				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$result['user']['id']);

				fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$user_redis->update_db($result['user']['id'],$data);

				//更新session
				$user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $result['user']['id']);
				es_session::set("user_info", $user_info);
			}
			$root['is_url'] = 1;
			$root['is_lack'] = $result['is_lack'];//是否缺少用户信息
			$root['is_agree'] = intval($result['user']['is_agree']);//是否同意直播协议 0 表示不同意 1表示同意
			$root['user_id'] = intval($result['user']['id']);
			$root['nick_name'] = $result['user']['nick_name'];
			$root['family_id']=intval($result['user']['family_id']);
			$root['family_chieftain']=intval($result['user']['family_chieftain']);
			$root['error'] = "注册成功";
			$root['user_info'] = $result['user_info'];

		}
		else
		{
            $root['status'] = 0;
			if($root['error']=='')
			    $root['error'] = $result['info'];
		}
		api_ajax_return($root);
	}


}
	