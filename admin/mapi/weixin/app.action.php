<?php

fanwe_require(APP_ROOT_PATH . 'mapi/lib/app.action.php');

class appCModule extends appModule
{
    public function init()
    {
        $m_config = load_auto_cache("m_config");//初始化手机端配置

        $root = [];
        $root['listmsg'] = load_auto_cache("article_notice");

        $root['has_wxgzh_login'] = intval($m_config['has_wxgzh_login']);//H5支持微信公众号登陆
        $root['h5_logo'] = get_spec_image($m_config['h5_logo']);

        $root['short_name'] = strim($m_config['short_name']);
        $root['ticket_name'] = strim($m_config['ticket_name']);
        $root['diamonds_name'] = strim($m_config['diamonds_name']) ?: '秀豆';
        $root['account_name'] = strim($m_config['account_name']);//app账号名称  账

        //首页其他分类
        $video_classified = load_auto_cache("video_classified");
        $root['video_classified'] = $video_classified ?: [];

        //发言等级
        $root['speak_level']=intval($m_config['speak_level']);
        //用户开启发言功能的最低等级
        $root['send_msg_lv'] = intval($m_config['send_msg_lv']);
        //用户开启私信功能的最低等级
        $root['private_letter_lv'] = intval($m_config['private_letter_lv']);


        api_ajax_return($root);
    }
}
