<?php
//底部文章
class anchor_level_auto_cache extends auto_cache
{
    private $key = "anchor_level:list";
    public function load($param)
    {
        $list = $GLOBALS['cache']->get($this->key);

        if ($list === false) {
            $sql = "select id,name,level,ticket,point,icon from " . DB_PREFIX . "anchor_level ORDER BY ticket DESC";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as &$value) {
                $value['icon'] = get_spec_image($value['icon']);
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
