<?php

class speak_level_auto_cache extends auto_cache
{
    private $key = "speak:level";

    public function load($param)
    {
        $list = array();

        if ($list == false) {
            $sql = "SELECT * FROM " . DB_PREFIX . "speak_level";
            $list = $GLOBALS['db']->getAll($sql);

            $GLOBALS['cache']->set($this->key, $list);
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
