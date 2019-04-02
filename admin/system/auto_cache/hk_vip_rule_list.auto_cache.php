<?php

class hk_vip_rule_list_auto_cache extends auto_cache
{
    private $key = "hk:vip:rule:list";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $type = intval($param['type']);

        $list = $GLOBALS['cache']->get($this->key);

        if ($list === false) {
            $sql = "select id,name,diamonds,month_num from " . DB_PREFIX . "hk_vip_rule where type = {$type} and is_effect = 1 order by sort desc";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            $GLOBALS['cache']->set($this->key, $list);
        }

        return $list;
    }

    public function rm($param)
    {
        $this->key .= md5(serialize($param));
        $GLOBALS['cache']->rm($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->rm($this->key);
    }
}
