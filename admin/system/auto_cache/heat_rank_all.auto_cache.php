<?php

class heat_rank_all_auto_cache extends auto_cache
{
    private $key = "rank_car:all:";
    public function load($param)
    {
        $rank_name = strim($param['rank_name']);
        $page = intval($param['page']);
        $page_size = intval($param['page_size']);
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $this->key .= $rank_name . '_' . $page;

        $key_bf = $this->key . '_bf';

        $list = $GLOBALS['cache']->get($this->key, true);

        if ($list === false || 1) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,r.heat_amount from " . DB_PREFIX . "rank_heat_all as r LEFT JOIN  " . DB_PREFIX . "user as u on u.id = r.user_id where u.is_effect=1  GROUP BY r.user_id order BY r.heat_amount desc,u.id ASC limit " . $limit;
                $list = $GLOBALS['db']->getAll($sql, true, true);
                foreach ($list as $k => $v) {
                    $list[$k]['nick_name'] = ($v['nick_name']);
                    $list[$k]['head_image'] = get_spec_image($v['head_image'], 150, 150);
                }
                if ($list == false) {
                    $list = array();
                }

                $GLOBALS['cache']->set($this->key, $list, 300, true); //缓存时间 28800秒 8h
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
        }
        if ($list == false) {
            $list = array();
        }

        $root['list'] = $list;
        return $root;
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
