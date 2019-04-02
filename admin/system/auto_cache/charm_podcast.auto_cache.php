<?php

class charm_podcast_auto_cache extends auto_cache
{
    private $key = "rank:charm_podcast";

    public function load($param, $cache = true)
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';

        $list = $GLOBALS['cache']->get($this->key, true);

        if ($list === false || !$cache) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                //缓存更新时间
                $rank_cache_time = intval($m_config['rank_cache_time']) > 0 ? intval($m_config['rank_cache_time']) : 300;
                //数据处理
                $types = isset($param['types']) ? $param['types'] : [
                    'hour',
                    'pre_hour',
                    'last_hour',
                    'day',
                    'pre_day',
                    'weeks',
                    'pre_weeks',
                    'month',
                    'all'
                ];
                $list = $this->charm_ceil($types, isset($param['page_size']) ? $param['page_size'] : 10);

                //数据处理结束
                $GLOBALS['cache']->set($this->key, $list, $rank_cache_time, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
        }

        if ($list == false) {
            $list = array();
        }

        return $list;
    }

    //给榜单中正在直播中的主播添加直播链接
    public function is_live($data, $live_list)
    {
        foreach ($data as $k => $v) {
            foreach ($live_list as $kk => $vv) {
                if ($vv['user_id'] == $v['user_id']) {
                    $data[$k]['live_in'] = $vv['live_in'];
                    $data[$k]['room_id'] = $vv['room_id'];
                    $data[$k]['watch_number'] = $vv['watch_number'];
                    $data[$k]['title'] = $vv['title'];
                    if ($vv['live_in'] == 3) {
                        $data[$k]['video_url'] = get_video_url($vv['room_id'], $vv['live_in']);
                    } else {
                        $data[$k]['video_url'] = "/" . intval($vv['user_id']);
                    }

                    $data[$k]['group_id'] = $vv['group_id'];
                    $data[$k]['live_image'] = get_spec_image(empty($vv['live_image']) ? $vv['head_image'] : $vv['live_image']);
                    $data[$k]['create_type'] = $vv['create_type'];
                    $data[$k]['video_type'] = $vv['video_type'];
                }
            }
            if (empty($data[$k]['video_url'])) {
                $data[$k]['video_url'] = "/" . intval($v['user_id']);
            }
            $data[$k]['user_level_ico'] = get_spec_image("./public/images/rank/rank_" . $v['user_level'] . ".png");
            $data[$k]['nick_name'] = ($v['nick_name']);
        }
        return $data;
    }

    //当前直播
    public function get_live()
    {
        $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, v.create_type,(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number,v.live_image, v.head_image,v.thumb_head_image, v.xpoint,v.ypoint,u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) and v.room_type = 3 order by v.create_time,v.sort_num desc,v.sort desc";
        $live_list = $GLOBALS['db']->getAll($sql, true, true);

        return $live_list;
    }

    // 魅力排行榜数据
    public function charm_ceil($types, $page_size = 10)
    {
        $pre_time = NOW_TIME - 60 * 10;
        $limit = " 0,{$page_size} "; //取前十

        $live_list = $this->get_live();
        if (in_array('hour', $types)) {
            $root['hour'] = $this->is_live($this->hour($limit), $live_list);
        }

        if (in_array('pre_hour', $types)) {
            $root['pre_hour'] = $this->is_live($this->hour($limit, $pre_time), $live_list);
        }

        if (in_array('last_hour', $types)) {
            $root['last_hour'] = $this->is_live($this->last_hour($limit), $live_list);
        }

        if (in_array('month', $types)) {
            $root['month'] = $this->is_live($this->month($limit), $live_list);
        }
        if (in_array('day', $types)) {
            $root['day'] = $this->is_live($this->day($limit), $live_list);
        }
        if (in_array('pre_day', $types)) {
            $root['pre_day'] = $this->is_live($this->day($limit, $pre_time), $live_list);
        }
        if (in_array('weeks', $types)) {
            $root['weeks'] = $this->is_live($this->weeks($limit), $live_list);
        }
        if (in_array('pre_weeks', $types)) {
            $root['pre_weeks'] = $this->is_live($this->weeks($limit, $pre_time), $live_list);
        }

        if (in_array('all', $types)) {

            //总榜
            $sql = "select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,u.ticket as ticket,u.is_authentication from " . DB_PREFIX . "user as u where u.is_effect=1 and u.ticket>0 order BY u.ticket desc limit " . $limit;
            $root['all'] = $this->is_live($GLOBALS['db']->getAll($sql), $live_list);
        }
        return $root;
    }

    private function hour($limit, $pre_time = NOW_TIME)
    {
        $hour = to_timespan(to_date(NOW_TIME, 'Y-m-d H:00:00'));
        return $this->get_data("create_time >= {$hour}  and is_red_envelope = 0", $limit, $pre_time);
    }

    private function last_hour($limit)
    {
        $hour = to_timespan(to_date(NOW_TIME, 'Y-m-d H:00:00'));
        $last_hour = $hour - 3600;
        return $this->get_data("create_time >= {$last_hour}  and is_red_envelope = 0", $limit, $hour);
    }

    private function day($limit, $pre_time = NOW_TIME)
    {
        return $this->get_data("create_d = day(curdate()) and is_red_envelope = 0", $limit, $pre_time);
    }

    private function weeks($limit, $pre_time = NOW_TIME)
    {
        return $this->get_data('create_w = WEEK(curdate()) and is_red_envelope = 0', $limit, $pre_time);
    }

    private function month($limit)
    {
        return $this->get_data("create_ym = " . to_date(NOW_TIME, 'Ym') . " and is_red_envelope = 0", $limit, NOW_TIME);
    }

    private function get_data($where, $limit, $pre_time)
    {
        $table = DB_PREFIX . 'video_prop_' . to_date(NOW_TIME, 'Ym');
        $last_month_table = DB_PREFIX . 'video_prop_' . to_date(to_timespan("first day of last month"), 'Ym');

        $sql = "SELECT u.id AS user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,u.is_authentication,sum(v.total_ticket) AS use_ticket FROM " . DB_PREFIX . "user AS u INNER JOIN( SELECT to_user_id , total_ticket FROM {$table} WHERE {$where} AND create_time < {$pre_time} UNION ALL SELECT to_user_id , total_ticket FROM {$last_month_table} WHERE {$where} AND create_time < {$pre_time}) AS v ON u.id = v.to_user_id WHERE u.is_effect = 1 GROUP BY v.to_user_id ORDER BY use_ticket DESC LIMIT {$limit}";

        return $GLOBALS['db']->getAll($sql);
    }

    public function rm()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
