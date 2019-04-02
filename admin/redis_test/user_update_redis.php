<?php
require '../system/common.php';
require '../system/db/db.php';
define('IS_DEBUG',0);
define('SHOW_DEBUG',0);
$db = require '../public/db_config.php';

$mysql = new mysql_db($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD'], $db['DB_NAME']);
 
//$user_list = $mysql->getAll("select id from fanwe_user limit 1,100");

require_once  "../system/cache/Rediscache/Rediscache.php";
$distribution_cfg = require '../public/directory_init.php';
require '../mapi/lib/redis/BaseRedisService.php';
require '../mapi/lib/redis/UserRedisService.php';
require '../mapi/lib/redis/UserFollwRedisService.php';

$user_redis = new UserRedisService();
$user_id = 167254;
$user_data = array();
$user_data['family_id'] = 0;
$user_data['family_chieftain'] = 0;
$user_redis->update_db($user_id, $user_data);
//print_r($user_list);
//foreach ($user_list as $user_key) {
//	$user_id = $user_key['id'];
//	$user_data = array();
//	$user_data['is_authentication'] = 0;
//	$user_data['v_type'] = 0;
//	$user_data['v_explain'] = '';
//	$user_data['v_icon'] = '';
//	//$user_redis->update_db($user_id, $user_data);
//}

//echo 'test_add_redis :'.var_dump($re);
echo '<br />';



