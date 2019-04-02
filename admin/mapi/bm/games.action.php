<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/games.action.php');
class gamesCModule extends gamesModule
{

    /**
     * 选择正在游戏中的直播列表
     * @param  [type]  $list    直播间列表
     * @param  mixd    $game_id false为不过滤，1：只选择游戏id为1的直播间
     * @return [type]           [description]
     */
    protected static function selectGameVideo($list, $game_id = false)
    {
        $array = [];
        foreach ($list as $key => $value) {
            $room_game_id = self::getGameIdByRoomId($value['room_id']);
            if ($room_game_id && (!$game_id || $room_game_id == $game_id)) {
                $list[$key]['game_id'] = $room_game_id;
                $array[] = $list[$key];
            }
        }
        return $array;
    }
    protected static function getGameIdByRoomId($room_id)
    {
        $game_log_id = self::$video_redis->getOne_db($room_id, 'game_log_id');
        if (!$game_log_id) {
            return 0;
        }
        $game = self::$redis->get($game_log_id, 'game_id');
        return intval($game['game_id']);
    }
    //游戏首页
    public function index()
    {
        $root = array('status' => 1, 'error' => '');

        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        //$GLOBALS['user_info']['id']=167628;
        //头像
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆."; // es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }
        $user_id = intval($GLOBALS['user_info']['id']);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        if (OPEN_DIAMOND_GAME_MODULE == 1) {
            $user_data = $user_redis->getRow_db($user_id, array('id', 'head_image', 'diamonds'));
            //游戏币
            $root['coins'] = 0;
        } else {
            $user_data = $user_redis->getRow_db($user_id, array('id', 'head_image', 'diamonds', 'coin'));
            //游戏币
            $root['coins'] = $user_data['coin'];
        }

        $root['head_image'] = get_spec_image($user_data['head_image']);
        $root['diamonds'] = $user_data['diamonds'];

        //在线人数
        /*require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
        $api = createTimAPI();
        $show_online_user = 0;
        if($m_config['tim_identifier']&&!is_array($api)){
        $ret = $api->group_get_group_member_info($m_config['on_line_group_id'],0,0);
        $online_user = isset($ret['MemberNum'])?intval($ret['MemberNum']):0;//减去管理员本身
        $root['online_user']=$online_user;
        }else{
        $root['online_user']=0;
        }*/
        $root['online_user'] = 0;

        //游戏队列
        $list = $plugin = $GLOBALS['db']->getALL("SELECT id,child_id,name,image,index_image,type,class as class_name FROM " . DB_PREFIX . "plugin WHERE is_effect=1 and type=2", true, true);
        foreach ($list as $key => $value) {
            $list[$key]['image'] = get_spec_image($value['image']);
        }

        //关注房间
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($user_id);
        $user_list = $userfollw_redis->following();

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
        $video_private_redis = new VideoPrivateRedisService();
        $private_list = array(); //$video_private_redis->get_video_list($user_id);

        if (sizeof($private_list) || sizeof($user_list)) {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $sdk_version_name = strim($_REQUEST['sdk_version_name']);
            $dev_type = strim($_REQUEST['sdk_type']);
            if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
                $list_all = load_auto_cache("select_video_check", array('has_private' => 1));
            } else {
                $list_all = load_auto_cache("select_video", array('has_private' => 1));
            }
            foreach ($list_all as $k => $v) {
                if ((($v['room_type'] == 1 && in_array($v['room_id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) && ($v['user_id'] != '13888888888' || $v['user_id'] != '13999999999')) {
                    $focus_list[] = $v;
                } else if ($v['user_id'] == $user_id && $v['room_type'] == 1 && $v['live_in'] == 1) {
                    $user_video = array();
                    $user_video = $v;
                }
            }
        }
        if ($user_video) {
            array_unshift($focus_list, $user_video);
        }
        $focus_list = self::selectGameVideo($focus_list);

        $root['list'] = $list;
        $root['focus_list'] = $focus_list;
        $root['status'] = 1;
        $root['init_version'] = intval($m_config['init_version']); //手机端配置版本号

        $room = Model::build('video')->getOneWithUser(['user_id' => $user_id]);
        if (!$room) {
            $root['has_room'] = 0;
        } else {
            $root['has_room'] = intval($room['room_id']); //self::getGameIdByRoomId($room['room_id']);
        }

        ajax_return($root);
    }
    public function join()
    {
        $game_id = intval($_REQUEST['game_id']);
        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
        if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
            $list = load_auto_cache("new_video_check");
        } else {
            $list = load_auto_cache("new_video");
        }
        $list = self::selectGameVideo($list, $game_id);
        if (empty($list)) {
            self::returnError('未找到游戏房间');
        }
        $room = $list[array_rand($list)];
        $room['sdk_type'] = 0;
        self::returnError('', 1, ['room' => $room]);
    }
    public function join_by_key()
    {
        $private_key = trim($_REQUEST['key_number']);
        if ($private_key == '') {
            self::returnError('请输入私密码');
        }
        $room = Model::build('video')->getOneWithUser(['private_key' => $private_key]);
        if (!$room) {
            self::returnError('未找到游戏房间');
        }
        $room['game_id'] = self::getGameIdByRoomId($room['room_id']);
        $room['sdk_type'] = 0;
        self::returnError('', 1, ['room' => $room]);
    }
    public function my_room()
    {
        $user_id = self::getUserId();
        $room = Model::build('video')->getOneWithUser(['user_id' => $user_id]);
        if (!$room) {
            self::returnError('未找到我的房间');
        }
        $room['game_id'] = self::getGameIdByRoomId($room['room_id']);
        $room['sdk_type'] = 0;
        self::returnError('', 1, ['room' => $room]);
    }
    //游戏房间
    public function games_room()
    {
        $id = intval($_REQUEST['id']); //游戏id
        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
        $m_config = load_auto_cache("m_config");
        //该游戏公开房间队列
        if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
            $list = load_auto_cache("new_video_check");
        } else {
            $list = load_auto_cache("new_video");
        }
        $list = self::selectGameVideo($list, $id);
        self::returnError('', 1, ['list' => $list]);
    }
    /**
     * 关注的游戏直播间
     * @return [type] [description]
     */
    public function follow_room()
    {
        $user_id = self::getUserId();

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
        $userfollw_redis = new UserFollwRedisService($user_id);
        $user_list = $userfollw_redis->following();

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
        $video_private_redis = new VideoPrivateRedisService();
        $private_list = array(); //$video_private_redis->get_video_list($user_id);

        if (sizeof($private_list) || sizeof($user_list)) {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $sdk_version_name = strim($_REQUEST['sdk_version_name']);
            $dev_type = strim($_REQUEST['sdk_type']);
            if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
                $list_all = load_auto_cache("select_video_check", array('has_private' => 1));
            } else {
                $list_all = load_auto_cache("select_video", array('has_private' => 1));
            }
            foreach ($list_all as $k => $v) {
                if ((($v['room_type'] == 1 && in_array($v['room_id'], $private_list)) || ($v['room_type'] == 3 && in_array($v['user_id'], $user_list))) && ($v['user_id'] != '13888888888' || $v['user_id'] != '13999999999')) {
                    $list[] = $v;
                } else if ($v['user_id'] == $user_id && $v['room_type'] == 1 && $v['live_in'] == 1) {
                    $user_video = array();
                    $user_video = $v;
                }
            }
        }
        if ($user_video) {
            array_unshift($list, $user_video);
        }
        $list = self::selectGameVideo($list);
        self::returnError('', 1, ['list' => $list]);
    }

