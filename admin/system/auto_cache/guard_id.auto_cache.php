<?php

class guard_id_auto_cache extends auto_cache
{
    public function load($param)
    {
        $id = intval($param['id']);
        $key = "guard:" . $id;
        $guard = $GLOBALS['cache']->get($key);
        if ($guard === false) {
            $field_str = 'id,name,icon,sort,is_animated,anim_type,content,pc_icon,pc_gif,gif_gift_show_style,svg_file';
            $sql = "SELECT " . $field_str . " FROM " . DB_PREFIX . "guard WHERE is_effect= 1 AND id=" . $id . ' LIMIT 1';
            $guard = $GLOBALS['db']->getRow($sql, true, true); //以后需要缓存
            if (!empty($guard)) {
                $guard['icon'] = get_spec_image($guard['icon']);
                $guard['pc_icon'] = get_spec_image($guard['pc_icon']);
                $guard['pc_gif'] = get_spec_image($guard['pc_gif']);
                $guard['svg_file'] = get_spec_image($guard['svg_file']);
                if ($guard['is_animated'] == 1) {
                    //要缓存getAllCached
                    $sql = "select id,url,play_count,delay_time,duration,show_user,type from " . DB_PREFIX . "guard_animated where guard_id = " . $id . " ORDER BY sort DESC";
                    $anim_list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($anim_list as $k => &$v) {
                        $v['url'] = get_spec_image($v['url']);
                        $v['gif_gift_show_style'] = $guard['gif_gift_show_style'];
                    }

                    $guard['anim_cfg'] = $anim_list;
                    //$ext['sql'] = $sql;
                } else {
                    $guard['anim_cfg'] = array();
                }

                $GLOBALS['cache']->set($key, $guard);
            }
        }
        if (empty($guard)) {
            $guard = array();
        }
        return $guard;
    }

    public function rm($param)
    {
        $id = intval($param['id']);
        $key = "guard:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }

    public function clear_all($param)
    {
        $id = intval($param['id']);
        $key = "guard:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
    }
}
