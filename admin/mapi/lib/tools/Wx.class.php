<?php

/**
 *
 */
class Wx
{
    public static function getWeixinInfo()
    {
        $wx_info = es_session::get('wx_info');
        if ($wx_info['openid']) {
            return $wx_info;
        }
        if (!isWeixin()) {
            return false;
        }
        $m_config = load_auto_cache("m_config");
        if (!($m_config['wx_gz_appid'] && $m_config['wx_gz_secrit'])) {
            return false;
        }
        $current_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
        fanwe_require(APP_ROOT_PATH . "system/utils/weixin.php");
        $weixin = new weixin($m_config['wx_gz_appid'], $m_config['wx_gz_secrit'], $current_url);
        if (!($_REQUEST['code'] && $_REQUEST['state'] == 1)) {
            $wx_url = $weixin->scope_get_code();
            app_redirect($wx_url);
        }
        $wx_info = $weixin->scope_get_userinfo($_REQUEST['code']);
        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        es_session::set("wx_info", $wx_info);
        wxMakeUser($wx_info);
        return $wx_info;
    }
}
