<?php
fanwe_require(APP_ROOT_PATH . "/mapi/app/page.php");

class liveCModule extends baseModule
{
    public function show()
    {
        $is_vod = intval($_REQUEST['is_vod']);//0:观看直播;1:点播
        $room_id = intval($_REQUEST['room_id']);//房间号id; 如果有的话，则返回当前房间信息;
        $podcast_id = intval($_REQUEST['podcast_id']); //主播信息
        $user_id = intval($GLOBALS['user_info']['id']);//用户ID
        $type = intval($_REQUEST['type']);//type: 0:热门;1:最新;2:关注 [随机返回一个type类型下的直播]
        $require_type = 1;
        //子房间
        if (defined('CHILD_ROOM') && CHILD_ROOM == 1) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/ChildRoom.class.php');
            $child_room = new child_room();
            $root = $child_room->get_child_video($room_id, $user_id, $type, $_REQUEST, $require_type);
        } else {
            $root = get_video_info2($room_id, $user_id, $type, $_REQUEST, $require_type);
        }
        if (defined('OPEN_EDU_MODULE') && OPEN_EDU_MODULE == 1) {
            if ($root['podcast']['user']['id'] != $user_id && $this->check_video_is_verify($room_id)) {
                $root['status'] = 0;
                $root['error'] = '此房间为验证码直播，请下载APP观看';
                unset($root['group_id']);
            }
        }

        if (empty($root['urls']) && !empty($root['play_url'])) {
            $root['urls'] = array('20' => $root['play_url']);
        }

        //兼容再次查找视频
        if (($root['live_in'] == 0 || $root['live_in'] == 3) && $is_vod == 1 && empty($root['urls'])) {
            $video_file = c_get_vodset_by_video_id($room_id);
            if (isset($video_file['vodset'])) {
                $play_list = array();
                $vodset = $video_file['vodset'];
                $urls = array();
                foreach ($vodset as $k => $v) {
                    $playSet = $v['fileSet'];
                    for ($i = sizeof($playSet) - 1; $i >= 0; $i--) {
                        $play_list[] = $playSet[$i]['fileId'];
                        if ($playSet[$i]['playSet'][$i]['definition'] == 0) {
                            $playSet[$i]['playSet'][$i]['definition'] = 20;
                        }
                        $urls[$playSet[$i]['playSet'][$i]['definition']] = $playSet[$i]['playSet'][$i]['url'];
                    }
                }

                ksort($urls);
                $play_info = array(
                    'file_id' => $play_list[0],
                    'urls' => $urls,
                    'play_url' => array_shift($urls),
                );
                $root['urls'] = $play_info['urls'];
            }
        }
        if ($GLOBALS['user_info']) {
            $is_effect = $GLOBALS['db']->getOne("SELECT is_effect FROM " . DB_PREFIX . "user where id=" . $user_id,
                true, true);
            if (!$is_effect) {
                $root['status'] = 0;
                $root['error'] = '帐户已被禁用';
            } elseif ($GLOBALS['db']->getOne("SELECT login_ip FROM " . DB_PREFIX . "user WHERE is_ban = 1 and ban_type = 1 and login_ip like '%" . get_client_ip() . "%' and is_effect !=1")) {
                $root['status'] = 0;
                $root['error'] = '当前IP已被封停';
            }
        }
        $list_all = load_auto_cache("select_video");
        if ($root['status'] != 1) {
            unset($root['play_url']);
            unset($root['play_hls']);
            unset($root['play_flv']);
            unset($root['play_rtmp']);
            unset($root['group_id']);
            if ($root['user_id'] && empty($root['podcast'])) {
                $root['podcast'] = getuserinfo($user_id, $root['user_id'], $root['user_id']);
            } elseif ($podcast_id > 0) {
                foreach ($list_all as $v) {
                    if ($v['user_id'] == $podcast_id) {
                        $params = $_REQUEST;
                        $params['is_vod'] = $v['live_in'] == 1 ? 0 : 1;
                        $root = get_video_info($v['room_id'], $user_id, $type, $params);
                    }
                }
                if (empty($root['podcast'])) {
                    $root['podcast'] = getuserinfo($user_id, $podcast_id, $podcast_id);
                }
            }
        }

