<?php

class videoCModule extends baseCModule
{
    public function get_video()
    {
        $id = intval($_REQUEST['id']);
        $user_id = $GLOBALS['user_info']['id'];

        if (!$id) {
            api_ajax_return(['status' => 404, 'error' => '直播间不存在']);
        }

        $video = get_video_by_id($id);
        if (empty($video)) {
            api_ajax_return(['status' => 404, 'error' => '直播间不存在']);
        }

        $error = '';
        $tim = null;
        if ($video['user_id'] == $user_id) {

        } elseif (!in_array($video['live_in'], [1, 3])) { //直播已结束
            unset($video['play_url']);
            unset($video['play_mp4']);
            unset($video['play_hls']);
            unset($video['play_flv']);
            $error = '直播已结束';
        } elseif ($video['is_live_pay']) { //付费直播
            // 判断是否付费
            unset($video['play_url']);
            unset($video['play_mp4']);
            unset($video['play_hls']);
            unset($video['play_flv']);
            $error = '主播开启了付费直播，请下载APP观看';
        } elseif ($video['room_type'] == 1) { //1:私密直播;3:直播
            // 判断是否邀请
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();
            if ($video_private_redis->check_user_push($id, $user_id) == false) {
                unset($video['play_url']);
                unset($video['play_mp4']);
                unset($video['play_hls']);
                unset($video['play_flv']);
                $error = "私聊群,用户不在邀请名单中";
            }
        }

        if(empty($error)){
            $tim_user_id = $user_id > 0 ? $user_id : 0;
            $usersig = load_auto_cache("usersig", array("id" => $tim_user_id));
            $m_config = load_auto_cache("m_config");
            $tim = [
                'sdkappid' => $m_config['tim_sdkappid'],
                'account_type' => $m_config['tim_account_type'],
                'account_id' => $tim_user_id,
                'usersig' => $usersig['usersig'],
                'allow_send' => $user_id && !$video['is_live_pay'] && in_array($video['live_in'], [1, 3]) ? 1 : 0,
            ];
        }

        $podcast = get_podcast_by_id($video['user_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($user_id);
        $podcast['has_focus'] = $user_id == $video['user_id'] ? -1 : ($userfollw_redis->is_following($video['user_id']) ? 1 : 0);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
        $video_viewer_redis = new VideoViewerRedisService();
        $viewer = $video_viewer_redis->get_viewer_list2($id, 1, 10);

        $data = [
            'video' => $video,
            'podcast' => $podcast,
            'user' => $GLOBALS['user_info'],
            'prop_list' => load_auto_cache("prop_list"),
            'viewer' => $viewer,
            'tim' => $tim,
            'error' => $error,
        ];

        api_ajax_return($data);
    }

    /**
     * 贡献榜（当天，所有）
     * room_id: ===>如果有值，则取：本场直播贡献榜排行
     * user_id:===>取某个用户的：总贡献榜排行
     * p:不传或传0;则取前50位排行
     */
    public function cont()
    {
        $room_id = intval($_REQUEST['room_id']);//当前正在直播的房间id
        $user_id = intval($_REQUEST['user_id']);//被查看的用户id
        if ($room_id == 0 && $user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);//取当前用户的id
        }
        if ($room_id == 0 && $user_id == 0) {
            api_ajax_return(['status' => 0, 'error' => '房间ID跟用户ID必须传一个']);
        }

        $page = intval($_REQUEST['p']);//取第几页数据
        $page_size = 50;

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
        $video_con = new VideoContributionRedisService($user_id);

        if ($room_id > 0) {
            //本场直播贡献榜排行
            $root = $video_con->get_video_contribute($room_id, $page, $page_size);
            $root['total_num'] = intval($root['total_ticket_num']);
            $root['v_icon'] = $root['user']['v_icon'];
            $root['user']['ticket'] = intval($root['user']['ticket']);
        } else {
            //总贡献榜排行
            //用户总票数
            $root = $video_con->get_podcast_contribute($user_id, $page, $page_size);
            $root['total_num'] = intval(floor($root['user']['ticket']));
            $root['user']['nick_name'] = emoji_decode($root['user']['nick_name']);
        }
        foreach ($root['list'] as $k => $v) {
            $root['list'][$k]['nick_name'] = emoji_decode($root['list'][$k]['nick_name']);
            $root['list'][$k]['use_ticket'] = intval($root['list'][$k]['num']);
            $root['list'][$k]['ticket'] = intval($root['list'][$k]['ticket']);
        }

        api_ajax_return($root);
    }
}