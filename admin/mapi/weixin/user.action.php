<?php

class userCModule extends baseCModule
{
    /*
	 * 个人主页
	 */
    public function user_home()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $to_user_id = intval($_REQUEST['to_user_id']) ?: $user_id;  //被查看的用户id
        $root = getuserinfo($user_id, 0, $to_user_id);
        $root['status'] = 1;

        if (!empty($root['user'])) {
            $root['user']['level_ico'] = get_spec_image("./public/images/rank/rank_" . $root['user']['user_level'] . ".png");
        }

        api_ajax_return($root);
    }

    /**
     * 直播回看
     */
    public function user_review()
    {
        $root = array();

        //$GLOBALS['user_info']['id'] = 1;


        $to_user_id = intval($_REQUEST['to_user_id']);//被查看的用户id
        if ($to_user_id == 0) {
            $to_user_id = intval($GLOBALS['user_info']['id']);
        }

        $sort = intval($_REQUEST['sort']);//排序类型; 0:最新;1:最热


        $page = intval($_REQUEST['p']);//取第几页数据
        if ($page == 0) {
            $page = 1;
        }
        $page_size = 10;

        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $sort_field = "vh.begin_time desc";
        if ($sort == 1) {
            $sort_field = "vh.max_watch_number desc";
        }

        //video_count
        if ($to_user_id == intval($GLOBALS['user_info']['id'])) {
            $sql = "select vh.id,vh.id as room_id,vh.group_id,vh.live_in,vh.title,vh.begin_time,vh.max_watch_number,vh.video_vid,vh.video_type,vh.channelid,vh.create_time,u.id as user_id,u.head_image,u.nick_name from " . DB_PREFIX . "video_history as vh left join " . DB_PREFIX . "user as u on u.id= vh.user_id  where vh.group_id!='' and  vh.is_delete = 0 and vh.is_del_vod = 0 and vh.user_id = '" . $to_user_id . "' order by " . $sort_field . " limit " . $limit;
        } else {
            $sql = "select vh.id,vh.id as room_id,vh.group_id,vh.live_in,vh.title,vh.begin_time,vh.max_watch_number,vh.video_vid,vh.video_type,vh.channelid,vh.create_time,u.id as user_id,u.head_image,u.nick_name from " . DB_PREFIX . "video_history as vh left join " . DB_PREFIX . "user as u on u.id= vh.user_id  where vh.group_id!='' and  vh.is_delete = 0 and vh.is_del_vod = 0 and vh.is_live_pay = 0 and user_id = '" . $to_user_id . "' order by " . $sort_field . " limit " . $limit;
        }

        $list = array();
        $list_arr = array();
        $list_info = $GLOBALS['db']->getAll($sql);
        foreach ($list_info as $k => $v) {
            $list_arr = $v;
            $list_arr['head_image'] = get_spec_image($v['head_image'], 150, 150);
            $list_arr['begin_time_format'] = format_show_date($v['begin_time']);
            if ($v['max_watch_number'] > 10000) {
                $list_arr['watch_number_format'] = round($v['max_watch_number'] / 10000, 2) . "万";
            } else {
                $list_arr['watch_number_format'] = $v['max_watch_number'];
            }

            $list_arr['max_watch_number'] = $v['max_watch_number'];
            //20170930 Ios端逻辑处理添加 @slf
            $list_arr['live_in'] = 3;

            if ($v['title'] == '') {
                $list_arr['title'] = "....";
            }

            $list[] = $list_arr;

        }
        $root['list'] = $list;

        if ($to_user_id == intval($GLOBALS['user_info']['id'])) {
            $sql = "select count(*)  from " . DB_PREFIX . "video_history as vh left join " . DB_PREFIX . "user as u on u.id= vh.user_id  where vh.group_id!='' and  vh.is_delete = 0 and vh.is_del_vod = 0 and vh.user_id = '" . $to_user_id . "' order by " . $sort_field;
        } else {
            $sql = "select count(*)  from " . DB_PREFIX . "video_history as vh left join " . DB_PREFIX . "user as u on u.id= vh.user_id  where vh.group_id!='' and  vh.is_delete = 0 and vh.is_del_vod = 0 and vh.is_live_pay = 0 and user_id = '" . $to_user_id . "' order by " . $sort_field;
        }

        $count = $GLOBALS['db']->getOne($sql);
        //$count = count($list);
        if ($count >= $page * $page_size) {
            $root['has_next'] = 1;
        } else {
            $root['has_next'] = 0;
        }

        $root['count'] = $count;
        if ($to_user_id == intval($GLOBALS['user_info']['id']) && $page == 1) {

            $sql = "update " . DB_PREFIX . "user set video_count = " . $root['count'] . " where id = " . intval($GLOBALS['user_info']['id']) . " and video_count!=" . $root['count'];
            $GLOBALS['db']->query($sql);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data = array();
            $user_data['video_count'] = $root['count'];
            $user_redis->update_db(intval($GLOBALS['user_info']['id']), $user_data);
        }

        $root['page'] = $page;
        $root['status'] = 1;

        ajax_return($root);
    }

    /**
     * 关注的用户，最多不超过100个
     */
    public function user_follow()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $to_user_id = intval($_REQUEST['to_user_id']) ?: $user_id;  //被查看的用户id

        $page = intval($_REQUEST['p']) ?: 1;
        $page_size = 20;

        $list = get_user_follow($user_id, $to_user_id, $page, $page_size);
        foreach ($list as &$item) {
            $item['level_ico'] = get_spec_image("./public/images/rank/rank_" . $item['user_level'] . ".png");
        }
        unset($item);

        api_ajax_return([
            'status' => 1,
            'list' => $list,
            'has_next' => count($list) == $page_size ? 1 : 0,
            'page' => $page,
        ]);
    }

    /**
     * 粉丝列表，最多不超过100个
     */
    public function user_focus()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $root = array();
        $root['status'] = 1;

        $user_id = intval($GLOBALS['user_info']['id']);
        $to_user_id = intval($_REQUEST['to_user_id']) ?: $user_id;  //被查看的用户id

        $page = intval($_REQUEST['p']) ?: 1;
        $page_size = 20;
        $list = get_user_focus($user_id, $to_user_id, $page, $page_size);
        foreach ($list as &$item) {
            $item['level_ico'] = get_spec_image("./public/images/rank/rank_" . $item['user_level'] . ".png");
        }
        unset($item);

        api_ajax_return([
            'status' => 1,
            'list' => $list,
            'has_next' => count($list) == $page_size ? 1 : 0,
            'page' => $page,
        ]);
    }


    /**
     * 设置黑名单
     */
    public function set_black()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);//当前用户;
        $to_user_id = intval($_REQUEST['to_user_id']);//被关注或取消关注的用户

        if ($user_id == $to_user_id) {
            api_ajax_return([
                'error' => '不能设置自己！',
                'status' => 0,
            ]);
        }

        $root = set_black($user_id, $to_user_id);
        ajax_return($root);
    }

    /**
     * 搜索用户列表
     * 模糊搜索
     */
    public function search()
    {
        if (!$GLOBALS['user_info']['id']) {
            api_ajax_return([
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ]);
        }

        $user_id = intval($GLOBALS['user_info']['id']);//当前用户;
        $keyword = strim($_REQUEST['keyword']);//搜索关键字
        if (empty($keyword)) {
            api_ajax_return(['status' => 1, 'has_next' => 0, 'list' => [], 'page' => 0]);
        }

        $page = intval($_REQUEST['p']) ?: 1;
        $page_size = 20;

        $list = user_search($keyword, $page, $page_size);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $user_redis = new UserFollwRedisService($user_id);
        $keys = $user_redis->following();
        foreach ($list as $k => &$v) {
            $has_focus = $user_id == $v['user_id'] ? -1 : (in_array($v['user_id'], $keys) ? 1 : 0);
            $v['has_focus'] = $has_focus;
            $v['head_image'] = get_spec_image($v['head_image']);
            $v['signature'] = htmlspecialchars_decode($v['signature']);
            $v['nick_name'] = htmlspecialchars_decode($v['nick_name']);
            $v['signature'] = emoji_decode($v['signature']);
            $v['nick_name'] = emoji_decode($v['nick_name']);
            $v['level_ico'] = get_spec_image("./public/images/rank/rank_" . $v['user_level'] . ".png");
        }
        unset($v);

        api_ajax_return([
            'status' => 1,
            'has_next' => count($list) == $page_size ? 1 : 0,
            'list' => $list,
            'page' => $page
        ]);
    }
}
