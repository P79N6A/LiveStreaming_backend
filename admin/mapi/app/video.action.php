<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
fanwe_require(APP_ROOT_PATH . 'mapi/lib/video.action.php');
class videoCModule extends videoModule
{
    /**
     * 当前房间用户列表（包括机器人，但不包括虚拟人数）
     */
    public function viewer()
    {
        $root = array();
        $group_id = strim($_REQUEST['group_id']); //聊天群id
        $page = intval($_REQUEST['p']); //取第几页数据
        $root = load_auto_cache("video_viewer", array('group_id' => $group_id, 'page' => $page));

        if (OPEN_PAI_MODULE == 1 && $page == 1) {
            //增加竞拍排序
            $sql = "select pai_id from " . DB_PREFIX . "video where group_id = '" . $group_id . "'";
            $video['pai_id'] = $GLOBALS['db']->getOne($sql);
            if (intval($video['pai_id']) > 0) {
                $user_list = $GLOBALS['db']->getAll("SELECT user_id,pai_status,order_id,order_status,pai_diamonds FROM " . DB_PREFIX . "pai_join WHERE pai_id=" . $video['pai_id'] . " ORDER BY pai_diamonds DESC limit 0,5");
                //log_result("SELECT user_id,pai_status,order_id,order_status,pai_diamonds FROM ".DB_PREFIX."pai_join WHERE pai_id=".$video['pai_id']." ORDER BY pai_diamonds DESC ");
                if ($user_list) {

                    fanwe_require(APP_ROOT_PATH . '/mapi/lib/redis/BaseRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $fields = array('is_authentication', 'head_image', 'user_level', 'v_type', 'v_icon', 'nick_name', 'signature', 'sex', 'province', 'city', 'thumb_head_image', 'v_explain', 'emotional_state', 'job', 'birthday', 'apns_code');

                    foreach ($user_list as $k => $v) {

                        if (intval($v['user_id']) > 0) {
                            $user_list[$k] = $user_redis->getRow_db(intval($v['user_id']), $fields);
                            $user_list[$k]['user_id'] = intval($v['user_id']);
                            $user_list[$k]['type'] = intval($v['pai_status']);
                            $user_list[$k]['pai_diamonds'] = intval($v['pai_diamonds']);
                        }

                    }

                    $root['tag'] = 1;
                }
                $list_1 = $root['list'];
                foreach ($list_1 as $k => $v) {
                    foreach ($user_list as $k2 => $v2) {
                        if ($list_1[$k]['user_id'] == $v2['user_id']) {
                            unset($list_1[$k]);
                            break;
                        }

                    }
                }
                if (!$list_1) {
                    $list_1 = array();
                }
                if (!$user_list) {
                    $user_list = array();
                }
                //log_result("==user_list==");
                //log_result($user_list);
                //log_result("==list_1==");
                //log_result($list_1);
                $root['list'] = array_merge($user_list, $list_1);

                //log_result("==list_2==");
                //log_result($root['list']);
                //$root['list']=$user_list;
                //$root['has_next']=0;
                //$root['page']=1;
                //$root['status']=1;
                ajax_return($root);
            }
        }
        ajax_return($root);
        //$GLOBALS['user_info']['id'] = 1;

        $group_id = strim($_REQUEST['group_id']); //聊天群id
        $page = intval($_REQUEST['p']); //取第几页数据
        if ($page == 0) {
            $page = 1;
        }

        $page_size = 50;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $sql = "select u.id as user_id,u.head_image,u.user_level,u.v_type,u.v_icon from " . DB_PREFIX . "video_viewer v left join " . DB_PREFIX . "user u on u.id = v.user_id where v.end_time = 0 and v.group_id = '" . $group_id . "' order by u.is_robot,u.user_level desc limit " . $limit;
        //$sql = "select u.id as user_id,u.head_image,u.user_level,u.v_type,u.v_icon from ".DB_PREFIX."user u order by u.user_level desc limit ".$limit;
        $list = $GLOBALS['db']->getAll($sql);
        //处理头像绝对路径
        foreach ($list as $k => $v) {
            $list[$k]['head_image'] = get_spec_image($v['head_image']);
            //$list[$k]['v_icon'] = get_abs_img_root($v['v_icon']);
        }
        $root['list'] = $list;
        //$root['sql'] = $sql;
        if (count($list) == $page_size) {
            $root['has_next'] = 1;
        } else {
            $root['has_next'] = 0;
        }

        //当前房间人数 = 当前实时观看人数（实际,不含虚拟人数,不包含机器人) + 当前虚拟观看人数 + 机器人
        //$root['viewer_num'] = $root['watch_number'] + $root['virtual_watch_number'] + $root['robot_num'];
        $sql = "select (watch_number + robot_num+ virtual_watch_number) as num from " . DB_PREFIX . "video where group_id = '" . $group_id . "'";
        //$sql = "select count(*) from ".DB_PREFIX."user";
        $root['watch_number'] = $GLOBALS['db']->getOne($sql);
        $root['page'] = $page; //
        $root['status'] = 1;

        //print_r($root);exit;
        //log_result($root);
        ajax_return($root);
    }

    /*
     * 直播详情
     */
    public function get_video()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            ajax_return(array('error' => '用户未登陆,请先登陆.', 'status' => 0, 'user_login_status' => 0));
        }
        //$user_id = intval($GLOBALS['user_info']['id']);//登陆用户ID
        $room_id = intval($_REQUEST['room_id']); //房间号id
        $is_vod = intval($_REQUEST['is_vod']); //0:观看直播;1:点播

