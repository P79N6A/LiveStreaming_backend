<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class user_centerModule extends baseModule
{
    //
    /**
     * 我的等级 （H5 页面接口）
     */
    public function grade()
    {
        $root = array();
        $user_id = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        if ($user_id == 0) {
            $user_id = $GLOBALS['user_info']['id'];
        }
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data = $user_redis->getRow_db($user_id, array('id', 'score', 'online_time', 'user_level', 'ticket', 'refund_ticket', 'anchor_level'));
            user_leverl_syn($user_data);
            $user_level = load_auto_cache('user_level');
            array_multisort(array_column($user_level, 'level'), SORT_ASC, $user_level);
            $anchor_level = load_auto_cache('anchor_level');
            array_multisort(array_column($anchor_level, 'level'), SORT_ASC, $anchor_level);

            $level = $GLOBALS['db']->getRow("select ul.name as leve_name,ul.score as l_score from " . DB_PREFIX . "user_level as ul  where ul.level=" . intval($user_data['user_level']));
            $m_config = load_auto_cache("m_config");
            $level['u_score'] = $user_data['score'] + floor($user_data['online_time'] * floatval($m_config['onlinetime_to_experience']));
            $level['up_score'] = $GLOBALS['db']->getOne("select score from " . DB_PREFIX . "user_level  where level=" . intval($user_data['user_level'] + 1));
            if (intval($level['up_score']) <= 0) {
                $l_up_score = $GLOBALS['db']->getOne("select score from " . DB_PREFIX . "user_level  where level>" . intval($user_data['user_level'] + 1));
                if ($l_up_score['score'] > 0) {
                    $level['up_score'] = $l_up_score['score'];
                } else {
                    $level['up_score'] = '满级';
                }
            }
            // $user_data['user_level'] = $level['name'];

            $ticket = $GLOBALS['db']->getRow("select ul.name as anchor_leve_name,ul.ticket as l_ticket from " . DB_PREFIX . "anchor_level as ul  where ul.level=" . intval($user_data['anchor_level']));
            $m_config = load_auto_cache("m_config");
            $ticket['u_ticket'] = $user_data['ticket'] + $user_data['refund_ticket'];
            $ticket['up_ticket'] = $GLOBALS['db']->getOne("select ticket from " . DB_PREFIX . "anchor_level  where level=" . intval($user_data['anchor_level'] + 1));
            if (intval($ticket['up_ticket']) <= 0) {
                $l_up_ticket = $GLOBALS['db']->getOne("select ticket from " . DB_PREFIX . "anchor_level  where level>" . intval($user_data['anchor_level'] + 1));
                if ($l_up_ticket['ticket'] > 0) {
                    $ticket['up_ticket'] = $l_up_ticket['ticket'];
                } else {
                    $ticket['up_ticket'] = '满级';
                }
            }
            $root = $level + $ticket;
            $root['user_level'] = $user_level;
            $root['anchor_level'] = $anchor_level;
            $root['status'] = 1;
            $root['error'] = '';
        }
        $root['page_title'] = '等级';
        api_ajax_return($root);
    }

    /**
     * 我的收益-主页面
     */
    public function profit()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            //$profit= $GLOBALS['db']->getRow("select ticket,money,subscribe,wx_openid,mobile from ".DB_PREFIX."user where id=".$user_id);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $profit = $user_redis->getRow_db($user_id, array('ticket', 'money', 'subscribe', 'wx_openid', 'mobile', 'refund_ticket', 'binding_alipay', 'user_level', 'binding_bankcard'));
            $refund_role = load_auto_cache("refund_role_level", array('level' => $profit['user_level'])); //初始化手机端配置
            //$root = $profit;
            $root['status'] = 1;
            $root['error'] = '';
            $root['withdraw'] = 1; // 是否可以提现
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //提现比例 如果主播提现比例为空，则使用后台通用比例
            $root['ticket_catty_ratio'] = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=" . $user_id);
            if ($root['ticket_catty_ratio'] == '') {
                if (!empty($refund_role)) {
                    $root['ticket_catty_ratio'] = (float) $refund_role['ticket_catty_ratio'];
                } else {
                    $root['ticket_catty_ratio'] = (float) ($m_config['ticket_catty_ratio']);
                }
            }
            //公会长提现比例特殊ljz 工会结算修改在这里
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE == 1) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain']) == 1) {
                        $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        $root['ticket_catty_ratio'] = floatval($refund_rate);
                        if ($root['ticket_catty_ratio'] > 1 || $root['ticket_catty_ratio'] <= 0) {
                            $root['ticket_catty_ratio'] = $m_config['society_public_rate'];
                        }
                    } else if ($society_info['society_id']) {
                        $root['ticket_catty_ratio'] = $m_config['society_user_rate'];
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $ticket___ = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $profit['ticket'] += $ticket___;
                            } else {
                                // 非工会长是否支持提现功能
                                $root['withdraw'] = 0; // 是否可以提现
                            }
                        }
                    }
                }
            }

            $root['ticket'] = intval($profit['ticket']);
            $root['money'] = number_format(intval($profit['ticket'] - $profit['refund_ticket']) * floatval($root['ticket_catty_ratio']), 2);
            $root['useable_ticket'] = intval($profit['ticket'] - $profit['refund_ticket']);
            $root['subscribe'] = intval($profit['subscribe']);

            //最小提现秀票
            $root['ticket_catty_min'] = intval(intval($m_config['ticket_catty_min']) / floatval($root['ticket_catty_ratio']));
            //添加微信公众号名称
            $root['subscription'] = mb_strlen($m_config['subscription']) ? $m_config['subscription'] : '';

            //每日可提现秀票
            if (floatval($root['ticket_catty_ratio']) > 0) {
                $root['day_ticket_max'] = intval(intval($m_config['day_cash_max']) / floatval($root['ticket_catty_ratio']));
            }

            //是否有未处理的提现
            if ($GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "user_refund WHERE user_id = " . $user_id . " and (is_pay =0 or is_pay=1)") > 0) {
                $root['refund_exist'] = 1;
            } else {
                $root['refund_exist'] = 0;
            }

            if ($profit['wx_openid'] != '') {
                $root['binding_wx'] = 1;
            } else {
                $root['binding_wx'] = 0;
            }

            if ($profit['mobile'] != '') {
                $root['mobile_exist'] = 1;
            } else {
                $root['mobile_exist'] = 0;
            }

            if (OPEN_PAI_MODULE == 1) {
                $rs = FanweServiceCall("user_center", "profit", array("user_id" => $user_id));
            }
            //$rs = FanweServiceCall("user_center","profit",array("user_id"=>$user_id));
            //$rs['pai_income_done'] = 0;
            //$rs['pai_income_undone'] = 0;
            $root['show_pai_ticket'] = 0;
            $root['pai_ticket'] = intval($rs['pai_income_done']);
            $root['pai_wait_ticket'] = intval($rs['pai_income_undone']);

            $root['show_goods_ticket'] = 0;
            $root['goods_ticket'] = intval($rs['goods_income_done']);
            $root['goods_wait_ticket'] = intval($rs['goods_income_undone']);

            //是否绑定支付宝 0指未绑定， 1指已绑定
            $root['binding_alipay'] = intval($profit['binding_alipay']);
            // 是否绑定了银行卡
            $root['binding_bankcard'] = intval($profit['binding_bankcard']);

            //提现开启或关闭 1：开启 0：关闭
            $root['is_refund'] = $m_config['is_refund'];

            if ((intval($m_config['is_refund']) == 1) && ($root['withdraw'] == 1)) {
                //提现是开启的
                //开启微信还是支付宝提现
                //提现是关闭的，微信支付宝均设为0
                $root['withdrawals_wx'] = intval($m_config['withdrawals_wx']);
                $root['withdrawals_alipay'] = intval($m_config['withdrawals_alipay']);
                $root['withdrawals_bankcard'] = intval($m_config['withdrawals_bankcard']);
                $root['withdrawals_name'] = '';
            } else {
                //提现是关闭的，微信支付宝均设为0
                $root['withdrawals_wx'] = 0;
                $root['withdrawals_alipay'] = 0;
                $root['withdrawals_bankcard'] = 0;
                $root['withdrawals_name'] = '';
            }
            //提现说明
            $root['refund_explain'] = array();
            $refund_arr = explode("<br />", nl2br($m_config['refund_explain']));
            foreach ($refund_arr as $k => $v) {
                $v = ltrim(rtrim(trim($v)));
                if ($v != '') {
                    $root['refund_explain'][] = $v;
                }
            }
        }
        api_ajax_return($root);
    }

    /**
     * 我的收益-兑换规则列表
     */
    public function exchange()
    {
        $root = array('status' => 1, 'error' => '');
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $exchange_rules = $video_new = $GLOBALS['db']->getAll("SELECT er.id,er.diamonds,er.ticket FROM " . DB_PREFIX . "exchange_rule as er where is_effect=1 and is_delete=0 order by er.diamonds");
            $root['exchange_rules'] = $exchange_rules;

            //$user =  $GLOBALS['db']->getRow("select ticket,diamonds from ".DB_PREFIX."user where id=".$GLOBALS['user_info']['id'],true,true);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user = $user_redis->getRow_db($GLOBALS['user_info']['id'], array('ticket', 'diamonds', 'refund_ticket'));

            $GLOBALS['user_info']['ticket'] = intval($user['ticket']);
            $root['ticket'] = intval($user['ticket']);

            $GLOBALS['user_info']['refund_ticket'] = intval($user['refund_ticket']);
            $root['refund_ticket'] = intval($user['refund_ticket']); //已使用的秀票

            $GLOBALS['user_info']['diamonds'] = intval($user['diamonds']);
            $root['diamonds'] = intval($user['diamonds']);
            $root['useable_ticket'] = intval($user['ticket'] - $user['refund_ticket']);
            //兑换规则
            //$ratio = floatval(app_conf('TICKET_CATTY_RATIO'));
            $ratio = $m_config['exchange_rate'];
            $root['ratio'] = $ratio;

            $m_config = load_auto_cache("m_config");
            $exchange_rate = floatval($m_config['exchange_rate']);
            //兑换最低票数
            if ($exchange_rate > 0) {
                $min_ticket = floatval(1 / $exchange_rate);
                $root['min_ticket'] = $min_ticket;
            } else {
                $root['min_ticket'] = 0;
            }
        }
        ajax_return($root);
    }

    /**
     * 我的收益-兑换功能
     */
    public function do_exchange()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //登录用户
            $is_live = $GLOBALS['db']->getRow("select user_id,live_in from " . DB_PREFIX . "video where  user_id=" . $user_id . " order by id desc");
            //异常退出后，等待家族长抽成完成后才能提现ljz
            if (!empty($is_live) && $is_live['live_in'] == 1) {
                $root['status'] = 0;
                $root['error'] = '由于您的视频还在直播，暂时无法兑换，请稍后再试';
            } else {
                $m_config = load_auto_cache("m_config"); //初始化手机端配置

                //$user =  $GLOBALS['db']->getRow("select ticket,diamonds from ".DB_PREFIX."user where id=".$user_id);
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user = $user_redis->getRow_db($user_id, array('ticket', 'diamonds', 'refund_ticket'));

                //无抽成模式下会长会员都不可以兑换秀豆ljz
                if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {
                    if ($m_config['society_pattern'] == 2) {
                        $society_id = $GLOBALS['db']->getOne("SELECT society_id FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
                        if ($society_id) {
                            $root['status'] = 0;
                            $root['error'] = '你有公会无法将秀票兑换成秀豆';
                            ajax_return($root);
                        }
                    } else if ($m_config['society_pattern'] == 3) {
                        // 独立模式
                        $society_info = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user where id=" . $user_id);
                        // 是否加入公会
                        if (!empty($society_info['society_id'])) {
                            // 判断是否工会长
                            $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                            if ($is_withdraw == 2) {
                                // 工会长获取手下所有用户的钱钱
                                if (intval($society_info['society_chieftain']) == 1) {
                                    $ticket_ = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                    $user['ticket'] += $ticket_;
                                } else {
                                    // 非工会长是否支持提现功能
                                    $root['status'] = 0;
                                    $root['error'] = '你有公会无法将秀票兑换成秀豆';
                                    ajax_return($root);
                                }
                            }
                        }
                    }
                }
                $rule_id = intval($_REQUEST['rule_id']); //100
                $ticket = intval($_REQUEST['ticket']); //250000

                if ($user['ticket'] - $user['refund_ticket'] < $ticket) {
                    $root['status'] = 0;
                    $root['error'] = $m_config['ticket_name'] . '不足';
                    $root['ticket'] = $user['ticket'];
                    $root['refund_ticket'] = $user['refund_ticket'];
                    $root['diamonds'] = $user['diamonds'];
                    ajax_return($root);
                }
                if ($rule_id > 0) {
                    //兑换规则
                    $exchange_rule = $video_new = $GLOBALS['db']->getRow("select er.* from " . DB_PREFIX . "exchange_rule as er where is_effect=1 and is_delete=0 and id = " . $rule_id . " and ticket=" . $ticket);
                    if ($exchange_rule) {
                        $diamonds = intval($exchange_rule['diamonds']);

                        $ticket = intval($exchange_rule['ticket']);
                    } else {
                        $root['status'] = 0;
                        $root['error'] = '兑换出错';
                        $root['ticket'] = $user['ticket'];
                        $root['refund_ticket'] = $user['refund_ticket'];
                        $root['diamonds'] = $user['diamonds'];
                        ajax_return($root);
                    }
                } else {
                    //自定义兑换
                    //$ratio = app_conf('TICKET_CATTY_RATIO');
                    $ratio = $m_config['exchange_rate'];
                    $diamonds = intval($ticket * $ratio);
                }

                if ($diamonds > 0) {
                    //使用兑换列表的值
                    $sql = "update " . DB_PREFIX . "user set refund_ticket=refund_ticket+" . $ticket . ",diamonds=diamonds+" . $diamonds . " where ticket >= refund_ticket + " . $ticket . " and id=" . $user_id;
                    $GLOBALS['db']->query($sql);
                    if ($GLOBALS['db']->affected_rows()) {
                        //redis 更新信息
                        user_deal_to_reids(array($user_id));

                        $exchange_log = array();
                        $exchange_log['user_id'] = $user_id;
                        $exchange_log['rule_id'] = $rule_id;
                        $exchange_log['diamonds'] = $diamonds;
                        $exchange_log['ticket'] = $ticket;
                        $exchange_log['create_time'] = get_gmtime();
                        $exchange_log['is_success'] = 1;
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "exchange_log", $exchange_log, "INSERT", "", "SILENT");
                        //写入用户日志
                        $data = array();
                        $data['diamonds'] = intval($diamonds);
                        $data['ticket'] = intval($ticket);
                        $param['type'] = 3; //类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票
                        $ticket_name = $m_config['ticket_name'] != '' ? $m_config['ticket_name'] : '秀票';
                        $log_msg = $ticket . $ticket_name . '兑换成' . $diamonds . '秀豆';
                        account_log_com($data, $user_id, $log_msg, $param);
                        $root['error'] = '兑换成功';
                        $root['status'] = 1;
                    } else {
                        $exchange_log['is_success'] = 0;
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "exchange_log", $exchange_log, "INSERT", "", "SILENT");
                        $root['status'] = 0;
                        $root['error'] = '兑换失败';
                    }
                }
            }
        }
        //获取新的秀票
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_new_info = $user_redis->getRow_db($user_id, array('ticket', 'diamonds', 'refund_ticket'));

        $GLOBALS['user_info']['ticket'] = $user_new_info['ticket'];
        $GLOBALS['user_info']['refund_ticket'] = $user_new_info['refund_ticket'];
        $GLOBALS['user_info']['diamonds'] = $user_new_info['diamonds'];

        $root['ticket'] = $user_new_info['ticket'];
        $root['refund_ticket'] = $user_new_info['refund_ticket'];
        $root['diamonds'] = $user_new_info['diamonds'];
        $root['useable_ticket'] = intval($user_new_info['ticket'] - $user_new_info['refund_ticket']);
        ajax_return($root);
    }

    /**
     * 微信提现页面
     */
    public function money_carry_wx()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if (intval($m_config['is_refund']) != 1 || intval($m_config['withdrawals_wx']) == 0) {
                $root['status'] = 0;
                $root['error'] = '微信提现已经关闭!';
                ajax_return($root);
            }
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $ticket_info = $user_redis->getRow_db($user_id, array('ticket', 'refund_ticket', 'user_level'));

            //如果开启公会并且开启无抽成模式，支付宝 将无法提现ljz
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE == 1) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain']) == 1) {
                        $root['error'] = '会长提现，请联系客服!';
                        $root['status'] = 0;
                        ajax_return($root);
                    } elseif (intval($society_info['society_id'])) {
                        $root['error'] = '公会成员不允许提现!';
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $__ticket = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $ticket_info['ticket'] += $__ticket;
                            } else {
                                $root['error'] = '公会成员不允许提现!';
                                $root['status'] = 0;
                                ajax_return($root);
                            }
                        }
                    }
                }
            }

            $refund_role = load_auto_cache("refund_role_level", array('level' => $ticket_info['user_level'])); //初始化手机端配置

            $ready_refund_ticket = floatval($GLOBALS['db']->getOne("select sum(ticket) from " . DB_PREFIX . "user_refund where user_id = " . intval($GLOBALS['user_info']['id']) . " and is_pay in (0,1,3)"));

            if (!empty($refund_role)) {
                $bank_info['ratio'] = (float) $refund_role['ticket_catty_ratio'];
            } else {
                $bank_info['ratio'] = (float) ($m_config['ticket_catty_ratio']);
            }
            $bank_info['can_use_ticket'] = $ticket_info['ticket'] - $ticket_info['refund_ticket'] - $ready_refund_ticket;
            $bank_info['ready_refund_ticket'] = $ready_refund_ticket;

            $root = $bank_info;
            $root['status'] = 1;
            $root['error'] = '';
        }
        ajax_return($root);
    }

    /**
     * 微信提现处理
     */
    public function submitrefundwx()
    {
        $root = array();
        $id = intval($_REQUEST['id']);
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $user_info_wx = es_session::get("user_info_wx");
        $user_info = $user_info_wx[$id];
        $refresh = SITE_DOMAIN . '/mapi/index.php?ctl=wx_bind';
        //$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($id));
        $tips = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><title>微信提现</title><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0,minimum-scale=0.5"><meta http-equiv="refresh" content="1; url=' . $refresh . '"><link rel="shortcut icon" href="/favicon.ico"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"></head><body style="background:#f0f7f6;">xxxx</body></html>';
        if (!$user_info) {
            $error = "用户未登陆,请先登陆.";
            echo str_replace('xxxx', $error, $tips);return;
        } else {

            // 进入每月提现一次流程
            $month_carry_one = intval($m_config['month_carry_one']) ? 1 : 0;
            $day_carry_one = intval($m_config['day_carry_one']) ? 1 : 0;

            if ($month_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user_refund where user_id = " . intval($user_info['id']) . " and is_pay = 3 ORDER BY id DESC LIMIT 1");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ym") == to_date(get_gmtime(), 'Ym'))) {
                    $error = '每月只能提现一次，本月已经提现过了';
                    echo str_replace('xxxx', $error, $tips);return;
                } else {
                    //查看本月允许提现时间
                    if (to_date(get_gmtime(), 'd') < intval($m_config['month_carry_min']) || to_date(get_gmtime(), 'd') > intval($m_config['month_carry_max'])) {
                        $error = '每月' . intval($m_config['month_carry_min']) . '日到' . intval($m_config['month_carry_max']) . '日才能提现';
                        echo str_replace('xxxx', $error, $tips);return;
                    }
                }
            }

            if ($day_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user_refund where user_id = " . intval($user_info['id']) . " and is_pay = 3");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ymd") == to_date(get_gmtime(), 'Ymd'))) {
                    $error = '每日只能提现一次，本日已经提现过了';
                    echo str_replace('xxxx', $error, $tips);return;
                }
            }

            $ready_refund_id = intval($GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_refund where user_id = " . intval($user_info['id']) . " and (is_pay =0 or is_pay=1)"));

            if ($ready_refund_id) {
                $error = '您还有未处理的提现！';
                echo str_replace('xxxx', $error, $tips);return;
            }

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_ticket_info = $user_redis->getRow_db($user_info['id'], array('ticket', 'refund_ticket', 'user_level'));
            $refund_role = load_auto_cache("refund_role_level", array('level' => $user_ticket_info['user_level'])); //初始化手机端配置

            $memo = "微信提现";
            //可用秀票
            $use_ticket = $user_ticket_info['ticket'] - $user_ticket_info['refund_ticket'];
            //今日可提现金额
            $s_now_time = to_timespan(to_date(NOW_TIME, "Y-m-d 00:00:00"));
            $e_now_time = to_timespan(to_date(NOW_TIME, "Y-m-d 23:59:59"));
            $ready_refund_info = $GLOBALS['db']->getOne("select sum(money) from " . DB_PREFIX . "user_refund where user_id = " . intval($user_info['id']) . " and is_pay = 3 and pay_time>=" . $s_now_time . " and pay_time<=" . $e_now_time);
            $use_cash_money = floatval(floatval($m_config['day_cash_max']) - $ready_refund_info);
            //提现比例 如果主播提现比例为空，则使用后台通用比例
            $rate = $GLOBALS['db']->getOne("SELECT alone_ticket_ratio FROM " . DB_PREFIX . "user where id=" . intval($user_info['id']));
            if ($rate == '') {
                if (!empty($refund_role)) {
                    $rate = $refund_role['ticket_catty_ratio'];
                } else {
                    $rate = $m_config['ticket_catty_ratio'];
                }
            }
            if (defined('OPEN_BM') && OPEN_BM) {
                $bm_config = load_auto_cache("bm_config");
                $bm_pid = intval($bm_config['bm_pid']);
                $bm_user = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id=" . intval($user_info['id']) . " and( bm_pid=" . $bm_pid . " or bm_pid=0)");
                $bm_promoter = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "bm_promoter where user_id=" . intval($user_info['id']));
                if ($bm_user == '' || $bm_promoter) {
                    $error = $m_config['bm_point'];
                    echo str_replace('xxxx', $error, $tips);return;
                }
            }

            //如果开启公会并且开启无抽成模式，微信 将无法提现ljz
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE == 1) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user where id=" . $user_info['id']);
                    if (intval($society_info['society_chieftain']) == 1) {
                        $error = '会长提现,请联系客服!';
                        echo str_replace('xxxx', $error, $tips);return;
                        // $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        // $rate = floatval($refund_rate);
                    } elseif (intval($society_info['society_id'])) {
                        $error = '公会成员不允许提现!';
                        echo str_replace('xxxx', $error, $tips);return;
                        // $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        // $rate = floatval($refund_rate);
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user where id=" . $user_info['id']);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $_ticket = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $use_ticket += $_ticket;
                            } else {
                                $error = '公会成员不允许提现!';
                                echo str_replace('xxxx', $error, $tips);return;
                            }
                        }
                    }
                }
            }

            //总金额
            $use_money = floatval($use_ticket * $rate);

            $ticket_catty_min = $m_config['ticket_catty_min'] ? $m_config['ticket_catty_min'] : 1;
            if ($use_money < $ticket_catty_min) {
                $error = "可领取金额小于提现最低额度" . $ticket_catty_min . "元！";
                echo str_replace('xxxx', $error, $tips);return;
            }

            $cash_money = strim($_REQUEST['cash_money']);
            $num_cash_money = floatval($cash_money);
            if (!isset($_REQUEST['cash_money'])) {
                $error_tip = "";
                $cash_money = '';
            } elseif ($cash_money == '') {
                $error_tip = "请输入领取金额";
                $cash_money = '';
            } elseif (!preg_match('/^[0-9]+(.[0-9]{0,2})?$/', $cash_money)) {
                $error_tip = "领取金额最多两位小数";
            } elseif ($num_cash_money < $ticket_catty_min) {
                $error_tip = "领取金额最低额度为" . $ticket_catty_min . "元";
            } elseif ($num_cash_money > $use_money) {
                $error_tip = "领取金额超过总金额";
            } elseif ($num_cash_money > $use_cash_money) {
                $error_tip = "领取金额超过今日可领取金额";
            } else {
                $ticket = $num_cash_money / $rate;
                if ($ticket > 0 && $num_cash_money > 0) {
                    $refund_data['money'] = $num_cash_money;
                    $refund_data['user_bank_id'] = -1;
                    $refund_data['ticket'] = $ticket;
                    $refund_data['user_id'] = $user_info['id'];
                    $refund_data['create_time'] = NOW_TIME;
                    $refund_data['memo'] = $memo;
                    $refund_data['withdrawals_type'] = 0;
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user_refund", $refund_data);
                    $error = '提现申请提交成功!';
                    echo str_replace('xxxx', $error, $tips);return;
                } else {
                    $error = '提现申请提交失败!';
                    echo str_replace('xxxx', $error, $tips);return;
                }

            }

            $action = SITE_DOMAIN . '/mapi/index.php?ctl=user_center&act=submitrefundwx&id=' . $id;
            $html = '<div class="content"><div class="m-top"><div class="m-user"><div class="user-img"><img src="' . $user_info['head_image'] . '"/></div><div class="user-name"><p class="name">' . $user_info['nick_name'] . '</p><p class="id">' . $user_info['id'] . '</p></div><div class="clear"></div></div><div class="m-money"><div class="money-all"><p class="money">' . number_format($use_money, 2) . '</p><p class="title">总金额（元）</p></div><div class="money-today"><p class="money">' . number_format($use_cash_money, 2) . '</p><p class="title">今日可领取金额（元）</p></div><div class="clear"></div></div><div class="clear"></div></div><form method="post" action="' . $action . '"><div class="m-input"><span>领取金额</span><div class="input-content"><input type="text" name="cash_money" value="' . $cash_money . '" placeholder="请输入要领取的金额" /><span>(元)</span></div></div>';
            if ($error_tip) {
                $html .= '<div class="m-input"><span style="padding-left:60px;text-align:right;">&nbsp;</span><div class="input-content"><span style="color:red">' . $error_tip . '</span></div></div>';
            }
            $html .= '<div class="button"><input type="submit" value="确定" style="-webkit-appearance: none;background:#FF0000;color:#fff;height:36px;line-height:36px;padding:0 15px;font-size:14px;border:0;" /></div></form></div>';
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><title>微信提现</title><meta name="viewport" content="initial-scale=1, maximum-scale=1"><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><style type="text/css">*{margin: 0;padding: 0;}body{background: #eef7f4;font-size: 16px;}.clear {clear: both;visibility: hidden;font-size: 0;height: 0;line-height: 0;}.content{padding: 20px;background: #fff;}.m-user{display: -webkit-flex;display: flex;-webkit-align-items: center;align-items: center;margin-bottom: 15px;}.user-img{width: 40px;height: 40px;overflow: hidden;border-radius: 50%;float: left;margin-right: 5px;}.user-img img{width: 100%;}.user-name{float: left;}.user-name .name{font-size: 18px;line-height: 18px;margin-bottom: 5px;}.m-money{text-align: center;display: -webkit-flex;display: flex;-webkit-justify-content: center;justify-content: center;}.money-all{width: 50%;padding-right: 10px;box-sizing: border-box;text-align: left;}.money{font-size: 24px;color: #FF0000;font-family: arial;}.money-today{width: 50%;padding-left: 10px;box-sizing: border-box;text-align: left;}.title{color: #666;line-height: 20px;font-size: 14px;}.button{text-align: center;display: -webkit-flex;display: flex;margin: 0 px;}.button input{border:none;text-align: center;background: #ff5500;height: 40px;line-height: 40px;color: #fff;padding: 0 30px;display: block;text-decoration: none;border-radius: 3px;}.m-input{display: -webkit-flex;display: flex;-webkit-align-items: center;align-items: center;margin-top: 15px;display:-webkit-box;-webkit-box-orient:horizontal;}.m-input .input-content{display:flex;-webkit-box-flex:1;-moz-box-flex:1;-webkit-box-align:center;}.m-input input{height: 36px;line-height: 36px;border: 1px solid #dedede;padding: 0 10px;margin: 0 10px;display: flex;font-size:14px;}.m-input span{font-size: 14px;line-height: 30px;color: #666;}</style></head><body>' . $html . '</body></html>';
        }
    }

    /**
     * 修改账户信息-主页
     */
    public function user_edit()
    {
        $root = array();
        $root['nick_info'] = '';
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $root['status'] = 1;
            $root['error'] = "";
            $user_id = intval($GLOBALS['user_info']['id']);
            $sql = "select id as user_id,head_image,nick_name,sex,signature,is_authentication,birthday,emotional_state,province,city,job,is_edit_sex,luck_num from " . DB_PREFIX . "user where id=" . $user_id;
            $user = $GLOBALS['db']->getRow($sql, true, true);
            foreach ($user as $k => $v) {
                $user[$k] = htmlspecialchars_decode($v);
            }
            $user['head_image'] = get_spec_image($user['head_image']);
            $user['birthday'] = date('Y-m-d', $user['birthday']);
            $user['signature'] = ($user['signature']);
            $user['nick_name'] = ($user['nick_name']);
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if ($m_config['is_change_name'] == 1) {
                $root['nick_info'] = "昵称只能免费更改一次,超过次数将收取" . $m_config['change_name'] . "秀豆";
            }

            $root['user'] = $user;
        }
        ajax_return($root);
    }

    /**
     * 保存账户信息
     */
    public function user_save()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $user_info_req = $_REQUEST;
            foreach ($user_info_req as $k => $v) {
                if ($v != 'user_center' || $v != 'user_save') {
                    $user_info[$k] = trim($v);
                }
            }
            //判断性别是否可修改
            if (isset($user_info['sex'])) {
                $user_info['is_edit_sex'] = 0;
            }

            $user_info['id'] = $user_id;
            $user_info['birthday'] = strtotime($user_info['birthday']);
            $user_info['signature'] = ($user_info['signature']);
            $user_info['nick_name'] = ($user_info['nick_name']);

            if ($user_info['birthday'] == '') {
                unset($user_info['birthday']);
            }

            if ($user_info['signature'] == '') {
                unset($user_info['signature']);
            }

            if ($user_info['nick_name'] == '') {
                unset($user_info['nick_name']);
            }

            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //昵称如果等于铭感词,则提示,如果包含 则用*代替
            $nick_name = $user_info['nick_name'];
            if ($m_config['name_limit'] == 1) {
                $limit_sql = $GLOBALS['db']->getCol("SELECT name FROM " . DB_PREFIX . "limit_name");
                $in = in_array($nick_name, $limit_sql);
                if ($in) {
                    ajax_return(array("status" => 0, "error" => '昵称包含敏感词汇'));
                } elseif ($GLOBALS['db']->getCol("SELECT name FROM " . DB_PREFIX . "limit_name WHERE '$nick_name' like concat('%',name,'%')")) {
                    $user_info['nick_name'] = str_replace($limit_sql, '*', $nick_name);
                }
            }
            if ($GLOBALS['db']->getOne("SELECT nick_name FROM " . DB_PREFIX . "user WHERE id<>" . $user_id . " and  nick_name ='$nick_name'")) {
                ajax_return(array("status" => 0, "error" => '昵称被占用，请重新输入'));
            }

            //修改昵称付费
            if ($m_config['is_change_name'] == 1 && $nick_name != '') {
                $change_name_info = $GLOBALS['db']->getRow("SELECT is_change_name,diamonds,nick_name FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
                if ($change_name_info['nick_name'] != $user_info['nick_name']) {
                    if ($change_name_info['is_change_name'] == 1) {
                        if ($change_name_info['diamonds'] >= intval($m_config['change_name'])) {
                            $sql = "update " . DB_PREFIX . "user set diamonds=diamonds-" . intval($m_config['change_name']) . " where id=" . $user_id;
                            $GLOBALS['db']->query($sql);
                        } else {
                            ajax_return(array("status" => 0, "error" => '昵称修改失败，请充值秀豆'));
                        }
                    }
                }
            }

            fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
            //提交空字段不操作
            if ($user_info) {
                $root = save_user($user_info, 'UPDATE');
            } else {
                $root['status'] = 1;
                $root['error'] = '';
                ajax_return($root);
            }
            if ($root['status']) {
                //更新session
                $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $root['data']);
                es_session::set("user_info", $user_info);

                $user_id = $root['data'];
                if ($m_config['is_change_name'] == 1) {
                    $sql = "select id as user_id,head_image,nick_name,sex,signature,is_authentication,birthday,emotional_state,province,city,job,is_edit_sex,diamonds,is_change_name from " . DB_PREFIX . "user where id=" . $user_id;
                } else {
                    $sql = "select id as user_id,head_image,nick_name,sex,signature,is_authentication,birthday,emotional_state,province,city,job,is_edit_sex,diamonds from " . DB_PREFIX . "user where id=" . $user_id;
                }

                $user = $GLOBALS['db']->getRow($sql);

                if ($m_config['is_change_name'] == 1 && $nick_name != '') {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    if ($change_name_info['nick_name'] != $user['nick_name']) {
                        if ($user['is_change_name'] == 0) {
                            $sql = "update " . DB_PREFIX . "user set is_change_name=1 where id=" . $user_id;
                            $GLOBALS['db']->query($sql);
                            $user_redis->update_db($user_id, array('is_change_name' => 1));
                        }
                        $user_redis->update_db($user_id, array('diamonds' => $user['diamonds']));
                    }
                }

                $user['head_image'] = get_spec_image($user['head_image']);

                $user['birthday'] = date('Y-m-d', $user['birthday']);
                $root['status'] = 1;
                $root['error'] = '编辑成功';
                $root['user'] = $user;
            } else {
                $root['status'] = 0;
                if ($root['error'] == '') {
                    $root['error'] = '编辑失败';
                }
            }
        }
        ajax_return($root); //返回信息缺少认证信息
    }

    /**
     * 地区接口
     */
    public function region_list()
    {
        $root = array();
        $root['status'] = 1;
        $root['error'] = '';
        $region_list = load_auto_cache("region_list");
        $root['region_list'] = $region_list;
        $m_config = load_auto_cache("m_config");
        $root['region_versions'] = $m_config['region_versions'];
        ajax_return($root);
    }

    /**
     * 认证初始化
     */
    public function authent()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config");
            $root['status'] = 1;
            $root['error'] = "";
            $root['title'] = $m_config['short_name'] . "认证";
            $user_id = intval($GLOBALS['user_info']['id']);

            $root['is_show_identify_number'] = intval($m_config['is_show_identify_number']); //0:不需要验证身份证号码 1:需要验证身份证号码
            $root['identify_hold_example'] = $m_config['identify_hold_example']; //手持身份证示例图片

            //判断是否开启公会邀请码功能ljz
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE && $m_config['society_pattern'] != 0 && $m_config['open_society_code'] == 1) {
                $root['open_society_code'] = 1;
            } else {
                $root['open_society_code'] = 0;
            }

            //车行定制 ljz 认证分类标签
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                $res = $GLOBALS['db']->getAll("select id,title from " . DB_PREFIX . "car_classify where classify_type in(1,3) and is_effect=1");
                $res1 = $GLOBALS['db']->getAll("select classify_id from " . DB_PREFIX . "user_classify where user_id=$user_id");
                if (empty($res1)) {
                    $res1 = [];
                }
                //已选标签选中
                foreach ($res as $key => $val) {
                    $res[$key]['type'] = 0;
                    foreach ($res1 as $k => $v) {
                        if ($val['id'] == $v['classify_id']) {
                            $res[$key]['type'] = 1;
                        }
                    }
                }
                $root['classify_list'] = $res;
            } else {
                $root['classify_list'] = [];
            }

            //车行定制 ljz 认证地区
            if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                $car_region = ",province,city ";
            }

            if ($root['open_society_code'] == 1 && defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                $user_sql = "select id,id as user_id,investor_send_info$car_region,authentication_type,authentication_name,identify_number, contact,from_platform,wiki,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication,game_distribution_id,society_code from " . DB_PREFIX . "user where is_effect =1 and id=" . $user_id;
            } elseif ($root['open_society_code'] == 1) {
                $user_sql = "select id,id as user_id,investor_send_info$car_region,authentication_type,authentication_name,identify_number, contact,from_platform,wiki,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication,society_code from " . DB_PREFIX . "user where is_effect =1 and id=" . $user_id;
            } elseif (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                $user_sql = "select id,id as user_id,investor_send_info$car_region,authentication_type,authentication_name,identify_number, contact,from_platform,wiki,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication,game_distribution_id from " . DB_PREFIX . "user where is_effect =1 and id=" . $user_id;
            } else {
                $user_sql = "select id,id as user_id,investor_send_info$car_region,authentication_type,authentication_name,identify_number, contact,from_platform,wiki,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication from " . DB_PREFIX . "user where is_effect =1 and id=" . $user_id;
            }

            $user = $GLOBALS['db']->getRow($user_sql, true, true);
            foreach ($user as $k => $v) {
                $user[$k] = htmlspecialchars_decode($v);
            }
            //①用户认证状态为非默认状态(未认证) AND 身份证号码为空, 则不显示身份证号码
            if (intval($user['is_authentication']) != 0 && trim($user['identify_number']) == '') {
                $root['is_show_identify_number'] = 0;
            }
            //②用户认证状态为未通过(3)状态 AND 需要身份验证, 则显示身份验证
            //            if(intval($user['is_authentication'])==3 && intval($m_config['is_show_identify_number'])==1){
            //                $root['is_show_identify_number'] = 1;
            //            }

            $user['identify_number'] = !empty($user['identify_number']) ? $user['identify_number'] : '';

            $authent_list_sql = "select id,`name` from " . DB_PREFIX . "authent_list order by sort desc";
            $authent_list = $GLOBALS['db']->getAll($authent_list_sql, true, true);

            $root['authent_list'] = $authent_list;
            $root['invite_id'] = intval($user['game_distribution_id']);
            unset($user['game_distribution_id']);
            $root['user'] = $user;
            $root['invite_type_list'] = [];
            if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                $root['invite_id'] = intval($user['game_distribution_id']);
                unset($user['game_distribution_id']);
                $root['invite_type_list'] = [
                    [
                        'id' => 1,
                        'name' => '无'
                    ],
                    [
                        'id' => 2,
                        'name' => '推荐人ID'
                    ],
                    [
                        'id' => 3,
                        'name' => '推荐人手机'
                    ]
                ];
            }

            //认证状态提示消息
            if (trim($user['investor_send_info'])) {
                $root['investor_send_info'] = $user['investor_send_info'];
            } else {
                $root['investor_send_info'] = $this->get_investor_info($user['is_authentication']);
            }
        }
        ajax_return($root);
    }

    /**
     * 提交保存认证信息
     */
    public function attestation()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $root['status'] = 1;
            $root['error'] = "";
            fanwe_require(APP_ROOT_PATH . 'system/libs/user.php');
            $authentication_type = strim($_REQUEST['authentication_type']); //认证类型
            $authentication_name = trim($_REQUEST['authentication_name']); //真实姓名

            $m_config = load_auto_cache("m_config");
            $is_check_id_num = intval($m_config['is_show_identify_number']); //0:不需要验证身份证号码 1:需要验证身份证号码
            $authent_info = $GLOBALS['db']->getRow("select is_authentication,identify_number from  " . DB_PREFIX . "user where id=" . $GLOBALS['user_info']['id']);
            if (intval($authent_info['is_authentication']) != 0 && trim($authent_info['identify_number']) == '') {
                $is_check_id_num = 0;
            }
            $identify_number = strim($_REQUEST['identify_number']); //身份证号码

            $identify_hold_image = strim($_REQUEST['identify_hold_image']); //手持身份证正面
            $identify_positive_image = strim($_REQUEST['identify_positive_image']); //身份证正面
            $identify_nagative_image = strim($_REQUEST['identify_nagative_image']); //身份证反面

            $contact = trim($_REQUEST['contact']); //联系方式
            //$from_platform = '';//来自平台
            $wiki = trim($_REQUEST['wiki']); //百度百科

            //=============================
            if ($authentication_type == '') {
                $root['status'] = 0;
                $root['error'] = '请选择认证类型！';
                ajax_return($root);
            }
            if ($authentication_name == '') {
                $root['status'] = 0;
                $root['error'] = '请填写真实姓名！';
                ajax_return($root);
            }
            //是否需要验证身份证号码 $is_check_id_num: 1需要验证; 0跳过验证
            if ($is_check_id_num == 1) {

                if ($identify_number == '') {
                    $root['status'] = 0;
                    $root['error'] = '请输入真实身份证号码！';
                    ajax_return($root);
                } else {
                    if ($GLOBALS['db']->getOne('SELECT COUNT(id) FROM `' . DB_PREFIX . 'user` WHERE identify_number = "' . $identify_number . '" AND is_authentication IN (1,2)') > 0) {
                        $root['status'] = 0;
                        $root['error'] = '身份证号码已经别人占用！';
                        ajax_return($root);
                    }
                }
            }
            if ($identify_positive_image == '') {
                $root['status'] = 0;
                $root['error'] = '请上传身份证正面照片！';
                ajax_return($root);
            }
            if ($identify_nagative_image == '') {
                $root['status'] = 0;
                $root['error'] = '请上传身份证背面照片！';
                ajax_return($root);
            }
            if ($identify_hold_image == '') {
                $root['status'] = 0;
                $root['error'] = '请上传手持身份证正面！';
                ajax_return($root);
            }
            if ($contact == '') {
                $root['status'] = 0;
                $root['error'] = '请填写联系方式！';
                ajax_return($root);
            }
            /*if($from_platform==''){
            $root['status'] = 0;
            $root['error'] = '请填写来自平台！';
            ajax_return($root);
            }*/

            if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $authentication_name)) {
                $root['status'] = 0;
                $root['error'] = '请填写2-4位中文姓名！';
                ajax_return($root);
            }
            if (!check_mobile($contact)) {
                $root['status'] = 0;
                $root['error'] = '请填写11位手机号！';
                ajax_return($root);
            }

            if (defined('EXAMINE_TIME') && EXAMINE_TIME) {
                $user_investor_time = $GLOBALS['db']->getRow("select investor_time from  " . DB_PREFIX . "user where id=" . $GLOBALS['user_info']['id']);
                $m_config = load_auto_cache("m_config");
                if ($m_config['attestation_time'] !== '') {
                    if (get_gmtime() < $user_investor_time['investor_time']) {
                        $investor_time = to_date($user_investor_time['investor_time'], "Y-m-d:H:i:s");
                        $root['status'] = 0;
                        $root['error'] = '您下次审核时间为' . $investor_time;
                        ajax_return($root);
                    }
                }
            }

            //判断该实名是否存在
            $user_info = $GLOBALS['db']->getRow("select id  from  " . DB_PREFIX . "user where id=" . $GLOBALS['user_info']['id']);
            if ($user_info) {
                $user_info['is_authentication'] = 1; //认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
                $user_info['user_type'] = 0; //用户类型
                $user_info['authentication_type'] = $authentication_type; //认证类型
                $user_info['authentication_name'] = $authentication_name; //真实姓名
                $user_info['identify_number'] = $identify_number; //身份证号码
                $user_info['contact'] = $contact; //联系方式
                //$user_info['from_platform'] = $from_platform;//来自平台
                $user_info['wiki'] = $wiki; //百度百科
                $user_info['identify_hold_image'] = get_spec_image($identify_hold_image); //手持身份证正面
                $user_info['identify_positive_image'] = get_spec_image($identify_positive_image); //身份证正面
                $user_info['identify_nagative_image'] = get_spec_image($identify_nagative_image); //身份证反面

                //车行定制 ljz 地区认证
                if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                    $user_info['province'] = trim($_REQUEST['province']);
                    $user_info['city'] = trim($_REQUEST['city']);
                }

                if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION) {
                    $game_distribution_id = 0;
                    $game_distribution_type = intval($_REQUEST['invite_type']);
                    switch ($game_distribution_type) {
                        case 2:
                            $game_distribution_id = intval($_REQUEST['invite_input']);
                            if (!$game_distribution_id) {
                                ajax_return(['status' => 0, 'error' => '推荐人ID不存在']);
                            }
                            $game_distribution_id = $GLOBALS['db']->getOne("select id from  " . DB_PREFIX . "user where id={$game_distribution_id} or luck_num = {$game_distribution_id}");
                            if (!$game_distribution_id) {
                                ajax_return(['status' => 0, 'error' => '推荐人ID不存在']);
                            }
                            break;
                        case 3:
                            $game_distribution_moblie = trim($_REQUEST['invite_input']);
                            if (!$game_distribution_moblie) {
                                ajax_return(['status' => 0, 'error' => '请填写推荐人手机号']);
                            }
                            $game_distribution_id = $GLOBALS['db']->getOne("select id from  " . DB_PREFIX . "user where mobile={$game_distribution_moblie}");
                            if (!$game_distribution_id) {
                                ajax_return(['status' => 0, 'error' => '推荐人手机号不存在']);
                            }
                            break;
                        default:
                            break;
                    }
                    if ($GLOBALS['user_info']['id'] == $game_distribution_id) {
                        ajax_return(['status' => 0, 'error' => '推荐人不能为自己']);
                    } else {
                        $set = "`game_distribution_id` = $game_distribution_id";
                        if (defined('GAME_DISTRIBUTION_TOP') && GAME_DISTRIBUTION_TOP && $game_distribution_type != 1) {
                            $top = $GLOBALS['db']->getOne("SELECT game_distribution_top_id FROM `" . DB_PREFIX . "user` WHERE `id` = " . $game_distribution_id);
                            if (!$top) {
                                ajax_return(['status' => 0, 'error' => '推荐人无效']);
                            }
                            $set .= ',`game_distribution_top_id` = ' . $top;
                        }
                        $GLOBALS['db']->query("UPDATE `" . DB_PREFIX . "user` SET $set WHERE `id` = " . intval($GLOBALS['user_info']['id']));
                    }
                }

                //公会邀请码ljz
                $user_info['society_code'] = strim($_REQUEST['society_code']);
                if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {
                    //服务端开始公会
                    $m_config = load_auto_cache("m_config");
                    if ($m_config['society_pattern'] != 0 && $m_config['open_society_code'] == 1 && strlen($user_info['society_code']) != 0) {
                        //后台开启公会，开启公会邀请码，邀请码有值
                        $society_id = $GLOBALS['db']->getOne("select society_id from " . DB_PREFIX . "user where id=" . $GLOBALS['user_info']['id']);
                        if ($society_id) {
                            //判断是否有公会
                            ajax_return(['status' => 0, 'error' => '您已经有公会不需要填写邀请码']);
                        } else {
                            $res = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society where status=1 and society_code='" . $user_info['society_code'] . "'");
                            if (empty($res)) {
                                //邀请码是否存在
                                ajax_return(['status' => 0, 'error' => '您填写的公会邀请码不存在']);
                            }
                        }
                    }
                }

                //车行定制 ljz 分类标签写入
                if (defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE) {
                    $classify_id = $_REQUEST['classify_id'] ? $_REQUEST['classify_id'] : 0;
                    $where = "user_id = " . $GLOBALS['user_info']['id'];
                    if ($classify_id) {
                        $classify = explode(',', $classify_id);
                        $classify_list = M('user_classify')->where($where)->field('classify_id')->select();
                        if ($classify_list != $classify) {
                            //M('user_classify')->where($where)->delete();
                            $GLOBALS['db']->query("delete from " . DB_PREFIX . "user_classify where $where");
                            foreach ($classify as $key => $val) {
                                M('user_classify')->add(array('user_id' => $user_id, 'classify_id' => $val, 'is_effect' => 0));
                            }
                        }
                    } else {
                        //M('user_classify')->where($where)->delete();
                        $GLOBALS['db']->query("delete from " . DB_PREFIX . "user_classify where $where");
                    }
                }

                $res = save_user($user_info, "UPDATE");

                if ($res['status'] == 1) {
                    //更新session
                    $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $res['data']);
                    es_session::set("user_info", $user_info);

                    $root['status'] = 1;
                    $root['error'] = '已提交,等待审核';
                } else {
                    $root['status'] = 0;
                    $root['error'] = $res['error'];
                }
            } else {
                $root['status'] = 0;
                $root['error'] = '会员信息不存在';
            }
        }
        ajax_return($root);
    }
    //提现领取记录
    public function extract_record()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = $GLOBALS['user_info']['id'];
            $sql = "select money,pay_time,create_time,is_pay from " . DB_PREFIX . "user_refund  where is_pay in (1,3) and user_id =" . $user_id;
            $list = $GLOBALS['db']->getAll($sql, true, true);
            if ($list) {
                foreach ($list as $k => $v) {
                    if ($v['is_pay'] == 3) {
                        $totle_money += $v['money'] * 100;
                    }
                    $record[$k]['money'] = number_format($v['money'], 2);
                    if ($v['pay_time'] != 0) {
                        $record[$k]['pay_time'] = date("Y年m月d日 H时i分", $v['pay_time']);
                    }
                    $record[$k]['is_pay'] = intval($v['is_pay']);
                    $record[$k]['create_time'] = date("Y年m月d日 H时i分", $v['create_time']);

                }
            } else {
                $record = array();
            }

            $root['total_money'] = number_format(intval($totle_money) / 100, 2);
            $root['status'] = 1;
            $root['error'] = ''; //提现记录
            $root['list'] = $record;
        }

        ajax_return($root);
    }
    //更新微信openid
    public function update_wxopenid()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            fanwe_require(APP_ROOT_PATH . "system/utils/weixin.php");
            $m_config = load_auto_cache("m_config"); //初始化手机端配置

            //获取微信配置信息
            if ($m_config['wx_appid'] == '' || $m_config['wx_secrit'] == '') {
                $root['status'] = 0;
                $root['error'] = "微信提现参数未配置，请联系客服";
                ajax_return($root);
            } else {
                $wx_appid = strim($m_config['wx_appid']);
                $wx_secrit = strim($m_config['wx_secrit']);
            }

            $jump_url = SITE_DOMAIN . url_wap("user_center#update_wxopenid");

            $weixin = new weixin($wx_appid, $wx_secrit, $jump_url);

            if (($_REQUEST['openid'] != "" && $_REQUEST['access_token'] != "") || $_REQUEST['code'] != "") {
                if ($_REQUEST['openid'] != "" && $_REQUEST['access_token'] != "") {
                    $wx_info = $weixin->sns_get_userinfo($_REQUEST['openid'], $_REQUEST['access_token']);
                } else if ($_REQUEST['code'] != "") {
                    $wx_info = $weixin->scope_get_userinfo($_REQUEST['code']);
                } else {
                    if (DEBUG_WX) {
                        log_result('-服务端获取微信参数失败-');
                    }
                    $root['status'] = 0;
                    $root['error'] = "服务端获取微信参数失(openid or code).";
                }
                fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
                $root = wxUser_update($wx_info, $user_id);
            } else {
                $root['status'] = 0;
                $root['error'] = "无法获取APP端微信参数(openid or code)!";
            }
        }
        ajax_return($root);
    }
    //支付宝提现绑定接口
    public function binding_alipay()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = $GLOBALS['user_info']['id'];
            $alipay_name = trim($_REQUEST['alipay_name']);
            $alipay_account = trim($_REQUEST['alipay_account']);

            if ($alipay_name != '' && $alipay_account != '') {
                $alipay = array();
                $alipay['alipay_name'] = strim($alipay_name);
                $alipay['alipay_account'] = strim($alipay_account);
                $alipay['binding_alipay'] = 1;
                $where = "id=" . $user_id;
                $result = $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $alipay, "UPDATE", $where);

                if (!$result) {
                    $root['error'] = "绑定失败";
                    $root['status'] = 0;
                } else {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_ticket_info = $user_redis->update_db($user_id, $alipay);

                    $root['error'] = '绑定成功';
                    $root['status'] = 1;
                }
            } else {
                $root['error'] = "支付宝账户与名称不能为空";
                $root['status'] = 0;
            }
        }
        ajax_return($root);
    }
    //支付宝提现界面初始化接口
    public function money_carry_alipay()
    {
        $root = array('status' => 1, 'error' => '初始化成功');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if (intval($m_config['is_refund']) != 1 || intval($m_config['withdrawals_alipay']) == 0) {
                $root['status'] = 0;
                $root['error'] = '支付宝提现已经关闭!';
                ajax_return($root);
            }
            $user_id = intval($GLOBALS['user_info']['id']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $ticket = $user_redis->getRow_db($user_id, array('ticket', 'refund_ticket', 'user_level'));
            $refund_role = load_auto_cache("refund_role_level", array('level' => $ticket['user_level'])); //初始化手机端配置

            //提现比例 如果主播提现比例为空，则使用后台通用比例
            $root['ratio'] = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=" . $user_id);
            if ($root['ratio'] == '') {
                if (!empty($refund_role)) {
                    $root['ratio'] = $refund_role['ticket_catty_ratio'];
                } else {
                    $root['ratio'] = $m_config['ticket_catty_ratio'];
                }
            }
            //公会长提现比例特殊
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain'])) {
                        $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        $root['ratio'] = floatval($refund_rate);
                        if ($root['ratio'] > 1 || $root['ratio'] <= 0) {
                            $root['ratio'] = $m_config['society_public_rate'];
                        }
                    } else if ($society_info['society_id']) {
                        $root['ratio'] = $m_config['society_user_rate'];
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $_ticket_ = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $ticket['ticket'] += $_ticket_;
                            }
                        }
                    }
                }
            }
            $root['can_use_ticket'] = intval($ticket['ticket'] - $ticket['refund_ticket']); //可提现秀票

            if (floatval($root['ratio']) > 0) {
                //每日可提现秀票
                $root['day_ticket_max'] = intval(intval($m_config['day_cash_max']) / floatval($root['ratio']));
                //最小提现秀票
                $root['ticket_catty_min'] = intval(intval($m_config['ticket_catty_min']) / floatval($root['ratio']));
            }

            //计算已提现秀票
            $user_refund = $GLOBALS['db']->getRow("SELECT SUM(ticket) AS refund_sum FROM " . DB_PREFIX .
                "user_refund WHERE user_id=" . $user_id . " AND is_pay=3");
            $root['ready_refund_ticket'] = intval($user_refund['refund_sum']);
        }
        ajax_return($root);
    }
    //支付宝提现接口
    public function submit_refund_alipay()
    {
        $root = array('status' => 1, 'error' => '成功');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $user_id = $GLOBALS['user_info']['id']; //会员ID

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_level = $user_redis->getOne_db($user_id, 'user_level');
            $refund_role = load_auto_cache("refund_role_level", array('level' => $user_level)); //初始化手机端配置

            //判断是否在直播中，如是则不让进入提现界面
            $is_live = $GLOBALS['db']->getRow("select user_id,live_in from " . DB_PREFIX . "video where  user_id=" . $user_id . " order by id desc");
            //异常退出后，等待公会长抽成完成后才能提现ljz
            if (!empty($is_live) && $is_live['live_in'] == 1) {
                $root['status'] = 0;
                $root['error'] = '由于您的视频还在直播，暂时无法提现，请稍后再试';
                ajax_return($root);
            }

            $rate = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=" . intval($user_id));
            if ($rate == '') {
                if (!empty($refund_role)) {
                    $rate = $refund_role['ticket_catty_ratio'];
                } else {
                    $rate = $m_config['ticket_catty_ratio'];
                }
            }
            $ticket = intval($_REQUEST['ticket']); //提现秀票
            $memo = "支付宝提现";
            $money = floatval($ticket * $rate);
            //未处理提现
            $ready_refund_id = intval($GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and (is_pay =0 or is_pay=1)"));
            if ($ready_refund_id) {
                $root['error'] = '您还有未处理的提现！';
                $root['status'] = 0;
                ajax_return($root);
            }
            //会员当前秀票
            $user_ticket_info = $GLOBALS['db']->getRow("select ticket,refund_ticket from " . DB_PREFIX . "user where id = '" . intval($user_id) . "'");
            //如果开启公会并且开启无抽成模式，支付宝 将无法提现ljz
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE == 1) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain']) == 1) {
                        $root['error'] = '会长提现，请联系客服!';
                        $root['status'] = 0;
                        ajax_return($root);
                    } elseif (intval($society_info['society_id'])) {
                        $root['error'] = '公会成员不允许提现!';
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $__ticket = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $user_ticket_info['ticket'] += $__ticket;
                            } else {
                                $root['error'] = '公会成员不允许提现!';
                                $root['status'] = 0;
                                ajax_return($root);
                            }
                        }
                    }
                }
            }
            $user_ticket = $user_ticket_info['ticket'] - $user_ticket_info['refund_ticket']; //可使用的秀票
            //超额判断
            if ($ticket > $user_ticket) {
                $root['error'] = '提现超出限制!不能大于可用提现';
                $root['status'] = 0;
                ajax_return($root);
            }
            // 进入每月提现一次流程
            $month_carry_one = intval($m_config['month_carry_one']) ? 1 : 0; //提现配置，1：每月提现1次，0：无限制
            $day_carry_one = intval($m_config['day_carry_one']) ? 1 : 0;
            if ($month_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select pay_time from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and is_pay = 3");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ym") == to_date(get_gmtime(), 'Ym'))) {
                    $root['error'] = '每月只能提现一次，本月已经提现过了';
                    $root['status'] = 0;
                    ajax_return($root);
                } else {
                    //查看本月允许提现时间，精确计算到天
                    if ((to_date(get_gmtime(), 'd') > intval($m_config['month_carry_max']) || to_date(get_gmtime(), 'd') < intval($m_config['month_carry_min'])) && intval($m_config['month_carry_max']) != 0 && intval($m_config['month_carry_min']) != 0) {
                        $root['error'] = '每月' . intval($m_config['month_carry_min']) . '日到' . intval($m_config['month_carry_max']) . '日才能提现';
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
            }
            if ($day_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select pay_time from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and is_pay = 3 ORDER BY id DESC LIMIT 1");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ymd") == to_date(get_gmtime(), 'Ymd'))) {
                    $root['error'] = '每日只能提现一次，本日已经提现过了';
                    $root['status'] = 0;
                    ajax_return($root);
                }
            }
            //百媚模式下代理会员提现
            if (defined('OPEN_BM') && OPEN_BM) {
                $bm_config = load_auto_cache("bm_config");
                $bm_pid = intval($bm_config['bm_pid']);
                $bm_user = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id=" . intval($user_id) . " and( bm_pid=" . $bm_pid . " or bm_pid=0)");
                $bm_promoter = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "bm_promoter where user_id=" . intval($user_id));
                if ($bm_user == '' || $bm_promoter) {
                    $root['error'] = $m_config['bm_point'];
                    $root['status'] = 0;
                    ajax_return($root);
                }

            }
            //提现最小值
            $ticket_catty_min = $m_config['ticket_catty_min'] > 1 ? $m_config['ticket_catty_min'] : 1;
            //最小判断
            if ($money < $ticket_catty_min) {
                $root['error'] = '支付宝提现单笔不能少于' . $ticket_catty_min . '元';
                $root['status'] = 0;
                ajax_return($root);
            }

            //取用户当日提现金额之和
            //create_time储存的是格林威治时间。使用时需转换为北京时间
            $refunded_money_sql = "select sum(money) from " . DB_PREFIX . "user_refund where user_id=$user_id and (is_pay=0 or is_pay=1 or is_pay=3) and DATE_FORMAT(FROM_UNIXTIME(create_time+28800),'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')";
            $refunded_money = $GLOBALS['db']->getOne($refunded_money_sql);
            $total_money = floatval($refunded_money) + $money;
            //提现最大值
            $day_cash_max = intval($m_config['day_cash_max']);
            //总和判断
            if ($total_money > $day_cash_max) {
                $root['error'] = '支付宝每日提现总额不能多于' . $day_cash_max . '元';
                $root['status'] = 0;
                ajax_return($root);
            }

            $refund_data['money'] = $money;
            $refund_data['user_bank_id'] = -1;
            $refund_data['ticket'] = $ticket;
            $refund_data['user_id'] = $user_id;
            $refund_data['create_time'] = NOW_TIME;
            $refund_data['memo'] = $memo;
            $refund_data['withdrawals_type'] = 1;
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_refund", $refund_data);
        }
        ajax_return($root);
    }
    //银行卡提现绑定接口
    public function binding_bankcard()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = $GLOBALS['user_info']['id'];
            $bank_name = trim($_REQUEST['bank_name']);
            $branch_name = trim($_REQUEST['branch_name']);
            $open_account_num = trim($_REQUEST['open_account_num']);
            $open_account_name = trim($_REQUEST['open_account_name']);

            if (!empty($bank_name) && !empty($branch_name) && !empty($open_account_num) && !empty($open_account_name)) {

                if (!luhn($open_account_num)) {
                    ajax_return(array('status' => 0, 'error' => '请输入有效的银行卡号'));
                }

                $bankcard = array();
                $bankcard['bank_name'] = strim($bank_name);
                $bankcard['branch_name'] = strim($branch_name);
                $bankcard['open_account_num'] = strim($open_account_num);
                $bankcard['open_account_name'] = strim($open_account_name);
                $bankcard['binding_bankcard'] = 1;
                $where = "id=" . $user_id;
                $result = $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $bankcard, "UPDATE", $where);

                if (!$result) {
                    $root['error'] = "绑定失败";
                    $root['status'] = 0;
                } else {
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_ticket_info = $user_redis->update_db($user_id, $bankcard);

                    $root['error'] = '绑定成功';
                    $root['status'] = 1;
                }
            } else {
                $root['error'] = "银行名称,支行名称,开户账号,开户名称不能为空";
                $root['status'] = 0;
            }
        }
        ajax_return($root);
    }
    //银行卡提现界面初始化接口
    public function money_carry_bankcard()
    {
        $root = array('status' => 1, 'error' => '初始化成功');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            if (intval($m_config['is_refund']) != 1 || intval($m_config['withdrawals_bankcard']) == 0) {
                $root['status'] = 0;
                $root['error'] = '银行提现已经关闭!';
                ajax_return($root);
            }
            $user_id = intval($GLOBALS['user_info']['id']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $ticket = $user_redis->getRow_db($user_id, array('ticket', 'refund_ticket', 'user_level'));
            $refund_role = load_auto_cache("refund_role_level", array('level' => $ticket['user_level'])); //初始化手机端配置

            //提现比例 如果主播提现比例为空，则使用后台通用比例
            $root['ratio'] = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=" . $user_id);
            if ($root['ratio'] == '') {
                if (!empty($refund_role)) {
                    $root['ratio'] = $refund_role['ticket_catty_ratio'];
                } else {
                    $root['ratio'] = $m_config['ticket_catty_ratio'];
                }
            }
            //公会长提现比例特殊
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE) {

                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain'])) {
                        $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        $root['ratio'] = floatval($refund_rate);
                        if ($root['ratio'] > 1 || $root['ratio'] <= 0) {
                            $root['ratio'] = $m_config['society_public_rate'];
                        }
                    } else if ($society_info['society_id']) {
                        $root['ratio'] = $m_config['society_user_rate'];
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $ticket_none = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $ticket['ticket'] += $ticket_none;
                            } else {
                                // 非工会长是否支持提现功能
                                $root['withdraw'] = 0; // 是否可以提现
                            }
                        }
                    }
                }
            }
            $root['can_use_ticket'] = intval($ticket['ticket'] - $ticket['refund_ticket']); //可提现秀票

            if (floatval($root['ratio']) > 0) {
                //每日可提现秀票
                $root['day_ticket_max'] = intval(intval($m_config['day_cash_max']) / floatval($root['ratio']));
                //最小提现秀票
                $root['ticket_catty_min'] = intval(intval($m_config['ticket_catty_min']) / floatval($root['ratio']));
            }

            //计算已提现秀票
            $user_refund = $GLOBALS['db']->getRow("SELECT SUM(ticket) AS refund_sum FROM " . DB_PREFIX . "user_refund WHERE user_id=" . $user_id . " AND is_pay=3");
            $root['ready_refund_ticket'] = intval($user_refund['refund_sum']);
        }
        ajax_return($root);
    }
    //银行卡提现接口
    public function submit_refund_bankcard()
    {
        $root = array('status' => 1, 'error' => '成功');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $user_id = $GLOBALS['user_info']['id']; //会员ID

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_level = $user_redis->getOne_db($user_id, 'user_level');
            $refund_role = load_auto_cache("refund_role_level", array('level' => $user_level)); //初始化手机端配置

            //判断是否在直播中，如是则不让进入提现界面
            $is_live = $GLOBALS['db']->getRow("select user_id,live_in from " . DB_PREFIX . "video where  user_id=" . $user_id . " order by id desc");
            //异常退出后，等待公会长抽成完成后才能提现ljz
            if (!empty($is_live) && $is_live['live_in'] == 1) {
                $root['status'] = 0;
                $root['error'] = '由于您的视频还在直播，暂时无法提现，请稍后再试';
                ajax_return($root);
            }

            $rate = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=" . intval($user_id));
            if ($rate == '') {
                if (!empty($refund_role)) {
                    $rate = $refund_role['ticket_catty_ratio'];
                } else {
                    $rate = $m_config['ticket_catty_ratio'];
                }
            }
            $ticket = intval($_REQUEST['ticket']); //提现秀票
            $memo = "银行卡提现";
            $money = floatval($ticket * $rate);
            //未处理提现
            $ready_refund_id = intval($GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and (is_pay =0 or is_pay=1)"));
            if ($ready_refund_id) {
                $root['error'] = '您还有未处理的提现！';
                $root['status'] = 0;
                ajax_return($root);
            }
            //会员当前秀票
            $user_ticket_info = $GLOBALS['db']->getRow("SELECT ticket,refund_ticket FROM " . DB_PREFIX . "user where id = '" . intval($user_id) . "'");

            //如果开启公会并且开启无抽成模式，支付宝 将无法提现ljz
            if (defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE == 1) {
                if ($m_config['society_pattern'] == 2) {
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    if (intval($society_info['society_chieftain']) == 1) {
                        $root['error'] = '会长提现，请联系客服!';
                        $root['status'] = 0;
                        ajax_return($root);
                    } elseif (intval($society_info['society_id'])) {
                        $root['error'] = '公会成员不允许提现!';
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                } else if ($m_config['society_pattern'] == 3) {
                    // 独立模式
                    $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
                    // 是否加入公会
                    if (!empty($society_info['society_id'])) {
                        // 判断是否工会长
                        $is_withdraw = $GLOBALS['db']->getOne("SELECT is_withdraw FROM " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                        //
                        if ($is_withdraw == 2) {
                            // 工会长获取手下所有用户的钱钱
                            if (intval($society_info['society_chieftain']) == 1) {
                                $__ticket = $GLOBALS['db']->getOne('SELECT SUM(`ticket`-`refund_ticket`) FROM `' . DB_PREFIX . 'user` WHERE `is_effect` = 1 AND `is_robot` = 0 AND `is_ban` = 0 AND `society_id` = ' . $society_info['society_id']);
                                $user_ticket_info['ticket'] += $__ticket;
                            } else {
                                $root['error'] = '公会成员不允许提现!';
                                $root['status'] = 0;
                                ajax_return($root);
                            }
                        }
                    }
                }
            }
            $user_ticket = $user_ticket_info['ticket'] - $user_ticket_info['refund_ticket']; //可使用的秀票
            //超额判断
            if ($ticket > $user_ticket) {
                $root['error'] = '提现超出限制!不能大于可用提现';
                $root['status'] = 0;
                ajax_return($root);
            }
            // 进入每月提现一次流程
            $month_carry_one = intval($m_config['month_carry_one']) ? 1 : 0; //提现配置，1：每月提现1次，0：无限制
            $day_carry_one = intval($m_config['day_carry_one']) ? 1 : 0;
            if ($month_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select pay_time from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and is_pay = 3");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ym") == to_date(get_gmtime(), 'Ym'))) {
                    $root['error'] = '每月只能提现一次，本月已经提现过了';
                    $root['status'] = 0;
                    ajax_return($root);
                } else {
                    //查看本月允许提现时间，精确计算到天
                    if ((to_date(get_gmtime(), 'd') > intval($m_config['month_carry_max']) || to_date(get_gmtime(), 'd') < intval($m_config['month_carry_min'])) && intval($m_config['month_carry_max']) != 0 && intval($m_config['month_carry_min']) != 0) {
                        $root['error'] = '每月' . intval($m_config['month_carry_min']) . '日到' . intval($m_config['month_carry_max']) . '日才能提现';
                        $root['status'] = 0;
                        ajax_return($root);
                    }
                }
            }
            if ($day_carry_one) {
                $ready_refund_info = $GLOBALS['db']->getRow("select pay_time from " . DB_PREFIX . "user_refund where user_id = " . intval($user_id) . " and is_pay = 3 ORDER BY id DESC LIMIT 1");
                $pay_time = $ready_refund_info['pay_time'];
                //本月是否有提现
                if ($pay_time != '' && (to_date($pay_time, "Ymd") == to_date(get_gmtime(), 'Ymd'))) {
                    $root['error'] = '每日只能提现一次，本日已经提现过了';
                    $root['status'] = 0;
                    ajax_return($root);
                }
            }
            //百媚模式下代理会员提现
            if (defined('OPEN_BM') && OPEN_BM) {
                $bm_config = load_auto_cache("bm_config");
                $bm_pid = intval($bm_config['bm_pid']);
                $bm_user = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id=" . intval($user_id) . " and( bm_pid=" . $bm_pid . " or bm_pid=0)");
                $bm_promoter = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "bm_promoter where user_id=" . intval($user_id));
                if ($bm_user == '' || $bm_promoter) {
                    $root['error'] = $m_config['bm_point'];
                    $root['status'] = 0;
                    ajax_return($root);
                }

            }

            //提现最小值
            $ticket_catty_min = $m_config['ticket_catty_min'] > 1 ? $m_config['ticket_catty_min'] : 1;
            //最小判断
            if ($money < $ticket_catty_min) {
                $root['error'] = '银行卡提现单笔不能少于' . $ticket_catty_min . '元';
                $root['status'] = 0;
                ajax_return($root);
            }

            //取用户当日提现金额之和
            //create_time储存的是格林威治时间。使用时需转换为北京时间
            $refunded_money_sql = "select sum(money) from " . DB_PREFIX . "user_refund where user_id=$user_id and (is_pay=0 or is_pay=1 or is_pay=3) and DATE_FORMAT(FROM_UNIXTIME(create_time+28800),'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d')";
            $refunded_money = $GLOBALS['db']->getOne($refunded_money_sql);
            $total_money = floatval($refunded_money) + $money;
            //提现最大值
            $day_cash_max = intval($m_config['day_cash_max']);
            //总和判断
            if ($total_money > $day_cash_max) {
                $root['error'] = '银行卡每日提现总额不能多于' . $day_cash_max . '元';
                $root['status'] = 0;
                ajax_return($root);
            }

            $refund_data['money'] = $money;
            $refund_data['user_bank_id'] = -1;
            $refund_data['ticket'] = $ticket;
            $refund_data['user_id'] = $user_id;
            $refund_data['create_time'] = NOW_TIME;
            $refund_data['memo'] = $memo;
            $refund_data['withdrawals_type'] = 3;
            $GLOBALS['db']->autoExecute(DB_PREFIX . "user_refund", $refund_data);
        }
        ajax_return($root);
    }
    //竞拍收入明细
    //$type  0 --已结算
    //$type  1 --待结算
    public function income()
    {

        // if (intval($_REQUEST['details'])==1) {
        //            $this->goods_income_details();
        //        }
        $root = array();
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            api_ajax_return($root);
        }

        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $type = intval($_REQUEST['type']);
        $is_pai = intval($_REQUEST['is_pai']);

        $time = NOW_TIME;
        $end_year = to_date($time, 'Y');
        $end_month = to_date($time, 'm');

        if ($year == 0) {
            $year = $end_year;
            $month = $end_month;
        }

        $rs = FanweServiceCall("user_center", "pai_income_details", array("user_id" => $user_id, "year" => $year, "month" => $month, "type" => $type, "is_pai" => $is_pai));

        if ($rs['status'] == 1) {

            $root['type'] = $type;
            $root['status'] = $rs['status'];
            $root['ticket'] = $rs['cumulative'];
            $root['pending'] = $rs['settlement'];
            $root['now_year'] = $year;
            $root['now_month'] = $month;
            $root['end_year'] = intval($end_year);
            $root['end_month'] = intval($end_month);
            $root['list'] = $rs['details'];
            $root['page'] = array('page' => 1, 'has_next' => 0);
        }

        api_ajax_return($root);
    }

    //商品收入明细
    //$type  1 --已结算
    //$type  2 --待结算
    //$tupe  3 --无效
    public function goods_income_details()
    {
        $root = array();
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            api_ajax_return($root);
        }

        $page = intval($_REQUEST['page']);
        $page_size = 20;
        $time = NOW_TIME;
        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $type = intval($_REQUEST['type']);

        $end_year = to_date($time, 'Y');
        $end_month = to_date($time, 'm');

        if ($year == 0) {
            $year = $end_year;
            $month = $end_month;
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/shop/pai_podcast.action.php');
        $pai_podcast = new pai_podcastCModule();
        $rs = $pai_podcast->commodity_profitlist($page, $page_size, $time, $year, $month, $type);

        if ($rs['status'] == 1) {
            if ($rs['OrderIncome']['totalAccount'] == '') {
                $rs['OrderIncome']['totalAccount'] = 0;
            }
            if ($rs['OrderIncome']['waitAccount'] == '') {
                $rs['OrderIncome']['waitAccount'] = 0;
            }
            if ($rs['ProfitOrder'] == '') {
                $rs['ProfitOrder'] = array();
            }

            $root['type'] = $type;
            $root['status'] = $rs['status'];
            $root['ticket'] = $rs['OrderIncome']['totalAccount'];
            $root['pending'] = $rs['OrderIncome']['waitAccount'];
            $root['now_year'] = $year;
            $root['now_month'] = $month;
            $root['end_year'] = intval($end_year);
            $root['end_month'] = intval($end_month);
            $root['list'] = $rs['ProfitOrder'];
            $root['page'] = array('page' => 1, 'has_next' => 0);
        }

        api_ajax_return($root);
    }
    /*
     * 支付宝认证
     */
    public function authent_alipay()
    {
        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        $user_id = intval($GLOBALS['user_info']['id']);
        $request = $_REQUEST;
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            ajax_return($root);
        } else {
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            $aliConnect = new aliConnectAPI($m_config['alipay_partner'], $m_config['alipay_key']);
            if (intval($GLOBALS['db']->getOne("select id from fanwe_user where v_type=3 and id=" . $user_id . " and alipay_authent_token <>''"))) {
                $root['status'] = 0;
                $root['error'] = '用户支付宝已认证';
                echo $aliConnect->build_html($root['error']);die;
            }

            if (strim($request['is_success']) == 'T') {
                //支付宝用户号
                $alipay_user_id = intval($request['user_id']);
                //授权令牌
                $token = strim($request['token']);
                //真实姓名
                $real_name = strim($_REQUEST['real_name']);
                require_once APP_ROOT_PATH . "system/libs/user.php";
                $user_data = array();
                $user_data['id'] = $user_id;
                $user_data['alipay_user_id'] = $alipay_user_id;
                $user_data['alipay_name'] = $real_name;
                $user_data['alipay_authent_token'] = $token;
                $user_data['v_type'] = 3;
                $root = AuthentAlipayUser($user_data);
                echo $aliConnect->buildRequestForm($root['error']);die;
            }

            if ((!defined('OPEN_AUTHENT_ALIPAY') || OPEN_AUTHENT_ALIPAY == 0) && intval($m_config['authent_alipay']) == 0) {
                $root['status'] = 0;
                $root['error'] = "支付宝一键认证未开启";
                ajax_return($root);
            } else {
                $aliConnect->get_display_code();
            }
        }

    }

    /*
     * 更新推荐人字段
     */
    public function update_p_user_id()
    {

        $root = array('status' => 1, 'error' => '');
        $user_id = intval($GLOBALS['user_info']['id']);
        $request = $_REQUEST;
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            ajax_return($root);
        }

        if (intval($GLOBALS['user_info']['p_user_id']) > 0) {
            $root['error'] = "推荐人已存在！";
            $root['status'] = 0;
            ajax_return($root);
        }

        $p_user_id = intval($_REQUEST['p_user_id']);
        $p_user_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id =" . $p_user_id);

        if (intval($p_user_id) == 0) {
            $root['error'] = "推荐人不存在！";
            $root['status'] = 0;
            ajax_return($root);
        }

        $data = array(
            'p_user_id' => $p_user_id
        );

        $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, "UPDATE", "id=" . $user_id);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_redis->update_db($user_id, $data);

        //更新session
        $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $user_id);
        es_session::set("user_info", $user_info);

        api_ajax_return($root);

    }

    /*
     * 获取推荐人字段
     */
    public function get_p_user_id()
    {

        $root = array('status' => 1, 'error' => '');
        $user_id = intval($GLOBALS['user_info']['id']);
        $request = $_REQUEST;
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            ajax_return($root);
        }

        $root['p_user_id'] = intval($GLOBALS['user_info']['p_user_id']);

        api_ajax_return($root);

    }
    //认证状态提示消息
    public function get_investor_info($authent_type)
    {
        $type = intval($authent_type);
        $info = '';
        if ($type == 0) {
            $info = '带星号项为必填项,为了保证您的利益,请如实填写';
        } elseif ($type == 1) {
            $info = '您的认证正在审核中,敬请等待';
        } elseif ($type == 2) {
            $info = '恭喜您认证成功';
        } elseif ($type == 3) {
            $info = '认证未通过,请修改认证信息后再认证';
        }
        return $info;
    }

    //分享收益
    public function share_profit()
    {

        $user_id = intval($_REQUEST['user_id']);
        if ($user_id == 0) {
            $user_id = $GLOBALS['user_info']['id'];
        }
        if ($user_id == 0) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $page = intval($_REQUEST['p']);
            if ($page == 0) {
                $page = 1;
            }
            $page_size = 10;
            $limit = (($page - 1) * $page_size) . "," . $page_size;
            //邀请用户列表 && 收益列表
            $sql = "select sh.id,sum(sh.ticket) as ticket,sh.from_user_id as user_id,u.head_image,u.nick_name as user_name
					from " . DB_PREFIX . "share_profit_log sh left join " . DB_PREFIX . "user as u
					on u.id = sh.from_user_id
					where sh.to_user_id = 168198
					group by sh.from_user_id order by sh.ticket desc limit " . $limit;
            $list = $GLOBALS['db']->getAll($sql, true, true);

            //计算总收益
            $sql = "select count(id) as number,sum(sh.ticket) as ticket
					from " . DB_PREFIX . "share_profit_log sh
					where to_user_id =168198";
            $ticket_list = $GLOBALS['db']->getRow($sql, true, true);

            foreach ($list as $k => $v) {
                $m_config = load_auto_cache("m_config");
                if ($v['head_image'] == '') {
                    $v['head_image'] = $m_config['app_logo'];
                }

                $list[$k]['head_image'] = get_spec_image($v['head_image'], 150, 150);
                $list[$k]['user_name'] = ($v['user_name']);
            }
            $rs_count = count($list);
            $root['user_list'] = $list;
            $root['profit_list'] = $list;

            //生成二维码
            //路径权限设置
            $dir_name = "share_qrcode";
            if (!is_dir(APP_ROOT_PATH . "public/attachment/" . $dir_name)) {
                @mkdir(APP_ROOT_PATH . "public/attachment/" . $dir_name);
                @chmod(APP_ROOT_PATH . "public/attachment/" . $dir_name, 0777);
            }
            //生成分享二维码图片

            $invite_url = SITE_DOMAIN . APP_ROOT . '/wap/lhfx/index.html?from=singlemessage#/activeIndex?user_id=' . $user_id; //分销邀请链接
            $path_dir = "public/attachment/share_qrcode/qrcode_" . $user_id . ".png"; //二维码图片相对路径
            $qrcode_dir = APP_ROOT_PATH . $path_dir; //二维码图片绝对路径
            if (!is_file($qrcode_dir)) //如果路径图片不存在，则生成保存图片并且写入数据库
            {
                get_qrcode_png($invite_url, $qrcode_dir, ""); //生成二维码图片并保存到绝对路径
                $head_image = "./" . $path_dir;
                syn_to_remote_image_server($head_image, true); //图片同步到服务器上，第二个参数true时本地图片不删除
                //存入图片路径和分销邀请链接至数据库
                $data['qr_code'] = $head_image;
                $data['qrcode_url'] = $invite_url;
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set qr_code='" . $data['qr_code'] . "',qrcode_url='" . $data['qrcode_url'] . "' where id=$user_id", true, true);
            }
            $qr_code = $GLOBALS['db']->getOne("select qr_code from " . DB_PREFIX . "user where id = " . $user_id);
            $root['qr_code'] = get_spec_image($qr_code);
            $root['number'] = $ticket_list['number'];
            $root['total_ticket'] = $ticket_list['ticket'];
            if ($page == 1) {
                $root['page'] = $page;
                $root['has_next'] = 0;
            } else {
                $has_next = ($rs_count > $page * $page_size) ? '1' : '0';
                $root['page'] = $page;
                $root['has_next'] = $has_next;
            }
        }
        api_ajax_return($root);

    }

    public function share_profit_index()
    {
        $user_id = intval($_REQUEST['user_id']);

        if ($user_id == 0) {
            $user_id = $GLOBALS['user_info']['id'];
        }

        if ($user_id == 0) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $url_tree_index = SITE_DOMAIN . APP_ROOT . "/frontEnd/langhua/index.html#/share_profit/$user_id";
            app_redirect($url_tree_index);
        }

    }
}
