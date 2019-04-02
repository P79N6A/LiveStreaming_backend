<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/12
 * Time: 12:05
 */
class child_room
{
    /*
     * 获取子房间id
     */
    public function child_id($room_id)
    {
        if ($room_id <= 0) {
            return false;
        }
        $child_id = $GLOBALS['db']->getAll("SELECT child_id FROM " . DB_PREFIX . "child_room WHERE parent_id =" . $room_id);
        if ($child_id) {
            return $child_id;
        } else {
            return false;
        }
    }

    public function parent_id($child_id)
    {
        $parent_id = $GLOBALS['db']->getOne("SELECT parent_id FROM " . DB_PREFIX . "child_room WHERE child_id =" . $child_id);
        return $parent_id ? $parent_id : $child_id;
    }

    /**
     * 子房间送礼物
     */
    public function child_pop_prop($user_id, $child_id)
    {
        //判断是否是子房间
        $parent_id = $GLOBALS['db']->getOne("SELECT parent_id FROM " . DB_PREFIX . "child_room WHERE child_id =" . $child_id);
        if (!$parent_id) {
            return $child_id;
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $fields = array("live_in");
        $child_info = $video_redis->getRow_db($child_id, $fields);

        $video = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "video WHERE id =" . $parent_id);
        if (!$video && $child_info['live_in'] != 3) {
            $root['status'] = 0;
            $root['error'] = "房间不存在";
            ajax_return($root);
        }
        $child_user_id = $GLOBALS['db']->getOne("SELECT user_id FROM " . DB_PREFIX . "video where id =" . $child_id);
        if ($user_id == $child_user_id) {
            $root['error'] = "不能发礼物给自己";
            $root['status'] = 0;
            ajax_return($root);
        }
        return $parent_id;
    }

