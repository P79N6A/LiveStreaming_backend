<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

//定时任务,在java定时访问调用 60s一次
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
$ret_array = crontab_do_award();
echo json_encode($ret_array);