    //开始直播，加入预先创建房间 并修改 begin_time状态
    public function bm_add_video()
    {
        if (!$GLOBALS['user_info']) {
            $return['error'] = "用户未登陆,请先登陆.";
            $return['status'] = 0;
            $return['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            //用户是否禁播，$is_ban=1 永久禁播；$is_ban=0非永久禁播，$ban_time禁播结束时间
            $user_id = intval($GLOBALS['user_info']['id']);
            $sql = "select is_authentication,is_ban,ban_time,mobile,login_ip,ban_type,apns_code,sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $user_id;
            $user = $GLOBALS['db']->getRow($sql, true, true);
            $video_classified = intval($_REQUEST['video_classified']);
            $is_authentication = intval($user['is_authentication']);
            $m_config = load_auto_cache("m_config");
            if (!isset($m_config['video_type'])) {
                $re = array("error" => "直播类型不存在", "status" => 0);
                ajax_return($re);
            }
            $dev_type = strim($_REQUEST['sdk_type']);
            $sdk_version_name = strim($_REQUEST['sdk_version_name']);
            //提过限制开播
            $allow = 0;
            if ($user['mobile'] == '13888888888' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
                $allow = 1;
            }
            if ($user['mobile'] == '13999999999' && $m_config['ios_check_version'] != '') {
                $allow = 1;
            }

            if ($allow) {
                $is_authentication = 2;
                $m_config['is_limit_time'] = 0;
            }

            if ($m_config['must_authentication'] == 1 && $is_authentication != 2) {
                $re = array("error" => "请认证后再发起直播 ", "status" => 0);
                ajax_return($re);
            }

            if (intval($m_config['is_limit_time']) == 1) {
                $now = to_date(get_gmtime(), "H");
                if (intval($m_config['is_limit_time_end']) == intval($m_config['is_limit_time_start'])) {
                    $re = array("error" => "直播功能已关闭", "status" => 0);
                    ajax_return($re);
                }
                $to_day = 1;
                if (intval($m_config['is_limit_time_start']) > intval($m_config['is_limit_time_end'])) {
                    $to_day = 0;
                }

                if ($to_day == 0 && intval($m_config['is_limit_time_start']) > $now && intval($m_config['is_limit_time_end']) <= $now) {
                    $re = array("error" => "请在每天的" . intval($m_config['is_limit_time_start']) . "时到第二天的" . intval($m_config['is_limit_time_end']) . "时期间进行直播", "status" => 0);
                    ajax_return($re);
                }

                if ($to_day == 1 && (intval($m_config['is_limit_time_start']) > $now || intval($m_config['is_limit_time_end']) <= $now)) {
                    $re = array("error" => "请在每天的" . intval($m_config['is_limit_time_start']) . "时到" . intval($m_config['is_limit_time_end']) . "时期间进行直播", "status" => 0);
                    ajax_return($re);
                }
            }

            $apns_code = addslashes($_REQUEST['apns_code']);
            if ($user['ban_type'] == 1 && $user['login_ip'] == get_client_ip() && $user['is_ban'] == 1) {
                $re = array("error" => "请求房间id失败，当前IP已被封停，请联系客服处理", "status" => 0);
                ajax_return($re);
            }

            if ($user['ban_type'] == 2 && $user['apns_code'] == $apns_code && $user['is_ban'] == 1) {
                $re = array("error" => "请求房间id失败，当前设备已被禁用，请联系客服处理", "status" => 0);
                ajax_return($re);
            }

            if (intval($user['is_ban']) == 0 && intval($user['ban_time']) < get_gmtime()) {
                //$_REQUEST['title'] = $_REQUEST['title']?$_REQUEST['title']:"#新人直播#";
                $title = strim(str_replace('#', '', $_REQUEST['title']));

                //$title = iconv("UTF-8","UTF-8//IGNORE",$title);

                //===lym start====
                $cate_name = $title;
                //===lym end===
                $cate_id = intval($_REQUEST['cate_id']);

                $xpoint = floatval($_REQUEST['xpoint']); //x座标(用来计算：附近)
                $ypoint = floatval($_REQUEST['ypoint']); //y座标(用来计算：附近)
                $live_image = strim($_REQUEST['live_image']); //图片地址,手机端图片先上传到oss，然后获得图片地址,再跟其它资料一起提交到服务器

                $location_switch = intval($_REQUEST['location_switch']); //1-上传当前城市名称
                $province = strim($_REQUEST['province']); //省
                $city = strim($_REQUEST['city']); //市

                $is_private = 1; //intval($_REQUEST['is_private']);//1：私密聊天; 0:公共聊天
                $share_type = strtolower(strim($_REQUEST['share_type'])); //WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,EMAIL,SMS,SINA
                if ($share_type == 'null') {
                    $share_type = '';
                }

                //检查话题长度
                if (strlen($title) > 60) {
                    $return['error'] = "话题太长";
                    $return['status'] = 0;
                    ajax_return($return);
                }

                //$private_ids = strim($_REQUEST['private_ids']);//字符串类型的私聊好友id 23,123,3455 以英文逗号分割的字符串 只有私聊时才需要上传这个参数

                $sql = "select id,video_type from " . DB_PREFIX . "video where live_in in (1,2) and is_bm =1 and user_id = " . $user_id;
                $video = $GLOBALS['db']->getRow($sql, true, true);
                if ($video) {

                    //更新心跳时间，免得被删除了
                    $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
                    $GLOBALS['db']->query($sql);

                    if ($GLOBALS['db']->affected_rows()) {
                        //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                        $return['status'] = 1;
                        $return['error'] = '';
                        $return['room_id'] = intval($video['id']);
                        $return['video_type'] = intval($video['video_type']);
                        $return['has_room'] = 1;
                        ajax_return($return);
                    }
                }

                //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
                $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
                $list = $GLOBALS['db']->getAll($sql, true, true);
                foreach ($list as $k => $v) {
                    //结束直播
                    do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
                }

                //话题
                if ($cate_id) {
                    //$cate_title = $GLOBALS['db']->getOne("select title from ".DB_PREFIX."video_cate where id=".$cate_id,true,true);
                    $cate = load_auto_cache("cate_id", array('id' => $cate_id));
                    $cate_title = $cate['title'];
                    if ($cate_title != $title) {
                        $cate_id = 0;
                    }
                }

                if ($cate_id == 0 && $title != '') {
                    $cate_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "video_cate where title='" . $title . "'", true, true);
                    if ($cate_id) {
                        $is_newtitle = 0;
                    } else {
                        $is_newtitle = 1;
                    }
                }

                if ($is_newtitle) {
                    $data_cate = array();
                    $data_cate['title'] = $title;
                    $data_cate['is_effect'] = 1;
                    $data_cate['is_delete'] = 0;
                    $data_cate['create_time'] = NOW_TIME;

                    $GLOBALS['db']->autoExecute(DB_PREFIX . "video_cate", $data_cate, 'INSERT');
                    $cate_id = $GLOBALS['db']->insert_id();
                }

                if ($m_config['must_cate'] == 1) {
                    if (!$cate_id) {
                        $re = array("error" => "直播话题不能为空", "status" => 0);
                        ajax_return($re);
                    }
                }

                //添加位置

                if ($province == 'null') {
                    $province = '';
                }

                if ($city == 'null') {
                    $city = '';
                }

                $province = str_replace("省", "", $province);

                $city = str_replace("市", "", $city);

                if (($province == '' || $city == '') && $location_switch == 1) {
                    /*
                    //客户端没有定位到,服务端则用ip再定位一次
                    fanwe_require APP_ROOT_PATH . "system/extend/ip.php";
                    $ip = new iplocate ();
                    $area = $ip->getaddress ( CLIENT_IP );
                    $location = $area ['area1'];
                     */

                    $ipinfo = get_ip_info();

                    $province = $ipinfo['province'];
                    $city = $ipinfo['city'];

                    //$title = print_r($ipinfo,1);
                }

                if ($province == '') {
                    $province = '千秀';
                }

                if ($city == '') {
                    $city = '千秀';
                }
                if ($city == '千秀' || $province == '千秀') {
                    $xpoint = ''; //x座标(用来计算：附近)
                    $ypoint = ''; //y座标(用来计算：附近)
                }
                //
                $video_id = get_max_room_id(0);
                $data = array();
                $data['id'] = $video_id;
                //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
                if ($is_private == 1) {
                    $data['room_type'] = 1;
                } else {
                    $data['room_type'] = 3;
                }

                $data['private_key'] = rand(100000, 999999); //私密直播key
                //判断私有码的唯一性
                //否则重新随机
                $private_key_array_this = array();
                $private_key_array_this[] = $data['private_key'];
                $private_key_array = $GLOBALS['db']->getAll("SELECT private_key  FROM " . DB_PREFIX . "video ");

                if ($private_key_array) {

                    $a1 = array_intersect($private_key_array, $private_key_array_this);
                    while (count($a1) > 0) {
                        $data['private_key'] = rand(100000, 999999);
                        $private_key_array_this = array();
                        $private_key_array_this[] = $data['private_key'];
                        $a1 = array_intersect($private_key_array, $private_key_array_this);
                    }

                }

                $data['virtual_number'] = intval($m_config['virtual_number']);
                $data['max_robot_num'] = intval($m_config['robot_num']); //允许添加的最大机器人数;

                /*$sql = "select sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from ".DB_PREFIX."user where id = ".$user_id;
                $user = $GLOBALS['db']->getRow($sql,true,true);*/

                //图片,应该从客户端上传过来,如果没上传图片再用会员头像

                if ($live_image != '' && $live_image != './(null)') {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
                    $trans = new transport();
                    $req = $trans->request(get_spec_image($live_image), '', 'GET');
                    if (strlen($req['body']) > 1000) {
                        $data['live_image'] = $live_image;
                    } else {
                        $data['live_image'] = $user['head_image'];
                    }
                } else {
                    $data['live_image'] = $user['head_image'];
                }

                $data['head_image'] = $user['head_image'];
                $data['thumb_head_image'] = $user['thumb_head_image'];

                $data['sex'] = intval($user['sex']); //性别 0:未知, 1-男，2-女

                $data['xpoint'] = $xpoint;
                $data['ypoint'] = $ypoint;

                $data['video_type'] = intval($m_config['video_type']); //0:腾讯云互动直播;1:腾讯云直播

                if ($data['video_type'] > 0) {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
                    $video_factory = new VideoFactory();
                    $channel_info = $video_factory->Create($video_id, 'mp4', $user_id);
                    if (!empty($channel_info['video_type'])) {
                        $data['video_type'] = $channel_info['video_type'];
                    }

                    $data['channelid'] = $channel_info['channel_id'];
                    $data['push_rtmp'] = $channel_info['upstream_address'];
                    $data['play_flv'] = $channel_info['downstream_address']['flv'];
                    $data['play_rtmp'] = $channel_info['downstream_address']['rtmp'];
                    $data['play_hls'] = $channel_info['downstream_address']['hls'];

                    require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
                    $api = createTimAPI();
                    $ret = $api->group_create_group('AVChatRoom', (string) $user_id, (string) $user_id, (string) $video_id);
                    if ($ret['ActionStatus'] != 'OK') {
                        ajax_return(array(
                            'status' => 0,
                            'error' => $ret['ErrorCode'] . $ret['ErrorInfo']
                        ));
                    }

                    $data['group_id'] = $ret['GroupId'];

                }

                $data['monitor_time'] = to_date(NOW_TIME, 'Y-m-d H:i:s'); //主播心跳监听

                $data['push_url'] = ''; //video_type=1;1:腾讯云直播推流地址
                $data['play_url'] = ''; //video_type=1;1:腾讯云直播播放地址(rmtp,flv)

                $data['share_type'] = $share_type;
                $data['title'] = $title;
                $data['cate_id'] = $cate_id;
                $data['video_classified'] = $video_classified;
                $data['user_id'] = $user_id;
                $data['live_in'] = 2; //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
                $data['watch_number'] = ''; //'当前观看人数';
                $data['vote_number'] = ''; //'获得票数';
                $data['province'] = $province; //'省';
                $data['city'] = $city; //'城市';

                $data['create_time'] = NOW_TIME; //'创建时间';
                $data['begin_time'] = NOW_TIME; //'开始时间';
                $data['end_time'] = ''; //'结束时间';
                $data['is_hot'] = 1; //'1热门; 0:非热门';
                $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

                $data['online_status'] = 1; //主播在线状态;1:在线(默认); 0:离开
                $data['is_bm'] = 1; //主播在线状态;1:在线(默认); 0:离开
                $data['is_push'] = 0; //主播在线状态;1:在线(默认); 0:离开

                //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
                $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);

                $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
                $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);

                $data['sort_init'] = 200000000 + $sort_init;
                $data['sort_num'] = $data['sort_init'];

                // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
                // 2、将礼物表名称写入fanwe_video 中，需新建字段
                // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
                // 4、修改所有读取礼物表的地方，匹配数据
                $data['prop_table'] = createPropTable();
                //直播分类
                $data['classified_id'] = $video_classified;

                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
                //$video_id =  $GLOBALS['db']->insert_id();

                if ($GLOBALS['db']->affected_rows()) {
                    $return['status'] = 1;
                    $return['error'] = '';
                    $return['room_id'] = $video_id;
                    $return['video_type'] = intval($data['video_type']);
                    $return['private_key'] = $data['private_key'];

                    sync_video_to_redis($video_id, '*', false);

                } else {
                    $return['status'] = 0;
                    $return['error'] = '创建房间失败！';
                }
            } else {
                if (intval($user['is_ban'] && intval($user['ban_type'] == 0))) {
                    $return['status'] = 0;
                    $return['error'] = '请求房间id失败，您被禁播，请联系客服处理。';
                } elseif (intval($user['is_ban'] && intval($user['ban_type'] == 1))) {
                    $return['status'] = 0;
                    $return['error'] = '请求房间id失败，当前IP已被封停，请联系客服处理。';
                } elseif (intval($user['is_ban'] && intval($user['ban_type'] == 2))) {
                    $return['status'] = 0;
                    $return['error'] = '请求房间id失败，当前设备已被禁用，请联系客服处理。';
                } else {
                    $return['status'] = 0;
                    $return['error'] = '由于您的违规操作，您被封号暂时不能直播，封号时间截止到：' . to_date(intval($user['ban_time']), 'Y-m-d H:i:s') . '。';
                }

            }
        }
        if ($m_config['must_authentication'] == 1) {
            if ($is_authentication != 2) {
                $return['room_id'] = 0;
            }
        }
        //-------------------------------------
        //sdk_type 0:使用腾讯SDK、1：使用金山SDK
        //映射关系类型  腾讯云直播, 金山云，星域，千秀云 ，阿里云
        //video_type     1          2         3        4        5
        //sdk_type       0            1         -        -        -
        $return['sdk_type'] = get_sdk_info($m_config['video_type']);

        //微信分享链接、图片，内容，callback_url
        $share = array();
        $share['share_title'] = strim($m_config['share_title']); //'你丑你先睡,我美我直播!';
        $share['share_imageUrl'] = $user['head_image'];
        $share['share_key'] = $return['room_id'];
        $share['share_url'] = SITE_DOMAIN . APP_ROOT . '/wap/index.php?ctl=share&act=live&user_id=' . $return['user_id'] . '&video_id=' . $return['room_id'] . '&share_id=' . $user_id;
        $share['share_content'] = $share['share_title'] . $root['podcast']['user']['nick_name'] . '正在直播,快来一起看~';

        $return['share'] = $share;
        $return['has_room'] = 0;

        //bm_video_cstatus更新
        $status = 1;
        $room_id = intval($return['room_id']);
        //$group_id=$data['group_id'];

        $set_fields = "";
        /*if ($group_id != ''){
        $set_fields .= ",group_id='".$group_id."'";
        }*/

        if ($channelid != '') {
            $set_fields .= ",channelid = '" . $channelid . "'";
        }

        if ($play_rtmp != '') {
            $set_fields .= ",play_rtmp = '" . $play_rtmp . "'";
        }

        if ($play_flv != '') {
            $set_fields .= ",play_flv = '" . $play_flv . "'";
        }

        if ($play_hls != '') {
            $set_fields .= ",play_hls = '" . $play_hls . "'";
        }

        $sql = "update " . DB_PREFIX . "video set live_in = 1 " . $set_fields . " where live_in =2 and id = " . $room_id . " and user_id = " . $user_id;
        $GLOBALS['db']->query($sql);

        //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        if ($GLOBALS['db']->affected_rows()) {

            $sql = "select user_id,room_type,title,city,cate_id from " . DB_PREFIX . "video where id = " . $room_id;
            $video = $GLOBALS['db']->getRow($sql);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video_redis->video_online($room_id, $group_id);
            //将mysql数据,同步一份到redis中
            sync_video_to_redis($room_id, '*', false);

            if ($video['cate_id'] > 0) {
                $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)";
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                if ((defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                    $sql .= " and b.province <> '千秀' and b.province <>''";
                }
                $sql .= ") where a.id = " . $video['cate_id'];
                $GLOBALS['db']->query($sql);
            }

            //
            if ($video['room_type'] == 3) {
                crontab_robot($room_id);
            }

        }

        ajax_return($return);
    }
    //

    //
    public function bm_video_cstatus()
    {
        $root = array();

        //$GLOBALS['user_info']['id'] = 1;

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $room_id = strim($_REQUEST['room_id']); //房间号id
            $status = intval($_REQUEST['status']); //status: 1:成功,其它用户可以开始加入;0:创建失败; 2:主播离开; 3:主播回来

            //当$status=2,3时，下面3个参数可以不用传;
            $channelid = strim($_REQUEST['channelid']); //旁路直播,频道ID
            $play_rtmp = strim($_REQUEST['play_rtmp']); //旁路直播,播放地址
            $play_flv = strim($_REQUEST['play_flv']); //旁路直播,播放地址
            $play_hls = strim($_REQUEST['play_hls']); //旁路直播,播放地址
            //在返回的hls地址中，加入/live/这一层
            //@author　jiangzuru
            $s1 = $play_hls;
            if ($s1 && strpos($s1, "com/live/") === false) {
                $pos1 = strpos($s1, "com/");
                $play_hls = substr_replace($s1, "live/", $pos1 + 4, 0);
            }

            $group_id = strim($_REQUEST['group_id']); //group_id; Private,Public,ChatRoom,AVChatRoom
            //$room_type = intval($_REQUEST['room_type']);//房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();

            if ($status == 2 || $status == 3) {
                //online_status 主播在线状态;1:在线(默认); 0:离开
                if ($status == 2) {
                    $sql = "update " . DB_PREFIX . "video set online_status = 0 where id = " . $room_id . " and user_id = " . $user_id;
                } else {
                    $sql = "update " . DB_PREFIX . "video set online_status = 1 where id = " . $room_id . " and user_id = " . $user_id;
                }

                $GLOBALS['db']->query($sql);
                if ($GLOBALS['db']->affected_rows()) {
                    $root['status'] = 1;

                    sync_video_to_redis($room_id, 'online_status', false);

                } else {
                    $root['status'] = 0;
                }
            } else {
                $set_fields = "";
                if ($group_id != '') {
                    $set_fields .= ",group_id='" . $group_id . "'";
                }

                if ($channelid != '') {
                    $set_fields .= ",channelid = '" . $channelid . "'";
                }

                if ($play_rtmp != '') {
                    $set_fields .= ",play_rtmp = '" . $play_rtmp . "'";
                }

                if ($play_flv != '') {
                    $set_fields .= ",play_flv = '" . $play_flv . "'";
                }

                if ($play_hls != '') {
                    $set_fields .= ",play_hls = '" . $play_hls . "'";
                }

                $sql = "update " . DB_PREFIX . "video set live_in = 1 " . $set_fields . " where live_in =2 and id = " . $room_id . " and user_id = " . $user_id;
                $GLOBALS['db']->query($sql);

                //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
                if ($GLOBALS['db']->affected_rows()) {

                    $sql = "select user_id,room_type,title,city,cate_id from " . DB_PREFIX . "video where id = " . $room_id;
                    $video = $GLOBALS['db']->getRow($sql);

                    $video_redis->video_online($room_id, $group_id);
                    //将mysql数据,同步一份到redis中
                    sync_video_to_redis($room_id, '*', false);

                    if ($video['cate_id'] > 0) {
                        $sql = "update " . DB_PREFIX . "video_cate a set a.num = (select count(*) from " . DB_PREFIX . "video b where b.cate_id = a.id and b.live_in in (1,3)";
                        $m_config = load_auto_cache("m_config"); //初始化手机端配置
                        if ((defined('OPEN_ROOM_HIDE') && OPEN_ROOM_HIDE == 1) && intval($m_config['open_room_hide']) == 1) {
                            $sql .= " and b.province <> '千秀' and b.province <>''";
                        }
                        $sql .= ") where a.id = " . $video['cate_id'];
                        $GLOBALS['db']->query($sql);
                    }

                    //
                    if ($video['room_type'] == 3) {
                        crontab_robot($room_id);
                    }

                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_data = $user_redis->getRow_db($user_id, array('id', 'nick_name', 'head_image'));
                    $pushdata = array(
                        'user_id' => $user_id, //'主播ID',
                        'nick_name' => $user_data['nick_name'], //'主播昵称',
                        'create_time' => NOW_TIME, //'创建时间',
                        'cate_title' => $video['title'], // '直播主题',
                        'room_id' => $room_id, // '房间ID',
                        'city' => $video['city'], // '直播城市地址',
                        'head_image' => get_spec_image($user_data['head_image']),
                        'status' => 0 //'推送状态(0:未推送，1：推送中；2：已推送）'
                    );
                    $m_config = load_auto_cache("m_config");
                    if (intval($m_config['service_push'])) {
                        $pushdata['pust_type'] = 1; //'推送状态(0:粉丝推送，1：全服推送）';
                    } else {
                        $pushdata['pust_type'] = 0; //'推送状态(0:粉丝推送，1：全服推送）';
                    }

                    $GLOBALS['db']->autoExecute(DB_PREFIX . "push_anchor", $pushdata, 'INSERT');

                    $root['status'] = 1;
                } else {
                    $sql = "update " . DB_PREFIX . "video set live_in = 0" . $set_fields . ", end_time = " . NOW_TIME . ", is_delete = 1 where live_in =2 and id = " . $room_id . " and user_id = " . $user_id;
                    $GLOBALS['db']->query($sql);

                    if ($GLOBALS['db']->affected_rows()) {
                        $root['status'] = 1;

                        //将mysql数据,同步一份到redis中
                        sync_video_to_redis($room_id, '*', false);

                    } else {
                        $root['status'] = 0;
                    }
                }

            }

        }

        ajax_return($root);
    }

    /**
     * 直播结束
     */
    public function bm_end_video()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $is_out = intval($_REQUEST['is_out']);
            $user_id = intval($GLOBALS['user_info']['id']);
            $room_id = strim($_REQUEST['room_id']); //房间号id
            $video_vid = strim($_REQUEST['video_url']); //视频地址

            if ($is_out == 1) {
                //主播离开
                //广播：直播结束
                $ext = array();
                $ext['type'] = 3; //0:普通消息;1:礼物;2:弹幕消息;3:主播退出;4:禁言;5:观众进入房间；6：观众退出房间；7:直播结束
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['show_num'] = 0; //观看人数
                $ext['fonts_color'] = ''; //字体颜色
                $ext['desc'] = '主播退出'; //弹幕消息;
                $ext['desc2'] = '主播退出'; //弹幕消息;

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem', //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => ''
                    )
                );
                $root['status'] = 1;
                ajax_return($root);
            }

            if ($video_vid == 'null') {
                $video_vid = '';
            }

            //$root['error'] = $video_vid;
            $sql = "";
            if (OPEN_PAI_MODULE == 1) {
                $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,pai_id from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id;

            } else {
                $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id;

            }
            $video = $GLOBALS['db']->getRow($sql, true, true);

            //只有主播自己能结束
            if ($user_id == $video['user_id']) {
                do_end_video($video, $video_vid, 0, $video['cate_id']);

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                $video_redis = new VideoRedisService();

                $root['watch_number'] = intval($video['max_watch_number']);
                $root['vote_number'] = intval($video['vote_number']) + intval($video_redis->getOne_db($video['id'], 'game_vote_number')); //获得秀票

                $time_len = NOW_TIME - $video['begin_time']; //私有聊天或小于5分钟的视频，不保存
                $m_config = load_auto_cache("m_config");
                $short_video_time = $m_config['short_video_time'] ? $m_config['short_video_time'] : 300;

                if ($video['room_type'] == 1 || $time_len < $short_video_time) {

                    $root['has_delvideo'] = 0; //1：显示删除视频按钮; 0:不显示；

                } else {
                    $root['has_delvideo'] = 1; //1：显示删除视频按钮; 0:不显示；
                }

            }
            rm_auto_cache("select_video");
            $root['status'] = 1;
        }

        ajax_return($root);
    }

    public function get_video2()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            //客服端手机类型dev_type=android;dev_type=ios
            $dev_type = strim($_REQUEST['sdk_type']);
            if (($dev_type == 'ios' || $dev_type == 'android')) {
                $room_id = intval($_REQUEST['room_id']); //房间号id; 如果有的话，则返回当前房间信息;
                $user_id = intval($GLOBALS['user_info']['id']); //用户ID
                $type = intval($_REQUEST['type']); //type: 0:热门;1:最新;2:关注 [随机返回一个type类型下的直播]

                //强制升级不升级无法查看直播
                $status = 1;
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                if (intval($m_config['forced_upgrade'])) {
                    $root = $this->compel_upgrade($m_config);
                    $status = $root['status'];
                }
                if ($status == 1) {
                    $root = get_video_info2($room_id, $user_id, $type, $_REQUEST);

                    if ($root['live_in'] == 1 && $root['user_id'] == $user_id) {
                        //主播重新进入自己的房间后，重新推一下：连麦观众消息
                        //$this->push_lianmai($room_id);
                    }
                }

            }
        }
        $root['is_bm'] = 1;
        ajax_return($root);
    }

    //开始推送
    public function starts_push()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $room_id = intval($_REQUEST['room_id']);
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $video = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id);
            $sql = "update " . DB_PREFIX . "video set is_push = 1 where id = " . $room_id . " and user_id = " . $user_id;
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                $root['status'] = 1;

                //将mysql数据,同步一份到redis中
                sync_video_to_redis($room_id, '*', false);

                $ext = array();
                $ext['type'] = 44;
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['fonts_color'] = ''; //字体颜色
                $ext['is_push'] = 1; //是否推送
                $ext['desc'] = '主播开始直播'; //弹幕消息;
                $ext['desc2'] = '主播开始直播'; //弹幕消息;

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem', //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => ''
                    )
                );

                //将创建的元素$msg_content_elem, 加入array $msg_content
                array_push($msg_content, $msg_content_elem);
                //log_result($msg_content);
                //发送广播：直播结束
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->group_send_group_msg2($user_id, $video['group_id'], $msg_content);
                //log_result($ret);
            }
        }

        ajax_return($root);
    }

    //结束推送
    public function end_push()
    {
        $root = array();

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $room_id = intval($_REQUEST['room_id']);
            $user_id = intval($GLOBALS['user_info']['id']); //用户ID
            $video = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id);
            $sql = "update " . DB_PREFIX . "video set is_push = 0 where id = " . $room_id . " and user_id = " . $user_id;
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                $root['status'] = 1;

                //将mysql数据,同步一份到redis中
                sync_video_to_redis($room_id, '*', false);

                $ext = array();
                $ext['type'] = 44;
                $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
                $ext['fonts_color'] = ''; //字体颜色
                $ext['is_push'] = 0; //是否推送
                $ext['desc'] = '主播关闭直播'; //弹幕消息;
                $ext['desc2'] = '主播关闭直播'; //弹幕消息;

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem', //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                        'Desc' => ''
                    )
                );

                //将创建的元素$msg_content_elem, 加入array $msg_content
                array_push($msg_content, $msg_content_elem);
                //log_result($msg_content);
                //发送广播：直播结束
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->group_send_group_msg2($user_id, $video['group_id'], $msg_content);
                //log_result($ret);
            }

        }

        ajax_return($root);
    }

    //开始推送
    public function is_push()
    {
        $is_push = intval($_REQUEST['is_push']);
        //log_result($is_push);
        if ($is_push == 1) {
            $this->starts_push();
        } else {
            $this->end_push();
        }

    }

}