        $table = DB_PREFIX . ($is_vod ? 'video_history' : 'video');
        $where = ($is_vod ? "live_in=0 and id=" : "id=") . $room_id;
        $video = $GLOBALS['db']->getRow("SELECT id as roome_id,group_id,watch_number,video_vid,room_type,vote_number,channelid,live_in,cate_id,user_id FROM $table WHERE $where", true, true); //查找是否有此房间
        if (!$video) {
            ajax_return(array('status' => 10001));
        }

        $cate_info = $GLOBALS['db']->getRow("SELECT id as cate_id,title FROM " . DB_PREFIX . "video_cate WHERE id=" . $video['cate_id'], true, true);
        unset($video['cate_id']);

        $user_info = $GLOBALS['db']->getRow("SELECT id,nick_name,signature,is_authentication,province,city,sex,user_level,head_image,thumb_head_image,v_type,v_icon,fans_count FROM " . DB_PREFIX . "user WHERE id=" . $video['user_id'], true, true);
        unset($video['user_id']);

        //播放地址
        $video_vid_list = array();
        $video_vid = get_vodset_by_video_id($room_id);
        foreach ($video_vid['vodset'] as $k => $v) {
            foreach ($v['fileSet'][0]['playSet'] as $kk => $vv) {
                if (strpos($vv['url'], 'f0.mp4')) {
                    $video_vid_list[$k]['url'] = $vv['url'];
                }
            }
            $video_vid_list[$k]['image_url'] = get_spec_image($v['fileSet'][0]['image_url']);
            $video_vid_list[$k]['fileId'] = $v['fileSet'][0]['fileId'];
        }
        $video['video_list'] = $video_vid_list; //视频数据

        $gift_list = $GLOBALS['db']->getAll("SELECT id,name,score,diamonds,icon,ticket,is_much,sort,is_red_envelope,is_animated,anim_type,robot_diamonds FROM " . DB_PREFIX . "prop WHERE is_effect=1", true, true);

