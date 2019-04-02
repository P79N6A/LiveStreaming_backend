<?php
//底部文章
class guardian_record_list_auto_cache extends auto_cache
{
    private $key = "guardian_record:list";
    public function load($param = array())
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';
        // $list = $GLOBALS['cache']->get($this->key);
        $user_id = isset($param['user_id']) ? intval($param['user_id']) : null;
        $anchor_id = isset($param['anchor_id']) ? intval($param['anchor_id']) : null;
        $list = $GLOBALS['cache']->get($this->key, true);

        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                if (!is_null($user_id) && !is_null($anchor_id)) {
                    $sql = "SELECT g.*, l.`name` AS level_name, l.icon AS level_icon, gu.`name`,gu.icon,gu.pc_icon, u.nick_name, u.head_image, u.thumb_head_image, u.user_level, u.v_icon FROM " . DB_PREFIX . "guardian_record AS g LEFT JOIN fanwe_guard AS gu ON gu.id = g.guard_id LEFT JOIN " . DB_PREFIX . "guard_level AS l ON l.`level` <= g.`level` LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = g.user_id WHERE g.user_id = {$user_id} AND g.anchor_id = {$anchor_id} ORDER BY l.`level` DESC LIMIT 1";
                    $list = $GLOBALS['db']->getRow($sql, true, true);
                    if (!empty($list['icon'])) {
                        $list['icon'] = get_spec_image($list['icon']);
                    }
                    if (!empty($list['pc_icon'])) {
                        $list['pc_icon'] = get_spec_image($list['pc_icon']);
                    }
                    if (!empty($list['level_icon'])) {
                        $list['level_icon'] = get_spec_image($list['level_icon']);
                    }
                    if (!empty($list['head_image'])) {
                        $list['head_image'] = get_spec_image($list['head_image']);
                    }
                    if (!empty($list['thumb_head_image'])) {
                        $list['thumb_head_image'] = get_spec_image($list['thumb_head_image']);
                    }
                    if (!empty($list['v_icon'])) {
                        $list['v_icon'] = get_spec_image($list['v_icon']);
                    }
                    $list['day'] = ~~((time() - $list['start_time']) / 3600 / 24);
                    $list['ticket'] = (int) load_auto_cache("user_prop_anchor_num", array('from_user_id' => $user_id, 'to_user_id' => $anchor_id));
                } else if (!is_null($user_id)) {
                    $sql = "SELECT g.*, l.`name` AS level_name, l.icon AS level_icon, gu.`name`,gu.icon,gu.pc_icon, u.nick_name, u.head_image, u.thumb_head_image, u.user_level, u.v_icon FROM " . DB_PREFIX . "guardian_record AS g LEFT JOIN fanwe_guard AS gu ON gu.id = g.guard_id LEFT JOIN " . DB_PREFIX . "guard_level AS l ON l.`level` <= g.`level` LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = g.anchor_id WHERE g.user_id = {$user_id} ORDER BY l.`level` DESC";
                    $list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($list as &$value) {
                        $value['icon'] = get_spec_image($value['icon']);
                        $value['pc_icon'] = get_spec_image($value['pc_icon']);
                        $value['level_icon'] = get_spec_image($value['level_icon']);
                        $value['head_image'] = get_spec_image($value['head_image']);
                        $value['thumb_head_image'] = get_spec_image($value['thumb_head_image']);
                        $value['v_icon'] = get_spec_image($value['v_icon']);
                        $value['day'] = ~~((time() - $value['start_time']) / 3600 / 24);
                        $value['ticket'] = (int) load_auto_cache("user_prop_anchor_num", array('from_user_id' => $user_id, 'to_user_id' => $value['anchor_id']));
                    }
                } else if (!is_null($anchor_id)) {
                    $sql = "SELECT g.*, l.`name` AS level_name, l.icon AS level_icon, gu.`name`,gu.icon,gu.pc_icon, u.nick_name, u.head_image, u.thumb_head_image, u.user_level, u.v_icon FROM " . DB_PREFIX . "guardian_record AS g LEFT JOIN fanwe_guard AS gu ON gu.id = g.guard_id LEFT JOIN " . DB_PREFIX . "guard_level AS l ON l.`level` <= g.`level` LEFT JOIN " . DB_PREFIX . "user AS u ON u.id = g.user_id WHERE g.anchor_id = {$anchor_id} ORDER BY l.`level` DESC";
                    $list = $GLOBALS['db']->getAll($sql, true, true);
                    foreach ($list as &$value) {
                        $value['icon'] = get_spec_image($value['icon']);
                        $value['pc_icon'] = get_spec_image($value['pc_icon']);
                        $value['level_icon'] = get_spec_image($value['level_icon']);
                        $value['head_image'] = get_spec_image($value['head_image']);
                        $value['thumb_head_image'] = get_spec_image($value['thumb_head_image']);
                        $value['v_icon'] = get_spec_image($value['v_icon']);
                        $value['day'] = ~~((time() - $value['start_time']) / 3600 / 24);
                        $value['ticket'] = (int) load_auto_cache("user_prop_anchor_num", array('from_user_id' => $value['user_id'], 'to_user_id' => $value['anchor_id']));
                    }
                }

                // $GLOBALS['cache']->set($this->key, $list);
                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
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
