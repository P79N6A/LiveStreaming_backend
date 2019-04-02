<?php

print_r(9999);exit;
/*require '../system/db/db.php';
define('IS_DEBUG',false);
define('SHOW_DEBUG',false);
$db = require '../public/db_config.php';

$mysql = new mysql_db($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD'], $db['DB_NAME']);
 
$user_list = $mysql->getAll("select * from fanwe_user where is_robot = 1");

require_once  "../system/cache/Rediscache/Rediscache.php";
$distribution_cfg = require '../public/directory_init.php';
require '../mapi/lib/redis/BaseRedisService.php';
require '../mapi/lib/redis/UserRedisService.php';

$user_obj = new UserRedisService();
$re = $user_obj->test_update_redis($user_list);

$user = $mysql->getAll("SELECT id,nick_name,head_image FROM fanwe_user where is_robot = 1");

if($user){
	foreach($user as $k=>$user_data){
		accountimport($user_data);
	}
	echo "IM用户同步结束";
	echo "<hr/>";
}

echo 'test_add_redis :'.var_dump($re);
echo '<br />';exit;*/



