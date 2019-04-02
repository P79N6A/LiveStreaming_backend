<?php

class signin_list_auto_cache extends auto_cache
{
    private $key = "signin:list";

    public function load($param)
    {
        $list = $GLOBALS['cache']->get($this->key);

        if ($list === false) {
            $sql = "SELECT s.*,p.`name` AS prop_name,p.`icon`,p.`is_effect` AS prop_is_effect FROM " . DB_PREFIX . "signin AS s LEFT JOIN " . DB_PREFIX . "prop AS p ON p.id = s.prop_id WHERE s.is_effect = 1 AND p.`is_effect` = 1 ORDER BY s.day ASC";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as &$value) {
                $value['icon'] = get_spec_image($value['icon']);
                $value['num'] = (int) ($value['num']);
            }
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
