<?php

class anchor_tag_auto_cache extends auto_cache
{
    private $key = "anchor:tag_list:";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $user_id = isset($param['user_id']) ? intval($param['user_id']) : 0;
        $num = isset($param['num']) ? intval($param['num']) : 3;
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "SELECT *, COUNT( tag_id ) AS tag_num FROM `" . DB_PREFIX . "user_tag_list` WHERE `user_id` = {$user_id} GROUP BY tag_id ORDER BY tag_num DESC LIMIT {$num}";
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
