<?php

class mount_list_auto_cache extends auto_cache
{
    private $key = "mount:list";

    public function load($param)
    {
        $list = $GLOBALS['cache']->get($this->key);

        if ($list === false) {
            $m_config = load_auto_cache("m_config");
            if (intval(OPEN_REWARD_GIFT)) {
                if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                    $list_award = get_award_info();
                }
            }
            $field_str = "id,name,score,diamonds,icon,pc_icon,pc_gif,sort,is_animated,anim_type,gif_gift_show_style";
            $sql = "select " . $field_str . " from " . DB_PREFIX . "mounts where is_effect = 1 order by sort desc";
            if ($m_config['ios_check_version'] != '') {
                $sql = "select " . $field_str . " from " . DB_PREFIX . "mounts where is_effect = 1 order by sort desc";
            }
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as &$v) {
                $v['icon'] = get_spec_image($v['icon']);
                $v['score_fromat'] = '+' . $v['score'] . '经验值';
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
