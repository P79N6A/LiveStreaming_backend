<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

fanwe_require(APP_ROOT_PATH.'mapi/lib/app_download.action.php');
class app_downloadCModule  extends app_downloadModule
{
	public function index()
	{
		$root = array();
		$list = array();
		$root['status'] = 1;

		$ios_down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_down_url'",true,true);
		$android_down_url = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_filename'",true,true);
		
		$has_ios = $ios_down_url?1:0;
		$has_android = $android_down_url?1:0;
		

		$list = array(
			"ios" =>array(
				'has_ios'=>$has_ios,
				'ios_down_url'=>$ios_down_url,
			),
			"android" =>array(
				'has_android'=>$has_android,
				'android_down_url'=>$android_down_url,
			),
			"wechat" =>array(
				'has_wechat'=>1,
				'url'=>SITE_DOMAIN.'/mapi/index.php?ctl=app_download',
			)
		);
		$root['list'] = $list;
		api_ajax_return($root);
	}	
	
}
?>