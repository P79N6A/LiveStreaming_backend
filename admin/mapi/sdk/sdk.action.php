<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/sdk/base.action.php');
class sdkCModule extends baseCModule
{
    public function __construct()
    {
        parent::__construct();
        //checkSaas();
    }
    //检验登陆
    public function check_login()
    {
        $session_id = strim($_REQUEST['session_id']);
        if ($session_id == null || es_session::id() != $session_id) {
            api_ajax_return(array("status" => 10007, "error" => '服务端未登陆'));
        }

    }

    // 会员同步注册
    public function register()
    {

        $shop_user_id = strim($_REQUEST['shop_user_id']);
        $nick_name = strim($_REQUEST['nick_name']);

        if ($shop_user_id == '') {
            api_ajax_return(array("status" => 0, "error" => '请填写购物系统用户ID'));
        }

        if ($nick_name == '') {
            api_ajax_return(array("status" => 0, "error" => '请填写昵称'));
        }

        $is_shop = intval($_REQUEST['is_shop']) > 0 ? 1 : 0;
        $sex = intval($_REQUEST['sex']);
        if ($sex < 1) {
            $sex = 1;
        }
        $head_image = strim($_REQUEST['head_image']);
        $thumb_head_image = strim($_REQUEST['thumb_head_image']);
        $session_id = strim($_REQUEST['session_id']);
        //es_session::set_sessid($session_id);

        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $root = sdkMakeUser(array(
            'shop_user_id' => $shop_user_id,
            'nick_name' => $nick_name,
            'is_shop' => $is_shop,
            'sex' => $sex,
            'head_image' => $head_image,
            'thumb_head_image' => $thumb_head_image,
            'session_id' => $session_id
        ));

        api_ajax_return($root);
    }

    // 购物直播SDK登录
    public function login()
    {
        //$video_user_id = intval($_REQUEST['video_user_id']);
        $shop_user_id = strim($_REQUEST['shop_user_id']);

        if ($shop_user_id) {
            $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where shop_user_id ='" . $shop_user_id . "'");
        } else {
            $root = array(
                'status' => 0,
                'error' => '请填写会员ID'
            );
            api_ajax_return($root);
        }

        if (!$user_data) {
            $root = array(
                'status' => 0,
                'error' => '请先注册会员'
            );
            api_ajax_return($root);
        }
        $video_user_id = intval($user_data['id']);
        //===========add  start ===========
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();

        $user_id = $video_user_id;
        $ridis_data = $user_redis->getRow_db($user_id);

        if (!$ridis_data) {
            $ridis_data = $user_redis->reg_data($user);
            $user_redis->insert_db($user_id, $ridis_data);

            $user_data = $user_redis->getRow_db($user_id);
        }

        //===========add  end ===========
        $GLOBALS['db']->query("update " . DB_PREFIX . "user set login_ip = '" . CLIENT_IP . "',login_time='" . date("Y-m-d H:i:s", NOW_TIME) . "' where id =" . $video_user_id);

        //===========add  start ===========
        $data = array();
        $data['login_ip'] = CLIENT_IP;
        $data['login_time'] = NOW_TIME;
        $user_redis->update_db($video_user_id, $data);

        user_leverl_syn($user_data);

        $session_id = strim($_REQUEST['session_id']);
        //es_session::set_sessid($session_id);
        //初始化session
        es_session::set("user_info", $user_data);
        //设置session过期时间一个月
        es_session::setGcMaxLifetime('2592000');

        //登录成功 同步信息
        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        accountimport($user_data);
        //设置cookie
        es_cookie::set("client_ip", CLIENT_IP, 3600 * 24 * 30);
        es_cookie::set("nick_name", $user_data['nick_name'], 3600 * 24 * 30);
        es_cookie::set("user_id", $user_data['id'], 3600 * 24 * 30);
        es_cookie::set("user_pwd", md5($user_data['user_pwd'] . "_EASE_COOKIE"), 3600 * 24 * 30);
        es_cookie::set("PHPSESSID2", $session_id, 3600 * 24 * 30);

        es_session::set("user_id", $user_data['id']);
        es_session::set("user_pwd", md5($user_data['user_pwd'] . "_EASE_COOKIE"));
        /*
        es_session::set("user_id", $user_data['id']);
        es_session::set("user_pwd", md5($user_data['user_pwd']."_EASE_COOKIE"));
        es_session::set("session_id", $session_id);
        log_result(es_session::get("session_id"));
        log_result(es_session::get("user_id"));
        log_result(es_session::get("user_pwd"));
         */
        $GLOBALS['user_info'] = $user_data;

        $root = array(
            'status' => 1,
            'error' => '登录成功',
            'video_user_id' => $video_user_id,
            'session_id' => $session_id
        );
        api_ajax_return($root);
    }

