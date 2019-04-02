<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

/**
 * 获取热度排行
 */
function get_rank()
{
    $rank = get_heat_rank_cache();
    if (intval($rank['is_first']) == 1 && $rank['list']) {
        //开启全服推送
        $notify_data = array();
        $notify_data['user_id'] = $rank['list'][0]['user_id'];
        $notify_data['user_name'] = $rank['list'][0]['nick_name'];
        $notify_data['room_id'] = $rank['list'][0]['video_id'];
        $notify_data['head_image'] = $rank['list'][0]['head_image'];
        $a = HeatNotify($notify_data);
    }
    return array($rank['key'], $rank['sql'], $notify_data, $a);
}

/**
 * 获取热度60s实时排行
 */
function get_rank_now()
{
    $rank = get_heat_rank_now_cache();
    return array($rank['key'], $rank['sql']);
}

/**
 * 获取主播60s的热度排行
 * @param $podcast_id
 * @return mixed
 */
function get_user_now_rank($podcast_id)
{
    $rank = get_heat_rank_now_cache();
    $rank_cache = get_heat_rank_cache();
    //log_file($rank,'get_user_now_rank');
    //log_file($rank_cache,'get_user_now_rank');
    if ($rank['list']) {
        foreach ($rank['list'] as $k => $v) {
            $count_down = 0;
            $rank['list'][$k]['rank'] = $k + 1;
            if ($v['user_id'] == $podcast_id) {
                $podcast_info = $v;
                $podcast_info['rank'] = $k + 1;
                $heat = $rank['list'][0]['heat_value'] - $v['heat_value'];
                $podcast_info['heat'] = intval($heat);
                $podcast_info['first_heat'] = $heat; //与下一轮热一相差值
                if ($rank_cache['cache_next_time'] != '') {
                    $now_t = date('Y-m-d H:i:s');
                    //log_file($now_t,'get_user_now_rank');
                    $count_down = strtotime($rank_cache['cache_next_time']) - strtotime($now_t);
                    //log_file($count_down,'get_user_now_rank');
                    $count_down = intval($count_down) > 0 ? intval($count_down) : 0;
                    //log_file($count_down,'get_user_now_rank');
                }
                $podcast_info['count_down'] = $count_down;
                //unset($rank['list'][$k]);
            }
        }
        $rank['list'] = array_slice($rank['list'], 0, 10);
        if ($podcast_info) {
            $rank['podcast'] = $podcast_info;
        }
    }
    $rank['count_down'] = $count_down;
    $rank['status'] = 1;
    $rank['error'] = '';
    return $rank;
}

/**
 * 获取主播600s的热度排行
 * @param $podcast_id
 * @return mixed
 */
function get_user_rank($podcast_id)
{
    $rank = get_heat_rank_cache();
    $heat_rank = 0;
    if ($rank['list']) {
        foreach ($rank['list'] as $k => $v) {
            $rank['list'][$k]['rank'] = $k + 1;
            if ($v['user_id'] == $podcast_id && intval($podcast_id) > 0) {
                $heat_rank = intval($rank['list'][$k]['rank']);
            }
        }
    }
    $live_list = load_auto_cache("select_heat_video");
    if ($live_list && $heat_rank == 0) {
        foreach ($live_list as $kk => $vv) {
            if ($vv['user_id'] == $podcast_id && intval($podcast_id) > 0) {
                $heat_rank = intval($kk + 1);
            }
        }
    }
    return $heat_rank;
}

/**
 * 获取10分钟内的热度排行榜
 * @return mixed
 */
function get_heat_rank_cache()
{
    $rank = load_auto_cache("heat_rank", 0); //10分钟前排行
    return $rank;
    /*$old_rank = load_auto_cache("heat_rank",1); //20分钟前排行
print_r($old_rank);echo "<hr/>";
$too_old_rank = load_auto_cache("heat_rank",2);//30分钟前排行
print_r($too_old_rank);exit;*/
}

/**
 * 获取1分钟内的热度排行榜
 * @return mixed
 */
function get_heat_rank_now_cache()
{
    $now_rank = load_auto_cache("heat_rank_now"); //1分钟内
    return $now_rank;
}

/**
 * 红包推送 全省/全服
 * @param $sender
 * @param $ext
 * @param $prop
 * @return mixed
 */
function propNotify_red($sender, $ext, $prop) //礼物发送者信息，群组内消息数据，礼物信息

