<?php


class discoveryModule extends baseModule
{
    public function index()
    {
        if (!$GLOBALS['user_info']) {
            $user_id = 0;
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
        }
        $page = intval($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

        $root = array(
            'has_next' => 1,
            'page' => $page,
            'status' => 1,
            'error' => ''
        );
        $page_size = 20;
        $list = load_auto_cache("select_weibo_recommond",
            array('page' => $page, 'page_size' => $page_size, 'user_id' => $user_id, 'type' => '"video"'));
        $root['list'] = $list;
        if (count($list) == $page_size) {
            $root['has_next'] = 1;
        } else {
            $root['has_next'] = 0;
        }

        api_ajax_return($root);
    }

    public function video()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            return api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $to_user_id = intval($_REQUEST['to_user_id']);//被查看的用户ID
        if (!$to_user_id) {
            $to_user_id = $user_id;
        }

        $page = intval($_REQUEST['page']); //取第几页数据
        $page = $page >= 1 ? $page : 1;
        $page_size = 10;

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');

        if ($user_id == $to_user_id) {
            $list = load_auto_cache("select_weibo_list",
                array('page' => $page, 'page_size' => $page_size, 'user_id' => $user_id));
        } else {
            $list = load_auto_cache("select_weibo_other_list",
                array('page' => $page, 'page_size' => $page_size, 'to_user_id' => $to_user_id));
            if ($user_id > 0) {
                $pay_digg_list = load_auto_cache("select_user_pay_list",
                    array('page' => $page, 'page_size' => $page_size, 'user_id' => $user_id));
                $diggs_array = $pay_digg_list['digg'];

                $user_redis = new UserFollwRedisService($user_id);
                $root['is_focus'] = intval($user_redis->is_following($to_user_id));
            } else {
                $diggs_array = array();
            }

            foreach ($list as $k => $v) {
                if (in_array($v['weibo_id'], $diggs_array)) {
                    $list[$k]['has_digg'] = 1;
                }
                if ($user_id != $v['user_id']) {
                    $list[$k]['is_top'] = 0;
                }
            }
        }

        return api_ajax_return(array(
            'status' => 1,
            'list' => $list,
            'has_next' => count($list) == $page_size ? 1 : 0,
            'page' => $page,
        ));
    }
}
