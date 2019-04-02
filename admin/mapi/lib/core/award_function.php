<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: &雲飞水月& (172231343@qq.com)
// +----------------------------------------------------------------------

//调试参数开关
define('crontab_do_award', 0);
define('award_update', 0);
define('get_award_info', 0);
define('get_award_multiple', 0);
define('gift_award', 0);
define('gift_totals', 0);
define('do_prize', 0);
define('push_award_im', 0);

/** 创建礼物总表
 * @return bool|string
 */
function createPropAllTable()
{
    $table = DB_PREFIX . 'video_prop_all';
    $res = $GLOBALS['db']->getRow("SHOW TABLES LIKE'$table'");
    if (!$res) {
        // 创建新表
        $sql = "CREATE TABLE `$table` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT '礼物id',
          `prop_name` varchar(255) NOT NULL COMMENT '道具名',
          `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
          `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
          `total_ticket` int(11) NOT NULL DEFAULT '0' COMMENT '秀票(to_user_id增加的秀票）合计;is_red_envelope=1时,为主播获得的：秀豆 数量',
          `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '送',
          `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '收',
          `create_time` int(10) NOT NULL COMMENT '时间',
          `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
          `create_d` tinyint(2) NOT NULL COMMENT '日',
          `create_w` tinyint(2) NOT NULL COMMENT '周',
          `num` int(10) NOT NULL COMMENT '送的数量',
          `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
          `group_id` varchar(20) NOT NULL COMMENT '群组ID',
          `is_red_envelope` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:红包',
          `msg` varchar(255) NOT NULL COMMENT '弹幕内容',
          `ActionStatus` varchar(10) NOT NULL COMMENT '消息发送，请求处理的结果，OK表示处理成功，FAIL表示失败。',
          `ErrorInfo` varchar(255) NOT NULL COMMENT '消息发送，错误信息',
          `ErrorCode` int(10) NOT NULL COMMENT '错误码',
          `create_ym` varchar(12) NOT NULL COMMENT '年月 如:201610',
          `from_ip` varchar(255) NOT NULL COMMENT '送礼物人IP',
          `is_private` int(4) default 0 COMMENT '判断是否为私信送礼 1表示私信 2表示不是私信',
          `is_award` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为可中奖礼物 1为 是、0为否',
          `is_heat` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热度礼物 1是 0否',
          `is_rocket` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否火箭榜礼物 1是 0否',
          PRIMARY KEY (`id`),
          KEY `idx_ecs_video_prop_cc_1` (`create_ym`,`create_d`,`from_user_id`,`total_diamonds`),
          KEY `from_user_id` (`from_user_id`,`total_diamonds`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_2` (`create_ym`,`from_user_id`,`total_diamonds`),
          KEY `to_user_id` (`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_3` (`create_ym`,`create_d`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
          KEY `idx_ecs_video_prop_cc_4` (`create_ym`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT='送礼物总表'";
        $res = $GLOBALS['db']->query($sql);
    }
    return $res ? $table : false;
}

/**
 * 计算奖金池等信息
 */
function crontab_do_award()
{
    if (intval(crontab_do_award)) {
        log_file("crontab_do_award_start", "award_crontab_do_award");
    }
    $root = array();
    $list_award = get_award_info();
    //$root['list_award'] = $list_award;
    $sql = "SELECT sum(total_diamonds) as total_diamonds FROM " . DB_PREFIX . "video_prop_all where create_time > " . $list_award['award_updatetime'] . " and is_award=1";
    //$root['sql'] = $sql;
    $total_diamonds = $GLOBALS['db']->getOne($sql, true, true);
    $root['total_diamonds'] = intval($total_diamonds); //上次更新到现在的礼物秀豆总数
    $new_award_diamonds = intval($total_diamonds * floatval($list_award['award_pool_ratio'] * 0.01));
    $root['new_award_diamonds'] = intval($new_award_diamonds); //上次更新到现在的 需要累加的奖池秀豆数量

    if ($new_award_diamonds) {
        $res = award_update('award_pool', $new_award_diamonds);
        if ($res) {
            $amount = intval($new_award_diamonds * floatval($list_award['award_ratio'] * 0.01));
            if ($amount) {
                $res2 = award_update('amount', $amount);
            }
        }
    }
    if ($res2) {
        award_update('award_updatetime', NOW_TIME, 1);
    }
    if (intval(crontab_do_award)) {
        log_file("crontab_do_award_end", "award_crontab_do_award");
    }
    return $root;
}

/**
 * 更新中奖礼物配置表
 * @param $code
 * @param $val
 * @param int $type
 * @return bool
 */
function award_update($code, $val, $type = 0)
{
    if (intval(award_update)) {
        log_file("award_update_start", "award_award_update");
    }
    $res = false;
    $code_arr = array('award_pool', 'amount', 'award_updatetime', 'used_amount');
    if (in_array($code, $code_arr)) {
        $table = DB_PREFIX . "award_config";
        if ($type) {
            $sql = "update " . $table . " set val='$val' where `code` = '" . $code . "'";
        } else {
            $sql = "update " . $table . " set val=val+'$val' where `code` = '" . $code . "'";
        }
        $res = $GLOBALS['db']->query($sql);
    }
    if (intval(award_update)) {
        log_file("award_update_end", "award_update_award");
    }
    return $res;
}

/**
 * 获取award 礼物中奖配置信息
 * @return mixed
 */
function get_award_info()
{
    if (intval(get_award_info)) {
        log_file("get_award_info_start", "award_get_award_info");
    }
    $sql = "select code,val from " . DB_PREFIX . "award_config";
    $award_config = $GLOBALS['db']->getAll($sql);
    $list_award = array();
    foreach ($award_config as $item) {
        $list_award[$item['code']] = $item['val'];
    }
    if (intval(get_award_info)) {
        log_file("get_award_info_end", "award_get_award_info");
    }
    return $list_award;
}

/**
 * 获取中奖的倍数设置表
 * @return mixed
 */
function get_award_multiple()
{
    if (intval(get_award_multiple)) {
        log_file("get_award_multiple_start", "award_get_award_multiple");
    }
    // $sql = "select * from " . DB_PREFIX . "award_multiple";
    $list_multiple = load_auto_cache('award_list');
    if (intval(get_award_multiple)) {
        log_file("get_award_multiple_end", "award_get_award_multiple");
    }
    return $list_multiple;
}

/**
 * 礼物中奖处理
 * @param $user_id
 * @param $prop
 * @param $video_id
 */
function gift_award($award_info, $prop, $total_diamonds = 0)
{
    $root = array('status' => 0, 'error' => '');
    // if (intval(gift_award)) {
    //     log_file("gift_award_start", "award_gift_award");
    //     log_file("award_info", "award_gift_award");
    //     log_file($award_info, "award_gift_award");
    // }
    $user_id = $award_info['user_id'];
    $video_id = $award_info['video_id'];
    $group_id = $award_info['group_id'];
    $from_ip = $award_info['from_ip'];
    $podcast_id = $award_info['podcast_id'];

    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
    $user_redis = new UserRedisService();
    $user_info = $user_redis->getRow_db($user_id, array('nick_name', 'head_image', 'user_level', 'v_icon'));

    $m_config = load_auto_cache('m_config');
    // if (intval(gift_award)) {
    //     log_file("user_info", "award_gift_award");
    //     log_file($user_info, "award_gift_award");
    // }
    //礼物中奖配置信息
    $list_award = get_award_info();
    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/AwardRedisService.php');
    $award_redis = new AwardRedisService();
    // 总秀豆数量 加上本次的秀豆数量
    $award_pool = (string) $award_redis->diamonds($total_diamonds); //bcadd($list_award['award_pool'], $total_diamonds);
    // 有秀豆才执行
    if ($award_pool > 0) {
        // 获取所有的规则，计算中间数据
        $award_multiple = get_award_multiple();

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/prop_notify.php');
        $root['winning_diamonds'] = 0;
        foreach ($award_multiple as &$_value) {
            // 锁住当前的这个倍率，防止多加载
            $award_redis->asyncWork('multiple:' . $_value['multiple'], function () use (&$_value, &$award_pool, &$award_redis, &$total_diamonds, &$prop, &$list_award, &$user_id, &$video_id, &$podcast_id, &$group_id, &$from_ip, &$user_info, &$user_redis, &$m_config, &$root) {
                // 获取整型的商
                $quotient = bcdiv($_value['molecular'], $_value['denominator']);
                // 计算当前的次数
                $num = bcdiv($award_pool, $quotient);
                // 余数
                // $mod = bcmod($award_pool, $quotient);
                // 判断该概率是否中奖 谁第一个拿到区间就是中奖
                if (($num > 0) && (!$award_redis->has_num($_value['multiple'], $quotient, $num))) {
                    $award_redis->set_num($_value['multiple'], $quotient, $num);
                    // 计算中奖次数
                    $win_num = (bcdiv($total_diamonds, $quotient) >> 0) ?: 1;
                    // 是否超过设定的上限
                    $win_num = (($win_num > $_value['max_num']) ? $_value['max_num'] : (($win_num < 1) ? 1 : $win_num));
                    //理论奖励秀豆
                    $award_award_s = bcmul(bcmul($prop['diamonds'], $_value['multiple']), $win_num);
                    // 进行中奖日计次
                    // fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/AwardRedisService.php');
                    // $award_redis = new AwardRedisService();
                    // if ($award_redis->get($user_id) >= $list_award['award_limit']) {
                    //      return;
                    // }
                    // $award_redis->inc($user_id);

                    //扣除手续费
                    $award_platform_fee = 0;
                    if (intval($list_award['award_platform_fee']) > 0) {
                        $award_platform_fee = bcmul($award_award_s, bcdiv($list_award['award_platform_fee'], 100, 2)); //向下取整
                    }
                    //增加：秀豆 数量
                    //实际获取秀豆
                    $award_award = intval($award_award_s - $award_platform_fee);
                    if ($award_award > 0) {
                        $pInTrans = $GLOBALS['db']->StartTrans();
                        try {
                            $sql = "UPDATE " . DB_PREFIX . "user set diamonds = diamonds + " . $award_award . " where id = " . $user_id;
                            $GLOBALS['db']->query($sql);
                            if ($GLOBALS['db']->affected_rows()) {
                                //写入用户日志
                                $data = array();
                                $data['diamonds'] = $award_award;
                                $data['video_id'] = intval($video_id) > 0 ? $video_id : 0;
                                $param['type'] = 12; //类型 0表示充值 1表示提现 2赠送道具 3兑换秀票 4分享获得秀票 5登录赠送积分 6观看付费直播 7游戏收益8扣除公会收益9分销收益10公会长收益11平台收益
                                $log_msg = '送礼物《' . $prop['name'] . '》中奖,获得' . $_value['multiple'] . "倍大奖{$win_num}次，扣除手续费" . $award_platform_fee . "秀豆，最终获取" . $award_award . '秀豆';
                                account_log_com($data, $user_id, $log_msg, $param);
                                // 中奖日志
                                $award_prop = array();
                                $award_prop['user_id'] = $user_id; //中奖用户ID
                                $award_prop['prop_id'] = intval($prop['id']); //礼物ID
                                $award_prop['to_user_id'] = $podcast_id; //直播间ID
                                $award_prop['video_id'] = $video_id; //直播间ID
                                $award_prop['group_id'] = $group_id; //聊天组ID
                                $award_prop['award_pool'] = $list_award['award_pool']; //当前资金池资金总数
                                $award_prop['award_ratio'] = intval($list_award['award_ratio']); //转换可用资金比例
                                $award_prop['usable_amount'] = intval($list_award['amount']) - intval($list_award['used_amount']); //实际可用奖金 = 理论可用金额-已用金额
                                $award_prop['commission_charge_ratio'] = intval($list_award['award_platform_fee']); //平台手续比例
                                $award_prop['commission_charge'] = $award_platform_fee; //平台手续 (中奖金额*平台手续费)
                                $award_prop['receive_bonus'] = $award_award; //实际到账奖金 = 中奖金额-手续费
                                $award_prop['create_time'] = NOW_TIME; //创建时间
                                $award_prop['create_date'] = "'" . to_date($award_prop['create_time'], 'Y-m-d') . "'";
                                $award_prop['create_ym'] = to_date($award_prop['create_time'], 'Ym');
                                $award_prop['create_d'] = to_date($award_prop['create_time'], 'd');
                                $award_prop['create_w'] = to_date($award_prop['create_time'], 'W');
                                $award_prop['from_ip'] = $from_ip;
                                $award_prop['winning_num'] = $_value['multiple'];
                                $award_prop['num'] = $win_num;
                                //将中奖信息写入mysql表中
                                $field_arr = array('user_id', 'prop_id', 'to_user_id', 'video_id', 'group_id', 'award_pool', 'award_ratio', 'usable_amount', 'commission_charge_ratio', 'commission_charge', 'receive_bonus', 'create_time', 'create_date', 'create_ym', 'create_d', 'create_w', 'from_ip', 'winning_num', 'num'
                                );
                                $fields = implode(",", $field_arr);
                                $valus = implode(",", $award_prop);

                                $table = DB_PREFIX . 'award_log';
                                $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
                                $GLOBALS['db']->query($sql);
                                $award_log_id = $GLOBALS['db']->insert_id();
                            }
                            //提交事务,不等 消息推送,防止锁太久
                            $GLOBALS['db']->Commit($pInTrans);
                            $pInTrans = false; //防止，下面异常时，还调用：Rollback
                            //IM消息推送
                            if (!empty($award_log_id)) {
                                //取消独立推送IM
                                // $data = array();
                                // $data['video_id'] = $video_id;
                                // $data['user_id'] = $user_id;
                                // $data['nick_name'] = $user_info['nick_name'];
                                // $data['icon'] = $prop['icon'];
                                // $data['desc'] = $user_info['nick_name'] . "送礼物" . $prop['name'] . "中奖了"; //普通群员收到的提示内容;
                                // $data['desc2'] = "你送" . $prop['name'] . "中奖了！"; //中奖接收人（中奖的观众）收到的提示内容;
                                // $data['prop_name'] = $prop['name'];
                                // $data['group_id'] = $group_id;
                                // $data['award_log_id'] = $award_log_id;
                                // $im_res = push_award_im($data);
                                $root['status'] = 1;
                                $root['award_log_id'] = $award_log_id; //中奖纪录ID
                                if ($_value['multiple'] >= $list_award['big_prize_limit']) {
                                    $winning_type = 2;
                                } else {
                                    $winning_type = 1;
                                }
                                $root['winning_type'] = intval($winning_type); //中奖类型 1 小 2 大
                                $root['winning_num'] = $_value['multiple']; //中奖倍数
                                $root['winning_diamonds'] += $award_award; //中奖金额
                                $root['prop_id'] = intval($award_prop['prop_id']); //中奖礼物iD
                                $root['user_id'] = $user_id; //中奖人id
                                $root['is_animated'] = $_value['is_animated'];
                                $root['svg_file'] = $_value['svg_file'];
                                $root['gif_gift_show_style'] = 1;
                                $root['desc'] = $user_info['nick_name'] . "送礼物" . $prop['name'] . "中奖了"; //普通群员收到的提示内容;
                                $root['desc2'] = "你送" . $prop['name'] . "中奖了！"; //中奖接收人（中奖的观众）收到的提示内容;
                                $root['anim_cfg'] = load_auto_cache('award_multiple', array('multiple' => $_value['multiple']));
                                $root['award_list'][] = array(
                                    'is_animated' => $_value['is_animated'],
                                    'svg_file' => $_value['svg_file'],
                                    'anim_cfg' => load_auto_cache('award_multiple', array('multiple' => $_value['multiple']))
                                );
                                $podcast_info = $user_redis->getRow_db($podcast_id, array('nick_name', 'head_image'));
                                $desc = '好运连连的【' . $user_info['nick_name'] . '】在【' . $podcast_info['nick_name'] . '】的直播间中得' . $_value['multiple'] . '倍大奖' . $win_num . '次，获得' . $award_award . $m_config['diamonds_name'];
                                $sender = array('user_id' => $user_id, 'nick_name' => $user_info['nick_name'], 'head_image' => get_spec_image($user_info['head_image']), 'user_level' => $user_info['user_level'], 'v_icon' => $user_info['v_icon']);
                                SendVideoMsg($video_id, $desc, $sender);
                                propNotify($sender, array('data_type' => 0, 'num' => 1, 'is_plus' => 0, 'is_much' => 0, 'room_id' => $video_id, 'app_plus_num' => 0, 'is_animated' => 1, 'head_image' => get_spec_image($podcast_info['head_image']), 'prop_id' => intval($award_prop['prop_id']), 'icon' => get_spec_image($prop['icon']), 'to_user_id' => $podcast_id, 'anim_type' => ''), $prop, $desc); //执行全服通告
                            }
                        } catch (Exception $e) {
                            //异常回滚
                            $root['error'] = $e->getMessage();
                            $root['status'] = 0;
                            $GLOBALS['db']->Rollback($pInTrans);
                        }
                    }
                }
            }, function () {});

        }
    }

    //中奖礼物数量
    // $video_prop_all = gift_totals($prop);
    // if (intval(gift_award)) {
    //     log_file("video_prop_all", "award_gift_award");
    //     log_file($video_prop_all, "award_gift_award");
    // }
    // //条件 1:中奖礼物数量大于后台设置的中奖添加；2、中奖礼物数量是后台设置的中奖礼物数量的倍数 如：后台设置1000个，中奖礼物数量是 2000个，即视为中奖。
    // /**测试开始**/
    // //$video_prop_all['totals'] = 100;
    // /**测试数据结束**/
    // $is_standard = $video_prop_all['totals'] % intval($list_award['award_condition']);
    // if ($video_prop_all['totals'] > intval($list_award['award_condition']) && $is_standard == 0) {
    //     //摇奖处理 返回中奖倍数
    //     $result = do_prize();
    //     if (intval(gift_award)) {
    //         log_file("条件", "award_gift_award");
    //         log_file(intval($video_prop_all['totals']), "award_gift_award");
    //         log_file(intval($list_award['award_condition']), "award_gift_award");
    //         log_file('intval($list_award[\'totals\'])%intval($video_prop_all[\'award_condition\'])', "award_gift_award");
    //         log_file(intval($video_prop_all['totals']) % intval($list_award['award_condition']), "award_gift_award");
    //         log_file("result", "award_gift_award");
    //         log_file($result, "award_gift_award");
    //     }
    //     /**测试数据开始**/
    //     /*if(1){
    //     $a = array(10,100,200,500,999);
    //     $b = rand(0,4);
    //     $result =$a[$b];
    //     }*/
    //     /**测试数据结束**/

    //     if ($result > 0) {
    //         if (intval(gift_award)) {
    //             log_file("奖励秀豆", "award_gift_award");
    //             log_file($result, "award_gift_award");
    //         }
    //         //理论奖励秀豆
    //         $award_award_s = intval($prop['diamonds'] * $result);
    //         // 进行中奖日计次
    //         fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/AwardRedisService.php');
    //         $award_redis = new AwardRedisService();
    //         if ($award_redis->get($user_id) >= $list_award['award_limit']) {
    //             return;
    //         }
    //         $award_redis->inc($user_id);
    //         //是否大于可用资金，大于系统可用资金 不中奖。
    //         // if ($award_award_s > intval($list_award['amount'] - $list_award['used_amount']) || $list_award['amount'] <= $list_award['used_amount']) {
    //         //     $root['status'] = 0;
    //         //     return $root;
    //         // }
    //         //开启事务
    //         $pInTrans = $GLOBALS['db']->StartTrans();
    //         try {
    //             //扣除手续费
    //             $award_platform_fee = 0;
    //             if (intval($list_award['award_platform_fee']) > 0) {
    //                 $award_platform_fee_r = intval($list_award['award_platform_fee']) * 0.01;
    //                 $award_platform_fee = floor($award_award_s * $award_platform_fee_r); //向下取整
    //             }
    //             if (intval(gift_award)) {
    //                 log_file("手续费", "award_gift_award");
    //                 log_file($award_platform_fee_r, "award_gift_award");
    //                 log_file($award_platform_fee, "award_gift_award");
    //             }
    //             //增加：秀豆 数量
    //             //实际获取秀豆
    //             $award_award = intval($award_award_s - $award_platform_fee);
    //             $sql = "UPDATE " . DB_PREFIX . "user set diamonds = diamonds + " . $award_award . " where id = " . $user_id;
    //             if (intval(gift_award)) {
    //                 log_file("奖励秀豆", "award_gift_award");
    //                 log_file($award_award, "award_gift_award");
    //                 log_file($sql, "award_gift_award");
    //             }
    //             $GLOBALS['db']->query($sql);
    //             if ($GLOBALS['db']->affected_rows()) {
    //                 //更新已用资金
    //                 // $res2 = award_update('used_amount', $award_award_s);

    //                 //写入用户日志
    //                 $data = array();
    //                 $data['diamonds'] = $award_award;
    //                 $data['video_id'] = intval($video_id) > 0 ? $video_id : 0;
    //                 $param['type'] = 12; //类型 0表示充值 1表示提现 2赠送道具 3兑换秀票 4分享获得秀票 5登录赠送积分 6观看付费直播 7游戏收益8扣除公会收益9分销收益10公会长收益11平台收益
    //                 $log_msg = '送礼物《' . $prop['name'] . '》中奖,获得' . $result . "倍大奖，扣除手续费" . $award_platform_fee . "秀豆，最终获取" . $award_award . '秀豆';
    //                 account_log_com($data, $user_id, $log_msg, $param);

    //                 //中奖日志
    //                 $award_prop = array();
    //                 $award_prop['user_id'] = $user_id; //中奖用户ID
    //                 $award_prop['prop_id'] = intval($prop['id']); //礼物ID
    //                 $award_prop['to_user_id'] = $podcast_id; //直播间ID
    //                 $award_prop['video_id'] = $video_id; //直播间ID
    //                 $award_prop['group_id'] = $group_id; //聊天组ID
    //                 $award_prop['award_pool'] = $list_award['award_pool']; //当前资金池资金总数
    //                 $award_prop['award_ratio'] = intval($list_award['award_ratio']); //转换可用资金比例
    //                 $award_prop['usable_amount'] = intval($list_award['amount']) - intval($list_award['used_amount']); //实际可用奖金 = 理论可用金额-已用金额
    //                 $award_prop['commission_charge_ratio'] = intval($list_award['award_platform_fee']); //平台手续比例
    //                 $award_prop['commission_charge'] = $award_platform_fee; //平台手续 (中奖金额*平台手续费)
    //                 $award_prop['receive_bonus'] = $award_award; //实际到账奖金 = 中奖金额-手续费
    //                 $award_prop['create_time'] = NOW_TIME; //创建时间
    //                 $award_prop['create_date'] = "'" . to_date($award_prop['create_time'], 'Y-m-d') . "'";
    //                 $award_prop['create_ym'] = to_date($award_prop['create_time'], 'Ym');
    //                 $award_prop['create_d'] = to_date($award_prop['create_time'], 'd');
    //                 $award_prop['create_w'] = to_date($award_prop['create_time'], 'W');
    //                 $award_prop['from_ip'] = $from_ip;
    //                 $award_prop['winning_num'] = $result;
    //                 //将中奖信息写入mysql表中
    //                 $field_arr = array(
    //                     'user_id',
    //                     'prop_id',
    //                     'to_user_id',
    //                     'video_id',
    //                     'group_id',
    //                     'award_pool',
    //                     'award_ratio',
    //                     'usable_amount',
    //                     'commission_charge_ratio',
    //                     'commission_charge',
    //                     'receive_bonus',
    //                     'create_time',
    //                     'create_date',
    //                     'create_ym',
    //                     'create_d',
    //                     'create_w',
    //                     'from_ip',
    //                     'winning_num'
    //                 );
    //                 $fields = implode(",", $field_arr);
    //                 $valus = implode(",", $award_prop);

    //                 $table = DB_PREFIX . 'award_log';
    //                 $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $valus . ")";
    //                 $GLOBALS['db']->query($sql);
    //                 $award_log_id = $GLOBALS['db']->insert_id();
    //             }
    //             //提交事务,不等 消息推送,防止锁太久
    //             $GLOBALS['db']->Commit($pInTrans);
    //             $pInTrans = false; //防止，下面异常时，还调用：Rollback
    //             //IM消息推送

    //             if ($award_log_id) {
    //                 //取消独立推送IM
    //                 // $data = array();
    //                 // $data['video_id'] = $video_id;
    //                 // $data['user_id'] = $user_id;
    //                 // $data['nick_name'] = $user_info['nick_name'];
    //                 // $data['icon'] = $prop['icon'];
    //                 // $data['desc'] = $user_info['nick_name'] . "送礼物" . $prop['name'] . "中奖了"; //普通群员收到的提示内容;
    //                 // $data['desc2'] = "你送" . $prop['name'] . "中奖了！"; //中奖接收人（中奖的观众）收到的提示内容;
    //                 // $data['prop_name'] = $prop['name'];
    //                 // $data['group_id'] = $group_id;
    //                 // $data['award_log_id'] = $award_log_id;
    //                 // $im_res = push_award_im($data);
    //                 $root['status'] = 1;
    //                 $root['award_log_id'] = $award_log_id; //中奖纪录ID
    //                 if ($result >= $list_award['big_prize_limit']) {
    //                     $winning_type = 2;
    //                 } else {
    //                     $winning_type = 1;
    //                 }
    //                 $root['winning_type'] = intval($winning_type); //中奖类型 1 小 2 大
    //                 $root['winning_num'] = $result; //中奖倍数
    //                 $root['winning_diamonds'] = $award_award; //中奖金额
    //                 $root['prop_id'] = intval($award_prop['prop_id']); //中奖礼物iD
    //                 $root['user_id'] = $user_id; //中奖人id
    //                 $root['is_animated'] = 1;
    //                 $root['gif_gift_show_style'] = 1;
    //                 $root['desc'] = $user_info['nick_name'] . "送礼物" . $prop['name'] . "中奖了"; //普通群员收到的提示内容;
    //                 $root['desc2'] = "你送" . $prop['name'] . "中奖了！"; //中奖接收人（中奖的观众）收到的提示内容;
    //                 $root['anim_cfg'] = load_auto_cache('award_multiple', array('multiple' => $result));
    //             }

    //         } catch (Exception $e) {
    //             //异常回滚
    //             $root['error'] = $e->getMessage();
    //             $root['status'] = 0;
    //             $GLOBALS['db']->Rollback($pInTrans);
    //         }
    //     }
    // }

    // if (intval(gift_award)) {
    //     log_file("root", "award_gift_award");
    //     log_file($root, "award_gift_award");
    //     log_file("gift_award_end", "award_gift_award");
    // }
    return $root;
}

/**
 * 统计中奖礼物数量
 * @param $prop
 * @return array
 */
function gift_totals($prop)
{
    if (intval(gift_totals)) {
        log_file("gift_totals_start", "award_gift_totals");
    }
    $root = array();
    $prop_id = intval($prop['id']);
    $sql = "SELECT COUNT(id) FROM " . DB_PREFIX . "video_prop_all where is_private = 0  and is_award = 1 and prop_id = " . $prop_id;
    if (intval(gift_totals)) {
        log_file($sql, "award_gift_totals");
    }
    $root['sql'] = $sql;
    $prop_num = $GLOBALS['db']->getOne($sql);
    if (intval(gift_totals)) {
        log_file($prop_num, "award_gift_totals");
    }
    $root['totals'] = $prop_num;
    if (intval(gift_totals)) {
        log_file("gift_totals_end", "award_gift_totals");
    }
    return $root;
}

/**
 * 抽奖处理
 * @return int
 */
function do_prize()
{
    if (intval(do_prize)) {
        log_file("do_prize_start", "award_do_prize");
    }
    fanwe_require(APP_ROOT_PATH . 'system/libs/lottery.php');
    //礼物中奖概率
    $list_multiple = get_award_multiple();
    if (intval(do_prize)) {
        log_file("list_multiple", "award_do_prize");
        log_file($list_multiple, "award_do_prize");
    }
    $lorrery = new LotteryBaseAction();
    $multiple_arr = $lorrery->get_rand($list_multiple, 100);
    if (intval(do_prize)) {
        log_file("multiple_arr", "award_do_prize");
        log_file($multiple_arr, "award_do_prize");
    }
    if (intval(do_prize)) {
        log_file("do_prize_end", "award_do_prize");
    }
    return $multiple_arr;
}

/**
 * 中奖消息推送
 * @param $data
 */
function push_award_im($data)
{
    if (intval(push_award_im)) {
        log_file("push_award_im_start", "award_push_award_im");
    }
    //数据整理
    $video_id = $data['video_id'];
    $user_id = $data['user_id'];
    $nick_name = $data['nick_name'];
    $icon = $data['icon'];
    $desc = $data['desc'];
    $desc2 = $data['desc2'];
    $prop_name = $data['prop_name'];
    $group_id = $data['group_id'];
    $award_log_id = $data['award_log_id'];
    $table = DB_PREFIX . 'award_log';

    //消息整理
    $ext = array();
    $ext['type'] = 60; // 60:礼物中奖 中奖信息推送到当前直播间
    $ext['room_id'] = $video_id; //直播ID 也是room_id;只有与当前房间相同时，收到消息才响应
    $ext['is_animated'] = 1; //1:动画；0：未动画

    //消息发送者
    $ext['icon'] = get_spec_image($icon); //图片，是否要: 大中小格式？
    $ext['to_user_id'] = $user_id; //中奖信息接收人（中奖的观众）
    $ext['fonts_color'] = ''; //字体颜色
    $ext['desc'] = $desc; //普通群员收到的提示内容;
    $ext['desc2'] = $desc2; //中奖接收人（中奖的观众）收到的提示内容;
    $ext['top_title'] = $nick_name . "送了," . $prop_name; //大型道具类型，标题;

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
    if ($ret['ActionStatus'] == 'FAIL' && $ret['ErrorCode'] == 10002) {
        //10002 系统错误，请再次尝试或联系技术客服。
        log_err_file(array(__FILE__, __LINE__, __METHOD__, $ret));
        $ret = $api->group_send_group_msg2($user_id, $group_id, $msg_content);
    }

    $GLOBALS['db']->autoExecute($table, $ret, 'UPDATE', 'id=' . $award_log_id);
    if (intval(push_award_im)) {
        log_file("push_award_im_end", "award_push_award_im");
    }
}
