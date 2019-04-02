<?php

function get_doll_list($room_ids)
{
    $m_config = load_auto_cache('m_config');
    $ret = load_auto_cache("usersig", array("id" => $m_config['tim_identifier']));
    $rand = mt_rand(1, 1000);

    $url = "https://yun.tim.qq.com/v4/ilvb_doll_catch/room_batch?sdkappid={$m_config['tim_sdkappid']}&identifier={$m_config['tim_identifier']}&usersig={$ret['usersig']}&random={$rand}&contenttype=json";

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $response = $trans->request($url, json_encode([
        "GroupIds" => [10001]
    ]), 'POST');

    return json_decode($response['body'], true);
}

function get_doll_control($room_id)
{
    $m_config = load_auto_cache('m_config');
    $ret = load_auto_cache("usersig", array("id" => $m_config['tim_identifier']));
    $rand = mt_rand(1, 1000);

    $url = "https://yun.tim.qq.com/v4/ilvb_doll_catch/room_addr?sdkappid={$m_config['tim_sdkappid']}&identifier={$m_config['tim_identifier']}&usersig={$ret['usersig']}&random={$rand}&contenttype=json";

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $response = $trans->request($url, json_encode([
        "GroupId" => $room_id
    ]), 'POST');

    return json_decode($response['body'], true);
}

function set_doll_express($doll, $address)
{
    //return  $doll['result_no'];
    $m_config = load_auto_cache('m_config');
    $ret = load_auto_cache("usersig", array("id" => $m_config['tim_identifier']));
    $rand = mt_rand(1, 1000);

    $url = "https://console.tim.qq.com/v4/ilvb_doll_catch/express?sdkappid={$m_config['tim_sdkappid']}&identifier={$m_config['tim_identifier']}&usersig={$ret['usersig']}&random={$rand}&contenttype=json";

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $response = $trans->request($url, json_encode([
        "Name" => $address['consignee'],
        "Phone" => $address['mobile'],
        "Province" => $address['province'],
        "City" => $address['city'],
        "Street" => $address['county'].$address['addr_detail'],
        "Results" => [
            [
                "ResultNo" => $doll['result_no'],
                "ToyId" => $doll['toy_id'],
            ]
        ],
    ]), 'POST');
    log_result($response);

    return json_decode($response['body'], true);
}

function get_doll_play_url_by_room_id($room_id, $user_id)
{
    $m_config = load_auto_cache('m_config');
    //直播码=BIZID_MD5(房间号_用户名_数据类型)。
    $stream = md5("{$room_id}_{$user_id}_main");

    return [
        "rtmp" => "rtmp://{$m_config['qcloud_bizid']}.liveplay.myqcloud.com/live/{$m_config['qcloud_bizid']}_" . $stream,
        "flv" => "http://{$m_config['qcloud_bizid']}.liveplay.myqcloud.com/live/{$m_config['qcloud_bizid']}_" . $stream . ".flv",
        "hls" => "http://{$m_config['qcloud_bizid']}.liveplay.myqcloud.com/live/{$m_config['qcloud_bizid']}_" . $stream . ".m3u8",
    ];
}

function get_doll_by_room_id($room_id)
{
    return $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "dolls where room_id = " . $room_id);
}

function get_dolls()
{
    return $GLOBALS['db']->getAll("select id,room_id,name,img,price,status from " . DB_PREFIX . "dolls order by sort desc, id desc limit 1000");
}

function get_doll_status_name($status)
{
    switch ($status) {
        case 0:
            return '空闲中';
        case 1:
            return '使用中';
        case 2:
            return '维护中';
    }
}

function create_doll_user($user_id)
{

}

function create_doll_video($video_id, $user_id, $title, $live_image)
{
    //用户是否禁播，$is_ban=1 永久禁播；$is_ban=0非永久禁播，$ban_time禁播结束时间
    $sql = "select is_authentication,is_ban,ban_time,mobile,login_ip,ban_type,apns_code,sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $user_id;
    $user = $GLOBALS['db']->getRow($sql, true, true);
    $m_config = load_auto_cache("m_config");

    $sql = "select id,video_type from " . DB_PREFIX . "video where id = " . $video_id;
    $video = $GLOBALS['db']->getRow($sql, true, true);
    if ($video) {

        //更新心跳时间，免得被删除了
        $sql = "update " . DB_PREFIX . "video live_in = 1, set monitor_time = '" . to_date(NOW_TIME + 86400 * 365,
                'Y-m-d H:i:s') . "' where id =" . $video['id'];
        $GLOBALS['db']->query($sql);

        if ($GLOBALS['db']->affected_rows()) {
            return;
        }
    }

    $data = array();
    $data['id'] = $video_id;

    $data['room_type'] = 3;
    $data['virtual_number'] = intval($m_config['virtual_number']);
    $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

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
    $data['sex'] = intval($user['sex']);//性别 0:未知, 1-男，2-女
    $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

    require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $api = createTimAPI();
    $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);

    $data['group_id'] = $video_id;
    $data['monitor_time'] = to_date(NOW_TIME + 86400 * 365, 'Y-m-d H:i:s');//主播心跳监听

    $data['push_url'] = '';//video_type=1;1:腾讯云直播推流地址
    $data['play_url'] = '';//video_type=1;1:腾讯云直播播放地址(rmtp,flv)

    $data['title'] = $title;
    $data['user_id'] = $user_id;
    $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
    $data['watch_number'] = '';//'当前观看人数';
    $data['vote_number'] = '';//'获得票数';
    $data['create_time'] = NOW_TIME;//'创建时间';
    $data['begin_time'] = NOW_TIME;//'开始时间';
    $data['end_time'] = '';//'结束时间';
    $data['is_hot'] = 1;//'1热门; 0:非热门';
    $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

    $play_info = get_doll_play_url_by_room_id($room_id, $user_id);
    $data['play_flv'] = $play_info['flv'];
    $data['play_rtmp'] = $play_info['rtmp'];
    $data['play_hls'] = $play_info['hls'];

    $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

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
    $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');

    if ($GLOBALS['db']->affected_rows()) {
        $return['status'] = 1;
        $return['error'] = '';
        $return['room_id'] = $video_id;
        $return['video_type'] = intval($data['video_type']);

        sync_video_to_redis($video_id, '*', false);

    } else {
        $return['status'] = 0;
        $return['error'] = '创建房间失败！';
    }
}
