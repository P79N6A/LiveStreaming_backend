<?php

class prop_auto_cache extends auto_cache
{
    private $key = "prop_id:info";

    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $list = $GLOBALS['cache']->get($this->key);
        $id = (isset($param['id']) ? intval($param['id']) : 0);
        if ($list === false) {
            $m_config = load_auto_cache("m_config");
            if (intval(OPEN_REWARD_GIFT)) {
                if (file_exists(APP_ROOT_PATH . 'mapi/lib/core/award_function.php')) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/award_function.php');
                    $list_award = get_award_info();
                }
            }
            $field_str = "id,name,score,diamonds,icon,pc_icon,pc_gif,ticket,society_ticket,is_much,sort,is_red_envelope,is_animated,anim_type,gif_gift_show_style,g_id,is_special,drawn_min,drawn_max,svg_file,drawn_time";
            if (intval(OPEN_REWARD_GIFT) && intval($list_award['is_open_award']) == 1) {
                $field_str .= ',is_award ';
            }

            if (intval(OPEN_CAR_MODULE)) {
                $field_str .= ',is_heat,red_envelope_type ';
            }

            $sql = "select " . $field_str . " from " . DB_PREFIX . "prop where is_effect = 1 AND id = {$id} LIMIT 1";
            if ($m_config['ios_check_version'] != '') {
                $sql = "select " . $field_str . " from " . DB_PREFIX . "prop where is_effect = 1 AND id = {$id} and is_red_envelope<>1 LIMIT 1";
            }
            $list = $GLOBALS['db']->getRow($sql, true, true);
            if (!empty($list)) {
                $list['icon'] = get_spec_image($list['icon']);
                $list['pc_icon'] = get_spec_image($list['pc_icon']);
                $list['pc_gif'] = get_spec_image($list['pc_gif']);
                $list['ticket'] = intval($list['ticket']);
                $list['society_ticket'] = intval($list['society_ticket']);
                $list['score_fromat'] = '+' . $list['score'] . '经验值';
            } else {
                $list = array();
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

    public function clear_all($param = array())
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
