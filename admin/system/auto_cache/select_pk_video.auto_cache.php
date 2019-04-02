<?php

class select_pk_video_auto_cache extends auto_cache
{
    private $key = "select_pk:video:";

    public function load($params, $is_real = true)
    {
        $this->key .= md5(serialize($params));
        $page = intval((isset($params['page']) ? ($params['page'] < 1 ? 1 : $params['page']) : 1));
        $keyword = (!empty($params['keyword']) ? $params['keyword'] : null);
        $user_id = (!empty($params['user_id']) ? $params['user_id'] : 0);
        $page_size = 20;
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false || $is_real == false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "SELECT v.id AS room_id, v.live_image, u.head_image,u.thumb_head_image, v.xpoint,v.ypoint, u.id as user_id,
						u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id WHERE v.live_in=1 AND v.in_livepk = 0 ";

                if (!is_null($keyword)) {
                    $sql .= " AND (u.id = '{$keyword}' OR u.nick_name LIKE '%{$keyword}%')";
                }
                $sql .= " AND u.id<>{$user_id} AND v.room_type = 3"; //1:私密直播;3:直播
                $sql .= " order by v.is_livepk DESC,v.sort_num desc,v.sort desc";
                $start = ($page - 1) * $page_size;
                $sql .= " LIMIT {$start},{$page_size}";
                $list = $GLOBALS['db']->getAll($sql, true, true);
                foreach ($list as &$v) {
                    $v['create_text'] = to_date($v['create_time'], 'Y.m.d');
                    $v['head_image'] = get_spec_image($v['head_image']);
                    $v['live_image'] = get_spec_image($v['live_image']);
                }
                unset($v);
                $list = array(
                    'list' => $list,
                    'has_next' => (count($list) < $page_size ? 0 : 1),
                    'page' => $page,
                    'status' => 1
                );
                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
            if (empty($list)) {
                $list = array(
                    'list' => array(),
                    'has_next' => 0,
                    'page' => 1,
                    'status' => 1
                );
            }
        }
        return $list;
    }

    public function rm()
    {

        $GLOBALS['cache']->clear_by_name($this->key);
    }

    public function clear_all()
    {
        $GLOBALS['cache']->clear_by_name($this->key);
    }
}
