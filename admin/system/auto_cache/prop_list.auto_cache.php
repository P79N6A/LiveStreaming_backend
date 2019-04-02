<?php

class prop_list_auto_cache extends auto_cache
{
    private $key = "prop:list";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $list = $GLOBALS['cache']->get($this->key);
        $where = (isset($param['g_id']) ? ' AND g_id = ' . $param['g_id'] : '');
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

            $sql = "select " . $field_str . " from " . DB_PREFIX . "prop where is_effect = 1 {$where} order by sort desc";
            if ($m_config['ios_check_version'] != '') {
                $sql = "select " . $field_str . " from " . DB_PREFIX . "prop where is_effect = 1 {$where} and is_red_envelope<>1 order by sort desc";
            }
            $list = $GLOBALS['db']->getAll($sql, true, true);

            foreach ($list as &$v) {
                $v['icon'] = get_spec_image($v['icon']);
                $v['svg_file'] = get_spec_image($v['svg_file']);
                $v['drawn_time'] = intval($v['drawn_time']);
                $v['ticket'] = intval($v['ticket']);
                $v['society_ticket'] = intval($v['society_ticket']);
                $v['drawn_min'] = intval($v['drawn_min']);
                $v['drawn_max'] = intval($v['drawn_max']);
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