    /**
     * 子房间礼物处理
     */
    public function child_room_prop($child_id, $video_id, $video_prop, $fields, $user_id, $total_ticket)
    {
        if (!empty($child_id) && $child_id != $video_id) {
            $table = DB_PREFIX . "child_video_prop";
            $table_version = $GLOBALS['db']->getRow("Describe " . $table . " is_coin", true, true);
            if (!$table_version) {
                $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `is_coin` varchar(255) NOT NULL  COMMENT '双币礼物，0是秀豆，1是游戏币'");
            }
            $child_user_id = $GLOBALS['db']->getOne("SELECT user_id FROM " . DB_PREFIX . "video where id =" . $child_id);
            $video_prop['video_id'] = $child_id;
            $video_prop['to_user_id'] = $child_user_id;
            $val = implode(",", $video_prop);
            $sql = "insert into " . $table . " (" . $fields . ") VALUES (" . $val . ")";

            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                if ($total_ticket > 0 && $video_prop['is_red_envelope'] == 0) {
                    //贡献榜
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                    $videoCont_redis = new VideoContributionRedisService();
                    $videoCont_redis->insert_db($user_id, $child_user_id, $child_id, $total_ticket);
                }
            }

        } else {
            return false;
        }
    }

    /*
     * 子房间弹幕
     */
    public function child_room_pop_msg($video_id, $video_prop, $fields, $user_id, $total_ticket)
    {
        if (!$video_id) {
            return false;
        }
        if ($GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "child_room where child_id =" . $video_id) > 0) {
            $parent_id = $this->parent_id($video_id);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($parent_id, array('id', 'user_id', 'group_id', 'prop_table'));
            $video_prop['group_id'] = "'" . $video['group_id'] . "'";
            $video_prop['to_user_id'] = $video['user_id'];
            $table = $video['prop_table'];

            $table_info = $GLOBALS['db']->getRow("Describe " . $table . " from_ip", true, true);
            if (!$table_info) {
                $GLOBALS['db']->query("ALTER TABLE " . $table . " ADD COLUMN `from_ip` varchar(255) NOT NULL  COMMENT '送礼物人IP'");
            }

            $valus = implode(",", $video_prop);
            $sql = "insert into " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
            $GLOBALS['db']->query($sql);
            $user_prop_id = $GLOBALS['db']->insert_id();
            if ($user_prop_id) {
                if ($total_ticket > 0) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                    $videoCont_redis = new VideoContributionRedisService();
                    $videoCont_redis->insert_db($user_id, $video['user_id'], $parent_id, $total_ticket);
                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 关闭子房间
     */
    public function end_child_video($room_id)
    {
        if ($room_id <= 0) {
            return false;
        }

        $child_id = $GLOBALS['db']->getAll("SELECT child_id FROM " . DB_PREFIX . "child_room WHERE parent_id =" . $room_id);
        if (empty($child_id)) {
            return false;
        }

        $child_ids = implode(',', array_column($child_id, 'child_id'));
        if (!$child_ids) {
            return false;
        }
        $group_id = $GLOBALS['db']->getOne("SELECT group_id FROM " . DB_PREFIX . "video WHERE id =" . $room_id);

        $child_video = $GLOBALS['db']->getAll("SELECT id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,video_vid,is_live_pay,live_pay_type FROM " . DB_PREFIX . "video WHERE id in (" . $child_ids . ")");

        foreach ($child_video as $k => $v) {
            if (empty($v['group_id'])) {
                $v['group_id'] = $group_id;
            }
            $child_res[] = do_end_video($v, $v['video_vid'], 0, $v['cate_id']);
        }

        return $child_res;
    }

    /*
     * 获取子房间
     */
    public function get_child_video($room_id, $user_id, $type, $param, $require_type = 0)
    {
        $parent_id = $GLOBALS['db']->getOne("SELECT parent_id FROM " . DB_PREFIX . "child_room WHERE child_id =" . $room_id);
        if (!$parent_id) {
            return get_video_info2($room_id, $user_id, $type, $param, $require_type);
        }
        $video = $GLOBALS['db']->getRow("SELECT id,user_id,group_id FROM " . DB_PREFIX . "video WHERE id =" . $parent_id);
        //判断主房间是否存在
        $root = array();
        if (empty($video) && !$param['is_vod']) {
            $root['status'] = 0;
            $root['error'] = '直播不存在';
        } else {
            $user = $GLOBALS['db']->getOne("SELECT user_id FROM " . DB_PREFIX . "video WHERE id =" . $room_id);
            if ($user == $user_id && $require_type == 0) {
                $root['error'] = "子房间主播无法在APP端观看";
                $root['status'] = 0;
                ajax_return($root);
            }
            $root = get_video_info2($room_id, $user_id, $type, $param, $require_type);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $fields = array("room_title", "title", "is_live_pay");
            $child_info = $video_redis->getRow_db($room_id, $fields);
            if ($video) {
                $root['group_id'] = $video['group_id'];
                $root['user_id'] = $video['user_id'];
                $root['podcast'] = getuserinfo($user_id, $video['user_id'], $video['user_id']);
            }
            $root['room_title'] = $child_info['room_title'];
            $root['title'] = $child_info['title'];
            $root['is_live_pay'] = $child_info['is_live_pay'];
            $root['viewer_num'] = $video_redis->get_video_watch_num($room_id);
            $root['child_id'] = $room_id;
            $child = $GLOBALS['db']->getRow("SELECT user_id FROM " . DB_PREFIX . "video WHERE id =" . $room_id);
            $root['has_lianmai'] = 0;

            //记录子房间观众并同步到redis
            $viewer = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "child_room_viewer WHERE child_room =" . $room_id . " and user_id =" . $user_id);
            if (!$viewer) {
                $data['user_id'] = $user_id;
                $data['child_room'] = $room_id;
                $GLOBALS['db']->autoExecute(DB_PREFIX . "child_room_viewer", $data, 'INSERT');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
                $video_viewer_obj = new VideoViewerRedisService();
                $video_viewer_obj->child_room_member_join(array(
                    'NewMemberList' => array(
                        array('Member_Account' => $user_id)
                    )
                ), $room_id);
            }
            $viewer_num = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "child_room_viewer WHERE child_room =" . $room_id);
            $video_redis->update_db($room_id, array('max_watch_number' => $viewer_num));
        }
        return $root;
    }

    // 关联子账户
    public function child_account($video_id, $data)
    {
        if ($data['room_type'] == 1) {
            return false;
        }

        $p_user_id = $data['user_id'];
        $c_user = $GLOBALS['db']->getAll("SELECT * FROM " . DB_PREFIX . "child_room_account WHERE is_effect =1 AND p_user_id =" . $p_user_id,
            true, true);
        $user_id_arr = array_column($c_user, 'c_user_id');
        $m_config = load_auto_cache('m_config');
        if (empty($user_id_arr)) {
            return false;
        }

        if (!$video_id) {
            return false;
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $live_image = $data['live_image'];
        foreach ($c_user as $value) {
            $data['user_id'] = $value['c_user_id'];
            $data['online_status'] = 1; //主播在线状态;1:在线(默认); 0:离开
            if ($value['room_title']) {
                $data['room_title'] = $value['room_title'];
                $data['title'] = $value['room_title'];
                $cate_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "video_cate where title='" . $data['title'] . "'",
                    true, true);
                if ($cate_id) {
                    $is_newtitle = 0;
                } else {
                    $is_newtitle = 1;
                }
                if ($is_newtitle) {
                    $data_cate = array();
                    $data_cate['title'] = $data['title'];
                    $data_cate['is_effect'] = 1;
                    $data_cate['is_delete'] = 0;
                    $data_cate['create_time'] = NOW_TIME;

                    $GLOBALS['db']->autoExecute(DB_PREFIX . "video_cate", $data_cate, 'INSERT');
                    $cate_id = $GLOBALS['db']->insert_id();
                }
                $data['cate_id'] = $cate_id;
            }
            if ($value['live_image']) {
                $data['live_image'] = $value['live_image'];
            } else {
                $data['live_image'] = $live_image;
            }

            $is_verify = 0;
            $video_code = '';
            if ($value['video_code']) {
                $is_verify = 1;
                $video_code = $value['video_code'];
            }

            if ($value['live_fee']) {
                $data['live_fee'] = $value['live_fee'];
                $data['is_live_pay'] = 1;
                $m_config['live_count_down'] = intval($m_config['live_count_down']) ? intval($m_config['live_count_down']) : 120;
                $live_pay_time = intval(NOW_TIME + $m_config['live_count_down']);
                $data['live_pay_time'] = $live_pay_time;
                $data['live_pay_type'] = 1;
            } else {
                $data['live_fee'] = 0;
                $data['is_live_pay'] = 0;
                $data['live_pay_type'] = 0;
                $data['live_pay_time'] = 0;
            }

            $sql = "select is_authentication,is_effect,is_ban,ban_time,mobile,login_ip,ban_type,apns_code,sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $data['user_id'];
            $user = $GLOBALS['db']->getRow($sql, true, true);
            if (intval($user['is_ban']) == 0 && intval($user['ban_time']) < get_gmtime() && intval($user['is_effect']) == 1) {
                //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
                $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);
                $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
                $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);

                $data['sort_init'] = 200000000 + $sort_init;
                $data['sort_num'] = $data['sort_init'];
                $data['prop_table'] = DB_PREFIX . "child_video_prop";
                $data['id'] = get_max_room_id(0);
                $data['head_image'] = $user['head_image'];
                $data['thumb_head_image'] = $user['thumb_head_image'];
                unset($data['group_id']);

                //插入数据
                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
                if ($GLOBALS['db']->affected_rows()) {
                    if ($is_verify) {
                        //edu_video插入教育直播数据
                        $edu_video_data['video_id'] = $data['id'];
                        $edu_video_data['deal_id'] = 0;
                        $edu_video_data['edu_cate_id'] = 0;
                        $edu_video_data['tags'] = '';
                        $edu_video_data['video_code'] = $video_code;
                        $edu_video_data['is_verify'] = $is_verify;
                        $edu_video_data['booking_class_id'] = 0;
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_video_info", $edu_video_data, 'INSERT');
                    }

                    $param = array();
                    $param['parent_id'] = $video_id;
                    $param['child_id'] = $data['id'];
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "child_room", $param, 'INSERT');

                    $video_redis = new VideoRedisService();
                    $video_redis->redis->sAdd('video_child_room_' . $video_id, $data['id']);
                    sync_video_to_redis($data['id'], '*', false);
                }
            } else {
                $data['sort_init'] = 0;
                $data['sort_num'] = 0;
                $data['prop_table'] = '';
                $data['id'] = 0;
                $data['head_image'] = '';
                $data['thumb_head_image'] = '';
            }
        }
        return true;
    }

    //推流后回调
    public function cstatus($room_id)
    {
        if (!$room_id) {
            return false;
        }
        $child_ids = $this->child_id($room_id);
        foreach ($child_ids as $item) {
            $sql = "update " . DB_PREFIX . "video set live_in = 1  where live_in =2 and id = " . $item['child_id'];
            $GLOBALS['db']->query($sql);
            sync_video_to_redis($item['child_id'], '*', false);
        }
        return true;
    }

    //判断是否是子房间
    public function is_child_room($room_id)
    {
        if (!$room_id) {
            return false;
        }

        $is_child = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM " . DB_PREFIX . "child_room WHERE child_id =" . $room_id,
            true, true);
        return $is_child > 0 ? 1 : 0;
    }

    //观众列表
    public function get_viewer_list($room_id, $limit, $where)
    {
        if (!$room_id) {
            return false;
        }

        $root = array();

        $sql = "SELECT user_id FROM " . DB_PREFIX . "child_room_viewer WHERE $where child_room =" . $room_id . " limit " . $limit;
        $root['viewer_user'] = $GLOBALS['db']->getAll($sql, true, true);

        $count_sql = "SELECT COUNT(id) FROM " . DB_PREFIX . "child_room_viewer WHERE child_room =" . $room_id;
        $root['rs_count'] = $GLOBALS['db']->getOne($count_sql, true, true);

        return $root;
    }

    //关联信息
    public function account_info($user_id, $limit)
    {
        if (!$user_id) {
            return false;
        }
        $root = array();

        $sql = "SELECT cra.*,u.id as user_id,u.nick_name,u.sex,u.head_image,u.thumb_head_image FROM " . DB_PREFIX . "child_room_account cra LEFT JOIN " . DB_PREFIX . "user u ON u.id = cra.p_user_id WHERE cra.c_user_id ={$user_id}  AND cra.is_effect =1 limit {$limit}";
        $root['p_user'] = $GLOBALS['db']->getAll($sql);

        $rs_sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "child_room_account cra LEFT JOIN " . DB_PREFIX . "user u ON u.id = cra.p_user_id WHERE cra.c_user_id ={$user_id}  AND cra.is_effect =1";
        $root['rs_count'] = $GLOBALS['db']->getOne($rs_sql);

        return $root;
    }

    //判断子房间账户是否已经在播
    public function is_live($user_id)
    {
        if (!$user_id) {
            return false;
        }

        $sql = "SELECT id as room_id FROM " . DB_PREFIX . "video  WHERE live_in = 1 AND user_id =" . $user_id;
        $video = $GLOBALS['db']->getAll($sql);

        if (!empty($video)) {
            foreach ($video as $item) {
                if ($this->is_child_room($item['room_id'])) {
                    api_ajax_return(array("status" => 0, "error" => "关联账号正在直播"));
                }
            }
        }
        return true;
    }
}
