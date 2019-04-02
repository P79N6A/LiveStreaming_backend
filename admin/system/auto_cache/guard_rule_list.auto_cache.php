<?php

class guard_rule_list_auto_cache extends auto_cache
{
    private $key = "guard_rule:list";
    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $list = $GLOBALS['cache']->get($this->key);
        $load = isset($param['rules']) ? false : true;
        $condition = '';
        if (isset($param['guard_id'])) {
            $condition .= " AND id = {$param['guard_id']}";
        }
        if ($list === false) {
            //unserialize(
            $sql = "SELECT * FROM " . DB_PREFIX . "guard WHERE is_effect = 1 {$condition} ORDER BY sort";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as $key => &$value) {
                $value['icon'] = get_spec_image($value['icon']);
                $value['pc_icon'] = get_spec_image($value['pc_icon']);
                $value['pc_gif'] = get_spec_image($value['pc_gif']);
                if ($load) {
                    $condition1 = '';
                    if (isset($param['rule_id'])) {
                        $condition1 .= " AND id = {$param['rule_id']}";
                    }
                    $value['rules'] = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "guard_rules WHERE guard_id={$value['id']} {$condition1} ORDER BY sort", true, true);
                    if (empty($value['rules'])) {
                        unset($list[$key]);
                    }
                }
            }
            $GLOBALS['cache']->set($this->key, array_values($list));
        }
        if (empty($list)) {
            $list = array();
        }
        return $list;
    }

    public function rm($param = array())
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
