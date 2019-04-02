<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class synCModule  extends baseModule
{
	//登录 test
	public function login()
	{
		if(IS_DEBUG){
			$mobile = intval($_REQUEST['mobile']);
			$uid = intval($_REQUEST['id']);
			if($mobile){
				$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where mobile =".$mobile);
			}else{
				if($uid){
					$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$uid);
				}else{
					print_r("请填写会员ID");exit;
				}
			}
	
			es_session::set("user_info",$user_data);
			$GLOBALS['user_info'] = $user_data;
			es_cookie::set("client_ip",CLIENT_IP,3600*24*30);
			es_cookie::set("nick_name",$user_data['nick_name'],3600*24*30);
			es_cookie::set("user_id",$user_data['id'],3600*24*30);
			es_cookie::set("user_pwd",md5($user_data['user_pwd']."_EASE_COOKIE"),3600*24*30);
			es_cookie::set("is_agree",$user_data['is_agree'],3600*24*30);
			es_cookie::set("PHPSESSID2",es_session::id(),3600*24*30);
			ajax_return($user_data);
		}
		
	}
	//循环同步到IM
	function  synchronization_im(){
		if(IS_DEBUG){
			fanwe_require(APP_ROOT_PATH."system/libs/user.php");
			$user_id = intval($_REQUEST['id']);
			if($user_id){
				$user = $GLOBALS['db']->getAll("SELECT id,nick_name,head_image FROM ".DB_PREFIX."user where id = ".$user_id);
			}else{
				$user = $GLOBALS['db']->getAll("SELECT id,nick_name,head_image FROM ".DB_PREFIX."user where synchronize=0 ");
			}
			if($user){
				foreach($user as $k=>$user_data){
					accountimport($user_data);
				}
				echo $user_id."用户已同步";
			}else{
				echo "用户已同步";
			}
		}
	}
	//循环同步到redis
	function  synchronization_redis(){
		if(IS_DEBUG){
			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
			$user_redis = new UserRedisService();
			$user_id = intval($_REQUEST['id']);
			$type = intval($_REQUEST['type']);
			if($user_id){
				if($type==1&&$_REQUEST['c']!=''){
					fanwe_require(APP_ROOT_PATH."system/libs/user.php");
					$user_info =array();
					if($_REQUEST['c']=='mobile'||$_REQUEST['c']=='all')
						$user_info['mobile'] ='';
	
					if($_REQUEST['c']=='qq'||$_REQUEST['c']=='all')
						$user_info['qq_openid'] ='';
	
					if($_REQUEST['c']=='wx'||$_REQUEST['c']=='all'){
						$user_info['wx_openid'] ='';
						$user_info['wx_unionid'] ='';
					}
					if($_REQUEST['c']=='sina'||$_REQUEST['c']=='all'){
						$user_info['sina_id'] ='';
					}
	
					if($_REQUEST['c']=='wx_gz'||$_REQUEST['c']=='all'){
						$user_info['gz_openid'] ='';
						$user_info['subscribe'] ='';
					}
	
					$where = "id=".intval($user_id);
					$GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,'UPDATE',$where);
				}
	
				$user = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user where id = ".$user_id);
				if($user){
					$user_redis->update_db($user_id,$user);
					echo "用户redis同步完成";
				}else{
					echo "用户redis已同步";
				}
			}
		}
	}
	//图片同步
	function  synchronization_image(){
		if(IS_DEBUG){
			$user_id = intval($_REQUEST['id']);
			if($user_id){
				$user_data = $GLOBALS['db']->getRow("select id,head_image from ".DB_PREFIX."user where id =".$user_id);
	
				if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
				{
					syn_to_remote_image_server($user_data['head_image']);
				}
	
				echo "执行结束";exit;
			}else{
				echo "ID空";exit;
			}
		}
	}
}


?>
