<?php
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';

fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/deal.action.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');

$user_id = $GLOBALS['db']->getOne("select id from fanwe_user where diamonds > 1000 ORDER BY RAND() LIMIT 1");
$room_id = $GLOBALS['db']->getOne("select id from fanwe_video where user_id <> {$user_id} and prop_table = 'fanwe_video_prop_201705' ORDER BY RAND() LIMIT 1");
$prop_id = $GLOBALS['db']->getOne("select id from fanwe_prop ORDER BY RAND() LIMIT 1");

$deal = new dealModule();
$GLOBALS['user_info'] = array('id' => $user_id);
$_REQUEST = array(
    "prop_id" => $prop_id,
    "room_id" => $room_id,
    "from" => "pc"
);
$deal->pop_prop();

echo json_encode(array(
    $user_id,
    $room_id,
    $prop_id
));
