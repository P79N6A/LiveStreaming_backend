<?php
require '../system/common.php';
require '../system/db/db.php';
define('IS_DEBUG',0);
define('SHOW_DEBUG',0);
$db = require '../public/db_config.php';
$mysql = new mysql_db($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD'], $db['DB_NAME']);
//$user_list = $mysql->getAll("select * from fanwe_user");
require_once  "../system/cache/Rediscache/Rediscache.php";
$distribution_cfg = require '../public/directory_init.php';
require '../mapi/lib/redis/BaseRedisService.php';
require '../mapi/lib/redis/UserRedisService.php';
/*
1、清空--主播待审认证
2、清空--家族列表
3、清空--话题列表
4、清空--直播结束视频
5、清空--回播列表
6、清空--推送消息列表
7、清空--在线充值单
8、清空--所有用户的蜗牛币和秀豆
 */
//清空--主播待审认证
//$mysql->query("update fanwe_user set authentication_type='',authentication_name='',contact='',from_platform='',wiki='',identify_hold_image='',identify_positive_image='',identify_nagative_image='',identify_number='' where is_robot = 0  ");

//清空--家族列表
//$mysql->query("delete from fanwe_family");
//$mysql->query("delete from fanwe_family_join");
//清空--话题列表
//$mysql->query("delete from fanwe_video_cate");
//清空--直播结束视频
//$mysql->query("delete from fanwe_video_history");
//清空--推送消息列表
//$mysql->query("delete from fanwe_push_anchor");
//清空--在线充值单
//$mysql->query("delete from fanwe_payment_notice");
//清空--所有用户的蜗牛币和秀豆
//$mysql->query("update fanwe_user set ticket=0,diamonds=0 where is_robot = 0");


$user_list = $mysql->getAll("select * from fanwe_user where is_robot = 0 ");
print_r($user_list);
exit;
$user_redis = new UserRedisService();
foreach($user_list as $k=>$v){
    $user_redis->update_db($v['id'],$v);
}
echo 'update_user :'.count($user_list);
echo '<br />';exit;

