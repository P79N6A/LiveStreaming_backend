<?php

class loginCModule extends baseCModule
{
    public function wx_login()
    {
        $is_weixin = isWeixin();
        log_result(333);
        if (!$is_weixin) {
            api_ajax_return(['status' => 0, 'error' => '请在微信端打开!']);
        }

        if (!is_request_ip_effect()) {
            api_ajax_return(['status' => 0, 'error' => '当前IP已被封停']);
        }

        $m_config = load_auto_cache("m_config");//初始化手机端配置
        if ($m_config['wx_gz_appid'] == '' || $m_config['wx_gz_secrit'] == '') {
            api_ajax_return(['status' => 0, 'error' => '公众号未配置!']);
        }

        $wx_appid = strim($m_config['wx_gz_appid']);
        $wx_secrit = strim($m_config['wx_gz_secrit']);

        fanwe_require(APP_ROOT_PATH . "system/utils/weixin.php");
        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $wx_info = es_session::get('wx_info');
        $h5_redirect = strim($_REQUEST['redirect']) ? strim($_REQUEST['redirect']) : SITE_DOMAIN . '/frontEnd/weixin/index.html#/';
        if ($wx_info['openid']) {
            $root = wxxMakeUser($wx_info);

            if ($root['status'] == 1 && $root['need_bind_mobile'] == 1) {
                app_redirect(SITE_DOMAIN . '/frontEnd/weixin/index.html#/login/index?code=' . $_REQUEST['code'] . '&status=1');
            } elseif ($root['status'] == 1) {
                app_redirect($h5_redirect);
            } else {
                ajax_return($root);
            }
        }
        $back_url = SITE_DOMAIN . "/mapi/index.php?ctl=login&act=wx_login&itype=weixin";
        $wx_status = (($wx_appid && $wx_secrit)) ? 1 : 0;
        if ($_REQUEST['code'] && $_REQUEST['state'] == 1 && $wx_status) {
            $weixin = new weixin($wx_appid, $wx_secrit, $back_url);
            if ($_REQUEST['openid'] != "" && $_REQUEST['access_token'] != "") {
                $wx_info = $weixin->sns_get_userinfo($_REQUEST['openid'], $_REQUEST['access_token']);
                $key = "wx_login_{$_REQUEST['openid']}_{$_REQUEST['access_token']}";
            } else {
                if ($_REQUEST['code'] != "") {
                    $wx_info = $weixin->scope_get_userinfo($_REQUEST['code']);
                    $key = "wx_login_{$_REQUEST['code']}";
                } else {
                    if (DEBUG_WX) {
                        log_result('-服务端获取微信参数失败-');
                    }
                    $root['status'] = 0;
                    $root['error'] = "服务端获取微信参数失(openid or code).";
                    api_ajax_return($root);
                }
            }
            if ($wx_info['openid'] != '') {
                if ($wx_info['unionid'] == '') {
                    $root['error'] = '公众号未绑定开放平台';
                    $root['status'] = 0;
                    api_ajax_return($root);
                }
            }
            es_session::set("wx_info", $wx_info);
            $root = wxxMakeUser($wx_info);

            if (empty($root['user_id'])) {
                $GLOBALS['cache']->set($key, $wx_info, 300, true);
                ajax_return($root);
            }

            log_login(['request' => json_encode($_REQUEST), 'login_type' => 0, 'user_id' => $root['user_id']]);
            if ($root['status'] == 1 && $root['need_bind_mobile'] == 1) {
                app_redirect(SITE_DOMAIN . '/frontEnd/weixin/index.html#/login/index?code=' . $_REQUEST['code'] . '&status=1');
            } elseif ($root['status'] == 1) {
                app_redirect($h5_redirect);
            } else {
                ajax_return($root);
            }
        } else {
            $weixin_2 = new weixin($wx_appid, $wx_secrit, $back_url);
            $wx_url = $weixin_2->scope_get_code();
            api_ajax_return(['status' => 1, 'url' => $wx_url]);
        }
    }

    public function mina_login()
    {
        $code = strim($_REQUEST['code']);
        $nickname = strim($_REQUEST['nickname']);
        $head_image = strim($_REQUEST['head_image']);

        if (empty($code)) {
            api_ajax_return(['status' => 0, 'error' => '参数错误']);
        }

        $wx_info = get_mina_user_info($code);
        $wx_info['nickname'] = $nickname;
        $wx_info['headimgurl'] = $head_image;

        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $root = wxxMakeUser($wx_info);

        $key = "wx_login_{$_REQUEST['code']}";
        if (empty($root['user_id'])) {
            $GLOBALS['cache']->set($key, $wx_info, 300, true);
        }

        api_ajax_return($root);
    }

    //发送手机验证码
    public function do_login()
    {
        $mobile = strim($_REQUEST['mobile']);
        $verify_code = strim($_REQUEST['verify_code']);
        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $result = do_login_user($mobile, $verify_code);

        if (!$result['status']) {
            api_ajax_return(['status' => 0, 'error' => $result['info']]);
        }

        if (!is_user_effect($result['user']['id'])) {
            api_ajax_return(['status' => 0, 'error' => '账号已被禁用']);
        } elseif (!is_request_ip_effect()) {
            api_ajax_return(['status' => 0, 'error' => '当前IP已被封停']);
        }

        log_login(['request' => json_encode($_REQUEST), 'login_type' => 2, 'user_id' => $result['user']['id']]);
        api_ajax_return(['status' => 1, 'error' => '登录成功']);
    }

    /**
     * 发送手机验证码
     */
    public function send_mobile_verify()
    {
        if (app_conf("SMS_ON") == 0) {
            api_ajax_return(['status' => 0, 'error' => '短信未开启']);
        }

        $mobile = strim($_REQUEST['mobile']);
        if (empty($mobile)) {
            api_ajax_return(['status' => 0, 'error' => '请输入你的手机号']);
        }

        if ((!defined('OPEN_YPSMS') || OPEN_YPSMS == 0) && !check_mobile($mobile)) {
            api_ajax_return(['status' => 0, 'error' => '请填写正确的手机号码']);
        }

        if (in_array($mobile, ['13888888888', '13999999999'])) {
            api_ajax_return(['status' => 1, 'time' => 60, 'error' => '发送成功']);
        }

        $root = check_sms_send($mobile);
        if (!$root['status']) {
            api_ajax_return($root);
        }

        if (!is_mobile_effect($mobile)) {
            api_ajax_return(['status' => 0, 'error' => '账号已被禁用']);
        }

        if (has_already_send($mobile)) {
            api_ajax_return(['status' => 0, 'error' => '发送速度太快了']);
        }

        $status = send_mobile_code($mobile);
        if ($status['is_success']) {
            api_ajax_return(['status' => 1, 'time' => 60, 'error' => $status['title'] . $status['result']]);
        } else {
            api_ajax_return(['status' => 0, 'error' => '短信验证码发送失败']);
        }
    }

    //退出登陆
    public function login_out()
    {
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        }

        fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
        $result = loginout_user();

        es_session::delete("user_info");
        es_session::delete("wx_info");
        $root['status'] = 1;
        $root['error'] = "登出成功";

        api_ajax_return($root);
    }

    public function is_login()
    {
        api_ajax_return([
            'status' => $GLOBALS['user_info']['id'] > 0 ? 1 : 0,
        ]);
    }
}