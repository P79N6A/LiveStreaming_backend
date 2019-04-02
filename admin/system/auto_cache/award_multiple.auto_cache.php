<?php

class award_multiple_auto_cache extends auto_cache
{
    private $key = "award_anim:list";

    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $multiple = intval($param['multiple']);
        $award_anim_list = $GLOBALS['cache']->get($this->key);
        if ($award_anim_list === false) {
            //要缓存getAllCached
            $sql = "SELECT a.`url`,a.`play_count`,a.`delay_time`,a.`duration`,a.`show_user`,a.`type`,m.`gif_gift_show_style` FROM " . DB_PREFIX . "award_animated AS a LEFT JOIN " . DB_PREFIX . "award_multiple AS m ON m.id = a.award_id WHERE m.multiple = " . $multiple . " ORDER BY a.sort DESC";
            $award_anim_list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($award_anim_list as &$v) {
                $v['url'] = get_spec_image($v['url']);
            }

            $GLOBALS['cache']->set($this->key, $award_anim_list);
        }
        if (empty($award_anim_list)) {
            $award_anim_list = array();
        }
        return $award_anim_list;
    }

    public function rm($param = array())
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all($param)
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
