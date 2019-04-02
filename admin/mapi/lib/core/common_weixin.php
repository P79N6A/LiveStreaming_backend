<?php

/**
 * 是否短时间内已发送过验证码
 * @param $mobile 手机号
 * @param int $time 时间范围，默认 60 秒
 * @return bool
 */
function has_already_send($mobile, $time = 60)
{
    $sql = "select count(*) from " . DB_PREFIX . "mobile_verify_code where mobile = '" . $mobile . "' and client_ip='" . get_client_ip() . "' and create_time>=" . (get_gmtime() - $time);
    return $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 发送手机验证码
 * @param $mobile 手机号
 * @return array
 */
function send_mobile_code($mobile)
{
    delete_mobile_verify_code();
    $code = rand(1000, 9999);
    $GLOBALS['db']->autoExecute(DB_PREFIX . "mobile_verify_code", array(
        "verify_code" => $code,
        "mobile" => $mobile,
        "create_time" => get_gmtime(),
        "client_ip" => get_client_ip()
    ), "INSERT");
    send_verify_sms($mobile, $code);

    $sql = "select * from " . DB_PREFIX . "deal_msg_list where dest = '{$mobile}' and code='{$code}'";
    return $GLOBALS['db']->getRow($sql);
}

/**
 * 手机号是否有效，未被禁用
 * @param $mobile 手机号
 * @return bool
 */
function is_mobile_effect($mobile)
{
    $sql = "select id from " . DB_PREFIX . "user where mobile = '{$mobile}' and is_effect = 1";
    return $GLOBALS['db']->getOne($sql) > 0;
}

/**
 * 用户是否有效
 * @param $id
 * @return mixed
 */
function is_user_effect($id)
{
    return $GLOBALS['db']->getOne("select is_effect from " . DB_PREFIX . "user where id =" . $id);
}

/**
 * 当前 IP 是否有效
 * @return mixed
 */
function is_request_ip_effect()
{
    $sql = "SELECT login_ip FROM " . DB_PREFIX . "user WHERE login_ip = '" . get_client_ip() . "' and is_effect !=1";
    return !$GLOBALS['db']->getOne($sql);
}

/**
 * 用户搜索
 * @param $keyword
 * @param $page
 * @param int $page_size
 * @return mixed
 */
function user_search($keyword, $page, $page_size = 20)
{
    if ($page <= 0) {
        $page = 1;
    }
    $limit = (($page - 1) * $page_size) . "," . $page_size;
    if ($page == 1 && preg_match("/^\d+$/", $keyword)) {//如果搜索关键字为数字
        //搜索ID，昵称,靓号，不为机器人。
        $sql = "select u.id as user_id,u.nick_name,u.signature,u.sex,u.head_image,u.user_level,u.v_icon from " . DB_PREFIX . "user u where  u.id = " . $keyword . " and u.is_robot=0 limit 0,1 
                                union select u.id as user_id,u.nick_name,u.signature,u.sex,u.head_image,u.user_level,u.v_icon from " . DB_PREFIX . "user u where u.luck_num = " . $keyword . " and u.is_robot=0 limit 0,1 
                                union select u.id as user_id,u.nick_name,u.signature,u.sex,u.head_image,u.user_level,u.v_icon from " . DB_PREFIX . "user u where u.nick_name = '" . $keyword . "' and u.is_robot=0 limit " . $page_size;
    } else {
        $sql = "select u.id as user_id,u.nick_name,u.signature,u.sex,u.head_image,u.user_level,u.v_icon from " . DB_PREFIX . "user u where  u.nick_name like '%" . $keyword . "%' and u.is_robot=0 limit " . $limit;
    }

    //查询用户列表,修改成 从只读数据库中取,但不是高效做法;主并发时,可以加入阿里云的搜索服务
    //https://www.aliyun.com/product/opensearch?spm=5176.8142029.388261.62.tgDxhe
    return $GLOBALS['db']->getAll($sql, true, true);
}

/**
 * 查看目标用户关注列表
 * @param $user_id
 * @param $to_user_id
 * @param $page
 * @param int $page_size
 * @return array
 */
function get_user_follow($user_id, $to_user_id, $page, $page_size = 20)
{
    if ($page <= 0) {
        $page = 1;
    }

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
    $user_redis = new UserFollwRedisService($user_id);
    $list = $user_redis->get_follonging_user($to_user_id, $page, $page_size);
    foreach ($list as &$v) {
        $v['signature'] = htmlspecialchars_decode($v['signature']);
        $v['nick_name'] = htmlspecialchars_decode($v['nick_name']);
        $v['signature'] = emoji_decode($v['signature']);
        $v['nick_name'] = emoji_decode($v['nick_name']);
    }
    unset($v);
    return $list;
}

/**
 * 查看目标用户关注列表
 * @param $user_id
 * @param $to_user_id
 * @param $page
 * @param int $page_size
 * @return array
 */
function get_user_focus($user_id, $to_user_id, $page, $page_size = 20)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
    $user_redis = new UserFollwRedisService($user_id);
    $list = $user_redis->get_follonging_by_user($to_user_id, $page, $page_size);
    $keys = $user_redis->following();
    foreach ($list as $k => &$v) {
        $v['follow_id'] = in_array($v['user_id'], $keys) ? 1 : 0;
        $v['head_image'] = get_spec_image($v['head_image']);
        $v['nick_name'] = htmlspecialchars_decode($v['nick_name']);
        $v['signature'] = htmlspecialchars_decode($v['signature']);
        $v['nick_name'] = emoji_decode($v['nick_name']);
        $v['signature'] = emoji_decode($v['signature']);
    }
    unset($v);
    return $list;
}

