<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//计划任务执行接口
interface schedule {

	/**
	 * 执行指定的计划任务,
	 * 返回   返回 array("status"=>0/1, "attemp"=>0/1,  "info"=>string);
	 * @param unknown_type $data
	 */
	function exec($data);

	
	
	
}
?>