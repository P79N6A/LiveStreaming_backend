<?php

class activity_anchor_list_auto_cache extends auto_cache
{
    private $key = "activity:anchor_list:";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $prop_id = isset($param['prop_id']) ? intval($param['prop_id']) : 0;
        $start_time = isset($param['start_time']) ? intval($param['start_time']) : 0;
        $end_time = isset($param['end_time']) ? intval($param['end_time']) : 0;
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "SELECT to_user_id, SUM( num ) AS all_num, SUM( total_diamonds ) AS all_diamonds FROM `" . DB_PREFIX . "video_prop_all` WHERE `prop_id` = {$prop_id} AND create_time BETWEEN " . $start_time . " AND " . $end_time . " GROUP BY to_user_id ORDER BY all_num DESC, all_diamonds DESC";
                $list = $GLOBALS['db']->getAll($sql, true, true);

                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }

            if ($list == false) {
                $list = array();
            }
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
