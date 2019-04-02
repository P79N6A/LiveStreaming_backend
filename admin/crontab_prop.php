<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2016/11/24
 * Time: 16:12
 */
//定时任务,在java定时访问调用
header("Content-Type:text/html; charset=utf-8");
define("FANWE_REQUIRE", true);
require __DIR__ . '/system/mapi_init.php';
fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/Model.class.php');
Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
echo json_encode(Model::build('prop')->crontabRobot());
