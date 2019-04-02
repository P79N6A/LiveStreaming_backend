<?php

// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------


require './system/system_init.php';

define('BASE_PATH', './');
define('THINK_PATH', './madmin/ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'madmin');
define('APP_PATH', './madmin');

// 加载框架入口文件
require THINK_PATH . "/ThinkPHP.php";

//引入文字参数
require './lang_ljz.php';

//实例化一个网站应用实例
$AppWeb = new App();
//应用程序初始化
$AppWeb->run();
