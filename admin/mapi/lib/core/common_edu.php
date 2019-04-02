<?php

define('AUTH_TYPE_TEACHER', '教师');//教师认证类型
define('AUTH_TYPE_ORG', '机构');//机构认证类型
define('AUTHENTICATED', 2);

function remove_emoji($text)
{
    return preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u',
        '', $text);
}

function upload_edu_video($file_id, $table, $field, $id)
{
    $GLOBALS['db']->autoExecute(DB_PREFIX . 'edu_video', array(
        'file_id' => $file_id,
        'callback_info' => json_encode(array(
            'table' => $table,
            'field' => $field,
            'id' => $id,
        )),
        'create_time' => NOW_TIME,
    ));

    $last_id = $GLOBALS['db']->insert_id();

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
    $video_factory = new VideoFactory();
    $video_factory->ModifyVodInfo($file_id, array('id' => 'edu_' . $last_id, 'begin_time' => NOW_TIME));
}

function update_deal_tags($user_id,$tags)
{
    $deals=$GLOBALS['db']->getAll("select id from ".DB_PREFIX."edu_deal where user_id= ".intval($user_id)." and deal_status=1");
    if($deals){
        $deals_ids=array_map('array_shift', $deals);
        $GLOBALS['db']->query("update ".DB_PREFIX."edu_deal set tags='".$tags."' where id in(".implode(',',$deals_ids).")");
    }
}

/*
 * 会员秀豆，秀票数变更日志
 * $data=array('diamonds'=>0,'ticket'=>0)
 * */
function insert_user_diamonds_log($data,$user_id,$msg,$type,$ext_id){
    $user_info = $GLOBALS['db']->getRow("select diamonds,ticket from " . DB_PREFIX . "user where id= " . $user_id . " ");
    $time=NOW_TIME;
    $diamonds_log_data = array(
        'user_id' => $user_id,
        'ext_id' =>  $ext_id,
        'diamonds' => $data['diamonds'],//变更秀豆数额
        'account_diamonds' => $user_info['diamonds'],//账户余额
        'ticket' => $data['ticket'],//变更秀票数
        'account_ticket' => $user_info['ticket'],//账户秀票数
        'memo' => $msg,//备注
        'create_time' => $time,
        'create_date' => to_date($time, 'Y-m-d H:i:s'),
        'create_time_ymd' => to_date($time, 'Y-m-d'),
        'create_time_y' => to_date($time, 'Y'),
        'create_time_m' => to_date($time, 'm'),
        'create_time_d' => to_date($time, 'd'),
        'type' => $type,//1课堂购买 2线下预约 3线上约课 4红包兑换 5众筹直播支持 6直播众筹退款 7众筹直播结算增加
    );
    $GLOBALS['db']->autoExecute(DB_PREFIX . "edu_user_diamonds_log", $diamonds_log_data);
}