<?php
ini_set('display_errors', 1);
define("FANWE_REQUIRE", true);

if (isset($_REQUEST['cstype']) && $_REQUEST['cstype'] == 1) {
    require './system/wap_init.php';
} else {
    require './system/pc_init.php';
}

if (!defined('APP_ROOT')) {
    // 网站URL根目录
    $_root = dirname(_PHP_FILE_);
    $_root = (($_root == '/' || $_root == '\\') ? '' : $_root);
    $_root = str_replace("/system", "", $_root);
    $_root = str_replace("/wap", "", $_root);
    define('APP_ROOT', $_root);
}

fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
require APP_ROOT_PATH . 'system/template/template.php';
$tmpl = new AppTemplate;

$GLOBALS['tmpl']->cache_dir = APP_ROOT_PATH . 'public/runtime/app/tpl_caches';

$GLOBALS['tmpl']->compile_dir = APP_ROOT_PATH . 'public/runtime/app/tpl_compiled';
$GLOBALS['tmpl']->template_dir = APP_ROOT_PATH . 'app/theme/demo';
$GLOBALS['tmpl']->assign("TMPL_REAL", APP_ROOT_PATH . "app/theme/demo");
$tmpl_path = get_domain() . APP_ROOT . "/app/theme/";
$GLOBALS['tmpl']->assign("TMPL", $tmpl_path . "demo");

$jstmpl_path = get_domain() . PAP_ROOT . "/app/";
$GLOBALS['tmpl']->assign("JSTMPL", $jstmpl_path);

$GLOBALS['tmpl']->assign("APP_ROOT", APP_ROOT);
fanwe_require(APP_ROOT_PATH . 'app/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

filter_injection($_REQUEST);

//会员自动登录及输出
$cookie_uid = es_cookie::get("user_id") ? es_cookie::get("user_id") : '';
$cookie_upwd = es_cookie::get("user_pwd") ? es_cookie::get("user_pwd") : '';

if ($cookie_uid != '' && $cookie_upwd != '' && !es_session::get("user_info")) {
    fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
    auto_do_login_user($cookie_uid, $cookie_upwd);
}

//用户信息
global $user_info;
$user_info = es_session::get('user_info');
if ($user_info) {
    if (MAX_LOGIN_TIME > 0) {
        $user_logined_time = intval($user_info['login_time']);
        if ((NOW_TIME - $user_logined_time) >= intval(MAX_LOGIN_TIME)) {
            es_session::delete('user_info');
            $user_info = '';
        }
    }

    if (!empty($user_info['id'])) {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_data = $user_redis->getRow_db($user_info['id'], array('id', 'diamonds', 'use_diamonds', 'ticket', 'refund_ticket', 'family_id', 'score', 'online_time', 'user_level', 'anchor_level'));
        if ($user_data['id'] !== false) {
            $user_data['useable_ticket'] = intval($user_data['ticket'] - $user_data['refund_ticket']);
            foreach (array_keys($user_data) as $key) {
                $user_info[$key] = $user_data[$key];
            }
        }

        $user_level = user_leverl_syn($user_info);
        $user_level['level_ico'] = get_domain() . '/public/images/rank/rank_' . $user_level["level"] . '.png';

        $p_score = $user_level['u_score'] - $user_level['score'];
        if ($p_score < 0) {
            $p_score = 0;
        }
        if (empty($user_level['next_level'])) {
            $user_level['progress'] = 100;
        } else {
            $next_level = $user_level['next_level'];
            $user_level['next_level'] = $next_level['level'];
            $user_level['next_score'] = $next_level['score'];
            $user_level['next_level_ico'] = get_domain() . '/public/images/rank/rank_' . $next_level["level"] . '.png';
            $user_level['progress'] = intval($p_score / ($next_level['score'] - $user_level['score']) * 100);
        }

        $GLOBALS['tmpl']->assign("user_info", $user_info);
        $GLOBALS['tmpl']->assign("user_level_info", $user_level);
    }
}

if (!$user_info && isset($_REQUEST['cstype'])) {
    $cstype = $_REQUEST['cstype'];

    if (intval($cstype) > 0) {
        $sql = "select * from " . DB_PREFIX . "user where id=" . intval($cstype);
    } else {
        $sql = "select * from " . DB_PREFIX . "user where id=100324";
    }
    //    es_session::set("user_info",$user_info);
    $user_info = $GLOBALS['db']->getRow($sql);
    //print_r($user_info);

} else {
    //print_r($user_info);
}

$cstype = $_REQUEST['cstype'];
if (!$user_info && $cstype != '') {

    if (intval($cstype) > 0) {
        $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=" . intval($cstype));
    } else {
        $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=290");
    }
//    es_session::set("user_info",$user_info);
}

$_REQUEST['ctl'] = filter_ma_request($_REQUEST['ctl']);
$_REQUEST['act'] = filter_ma_request($_REQUEST['act']);

$class = strtolower(strim($_REQUEST['ctl'])) ? strtolower(strim($_REQUEST['ctl'])) : "index";
$GLOBALS['tmpl']->assign("ctl", $class);

$class_name = $class;

$act = strtolower(strim($_REQUEST['act'])) ? strtolower(strim($_REQUEST['act'])) : "index";
$GLOBALS['tmpl']->assign("act", $act);

fanwe_require(APP_ROOT_PATH . "mapi/lib/base.action.php");
if (file_exists(APP_ROOT_PATH . "mapi/app/" . $class . ".action.php")) {
    @fanwe_require(APP_ROOT_PATH . "mapi/app/" . $class . ".action.php");
}
$class = $class . 'CModule';

if (class_exists($class)) {
    $obj = new $class;

    if (method_exists($obj, $act)) {
        $obj->$act();
    } else {
        $error["errcode "] = 10006;
        $error["errmsg "] = "接口方法不存在!";
        ajax_return($error);
    }
} else {
    $error["errcode "] = 10005;
    $error["errmsg "] = "接口不存在!";
    ajax_return($error);
}

//fanwe_require(APP_ROOT_PATH.'app/index.php');
