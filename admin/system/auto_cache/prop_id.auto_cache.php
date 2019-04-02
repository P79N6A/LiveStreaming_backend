<?php

class prop_id_auto_cache extends auto_cache
{
    public function load($param = array())
    {
        $id = intval($param['id']);
        $key = "prop:" . $id;
        $prop = $GLOBALS['cache']->get($key);
        if ($prop === false) {
            if (intval(OPEN_REWARD_GIFT)) {
                if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                    $list_award = get_award_info();
                }
            }
            $field_str = 'id,name,score,diamonds,icon,pc_icon,pc_gif,ticket,society_ticket,is_much,sort,is_red_envelope,is_animated,anim_type,robot_diamonds,gif_gift_show_style,is_rocket,is_full_push,g_id,is_special,drawn_min,drawn_max,svg_file,drawn_time';
            if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                $field_str .= ',is_award ';
            }
            if (intval(OPEN_CAR_MODULE)) {
                $field_str .= ',is_heat,red_envelope_type ';
            }
            $sql = "SELECT " . $field_str . " FROM " . DB_PREFIX . "prop WHERE id=" . $id;
            $prop = $GLOBALS['db']->getRow($sql, true, true); //以后需要缓存
            if (!empty($prop)) {
                $prop['pc_icon'] = get_spec_image($prop['pc_icon']);
                $prop['pc_gif'] = get_spec_image($prop['pc_gif']);
                $prop['icon'] = get_spec_image($prop['icon']);
                $prop['svg_file'] = get_spec_image($prop['svg_file']);
                $prop['ticket'] = intval($prop['ticket']);
                $prop['society_ticket'] = intval($prop['society_ticket']);
                $prop['drawn_min'] = intval($prop['drawn_min']);
                $prop['drawn_max'] = intval($prop['drawn_max']);
                $prop['drawn_time'] = intval($prop['drawn_time']);

                if ($prop['is_animated'] == 1) {
                    //要缓存getAllCached
                    $sql = "select id,url,play_count,delay_time,duration,show_user,type from " . DB_PREFIX . "prop_animated where prop_id = " . $id . " order by sort desc";
                    $anim_list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($anim_list as &$v) {
                        $v['url'] = get_spec_image($v['url']);
                        $v['gif_gift_show_style'] = $prop['gif_gift_show_style'];
                    }

                    $prop['anim_cfg'] = $anim_list;
                    //$ext['sql'] = $sql;
                } else {
                    $prop['anim_cfg'] = array();
                }
                $GLOBALS['cache']->set($key, $prop);
            }
        }
        if (empty($prop)) {
            $prop = array();
        }
        return $prop;
    }

    public function rm($param = array())
    {
        $id = intval($param['id']);
        $key = "prop:" . $id;
        $GLOBALS['cache']->rm($key);
        rm_auto_cache('prop', $param);
    }

    public function clear_all($param = array())
    {
        $id = intval($param['id']);
        $key = "prop:" . $id;
        $GLOBALS['cache']->clear_by_name($key);
        clear_auto_cache('prop', $param);
    }
}
