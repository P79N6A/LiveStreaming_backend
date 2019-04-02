<?php

class user_prop_anchor_num_auto_cache extends auto_cache
{
    private $key = "select_user_prop_anchor:anchor:";

    public function load($params, $is_real = true)
    {
        $this->key .= md5(serialize($params));
        $from_user_id = (!empty($params['from_user_id']) ? $params['from_user_id'] : null);
        $to_user_id = (!empty($params['to_user_id']) ? $params['to_user_id'] : null);
        if (empty($from_user_id) || empty($to_user_id)) {
            return 0;
        }
        $key_bf = $this->key . '_bf';
        $all_total_diamonds = $GLOBALS['cache']->get($this->key, true);
        if ($all_total_diamonds === false || $is_real == false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $all_total_diamonds = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "SELECT SUM(total_diamonds) as all_total_diamonds FROM " . DB_PREFIX . "video_prop_all WHERE `from_user_id` = {$from_user_id} and `to_user_id` = {$to_user_id} LIMIT 1";
                $all_total_diamonds = $GLOBALS['db']->getOne($sql, true, true);
                $GLOBALS['cache']->set($this->key, $all_total_diamonds, 10, true);
                $GLOBALS['cache']->set($key_bf, $all_total_diamonds, 86400, true); //备份
            }
        }
        if (empty($all_total_diamonds)) {
            return 0;
        }
        return $all_total_diamonds;
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
