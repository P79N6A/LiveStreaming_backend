<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common_yun.php');
//直播消息推送
fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/push.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');

$ret_array = array();
$ret_array4 = crontab_do_end_video_3();
$ret_array5 = crontab_do_end_video_4();
$ret_array6 = crontab_do_pullcomplete();

array_push($ret_array, $ret_array4, $ret_array5, $ret_array6);

echo json_encode($ret_array);
//所有推送事件
//print_r(crontab_do_pullcomplete());exit;
//所有推送事件
//print_r(test_ConcatComplete());exit;
