<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

/**
 * api 登录的接口标准
 */
interface api_login{
	
	function callback();     //用于处理 api 访问回调的callback
	
	function get_title();    //后台列表调用的显示信息
	
	
	
}
?>