<?php
//底部文章
class guard_level_auto_cache extends auto_cache
{
    private $key = "guard_level:list";
    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $list = $GLOBALS['cache']->get($this->key);
        $level = isset($param['level']) ? intval($param['level']) : null;
        if ($list === false) {
            if (is_null($level)) {
                $sql = "select * from " . DB_PREFIX . "guard_level order by point desc";
                $list = $GLOBALS['db']->getAll($sql, true, true);
                foreach ($list as &$value) {
                    $value['icon'] = get_spec_image($value['icon']);
                }
            } else {
                $sql = "select * from " . DB_PREFIX . "guard_level WHERE level <= {$level} ORDER BY level DESC LIMIT 1";
                $list = $GLOBALS['db']->getRow($sql, true, true);
                if (!empty($list['icon'])) {
                    $list['icon'] = get_spec_image($list['icon']);
                }
            }
            $GLOBALS['cache']->set($this->key, $list);
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
