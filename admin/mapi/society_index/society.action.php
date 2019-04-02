<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class societyCModule extends baseModule
{
    //获取登录用户的ID
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0
            ));
        }
        return $user_id;
    }

    //主播违规记录
    public function illegal_index()
    {
        $root = array(
            'status' => 0,
            'error' => ''
        );

        $id = intval($_REQUEST['user_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $field = "t.id,u.nick_name,t.to_user_id as user_id,t.create_time,tt.name as tipoff_type,u.head_image";
            $table = DB_PREFIX . "tipoff t";
            $left_join1 = DB_PREFIX . "user u on t.to_user_id=u.id";
            $left_join2 = DB_PREFIX . "tipoff_type tt on t.tipoff_type_id=tt.id";
            $where = "u.society_id=" . $society_info['society_id'];
            if ($id) {
                $where .= " and u.id=$id";
            }
            $start = ($page - 1) * $page_size;
            $sql = "select $field from $table left join $left_join1 left join $left_join2 where $where order by t.create_time desc limit $start,$page_size";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list) {
                $rs_count = $GLOBALS['db']->getOne("select count(t.id) from $table left join $left_join1 where $where");
                foreach ($list as $k => $v) {
                    $list[$k]['create_time'] = to_date($v['create_time']);
                    $list[$k]['nick_name'] = ($v['nick_name']);
                }

                $page = new Page($rs_count, $page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['list'] = $list;
                $root['status'] = 1;
            }
        }

        $root['user_id'] = $id;
        if ($root['user_id'] == 0) {
            $root['user_id'] = "";
        }

        api_ajax_return($root);
    }

    //会员管理
    public function user_manage()
    {
        $user_id = self::getUserId();
        $id = intval($_REQUEST['user_id']);
        $apply_status = intval($_REQUEST['status_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;

        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
        if ($society_info['society_chieftain'] != 1) {
            api_ajax_return(array('status' => 0, 'error' => '您不是公会长'));
        }

        $where = "sa.society_id=" . $society_info['society_id'];
        if ($id > 0) {
//id查询
            $where .= " and sa.user_id=" . $id;
        }
        if ($apply_status == 0) {
            $where .= " and 1=1";
        } else {
            switch ($apply_status) {
                case 1;
                    $where .= " and sa.status=0";
                    break;
                case 2;
                    $where .= " and sa.status=1";
                    break;
                case 3;
                    $where .= " and sa.status=3";
                    break;
            }
        }

        $apply_count = $GLOBALS['db']->getOne("select count(id) from(select count(sa.id) as id from " . DB_PREFIX . "society_apply sa join " . DB_PREFIX . "user u on u.id=sa.user_id where " . $where . " group by sa.user_id) a");

        //$apply_count = $GLOBALS['db']->getOne("select count(id) from " . DB_PREFIX . "society_apply sa where $where");

        $field = "u.id as user_id,u.nick_name,u.sex,u.v_type,u.v_icon,u.head_image,u.signature,u.is_authentication,u.society_chieftain,
                        u.society_settlement_type,sa.status,sa.create_time";
        $start = ($page - 1) * $page_size;
        $end = $page_size;
        $rs_count = $apply_count ? $apply_count : 0;
        $table = DB_PREFIX . "user u";
        $left_join = DB_PREFIX . "society_apply sa on u.id=sa.user_id";
        $sql = "select $field from $table left join $left_join where $where limit $start,$end";
        $list = $GLOBALS['db']->getAll($sql);
        foreach ($list as $k => $v) {
            //筛选出会长
            if ($v['society_chieftain'] == 1) {
                $v['status'] = 9;
            }

            $list[$k]['head_image'] = get_spec_image($v['head_image']);
            $list[$k]['create_time'] = to_date($v['create_time'] - 28800, 'Y-m-d H:i:s');
            $list[$k]['nick_name'] = ($v['nick_name']);
            //审核状态；0申请加入待审核、1加入申请通过、2 加入申请被拒绝，3申请退出公会待审核 4退出公会申请通过 5.退出公会申请被拒
            switch ($v['status']) {
                case 0;
                    $list[$k]['status'] = '加入申请待审核';
                    break;
                case 1;
                    $list[$k]['status'] = '加入申请通过';
                    break;
                case 3;
                    $list[$k]['status'] = '申请退出待审核';
                    break;
                case 9;
                    $list[$k]['status'] = '会长';
                    break;
                default:
                    echo "未知";
            }
            $list[$k]['society_id'] = $society_info['society_id'];

        }
        //$root['url'] = SITE_DOMAIN.'/index.php?ctl=society&act=mobile_login&society_id='.$society_info['society_id'];
        $root['user_id'] = $id;
        if ($root['user_id'] == 0) {
            $root['user_id'] = "";
        }
        $root['status_id'] = $apply_status;
        $page = new Page($rs_count, $page_size);
        $root['page'] = $page->show();
        $root['rs_count'] = $rs_count;
        $root['list'] = $list;
        api_ajax_return($root);
    }

    //搜索
    public function com_search()
    {
        $map = array();

        $map['start_time'] = trim($_REQUEST['start_time']);
        $map['end_time'] = trim($_REQUEST['end_time']);

        if ($map['start_time'] == '' && $map['end_time'] != '') {
            $this->error('开始时间 不能为空');
            exit;
        }

        if ($map['start_time'] != '' && $map['end_time'] == '') {
            $this->error('结束时间 不能为空');
            exit;
        }

        if ($map['start_time'] != '' && $map['end_time'] != '') {

            $d = explode('-', $map['start_time']);
            if (checkdate($d[1], $d[2], $d[0]) == false) {
                $this->error("开始时间不是有效的时间格式:{$map['start_time']}(yyyy-mm-dd)");
                exit;
            }

            $d = explode('-', $map['end_time']);
            if (checkdate($d[1], $d[2], $d[0]) == false) {
                $this->error("结束时间不是有效的时间格式:{$map['end_time']}(yyyy-mm-dd)");
                exit;
            }

            if (to_timespan($map['start_time']) > to_timespan($map['end_time'])) {
                $this->error('开始时间不能大于结束时间:' . $map['start_time'] . '至' . $map['end_time']);
                exit;
            }

            $q_date_diff = 31;
            $this->assign("q_date_diff", $q_date_diff);
            if ($q_date_diff > 0 && (abs(to_timespan($map['end_time']) - to_timespan($map['start_time'])) / 86400 + 1 > $q_date_diff)) {
                $this->error("查询时间间隔不能大于  {$q_date_diff} 天");
                exit;
            }

            $map['start_time'] = to_timespan($map['start_time']);
            $map['end_time'] = to_timespan($map['end_time']);

            /*admin_ajax_return($map['start_time']);*/
        } else {
            $map = array();
        }

        return $map;
    }

    //收益管理
    public function earnings_manage()
    {

        $map = $this->com_search(); //获取时间搜索状态

        //分页参数处理
        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;
        $start = ($page - 1) * $page_size;
        $end = $page_size;

        $chief_id['user_id'] = self::getUserId(); //获取公会长ID
        $society_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "society where user_id=" . $chief_id['user_id']);

        $m_config = load_auto_cache("m_config"); //初始化手机端配置
        if ($m_config['society_pattern'] == 1) {
            $utype = "u1.type in(8,13) and u1.contribution_id = u2.id";
        } elseif ($m_config['society_pattern'] == 2) {
            //$utype = "u1.type in(10,13) ";
            $utype = "u1.type in(10,13) and u1.contribution_id = u2.id";
        }

        $parameter = '';
        $sql_w = '';
        //查看是否有进行时间搜索
        if ($map['start_time'] != '' && $map['end_time'] != '') {
            $parameter .= " log_time between '" . $map['start_time'] . "' and '" . $map['end_time'] . "'&";
            $sql_w .= " log_time between '" . $map['start_time'] . "' and '" . $map['end_time'] . "' and ";
        }
        //查看是否有进行贡献会员的ID或昵称搜索
        if (strim($_REQUEST['mid']) != '') {
            $sql_w = " u2.id like '%" . strim($_REQUEST['mid']) . "%' and ";
        }
        if (strim($_REQUEST['nick_name']) != '') {
            $sql_w = " nick_name like '%" . strim($_REQUEST['nick_name']) . "%' and ";
        }

        $sql_str = "SELECT u2.id as mid,nick_name,u1.ticket,u1.log_time as time " . "FROM " . DB_PREFIX . "user_log u1," . DB_PREFIX . "user u2 where u2.society_id=$society_id and $utype and u1.user_id = " . $chief_id['user_id'];

        $sql_str .= " and " . $sql_w . " 1=1 ";
        $sql_str .= " order by u1.log_time desc limit $start,$end ";
        /*if(strim($_REQUEST['name'])!=''){
        admin_ajax_return($sql_str);
        } */
        $voList = $GLOBALS['db']->getAll($sql_str);

        for ($i = 0; $i < count($voList); $i++) {
            $voList[$i]['time'] = to_date($voList[$i]['time']);
            $voList[$i]['nick_name'] = ($voList[$i]['nick_name']);
        }

        $list['list'] = $voList;

        //获取总量与总金额
        $sql_count = "SELECT sum(u1.ticket) as count " . "FROM " . DB_PREFIX . "user_log u1," . DB_PREFIX . "user u2 where u2.society_id=$society_id and $utype and u1.user_id = " . $chief_id['user_id'];
        $sql_count .= " and " . $sql_w . " 1=1 ";
        /*admin_ajax_return($sql_count);*/

        $count = $GLOBALS['db']->getRow($sql_count);
        //$rs_count = $count ? $count : 0;

        $root = array_merge($chief_id, $list, $count);
        $rs_count = $GLOBALS['db']->getOne("SELECT count(u2.id)  FROM " . DB_PREFIX . "user_log u1," . DB_PREFIX . "user u2 where u2.society_id=$society_id and $utype and u1.user_id = " . $chief_id['user_id'] . " and " . $sql_w . " 1=1 ");
        $page = new Page($rs_count, $page_size);
        $root['page'] = $page->show();
        $root['rs_count'] = $rs_count;
        $root['search_mid'] = $_REQUEST['mid'] ? $_REQUEST['mid'] : '';
        $root['search_nick_name'] = $_REQUEST['nick_name'] ? $_REQUEST['nick_name'] : '';

        api_ajax_return($root);
    }

    //将时间戳差值转换为x时x分x秒
    protected static function timelen_change($timelen)
    {
        if ($timelen > 0) {
            $hour = intval($timelen / 3600);
            $res = $timelen % 3600;
            $minute = intval($res / 60);
            $second = $res % 60;
            return $hour . "时" . $minute . "分" . $second . "秒";
        } else {
            return "";
        }
    }

    //公会月收入
    public function society_income_month()
    {
        $root = array('status' => 1, 'error' => '');
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);

        $date_str = $_REQUEST['date_str'];
        if ($date_str == '') {
            $date_str = date('Y-m', NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));

        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $where = "s.society_id=" . $society_info['society_id'] . " and s.end_Y=$year and s.end_m=$month and (s.society_settlement_type=" . $m_config['society_pattern'] . " )";
            /* if($m_config['society_pattern']==1){
            $where .= " and u.society_chieftain!=1";
            } */
            $sql = "select sum(s.society_number) from " . DB_PREFIX . "society_earning s join " . DB_PREFIX . "user u on s.user_id=u.id where $where";
            //select sum(vote_number) from fanwe_society_earning where society_id=1 and end_Y=2017 and end_m=8
            //log_ljz($sql,__FILE__);
            $ticket = $GLOBALS['db']->getOne($sql); //统计公会成员一个月的直播收入总和
            if (!$ticket) {
                $ticket = 0;
            }
            //区分模式
            if ($m_config['society_pattern'] == 1) {
                $type = "type=13 or user_id=contribution_id";
            } elseif ($m_config['society_pattern'] == 2) {
                $type = "type in(10,13)";
            }
            //查询私信的收益
            $sql1 = "select sum(ticket) from " . DB_PREFIX . "user_log where user_id=$user_id and ($type) and  log_info like'%私信获取收益%' and (FROM_UNIXTIME(log_time,'%Y')=$year and FROM_UNIXTIME(log_time,'%m')=$month)";
            $game_ticket = $GLOBALS['db']->getOne($sql1);
            if ($game_ticket) {
                $ticket = $ticket + $game_ticket;
            }
            $data = array();
            $data['date'] = $date_str;
            $data['ticket'] = $ticket;
            //公会提现比例
            if ($m_config['society_pattern'] == 2) {
//无抽成模式
                $refund_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
                if (floatval($refund_rate) == 0) {
                    $refund_rate = floatval($m_config['society_public_rate']);
                }
            } elseif ($m_config['society_pattern'] == 1) {
//有抽成模式
                $refund_rate = $GLOBALS['db']->getOne("select alone_ticket_ratio from " . DB_PREFIX . "user where id=$user_id");
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_level = $user_redis->getOne_db($user_id, 'user_level');
                $refund_role = load_auto_cache("refund_role_level", array('level' => $user_level)); //初始化手机端配置

                if (floatval($refund_rate) == 0) {
                    if (!empty($refund_role)) {
                        $refund_rate = floatval($refund_role['ticket_catty_ratio']);
                    } else {
                        $refund_rate = floatval($m_config['ticket_catty_ratio']);
                    }
                }
                if (!$refund_rate) {
                    $refund_rate = 1;
                }
            }

            $data['total_money'] = $ticket * floatval($refund_rate);
            $data['society_rate'] = strval(floatval($refund_rate) * 100) . "%";
            $data['society_money'] = $ticket * floatval($refund_rate);

            $root['list'] = $data;
            $root['list'] = $data;
        }
        api_ajax_return($root);
    }

    //主播月收入
    public function user_income_month()
    {
        $root = array('status' => 0, 'error' => '');
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from " . DB_PREFIX . "user where id=" . $user_id);

        //搜索的月份
        $date_str = $_REQUEST['date_str'];
        if ($date_str == '') {
            $date_str = date('Y-m', NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;

        //搜索的主播id
        $id = intval($_REQUEST['uid']);

        $m_config = load_auto_cache('m_config');

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_level = $user_redis->getOne_db($id, 'user_level');
        $refund_role = load_auto_cache("refund_role_level", array('level' => $user_level)); //初始化手机端配置
        if (!empty($refund_role)) {
            $m_config['ticket_catty_ratio'] = $refund_role['ticket_catty_ratio'];
        }
        if ($m_config['society_pattern'] == 2) {
//公会模式区分
            $type = "ul.type in(10) and ul.log_info like '%获取收益%' and (ul.contribution_id=0 or ul.contribution_id=ul.user_id) ";
            $society_rate = floatval($m_config['society_user_rate']);
            $ticket = "sum(ul.ticket) as ticket,sum(ul.ticket)*$society_rate as society_private_money ";
        } elseif ($m_config['society_pattern'] == 1) {
            //获取公会长收益的秀票数
            $society_ticket = $GLOBALS['db']->getOne("select sum(ticket)as ticket from " . DB_PREFIX . "user_log where user_id=$user_id and type=8 and user_id<>contribution_id");
            if ($society_ticket == "") {
                $society_ticket = 0;
            }

            $type = "(ul.type in(8,11,13))and ul.log_info like '%获取收益%'";
            $type_a = "(ul.type in(8,11))and ul.log_info like '%扣除%'";
            $society_rate = floatval($m_config['ticket_catty_ratio']);
            $ticket = "(sum(ul.ticket)) as ticket,(u.alone_ticket_ratio) as society_private_money ";
        }

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            if ($society_rate > 0) {
                $society_user = $GLOBALS['db']->getAll("select id from " . DB_PREFIX . "user where society_id=" . $society_info['society_id']);
                $array = [];
                foreach ($society_user as $k => $v) {
                    array_push($array, $v['id']);
                }
                $society_user = implode(',', $array);
                $where = "ul.society_id=" . $society_info['society_id'] . " and u.id in($society_user) and ($type) and (FROM_UNIXTIME(log_time,'%Y')=$year and FROM_UNIXTIME(log_time,'%m')=$month)";
                $table = "select u.id as user_id,u.nick_name,$ticket from " . DB_PREFIX . "user u left join " . DB_PREFIX . "user_log ul on ul.user_id=u.id where $where group by u.id";

                if ($id > 0) {
                    $where1 = " s.user_id=" . $id;
                } else {
                    $where1 = " 1=1";
                }

                $start = ($page - 1) * $page_size;
                $sql = "select * from ($table) as s where $where1 limit $start,$page_size";
                $list = $GLOBALS['db']->getAll($sql); //当月收入

                if ($m_config['society_pattern'] == 1) {
                    $where_a = "ul.society_id=" . $society_info['society_id'] . " and u.id in($society_user) and ($type_a) and (FROM_UNIXTIME(log_time,'%Y')=$year and FROM_UNIXTIME(log_time,'%m')=$month)";
                    $table_a = "select u.id as user_id,u.nick_name,$ticket from " . DB_PREFIX . "user u left join " . DB_PREFIX . "user_log ul on ul.user_id=u.id where $where_a group by u.id";
                    $sql_a = "select * from ($table_a) as s where $where1 limit $start,$page_size";
                    $list_a = $GLOBALS['db']->getAll($sql_a); //当月支出
                    //当月收入-当月支出
                    for ($i = 0; $i < count($list_a); $i++) {
                        for ($y = 0; $y < count($list); $y++) {
                            if ($list_a[$i]['user_id'] == $list[$y]['user_id']) {
                                $list[$y]['ticket'] = $list[$y]['ticket'] - $list_a[$i]['ticket'];
                            }
                        }
                    }
                    //每个主播单独结算比例
                    foreach ($list as $k => $v) {
                        if ($list[$k]['society_private_money'] != 0) {
                            $list[$k]['society_private_money'] = $list[$k]['ticket'] * $v['society_private_money'];
                        } else {
                            $list[$k]['society_private_money'] = $list[$k]['ticket'] * floatval($m_config['ticket_catty_ratio']);
                        }
                    }
                }

                foreach ($list as $k => $v) {
                    $list[$k]['nick_name'] = ($v['nick_name']);
                }

                if ($list) {
                    $rs_count = count($list);
                    $page = new Page($rs_count, $page_size);
                    $root['page'] = $page->show();
                    $root['date'] = $date_str;
                    $root['rs_count'] = $rs_count;
                    $root['list'] = $list;
                    $root['status'] = 1;
                    $root['user_id'] = $user_id;
                    $root['uid'] = $id;
                    if ($root['uid'] == 0) {
                        $root['uid'] = "";
                    }
                } else {
                    $root['date'] = $date_str;
                    $root['status'] = 1;
                    $root['uid'] = $id;
                    if ($root['uid'] == 0) {
                        $root['uid'] = "";
                    }
                }

            } else {
                $root['status'] = 0;
                $root['error'] = '收益参数错误';
            }

        }
        $root['date'] = $date_str;
        api_ajax_return($root);
    }

    //主播日收入
    public function user_income()
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from " . DB_PREFIX . "user where id=" . $user_id);
        $date_str = $_REQUEST['date_str'];

        //搜索的日期
        if ($date_str == '') {
            $date_str = date('Y-m-d', NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));
        $day = intval(substr($date_str, 8, 2));

        //搜索的主播id
        $id = intval($_REQUEST['uid']);

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;

        $m_config = load_auto_cache('m_config');

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user_level = $user_redis->getOne_db($id, 'user_level');
        $refund_role = load_auto_cache("refund_role_level", array('level' => $user_level)); //初始化手机端配置
        if (!empty($refund_role)) {
            $m_config['ticket_catty_ratio'] = $refund_role['ticket_catty_ratio'];
        }
        if ($m_config['society_pattern'] == 2) {
//公会模式区分
            $type = "ul.type in(10) and ul.log_info like '%获取收益%' and (ul.contribution_id=0 or ul.contribution_id=ul.user_id) ";
            $society_rate = floatval($m_config['society_user_rate']);
            $ticket = "sum(ul.ticket) as ticket,sum(ul.ticket)*$society_rate as society_money ";
        } elseif ($m_config['society_pattern'] == 1) {
            //获取公会长收益的秀票数
            $society_ticket = $GLOBALS['db']->getOne("select sum(ticket)as ticket from " . DB_PREFIX . "user_log where user_id=$user_id and type=8 and user_id<>contribution_id");
            if ($society_ticket == "") {
                $society_ticket = 0;
            }

            $type = "(ul.type in(8,11,13))and ul.log_info like '%获取收益%'";
            $type_a = "(ul.type in(8,11))and ul.log_info like '%扣除%'";
            $society_rate = floatval($m_config['ticket_catty_ratio']);
            $ticket = "(sum(ul.ticket)) as ticket,(u.alone_ticket_ratio) as society_money ";
        }

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            if ($society_rate > 0) {
                $society_user = $GLOBALS['db']->getAll("select id from " . DB_PREFIX . "user where society_id=" . $society_info['society_id']);
                $array = [];
                foreach ($society_user as $k => $v) {
                    array_push($array, $v['id']);
                }
                $society_user = implode(',', $array);
                $where = "ul.society_id=" . $society_info['society_id'] . " and u.id in($society_user) and ($type) and (FROM_UNIXTIME(log_time,'%Y')=$year and FROM_UNIXTIME(log_time,'%m')=$month and FROM_UNIXTIME(log_time,'%d')=$day)";
                $table = "select u.id as user_id,u.nick_name,$ticket from " . DB_PREFIX . "user u left join " . DB_PREFIX . "user_log ul on ul.user_id=u.id where $where group by u.id";
                if ($id > 0) {
                    $where1 = " s.user_id=" . $id;
                } else {
                    $where1 = " 1=1";
                }

                $start = ($page - 1) * $page_size;
                $sql = "select * from ($table) as s where $where1 limit $start,$page_size";
                $list = $GLOBALS['db']->getAll($sql); //当天收入

                if ($m_config['society_pattern'] == 1) {
                    $where_a = "ul.society_id=" . $society_info['society_id'] . " and u.id in($society_user) and ($type_a) and (FROM_UNIXTIME(log_time,'%Y')=$year and FROM_UNIXTIME(log_time,'%m')=$month and FROM_UNIXTIME(log_time,'%d')=$day)";
                    $table_a = "select u.id as user_id,u.nick_name,$ticket from " . DB_PREFIX . "user u left join " . DB_PREFIX . "user_log ul on ul.user_id=u.id where $where_a group by u.id";
                    $sql_a = "select * from ($table_a) as s where $where1 limit $start,$page_size";
                    $list_a = $GLOBALS['db']->getAll($sql_a); //当天支出
                    //当天收入-当天支出
                    for ($i = 0; $i < count($list_a); $i++) {
                        for ($y = 0; $y < count($list); $y++) {
                            if ($list_a[$i]['user_id'] == $list[$y]['user_id']) {
                                $list[$y]['ticket'] = $list[$y]['ticket'] - $list_a[$i]['ticket'];
                            }
                        }
                    }
                    //每个主播单独结算比例
                    foreach ($list as $k => $v) {
                        if ($list[$k]['society_money'] != 0) {
                            $list[$k]['society_money'] = $list[$k]['ticket'] * $v['society_money'];
                        } else {
                            $list[$k]['society_money'] = $list[$k]['ticket'] * floatval($m_config['ticket_catty_ratio']);
                        }
                    }
                }

                foreach ($list as $k => $v) {
                    $list[$k]['nick_name'] = ($v['nick_name']);
                }
                $now_date = to_date(NOW_TIME, 'Y-m-d');
                if ($list) {
                    $rs_count = count($list);
                    $page = new Page($rs_count, $page_size);
                    $root['page'] = $page->show();
                    $root['date'] = $date_str;
                    $root['rs_count'] = $rs_count;
                    $root['now_date'] = $now_date;
                    $root['uid'] = $id;
                    if ($root['uid'] == 0) {
                        $root['uid'] = "";
                    }
                    $root['list'] = $list;
                } else {
                    $root['date'] = $date_str;
                    $root['status'] = 1;
                    $root['uid'] = $id;
                    if ($root['uid'] == 0) {
                        $root['uid'] = "";
                    }
                }
            } else {
                $root['status'] = 0;
                $root['error'] = '收益参数错误';
            }

        }
        api_ajax_return($root);
    }

    //主播直播时长
    public function user_live_length()
    {
        $root = array(
            'status' => 0,
            'error' => ''
        );
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);

        //搜索的主播id
        $id = intval($_REQUEST['uid']);

        //搜索的日期
        //$date_str = $_REQUEST['date_str'];
        $begin_date = $_REQUEST['begin_date']; //开始时间
        if ($begin_date == "") {
            $begin_date = date('Y-m-d', NOW_TIME);
        }
        $begin_y = intval(substr($begin_date, 0, 4));
        $begin_m = intval(substr($begin_date, 5, 2));
        $begin_d = intval(substr($begin_date, 8, 2));

        $end_date = $_REQUEST['end_date']; //结束时间
        if ($end_date == "") {
            $end_date = date('Y-m-d', NOW_TIME);
        }
        $end_y = intval(substr($end_date, 0, 4));
        $end_m = intval(substr($end_date, 5, 2));
        $end_d = intval(substr($end_date, 8, 2));
        /* if(strtotime($begin_date) > strtotime($end_date)){
        $root['status'] =0;
        $root['error'] = '开始时间不能大于结束时间';
        } */

        //分页
        fanwe_require(APP_ROOT_PATH . 'mapi/society_index/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $page_size = 10;
        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $field = "s.user_id,u.nick_name,sum(s.timelen) as timelen";
            $table = DB_PREFIX . "society_earning s";
            $where = "s.society_id=" . $society_info['society_id'] . " and (s.society_settlement_type=" . $m_config['society_pattern'] . ")";
            if ($id > 0) {
                $where .= " and s.user_id=" . $id;
            }
            if ($date_str == '') {
                $date_arr = explode('-', $date_str);
                $where .= " and s.end_Y>=" . $begin_y . ". and s.end_m>=" . $begin_m . " and s.end_d>=" . $begin_d . " and s.end_Y<=" . $end_y . ". and s.end_m<=" . $end_m . " and s.end_d<=" . $end_d;
            }
            $left_join = DB_PREFIX . "user u on s.user_id=u.id";
            $start = ($page - 1) * $page_size;
            $sql = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id limit $start,$page_size";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list) {
                foreach ($list as $k => $v) {
                    $list[$k]['timelen'] = self::timelen_change($v['timelen']);
                    $list[$k]['nick_name'] = ($v['nick_name']);
                }

                //                $rs_count = $GLOBALS['db']->getOne("select count(*) from $table where $where GROUP BY s.user_id");
                $rs_count = count($list);
                $month_e = date('Y-m-d', time()); //2017-03-19 结束时间
                $month_s = date("Y-m-d", strtotime($month_e . " -1 month -1 day")); //2017-03-19 开始时间
                $month_date = $month_s . ' - ' . $month_e;
                $page = new Page($rs_count, $page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['list'] = $list;
                //                $root['month_date'] = $month_date;
                $root['status'] = 1;
            }
        }
        $root['begin_date'] = $begin_date;
        $root['end_date'] = $end_date;
        $root['uid'] = $id;
        if ($root['uid'] == 0) {
            $root['uid'] = "";
        }
        api_ajax_return($root);
    }

    //导出公会月收入
    public function society_csv()
    {
        $root = array(
            'status' => 1,
            'error' => ''
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);

        //搜索的月份
        $date_str = $_REQUEST['date_str'];
        if ($date_str == '') {
            $date_str = to_date(NOW_TIME, 'Y-m');
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));

        $m_config = load_auto_cache('m_config');
        $content = iconv('utf-8', 'gbk', '日期,总秀票,分成比例,结算金额,实际收入');
        $content .= "\n";

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $where = "society_id=" . $society_info['society_id'] . " and end_Y=$year and end_m=$month";
            $sql = "select sum(vote_number) from " . DB_PREFIX . "society_earning where $where";
            $ticket = $GLOBALS['db']->getOne($sql);

            if (!$ticket) {
                $ticket = 0;
            }
            $society_rate = $GLOBALS['db']->getOne("select refund_rate from " . DB_PREFIX . "society where id=" . $society_info['society_id']);
            if (floatval($society_rate) == 0) {
                $society_rate = floatval($m_config['society_public_rate']);
            }
            $society_money = $ticket * floatval($society_rate);
            $society_rate = strval(floatval($society_rate) * 100) . "%";
            $time = '1970-01-01 16:00:00';
            $data = array();
            $data['date'] = '"' . iconv('utf-8', 'gbk', $date_str) . '"';
            $data['date'] = str_replace($time, '0', $date_str);
            $data['ticket'] = '"' . iconv('utf-8', 'gbk', $ticket) . '"';
            $data['society_rate'] = '"' . iconv('utf-8', 'gbk', $society_rate) . '"';
            $data['society_money'] = '"' . iconv('utf-8', 'gbk', $society_money) . '"';
            $data['total_money'] = '"' . iconv('utf-8', 'gbk', $society_money) . '"';

            $content .= implode(",", $data) . "\n";

            header("Content-Disposition: attachment; filename=society_income.csv");
            echo $content;
        } else {
            header("Content-Disposition: attachment; filename=society_income.csv");
            echo $content;
        }
    }

    //导出主播月收入
    public function user_month_csv()
    {
        $root = array('status' => 0, 'error' => '');
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain,society_settlement_type from " . DB_PREFIX . "user where id=" . $user_id);

        //搜索的月份
        $date_str = $_REQUEST['date_str'];
        if ($date_str == '') {
            $date_str = date('Y-m', NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        $content = iconv('utf-8', 'gbk', '日期,主播ID,主播昵称,总秀票,结算金额,实际收入');
        $content .= "\n";

        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $society_rate = floatval($m_config['society_user_public_rate']);
            if ($society_rate > 0) {
                $field = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*" . $society_rate . " as society_private_money";
                $table = DB_PREFIX . "society_earning s";
                $where = "s.society_id=" . $society_info['society_id'] . " and s.end_Y=$year and s.end_m=$month";
                if ($id > 0) {
                    $where .= " and s.user_id=" . $id;
                }
                $left_join = DB_PREFIX . "user u on s.user_id=u.id";
                $sql = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
                $list = $GLOBALS['db']->getAll($sql);
                if ($list) {
                    $data = array();
                    foreach ($list as $k => $v) {
                        $list[$k]['nick_name'] = ($v['nick_name']);
                        $data['date'] = '"' . iconv('utf-8', 'gbk', $date_str) . '"';
                        $data['user_id'] = '"' . iconv('utf-8', 'gbk', $list[$k]['user_id']) . '"';
                        $data['nick_name'] = '"' . iconv('utf-8', 'gbk', $list[$k]['nick_name']) . '"';
                        $data['ticket'] = '"' . iconv('utf-8', 'gbk', $list[$k]['ticket']) . '"';
                        $data['society_private_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $data['total_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $content .= implode(",", $data) . "\n";
                    }
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                } else {
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                }
            } else {
                $root['status'] = 0;
                $root['error'] = '收益参数错误';
            }

        } else {
            header("Content-Disposition: attachment; filename=user_month_income.csv");
            echo $content;
        }
    }

    //导出主播日收入
    public function user_day_csv()
    {
        $root = array(
            'status' => 0,
            'error' => ''
        );

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);
        $date_str = $_REQUEST['date_str'];

        //搜索的日期
        if ($date_str == '') {
            $date_str = date('Y-m-d', NOW_TIME);
        }
        $year = intval(substr($date_str, 0, 4));
        $month = intval(substr($date_str, 5, 2));
        $day = intval(substr($date_str, 8, 2));

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        $content = iconv('utf-8', 'gbk', '日期,主播ID,主播昵称,结算方式,总秀票,结算金额,实际收入');
        $content .= "\n";

        $m_config = load_auto_cache('m_config');
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $society_rate = floatval($m_config['society_user_public_rate']);
            if ($society_rate > 0) {
                $field = "s.user_id,u.nick_name,u.society_settlement_type,sum(s.vote_number) as ticket,sum(s.vote_number)*" . $society_rate . " as society_private_money";
                $table = DB_PREFIX . "society_earning s";
                $where = "s.society_id=" . $society_info['society_id'] . " and s.end_Y=$year and s.end_m=$month and s.end_d=$day";
                if ($id > 0) {
                    $where .= " and s.user_id=" . $id;
                }
                $left_join = DB_PREFIX . "user u on s.user_id=u.id";
                $sql = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
                $list = $GLOBALS['db']->getAll($sql);
                if ($list) {

                    $data = array();
                    foreach ($list as $k => $v) {
                        $list[$k]['nick_name'] = ($v['nick_name']);
                        $data['date'] = '"' . iconv('utf-8', 'gbk', $date_str) . '"';
                        $data['user_id'] = '"' . iconv('utf-8', 'gbk', $list[$k]['user_id']) . '"';
                        $data['nick_name'] = '"' . iconv('utf-8', 'gbk', $list[$k]['nick_name']) . '"';
                        $data['ticket'] = '"' . iconv('utf-8', 'gbk', $list[$k]['ticket']) . '"';
                        $data['society_private_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $data['total_money'] = '"' . iconv('utf-8', 'gbk', $list[$k]['society_private_money']) . '"';
                        $content .= implode(",", $data) . "\n";
                    }
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                } else {
                    header("Content-Disposition: attachment; filename=user_month_income.csv");
                    echo $content;
                }
            } else {
                echo '参数错误';
            }
        } else {
            header("Content-Disposition: attachment; filename=user_month_income.csv");
            echo $content;
        }
    }

    //导出主播直播时长
    public function user_live_length_csv()
    {
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from " . DB_PREFIX . "user where id=" . $user_id);

        //搜索的主播id
        $id = intval($_REQUEST['id']);

        //搜索的日期
        $date_str = $_REQUEST['date_str'];

        $content = iconv('utf-8', 'gbk', '主播ID,主播昵称,直播时长');
        $content .= "\n";

        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1) {
            $field = "s.user_id,u.nick_name,sum(s.timelen) as timelen";
            $table = DB_PREFIX . "society_earning s";
            $where = "s.society_id=" . $society_info['society_id'];
            if ($id > 0) {
                $where .= " and s.user_id=" . $id;
            }
            if ($date_str != '') {
                $date_arr = explode('-', $date_str);
                $where .= " and s.end_Y>=" . $date_arr[0] . ". and s.end_m>=" . $date_arr[1] . " and s.end_d>=" . $date_arr[2] . "
                            and s.end_Y<=" . $date_arr[3] . ". and s.end_m<=" . $date_arr[4] . " and s.end_d<=" . $date_arr[5];
            }
            $left_join = DB_PREFIX . "user u on s.user_id=u.id";
            $sql = "select $field from $table LEFT JOIN  $left_join where $where group by s.user_id";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list) {
                foreach ($list as $k => $v) {
                    $list[$k]['nick_name'] = ($v['nick_name']);
                    $list[$k]['timelen'] = self::timelen_change($v['timelen']);
                    $data['user_id'] = '"' . iconv('utf-8', 'gbk', $list[$k]['user_id']) . '"';
                    $data['nick_name'] = '"' . iconv('utf-8', 'gbk', $list[$k]['nick_name']) . '"';
                    $data['timelen'] = '"' . iconv('utf-8', 'gbk', $list[$k]['timelen']) . '"';
                    $content .= implode(",", $data) . "\n";
                }
                header("Content-Disposition: attachment; filename=user_month_income.csv");
                echo $content;
            } else {
                header("Content-Disposition: attachment; filename=user_month_income.csv");
                echo $content;
            }
        } else {
            header("Content-Disposition: attachment; filename=user_month_income.csv");
            echo $content;
        }
    }

    public function society_details()
    {
        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select s.*,u.nick_name from " . DB_PREFIX . "society s join " . DB_PREFIX . "user u on s.user_id=u.id where s.user_id=" . $user_id);

        $society_info['logo'] = get_spec_image($society_info['logo']);
        $society_info['create_time'] = to_date($society_info['create_time'], 'Y-m-d');
        $society_info['nick_name'] = ($society_info['nick_name']);
        switch ($society_info['status']) {
            case 0:
                $society_info['status'] = "未审核";
                break;
            case 1:
                $society_info['status'] = "审核通过";
                break;
            case 2:
                $society_info['status'] = "拒绝通过";
                break;
        }
        $m_config = load_auto_cache('m_config');
        $society_info['open_society_code'] = $m_config['open_society_code'];
        //admin_ajax_return($society_info);
        api_ajax_return($society_info);
    }

}
