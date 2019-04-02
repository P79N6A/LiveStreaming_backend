<?php

class refund_role_level_auto_cache extends auto_cache
{
    private $key = "refund_role:level:";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $level = isset($param['level']) ? intval($param['level']) : 0;
        $list = $GLOBALS['cache']->get($this->key, true);

        if ($list === false) {
            $sql = "SELECT * FROM " . DB_PREFIX . "refund_role WHERE `level`<=1000 ORDER BY `level` DESC LIMIT 1";
            $list = $GLOBALS['db']->getRow($sql, true, true);
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