        if (empty($root['id'])) {
            if (empty($podcast_id)) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                $video_redis = new VideoRedisService();
                $podcast_id = $video_redis->getOne_db($room_id, "user_id");
                $root['podcast'] = getuserinfo($user_id, $podcast_id, $podcast_id);
            }
        } else {
            if ($root['video_type'] == 0 && $root['channelid']) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
                $video_factory = new VideoFactory();
                $video_info = $video_factory->Query($root['channelid']);
                $root['play_hls'] = $video_info['downstream_address']['hls'];
                $root['play_flv'] = $video_info['downstream_address']['flv'];
            }
        }

        if (defined('OPEN_PC_HISTORY') && OPEN_PC_HISTORY == 1 && $user_id > 0 && !empty($root['room_id'])) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserViewHistoryRedisService.php');
            $history_redis = new UserViewHistoryRedisService($user_id);
            $history_redis->view($root['room_id']);
        }

        $root['is_recommend'] = array();
        $is_recommend_list = load_auto_cache("selectpc_video", array('is_recommend' => 1, 'pc' => 1));
        foreach (array_rand($is_recommend_list, 2) as $key) {
            $root['is_recommend'][] = $is_recommend_list[$key];
        }
        $root['is_recommend_more_url'] = url("video#video_list", array('is_recommend' => 1));
        if (!$root['podcast']['user']['fans_count']) {
            $root['podcast']['user']['fans_count'] = 0;
        }

        if (!$root['viewer_num']) {
            $root['viewer_num'] = 0;
        }

        // 广告列表
        $place_id = 4;
        $root['ad_list'] = load_auto_cache("ad_list", $place_id);

        //直播公告消息
        $root['listmsg'] = load_auto_cache("article_notice");

        if (empty($root['room_title'])) {
            $root['room_title'] = $root['podcast']['user']['nick_name'] . '的直播间';
        }

        if (!$root['page_title']) {
            $root['page_title'] = $root['room_title'];
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($root['user_id']);
        $user_list = $userfollw_redis->following();

        $video_list = get_rand_video($room_id, $user_id, $type, $_REQUEST, 20);

        $root['follow_list'] = array();
        if (count($user_list)) {
            foreach ($list_all as $v) {
                if (count($root['follow_list']) < 3 && ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) {
                    $root['follow_list'][] = $v;

                    for ($i = 0; $i < count($video_list); $i++) {
                        if ($v['room_id'] == $video_list[$i]['room_id']) {
                            unset($video_list[$i]);
                        }
                    }
                    reset($video_list);
                }
            }
        }

        $root['video_list'] = array_values($video_list);
        $root['prop_list'] = array();

        foreach (load_auto_cache("prop_list") as $prop) {
            $prop['icon'] = empty($prop['pc_icon']) ? get_spec_image($prop['icon'], 100, 100) : $prop['pc_icon'];
            $root['prop_list'][] = $prop;
        }

        $tim_user_id = $user_id > 0 ? $user_id : 0;
        $usersig = load_auto_cache("usersig", array("id" => $tim_user_id));
        $m_config = load_auto_cache("m_config");
        $root['tim'] = array(
            'sdkappid' => $m_config['tim_sdkappid'],
            'account_type' => $m_config['tim_account_type'],
            'account_id' => $tim_user_id,
            'usersig' => $usersig['usersig'],
        );
        $root['qq_wpa_key'] = $m_config['qq_wpa_key'];
        //游客发言
        if (!$GLOBALS['user_info']) {
            $tourist_chat = $m_config['tourist_chat'];
            if ($tourist_chat == 1) {
                $toutist = es_session::get('tourist');
                if ($toutist) {
                    $user_id = $toutist['user_id'];
                } else {
                    $user_id = NOW_TIME . mt_rand(10, 99);
                    es_session::set('tourist', array('user_id' => $user_id));
                }
                $user_id = substr($user_id, -6);
                $root['tourist'] = array(
                    'tourist_id' => $user_id,
                    'tourist_head_image' => $m_config['pc_default_headimg'],
                    'tourist_level' => 1,
                );
            }
        }
        //众筹预约直播
        if (defined('ORDER_ZC') && ORDER_ZC == 1) {
            $is_order = is_ordered($room_id,$user_id);
            if($is_order){
                $root['is_live_pay'] = 0;
            }
        }

        if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1 && $root['is_live_pay'] == 1) {
            unset($root['play_url']);
            unset($root['play_hls']);
            unset($root['play_flv']);
            unset($root['group_id']);
        }

        api_ajax_return($root);
    }

    public function tipoff()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0,//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $room_id = intval($_REQUEST['room_id']);//房间号id; 如果有的话，则返回当前房间信息;
        $user_id = intval($GLOBALS['user_info']['id']);//用户ID

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $fields = array('id', 'user_id', 'channelid', 'title', 'live_in', 'room_title');
        $root = $video_redis->getRow_db($room_id, $fields);

        if (!$root) {
            $root = array(
                "error" => "你要举报的房间未找到",
                "status" => 0,
            );
            api_ajax_return($root);
        }
        $root['podcast'] = getuserinfo($user_id, $root['user_id'], $root['user_id']);
        $root['user'] = $GLOBALS['user_info'];
        $root['type_list'] = load_auto_cache("tipoff_type_list");
        if (empty($root['room_title'])) {
            $root['room_title'] = $root['podcast']['user']['nick_name'] . '的直播间';
        }

        if (!$root['page_title']) {
            $root['page_title'] = $root['room_title'];
        }
        if (!$root['page_title'] && $root['podcast']) {
            $root['page_title'] = $root['room_title'];
        }

        $root["status"] = 1;
        api_ajax_return($root);
    }

    public function do_tipoff()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0,//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $verify_code = strim($_REQUEST["verify_code"]);
        if (es_session::get("tipoff_verify") != md5($verify_code)) {
            $root = array(
                "error" => "验证码错误",
                "status" => 0,
            );
            api_ajax_return($root);
        }

        $room_id = intval($_REQUEST['room_id']); //被举报的房间id

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $fields = array('id', 'user_id');
        $video = $video_redis->getRow_db($room_id, $fields);
        if (!$video) {
            $root = array(
                "error" => "你要举报的房间未找到或已处理",
                "status" => 0,
            );
            api_ajax_return($root);
        }

        $user_id = $GLOBALS['user_info']['id'];
        if ($user_id == $video['user_id']) {
            $root = array(
                "error" => "不能举报自己",
                "status" => 0,
            );
            api_ajax_return($root);
        }

        $lock_key = "live:tipoff:{$user_id}:{$room_id}";
        $is_ok = $GLOBALS['cache']->set_lock($lock_key, 10);
        if (!$is_ok) {
            $root = array(
                "error" => "您已要举报该房间,无需频繁操作",
                "status" => 0,
            );
            api_ajax_return($root);
        }

        /* 一个房间是否可累计举报？
        $tipoff_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "tipoff where video_id =" . $room_id . " and from_user_id=" . $user_id);
        if($tipoff_info)
        {
             $root = array(
                 "error" => "您已要举报该房间",
                 "status" => 0,
             );
             api_ajax_return($root);
        }*/

        $card_type = intval($_REQUEST['card_type']);
        if ($card_type < 0) {
            $root['status'] = 0;
            $root['error'] = '请选择举报类型！';
            ajax_return($root);
        }

        $qq = strim($_REQUEST['qq']);
        if ($qq == '') {
            $root['status'] = 0;
            $root['error'] = '请填写真实QQ！';
            ajax_return($root);
        }

        $tipoff = array();
        $tipoff['from_user_id'] = $user_id;
        $tipoff['from_user_qq'] = $qq;
        $tipoff['to_user_id'] = $video["user_id"];
        $tipoff['video_id'] = $room_id;
        $tipoff['tipoff_type_id'] = intval($_REQUEST['card_type']);
        $tipoff['screenshot'] = strim($_REQUEST['screenshot']);
        $tipoff['remark'] = strim($_REQUEST['reason']);
        $tipoff['create_time'] = NOW_TIME;
        $GLOBALS['db']->autoExecute(DB_PREFIX . "tipoff", $tipoff, "INSERT");

        //累加举报次数
        $sql = "update " . DB_PREFIX . "video set tipoff_count = tipoff_count + 1 where id =" . $room_id;
        $GLOBALS['db']->query($sql);

        $root = array(
            "error" => "举报成功",
            "status" => 1,
        );
        api_ajax_return($root);
    }

    // 搜索结果页
    public function search()
    {
        $root = array();
        $p = intval($_REQUEST['p']);
        $key = strim($_REQUEST['key']);
        $type = strim($_REQUEST['type']);

        $p = $p > 0 ? $p : 1;//页码
        $page_size = 16;//分页数量
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $table_user = DB_PREFIX . 'user u';//主播表
        $table_live = DB_PREFIX . 'video v ';//直播表与主播表

        $field_live = 'v.id as room_id,v.sort_num,v.group_id,v.user_id,v.city,v.title,v.cate_id,v.live_in,v.video_type,
                        v.room_type,(v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number,v.live_image,v.head_image,v.thumb_head_image,v.xpoint,v.ypoint,u.v_type,
                        u.v_icon,u.nick_name,u.user_level';//要获取的直播字段
        $field_user = 'u.id as user_id,u.city,u.head_image,u.thumb_head_image,u.v_type,u.v_icon,u.nick_name,
                        u.user_level,u.fans_count';//要获取的主播字段

        if ($key != '') {//搜索关键字不为空时，进行搜索
            $where_live = 'v.title like \'%' . $key . '%\' or v.id like \'%' . $key . '%\' and v.live_in in (1,3) and v.room_type=3';//直播搜索语句
            $list_live = $GLOBALS['db']->getAll("SELECT {$field_live} FROM {$table_live} LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id  WHERE  {$where_live} LIMIT {$limit}",
                true, true);
            foreach ($list_live as $k => $v) {
                $list_live[$k]['head_image'] = get_spec_image($v['head_image'], 320, 180, 1);
                if ($v['thumb_head_image'] == '') {
                    $list_live[$k]['thumb_head_image'] = get_spec_image($v['head_image'], 40, 40);
                } else {
                    $list_live[$k]['thumb_head_image'] = get_spec_image($v['thumb_head_image'], 40, 40);
                }
                if (empty($v['live_image'])) {
                    $list_live[$k]['live_image'] = $list_live[$k]['head_image'];
                } else {
                    $list_live[$k]['live_image'] = get_spec_image($v['live_image'], 320, 180, 1);
                }
                $list_live[$k]['video_url'] = get_video_url($v['room_id'], $v['live_in']);
            }//拼凑出video_url字段
            $where_user = 'u.nick_name like \'%' . $key . '%\' ';//主播搜索语句
            $list_user = $GLOBALS['db']->getAll("SELECT {$field_user} FROM {$table_user} WHERE {$where_user} LIMIT $limit",
                true, true);
        } else {//如果关键字为空，直接返回错误
            $root['error'] = "关键字不能为空";

            api_ajax_return($root);
        }

        $root['key'] = $key;
        $root['type'] = empty($type) ? 0 : $type;
        $live = get_live();

        if (empty($list_live) && empty($list_user)) {
            //推荐
            $is_recommend_list = load_auto_cache("selectpc_video", array('is_recommend' => 1, 'pc' => 1));
            $root['is_recommend'] = $is_recommend_list;
        }

        //搜索类型 0或空 为所有，1为主播、2为直播
        if (!isset($root['type']) || intval($root['type']) == 0) {
            if ($list_user || $list_live) {
                $root['status'] = 1;
            } else {
                $root['status'] = 0;
            }
            $root['live_more'] = url("live#search", array("type" => 2, "key" => $key));
            $root['user_more'] = url("live#search", array("type" => 1, "key" => $key));
            $root['user_list'] = is_live($list_user, $live);
            $root['live_list'] = $list_live;
            api_ajax_return($root);
        } elseif (intval($root['type']) == 1) {
            if ($list_user) {
                $root['status'] = 1;
            } else {
                $root['status'] = 0;
            }
            $rs_count = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM $table_user WHERE {$where_user}", true,
                true);//获取的直播记录数量
            $page = new Page($rs_count, $page_size);//主播分页
            $root['type'] = $type;
            $root['user_list'] = is_live($list_user, $live);
            $root['page'] = $page->show();
            api_ajax_return($root);
        } elseif (intval($root['type']) == 2) {
            if ($list_live) {
                $root['status'] = 1;
            } else {
                $root['status'] = 0;
            }
            $rs_count = $GLOBALS['db']->getOne("SELECT COUNT(v.id) FROM $table_live  LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id WHERE  {$where_live}",
                true, true);//获取的直播记录数量
            $page = new Page($rs_count, $page_size);//直播分页
            $root['type'] = $type;
            $root['live_list'] = $list_live;
            $root['page'] = $page->show();

            api_ajax_return($root);
        } else {
            $root['error'] = "搜索类型错误";
        }

    }

    //判断是否为验证码直播
    public function check_video_is_verify($room_id)
    {
        if (!$room_id) {
            return false;
        }
        $table = DB_PREFIX . "edu_video_info";
        $where = " video_id =" . $room_id;
        $verify = $GLOBALS['db']->getRow("SELECT video_code,is_verify FROM $table WHERE  $where", true,
            true);//获取的直播记录数量
        if (!empty($verify) && $verify['is_verify'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}