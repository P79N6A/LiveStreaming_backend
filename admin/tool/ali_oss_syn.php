<?php

// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------
if (isset($_GET['DEBUG'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
    set_error_handler(function ($a, $b, $c, $d) {
        var_dump($a, $b, $c, $d);
    });
    register_shutdown_function(function () {
        if ($a = error_get_last()) {
            var_dump($a);
        }
    });
}
//同步本地文件至阿里oss
define("FILE_PATH", "/tool"); //文件目录，空为根目录
require_once dirname(__DIR__) . '/system/system_init.php';
require dirname(__DIR__) . '/public/directory_init.php';
set_time_limit(0);
$paths = array(
    "public/attachment",
    "public/avatar",
    "public/emoticons",
    "public/images",
    "public/js",
    "public/gift",
    'public/runtime/statics',
);
function syn_path(&$service, &$bucket, $pathName)
{
    //判断传入的变量是否是目录
    if (!is_dir($pathName) || !is_readable($pathName)) {
        return null;
    }
    //取出目录中的文件和子目录名,使用scandir函数
    $allFiles = scandir($pathName);
    //遍历他们
    foreach ($allFiles as $fileName) {
        //判断是否是.和..因为这两个东西神马也不是。。。
        if (in_array($fileName, array('.', '..'))) {
            continue;
        }
        //路径加文件名
        $fullName = $pathName . '/' . $fileName;
        //如果是目录的话就继续遍历这个目录
        if (is_dir($fullName)) {
            //将这个目录中的文件信息存入到数组中
            syn_path($service, $bucket, $fullName);
        } else {
            //如果是文件就先存入临时变量
            // $a[] = $fullName;
            // syn_to_remote_image_server(str_replace(APP_ROOT_PATH, './', $fullName), false);
            $file_dir = str_replace(APP_ROOT_PATH, "", $pathName);
            $object = $file_dir . '/' . $fileName;
            $file_path = $pathName . '/' . $fileName;
            //log_result($file_path);
            try {
                $service->upload_file_by_file($bucket, $object, $file_path);
            } catch (Exception $e) {
            }
        }
    }
}

if ($GLOBALS['distribution_cfg']['OSS_TYPE'] == "ALI_OSS") {
    require_once APP_ROOT_PATH . "system/alioss/sdk.class.php";

    $oss_sdk_service = new ALIOSS();
    //设置是否打开curl调试模式
    $oss_sdk_service->set_debug_mode(true);

    $bucket = $GLOBALS['distribution_cfg']['OSS_BUCKET_NAME'];

    foreach ($GLOBALS['paths'] as $path) {
        syn_path($oss_sdk_service, $bucket, APP_ROOT_PATH . $path);
    }
    echo 'ok';
}
