<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');

$ret_array = array();
$ret_array2 = crontab_do_check_pc_video();
$ret_array3 = crontab_do_check_upload_video();

array_push($ret_array, $ret_array2, $ret_array3);
echo json_encode($ret_array);
