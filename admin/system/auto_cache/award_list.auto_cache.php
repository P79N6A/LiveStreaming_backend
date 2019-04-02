<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

class award_list_auto_cache extends auto_cache
{
    private $key = "award:list";

    public function load($param)
    {
        $list = $GLOBALS['cache']->get($this->key);
        if ($list === false) {
            $sql = "SELECT * FROM " . DB_PREFIX . "award_multiple where is_effect = 1 ORDER BY id DESC";
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as &$value) {
                $value['svg_file'] = get_spec_image($value['svg_file']);
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
