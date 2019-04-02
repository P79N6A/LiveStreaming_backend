<?php

class tag_list_auto_cache extends auto_cache
{
    private $key = "user_tag:list:";
    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';

        // $list = $GLOBALS['cache']->get($this->key, true);
        // if (($list === false) || $reload) {
        //     $is_ok = $GLOBALS['cache']->set_lock($this->key);
        //     if (!$is_ok && !$reload) {
        //         $list = $GLOBALS['cache']->get($key_bf, true);
        //     } else {
        $tag_list = array();
        $id_list = array_column($GLOBALS['db']->getAll("SELECT id FROM `" . DB_PREFIX . "user_tags` WHERE `is_effect` = 1"), 'id');
        if (!empty($id_list)) {
            shuffle($id_list);
            $id_list = array_slice($id_list, 0, 9);
            $tag_list = $GLOBALS['db']->getAll("SELECT * FROM `" . DB_PREFIX . "user_tags` WHERE `id` IN (" . implode(',', $id_list) . ") AND  `is_effect` = 1 ORDER BY sort LIMIT 9;");
        }
        if (empty($tag_list)) {
            $tag_list = array();
        }
        return $tag_list;
    }

    public function rm()
    {

        // $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {

        // $GLOBALS['cache']->clear_by_name($this->key);
    }
}
