<?php
//礼物发送者信息，群组内消息数据，礼物信息
function propNotify($sender, $ext, $prop, $desc = '')
{
    $all_success_flag = 1; //所有群组IM发送都成功的标志位,默认置1
    $user_id = $sender['user_id'];

    //定义广播消息相关内容
    $broadMsg = array();
    $broadMsg['type'] = 50; //IM的type
    $broadMsg['data_type'] = isset($ext['data_type']) ? (int) $ext['data_type'] : 1; // 全屏消息类型
    $broadMsg['num'] = $ext['num']; //礼物数量
    $broadMsg['is_plus'] = $ext['is_plus']; //可以叠加显示多个
    $broadMsg['is_much'] = $ext['is_much']; //可以连续发送多个
    $broadMsg['room_id'] = $ext['room_id']; //房间号
    $broadMsg['app_plus_num'] = $ext['app_plus_num'];
    $broadMsg['is_animated'] = $ext['is_animated']; //动画类型

    $broadMsg['sender'] = $sender; //礼物发送者
    $broadMsg['head_image'] = $ext['head_image']; //主播头像
    $broadMsg['prop_id'] = intval($ext['prop_id']); //礼物ID
    $broadMsg['icon'] = $ext['icon']; //礼物图标
    $broadMsg['to_user_id'] = $ext['to_user_id']; //接收礼物的主播ID
    $broadMsg['fonts_color'] = ""; //消息字体颜色
    $broadMsg['anim_type'] = $ext['anim_type']; //大型道具类型;
    $broadMsg['desc'] = empty($desc) ? "豪气冲天的" . $sender['nick_name'] . "送了" . $prop['name'] . "给主播" . $broadMsg['to_user_id'] : $desc; //消息;

    #构造rest API请求包
    $msg_content = array();
    //创建$msg_content 所需元素
    $msg_content_elem = array(
        'MsgType' => 'TIMCustomElem', //定义类型为普通文本型
        'MsgContent' => array(
            'Data' => json_encode($broadMsg) //转为JSON字符串
        )
    );
    $m_config = load_auto_cache("m_config"); //初始化手机端配置
    if (intval($m_config['has_dirty_words']) == 1) {
        //文档内容,用来过滤脏字
        $msg_text_elem = array(
            'MsgType' => 'TIMTextElem', //
            'MsgContent' => array(
                'Text' => $desc
            )
        );
        array_push($msg_content, $msg_text_elem, $msg_content_elem);
    } else {
        //将创建的元素$msg_content_elem, 加入array $msg_content
        array_push($msg_content, $msg_content_elem);
    }

    //引入IM API文件
    fanwe_require(APP_ROOT_PATH . 'system/tim/TimApi.php');
    $tim_api = createTimAPI();

    //获取所有的群组ID
    $group_id_all = $GLOBALS['db']->getAll("SELECT id,group_id,live_in FROM " . DB_PREFIX . "video WHERE live_in = 1", true, true);
    //向所有群组发送消息
    $ret = array(); //存放发送返回信息
    foreach ($group_id_all as $key => &$value) {
        $ret[$key] = $tim_api->group_send_group_msg2($user_id, $value['group_id'], $msg_content);
        if ($ret[$key]['ActionStatus'] == 'FAIL' && $ret[$key]['ErrorCode'] == 80001) {
            $root['error'] = '该词已被禁用';
            $root['status_notify_all'] = 0;
            return $root;
        }
        $idx = 'group' . $key;
        $root[$idx] = $value['group_id'];
    }
    // for ($i = 0; $i < count($group_id_all); $i++) {
    //     /*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
    //     if (($ext['room_id'] != $group_id_all[$i]['id']) && ($group_id_all[$i]['live_in'] == 1)) //除了自己的房间外，其他的房间全部通告;且只对直播中的有效
    //     {
    //         $ret[] = $tim_api->group_send_group_msg2($user_id, $group_id_all[$i]['group_id'], $msg_content);
    //         $idx = 'group' . $i;
    //         $root[$idx] = $group_id_all[$i]['group_id'];
    //     }
    // }

    //遍历群组发送情况，对其中发送失败的群组且错误码为10002的，自动重发一次
    foreach ($ret as $_key => &$_value) {
        if ($_value['ActionStatus'] == 'FAIL' && $_value['ErrorCode'] == 10002) {
            //10002 系统错误，请再次尝试或联系技术客服。
            log_err_file(array(__FILE__, __LINE__, __METHOD__, $_value));
            /*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
            $_value = $tim_api->group_send_group_msg2($user_id, $group_id_arr[$_key]['group_id'], $msg_content);
            $root['repeat_test'] = 1;
        }
    }
    // for ($i = 0; $i < count($ret); $i++) {
    //     if ($_value['ActionStatus'] == 'FAIL' && $ret[$i]['ErrorCode'] == 10002) {
    //         //10002 系统错误，请再次尝试或联系技术客服。
    //         log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret[$i]));
    //         /*if ($i==1) $group_id_all[$i]['group_id'] = 66666;//错误测试*/
    //         $ret[$i] = $tim_api->group_send_group_msg2($user_id, $group_id_arr[$i]['group_id'], $msg_content);
    //         $root['repeat_test'] = 1;
    //     }
    // }
    reset($ret);
    //查看是否全部发送成功,对于没发送成功的情况进行回馈
    foreach ($ret as $_key_ => &$_value_) {
        //定义对应信息的存放键值
        $err_info = 'error_notify' . $_key_;
        $status_info = 'status_notify' . $_key_;
        //出错的写入对应位置
        if ($ret[$i]['ActionStatus'] == 'FAIL') {
            $root[$err_info] = $_value_['ErrorInfo'] . ":" . $_value_['ErrorCode'];
            $root[$status_info] = 0;
            $all_success_flag = 0;
        }
    }
    // for ($i = 0; $i < count($ret); $i++) {
    //     //定义对应信息的存放键值
    //     $err_info = 'error_notify' . $i;
    //     $status_info = 'status_notify' . $i;
    //     //出错的写入对应位置
    //     if ($ret[$i]['ActionStatus'] == 'FAIL') {
    //         $root[$err_info] = $ret[$i]['ErrorInfo'] . ":" . $ret[$i]['ErrorCode'];
    //         $root[$status_info] = 0;
    //         $all_success_flag = 0;
    //     }
    // }
    if ($all_success_flag) {
        $root['status_notify_all'] = 1;
    } else {
        $root['error'] = '群发失败';
        $root['status_notify_all'] = 0;
    }

    //测试一个
    /*$group_id = $group_id_all[0]['group_id'];
    $ret = $tim_api->group_send_group_msg2($user_id,$group_id,$msg_content);
    if ($ret['ActionStatus'] == 'FAIL' && $ret[$i]['ErrorCode'] == 10002){
    //10002 系统错误，请再次尝试或联系技术客服。
    log_err_file(array(__FILE__,__LINE__,__METHOD__,$ret[$i]));
    $ret = $tim_api->group_send_group_msg2($user_id,$group_id,$msg_content);
    }
    if ($ret['ActionStatus'] == 'FAIL')
    {
    $root['error_notify'] = $ret['ErrorInfo'].":".$ret['ErrorCode'];
    $root['status_notify'] = 0;

    }
    else
    {
    $root['error_notify'] = '';
    $root['status_notify'] = 1;
    }*/

    /* $root['$user_id_notify'] = $user_id;
    $root['$group_id_notify'] = $group_id_all;*/

    /* $root['msg_notify'] = $msg_content;*/

    return $root;
}