{

    $user_id = $sender['user_id'];

    //定义广播消息相关内容
    $broadMsg = array();
    $broadMsg['type'] = 50; //IM的type

    $broadMsg['num'] = $ext['num']; //礼物数量
    $broadMsg['is_plus'] = $ext['is_plus']; //可以叠加显示多个
    $broadMsg['is_much'] = $ext['is_much']; //可以连续发送多个
    $broadMsg['room_id'] = $ext['room_id']; //房间号
    $broadMsg['app_plus_num'] = $ext['app_plus_num'];
    $broadMsg['app_plus_num'] = $ext['is_animated']; //动画类型
    $broadMsg['is_red_envelope'] = 1; //是否红包

    $broadMsg['sender'] = $sender; //礼物发送者
    $broadMsg['head_image'] = $ext['head_image']; //主播头像
    $broadMsg['prop_id'] = intval($ext['prop_id']); //礼物ID
    $broadMsg['icon'] = $ext['icon']; //礼物图标
    $broadMsg['to_user_id'] = $ext['to_user_id']; //接收礼物的主播ID
    $broadMsg['fonts_color'] = ""; //消息字体颜色
    $broadMsg['anim_type'] = $ext['anim_type']; //大型道具类型;
    //$broadMsg['desc'] = "房间号：".$broadMsg['room_id'].",派送全服大红包，速速围观！豪气冲天的".$sender['nick_name']."送了".$prop['name']."给主播".$broadMsg['to_user_id'];//消息;

    //判断红包类型 0普通、1全省、2全服
    if (intval($prop['red_envelope_type']) == 1) {
        $broadMsg['desc'] = "房间号：" . $broadMsg['room_id'] . ",派送全省大红包，速速围观！"; //消息;
        //获取和主播同一省份的主播所在的所有的群组ID
        $sql = "SELECT v.id,group_id,live_in FROM " . DB_PREFIX . "video as v LEFT JOIN " . DB_PREFIX . "user as u ON u.id = v.user_id where live_in  in(1,3)  and v.id != " . $ext['room_id'] . " and u.province = (SELECT province FROM fanwe_user where id = " . $ext['to_user_id'] . ")";
        $group_id_all = $GLOBALS['db']->getAll($sql, true, true);
    } else {
        $broadMsg['desc'] = "房间号：" . $broadMsg['room_id'] . ",派送全服大红包，速速围观！"; //消息;
        //获取除发红包的直播间外的所有的群组ID
        $sql = "SELECT id,group_id,live_in FROM " . DB_PREFIX . "video where live_in in(1,3) and id!=" . $ext['room_id'];
        $group_id_all = $GLOBALS['db']->getAll($sql, true, true);
    }
    //发送消息
    $root = push_im($broadMsg, $group_id_all);
    return $root;
}

/**
 * 热度第一推送
 * @param $user_name
 * @return mixed
 */
function HeatNotify($notify_data)
{
    //定义广播消息相关内容
    $broadMsg = array();
    $broadMsg['type'] = 50; //IM的type
    $broadMsg['icon'] = ""; //礼物图标
    $broadMsg['fonts_color'] = ""; //消息字体颜色
    $user_name = ($notify_data['user_name']);
    $broadMsg['desc'] = $user_name . "本轮热度排序获得第一名"; //消息;
    $broadMsg['room_id'] = $notify_data['room_id']; // 热度第一直播间ID
    $broadMsg['head_image'] = $notify_data['head_image']; // 热度第一主播头像
    $broadMsg['user_id'] = $notify_data['user_id']; // 热度第一主播id

    //更新获取热度第一次数
    up_heat_all_rank($notify_data);

    //获取所有的群组ID
    $sql = "SELECT id,group_id,live_in FROM " . DB_PREFIX . "video where live_in = 1";
    $group_id_all = $GLOBALS['db']->getAll($sql, true, true);
    //log_file($group_id_all,'HeatNotify');
    //发送消息
    $root = push_im($broadMsg, $group_id_all);
    return $root;
}

/**
 * IM 推送消息实体
 * @param $broadMsg
 * @param $group_id_all
 * @return mixed
 */
function push_im($broadMsg, $group_id_all) //IM消息推送

