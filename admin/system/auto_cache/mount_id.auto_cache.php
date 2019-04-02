<?php

class mount_id_auto_cache extends auto_cache
{
    public function load($param)
    {
        $id = intval($param['id']);
        $key = "mount:" . $id;
        $mount = $GLOBALS['cache']->get($key);
        if ($mount === false) {
            if (intval(OPEN_REWARD_GIFT)) {
                if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                    $list_award = get_award_info();
                }
            }
            $field_str = 'id,name,icon,pc_icon,pc_gif,sort,is_animated,anim_type,gif_gift_show_style,`desc`,svg_file';
            $sql = "select " . $field_str . " from " . DB_PREFIX . "mounts where id=" . $id . " LIMIT 1";
            $mount = $GLOBALS['db']->getRow($sql, true, true); //以后需要缓存
            if (!empty($mount)) {
                $mount['icon'] = get_spec_image($mount['icon']);
                $mount['pc_icon'] = get_spec_image($mount['pc_icon']);
                $mount['pc_gif'] = get_spec_image($mount['pc_gif']);
                $mount['svg_file'] = get_spec_image($mount['svg_file']);
                if ($mount['is_animated'] == 1) {
                    //要缓存getAllCached
                    $sql = "select id,url,play_count,delay_time,duration,show_user,type from " . DB_PREFIX . "mounts_animated where mount_id = " . $id . " order by sort desc";
                    $anim_list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($anim_list as &$v) {
                        $v['url'] = get_spec_image($v['url']);
                        $v['gif_gift_show_style'] = $mount['gif_gift_show_style'];
                    }

                    $mount['anim_cfg'] = $anim_list;
                    //$ext['sql'] = $sql;
                } else {
                    $mount['anim_cfg'] = array();
                }

                $GLOBALS['cache']->set($key, $mount);
            }
        }
        if (empty($mount)) {
            $mount = array();
        }
        return $mount;
    }

    public function rm($param)
    {
        $id = intval($param['id']);
        $key = "mount:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }

    public function clear_all($param)
    {
        $id = intval($param['id']);
        $key = "mount:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }
}
