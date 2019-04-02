<?php

class inviteModule extends baseModule
{
    public function code()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            return api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id = $GLOBALS['user_info']['id'];

        $is_authentication = $GLOBALS['db']->getOne("select is_authentication from " . DB_PREFIX . "user where id = " . $user_id);
        if ($is_authentication != 2) {
            return api_ajax_return(array(
                'error' => '未通过认证，请先认证',
                'status' => 2,
            ));
        }

        $user_invite = $GLOBALS['db']->getRow("select user_id, code from " . DB_PREFIX . "user_invite where user_id = " . $user_id);
        while (empty($user_invite['code'])) {
            fanwe_require(APP_ROOT_PATH . "system/utils/es_string.php");
            $rand = strtoupper(es_string::rand_string(8, 1));
            if (empty($user_invite['user_id'])) {
                $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                    'user_id' => $user_id,
                    'code' => $rand,
                ));
            } else {
                $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                    'code' => $rand,
                ), 'UPDATE', 'user_id = ' . $user_id);
            }
            if ($GLOBALS['db']->affected_rows() > 0) {
                $user_invite['code'] = $rand;
            }
        }

        return api_ajax_return(array(
            'status' => 1,
            'code' => $user_invite['code'],
        ));
    }

    public function invite_by()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            return api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id = $GLOBALS['user_info']['id'];
        $user_invite = $GLOBALS['db']->getRow("select user_id, invite_by_code from " . DB_PREFIX . "user_invite where user_id = " . $user_id);
        if (!empty($user_invite['invite_by_code'])) {
            return api_ajax_return(array(
                'status' => 0,
                'error' => '邀请码已填写',
            ));
        }

        $code = strim($_REQUEST['code']);
        if (empty($code)) {
            return api_ajax_return(array(
                'status' => 0,
                'error' => '请填写邀请码',
            ));
        }

        $code = strtoupper($code);
        $invite_by_user_id = $GLOBALS['db']->getOne("select user_id from " . DB_PREFIX . "user_invite where code = '{$code}'");
        if (empty($invite_by_user_id) || $user_id == $invite_by_user_id) {
            return api_ajax_return(array(
                'status' => 0,
                'error' => '邀请码不正确',
            ));
        }

        if (empty($user_invite['user_id'])) {
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                'user_id' => $user_id,
                'invite_by_user_id' => $invite_by_user_id,
                'invite_by_code' => $code,
                'is_skip' => 1,
            ));
        } else {
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                'invite_by_user_id' => $invite_by_user_id,
                'invite_by_code' => $code,
                'is_skip' => 1,
            ), 'UPDATE', 'user_id = ' . $user_id);
        }

        return api_ajax_return(array(
            'status' => 1,
            'invite_by_user_id' => $invite_by_user_id,
        ));
    }

    public function is_skip()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            return api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id = $GLOBALS['user_info']['id'];
        $user_invite = $GLOBALS['db']->getRow("select invite_by_code, is_skip from " . DB_PREFIX . "user_invite where user_id = " . $user_id);

        return api_ajax_return(array(
            'status' => 1,
            'is_skip' => $user_invite['is_skip'] || !empty($user_invite['invite_by_code']),
        ));
    }

    public function skip()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            return api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id = $GLOBALS['user_info']['id'];
        $user_invite = $GLOBALS['db']->getRow("select user_id, invite_by_code, is_skip from " . DB_PREFIX . "user_invite where user_id = " . $user_id);

        if (empty($user_invite['user_id'])) {
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                'user_id' => $user_id,
                'is_skip' => 1,
            ));
        } elseif (!$user_invite['is_skip']) {
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_invite", array(
                'is_skip' => 1,
            ), 'UPDATE', 'user_id = ' . $user_id);
        }

        return api_ajax_return(array(
            'status' => 1,
        ));
    }
}
