<?php

class prop_group_list_auto_cache extends auto_cache
{
    private $key = "prop_group:list";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $list = $GLOBALS['cache']->get($this->key);

        $display = isset($param['display']) ? $param['display'] : true;

        if ($list === false) {
            $sql = "SELECT id AS g_id,name FROM " . DB_PREFIX . "prop_group WHERE id > 0 ORDER BY sort ASC";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            if ($display) {
                array_push($list, array('g_id' => '0', 'name' => '其它'));
            }
            foreach ($list as $key => &$value) {
                $value['data'] = load_auto_cache("prop_list", array('g_id' => $value['g_id']));
                if (empty($value['data'])) {
                    unset($list[$key]);
                }
            }
            $list = array_values($list);
            $GLOBALS['cache']->set($this->key, $list, 86400, true);
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