{
    $all_success_flag = 1; //所有群组IM发送都成功的标志位,默认置1
    #构造rest API请求包
    $msg_content = array();
    //创建$msg_content 所需元素
    $msg_content_elem = array(
        'MsgType' => 'TIMCustomElem', //定义类型为普通文本型
        'MsgContent' => array(
            'Data' => json_encode($broadMsg) //转为JSON字符串
        )
    );

    //将创建的元素$msg_content_elem, 加入array $msg_content
    array_push($msg_content, $msg_content_elem);

    //引入IM API文件
    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $tim_api = createTimAPI();

    //向所有群组发送消息
    $ret = array(); //存放发送返回信息
    for ($i = 0; $i < count($group_id_all); $i++) {
        $ret[] = $tim_api->group_send_group_msg2($broadMsg['user_id'], $group_id_all[$i]['group_id'], $msg_content);
        $idx = 'group' . $i;
        $root[$idx] = $group_id_all[$i]['group_id'];
    }
    //遍历群组发送情况，对其中发送失败的群组且错误码为10002的，自动重发一次
    for ($i = 0; $i < count($ret); $i++) {
        if ($ret[$i]['ActionStatus'] == 'FAIL' && $ret[$i]['ErrorCode'] == 10002) {
            //10002 系统错误，请再次尝试或联系技术客服。
            log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret[$i]));
            /*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
            $ret[$i] = $tim_api->group_send_group_msg2($broadMsg['user_id'], $group_id_all[$i]['group_id'], $msg_content);
            $root['repeat_test'] = 1;

        }
    }

    //查看是否全部发送成功,对于没发送成功的情况进行回馈
    for ($i = 0; $i < count($ret); $i++) {
        //定义对应信息的存放键值
        $err_info = 'error_notify' . $i;
        $status_info = 'status_notify' . $i;
        //出错的写入对应位置
        if ($ret[$i]['ActionStatus'] == 'FAIL') {
            $root[$err_info] = $ret[$i]['ErrorInfo'] . ":" . $ret[$i]['ErrorCode'];
            $root[$status_info] = 0;
            $all_success_flag = 0;
        }
    }
    if ($all_success_flag) {
        $root['status_notify_all'] = 1;
        $root['ret'] = $ret;
    } else {
        $root['status_notify_all'] = 0;
    }

    return $root;
}

/**
 * 直播间内身份区分
 * @param $user_info 访问者信息
 * @param $user_id  主播id
 * @param $tourist_astrict 是否开启游客限言 0,1
 * @return mixed
 */
function video_status_effect($user_info, $user_id, $tourist_astrict = 0)
{
    $data = array();
    $speak_level = load_auto_cache('speak_level');
    if (!empty($speak_level)) {
        foreach ($speak_level as $k => $v) {
            if (($user_info['user_level'] >= $v['begin_level']) && ($user_info['user_level'] < $v['end_level'])) {
                if ($v['colour'] == '') {
                    $data['v_identity_colour'] = '#000000';
                } else {
                    $data['v_identity_colour'] = $v['colour'];
                }
                $data['v_speak_num'] = (int) $v['speak_num'];
            }
        }
    } else {
        $data['v_speak_num'] = 0; //默认发言限制
        $data['v_identity_colour'] = "#000000"; //默认颜色 黑色
    }

    if ($tourist_astrict) {
        $data['v_speak_num'] = 8; //默认发言限制
        //$data['v_identity_colour'] = "#000000";//默认颜色 黑色
    }
    //身份区分
    if ($user_info['id'] == $user_id) {
        //主播
        $data['v_identity'] = 3;
        $data['v_identity_colour'] = "#FF0000"; //红色
        $data['v_speak_num'] = 0;
        $data['v_join_name'] = ($user_info['nick_name']) . "-【主播】-进入";
    } elseif ($user_info['login_type'] != 4) {
        //会员
        $data['v_identity'] = 1;
        // $data['v_identity_colour'] = "#FFFF00"; //黄色
        // $data['v_speak_num'] = 0;
        $data['v_join_name'] = ($user_info['nick_name']) . "-【会员】-进入";
    } else {
        //游客
        // $data['v_identity'] = 0;
        $data['v_join_name'] = ($user_info['nick_name']) . "-【游客】-进入";
    }
    //$root['user_id']主播id $user_id用户id
    $res = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_admin where user_id=" . $user_info['id'] . " and podcast_id=" . $user_id);
    if (!empty($res)) {
        //管理员
        $data['v_identity'] = 2;
        $data['v_identity_colour'] = "#8B00FF";
        $data['v_speak_num'] = 0;
        $data['v_join_name'] = ($user_info['nick_name']) . "-【管理员】-进入";
    }
    return $data;
}

/**
 * 当前房间的热度排名推送
 * @param $user_id
 * @param $room_id
 * @param $group_id
 * @param $heat_rank
 * @return mixed
 */
function video_push_im($user_id, $room_id, $group_id, $heat_rank)
{

    $ext = array();
    $ext['type'] = 70; // 直播间内热度推送
    $ext['room_id'] = $room_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
    $ext['heat_rank'] = $heat_rank; //当前房间热度排名

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

    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $api = createTimAPI();
    $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
    return $ret;
}

/**
 * 获取总榜数据
 * @return mixed
 */
function get_heat_all_rank($param)
{
    $rank = load_auto_cache("heat_rank_all", $param); //总榜排行
    return $rank;
}

function up_heat_all_rank($notify_data)
{
    $user_id = $notify_data['user_id'];
    $now_time = NOW_TIME;
    $create_time = $now_time; //时间
    $update_time = $now_time; //时间
    $update_date = to_date($now_time); //日期字段，按日期归档；
    $update_ym = to_date($now_time, 'Ym'); //年月 如:201610
    $update_d = to_date($now_time, 'd'); //日
    $update_w = to_date($now_time, 'w'); //周
    //更新热度第一次数
    if ($GLOBALS['db']->getOne("SELECT id FROM  " . DB_PREFIX . "rank_heat_all WHERE user_id=" . $user_id)) {
        //更新
        $sql = "UPDATE " . DB_PREFIX . "rank_heat_all set update_time = $update_time ,update_date= '" . $update_date . "',update_ym = '" . $update_ym . "',update_d = '" . $update_d . "',update_w = '" . $update_w . "',heat_amount = heat_amount+1  WHERE user_id = " . $user_id;
        $GLOBALS['db']->query($sql);
    } else {
        $heat_all = array();
        $heat_all['user_id'] = $user_id;
        $heat_all['heat_amount'] = 1;
        $heat_all['create_time'] = $create_time;

        //插入
        $GLOBALS['db']->autoExecute(DB_PREFIX . "rank_heat_all", $heat_all, "INSERT");
    }
}
