<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/29
 * Time: 17:25
 */
class all_video_auto_cache extends auto_cache
{
    private $key = "all:video:";

    public function load($param)
    {
        $this->key .= md5(serialize($param));
        $key_bf = $this->key . '_bf';
        $list = $GLOBALS['cache']->get($this->key, true);

        $page = $param['page'] > 0 ? $param['page'] : 1;
        $page_size = $param['page_size'] > 0 ? $param['page_size'] : 12;
        $limit = (($page - 1) * $page_size) . "," . $page_size;
        $is_recommend = ($param['is_recommend'] == 1);
        $has_private = $param['has_private'];
        $is_hot_cate = $param['is_hot_cate'];
        if ($list === false) {
            $is_ok = $GLOBALS['cache']->set_lock($this->key);
            if (!$is_ok) {
                $list = $GLOBALS['cache']->get($key_bf, true);
            } else {
                if ($is_recommend) {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.live_image,u.head_image,u.thumb_head_image, v.xpoint,v.ypoint, u.v_type, u.v_icon, u.nick_name,u.user_level,vs.title AS class_name  FROM " . DB_PREFIX . "video v
                    LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id LEFT JOIN " . DB_PREFIX . "video_classified AS vs ON vs.id = v.classified_id where v.live_in in (1,3) and v.is_recommend = 1";
                } else if ($param['cate_id'] != '') {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.live_image,u.head_image,u.thumb_head_image, v.xpoint,v.ypoint, u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) and v.cate_id =" . $param['cate_id'];
                } elseif ($is_hot_cate) {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number,v.live_image, u.head_image,u.thumb_head_image, v.xpoint,v.ypoint, u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id LEFT JOIN " . DB_PREFIX . "video_cate vc on vc.id=v.cate_id  where v.live_in in (1,3) ";
                } else {
                    $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type, (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, u.head_image,u.thumb_head_image,v.live_image, v.xpoint,v.ypoint, u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
					LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) ";
                }

                if ($has_private == 1) {
                    $sql .= ' and v.room_type in (1,3)'; //1:私密直播;3:直播
                } else {
                    $sql .= ' and v.room_type = 3'; //1:私密直播;3:直播
                }
                if ($is_hot_cate) {
                    $sql .= " order by vc.num desc";
                } else {
                    $sql .= " order by v.sort_num desc";
                }

                $sql .= " limit " . $limit;
                $list = $GLOBALS['db']->getAll($sql, true, true);
                fanwe_require(APP_ROOT_PATH . 'mapi/car/core/common_car.php');
                $rank = get_heat_rank_cache();
                $rank_arr = array();
                if (!empty($rank['list'])) {
                    foreach ($rank['list'] as $kk => $vv) {
                        $rank_arr[$vv['video_id']] = $kk + 1;
                    }
                }
                foreach ($list as $k => &$v) {
                    $v['medals'] = array_map(function ($_v) {
                        return $_v['icon'];
                    }, load_auto_cache('user_medals', array('user_id' => $v['user_id'], 'no_expired' => true)));
                    $v['heat_rank'] = intval($rank_arr[$v['room_id']]);
                    if ($v['thumb_head_image'] == '') {
                        $v['thumb_head_image'] = get_spec_image($v['head_image'], 40, 40);
                    } else {
                        $v['thumb_head_image'] = get_spec_image($v['thumb_head_image'], 40, 40);
                    }
                    if (empty($v['live_image'])) {
                        $v['live_image'] = get_spec_image($v['head_image'], 320, 180, 1);
                    } else {
                        $v['live_image'] = get_spec_image($v['live_image'], 320, 180, 1);
                    }
                    $v['head_image'] = get_spec_image($v['head_image'], 40, 40);
                    if ($v['live_in'] == 3) {
                        $v['video_url'] = url('live#show', array('room_id' => $v['room_id'], 'is_vod' => 1));
                    } else {
                        $v['video_url'] = url('live#show', array('room_id' => $v['room_id']));
                    }
                }
                array_multisort(array_column($list, 'heat_rank'), SORT_ASC, $list);

                $GLOBALS['cache']->set($this->key, $list, 10, true);

                $GLOBALS['cache']->set($key_bf, $list, 86400, true); //备份
            }
        }
        if ($list == false) {
            $list = array();
        }

        $res = array('list' => $list);
        return $res;
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
