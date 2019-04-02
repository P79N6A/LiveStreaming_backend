<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

class heat_rank_auto_cache extends auto_cache
{
    private $key = "rank:heat";

    /**
     * 加载热度排行榜 600s
     * @param $param
     * @return array|bool|mixed|string
     */
    public function load($param)
    {
        //$a['param'] = $param;
        // $this->key .= $this->get_key();
        // $key_bf = $this->key . '_bf';
        // $list = $GLOBALS['cache']->get($this->key, true);
        //$a['key'] = $this->key;
        $a['is_first'] = 0;
        // if ($list == false) {
        //     $a['is_first'] = 1;
        //     $is_ok = $GLOBALS['cache']->set_lock($this->key);
        //     if (!$is_ok) {
        //         $list = $GLOBALS['cache']->get($key_bf, true);
        //     } else {
        //缓存更新时间
        // $rank_cache_time = 5;
        $list_arr = $this->rank_ceil();
        //log_file('new_key','heat_rank');
        //log_file($this->key,'heat_rank');
        $list = $list_arr[0];
        //log_file($list,'heat_rank');
        foreach ($list as $k => &$v) {
            $v['head_image'] = get_spec_image($v['head_image'], 150, 150);
        }
        if (empty($list)) {
            $list = array();
        }

        // if ($list) {
        //     //数据处理结束
        //     $GLOBALS['cache']->set($this->key, $list, $rank_cache_time, true);

        //     $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
        // }
        //     }
        // }

        if (empty($list)) {
            $list = array();
        }

        $a['list'] = $list;
        // $a['cache_next_time'] = $key[1];
        return $a;
    }
    /**
     * 热度排行榜数据
     * @return array
     */
    public function rank_ceil()
    {
        //$sql = "SELECT v.id as video_id,v.live_in,v.heat_value,u.id as user_id,u.nick_name,u.head_image,u.user_level,u.sex,u.v_type,u.v_icon,u.signature,rh.heat_amount,IF(rh.heat_amount,CONCAT_WS('','成为热门第一',rh.heat_amount,'次') ,'')as heat_msg from " . DB_PREFIX . "video as v LEFT JOIN " . DB_PREFIX . "user as u on u.id = v.user_id LEFT JOIN " . DB_PREFIX . "rank_heat_all as rh ON rh.user_id=u.id where v.live_in = 1 and v.heat_value > 0 ORDER BY v.heat_value desc ";
        $sql = "SELECT v.id AS video_id, v.live_in, v.heat_value, v.group_id, v.user_id, u.nick_name, u.head_image, u.user_level, u.sex, u.v_type, u.v_icon, u.signature, rh.heat_amount FROM " . DB_PREFIX . "video AS v LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = v.user_id LEFT JOIN " . DB_PREFIX . "rank_heat_all AS rh ON rh.user_id = u.id WHERE v.live_in IN (1,3) ORDER BY v.heat_value DESC, v.vote_number DESC, u.id ASC";
        $rank_info = $GLOBALS['db']->getAll($sql, true, true);

        $rank_update_time = NOW_TIME;
        $rank_update_date = to_date($rank_update_time);
        $sql1 = "UPDATE " . DB_PREFIX . "video set total_heat_value = total_heat_value + heat_value,heat_value = 0,rank_update_time= " . $rank_update_time . ",rank_update_date = '" . $rank_update_date . "'  WHERE live_in IN (1,3) AND (UNIX_TIMESTAMP() - rank_update_time >= 7200)";

        //拼接提示
        foreach ($rank_info as $key => $val) {
            $rank_info[$key]['heat_msg'] = '';
            if ($val['heat_amount']) {
                $rank_info[$key]['heat_msg'] = '成为热门第一' . $val['heat_amount'] . '次';
            }
        }
        if ($rank_info) {
            $GLOBALS['db']->query($sql1);
            $user_id = array('user_id' => $rank_info[0]['user_id']);
            //更新获取热度第一次数
            up_heat_all_rank($user_id);
        }
        return array($rank_info, $sql);
    }

    /**
     * 获取KEY
     * @param $param
     * @param int $type
     * @return string
     */
    public function get_key()
    {
        // $t_i = date('i');
        // $H = date('H');
        // if ($t_i < 10) {
        //     $i = 1;
        //     $ii = "00";
        //     $iii = "10";
        // } else if ($t_i < 20) {
        //     $i = 2;
        //     $ii = "10";
        //     $iii = "20";
        // } else if ($t_i < 30) {
        //     $i = 3;
        //     $ii = "20";
        //     $iii = "30";
        // } else if ($t_i < 40) {
        //     $i = 4;
        //     $ii = "30";
        //     $iii = "40";
        // } else if ($t_i < 50) {
        //     $i = 5;
        //     $ii = "40";
        //     $iii = "50";
        // } else {
        //     $i = 6;
        //     $ii = "50";
        //     $iii = "59";
        // }
        // if ($param == 1) {
        //     if ($i > 1) {
        //         $i = $i - 1;
        //     } else {
        //         $H = $H - 1;
        //         $i = 6;
        //     }
        // } elseif ($param == 2) {
        //     if ($i > 2) {
        //         $i = $i - 2;
        //     } else if ($i == 2) {
        //         $H = $H - 1;
        //         $i = 6;
        //     } else {
        //         $H = $H - 1;
        //         $i = 5;
        //     }

        // }
        // if ($H < 10 && $param > 0) {
        //     $H = "0" . $H;
        // }
        // if (intval($type)) {
        //     $key = date('Y-m-d') . " " . $H . ":" . $ii;
        // } else {
        //     $key = ":" . date('Ymd') . $H . $i;
        // }
        // $key1 = date('Y-m-d') . " " . $H . ":" . $iii . ":59";
        // return array($key, $key1);
        $s = intval(date('s') / 5); //10s 更新
        $key = ":" . date('YmdHi') . $s;
        return $key;
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
