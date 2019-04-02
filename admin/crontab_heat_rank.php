<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/car/core/common_car.php');
$ret_array = get_rank();
echo json_encode($ret_array);