/**
 * 获取用户公会ID
 * @param $user_id
 * @return mixed
 */
function get_user_society_id($user_id)
{
    $sql = "select society_id from " . DB_PREFIX . "user where id=" . $user_id;
    return $GLOBALS['db']->getOne($sql);
}

/**
 * 获取用户公会申请状态
 * @param $user_id
 * @param $society_id
 * @param int $type
 * @return mixed
 */
function get_user_society_apply_type($user_id, $society_id, $type = 0)
{
    $sql = "select id from " . DB_PREFIX . "society_apply where user_id= {$user_id} and society_id={$society_id} and apply_type=" . $type;
    return $GLOBALS['db']->getOne($sql);
}

/**
 * 获取公会列表
 * @param $page
 * @param $page_size
 * @return mixed
 */
function get_society_list($page, $page_size = 20)
{
    $limit = (($page - 1) * $page_size) . "," . $page_size;
    $sql = "select s.id,s.logo,s.name,s.user_count,s.status,u.nick_name,u.id as uid from " . DB_PREFIX . "society s inner join " . DB_PREFIX . "user u on s.user_id=u.id where s.status = 1 order by s.society_rank desc,s.create_time desc limit " . $limit;
    return $GLOBALS['db']->getAll($sql);
}

function get_video_by_id($id)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
    $video_redis = new VideoRedisService();

    $fields = array(
        'id',
        'create_type',
        'head_image',
        'cate_id',
        'title',
        'thumb_head_image',
        'sort_num',
        'play_url',
        'play_mp4',
        'play_hls',
        'play_flv',
        'room_type',
        'user_id',
        'live_in',
        'monitor_time',
        'max_watch_number',
        'online_status',
        'group_id',
        'room_type',
        'private_key',
        'share_type',
        'province',
        'begin_time',
        'create_time',
        'live_pay_time',
        'is_live_pay',
        'live_pay_type',
        'live_fee',
        'live_is_mention',
        'room_title',
        'pay_room_id',
        'video_type',
        'channelid',
        'live_image',
    );
    $video = $video_redis->getRow_db($id, $fields);
    if (!$video['id']) {
        return null;
    }

    $video['head_image'] = get_spec_image($video['head_image']);
    $video['thumb_head_image'] = get_spec_image($video['thumb_head_image']);
    $video['live_image'] = get_spec_image($video['live_image']);

    if (empty($video['play_url'])) {
        if ($video['live_in'] == 1) {
            $video['play_url'] = $video['play_hls'];
        } elseif (!empty($video['play_mp4'])) {
            $video['play_url'] = $video['play_mp4'];
        } else {
            $file_info = load_auto_cache('video_file', array(
                'id' => $id,
                'video_type' => $video['video_type'],
                'channelid' => $video['channelid'],
                'begin_time' => $video['begin_time'],
                'create_time' => $video['create_time'],
            ));
            $video['play_url'] = $file_info['play_url'];
        }
    }

    return $video;
}

function get_podcast_by_id($user_id)
{
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    $user_redis = new UserRedisService();

    $fields = array(
        'id',
        'fans_count',
        'focus_count',
        'is_agree',
        'video_count',
        'is_authentication',
        'authentication_type',
        'authentication_name',
        'nick_name',
        'signature',
        'sex',
        'province',
        'city',
        'head_image',
        'ticket',
        'no_ticket',
        'refund_ticket',
        'use_diamonds',
        'diamonds',
        'user_level',
        'v_type',
        'v_explain',
        'v_icon',
        'is_remind',
        'birthday',
        'emotional_state',
        'job',
        'family_id',
        'family_chieftain',
        'society_id',
        'society_chieftain',
        'society_settlement_type',
        'is_robot',
        'room_title',
        'luck_num',
        'coin',
        'is_nospeaking',
        'weibo_count',
    );

    $podcast = $user_redis->getRow_db($user_id, $fields);
    $podcast['head_image'] = get_spec_image($podcast['head_image']);
    $podcast['ticket'] = intval(floor($podcast['ticket']));
    $podcast['nick_name'] = emoji_decode($podcast['nick_name']);

    return $podcast;
}

/**
 * 获取小程序用户信息
 * @param $code
 * @return array|bool|mix|mixed|stdClass|string
 */
function get_mina_user_info($code)
{
    $m_config = load_auto_cache('m_config');
    if (empty($m_config['wx_mina_appid'])) {
        throw new Exception('未配置小程序 APP ID');
    }

    if (empty($m_config['wx_mina_secret'])) {
        throw new Exception('未配置小程序 SECRET ID');
    }

    $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$m_config['wx_mina_appid']}&secret={$m_config['wx_mina_secret']}&js_code={$code}&grant_type=authorization_code";

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
    $trans = new transport();
    $response = $trans->request($url, array(), 'GET');
    return json_decode($response['body'], true);
}
