<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

$ret_array = array();
//排查拍卖订单支付超时
deal_payment_timeout();
//查看拍卖时间到期
deal_pai_timeout();
//订单后续状态更新
deal_pai_order_status();

echo json_encode($ret_array);
