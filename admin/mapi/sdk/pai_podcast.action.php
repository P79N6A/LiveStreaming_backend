<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH.'mapi/lib/pai_podcast.action.php');
class pai_podcastCModule extends pai_podcastModule
{
    public function upload()
    {
        if ($GLOBALS['user_info']['id'] == 0) {
            ajax_return(array('status' => 0, 'error' => '请先登录'));
        }
        // 开始上传
        // 创建avatar临时目录
        $temp = APP_ROOT_PATH . "public/paiimgs/temp/";
        self::mkdirm($temp);

        $img_result = save_image_upload($_FILES, "file", "attachment/temp", array('origin' => array(600, 600, 0, 0)));
        // 开始移动图片到相应位置
        $id = $GLOBALS['user_info']['id'];

        $dir_name = to_date(get_gmtime(), "Ym/d/H");

        $save_rec_Path = "/public/paiimgs/" . $dir_name . "/origin/"; //上传时先存放原图
        $savePath      = APP_ROOT_PATH . "public/paiimgs/" . $dir_name . "/origin/"; //绝对路径
        self::mkdirm(APP_ROOT_PATH . "public/paiimgs/" . $dir_name . "/origin/");
        //文件名
        $save_name = md5(time() . rand(100, 999)) . $id . ".jpg";
        //相对路径
        $image_file_domain = ".".$save_rec_Path.$save_name;
        //服务器路径
        $image_big_file = $savePath . $save_name;

        //保存文件
        @file_put_contents ( $image_big_file, file_get_contents ( $img_result ['file'] ['thumb'] ['origin'] ['path'] ) );
        
        if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
        {
        	syn_to_remote_image_server($image_file_domain);
        }
        
        @unlink ( $img_result ['file'] ['thumb'] ['origin'] ['path'] );
        @unlink ( $img_result ['file'] ['path'] );
        
        if(file_exists($image_big_file)){
        	$root['status'] = 1;
        	$root['error'] = '上传成功';
        	$root['server_path']      = $image_file_domain;
        	$root['server_full_path'] =get_spec_image($image_file_domain);
        }else{
        	$root['status'] = 0;
        	$root['error'] = '上传失败';
        	$root['path'] ='';
        }
        ajax_return($root);
    }
    /**
     * 虚幻创建文件夹
     * @param  [type]  $path [description]
     * @param  integer $mod  [description]
     * @return [type]        [description]
     */
    protected static function mkdirm($path, $mod = 0777)
    {
        if (!file_exists($path)) {
            self::mkdirm(dirname($path));
            mkdir($path, $mod);
        }
    }

    public function test()
    {

        //推送

        $user_id = intval($_REQUEST['id']);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

        fanwe_require(APP_ROOT_PATH . 'system/schedule/android_list_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_list_schedule.php');

        fanwe_require(APP_ROOT_PATH . 'system/schedule/android_file_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_file_schedule.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');

        $user_type = $GLOBALS['db']->getRow("SELECT apns_code,device_type FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
        if (intval($user_type['device_type']) == 1) {
            print_r('安卓推送');
            //安卓推送信息
            $apns_app_code_list   = array();
            $apns_app_code_list[] = $user_type['apns_code'];

            $AndroidList = new android_list_schedule();
            $data        = array(
                'dest'    => implode(",", $apns_app_code_list),
                'content' => 'ceshi',
                'user_id' => $user_id,
                'room_id' => 0,
                'url'     => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods',
                'type'    => 5,
            );
            print_r($data);
            //print_r($AndroidList);
            $ret_android = $AndroidList->exec($data);
            print_r($ret_android);
            print_r('安卓推送结束');
        } elseif (intval($user_type['device_type']) == 2) {
            print_r('ios 推送');
            //ios 推送信息
            $apns_ios_code_list   = array();
            $apns_ios_code_list[] = $user_type['apns_code'];

            $IosList  = new ios_list_schedule();
            $ios_data = array(
                'dest'    => implode(",", $apns_ios_code_list),
                'content' => 'ceshi',
                'user_id' => $user_id,
                'room_id' => 0,
                'url'     => SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=pai_user&act=goods',
                'type'    => 5,
            );
            print_r($ios_data);
            //print_r($IosList);
            $ret_ios = $IosList->exec($ios_data);
            print_r($ret_ios);
            print_r('ios 推送结束');
        }
    }
}

?>