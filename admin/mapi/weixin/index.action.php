<?php

class indexCModule extends baseCModule
{
    /**
     *   首页
     */
    public function index()
    {
        $list = load_auto_cache("select_video");

        api_ajax_return([
            'status' => 1,
            'banner' => load_auto_cache("banner_list"),
            'list' => $list,
            'page' => 1,
            'has_next' => 0,
        ]);
    }

    /**
     * 我关注的主播 直播
     */
    public function focus_video()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $user_follw_redis = new UserFollwRedisService($user_id);
        $user_list = $user_follw_redis->following();

        //私密直播  video_private,私密直播结束后， 本表会清空
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
        $video_private_redis = new VideoPrivateRedisService();
        $private_list = $video_private_redis->get_video_list($user_id);

        $list = array();

        if (sizeof($private_list) || sizeof($user_list)) {
            $list_all = load_auto_cache("select_video", array('has_private' => 1));

            foreach ($list_all as $v) {
                if ((($v['room_type'] == 1 && in_array($v['room_id'],
                                $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'],
                                $user_list))) && ($v['user_id'] != '13888888888' || $v['user_id'] != '13999999999')
                ) {
                    $list[] = $v;
                } elseif ($v['user_id'] == $user_id && $v['room_type'] == 1 && $v['live_in'] == 1) {
                    $user_video = $v;
                }
            }
        }

        if (!empty($user_video)) {
            array_unshift($list, $user_video);
        }

        $playback = load_auto_cache("playback_list", array('user_id' => $user_id));
        foreach ($playback as &$v) {
            $v['nick_name'] = emoji_decode($v['nick_name']);
        }
        unset($v);

        api_ajax_return([
            'status' => 1,
            'list' => $list,
            'playback' => $playback,
            'page' => 1,
            'has_next' => 0,
        ]);
    }

    /**
     * 自定义分类
     */
    public function classify()
    {
        $classified_id = intval($_REQUEST['classified_id']) ?: 1;
        $list = load_auto_cache("select_video", array('is_classify' => $classified_id));

        api_ajax_return([
            'status' => 1,
            'list' => $list,
            'page' => 1,
            'has_next' => 0,
        ]);
    }

    /**
     * 公会列表的显示
     */
    function society()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $user_society_id = get_user_society_id($user_id);

        $page = intval($_REQUEST['page']) ?: 1;
        $page_size = 20;
        $list = get_society_list($page, $page_size);
        foreach ($list as &$val) {
            if ($user_id == $val['uid']) {//会长
                $val['type'] = 1; // 0 会员 1 会长 2 其它公会会员 3 无公会 4 申请入会 5 申请退会
            } elseif ($user_society_id == $val['id']) {
                // 是否申请退出公会
                $val['type'] = get_user_society_apply_type($user_id, $val['id'], 1) ? 5 : 0;
            } elseif ($user_society_id != 0 && $user_society_id != $val['id']) {//其他公会成员
                $val['type'] = 2;
            } else {
                $val['type'] = get_user_society_apply_type($user_id, $val['id'], 0) ? 4 : 3;
            }

            $val['society_id'] = intval($val['id']);
            $val['society_image'] = get_spec_image($val['logo']);
            $val['society_name'] = $val['name'];
            $val['society_user_count'] = intval($val['user_count']);
            $val['nick_name'] = emoji_decode($val['nick_name']);
            $val['society_chairman'] = $val['nick_name'];
            $val['gh_status'] = intval($val['status']);
        }
        unset($val);

        ajax_return(['status' => 1, 'list' => $list, 'has_next' => count($list) == $page_size ? 1 : 0]);
    }
}