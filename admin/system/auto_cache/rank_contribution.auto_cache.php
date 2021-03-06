<?php

class rank_contribution_auto_cache extends auto_cache
{
    private $key = "rank:contribution:";
    public function load($param)
    {
        $rank_name = strim($param['rank_name']);
        $table = strim($param['table']);
        $page = intval($param['page']);
        $page_size = intval($param['page_size']);
        $cache_time = strim($param['cache_time']);
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $this->key .= $rank_name . '_' . $page;

        $key_bf = $this->key . '_bf';

        $list = $GLOBALS['cache']->get($this->key, true);

        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                if ($rank_name == 'day') {
                    //day
                    $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication from " . $table . " as v LEFT JOIN  " . DB_PREFIX . "user as u on u.id = v.from_user_id where u.is_effect=1 and v.create_ym=" . to_date(NOW_TIME, 'Ym') . " and v.create_d=" . to_date(NOW_TIME, 'd') . " GROUP BY v.from_user_id order BY ticket desc limit " . $limit;
                } elseif ($rank_name == 'month') {
                    //month
                    $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket ,u.is_authentication from  " . $table . " as v LEFT JOIN " . DB_PREFIX . "user as u on u.id = v.from_user_id where u.is_effect=1 and v.create_ym=" . to_date(NOW_TIME, 'Ym') . " GROUP BY v.from_user_id order BY ticket desc limit " . $limit;
                } else if ($rank_name == 'days') {
                    $start_time = time() - 3 * 24 * 60 * 60;
                    $sql = "select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket,u.is_authentication from  " . $table . " as v LEFT JOIN " . DB_PREFIX . "user as u  on u.id = v.from_user_id where u.is_effect=1 and v.create_time<=" . time() . " and v.create_time>=" . $start_time . " and v.is_red_envelope = 0 GROUP BY v.to_user_id order BY ticket desc limit " . $limit;
                } else if ($rank_name == 'week') {
                    $sql = "select u.id as user_id,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_diamonds) as ticket,u.is_authentication from  " . $table . " as v LEFT JOIN " . DB_PREFIX . "user as u  on u.id = v.from_user_id where u.is_effect=1 and v.create_w = week(curdate()) and v.is_red_envelope = 0 GROUP BY v.to_user_id order BY ticket desc limit " . $limit;
                } else {
                    //all
                    $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,u.use_diamonds as ticket ,u.is_authentication from " . DB_PREFIX . "user as u where u.is_effect=1 and u.use_diamonds>0 order BY u.use_diamonds desc limit " . $limit;
                }

                $list = $GLOBALS['db']->getAll($sql, true, true);

                if ($rank_name == 'day') {
                    $nowtimes = date('Y-m-d H:i:s');
                    $nowday = date('Y-m-d ');
                    $i = intval(date('i'));
                    $H = intval(date('H'));
                    if ($i < 30) {
                        $i = '30';
                    } else {
                        $i = '00';
                        $H = $H + 1;
                    }
                    $nowdaytime = $nowday . $H . ':' . $i . ":00";
                    $start = strtotime($nowtimes);
                    $end = strtotime($nowdaytime);
                    if ($cache_time > ($end - $start)) {
                        $cache_time = ($end - $start);
                    }
                    $GLOBALS['cache']->set($this->key, $list, $cache_time, true); //缓存时间 1800秒
                    $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                } else if ($rank_name == 'days') {
                    $GLOBALS['cache']->set($this->key, $list, $cache_time, true); //缓存时间 28800秒 8h
                    $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                } else if ($rank_name == 'week') {
                    $GLOBALS['cache']->set($this->key, $list, $cache_time, true); //缓存时间 28800秒 8h
                    $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                } else if ($rank_name == 'month') {
                    $GLOBALS['cache']->set($this->key, $list, $cache_time, true); //缓存时间 28800秒 8h
                    $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                } else {
                    $GLOBALS['cache']->set($this->key, $list, $cache_time, true); //缓存时间 86400秒 24h
                    $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
                }
            }
        }

        if ($list == false) {
            $list = array();
        }

        return $list;
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
