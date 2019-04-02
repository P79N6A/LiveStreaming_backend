<?php
define("FANWE_REQUIRE", true);

if (!empty($_REQUEST['cstype']) && $_REQUEST['cstype'] == 1) {
    require_once '../system/wap_init.php';
} else {
    require_once '../system/mapi_init.php';
}

fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
require APP_ROOT_PATH . 'system/template/template.php';
$tmpl = new AppTemplate;

$GLOBALS['tmpl']->cache_dir = APP_ROOT_PATH . 'public/runtime/wap/tpl_caches';
$GLOBALS['tmpl']->compile_dir = APP_ROOT_PATH . 'public/runtime/wap/tpl_compiled';
$GLOBALS['tmpl']->template_dir = APP_ROOT_PATH . 'weixin/theme/default/view';
$GLOBALS['tmpl']->assign("TMPL_REAL", APP_ROOT_PATH . "weixin/theme/default");
$tmpl_path = get_domain() . APP_ROOT . "/theme/";
$GLOBALS['tmpl']->assign("TMPL", $tmpl_path . "default");

$jstmpl_path = get_domain() . PAP_ROOT . "/weixin/";
$GLOBALS['tmpl']->assign("JSTMPL", $jstmpl_path);

fanwe_require(APP_ROOT_PATH . 'weixin/lib/core/common.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');

filter_injection($_REQUEST);

//登录url,跳到得来那边
global $logo_url;
$logo_url = app_conf('DELAI_DOMAIN') . '/login';

global $ref_uid;
//保存返利的cookie
if ($_REQUEST['ref']) {
    $rid = intval(base64_decode($_REQUEST['ref']));
    $ref_uid = intval($GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id = " . $rid));
    es_cookie::set("REFERRAL_USER", $ref_uid);
} else {
    //获取存在的推荐人ID
    if (intval(es_cookie::get("REFERRAL_USER")) > 0) {
        $ref_uid = intval($GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id = " . intval(es_cookie::get("REFERRAL_USER"))));
    }

}

//用户信息
global $user_info;
//会员自动登录及输出
$cookie_uid = es_cookie::get("user_id") ? es_cookie::get("user_id") : '';
$cookie_upwd = es_cookie::get("user_pwd") ? es_cookie::get("user_pwd") : '';

if ($cookie_uid != '' && $cookie_upwd != '' && !es_session::get("user_info")) {
    fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
    auto_do_login_user($cookie_uid, $cookie_upwd);
}
$user_info = es_session::get('user_info');
$GLOBALS['tmpl']->assign("user_info", $user_info);

$cstype = $_REQUEST['cstype'];
if (!$user_info && $cstype != '') {

    if (intval($cstype) > 0) {
        $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=" . intval($cstype));
    } else {
        $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=1");
    }

    $user_info = es_session::get('user_info');
    $GLOBALS['tmpl']->assign("user_info", $user_info);
}

$_REQUEST['ctl'] = filter_ma_request($_REQUEST['ctl']);
$_REQUEST['act'] = filter_ma_request($_REQUEST['act']);

$is_weixin = isWeixin();

if (!$user_info && $is_weixin) {
    //微信登陆
    fanwe_require(APP_ROOT_PATH . "system/utils/weixin.php");
    $m_config = load_auto_cache("m_config"); //初始化手机端配置
    $wx_status = (($m_config['wx_appid'] && $m_config['wx_secrit'])) ? 1 : 0;

    $class2 = strtolower(strim($_REQUEST['ctl'])) ? strtolower(strim($_REQUEST['ctl'])) : "index";
    $act2 = strtolower(strim($_REQUEST['act'])) ? strtolower(strim($_REQUEST['act'])) : "index";
    $current_url = url_wx($class2 . "#" . $act2, $_REQUEST);

    if ($_REQUEST['code'] && $_REQUEST['state'] == 1 && $wx_status) {

        $weixin = new weixin($m_config['wx_appid'], $m_config['wx_secrit'], get_domain() . $current_url);

        if ($_REQUEST['code'] != "") {
            $wx_info = $weixin->scope_get_userinfo($_REQUEST['code']);
            fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
            $root = wxMakeUser($wx_info);

            if ($root['status'] == 0) {
                var_dump($root);exit();
            } else {
                $user_info = es_session::get('user_info');
                $GLOBALS['tmpl']->assign("user_info", $user_info);
            }

        }

    } else {
        if ($wx_status) {
            $weixin_2 = new weixin($m_config['wx_appid'], $m_config['wx_secrit'], get_domain() . $current_url);
            $wx_url = $weixin_2->scope_get_code();
            app_redirect($wx_url);
        }
    }

}

$search = array("../", "\n", "\r", "\t", "\r\n", "'", "<", ">", "\"", "%", "\\", ".", "/");
$itype = str_replace($search, "", $_REQUEST['itype']);
$class = strtolower(strim($_REQUEST['ctl'])) ? strtolower(strim($_REQUEST['ctl'])) : "course";
$class_name = $class;
$lib = $itype ? $itype : 'wx';

//升级会员
if (isset($_REQUEST['vip_code']) && $user_info) {
    $vip_code = $_REQUEST['vip_code'];
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/Model.class.php');
    Model::$lib = APP_ROOT_PATH . 'mapi/wx/';
    $vip = Model::build('vip_exchange');
    $result = $vip->exchange($vip_code, intval($user_info['id']));
}

if ($lib == 'lib') {
    //fanwe_require("./lib/base.action.php");
    fanwe_require(APP_ROOT_PATH . "mapi/lib/base.action.php");
    @fanwe_require(APP_ROOT_PATH . "mapi/lib/" . $class . ".action.php");
    //@fanwe_require("./lib/".$class.".action.php");
    $class = $class . 'Module';
} else {
    //fanwe_require("./lib/base.action.php");
    fanwe_require(APP_ROOT_PATH . "mapi/lib/base.action.php");
    //@fanwe_require("./lib/".$class.".action.php");

    //fanwe_require("./".$lib."/base.action.php");
    //@fanwe_require("./".$lib."/".$class.".action.php");
    fanwe_require(APP_ROOT_PATH . "mapi/" . $lib . "/base.action.php");
    fanwe_require(APP_ROOT_PATH . "mapi/" . $lib . "/" . $class . ".action.php");
    $class = $class . 'CModule';

}

$act = strtolower(strim($_REQUEST['act'])) ? strtolower(strim($_REQUEST['act'])) : "index";
$GLOBALS['tmpl']->assign("ctl", $class_name);
$GLOBALS['tmpl']->assign("act", $act);

if (class_exists($class)) {
    $obj = new $class;

    if (method_exists($obj, $act)) {
        $obj->$act();
    } else {
        $error["errcode "] = 10006;
        $error["errmsg "] = "接口方法不存在";
        ajax_return($error);
    }
} else {
    $error["errcode "] = 10005;
    $error["errmsg "] = "接口不存在";
    ajax_return($error);
}