    // 购物直播SDK登出
    public function logout()
    {
        //$video_user_id = intval($_REQUEST['video_user_id']);
        $shop_user_id = strim($_REQUEST['shop_user_id']);
/*
$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where shop_user_id ='".$shop_user_id."'");
$video_user_id=intval($user_data['id']);

$user_info = es_session::get("user_info");
log_result('===logout===');
log_result($user_info);
if(!$user_info || intval($user_info['id']) != $video_user_id){
$root = array(
'status' => 0,
'error' => '请先登录',
);
log_result('===logout1===');
log_result($user_info);
log_result($video_user_id);
api_ajax_return($root);
}
 */
        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $result = loginout_user();
        $root = array(
            'status' => 1,
            'error' => '登出成功',
            'video_user_id' => $video_user_id
        );

        api_ajax_return($root);
    }

    // 购物直播主播审核
    public function do_shop()
    {
        //$this->check_login();
        $shop_user_id = strim($_REQUEST['shop_user_id']);
        if ($shop_user_id == '') {
            api_ajax_return(array("status" => 0, "error" => '请填写购物系统用户ID'));
        }

        $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where shop_user_id ='" . $shop_user_id . "'");
        if (!$user_data) {
            api_ajax_return(array("status" => 0, "error" => '购物系统用户ID不存在'));
        }

        if ($user_data['is_shop'] == 0) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();

            $GLOBALS['db']->query("update " . DB_PREFIX . "user set is_shop = 1 where id =" . $video_user_id);

            $data = array();
            $data['is_shop'] = 1;
            $user_redis->update_db($user_id, $data);
        }

        $root = array(
            'status' => 1,
            'video_user_id' => $video_user_id
        );

        api_ajax_return($root);
    }

    // 检测主播房间状态
    public function check()
    {
        //$this->check_login();
        $podcast_user_id = strim($_REQUEST['podcast_user_id']);
        if (podcast_user_id == '') {
            api_ajax_return(array("status" => 0, "error" => '请填写购物系统用户ID', "podcast_user_id" => $podcast_user_id, "podcast_status" => 0));
        }

        $user_data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where shop_user_id ='" . $podcast_user_id . "'");
        if (!$user_data) {
            api_ajax_return(array("status" => 0, "error" => '购物系统用户ID不存在', "podcast_user_id" => $podcast_user_id, "podcast_status" => 0));
        }
        $user_id = intval($user_data['id']);
        $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $user_id . " and live_in=1");
        $podcast_status = 0;
        if ($video_info) {
            $podcast_status = 1;
        }

        $root['status'] = 1;
        $root['error'] = '';
        $root['podcast_user_id'] = $podcast_user_id;
        $root['podcast_status'] = $podcast_status;

        api_ajax_return($root);

    }
    /**
     * 初始化
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=553
     * @return [type] [description]
     */
    public function init()
    {
        self::checkLogin();
        $video_user_id = $_REQUEST['video_user_id'];
        $session_id = $_REQUEST['session_id'];
        $user_id = intval(isset($GLOBALS['user_info']['id']) ? $GLOBALS['user_info']['id'] : 0);
        if ($video_user_id == $user_id && $session_id == es_session::id()) {
            api_ajax_return(array('status' => 1, 'video_user_id' => $video_user_id, 'session_id' => $session_id));
        } else {
            api_ajax_return(array('status' => 0, 'video_user_id' => 0, 'session_id' => $session_id));
        }
    }

}
