<?php

class shareCModule extends baseCModule
{
    public function video()
    {
        $id = intval($_REQUEST['id']);
        $m_config = load_auto_cache('m_config');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $video = $video_redis->getRow_db($id, ['id', 'live_image']);
        if (!$video['id']) {
            api_ajax_return(['status' => 404, 'error' => '直播间不存在']);
        }

        $share_url = '/frontEnd/weixin/#/live/room_app/' . $id;

        fanwe_require(APP_ROOT_PATH . "system/utils/jssdk.php");
        $wx_sdk = new JSSDK($m_config['wx_gz_appid'], $m_config['wx_gz_secrit']);
        $wx_sdk->set_url($share_url);

        $user_info = $GLOBALS['userinfo'];

        $share = [
            'short_name' => strim($m_config['short_name']),
            'share_title' => strim($m_config['share_title']),
            'share_img_url' => empty($user_info['head_image']) ? get_spec_image($video['live_image']) : get_spec_image($user_info['head_image']),
            'share_wx_url' => $share_url,
            'share_desc' => strim($m_config['share_title']) . $user_info['nick_name'] . '正在直播,快来一起看~'
        ];

        api_ajax_return(['status' => 1, 'sign' => $wx_sdk->getSignPackage(), 'share' => $share]);
    }

    public function sign()
    {
        $m_config = load_auto_cache('m_config');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');

        $share_url = strim($_REQUEST['url']);

        if (empty($share_url)) {
            api_ajax_return(['status' => 0, 'error' => '参数为空']);
        }

        fanwe_require(APP_ROOT_PATH . "system/utils/jssdk.php");
        $wx_sdk = new JSSDK($m_config['wx_gz_appid'], $m_config['wx_gz_secrit']);
        $wx_sdk->set_url($share_url);

        api_ajax_return(['status' => 1, 'sign' => $wx_sdk->getSignPackage()]);
    }
}
