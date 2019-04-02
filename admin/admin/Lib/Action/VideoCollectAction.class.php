<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class VideoCollectAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/admin/Lib/Action/VideoCommonAction.class.php";
        require_once APP_ROOT_PATH . "/admin/Lib/Action/UserCommonAction.class.php";
    }

    /**
     * 视频采集
     *
     */
    public function index()
    {
        $url = 'http://1hph.cn/xyjk.html';
        if (empty(S('data'))) {
            header("Content-type: text/json; charset=utf-8");
            include 'JSON.php';//https://github.com/pear/Services_JSON
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
            $data = $this->getAction($url);
            $d = $json->decode($data);
            S('data', $d, '3600');
        } else {
            $d = S('data');
        }
        array_pop($d['data']);
        $this->assign('url', $url);
        $this->assign('data', $d['data']);
        $this->display();
    }

    //详细页
    public function two()
    {
        header("Content-type: text/json; charset=utf-8");
        $new_url = 'http://1hph.cn' . $_GET['url'];
        if (empty(S($_GET['url']))) {
            $data2 = $this->getAction($new_url);
            $data2 = substr($data2, 3);//需要去BOM掉头
            $d2 = json_decode($data2, true);
            S($_GET['url'], $d2, '3600');
        } else {
            $d2 = S($_GET['url']);
        }
        $this->assign('data', $d2['data']);
        $this->display();
    }

    //加入直播
    public function add_video()
    {
        $m_config = load_auto_cache("m_config");
        //增加虚拟会员
        $userRobot = array();
        $userRobot['nick_name'] = $_REQUEST['nickname'];
        $userRobot['head_image'] = $_REQUEST['logourl'];
        $userRobot['is_admin'] = "0";
        $userRobot['mobile'] = "";
        $userRobot['province'] = "";
        $userRobot["city"] = "";
        $userRobot["sex"] = "1";
        $userRobot['user_level'] = "1";
        $userRobot['signature'] = "";
        $userRobot['is_effect'] = "1";
        $userRobot['is_ban'] = "0";
        $userRobot["ban_time"] = "";
        $userRobot["is_authentication"] = "0";
        $userRobot["authentication_type"] = "0";
        $userRobot["v_explain"] = "0";
        $userRobot['identify_positive_image'] = "";
        $userRobot["identify_nagative_image"] = "";
        $userRobot["identify_hold_image"] = "";
        $userRobot["identify_number"] = "";
        $userRobot['member_type'] = "1";
        $userRobot['is_robot'] = "1";
        $userRobot['v_icon'] = "";
        $userRobot["score"] = "10";

        $common = new UserCommon();
        filter_request($userRobot);
        $res = save_user($userRobot, 'INSERT', $update_status = 1);
        $user_id = intval($res['data']);

        //添加采集流
        $sql = "select id,video_type from " . DB_PREFIX . "video where live_in =2 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {

            //更新心跳时间，免得被删除了
            $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $this->success("加入成功");
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
        $list = $GLOBALS['db']->getAll($sql, true, true);
        foreach ($list as $k => $v) {
            //结束直播
            do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
        }

        require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
        $video_id = get_max_room_id(0);
        $data = array();
        $data['id'] = $video_id;
        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        $data['room_type'] = 3;

        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

        //图片,应该从客户端上传过来,如果没上传图片再用会员头像

        $data['head_image'] = $_REQUEST['logourl'];
        $data['thumb_head_image'] = $_REQUEST['logourl'];
        $data['sex'] = "2";//性别 0:未知, 1-男，2-女
        $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

        if ($data['video_type'] > 0) {
            require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
            if ($ret['ActionStatus'] != 'OK') {
                $this->error("加入失败[" . $ret['ErrorCode'] . $ret['ErrorInfo'] . "]");
            }

            $data['group_id'] = $ret['GroupId'];

            /*fanwe_require(APP_ROOT_PATH.'mapi/lib/core/video_factory.php');
            $video_factory = new VideoFactory();
            $channel_info = $video_factory->Create($video_id,'mp4',$user_id);
            if(! empty($channel_info['video_type'])) {
                $data['video_type'] = $channel_info['video_type'];
            }

            $data['channelid'] = $channel_info['channel_id'];
            $data['push_rtmp'] = $channel_info['upstream_address'];
            $data['play_flv'] = $channel_info['downstream_address']['flv'];
            $data['play_rtmp'] = $channel_info['downstream_address']['rtmp'];
            $data['play_hls'] = $channel_info['downstream_address']['hls'];*/

        }

        $data['monitor_time'] = to_date(NOW_TIME, 'Y-m-d H:i:s');//主播心跳监听
        $data['title'] = $_REQUEST['nickname'];
        $data['cate_id'] = 1;
        $data['user_id'] = $user_id;
        $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = '';//'当前观看人数';
        $data['vote_number'] = '';//'获得票数';
        $data['province'] = "火星";//'省';
        $data['city'] = "火星";//'城市';
        $data['xpoint'] = "";
        $data['ypoint'] = "";

        $data['create_time'] = NOW_TIME;//'创建时间';
        $data['begin_time'] = NOW_TIME;//'开始时间';
        $data['end_time'] = '';//'结束时间';
        $data['is_hot'] = 1;//'1热门; 0:非热门';
        $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

        //sort_init(初始排序权重) = (用户可提现印票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留印票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
        $sort_init = 0;

        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];


        // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
        // 2、将礼物表名称写入fanwe_video 中，需新建字段
        // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
        // 4、修改所有读取礼物表的地方，匹配数据
        $data['prop_table'] = createPropTable();
        //直播分类
        $data['classified_id'] = 8;
        if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && intval($m_config['switch_public_pay']) == 1 && intval($m_config['public_pay']) > 0) {
            $data['is_live_pay'] = 1;
            $data['live_pay_type'] = 1;
            $data['public_screen'] = 1;
            $data['live_fee'] = intval($m_config['public_pay']);
            $data['live_pay_time'] = intval(NOW_TIME);
        }

        $data['is_live_pay'] = $_REQUEST['is_live_pay'];
        $data['live_pay_type'] = $_REQUEST['live_pay_type'];
        $data['live_fee'] = $_REQUEST['live_fee'];
        $data['live_pay_time'] = intval(NOW_TIME);

        $play_url = $_REQUEST['url'];
        //判断流媒体格式
        //rtmp格式
        if (preg_match("/^rtmp:/", $play_url)) {
            //RTMP格式
            $data['play_rtmp'] = $play_url;
        }
        if (preg_match("/flv$/", $play_url)) {
            //FLV
            $data['play_flv'] = $play_url;
        }
        if (preg_match("/mp4$/", $play_url)) {
            //MP4
            $data['play_mp4'] = $play_url;
        }
        if (preg_match("/m3u8$/", $play_url)) {
            //HLS 仅此格式能播放
            $data['play_hls'] = $play_url;
        }

        $data['channelid'] = "gather";

        $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
        //$video_id =  $GLOBALS['db']->insert_id();

        if ($GLOBALS['db']->affected_rows()) {
            sync_video_to_redis($video_id, '*', false);
            $this->success("加入成功");
        } else {
            $this->error("加入失败");
        }
    }

    //批量循环加入直播
    public function addall_video()
    {

        $m_config = load_auto_cache("m_config");
        header("Content-type: text/json; charset=utf-8");
        $new_url = 'http://1hph.cn' . $_GET['url'];
        if (empty(S($_GET['url']))) {
            $data2 = $this->getAction($new_url);
            $data2 = substr($data2, 3);//需要去BOM掉头
            $d2 = json_decode($data2, true);
            S($_GET['url'], $d2, '3600');
        } else {
            $d2 = S($_GET['url']);
        }
        $i = 0;
        foreach ($d2['data'] as $newArray) {

            $play_url = $newArray['play_url'];
            //判断流媒体格式 不支持的格式直接过滤
            if (!preg_match("/flv$/", $play_url)) {
                continue;
            }


            //增加虚拟会员
            $userRobot = array();
            $userRobot['nick_name'] = $newArray['nickname'];
            $userRobot['head_image'] = $newArray['logourl'];
            $userRobot['is_admin'] = "0";
            $userRobot['mobile'] = "";
            $userRobot['province'] = "";
            $userRobot["city"] = "";
            $userRobot["sex"] = "1";
            $userRobot['user_level'] = "1";
            $userRobot['signature'] = "";
            $userRobot['is_effect'] = "1";
            $userRobot['is_ban'] = "0";
            $userRobot["ban_time"] = "";
            $userRobot["is_authentication"] = "0";
            $userRobot["authentication_type"] = "0";
            $userRobot["v_explain"] = "0";
            $userRobot['identify_positive_image'] = "";
            $userRobot["identify_nagative_image"] = "";
            $userRobot["identify_hold_image"] = "";
            $userRobot["identify_number"] = "";
            $userRobot['member_type'] = "1";
            $userRobot['is_robot'] = "1";
            $userRobot['v_icon'] = "";
            $userRobot["score"] = "10";

            $common = new UserCommon();
            filter_request($userRobot);
            $res = save_user($userRobot, 'INSERT', $update_status = 1);
            $user_id = intval($res['data']);

            //添加采集流
            $sql = "select id,video_type from " . DB_PREFIX . "video where live_in =2 and user_id = " . $user_id;
            $video = $GLOBALS['db']->getRow($sql, true, true);
            if ($video) {

                //更新心跳时间，免得被删除了
                $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
                $GLOBALS['db']->query($sql);

                if ($GLOBALS['db']->affected_rows()) {
                    //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                    $this->success("加入成功");
                }
            }

            //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
            /*$sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as $k => $v) {
                //结束直播
                do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
            }*/

            require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
            $video_id = get_max_room_id(0);
            $data = array();
            $data['id'] = $video_id;
            //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
            $data['room_type'] = 3;

            $data['virtual_number'] = intval($m_config['virtual_number']);
            $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

            //图片,应该从客户端上传过来,如果没上传图片再用会员头像

            $data['head_image'] = $newArray['logourl'];
            $data['thumb_head_image'] = $newArray['logourl'];
            $data['sex'] = "2";//性别 0:未知, 1-男，2-女
            $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

            if ($data['video_type'] > 0) {
                require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
                if ($ret['ActionStatus'] != 'OK') {
                    $this->error("加入失败[" . $ret['ErrorCode'] . $ret['ErrorInfo'] . "]");
                }

                $data['group_id'] = $ret['GroupId'];

            }

            $data['monitor_time'] = to_date(NOW_TIME, 'Y-m-d H:i:s');//主播心跳监听
            $data['title'] = $newArray['nickname'];
            $data['cate_id'] = 1;
            $data['user_id'] = $user_id;
            $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
            $data['watch_number'] = '';//'当前观看人数';
            $data['vote_number'] = '';//'获得票数';
            $data['province'] = "火星";//'省';
            $data['city'] = "火星";//'城市';
            $data['xpoint'] = "";
            $data['ypoint'] = "";

            $data['create_time'] = NOW_TIME;//'创建时间';
            $data['begin_time'] = NOW_TIME;//'开始时间';
            $data['end_time'] = '';//'结束时间';
            $data['is_hot'] = 1;//'1热门; 0:非热门';
            $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

            $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

            //sort_init(初始排序权重) = (用户可提现印票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留印票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
            $sort_init = $i;

            $data['sort_init'] = 200000000 + $sort_init;
            $data['sort_num'] = $data['sort_init'];


            // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
            // 2、将礼物表名称写入fanwe_video 中，需新建字段
            // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
            // 4、修改所有读取礼物表的地方，匹配数据
            $data['prop_table'] = createPropTable();
            //直播分类
            $data['classified_id'] = 8;
            if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && intval($m_config['switch_public_pay']) == 1 && intval($m_config['public_pay']) > 0) {
                $data['is_live_pay'] = 1;
                $data['live_pay_type'] = 1;
                $data['public_screen'] = 1;
                $data['live_fee'] = intval($m_config['public_pay']);
                $data['live_pay_time'] = intval(NOW_TIME);
            }

            $data['channelid'] = "gather";
            $data['play_flv'] = $play_url;

            $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
            //$video_id =  $GLOBALS['db']->insert_id();

            if ($GLOBALS['db']->affected_rows()) {
                sync_video_to_redis($video_id, '*', false);
                //$this->success("加入成功");
            } else {
                $this->error("加入失败");
            }
            $i = $i + 1;
            //echo $i;
        }
        $this->success("批量加入成功");
    }

    public function change_roomtype()
    {
        $this->display();
    }

    //切换付费直播
    public function live_pay()
    {
        $m_config = load_auto_cache("m_config");//初始化手机端配置
        if (!isset($m_config)) {
            $this->error("初始化手机端配置错误");
        }

        $_REQUEST['user_id'] = 164762;
        $_REQUEST['room_id'] = 22129;
        $_REQUEST['live_fee'] = 5;
        $_REQUEST['live_pay_type'] = 1;
        $_REQUEST['is_mention'] = 0;

        $user_id = intval($_REQUEST['user_id']);//用户ID
        $room_id = intval($_REQUEST['room_id']);//直播ID 也是room_id
        $live_fee = intval($_REQUEST['live_fee']);//直播收取的费用 （钻石/分钟）
        $pay_type = intval($_REQUEST['live_pay_type']);//收费类型 0按时收费，1按场次收费
        //提档
        $is_mention = intval($_REQUEST['is_mention']);//提档 0不提档 1 提档
        //按时
        $live_pay_max = intval($m_config['live_pay_max']);//付费直播收费最高
        $live_pay_min = intval($m_config['live_pay_min']);//付费直播收费最低
        //按场
        $live_pay_scene_max = intval($m_config['live_pay_scene_max']);//付费直播收费最高
        $live_pay_scene_min = intval($m_config['live_pay_scene_min']);//付费直播收费最低

        if ($pay_type == 0 && (defined('LIVE_PAY') && LIVE_PAY == 0)) {
            $this->error("按时付费未开启");
        }
        if ($pay_type == 1 && (defined('LIVE_PAY_SCENE') && LIVE_PAY_SCENE == 0)) {
            $this->error("按场付费未开启");
        }

        if ($pay_type == 0 && $live_pay_max < $live_fee && $is_mention == 0 && $live_pay_max > 0) {
            $this->error("按时收费不能高于" . $live_pay_max . $m_config['diamonds_name']);
        }
        if ($pay_type == 0 && $live_pay_min > $live_fee && $is_mention == 0) {
            $this->error("按时收费不能低于" . $live_pay_min . $m_config['diamonds_name']);
        }

        if ($pay_type == 1 && $live_pay_scene_max < $live_fee && $is_mention == 0 && $live_pay_scene_max > 0) {
            $this->error("按场收费不能高于" . $live_pay_scene_max . $m_config['diamonds_name']);
        }
        if ($pay_type == 1 && $live_pay_scene_min > $live_fee && $is_mention == 0) {
            $this->error("按场收费不能低于" . $live_pay_scene_min . $m_config['diamonds_name']);
        }

        //判断付费是否开启
        $pay_info = $this->get_pay_info();
        if ($pay_type == 0 && intval($pay_info['live_pay']) == 0) {
            $this->error("按时付费未开启");
        }
        if ($pay_type == 1 && intval($pay_info['live_pay_scene']) == 0) {
            $this->error("按场付费未开启");
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $fields = array('group_id', 'live_fee', 'live_is_mention', 'is_live_pay', 'user_id');
        $video_info = $video_redis->getRow_db($room_id, $fields);
        $live_pay_type = intval($video_info['live_pay_type']);
        //实际付费人数
        if ($live_pay_type == 0) {
            $times = get_gmtime() - 60;
            $sql = "select count(*) from " . DB_PREFIX . "live_pay_log where video_id =" . $room_id . " and pay_time_end>=" . $times;
            $live_viewer = $GLOBALS['db']->getOne($sql, true, true);
        } else {
            $live_viewer = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "live_pay_log where video_id =" . $room_id, true, true);
        }
        $group_id = $video_info['group_id'];

        if (!$room_id) {
            $this->error("直播ID不存在");
        }
        $m_config['live_count_down'] = intval($m_config['live_count_down']) ? intval($m_config['live_count_down']) : 120;
        $live_pay_time = intval(NOW_TIME + $m_config['live_count_down']);
        //加入直播意外终止的问题
        $p_user_id = $video_info['user_id'];
        //获取支付信息
        $sql = "select id,user_id,live_pay_type,live_fee from " . DB_PREFIX . "video  where user_id = " . $p_user_id . " and live_pay_type=" . $pay_type . "  and is_live_pay =1 and is_aborted = 1 and live_in !=3";
        $live_old_info = $GLOBALS['db']->getRow($sql);
        $live_old_id = intval($live_old_info['id']);//被服务器异常终止结束(主要是心跳超时)
        $pay_room_id = 0;
        if ($live_old_id > 0) {
            $pay_room_id = $live_old_id;
        }
        //提档流程
        if ($is_mention == 1 && $pay_type == 0) {
            if (intval($video_info['is_live_pay']) == 1 && intval($video_info['live_fee']) > 0 && intval($video_info['live_fee']) == 0) {
                $this->error("切换失败");
            }
            if (intval($m_config['live_pay_fee']) == 0) {
                $this->error("提档参数不存在");
            }

            $live_fee = intval($m_config['live_pay_fee'] + $video_info['live_fee']);
            //更新付费信息
            $sql = "update " . DB_PREFIX . "video set live_is_mention =1,live_fee = " . $live_fee . ",live_pay_type = " . $pay_type . ",pay_room_id = " . $pay_room_id . " where live_pay_type=0 and is_live_pay =1 and live_in =1 and id = " . $room_id . " and user_id = " . $user_id;
        } else {
            if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
            } else {
                if (intval($video_info['is_live_pay']) == 1 && intval($video_info['live_fee']) > 0) {
                    $this->error("切换失败");
                }
            }
            if ((defined('PUBLIC_PAY') && PUBLIC_PAY == 1) && $m_config['switch_public_pay'] == 1 && $m_config['public_pay'] > 0) {
                $sql = "update " . DB_PREFIX . "video set live_fee = '" . $live_fee . "',public_screen = 0,live_pay_type = '" . $pay_type . "',is_live_pay =1,live_pay_time = '" . $live_pay_time . "',pay_room_id = " . $pay_room_id . " where is_live_pay =1 and  live_in =1 and public_screen=1 and id = " . $room_id . " and user_id = " . $user_id;
                $public_screen = $GLOBALS['db']->getOne("SELECT public_screen FROM  " . DB_PREFIX . "video WHERE user_id=" . $user_id . " and live_in=1");
            } else {
                $sql = "update " . DB_PREFIX . "video set live_fee = '" . $live_fee . "',live_pay_type = '" . $pay_type . "',is_live_pay =1,live_pay_time = '" . $live_pay_time . "',pay_room_id = " . $pay_room_id . " where is_live_pay =0 and  live_in =1 and id = " . $room_id . " and user_id = " . $user_id;
            }
        }
        $GLOBALS['db']->query($sql);
        if ($GLOBALS['db']->affected_rows()) {
            $data['live_fee'] = $live_fee;
            $data['live_viewer'] = intval($live_viewer);
            $data['pay_type'] = $pay_type;
            $data['count_down'] = $m_config['live_count_down'];
            if ($is_mention) {
                $data['count_down'] = 0;
            }
            $root = $data;
            $root['status'] = 1;
            $root['error'] = '';
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
            sync_video_to_redis($room_id, '*', false);
            if ($is_mention == 0) {
                //=========================================================
                //广播：开始进入收费直播
                //发送广播：收费直播
                fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ext = array();
                if ($pay_type == 1) {
                    $ext['type'] = 40; //40：按场收费直播（全体推送的，用于通知用户即将进入收费直播）
                } else {
                    $ext['type'] = 32; //32：按时收费直播（全体推送的，用于通知用户即将进入收费直播）
                }
                $ext['room_id'] = $room_id;//直播ID 也是room_id;只有与当前房间相同时，收到消息才响应

                #构造高级接口所需参数
                $msg_content = array();
                //创建array 所需元素
                $msg_content_elem = array(
                    'MsgType' => 'TIMCustomElem',       //自定义类型
                    'MsgContent' => array(
                        'Data' => json_encode($ext),
                    )
                );
                array_push($msg_content, $msg_content_elem);
                //32：收费直播
                //$api->group_send_group_msg2($group_id, $msg_content);
                //$api->openim_push($user_id, $msg_content,0);
                $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                //=========================================================
                if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
                    //10002 系统错误，请再次尝试或联系技术客服。
                    log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
                    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
                }

                if ($ret['ActionStatus'] == 'FAIL') {
                    $this->error($ret['ErrorInfo'] . ":" . $ret['ErrorCode']);
                } else {
                    if ($pay_type == 1) {
                        $this->success('已切换为付费直播，' . $live_fee . $m_config['diamonds_name'] . '/场');
                    } else {
                        $this->success('已切换为付费直播，' . $live_fee . $m_config['diamonds_name'] . '/分钟');
                    }

                }
            } else {
                if ($pay_type == 0 && $is_mention == 1) {
                    $this->success('提档成功，' . $live_fee . $m_config['diamonds_name'] . '/分钟');
                }
            }
        } else {
            $this->error('切换失败');
        }
    }

    //已添加的视频源
    public function add_list()
    {
        import('ORG.Util.Page');
        $data = M('collect_data_add')->select();
        $count = M('collect_data_add')->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = M('collect_data_add')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('data', $list);
        $this->assign('page', $show);;
        $this->display();

    }

    public function video_delete()
    {
        if ($_GET['id']) {
            if (M('collect_data_add')->delete($_GET['id'])) {
                $this->success(L("删除成功"));
            }
        }
    }

    //数据添加
    public function add()
    {
        import('ORG.Net.UploadFile');
        if ($_POST) {
            $d['name'] = $_POST['name'];
            $d['url'] = $_POST['url'];
            $upload = new UploadFile();
            $upload->maxSize = 3145728;
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->savePath = './Public/Uploads/';
            if (!$upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getErrorMsg());
            } else {// 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                $d['img'] = "/Public/Uploads/" . $info[0]['savename'];
                if (M()->execute("insert into fanwe_collect_data_add(name,img,url) VALUE ('$d[name]','$d[img]','$d[url]')")) {
                    $this->success(L("添加成功"));
                }
            }
        }
        $this->display();
    }


    public function getAction($url = '')
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;

    }

    public function get_pay_info(){
        //付费开关
        $live_pay_info= $GLOBALS['db']->getAll("SELECT id,class FROM ".DB_PREFIX."plugin WHERE is_effect=1 and type = 1");
        $live_pay = array();
        if($live_pay_info){
            foreach($live_pay_info as $k=>$v){
                $live_pay[$v['class']] = $v['id'];
            }
        }
        return $live_pay;
    }
}

?>