<?php

class rocket_list_auto_cache extends auto_cache
{
    private $key = "rocket:list";
    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';
        $load_more = isset($param['load_more']) ? $param['load_more'] == 1 : false;
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                if ($load_more) {
                    $sql = "SELECT rrl.user_id,u.head_image,u.thumb_head_image,u.nick_name,v.id AS room_id FROM `" . DB_PREFIX . "rank_rocket_all` AS rrl LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = rrl.user_id LEFT JOIN " . DB_PREFIX . "video AS v ON v.user_id = u.id AND v.live_in IN ( 1, 3 ) AND v.room_type = 3 WHERE ((UNIX_TIMESTAMP() - rrl.update_time) < 7200) ORDER BY rrl.update_time DESC";
                } else {
                    $sql = "SELECT rrl.user_id,u.head_image,u.thumb_head_image,u.nick_name,v.id AS room_id FROM `" . DB_PREFIX . "rank_rocket_all` AS rrl LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = rrl.user_id LEFT JOIN " . DB_PREFIX . "video AS v ON v.user_id = u.id AND v.live_in IN ( 1, 3 ) AND v.room_type = 3 WHERE ((UNIX_TIMESTAMP() - rrl.update_time) < 7200) ORDER BY rrl.update_time DESC LIMIt 5";
                }
                $list = $GLOBALS['db']->getAll($sql, true, true);
                foreach ($list as &$value) {
                    $value['head_image'] = get_spec_image($value['head_image']);
                    $value['thumb_head_image'] = get_spec_image($value['thumb_head_image']);
                }
                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
        }
        if (empty($list)) {
            $list = array();
        }
        return $list;
    }

    public function rm($param)
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
