<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class rankh5Module extends baseModule
{
    private $live_list;

    private function get_live_list()
    {
        if (is_null($this->live_list)) {
            $live_list = load_auto_cache('select_video');
            foreach ($live_list as $item) {
                $this->live_list[$item['user_id']] = $item;
            }
        }
        return $this->live_list;
    }

    private function get_rank($list, $type, $page_size = 20)
    {
        $last_ranks = array();
        $last_rank_num = count($list['pre_' . $type]);
        for ($i = 0; $i < $last_rank_num; $i++) {
            $last_ranks[$list['pre_' . $type][$i]['user_id']] = $i;
        }

        $user_id = $GLOBALS['user_info']['id'];
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $user_follow = new UserFollwRedisService($user_id);
        $user_follow_ids = $user_follow->following();
        $items = array();
        $live_list = $this->get_live_list();
        for ($i = 0, $l = min(count($list[$type]), $page_size); $i < $l; $i++) {
            $item = $list[$type][$i];
            if ($last_rank_num == 0) {
                $rank = 'none';
            } elseif (!isset($last_ranks[$item['user_id']])) {
                $rank = 'up';
            } elseif ($last_ranks[$item['user_id']] > $i) {
                $rank = 'up';
            } elseif ($last_ranks[$item['user_id']] < $i) {
                $rank = 'down';
            } else {
                $rank = 'none';
            }

            $has_focus = $user_id == $item['user_id'] ? -1 : (in_array($item['user_id'], $user_follow_ids) ? 1 : 0);
            if (isset($live_list[$item['user_id']])) {
                $live = $live_list[$item['user_id']];
                $items[] = array(
                    'id' => $item['user_id'],
                    'nick_name' => $item['nick_name'],
                    'head_image' => get_spec_image($item['head_image']),
                    'live_in' => $live['live_in'],
                    'room_id' => $live['room_id'],
                    'ticket' => isset($item['use_ticket']) ? $item['use_ticket'] : $item['ticket'],
                    'rank' => $rank,
                    'has_focus' => $has_focus,
                    'group_id' => $live['group_id'],
                    'create_type' => $live['create_type'],
                    'video_type' => $live['live_in'] == 1 ? 0 : 1,
                    'live_image' => $live['live_image']
                );
            } else {
                $items[] = array(
                    'id' => $item['user_id'],
                    'nick_name' => $item['nick_name'],
                    'head_image' => get_spec_image($item['head_image']),
                    'live_in' => "0",
                    'ticket' => isset($item['use_ticket']) ? $item['use_ticket'] : $item['ticket'],
                    'rank' => $rank,
                    'has_focus' => $has_focus
                );
            }
        }
        return $items;
    }

    public function rank_all()
    {
        $m_config = load_auto_cache('m_config');
        $charm_list = load_auto_cache('charm_podcast', array('page_size' => 50));
        $rich_list = load_auto_cache('rich_list', array('page_size' => 50));

        $root = array(
            'status' => 1,
            'ticket_name' => $m_config['ticket_name'],
            'anchor' => array(
                'hour' => $this->get_rank($charm_list, 'hour', 50),
                'last_hour' => $this->get_rank($charm_list, 'last_hour', 3),
                'day' => $this->get_rank($charm_list, 'day'),
                'week' => $this->get_rank($charm_list, 'weeks')
            ),
            'user' => array(
                'day' => $this->get_rank($rich_list, 'day'),
                'week' => $this->get_rank($rich_list, 'weeks')
            )
        );
        api_ajax_return($root);
    }

    //播主小时榜
    public function rank_anchor_hour()
    {
        $m_config = load_auto_cache('m_config');
        $charm_list = load_auto_cache('charm_podcast', array('page_size' => 50));
        $items = $this->get_rank($charm_list, 'hour', 50);
        $last_items = $this->get_rank($charm_list, 'last_hour', 3);
        $root = array(
            'status' => 1,
            'error' => '',
            'ticket_name' => $m_config['ticket_name'],
            'items' => $items,
            'last_items' => $last_items
        );
        api_ajax_return($root);
    }

    public function rank_anchor_last_hour()
    {
        $m_config = load_auto_cache('m_config');
        $charm_list = load_auto_cache('charm_podcast', array('page_size' => 50));
        $items = $this->get_rank($charm_list, 'last_hour');
        $root = array('status' => 1, 'error' => '', 'ticket_name' => $m_config['ticket_name'], 'items' => $items);
        api_ajax_return($root);
    }

    //播主日榜
    public function rank_anchor_day()
    {
        $m_config = load_auto_cache('m_config');
        $charm_list = load_auto_cache('charm_podcast', array('page_size' => 50));
        $items = $this->get_rank($charm_list, 'day');
        $root = array('status' => 1, 'error' => '', 'ticket_name' => $m_config['ticket_name'], 'items' => $items);
        api_ajax_return($root);
    }

    //播主周榜
    public function rank_anchor_week()
    {
        $m_config = load_auto_cache('m_config');
        $charm_list = load_auto_cache('charm_podcast', array('page_size' => 50));
        $items = $this->get_rank($charm_list, 'weeks');
        $root = array('status' => 1, 'error' => '', 'ticket_name' => $m_config['ticket_name'], 'items' => $items);
        api_ajax_return($root);
    }

    //用户日榜
    public function rank_user_day()
    {
        $m_config = load_auto_cache('m_config');
        $rich_list = load_auto_cache('rich_list', array('page_size' => 50));
        $items = $this->get_rank($rich_list, 'day');
        $root = array('status' => 1, 'error' => '', 'ticket_name' => $m_config['ticket_name'], 'items' => $items);
        api_ajax_return($root);
    }

    //用户周榜
    public function rank_user_week()
    {
        $m_config = load_auto_cache('m_config');
        $rich_list = load_auto_cache('rich_list', array('page_size' => 50));
        $items = $this->get_rank($rich_list, 'weeks');
        $root = array('status' => 1, 'error' => '', 'ticket_name' => $m_config['ticket_name'], 'items' => $items);
        api_ajax_return($root);
    }
}
