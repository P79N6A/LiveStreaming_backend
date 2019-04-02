<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 11:07
 */
class obsCModule extends baseModule
{
    /*推流工具API--会员登录验证*/
    public function verify()
    {
        $root = array();
        $user_id = intval($_REQUEST['user_name']);
        $user_pwd = trim($_REQUEST['user_pwd']);
        if ($GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "user where id={$user_id} and user_pwd='{$user_pwd}'",
                true, true) > 0
        ) {
            $root['status'] = 1;
        } else {
            $root['status'] = -1;
            $root['error'] = '用户验证不通过';
        }
        api_ajax_return($root);
    }

    public function login()
    {
        $id = intval($_REQUEST['id']);
        $pwd = intval($_REQUEST['pwd']);

        if (!$id) {
            api_ajax_return(array('status' => -1, 'error' => '参数不正确 id or pwd'));
        }

        $user_pwd = md5($pwd);
        $user = $GLOBALS['db']->getRow("SELECT id FROM " . DB_PREFIX . "user where id={$id} and user_pwd='{$user_pwd}'");
        if (empty($user)) {
            api_ajax_return(array('status' => 0, 'error' => '用户ID或密码不正确'));
        }

        $user_sig = load_auto_cache("usersig", array("id" => $user['id']));
        api_ajax_return(array('status' => 1, 'data' => array('sig' => $user_sig['usersig'])));
    }

    /*推流工具API--开始串流验证*/
    public function create()
    {
        $root = array();
        $user_id = intval($_REQUEST['user_name']);
        $user_pwd = trim($_REQUEST['user_pwd']);
        if (empty($user_id) || empty($user_pwd)) {
            $root['status'] = -1;
            $root['error'] = '用户验证不通过';
            api_ajax_return($root);
        }
        $verify = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "user where id={$user_id} and user_pwd='{$user_pwd}'",
            true, true);
        if (!$verify) {
            $root['status'] = -1;
            $root['error'] = '用户验证不通过';
            api_ajax_return($root);
        }
        $user = $GLOBALS['db']->getRow("select is_ban,ban_time,is_effect,is_authentication,is_agree from " . DB_PREFIX . "user where id = " . $user_id,
            true, true);
        if (intval($user['is_effect']) == 0) {
            $root = array(
                'status' => 0,
                'error' => '请求房间id失败，您被禁播，请联系客服处理。'
            );
            api_ajax_return($root);
        }
        if (intval($user['is_ban']) != 0 || intval($user['ban_time']) >= get_gmtime()) {
            $root = array('status' => 0);
            if (intval($user['is_ban'])) {
                $root['error'] = '请求房间id失败，您被禁播，请联系客服处理。';
            } else {
                $root['error'] = '由于您的违规操作，您被封号暂时不能直播，封号时间截止到：' . to_date(intval($user['ban_time']),
                        'Y-m-d H:i:s') . '。';
            }
            api_ajax_return($root);
        }
        //开启强制认证
        $m_config = load_auto_cache("m_config");
        //if ($m_config['must_authentication'] == 1) {
        if ($user['is_authentication'] != 2) { // 未认证
            api_ajax_return(array(
                "status" => -2,
                "error" => "主播请先认证",
                "need_authorized" => true,
                'auth_url' => get_domain() . url('user#userinfo')
            ));
        }
        // }
        $title = strim(str_replace('#', '', $_REQUEST['title']));
        if (!$title) {
            $title = "我要直播";
        }
        $cate_id = intval($_REQUEST['cate_id']);
        $location_switch = intval($_REQUEST['location_switch']);//1-上传当前城市名称
        $province = strim($_REQUEST['province']);//省
        $city = strim($_REQUEST['city']);//市
        $is_private = intval($_REQUEST['is_private']);//1：私密聊天; 0:公共聊天
        $share_type = strtolower(strim($_REQUEST['share_type']));//WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,EMAIL,SMS,SINA
        if ($share_type == 'null') {
            $share_type = '';
        }

        //检查话题长度
        if (strlen($title) > 60) {
            $return['error'] = "话题太长";
            $return['status'] = 0;
            api_ajax_return($return);
        }
        //obs 推流延长首次心跳时间
        $obs_monitor_time = intval($m_config['obs_monitor_time']) ? intval($m_config['obs_monitor_time']) : 300;
        $monitor_time = to_date(NOW_TIME + $obs_monitor_time, 'Y-m-d H:i:s');//主播心跳监听
        $has_video = $GLOBALS['db']->getRow("select id from " . DB_PREFIX . "video where create_type = 0 and (live_in = 1 or live_in = 2)and user_id = " . $user_id,
            true, true);
        if (!empty($has_video)) {
            $root['error'] = "APP端已发起直播，PC端不能重复";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $sql = "select id,push_rtmp,live_image,live_in from " . DB_PREFIX . "video where create_type = 1 and live_in = 2 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {
            //更新心跳时间，免得被删除了
            $sql = "update " . DB_PREFIX . "video set monitor_time = '" . $monitor_time . "' where id =" . $video['id'];
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $root['status'] = 1;
                $root['live_id'] = $video['id'];
                $push_rtmp = $video['push_rtmp'];
                $i = strrpos($push_rtmp, "/") + 1;
                $root['push_url'] = substr($push_rtmp, 0, $i);
                $root['push_code'] = substr($push_rtmp, $i);
                api_ajax_return($root);
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,push_rtmp,live_image,live_in from " . DB_PREFIX . "video where create_type = 1 and live_in = 1 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {
            $root['status'] = 1;
            $root['live_id'] = $video['id'];
            $push_rtmp = $video['push_rtmp'];
            $i = strrpos($push_rtmp, "/") + 1;
            $root['push_url'] = substr($push_rtmp, 0, $i);
            $root['push_code'] = substr($push_rtmp, $i);
            api_ajax_return($root);
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

        if ($province == '' || $city == '') {
            $ipinfo = get_ip_info();
            $province = $ipinfo['province'];
            $city = $ipinfo['city'];
        }
        $data = $this->create_video($user_id, $title, $is_private, $monitor_time, $cate_id, $province, $city,
            $share_type);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        $channel_info = $video_factory->Create($data['id'], 'flv');
        $data['prop_table'] = createPropTable();
        $data['channelid'] = $channel_info['channel_id'];
        $data['push_rtmp'] = $channel_info['upstream_address'];
        $data['play_flv'] = $channel_info['downstream_address']['flv'];
        $data['play_rtmp'] = $channel_info['downstream_address']['rtmp'];
        $data['play_hls'] = $channel_info['downstream_address']['hls'];
        $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');

        if ($GLOBALS['db']->affected_rows()) {
            $root['status'] = 1;
            $root['live_id'] = $data['id'];
            $root['live_image'] = $data['live_image'];
            $i = strrpos($data['push_rtmp'], "/") + 1;
            $root['push_url'] = substr($data['push_rtmp'], 0, $i);
            $root['push_code'] = substr($data['push_rtmp'], $i);
            sync_video_to_redis($data['id'], '*', false);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $status['online_status'] = 1;
            $video_redis->update_db($data['id'], $status);
        } else {
            $root['status'] = 0;
            $root['error'] = '创建房间失败！';
        }
        api_ajax_return($root);
    }

    /**
     * @param $is_private
     * @param $m_config
     * @param $user_id
     * @param $live_image
     * @param $xpoint
     * @param $ypoint
     * @param $monitor_time
     * @param $share_type
     * @param $title
     * @param $cate_id
     * @param $province
     * @param $city
     * @return array
     */
    public function create_video(
        $user_id,
        $title,
        $is_private,
        $monitor_time,
        $cate_id = '',
        $province = '',
        $city = '',
        $share_type = ''
    ) {
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
            $cate_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "video_cate where title='" . $title . "'",
                true, true);
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

        if ($province == '') {
            $province = '火星';
        }

        if ($city == '') {
            $city = '火星';
        }

        $video_id = get_max_room_id(0);
        $data = array();
        $data['id'] = $video_id;
        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        if ($is_private == 1) {
            $data['room_type'] = 1;
            $data['private_key'] = md5($video_id . rand(1, 9999999));//私密直播key
        } else {
            $data['room_type'] = 3;
        }

        $m_config = load_auto_cache("m_config");
        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

        $sql = "select sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $user_id;
        $user = $GLOBALS['db']->getRow($sql, true, true);

        $info = origin_image_info($user['head_image']);
        $data['head_image'] = get_spec_image($info['file_name']);
        $data['thumb_head_image'] = $user['thumb_head_image'];
        $data['live_image'] = $data['head_image'];

        $data['sex'] = intval($user['sex']);//性别 0:未知, 1-男，2-女
        // 0:腾讯云互动直播, 1:腾讯云直播, 2:金山云，3:星域，4:千秀云
        $data['video_type'] = $m_config['video_type'];

        require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
        $api = createTimAPI();
        $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
        if ($ret['ActionStatus'] != 'OK') {
            api_ajax_return(array(
                'status' => 0,
                'error' => $ret['ErrorCode'] . $ret['ErrorInfo']
            ));
        }

        $data['group_id'] = $ret['GroupId'];
        $data['monitor_time'] = $monitor_time;

        $data['create_type'] = 1;// 0:APP端创建的直播;1:PC端创建的直播
        $data['push_url'] = '';//video_type=1;1:腾讯云直播推流地址
        $data['play_url'] = '';//video_type=1;1:腾讯云直播播放地址(rmtp,flv)

        $data['share_type'] = $share_type;
        $data['title'] = $title;
        $data['cate_id'] = $cate_id;
        $data['user_id'] = $user_id;
        $data['live_in'] = 2;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = '';//'当前观看人数';
        $data['vote_number'] = '';//'获得票数';
        $data['province'] = $province;//'省';
        $data['city'] = $city;//'城市';

        $data['create_time'] = NOW_TIME;//'创建时间';
        $data['begin_time'] = NOW_TIME;//'开始时间';
        $data['end_time'] = '';//'结束时间';
        $data['is_hot'] = 1;//'1热门; 0:非热门';
        $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

        //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
        $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);

        $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
        $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);

        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];
        return $data;
    }

    /**
     * 结束串流验证
     */
    public function end()
    {
        $root = array();
        $user_id = intval($_REQUEST['user_name']);
        $user_pwd = trim($_REQUEST['user_pwd']);
        $verify = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "user where id={$user_id} and user_pwd='{$user_pwd}'",
            true, true);
        if (!$verify) {
            $root['status'] = -1;
            $root['error'] = '用户验证不通过';
            api_ajax_return($root);
        }
        $room_id = strim($_REQUEST['live_id']);//房间号id
        $video_vid = strim($_REQUEST['video_url']);//视频地址
        if ($video_vid == 'null') {
            $video_vid = '';
        }
        if (OPEN_PAI_MODULE == 1) {
            $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id,pai_id from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id;

        } else {
            $sql = "select id,user_id,max_watch_number,virtual_watch_number,robot_num,vote_number,group_id,room_type,begin_time,end_time,channelid,cate_id from " . DB_PREFIX . "video where id = " . $room_id . " and user_id = " . $user_id;

        }
        $video = $GLOBALS['db']->getRow($sql, true, true);

        //只有主播自己能结束
        if ($user_id == $video['user_id']) {
            do_end_video($video, $video_vid, 0, $video['cate_id']);

//            $root['watch_number'] = intval($video['max_watch_number']);
//            $root['vote_number'] = intval($video['vote_number']);//获得秀票

//            $time_len =  NOW_TIME -  $video['begin_time'];//私有聊天或小于5分钟的视频，不保存
//            $m_config =  load_auto_cache("m_config");
//            $short_video_time = $m_config['short_video_time']?$m_config['short_video_time']:300;

//            if ($video['room_type'] == 1 || $time_len < $short_video_time || $video_vid == ''){
//
//                $root['has_delvideo'] = 0;//1：显示删除视频按钮; 0:不显示；
//
//            }else {
//                $root['has_delvideo'] = 1;//1：显示删除视频按钮; 0:不显示；
//            }

        } else {
            $root['status'] = 0;
            $root['error'] = '无权关闭';
        }
        rm_auto_cache("select_video");
        $root['status'] = 1;

        ajax_return($root);
    }
}