        ajax_return(array(
            'cate_info' => $cate_info, //话题信息
            'user_info' => $user_info, //直播信息
            'video' => $video, //视频信息
            'page_title' => $user_info['nick_name'] . "的直播间", //标题***的直播间
            'gift_list' => $gift_list, //礼物列表
            'status' => 1
        ));
    }
    //PC端全部直播接口
    public function video_list()
    {
        $root = array();
        $root['page_title'] = "全部直播";
        $p = intval($_REQUEST['p']); //页码
        $cate_id = intval($_REQUEST['cate_id']); //话题ID
        $is_recommend = intval($_REQUEST['is_recommend']); //推荐列表
        $is_hot = intval($_REQUEST['is_hot']); //热门列表
        $is_new = intval($_REQUEST['is_new']); //最新列表
        $is_family_hot = intval($_REQUEST['is_family_hot']); //热门家族列表
        $change_new = intval($_REQUEST['change_new']); //换一换
        $is_hot_cate = intval($_REQUEST['is_hot_cate']); //热门话题
        $jump_type = intval($_REQUEST['jump_type']); //热门话题
        $page_size = 12; //分页数量
        if ($p == 0 || $p == '') {
            $p = 1;
        }
        if ($jump_type == 1) {
            $this->live_list($jump_type, $cate_id);
        } elseif ($jump_type == 2) {
            $this->focus_list($jump_type);
        } elseif ($jump_type == 3) {
            $this->history_list($jump_type);
        }
        $param = array('page' => $p, 'page_size' => $page_size);
        if ($GLOBALS['user_info']) {$param['has_private'] = 1;}
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $has_is_authentication = intval($m_config['has_is_authentication']) ? 1 : 0;
        if ($has_is_authentication && $m_config['ios_check_version'] == '') {
            $countsql = "SELECT count(v.id) FROM " . DB_PREFIX . "video v
			LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) and u.is_authentication = 2 ";
        } elseif ($GLOBALS['user_info']) {
            $countsql = "SELECT count(v.id) from " . DB_PREFIX . "video v where v.live_in in (1,3) and v.room_type in (1,3)";
        } else {
            $countsql = "SELECT count(v.id) from " . DB_PREFIX . "video v where v.live_in in (1,3) and v.room_type = 3 ";
        }
        if ($cate_id) {
            $countsql .= " and  v.cate_id = " . $cate_id;
        }
        if ($is_new) {
            $param['is_new'] = $is_new;
            $param['cate_id'] = $cate_id;
            $root['cate_top'] = load_auto_cache("cate_top");
            $list = load_auto_cache("selectpc_video", $param);
            $root['list'] = $list;
            $root['type'] = "is_new";
        } elseif ($is_recommend) {
            $countsql .= ' and v.is_recommend = ' . $is_recommend;
            $param['is_recommend'] = $is_recommend;
            $list = load_auto_cache("selectpc_video", $param);
            $root['list'] = $list;
            $root['type'] = "is_recommend";
        } elseif ($is_family_hot) {
            $countsql = "SELECT count(v.id) FROM (SELECT family_id FROM " . DB_PREFIX . "family_join GROUP BY family_id ORDER BY count(*) DESC) f, " . DB_PREFIX . "video v LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where u.family_id = f.family_id and u.family_id >0 and v.live_in in (1,3) and v.room_type = 3 ";
            $param['is_family_hot'] = $is_family_hot;
            $list = load_auto_cache("selectpc_video", $param);
            $root['list'] = $list;
            $root['type'] = "is_family_hot";
        } elseif ($is_hot) {
            $param['is_hot'] = $is_hot;
            $list = load_auto_cache("selectpc_video", $param);
            $root['list'] = $list;
            $root['type'] = "is_hot";
        } else {
            $param['page_size'] = 12; //分页数量
            $page_size = 12;
            $param['cate_id'] = $cate_id;
            if ($change_new) {
                $param['change'] = 1;
                $list = load_auto_cache("new_list", $param);
            } else {
                $list = load_auto_cache("new_list");
            }
            if ($is_hot_cate) {
                $param['is_hot_cate'] = 1;
            }
            $root['new_list'] = $list;
            $root['cate_top'] = load_auto_cache("cate_top");
            $info = load_auto_cache("all_video", $param);
            $root['list'] = $info['list'];
        }

        // 广告列表
        $place_id = 2;
        $root['ad_list'] = load_auto_cache("ad_list", $place_id);

        $rs_count = $GLOBALS['db']->getOne($countsql, true, true);
        $page = new Page($rs_count, $page_size); //初始化分页对象
        $root['page'] = $page->show();
        $root['cate_id'] = $cate_id;
        $root['status'] = empty($root) ? 0 : 1;
        $root['jump_type'] = $jump_type;
        $root['qq_wpa_key'] = $m_config['qq_wpa_key'];
        api_ajax_return($root);
    }

    public function live_list($jump_type, $cate_id)
    {
        $p = intval($_REQUEST['p']); //页码
        $page_size = 12;
        $cate_id = empty($_REQUEST['cate_id']) ? $cate_id : intval($_REQUEST['cate_id']); //话题ID
        if (empty($jump_type)) {
            $jump_type = 1;
        }
        $param = array('page' => $p, 'page_size' => $page_size, 'cate_id' => $cate_id);
        if ($GLOBALS['user_info']) {$param['has_private'] = 1;}
        $info = load_auto_cache("all_video", $param);
        $countsql = "SELECT count(id) from " . DB_PREFIX . "video v where v.live_in in (1,3) and v.room_type = 3 ";
        if ($cate_id) {
            $countsql .= " and  v.cate_id = " . $cate_id;
        }
        $rs_count = $GLOBALS['db']->getOne($countsql, true, true);
        $page = new Page($rs_count, $page_size);
        $cate_top = load_auto_cache("cate_top");
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $root = array('list' => $info['list'], 'page' => $page->show(), 'jump_type' => $jump_type, 'cate_top' => $cate_top, 'cate_id' => $cate_id, 'qq_wpa_key' => $m_config['qq_wpa_key']);
        api_ajax_return($root);
    }

    public function new_list()
    {
        $param = array('change' => 1);
        $list = load_auto_cache("new_list", $param);
        $root = array('new_list' => $list);
        api_ajax_return($root);
    }

    public function history_list($jump_type)
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        if (empty($jump_type)) {
            $jump_type = 3;
        }
        $p = intval($_REQUEST['p']); //页码
        $page_size = 12;
        $user_id = intval($GLOBALS['user_info']['id']); //用户ID

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserViewHistoryRedisService.php');
        $history_redis = new UserViewHistoryRedisService($user_id);
        $room_id = intval($_REQUEST['del_room_id']);
        if ($room_id > 0) {
            $history_redis->remove($room_id);
        }

        $list = $history_redis->get_history($p, $page_size);
        $rs_count = $history_redis->count();
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $page = new Page($rs_count, $page_size);
        $root = array('list' => $list, 'page' => $page->show(), 'jump_type' => $jump_type, 'qq_wpa_key' => $m_config['qq_wpa_key']);
        api_ajax_return($root);
    }

    public function focus_list($jump_type)
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        if (empty($jump_type)) {
            $jump_type = 2;
        }
        $p = intval($_REQUEST['p']); //页码
        $user_id = intval($GLOBALS['user_info']['id']); //登录用户id
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($user_id);
        $user_list = $userfollw_redis->following();
        //私密直播  video_private,私密直播结束后， 本表会清空
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
        $video_private_redis = new VideoPrivateRedisService();
        $private_list = $video_private_redis->get_video_list($user_id);
        $list = array();
        $param = array('has_private' => 1);
        if (sizeof($private_list) || sizeof($user_list)) {
            $info = load_auto_cache("focus_video", $param);
            $list_all = $info;
            foreach ($list_all as $k => $v) {
                if (($v['room_type'] == 1 && in_array($v['room_id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) {
                    $list[] = $v;
                }
            }
        }

        if ($p < 1) {
            $p = 1;
        }
        $page_size = 18;
        $start = ($p - 1) * $page_size;
        $new_list = array_slice($list, $start, $page_size);
        $page = new Page(count($list), $page_size); //初始化分页对象
        $root = array('list' => $new_list, 'page' => $page->show(), 'jump_type' => $jump_type);
        api_ajax_return($root);
    }

    //我的关注
    public function focus_video()
    {
        $root = array();
        $page = intval($_REQUEST['p']); //取第几页数据
        $cateid = intval($_REQUEST['cate_id']);
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            //关注
            $user_id = intval($GLOBALS['user_info']['id']); //登录用户id
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $userfollw_redis = new UserFollwRedisService($user_id);
            $user_list = $userfollw_redis->following();
            //私密直播  video_private,私密直播结束后， 本表会清空
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();
            $private_list = $video_private_redis->get_video_list($user_id);
            $list = array();
            if ($page == 0 || $page == '') {
                $page = 1;
            }
            $page_size = 10;
            $start = ($page - 1) * $page_size;
            $param = array('has_private' => 1, 'cate_id' => $cateid);
            if (sizeof($private_list) || sizeof($user_list)) {
                $info = load_auto_cache("focus_video", $param);
                $list_all = $info;
                foreach ($list_all as $k => $v) {
                    if (($v['room_type'] == 1 && in_array($v['room_id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) {
                        $list[] = $v;
                    }
                }
            }
            $new_list = array_slice($list, $start, $page_size);

            $root['list'] = $new_list;
            $root['cate_top'] = load_auto_cache("cate_top");
            $page = new Page(count($list), $page_size); //初始化分页对象
            $root['status'] = 1;
            $root['page'] = $page->show();
        }
        api_ajax_return($root);
    }
}
