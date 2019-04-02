<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
//fanwe_require(APP_ROOT_PATH.'mapi/lib/index.action.php');
class activityCModule extends baseModule
{
    public function index()
    {
        $m_config = load_auto_cache('m_config');

        if (empty($m_config['activity_prop_id'])) {
            api_ajax_return(array('status' => 0, 'error' => '活动礼物还没有设置'));
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_list = load_auto_cache('activity_user_list', array('prop_id' => $m_config['activity_prop_id'], 'start_time' => strtotime($m_config['activity_start_time']), 'end_time' => strtotime($m_config['activity_end_time'])));

        foreach ($user_list as &$value) {
            $user = $user_redis->getRow_db($value['from_user_id'], array('nick_name', 'head_image'));
            $value['nick_name'] = $user['nick_name'];
            $value['head_image'] = get_spec_image($user['head_image']);
        }
        $anchor_list = load_auto_cache('activity_anchor_list', array('prop_id' => $m_config['activity_prop_id'], 'start_time' => strtotime($m_config['activity_start_time']), 'end_time' => strtotime($m_config['activity_end_time'])));
        foreach ($anchor_list as &$value) {
            $user = $user_redis->getRow_db($value['to_user_id'], array('nick_name', 'head_image'));
            $value['nick_name'] = $user['nick_name'];
            $value['head_image'] = get_spec_image($user['head_image']);
        }
        $root['status'] = 1;
        $root['start_time'] = $m_config['activity_start_time'];
        $root['end_time'] = $m_config['activity_end_time'];
        $root['user_list'] = $user_list;
        $root['anchor_list'] = $anchor_list;
        api_ajax_return($root);
    }
}
