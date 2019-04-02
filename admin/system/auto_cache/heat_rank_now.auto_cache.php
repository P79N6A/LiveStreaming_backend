<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

class heat_rank_now_auto_cache extends auto_cache
{
    private $key = "rank_now:heat";

    /**
     * 加载热度实时排行榜 60s
     * @param $param
     * @return array|bool|mixed|string
     */
    public function load($param)
    {
        $this->key .= $this->get_key();
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($param == 0) {
            // $this->rm();
            //return $a;
        }

        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                //缓存更新时间
                $rank_cache_time = 600;
                //数据处理
                $list_arr = $this->rank_ceil();
                $list = $list_arr[0];
                foreach ($list as $k => $v) {
                    $list[$k]['nick_name'] = ($v['nick_name']);
                    $list[$k]['head_image'] = get_spec_image($v['head_image'], 150, 150);
                }
                if ($list == false) {
                    $list = '';
                }

                //数据处理结束
                $GLOBALS['cache']->set($this->key, $list, $rank_cache_time, true);

                $GLOBALS['cache']->set($key_bf, $list, 6000, true); //备份
            }
        }

        if ($list == false) {
            $list = array();
        }

        $a['list'] = $list;
        return $a;
    }
    /**
     * 热度排行榜数据
     * @param $cache_time
     * @return array
     */
    public function rank_ceil()
    {
        $sql = "SELECT v.id as video_id,v.live_in,v.heat_value,v.group_id,u.id as user_id,u.nick_name,u.head_image,u.user_level,u.sex,u.v_type,u.v_icon,u.signature from " . DB_PREFIX . "video as v LEFT JOIN " . DB_PREFIX . "user as u on u.id = v.user_id where v.live_in = 1  ORDER BY v.heat_value desc ,u.id ASC ";
        $rank_info = $GLOBALS['db']->getAll($sql, true, true);
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
        $s = intval(date('s') / 10); //10s 更新
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
