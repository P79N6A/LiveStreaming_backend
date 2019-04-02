<?php

class select_hk_video_auto_cache extends auto_cache
{
    private $key = "select_hk:video:";

    public function load($param, $is_real = true)
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);
        if ($list === false || $is_real == false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                $sql = "SELECT v.id AS room_id, v.channelid, v.begin_time, v.create_time, v.play_url, v.play_flv, v.play_hls, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type,
						(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.live_image, u.head_image,u.thumb_head_image, v.xpoint,v.ypoint,
						u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) ";

                $sql .= ' and v.room_type = 3'; //1:私密直播;3:直播
                $sql .= " order by v.sort_num desc,v.sort desc";

                $list = $GLOBALS['db']->getAll($sql, true, true);

                foreach ($list as &$v) {
                    $v['nick_name'] = ($v['nick_name']);
                    $v['create_text'] = to_date($v['create_time'], 'Y.m.d');
                }
                unset($v);
                $GLOBALS['cache']->set($this->key, $list, 10, true);
                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }

        }
        if (empty($list)) {
            $list = array();
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
