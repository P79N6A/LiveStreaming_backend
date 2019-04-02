<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH . 'mapi/lib/user.action.php');
fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');

class userCModule extends userModule
{
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0
            ));
        }
        return $user_id;
    }

    public function tipoff_type()
    {
        api_ajax_return(array(
            'status' => 1,
            'list' => load_auto_cache("tipoff_type_list")
        ));
    }

    //个人中心-我的资料
    public function userinfo()
    {

        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0
            ));
        }

        $user_id = intval($GLOBALS['user_info']['id']); //自己ID
        $podcast_id = intval($_REQUEST['podcast_id']); //主播id
        $to_user_id = intval($_REQUEST['to_user_id']); //被查看的用户id

        $fields = 'id as user_id,nick_name,signature,sex,city,focus_count,video_count,is_authentication,head_image,fans_count,ticket,refund_ticket,user_level,use_diamonds,diamonds,v_type,v_icon,v_explain,mobile';
        if ($podcast_id) {
            //主播资料
            $podcast = $GLOBALS['db']->getRow("SELECT $fields FROM " . DB_PREFIX . "user WHERE id=" . $podcast_id, true,
                true);
            $podcast['head_image'] = get_spec_image($podcast['head_image']);
            api_ajax_return(array('error' => '', 'status' => 1, 'user' => $podcast));

        } elseif ($to_user_id) {
            //被查看的用户资料
            $to_user = $GLOBALS['db']->getRow("SELECT $fields FROM " . DB_PREFIX . "user WHERE id=" . $to_user_id, true,
                true);
            $to_user['head_image'] = get_spec_image($to_user['head_image']);
            api_ajax_return(array('error' => '', 'status' => 1, 'user' => $to_user));

        } else {
            //自己资料
            $user = $GLOBALS['db']->getRow("SELECT $fields FROM " . DB_PREFIX . "user WHERE id=" . $user_id, true,
                true);
            $user['head_image'] = get_spec_image($user['head_image']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $data = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id= " . $user_id, true, true);
            $data['head_image'] = get_spec_image($data['head_image']);
            $user_redis->update_db($data['id'], $data);
            es_session::set("user_info", $data);
            $GLOBALS['user_info'] = $data;
            api_ajax_return(array(
                'error' => '',
                'status' => 1,
                'user' => $user,
                'useable_ticket' => intval($user['ticket'] - $user['refund_ticket']),
                'page_title' => '个人中心'
            ));
        }

    }

    //个人中心-修改昵称接口
    public function update()
    {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0
            ));
        }
        $room_id = strim($_REQUEST['room_id']); //要修改的直播
        $user_id = intval($GLOBALS['user_info']['id']); //自己ID
        $nick_name = strim($_REQUEST['nick_name']); //要修改的昵称
        $head_image = strim($_REQUEST['head_image']); //要修改的头像地址
        $room_title = strim($_REQUEST['room_title']); //要修改的房间名称
        $data = array();
        if ($nick_name) {
            if (mb_strlen($nick_name) > 15) {
                api_ajax_return(array(
                    'status' => '0',
                    'error' => '名称限制15字以内'
                ));
            }
            $m_config = load_auto_cache("m_config"); //初始化手机端配置
            //判断昵称是否包含敏感词汇
            if ($m_config['name_limit'] == 1) {
                $limit_sql = $GLOBALS['db']->getCol("SELECT name FROM " . DB_PREFIX . "limit_name");
                $in = in_array($nick_name, $limit_sql);
                if ($in) {
                    api_ajax_return(array("status" => 0, "error" => '昵称包含敏感词汇'));
                } elseif ($GLOBALS['db']->getCol("SELECT name FROM " . DB_PREFIX . "limit_name WHERE '$nick_name' like concat('%',name,'%')")) {
                    $nick_name = str_replace($limit_sql, '*', $nick_name);
                }
            }
            $data['nick_name'] = htmlspecialchars_decode($nick_name);
        }

        if ($head_image) {
            $data['head_image'] = $head_image;
            $data['thumb_head_image'] = get_spec_image($head_image, 40, 40);
        }

        if ($room_title) {
            if (strlen($room_title) < 15) {
                api_ajax_return(array('error' => '直播间名称长度至少5个汉字或10个字母', 'status' => 0));
            }
            if (strlen($room_title) > 40) {
                api_ajax_return(array('error' => '直播间名称长度不超过20个汉字', 'status' => 0));
            }

            $data['room_title'] = $room_title;
            $video_data['room_title'] = $room_title;
        }

        if (empty($data)) {
            api_ajax_return(array('error' => '填写不能为空', 'status' => 0));
        }
        $info = $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, $mode = 'UPDATE', "id=" . $user_id);
        if ($room_id) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video = $video_redis->getRow_db($room_id, array('user_id', 'live_in'));
            if ($video['user_id'] != $user_id) {
                api_ajax_return(array('error' => '非法操作', 'status' => 0));
            }

            if ($video['live_in'] == 1) {
                api_ajax_return(array('error' => '直播已开始', 'status' => 0));
            }

            if ($video['live_in'] != 2) {
                api_ajax_return(array('error' => '直播已结束', 'status' => 0));
            }
            $video = $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $video_data, $mode = 'UPDATE', "id=" . $room_id);
            if ($info && $video) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                $video_redis = new VideoRedisService();
                $video_redis->update_db($room_id, $video_data);
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($user_id, $data);
                //更新session
                $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $user_id);
                es_session::set("user_info", $user_info);
                api_ajax_return(array('error' => '修改成功', 'status' => 1));
            } else {
                api_ajax_return(array('error' => '修改失败', 'status' => 0));
            }
        }
        if ($info) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_redis->update_db($user_id, $data);
            //更新session
            $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $user_id);
            es_session::set("user_info", $user_info);
            if ($video) {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                $video_redis = new VideoRedisService();
                $video_redis->update_db($room_id, $video_data);
            }
            api_ajax_return(array('error' => '修改成功', 'status' => 1));
        } else {
            api_ajax_return(array('error' => '修改失败', 'status' => 0));
        }
    }

    //魅力
    public function charm_podcast()
    {
        $list = load_auto_cache("charm_podcast");
        return $list;
    }

    //财富
    public function rich_list()
    {
        $list = load_auto_cache("rich_list");
        return $list;
    }

    //家族
    public function family_rank()
    {
        $list = load_auto_cache("family_rank");
        return $list;
    }

    //新秀
    public function newstar_rank()
    {
        $list = load_auto_cache("newstar_rank");
        return $list;
    }

    //个人中心秀票贡献榜
    public function contribution_list()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array();
        $user_id = intval($_REQUEST['user_id']); //被查看的用户id
        if ($user_id == 0 || $user_id == '') {
            $user_id = intval($GLOBALS['user_info']['id']); //取当前用户的id
        }
        if ($user_id == 0) {
            $root['error'] = "用户ID为空";
            $root['status'] = 0;
        } else {
            $p = $_REQUEST['p'];
            $type = $_REQUEST['type'];
            if ($p == '') {
                $p = 1;
            }
            $p = $p > 0 ? $p : 1;
            $page_size = 10;
            $limit = (($p - 1) * $page_size) . "," . $page_size;
            $live_list = $this->get_live();
            $root['type'] = !empty($_REQUEST['type']) ? $_REQUEST['type'] : "all";

            if ($type == 'all' || $type == '') {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoContributionRedisService.php');
                $video_con = new VideoContributionRedisService($user_id);
                //总贡献榜排行
                $data = $video_con->get_podcast_contribute($user_id, $p, $page_size);
                foreach ($data['list'] as $k => $v) {
                    if (!$GLOBALS['db']->getOne("select is_effect from " . DB_PREFIX . "user where id=" . $v['user_id'])) {
                        unset($data['list'][$k]);
                        $data['rs_count'] -= 1;
                    }
                }
                $page = new Page($data['rs_count'], $page_size);
                $page_show = $page->show();
                $root['page'] = $page_show;
                $root['list'] = $this->is_live(array_values($data['list']), $live_list);

            } else {
                if ($type == 'day') {
                    $where = " u.is_effect=1 and v.create_d = day(curdate()) ";
                } elseif ($type == 'month') {
                    $where = "  u.is_effect=1 and TO_DAYS(NOW())-TO_DAYS(v.create_date) <=30 ";
                } elseif ($type == 'week') {
                    $where = "  u.is_effect=1 and v.create_w = WEEK(curdate(), 1)";
                }

                $video_prop_table_name = createPropTable();
                $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN  " . $video_prop_table_name . " as v ON u.id=v.from_user_id where v.is_red_envelope=0 and v.to_user_id=" . $user_id . " and " . $where . " GROUP BY from_user_id order BY use_ticket desc ";

                $count = $GLOBALS['db']->getAll($sql, true, true);
                $page = new Page(count($count), $page_size);
                $page_show = $page->show();
                $sql .= " limit " . $limit;
                $root['list'] = $this->is_live($GLOBALS['db']->getAll($sql, true, true), $live_list);
                $root['page'] = $page_show;
            }
            $root['sort_num'] = $p - 1;
            $root['error'] = "";
            $root['status'] = 1;

            $m_config = load_auto_cache('m_config');
            $root['page_title'] = "个人中心-{$m_config['ticket_name']}贡献榜";
        }
        api_ajax_return($root);
    }

    // 绑定手机号
    public function bind_mobile()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        if ($GLOBALS['user_info']['mobile']) {
            $root = array(
                "error" => "已绑定过手机号码",
                "status" => 0
            );
            api_ajax_return($root);
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $mobile = strim($_REQUEST['mobile']);
        $verify_code = strim($_REQUEST['verify_coder']);

        if (!$mobile) {
            $root = array(
                "error" => "请输入手机号",
                "status" => 0
            );
            api_ajax_return($root);
        }

        if (!$verify_code) {
            $root = array(
                "error" => "请输入验证码",
                "status" => 0
            );
            api_ajax_return($root);
        }

        if (!check_mobile(trim($mobile))) {
            $root = array(
                "error" => "手机格式错误",
                "status" => 0
            );
            api_ajax_return($root);
        }

        if ($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "mobile_verify_code WHERE mobile=" . $mobile . " AND verify_code='" . $verify_code . "'") == 0) {
            $root = array(
                "error" => "手机验证码出错",
                "status" => 0
            );
            api_ajax_return($root);
        }

        $user = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where mobile = '" . $mobile . "'");
        if ($user && $user['id'] != $user_id) {
            $root = array(
                "error" => "手机号码已被占用",
                "status" => 0
            );
            api_ajax_return($root);
        }

        $data = array("mobile" => $mobile);
        $info = $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, $mode = 'UPDATE', "id=" . $user_id);
        if ($info) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_redis->update_db($user_id, $data);

            //更新session
            $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $user_id);
            es_session::set("user_info", $user_info);
        }

        $root = array(
            "error" => "绑定成功",
            "status" => 1
        );
        api_ajax_return($root);
    }

    //发送手机验证码
    public function send_mobile_verify()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['user_id']);
        $mobile = $GLOBALS['user_info']['mobile'];
        if (app_conf("SMS_ON") == 0) {
            $root['status'] = 0;
            $root['error'] = "短信未开启";
            ajax_return($root);
        }

        if (empty($mobile)) {
            $mobile = $GLOBALS['db']->getOne('SELECT mobile FROM ' . DB_PREFIX . 'user WHERE id=' . $user_id);
            if (empty($mobile)) {
                $root['status'] = 0;
                $root['error'] = "请先绑定手机号";
                ajax_return($root);
            }
        }

        //添加：手机发送 防护
        $root = check_sms_send($mobile);
        if ($root['status'] == 0) {
            ajax_return($root);
        }

        $result = array("status" => 1, "info" => '');

        if (!check_ipop_limit(get_client_ip(), "mobile_verify", 60, 0)) {
            $root['status'] = 0;
            $root['error'] = "发送速度太快了";
            ajax_return($root);
        }

        if ($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "mobile_verify_code where mobile = '" . $mobile . "' and client_ip='" . get_client_ip() . "' and create_time>=" . (get_gmtime() - 60) . " ORDER BY id DESC") > 0) {
            $root['status'] = 0;
            $root['error'] = "发送速度太快了";
            ajax_return($root);
        }
        $n_time = get_gmtime() - 300;
        //删除超过5分钟的验证码
        $GLOBALS['db']->query("DELETE FROM " . DB_PREFIX . "mobile_verify_code WHERE create_time <=" . $n_time);
        //开始生成手机验证

        $code = rand(1000, 9999);
        $GLOBALS['db']->autoExecute(DB_PREFIX . "mobile_verify_code", array(
            "verify_code" => $code,
            "mobile" => $mobile,
            "create_time" => get_gmtime(),
            "client_ip" => get_client_ip()
        ), "INSERT");

        send_verify_sms($mobile, $code);
        $status = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "deal_msg_list where dest = '" . $mobile . "' and code='" . $code . "'");

        if ($status['is_success']) {
            $root['status'] = 1;
            $root['time'] = 60;
            $root['error'] = $status['title'] . $status['result'];
        } else {
            $root['status'] = 0;
            $root['time'] = 0;
            $root['error'] = "短信验证码发送失败";
        }

        api_ajax_return($root);
    }

    //推广中心概况
    public function promoter_info()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1";
        $promoter = $GLOBALS['db']->getRow($sql, true, true);
        if (!$promoter) {
            $root = array(
                "error" => "帐户无登陆权限，请联系管理员",
                "status" => 0
            );
        }

        if (intval($promoter['pid']) == 0) {
            //运营中心
            $root['p_name'] = '平台';
            $root['is_top'] = 1;

            //待审核的推广商
            $sql = "select count(*) from  " . DB_PREFIX . "bm_promoter where pid=" . $user_id . " and is_effect=1 and status=0";
            $re_promoter_count = $GLOBALS['db']->getOne($sql, true, true);

            //我的推广商数量
            $sql = "select count(*) from  " . DB_PREFIX . "bm_promoter where pid=" . $user_id . " and is_effect=1 and status=1";
            $promoter_count = $GLOBALS['db']->getOne($sql, true, true);

        } else {
            //推广商
            $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . intval($promoter['pid']) . " and is_effect=1";
            $p_promoter = $GLOBALS['db']->getRow($sql, true, true);
            $root['p_name'] = $p_promoter['name'];
            $root['is_top'] = 0;

        }

        $sql = "select * from  " . DB_PREFIX . "user where id=" . $user_id . " and is_effect=1";
        $root['user_info'] = $GLOBALS['db']->getRow($sql, true, true);

        //我的秀豆、秀票数
        //$diamonds = $user_info['diamonds'];
        //$ticket = $user_info['ticket'];

        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
        $start = to_timespan(to_date(NOW_TIME, 'Y-m-d 00:00:00'));
        $end = strtotime('+1 day', $start) - 1;
        $where = [
            'l.bm_pid' => ['p.user_id'],
            'l.create_time' => ['between', [$start, $end]]
        ];
        $model = Model::build('bm_promoter_game_log');
        if ($promoter['pid']) {
            $where['p.user_id'] = $user_id;
            $root['game_sum'] = intval($model->table('bm_promoter_game_log l,bm_promoter p')->sum('gain', $where));
        } else {
            $where['p.pid'] = $user_id;
            $root['game_sum'] = intval($model->table('bm_promoter_game_log l,bm_promoter p')->sum('promoter_gain',
                $where));
        }
        //我的游戏收益(今日新增)user_game_log

        if (intval($promoter['pid']) == 0) {
            //新增会员数量
            $root['new_user_sum'] = intval(count($this->get_userlist($user_id, 1, 1)));

            //礼物收益数量
            $userlist = $this->get_userlist($user_id, 1);
            if (count($userlist) > 0) {
                $id_list = array_map('array_shift', $userlist);
                $where1 = " u.bm_pid in (" . implode(',', $id_list) . ")";
            } else {
                $where1 = " 1=0";
            }

            $video_prop_table_name = createPropTable();

            $sql = "SELECT  sum(l.total_ticket) as total_ticket FROM   "
                . DB_PREFIX . $video_prop_table_name . " as l LEFT JOIN "
                . DB_PREFIX . "user AS u ON l.to_user_id = u.id  WHERE "
                . $where1 . " ";

            $root['gift_sum'] = intval($GLOBALS['db']->getOne($sql, true, true));
        } else {
            //新增会员数量
            // $root['new_user_sum'] = intval(count($this->get_userlist($user_id, 0, 1)));

            $root['new_user_sum'] = intval($model->table('user')->count(['bm_pid' => $user_id, 'create_time' => ['between', [$start, $end]]]));
            //礼物收益数量
            $video_prop_table_name = createPropTable();
            $root['gift_sum'] = intval($model->table("$video_prop_table_name l,user u")->sum('total_ticket', ['l.to_user_id' => ['u.id'], 'u.bm_pid' => $user_id, 'l.create_time' => ['between', [$start, $end]]]));
            // $id_list = array_map('array_shift', $this->get_userlist($user_id));
            // if (count($userlist) > 0) {
            //     $id_list = array_map('array_shift', $userlist);
            //     $where1 = " u.bm_pid in (" . implode(',', $id_list) . ")";
            // } else {
            //     $where1 = " 1=0";
            // }

            // $y = date('Y');
            // $m = date('m');
            // $time = $y . '' . $m;

            // $sql = "SELECT  sum(l.total_ticket) as total_ticket FROM   "
            //     . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN "
            //     . DB_PREFIX . "user AS u ON l.to_user_id = u.id  WHERE "
            //     . $where1 . " ";

            // $root['gift_sum'] = intval($GLOBALS['db']->getOne($sql, true, true));
        }

        $root['start'] = date('Y-m-d', $start);
        $root['end'] = date('Y-m-d', $end);
        $root['promoter'] = $promoter;
        $root['re_promoter_count'] = $re_promoter_count;
        $root['promoter_count'] = $promoter_count;
        $root['page_title'] = '概况';
        api_ajax_return($root);
    }

    //推广商列表
    public function promoter_list()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        //更新推广商子集个数
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/models/bm_promoterModel.class.php');
        $bm_promoter_obj = new bm_promoterModel();
        $bm_promoter_obj->update_promoter_two_child($user_id, 600, $user_id);

        //条件
        $promoter_name = strim($_REQUEST['promoter_name']);
        $mobile = strim($_REQUEST['mobile']);
        $login_state = isset($_REQUEST['login_state']) ? intval($_REQUEST['login_state']) : -1; //暂无
        $s_user_id = strim($_REQUEST['user_id']);

        $where = " p.pid=" . $user_id . " and p.is_effect=1 and p.status=1 ";
        if ($promoter_name != "") {
            $where .= " and p.name = '" . $promoter_name . "' ";
        }
        if ($mobile != "") {
            $where .= " and p.mobile = '" . $mobile . "' ";
        }
        /*if ($login_state>-1) {
        $where.=" and p.is_effect = ".$login_state." ";
        }*/
        if ($s_user_id > 0) {
            $where .= " and p.user_id = " . $s_user_id . " ";
        }

        $sql = "select p.id,p.name,p.mobile,p.user_id,p.child_count,p.is_effect,p.status,p.create_time,u.diamonds,u.coin from  " . DB_PREFIX . "bm_promoter as p
        left join " . DB_PREFIX . "user as u on u.id = p.user_id where " . $where;

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter as p where " . $where);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;

        $root['promoter_list'] = $GLOBALS['db']->getAll($sql, true, true);

        $root['page'] = $page_show;
        $root['page_title'] = '推广商列表';
        $root['promoter_name'] = $promoter_name;
        $root['mobile'] = $mobile;
        $root['s_user_id'] = $s_user_id;
        $root['login_state'] = $login_state;

        api_ajax_return($root);
    }

    //推广商下属会员列表
    public function promoter_userlist()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        //条件
        $nick_name = strim($_REQUEST['nick_name']);
        $promoter_name = strim($_REQUEST['promoter_name']);
        //$login_state=isset($_REQUEST['login_state'])?intval($_REQUEST['login_state']):-1;//暂无
        $bm_qrcode_id = intval($_REQUEST['bm_qrcode_id']);

        $where = " ";
        $where_p = " ";
        $mobile = strim($_REQUEST['mobile']);
        if ($mobile != "") {
            $where .= " and u.mobile = '" . $mobile . "' ";
        }
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }
        if ($bm_qrcode_id > 0) {
            $where .= " and u.bm_qrcode_id = " . $bm_qrcode_id . " ";
        }

        if ($promoter_name != "") {
            $where_p .= " and name = '" . $promoter_name . "' ";
        }
        /*if ($login_state>-1) {
        $where.=" and is_effect = ".$login_state." ";
        }*/

        $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where (pid=" . $user_id . " or user_id=" . $user_id . ") and is_effect=1 and status=1" . $where_p;
        $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);
        if (!$promoter_list) {
            $root['user_list'] = array();
            $root['page'] = '';
            $root['page_title'] = '会员列表';
            api_ajax_return($root);
        }

        $id_list = array_map('array_shift', $promoter_list);
        $where .= " and u.bm_pid  in (" . implode(',', $id_list) . ")";
        $sql = "select u.id,u.nick_name,u.head_image,u.create_time,u.mobile,u.is_effect,u.is_authentication,u.bm_qrcode_id,p.name as promoter_name  from  " . DB_PREFIX . "user as u left join " . DB_PREFIX . "bm_promoter as p on p.user_id=u.bm_pid where  u.is_effect=1 " . $where;

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("select count(*)  from  " . DB_PREFIX . "user as u where  u.is_effect=1 " . $where);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $root['user_list'] = $GLOBALS['db']->getAll($sql, true, true);

        $root['page'] = $page_show;
        $root['page_title'] = '会员列表';
        $root['promoter_name'] = $promoter_name;
        $root['mobile'] = $mobile;
        $root['nick_name'] = $nick_name;
        $root['bm_qrcode_id'] = $bm_qrcode_id;

        api_ajax_return($root);
    }

    // 推广主播列表
    public function promoter_anchorlist()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        //条件
        $nick_name = strim($_REQUEST['nick_name']);
        $promoter_name = strim($_REQUEST['promoter_name']);
        //$login_state=isset($_REQUEST['login_state'])?intval($_REQUEST['login_state']):-1;//暂无
        $bm_special = intval($_REQUEST['bm_special']);

        //已经认证的用户为主播
        $where = " and u.is_authentication>0 and u.bm_special=" . $bm_special . " ";
        $where_p = " ";

        $mobile = strim($_REQUEST['mobile']);
        if ($mobile != "") {
            $where .= " and u.mobile = '" . $mobile . "' ";
        }
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }
        if ($promoter_name != "") {
            $where_p .= " and name = '" . $promoter_name . "' ";
        }
        /*if ($login_state>-1) {
        $where.=" and is_effect = ".$login_state." ";
        }*/

        $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where (pid=" . $user_id . " or user_id=" . $user_id . ") and is_effect=1 and status=1" . $where_p;
        $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);
        if (!$promoter_list) {
            $root['user_list'] = array();
            $root['page'] = '';
            $root['page_title'] = '主播列表';
            $root['promoter_name'] = $promoter_name;
            $root['mobile'] = $mobile;
            $root['nick_name'] = $nick_name;
            $root['bm_special'] = $bm_special;
            api_ajax_return($root);
        }

        $id_list = array_map('array_shift', $promoter_list);
        $where .= " and u.bm_pid  in (" . implode(',', $id_list) . ")";
        $sql = "select u.id,u.nick_name,u.head_image,u.create_time,u.mobile,u.is_effect,u.is_authentication,p.name as promoter_name  from  " . DB_PREFIX . "user as u left join " . DB_PREFIX . "bm_promoter as p on p.user_id=u.bm_pid where  u.is_effect=1 " . $where;

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("select count(*)  from  " . DB_PREFIX . "user as u where  u.is_effect=1 " . $where);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $user_list = $GLOBALS['db']->getAll($sql, true, true);
        $root['user_list'] = $user_list;

        $root['page'] = $page_show;
        $root['page_title'] = '主播列表';
        $root['promoter_name'] = $promoter_name;
        $root['nick_name'] = $nick_name;
        $root['bm_special'] = $bm_special;

        $root['promoter_pid'] = $GLOBALS['db']->getOne("select pid from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . "");

        api_ajax_return($root);
    }

    //
    public function promoter_checklist()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未推广商审核列表登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        //条件
        $promoter_name = strim($_REQUEST['promoter_name']);
        $check_state = isset($_REQUEST['check_state']) ? intval($_REQUEST['check_state']) : -1; //暂无

        $where = " pid=" . $user_id . " and status in (0,2) ";
        if ($promoter_name != "") {
            $where .= " and name = '" . $promoter_name . "' ";
        }
        if ($check_state > -1) {
            $where .= " and status = " . $check_state . " ";
        }

        $sql = "select * from  " . DB_PREFIX . "bm_promoter where " . $where;

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("select count(*) from  " . DB_PREFIX . "bm_promoter where " . $where);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $root['re_promoter_list'] = $GLOBALS['db']->getAll($sql, true, true);
        $root['page'] = $page_show;
        $root['page_title'] = '审核推广商列表';
        $root['promoter_name'] = $promoter_name;
        $root['check_state'] = $check_state;
        api_ajax_return($root);
    }

    //推广商下属公会长列表(user表pid)(弃用)
    public function promoter_list_3()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        $sql = "select * from  " . DB_PREFIX . "bm_promoter where pid=" . $user_id . " and is_effect=1 and status=1";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getAll($sql, true, true);
        $page = new Page(count($count), $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $root['promoter_list'] = $GLOBALS['db']->getAll($sql, true, true);
        $root['page'] = $page_show;
        $root['page_title'] = '推广商列表';
        api_ajax_return($root);
    }

    //礼物收益列表
    public function gift_list()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $nick_name = strim($_REQUEST['anchor_name']);
        $bm_special = isset($_REQUEST['bm_special']) ? intval($_REQUEST['bm_special']) : -1;

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }
        //根据用户身份决定数据返回
        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1 and status=1";
        $promoter_info = $GLOBALS['db']->getRow($sql, true, true);
        $user_list = array();
        if ($promoter_info) {
            //代理
            if ($promoter_info['pid'] == 0) {
                $user_list = $this->get_userlist($user_id, 1);

            } else {
                //普通代理
                $user_list = $this->get_userlist($user_id);

            }
        } else {
            //非代理
            $root['error'] = '非代理身份';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        if (!$user_list) {
            $root['page_title'] = '礼物收益列表';
            $root['bm_special'] = $bm_special;
            $root['nick_name'] = $nick_name;
            $root['begin_time'] = strim($_REQUEST['begin_time']);
            $root['end_time'] = strim($_REQUEST['end_time']);
            api_ajax_return($root);
        }

        $id_list = array_map('array_shift', $user_list);
        $where = " l.to_user_id in (" . implode(',', $id_list) . ") ";

        //时间

        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        //会员名
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }

        //主播类型
        if ($bm_special > -1) {
            $where .= " and u.bm_special = '" . $bm_special . "' ";
        }

        $sql = "SELECT l.id,l.to_user_id,sum(l.total_ticket) as total_ticket,u.nick_name,u.bm_special,bp.name as promoter_name FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.to_user_id = u.id" . " LEFT JOIN "
            . DB_PREFIX . "bm_promoter AS bp ON bp.user_id = u.bm_pid" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.to_user_id ";

        $sql_count = "SELECT count(1) FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.to_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.to_user_id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $sql .= "limit " . $limit;

        $gift_list = $GLOBALS['db']->getAll($sql, true, true);

        $count = $GLOBALS['db']->getAll($sql_count, true, true);
        $page = new Page(count($count), $page_size);
        $page_show = $page->show();

        $bm_config = load_auto_cache("bm_config");
        //代理商签约主播收益分成
        $promoter_sign_anchor_revenue = intval($bm_config['promoter_sign_anchor_revenue']);
        if ($promoter_sign_anchor_revenue < -1 || $promoter_sign_anchor_revenue > 100) {
            $promoter_sign_anchor_revenue = 90;
        }
        //代理商普通主播收益分成
        $promoter_average_anchor_revenue = intval($bm_config['promoter_average_anchor_revenue']);
        if ($promoter_average_anchor_revenue < -1 || $promoter_average_anchor_revenue > 100) {
            $promoter_average_anchor_revenue = 70;
        }

        foreach ($gift_list as $k => $v) {
            if ($v['bm_special'] == 1) {
                $gift_list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_sign_anchor_revenue / 100);
            } else {
                $gift_list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_average_anchor_revenue / 100);
            }

            $gift_list[$k]['promoter_ticket'] = $gift_list[$k]['total_ticket'] - $gift_list[$k]['user_ticket'];
        }

        //合计
        $sql_total = "SELECT u.bm_special,sum(l.total_ticket) as total_ticket FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.to_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.to_user_id";
        $gift_list_all = $GLOBALS['db']->getAll($sql_total);
        foreach ($gift_list_all as $k => $v) {
            $total_ticket_all += $v['total_ticket'];
            if ($v['bm_special'] == 1) {
                $user_ticket_all += $v['total_ticket'] * ($bm_config['promoter_sign_anchor_revenue'] / 100);
            } else {
                $user_ticket_all += $v['total_ticket'] * ($bm_config['promoter_average_anchor_revenue'] / 100);
            }
        }
        $promoter_ticket_all = $total_ticket_all - $user_ticket_all;

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '礼物收益列表';
        $root['bm_special'] = $bm_special;
        $root['nick_name'] = $nick_name;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['total_ticket_all'] = $total_ticket_all;
        $root['user_ticket_all'] = $user_ticket_all;
        $root['promoter_ticket_all'] = $promoter_ticket_all;

        api_ajax_return($root);
    }

    //礼物收益详情列表
    public function gift_detail()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $prop_id = strim($_REQUEST['prop_id']);

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }

        $where = "l.to_user_id =" . $user_id . " ";

        //礼物名称
        if ($prop_id > 0) {
            $where .= " and l.prop_id = " . $prop_id . "";
        }

        //时间
        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        $sql = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,u.bm_special FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id WHERE " . $where . "  and" . $wheretime . " ";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  " . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l WHERE " . $where . "  and" . $wheretime . " ");
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " ORDER BY l.create_time DESC limit " . $limit;
        $gift_list = $GLOBALS['db']->getAll($sql, true, true);
        $bm_config = load_auto_cache("bm_config");
        //代理商签约主播收益分成
        $promoter_sign_anchor_revenue = intval($bm_config['promoter_sign_anchor_revenue']);
        if ($promoter_sign_anchor_revenue < -1 || $promoter_sign_anchor_revenue > 100) {
            $promoter_sign_anchor_revenue = 90;
        }
        //代理商普通主播收益分成
        $promoter_average_anchor_revenue = intval($bm_config['promoter_average_anchor_revenue']);
        if ($promoter_average_anchor_revenue < -1 || $promoter_average_anchor_revenue > 100) {
            $promoter_average_anchor_revenue = 70;
        }

        if ($gift_list[0]['bm_special'] == 1) {
            $revenue_percent = $promoter_sign_anchor_revenue;
        } else {
            $revenue_percent = $promoter_average_anchor_revenue;
        }

        foreach ($gift_list as $k => $v) {
            $gift_list[$k]['user_ticket'] = $v['total_ticket'] * ($revenue_percent / 100);
            $gift_list[$k]['promoter_ticket'] = $gift_list[$k]['total_ticket'] - $gift_list[$k]['user_ticket'];
            $gift_list[$k]['ticket'] = $v['total_ticket'] / $v['num'];
        }

        //合计
        $total_ticket_all = $GLOBALS['db']->getOne("SELECT sum(total_ticket) as total_ticket FROM  " . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l WHERE " . $where . "");
        $user_ticket_all = $total_ticket_all * ($revenue_percent / 100);
        $promoter_ticket_all = $total_ticket_all - $user_ticket_all;

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '礼物收益详情列表';
        $root['prop_id'] = $prop_id;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');

        $root['total_ticket_all'] = $total_ticket_all;
        $root['user_ticket_all'] = $user_ticket_all;
        $root['promoter_ticket_all'] = $promoter_ticket_all;

        $root['prop_id'] = $prop_id;
        $root['prop_list'] = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "prop where is_effect=1");

        $root['nick_name'] = strim($_REQUEST['nick_name']);
        api_ajax_return($root);

    }

    //游戏收益列表
    public function coins_list()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $nick_name = strim(addslashes($_REQUEST['nick_name']));
        $promoter_name = strim(addslashes($_REQUEST['promoter_name']));

        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }

        //根据用户身份决定数据返回
        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1 and status=1";
        $promoter_info = $GLOBALS['db']->getRow($sql, true, true);

        $user_list = array();

        if ($promoter_info) {
            //代理
            if ($promoter_info['pid'] == 0) {
                $user_list = $this->get_userlist($user_id, 1);
                $where = "bp.pid = '{$user_id}' and bp.is_effect=1 and bp.status=1";
            } else {
                //普通代理
                $user_list = $this->get_userlist($user_id);
                $where = "bp.user_id = '{$user_id}' and bp.is_effect=1 and bp.status=1";

            }
        } else {
            //非代理
            $root['error'] = '非代理身份';
            $root['status'] = 0;
            api_ajax_return($root);

        }

        if (!$user_list) {
            $root['gift_list'] = array();
            $root['page_title'] = '游戏收益列表';

            $root['nick_name'] = $nick_name;
            $root['promoter_name'] = $promoter_name;
            api_ajax_return($root);
        }

        //会员名
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }

        //上级推广商名称
        if ($nick_name != "") {
            $where .= " and bp.name = '" . $promoter_name . "' ";
        }
        $pre = DB_PREFIX;

        $sql = "SELECT
                    count(1) as count
                FROM
                    (
                    SELECT
                        1
                    FROM
                        {$pre}bm_promoter_game_log AS l
                    LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                    LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                    WHERE
                        {$where} AND {$wheretime}
                    GROUP BY
                        l.user_id
                    ) AS a";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne($sql, true, true);
        $page = new Page($count, $page_size);
        $page_show = $page->show();

        $sql = "SELECT
                    l.bm_pid,
                    l.user_id,
                    l.game_id,
                    sum(l.sum_bet) AS sum_bet,
                    sum(l.user_gian) AS sum_win1,
                    sum(ABS(l.sum_win)) AS sum_win,
                    sum(l.promoter_gain) AS promoter_gain,
                    sum(l.platform_gain) AS platform_gain,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.gain) AS gain,
                    l.create_time,
                    l.is_count,
                    bp.`name`,
                    bp.id,
                    u.nick_name
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where} AND {$wheretime}
                GROUP BY
                    l.user_id
                LIMIt {$limit}";

        $root['gift_list'] = $GLOBALS['db']->getAll($sql, true, true);
        $root['page'] = $page_show;
        $root['page_title'] = '游戏收益列表';

        $sql = "SELECT
                    sum(l.sum_bet) AS sum_bet,
                    sum(l.user_gain) AS sum_win1,
                    sum(ABS(l.sum_win)) AS sum_win,
                    sum(l.promoter_gain) AS promoter_gain,
                    sum(l.platform_gain) AS platform_gain,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.gain) AS gain
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where} AND {$wheretime}";
        $root['sum'] = $GLOBALS['db']->getRow($sql, true, true);
        $root['nick_name'] = $nick_name;
        $root['promoter_name'] = $promoter_name;

        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);
    }

    // 游戏收益明细
    public function coins_detail()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
        //查看的用户数据
        $user_id = intval($_REQUEST['id']);
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }

        //时间
        $nick_name = strim(addslashes($_REQUEST['nick_name']));
        $root['is_win'] = $is_win = intval($_REQUEST['is_win']);
        $root['game_id'] = $game_id = intval($_REQUEST['game_id']);

        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        $where = "u.id  ={$user_id} and bp.is_effect=1 and bp.status=1";
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }
        if ($is_win == 1) {
            $where .= " and l.sum_win > 0 ";
        }
        if ($is_win == -1) {
            $where .= " and l.sum_win <= 0 ";
        }
        if ($game_id) {
            $where .= " and l.game_id = {$game_id} ";
        }

        $pre = DB_PREFIX;
        $sql = "SELECT
                    count(1) as count
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where} AND {$wheretime}";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne($sql, true, true);
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql = "SELECT
                    l.bm_pid,
                    l.user_id,
                    l.sum_bet,
                    l.sum_gain,
                    l.sum_win,
                    l.promoter_gain,
                    l.platform_gain,
                    l.user_gain,
                    l.gain,
                    l.game_id,
                    l.create_time,
                    l.is_count,
                    bp.`name`,
                    bp.id,
                    u.nick_name
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where} AND {$wheretime}
                ORDER BY
                    l.create_time DESC
                LIMIT $limit";
        $root['gift_list'] = $GLOBALS['db']->getAll($sql, true, true);

        $game = Model::build('games')->select(['is_effect' => 1]);
        $game_array = [];
        foreach ($game as $value) {
            $game_array[$value['id']] = $value['name'];
        }
        $root['game_array'] = $game_array;
        foreach ($root['gift_list'] as $key => $value) {
            $root['gift_list'][$key]['game_id'] = $game_array[$value['game_id']];
        }
        $sql = "SELECT
                    sum(l.sum_bet) as sum_bet,
                    sum(l.sum_gain) as sum_gain,
                    sum(abs(l.sum_win)) as sum_win,
                    sum(l.promoter_gain) as promoter_gain,
                    sum(l.platform_gain) as platform_gain,
                    sum(l.user_gain) as user_gain,
                    sum(l.gain) as gain
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where} AND {$wheretime}";
        $root['sum'] = $GLOBALS['db']->getRow($sql, true, true);
        $root['page'] = $page_show;
        $user = Model::build('user')->selectOne(['id' => $user_id]);
        $p_user = Model::build('bm_promoter')->selectOne(['user_id' => $user['bm_pid']]);
        $root['user'] = $user;
        $root['p_user'] = $p_user;
        $root['page_title'] = '游戏收益详情列表';

        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);
    }

    //秀豆送礼物列表
    public function gift_from_list()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $nick_name = strim($_REQUEST['anchor_name']);
        $bm_special = isset($_REQUEST['bm_special']) ? intval($_REQUEST['bm_special']) : -1;

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }
        //根据用户身份决定数据返回
        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1 and status=1";
        $promoter_info = $GLOBALS['db']->getRow($sql, true, true);
        $user_list = array();
        if ($promoter_info) {
            //代理
            if ($promoter_info['pid'] == 0) {
                $user_list = $this->get_userlist($user_id, 1);

            } else {
                //普通代理
                $user_list = $this->get_userlist($user_id);

            }
        } else {
            //非代理
            $root['error'] = '非代理身份';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        if (!$user_list) {
            $root['page_title'] = '秀豆送礼物列表';
            $root['bm_special'] = $bm_special;
            $root['nick_name'] = $nick_name;
            $root['begin_time'] = strim($_REQUEST['begin_time']);
            $root['end_time'] = strim($_REQUEST['end_time']);
            api_ajax_return($root);
        }

        $id_list = array_map('array_shift', $user_list);
        $where = " l.from_user_id in (" . implode(',', $id_list) . ")  and l.is_coin=0 ";

        //时间

        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        //会员名
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }

        //主播类型
        if ($bm_special > -1) {
            $where .= " and u.bm_special = '" . $bm_special . "' ";
        }

        $sql = "SELECT l.id,l.to_user_id,l.from_user_id,sum(l.total_ticket) as total_ticket,sum(l.total_diamonds) as total_diamonds,u.nick_name,u.bm_special,bp.name as promoter_name FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " LEFT JOIN "
            . DB_PREFIX . "bm_promoter AS bp ON bp.user_id = u.bm_pid" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id ";

        $sql_count = "SELECT count(1) FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $sql .= "limit " . $limit;

        $gift_list = $GLOBALS['db']->getAll($sql, true, true);

        $count = $GLOBALS['db']->getAll($sql_count, true, true);
        $page = new Page(count($count), $page_size);
        $page_show = $page->show();

        //合计
        $sql_total = "SELECT u.bm_special,sum(l.total_ticket) as total_ticket,sum(l.total_diamonds) as total_diamonds FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id";
        $gift_list_all = $GLOBALS['db']->getAll($sql_total);
        foreach ($gift_list_all as $k => $v) {
            $total_ticket_all += $v['total_ticket'];
            $total_diamonds_all += $v['total_diamonds'];

        }

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '秀豆送礼物列表';
        $root['bm_special'] = $bm_special;
        $root['nick_name'] = $nick_name;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['total_ticket_all'] = $total_ticket_all;
        $root['total_diamonds_all'] = $total_diamonds_all;

        api_ajax_return($root);
    }

    //秀豆送礼物详情列表
    public function gift_from_detail()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $prop_id = strim($_REQUEST['prop_id']);

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }

        $where = "l.from_user_id =" . $user_id . "  and l.is_coin=0 ";

        //礼物名称
        if ($prop_id > 0) {
            $where .= " and l.prop_id = " . $prop_id . "";
        }

        //时间
        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        $sql = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,u.bm_special,l.total_diamonds FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.from_user_id = u.id WHERE " . $where . "  and" . $wheretime . " ";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  " . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l WHERE " . $where . "  and" . $wheretime . " ");
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $gift_list = $GLOBALS['db']->getAll($sql, true, true);

        //合计
        foreach ($gift_list as $k => $v) {
            $total_ticket_all += $v['total_ticket'];
            $total_diamonds_all += $v['total_diamonds'];
        }

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '秀豆送礼物详情列表';
        $root['prop_id'] = $prop_id;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');

        $root['total_ticket_all'] = $total_ticket_all;
        $root['total_diamonds_all'] = $total_diamonds_all;

        $root['prop_id'] = $prop_id;
        $root['prop_list'] = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "prop where is_effect=1");

        $root['nick_name'] = strim($_REQUEST['nick_name']);
        api_ajax_return($root);

    }

    //游戏币送礼物列表
    public function gift_from_coin_list()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $nick_name = strim($_REQUEST['anchor_name']);
        $bm_special = isset($_REQUEST['bm_special']) ? intval($_REQUEST['bm_special']) : -1;

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }
        //根据用户身份决定数据返回
        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1 and status=1";
        $promoter_info = $GLOBALS['db']->getRow($sql, true, true);
        $user_list = array();
        if ($promoter_info) {
            //代理
            if ($promoter_info['pid'] == 0) {
                $user_list = $this->get_userlist($user_id, 1);

            } else {
                //普通代理
                $user_list = $this->get_userlist($user_id);

            }
        } else {
            //非代理
            $root['error'] = '非代理身份';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        if (!$user_list) {
            $root['page_title'] = '游戏币送礼物列表';
            $root['bm_special'] = $bm_special;
            $root['nick_name'] = $nick_name;
            $root['begin_time'] = strim($_REQUEST['begin_time']);
            $root['end_time'] = strim($_REQUEST['end_time']);
            api_ajax_return($root);
        }

        $id_list = array_map('array_shift', $user_list);
        $where = " l.from_user_id in (" . implode(',', $id_list) . ")  and l.is_coin=1 ";

        //时间

        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        //会员名
        if ($nick_name != "") {
            $where .= " and u.nick_name = '" . $nick_name . "' ";
        }

        //主播类型
        if ($bm_special > -1) {
            $where .= " and u.bm_special = '" . $bm_special . "' ";
        }

        $sql = "SELECT l.id,l.to_user_id,l.from_user_id,sum(l.total_ticket) as total_ticket,sum(l.total_diamonds) as total_diamonds,u.nick_name,u.bm_special,bp.name as promoter_name FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " LEFT JOIN "
            . DB_PREFIX . "bm_promoter AS bp ON bp.user_id = u.bm_pid" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id ";

        $sql_count = "SELECT count(1) FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $sql .= "limit " . $limit;

        $gift_list = $GLOBALS['db']->getAll($sql, true, true);

        $count = $GLOBALS['db']->getAll($sql_count, true, true);
        $page = new Page(count($count), $page_size);
        $page_show = $page->show();

        //合计
        $sql_total = "SELECT u.bm_special,sum(l.total_ticket) as total_ticket,sum(l.total_diamonds) as total_diamonds FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u ON l.from_user_id = u.id" . " WHERE "
            . $where . " AND {$wheretime} GROUP BY l.from_user_id";
        $gift_list_all = $GLOBALS['db']->getAll($sql_total);
        foreach ($gift_list_all as $k => $v) {
            $total_ticket_all += $v['total_ticket'];
            $total_diamonds_all += $v['total_diamonds'];

        }

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '游戏币送礼物列表';
        $root['bm_special'] = $bm_special;
        $root['nick_name'] = $nick_name;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['total_ticket_all'] = $total_ticket_all;
        $root['total_diamonds_all'] = $total_diamonds_all;

        api_ajax_return($root);
    }

    //游戏币送礼物详情列表
    public function gift_from_coin_detail()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($_REQUEST['id']);
        $prop_id = strim($_REQUEST['prop_id']);

        //查看的用户数据
        if ($user_id == 0) {
            $user_id = intval($GLOBALS['user_info']['id']);
        }

        $where = "l.from_user_id =" . $user_id . "  and l.is_coin=1 ";

        //礼物名称
        if ($prop_id > 0) {
            $where .= " and l.prop_id = " . $prop_id . "";
        }

        //时间
        $time = $this->check_date();
        $wheretime = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";

        $sql = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,u.bm_special,l.total_diamonds FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.from_user_id = u.id WHERE " . $where . "  and" . $wheretime . " ";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  " . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l WHERE " . $where . "  and" . $wheretime . " ");
        $page = new Page($count, $page_size);
        $page_show = $page->show();
        $sql .= " limit " . $limit;
        $gift_list = $GLOBALS['db']->getAll($sql, true, true);

        //合计
        foreach ($gift_list as $k => $v) {
            $total_ticket_all += $v['total_ticket'];
            $total_diamonds_all += $v['total_diamonds'];
        }

        $root['gift_list'] = $gift_list;
        $root['page'] = $page_show;
        $root['page_title'] = '游戏币送礼物详情列表';
        $root['prop_id'] = $prop_id;
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');

        $root['total_ticket_all'] = $total_ticket_all;
        $root['total_diamonds_all'] = $total_diamonds_all;

        $root['prop_id'] = $prop_id;
        $root['prop_list'] = $GLOBALS['db']->getAll("select id,name from " . DB_PREFIX . "prop where is_effect=1");

        $root['nick_name'] = strim($_REQUEST['nick_name']);
        api_ajax_return($root);

    }

    // 创建推广商
    public function create_promoter()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1";
        $promoter = $GLOBALS['db']->getRow($sql, true, true);
        if (!$promoter || $promoter['pid'] != 0) {
            $root = array(
                "error" => "帐户无登陆权限，请联系管理员",
                "status" => 0
            );
            api_ajax_return($root);
        }
        $id = intval($_REQUEST['id']);
        if ($id) {
            $sql = "select p.id,p.name as promoter_name,p.mobile,p.user_id,u.mobile as binding_mobile from  " . DB_PREFIX . "bm_promoter as p left join  " . DB_PREFIX . "user as u on u.id = p.user_id where p.id=" . $id . " and p.status =2";
            $edit_promoter = $GLOBALS['db']->getRow($sql, true, true);
            if (!$edit_promoter) {
                $root = array(
                    "error" => "无效的推广信息",
                    "status" => 0
                );
                api_ajax_return($root);
            }
            $root['edit_promoter'] = $edit_promoter;
        }
        $root['id'] = $id;
        $root['status'] = 1;

        api_ajax_return($root);
    }

    // 提交创建推广商
    public function update_promoter()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => "", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);
        $id = intval($_REQUEST['id']);
        $mobile = strim($_REQUEST['mobile']);
        $binding_mobile = strim($_REQUEST['binding_mobile']);
        $promoter_name = strim($_REQUEST['promoter_name']);

        if (!check_mobile($mobie)) {
            $root['error'] = '登陆手机格式错误';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        if (!check_mobile($binding_mobile)) {
            $root['error'] = '绑定手机格式错误';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        //是否有会员
        $user_info = $GLOBALS['db']->getRow("select id,pid,is_effect,nick_name from " . DB_PREFIX . "user where mobile= " . $binding_mobile . " ");
        if (!$user_info) {
            $root["status"] = 0;
            $root["error"] = "会员未注册，请注册后再绑定";
            api_ajax_return($root);
        }

        if ($user_id == $user_info['id']) {
            $root["status"] = 0;
            $root["error"] = "不允许绑定自己为推广商";
            api_ajax_return($root);
        }

        //会员是否有效
        if ($user_info['is_effect'] == 0) {
            $root["status"] = 0;
            $root["error"] = "无效的会员";
            api_ajax_return($root);
        }

        //是否是三级推广会员
        /*if($user_info['pid'] >0){
        $root["status"]=0;
        $root["error"]="该会员已是推广会员，不能绑定";
        api_ajax_return($root);
        }*/

        //是否已是绑定推广商
        $count_promoter = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where user_id=" . intval($user_info['id']) . " and status=1");
        if ($count_promoter > 0) {
            $root["status"] = 0;
            $root["error"] = "该会员已绑定推广商";
            api_ajax_return($root);
        }

        if ($promoter_name == "") {
            $root["status"] = 0;
            $root["error"] = "请填写推广商名称";
            api_ajax_return($root);
        }

        if ($id > 0) {
            if (intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where mobile='" . $mobile . "' and id <> " . $id . "")) > 0) {
                $root['error'] = '登录手机号已存在';
                $root['status'] = 0;
                api_ajax_return($root);
            }

            if (intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where name='" . $promoter_name . "' and id <> " . $id . "")) > 0) {
                $root["status"] = 0;
                $root["error"] = "推广商名称已存在";
                api_ajax_return($root);
            }
        } else {
            if (intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where mobile='" . $mobile . "'")) > 0) {
                $root['error'] = '登录手机号已存在';
                $root['status'] = 0;
                api_ajax_return($root);
            }

            if (intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where name='" . $promoter_name . "'")) > 0) {
                $this->error("");
                $root["status"] = 0;
                $root["error"] = "推广商名称已存在";
                api_ajax_return($root);
            }
        }

        if ($id > 0) {
            $re_promoter = array();
            $pwd = strim($_REQUEST['password']);
            if ($pwd != '') {
                $re_promoter['pwd'] = md5($pwd);
            }
            $re_promoter['name'] = $promoter_name;
            $re_promoter['mobile'] = $mobile;
            $re_promoter['user_id'] = intval($user_info['id']);
            $re_promoter['pid'] = $user_id;
            $re_promoter['is_effect'] = 0;
            $re_promoter['status'] = 0;

            if ($GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $re_promoter, "UPDATE",
                " id=" . $id . " and status=2 ")
            ) {
                $root["status"] = 1;
                $root["error"] = "提交成功";
            } else {
                $root["status"] = 0;
                $root["error"] = "提交失败，请刷新页面重试";
            }
        } else {
            $re_promoter = array();
            $re_promoter['pwd'] = $re_promoter['pwd'] == '' ? md5('123456') : md5(strim($_REQUEST['password']));
            $re_promoter['name'] = $promoter_name;
            $re_promoter['mobile'] = $mobile;
            $re_promoter['user_id'] = intval($user_info['id']);
            $re_promoter['pid'] = $user_id;
            $re_promoter['is_effect'] = 0;
            $re_promoter['status'] = 0;
            $re_promoter['create_time'] = NOW_TIME;

            if ($GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $re_promoter, "INSERT")) {
                $root["status"] = 1;
                $root["error"] = "新增成功";
            } else {
                $root["status"] = 0;
                $root["error"] = "提交失败，请刷新页面重试";
            }
        }

        api_ajax_return($root);
    }

    // 创建主播
    public function create_anchor()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $root = array("error" => ".", "status" => 1);
        $root['authent_list'] = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "authent_list ORDER BY `sort`");
        $root['left_sign_anchor'] = $this->get_left_sign_anchor($GLOBALS['user_info']['id']);

        api_ajax_return($root);
    }

    // 编辑主播
    public function edit_anchor()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $user = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id=" . intval($_REQUEST['user_id']) . " and status=1");
        $root['user'] = $user;
        $root['page_title'] = '编辑主播';
        api_ajax_return($root);
    }

    // 提交创建主播
    public function update_anchor()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        $login_user_id = intval($GLOBALS['user_info']['id']);

        //只有推广商
        $promoter = $GLOBALS['db']->getRow("select id,pid,user_id from " . DB_PREFIX . "bm_promoter where user_id= " . $login_user_id . " and is_effect=1 and status=1",
            true, true);
        if (!$promoter) {
            $root = array(
                "error" => "无效的推广商",
                "status" => 0
            );
            api_ajax_return($root);
        }
        if ($promoter['pid'] <= 0) {
            $root = array(
                "error" => "您不是推广商不能增加",
                "status" => 0
            );
            api_ajax_return($root);
        }

        $request_user_id = intval($_REQUEST['user_id']);
        if ($request_user_id > 0) {
            /*
            //是老用户，修改绑定关系
            $user['bm_pid'] = $user_id;
            $user['id'] = intval($_REQUEST['user_id']);

            $res = $this->save_user($_REQUEST,'UPDATE',$update_status=1);
            //写入失败
            if ($res['status']==0) {
            $root['status']=$res['status'];
            $root['error']=$res['info'];
            }else{
            $root['status']=1;
            $root['error']="更新成功";
            }

             */

            //编辑
            $user['id'] = $request_user_id;
            $user['bm_special'] = intval($_REQUEST['bm_special']);
            $res = $this->save_user($_REQUEST, 'UPDATE', $update_status = 1);
            //写入失败
            if ($res['status'] == 0) {
                $root['status'] = $res['status'];
                $root['error'] = $res['info'];
            } else {

                $root['status'] = 1;
                $root['error'] = "更新成功";
            }

        } else {
            //是新用户的情况下

            $user_data['id'] = get_max_user_id();
            $user_data['bm_special'] = intval($_REQUEST['bm_special']);
            $user_data['nick_name'] = strim($_REQUEST['nick_name']);
            $user_data['mobile'] = strim($_REQUEST['mobile']);
            $user_data['province'] = strim($_REQUEST['province']);
            $user_data['city'] = strim($_REQUEST['city']);
            $user_data['sex'] = intval($_REQUEST['sex']);
            $user_data['authentication_type'] = strim($_REQUEST['authentication_type']);
            $user_data['authentication_name'] = strim($_REQUEST['realname']);
            $user_data['identify_number'] = strim($_REQUEST['identify_number']);
            $user_data['identify_positive_image'] = strim($_REQUEST['identify_positive_image']);
            $user_data['identify_nagative_image'] = strim($_REQUEST['identify_nagative_image']);
            $user_data['identify_hold_image'] = strim($_REQUEST['identify_hold_image']);
            $user_data['head_image'] = strim($_REQUEST['head_image']);
            $user_data['contact'] = $user_data['mobile'];
            $user_data['login_type'] = 2;

            if ($user_data['bm_special'] == 1) {
                $left_sign_anchor = $this->get_left_sign_anchor($login_user_id);
                if ($left_sign_anchor == 0) {
                    $root['status'] = 0;
                    $root['error'] = "签约主播个数已达到上限";
                    api_ajax_return($root);
                }
            }

            if ($user_data['mobile'] == '') {
                $root['status'] = 0;
                $root['error'] = "请输入手机号吗";
                api_ajax_return($root);
            }

            if (!check_mobile($user_data['mobile'])) {
                $res['info'] = '手机格式错误:' . $user_data['mobile'];
                $res['status'] = 0;
                api_ajax_return($root);
            }

            if (intval($GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user where mobile='" . $user_data['mobile'] . "'")) > 0) {
                $res['info'] = '手机号已存在';
                $res['status'] = 0;
                api_ajax_return($root);
            }

            //更新用户表
            $user_data['bm_pid'] = $login_user_id;
            $user_data['is_authentication'] = 1;
            $res = $this->save_user($user_data, 'INSERT', $update_status = 1);

            //写入失败
            if ($res['status'] == 0) {
                $root['status'] = $res['status'];
                $root['error'] = $res['info'];
            } else {

                //更新推广商个数
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/models/bm_promoterModel.class.php');
                $bm_promoter_obj = new bm_promoterModel();
                $bm_promoter_obj->update_promoter_child($login_user_id, 0);

                $root['status'] = 1;
                $root['error'] = "创建成功";
            }

        }

        api_ajax_return($root);
    }

    // 创建会长
    public function create_society()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        api_ajax_return($root);
    }

    // 提交创建会长
    public function update_society()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $root = array("error" => ".", "status" => 1);
        api_ajax_return($root);
    }

    // 账户管理
    public function account()
    {
        $root = array("error" => ".", "status" => 1);
        $user_id = self::getUserId();
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
        $start = strtotime(date('Y-m') . "-01 00:00:00");
        $end = strtotime('+1 month', $start) - 1;
        $where = [
            'p.pid' => $user_id,
            'p.user_id' => ['l.user_id'],
            'l.create_time' => ['between', [$start, $end]],
            'l.gain' => ['>', 0]
        ];
        $model = Model::build('bm_promoter_game_log');
        $root['game_gain'] = intval($model->table('bm_promoter_game_log l,bm_promoter p')->sum('gain', $where));
        $where['l.gain'] = ['<', 0];
        $root['game_pay'] = intval($model->table('bm_promoter_game_log l,bm_promoter p')->sum('gain', $where));
        $sql = "select * from  " . DB_PREFIX . "user where id=" . $user_id . " and is_effect=1";
        $root['user_info'] = $GLOBALS['db']->getRow($sql, true, true);
        $root['start'] = date('Y-m-d', $start);
        $root['end'] = date('Y-m-d', $end);
        $root['promoter'] = $promoter;
        api_ajax_return($root);
    }

    // 推广码管理
    public function promot_codelist()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        //条件
        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        $is_effect = isset($_REQUEST['code_state']) ? intval($_REQUEST['code_state']) : -1;
        $is_effect = $_REQUEST['code_state'] === '' ? -1 : $is_effect;

        $name = strim($_REQUEST['code_name']);

        $where = " ";
        if ($is_effect > -1) {
            $where .= " and q.is_effect = " . $is_effect . " ";
        }
        if ($name != '') {
            $where .= " and q.name like '%" . $name . "%' ";
        }

        $sql = "select count(u.bm_qrcode_id) as qrcode_user_num ,q.* from  " . DB_PREFIX . "bm_qrcode as q
        left join " . DB_PREFIX . "user as u on u.bm_qrcode_id = q.id where q.p_user_id=" . $user_id . $where . "GROUP BY q.id";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;

        $count_list = $GLOBALS['db']->getALL($sql, true, true);
        $count = count($count_list);
        $page = new Page($count, $page_size);
        $page_show = $page->show();

        $sql .= " limit " . $limit . "";

        $promot_codelist = $GLOBALS['db']->getAll($sql, true, true);
        foreach ($promot_codelist as $k => $v) {
            $promot_codelist[$k]['img'] = get_spec_image($v['img']);
        }
        $root['promot_codelist'] = $promot_codelist;
        $root['page'] = $page_show;
        $root['page_title'] = '推广码列表';

        $root['name'] = $name;
        $root['is_effect'] = $is_effect;

        api_ajax_return($root);
    }

    // 创建推广码
    public function create_promotcode()
    {
        $root = array("error" => ".", "status" => 1);
        api_ajax_return($root);
    }

    // 提交推广码
    public function update_promotcode()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }

        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);
        $qrcode_info = array();
        $qrcode_info['name'] = strim($_REQUEST['name']);
        $qrcode_info['create_time'] = NOW_TIME;
        $qrcode_info['user_id'] = $user_id;
        $qrcode_info['p_user_id'] = $user_id;
        $qrcode_info['is_effect'] = 1;

        //增加二维码邀请码
        $max_id = $GLOBALS['db']->getOne("select max(id) from " . DB_PREFIX . "bm_qrcode");
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/models/bm_promoterModel.class.php');
        $bm_promoter_obj = new bm_promoterModel();
        $qrcode_info['qrcode_sn'] = $bm_promoter_obj->ToNumberSystem26($max_id + 475255);
        //增加都id
        $qrcode_info['promoter_id'] = intval($GLOBALS['promoter_info']['id']);

        if ($GLOBALS['db']->autoExecute(DB_PREFIX . "bm_qrcode", $qrcode_info, "INSERT")) {

            $qrcode_id = $GLOBALS['db']->insert_id();
            //img
            //$register_url = SITE_DOMAIN.'/index.php?ctl=user&act=init_register&user_id='.$user_id.'&qrcode_id='.$qrcode_id;
            //http://site.88817235.cn/frontEnd/baimei/h5/index.html#/register?xx=123&yy=123
            $url = SITE_DOMAIN . '/frontEnd/baimei/h5/index.html#/register?user_id=' . $user_id . '&qrcode_id=' . $qrcode_id;

            $invite_image_dir = APP_ROOT_PATH . "public/sell_image";
            if (!is_dir($invite_image_dir)) {
                @mkdir($invite_image_dir, 0777);
            }

            $path_dir = "/public/sell_image/sell_qrcode_" . $qrcode_id . ".png";
            $path_logo_dir = "/public/sell_image/sell_qrcode_" . $qrcode_id . ".png";
            $qrcode_dir = APP_ROOT_PATH . $path_dir;
            $qrcode_dir_logo = APP_ROOT_PATH . $path_logo_dir;
            if (!is_file($qrcode_dir) || !is_file($qrcode_dir_logo)) {
                get_qrcode_png($url, $qrcode_dir, $qrcode_dir_logo);
            }
            if ($GLOBALS['distribution_cfg']['OSS_TYPE'] && $GLOBALS['distribution_cfg']['OSS_TYPE'] != 'NONE') {
                //syn_to_remote_image_server(".".$path_dir);
                syn_to_remote_image_server("." . $path_logo_dir);
            }

            $GLOBALS['db']->query("update " . DB_PREFIX . "bm_qrcode set img = '" . "." . $path_logo_dir . "'  where id=" . intval($qrcode_id) . "");

            $root["status"] = 1;
            $root["error"] = "提交成功";
        } else {
            $root["status"] = 0;
            $root["error"] = "提交失败，请刷新页面重试";
        }

        api_ajax_return($root);
    }

    public function init_register()
    {

        $root = array('status' => 0, 'error' => '');
        $share_id = intval($_REQUEST['user_id']);
        $qrcode_id = intval($_REQUEST['qrcode_id']);
        // if (!$qrcode_id) {
        //     $root['error'] = '分享ID错误';
        //     api_ajax_return($root);
        // }
        //$from_user = $GLOBALS['db']->getRow("select id,nick_name,head_image from " . DB_PREFIX . "user  where id = " . $share_id);
        $qrcode_info = $GLOBALS['db']->getRow("select q.id,q.is_effect,u.id as from_user_id,u.nick_name,u.head_image from " . DB_PREFIX . "bm_qrcode as q left join " . DB_PREFIX . "user as u on u.id = q.user_id where q.id = " . $qrcode_id);
        if ($qrcode_info) {
            if ($qrcode_info['is_effect'] != 1) {
                $root['error'] = '二维码已关闭';
                api_ajax_return($root);
            }
            if (!$qrcode_info['from_user_id']) {
                $root['error'] = '分享ID错误,该会员不存在';
                api_ajax_return($root);
            }
        } else {
            $pre = DB_PREFIX;
            $promoter = $GLOBALS['db']->getRow("SELECT id FROM {$pre}bm_promoter WHERE `user_id` = {$share_id} AND `pid`>0 AND `status`=1");
            if ($promoter) {
                $qrcode_info = ['from_user_id' => $share_id];
            } else {
                $qrcode_info = ['from_user_id' => $GLOBALS['db']->getOne("SELECT bm_pid FROM {$pre}user WHERE `id` ='{$share_id}' ")];
            }
            // $root['error'] = '无效的二维码';
            // api_ajax_return($root);
        }
        // $root['from_nick_name'] = $qrcode_info['nick_name'];
        // $root['from_head_image'] = $this->deal_weio_image($qrcode_info['head_image'], 'head_image');
        $root['url'] = SITE_DOMAIN . '/mapi/index.php?ctl=user&act=register&itype=bm_index&user_id=' . $qrcode_info['from_user_id'] . '&qrcode_id=' . $qrcode_id;
        $root['page_title'] = '手机注册';
        $root['app_down_url'] = SITE_DOMAIN . "/appdown.php";
        $root['is_login_user'] = 0;
        $root['nick_name'] = '';
        $root['head_image'] = '';
        $root['status'] = 1;
        if ($GLOBALS['user_info']) {
            $root['is_login_user'] = $GLOBALS['user_info']['id'];
            $row = $GLOBALS['db']->getRow("select nick_name,head_image from " . DB_PREFIX . "user  where id = " . $GLOBALS['user_info']['id']);
            $root['nick_name'] = $row['nick_name'];
            $root['head_image'] = $this->deal_weio_image($row['head_image'], 'head_image');
        }
        $m_config = load_auto_cache("m_config");
        $root['site_name'] = $GLOBALS['db']->getOne("select `value` from " . DB_PREFIX . "conf  where `name` = 'SITE_NAME'");
        $root['logo'] = $m_config['app_logo'];
        api_ajax_return($root);
    }

    public function register()
    {
        $root = array('status' => 1, 'error' => '');
        $root['url'] = get_domain() . "/appdown.php";
        if (!$_REQUEST) {
            app_redirect(get_domain() . "/");
        }
        foreach ($_REQUEST as $k => $v) {
            $_REQUEST[$k] = strim($v);
        }
        $bm_pid = intval($_REQUEST['user_id']);
        $qrcode_id = intval($_REQUEST['qrcode_id']);
        $mobile = $_REQUEST['mobile'];
        if (!$mobile) {
            $root['error'] = "手机号未上传！";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $p_user_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id =" . $bm_pid);
        $mobile = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where mobile =" . $mobile);

        $qrcode_info = $GLOBALS['db']->getRow("select q.id,q.is_effect from " . DB_PREFIX . "bm_qrcode as q where q.id = " . $qrcode_id);

        if (intval($p_user_id) == 0) {
            $root['error'] = "上级用户不存在！";
            $root['status'] = 0;
            api_ajax_return($root);
        }

        if ($mobile != '') {
            $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $mobile);
            es_session::set("user_info", $user_info);
            $root['is_url'] = 1;
            $root['is_lack'] = $user_info['is_lack']; //是否缺少用户信息
            $root['is_agree'] = intval($user_info['is_agree']); //是否同意直播协议 0 表示不同意 1表示同意
            $root['user_id'] = intval($user_info['user']['id']);
            $root['nick_name'] = $user_info['nick_name'];
            $root['family_id'] = intval($user_info['family_id']);
            $root['family_chieftain'] = intval($user_info['family_chieftain']);
            $root['error'] = "登录成功";
            $root['user_info'] = $user_info;
            $root['status'] = 1;
            api_ajax_return($root);

        }
        if ($root['status'] != 0) {
            fanwe_require(APP_ROOT_PATH . "system/libs/user.php");
            $result = do_login_user($_REQUEST['mobile'], $_REQUEST['verify_coder'], $p_user_id);
        }
        if ($result['status']) {
            $root['user_id'] = $result['user']['id'];
            $root['status'] = 1;
            if ($result['user']['head_image'] == '' || $result['user_info']['head_image'] == '') {
                //头像
                $m_config = load_auto_cache("m_config"); //初始化手机端配置
                $system_head_image = $m_config['app_logo'];

                if ($system_head_image == '') {
                    $system_head_image = './public/attachment/test/noavatar_11.JPG';
                    syn_to_remote_image_server($system_head_image);
                }

                $data = array(
                    'head_image' => $system_head_image,
                    'thumb_head_image' => get_spec_image($system_head_image, 40, 40),
                    'bm_pid' => $result['p_user_id'],
                    'bm_qrcode_id' => $qrcode_id
                );

                $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $data, "UPDATE", "id=" . $result['user']['id']);

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($result['user']['id'], $data);

                //更新session
                $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $result['user']['id']);
                es_session::set("user_info", $user_info);
            }
            $root['is_url'] = 1;
            $root['is_lack'] = $result['is_lack']; //是否缺少用户信息
            $root['is_agree'] = intval($result['user']['is_agree']); //是否同意直播协议 0 表示不同意 1表示同意
            $root['user_id'] = intval($result['user']['id']);
            $root['nick_name'] = $result['user']['nick_name'];
            $root['family_id'] = intval($result['user']['family_id']);
            $root['family_chieftain'] = intval($result['user']['family_chieftain']);
            $root['error'] = "注册成功";
            $root['user_info'] = $result['user_info'];

        } else {
            $root['status'] = 0;
            if ($root['error'] == '') {
                $root['error'] = $result['info'];
            }
        }
        api_ajax_return($root);
    }

    //检查绑定会员
    public function check_user()
    {

        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['id']);
        $binding_mobile = strim($_REQUEST['binding_mobile']);
        if (!$binding_mobile) {
            $root['error'] = '请输入绑定会员手机号';
            $root['status'] = 0;
            api_ajax_return($root);
        }
        if (!check_mobile($binding_mobile)) {
            $root['error'] = '绑定手机格式错误';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        //是否有会员
        $user_info = $GLOBALS['db']->getRow("select id,pid,is_effect,nick_name from " . DB_PREFIX . "user where mobile= '" . $binding_mobile . "' ");
        if (!$user_info) {
            $root['status'] = 0;
            $root['error'] = "会员未注册，请注册后再绑定";
            api_ajax_return($root);
        }

        if ($user_id == $user_info['id']) {
            $root['status'] = 0;
            $root['error'] = "不允许绑定自己为推广商";
            api_ajax_return($root);
        }

        //会员是否有效
        if ($user_info['is_effect'] == 0) {
            $root['status'] = 0;
            $root['error'] = "无效的会员";
            api_ajax_return($root);
        }

        //是否是三级推广会员
        /*if($user_info['pid'] >0){
        $root["status"]=0;
        $root["error"]="该会员已是推广会员，不能绑定";
        api_ajax_return($root);
        }*/

        //是否已是绑定推广商
        $count_promoter = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "bm_promoter where user_id=" . intval($user_info['id']) . " and status=1");
        if ($count_promoter > 0) {
            $root['status'] = 0;
            $root['error'] = "该会员已绑定推广商";
            api_ajax_return($root);
        }

        $root['status'] = 1;
        $root['user_id'] = intval($user_info['id']);
        $root['user'] = $user_info;

        api_ajax_return($root);
    }

    public function update_promoter_pwd()
    {
        $root = array('status' => 0, 'error' => '', 'user_login_status' => 1);
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0
            ));
        }

        $user_id = intval($GLOBALS['user_info']['id']);
        $mobile = strim($_REQUEST['mobile']);
        $verify_code = strim($_REQUEST['verify_coder']);
        $new_pwd = strim($_REQUEST['new_pwd']);

        if (!$verify_code) {
            $root['error'] = "请输入验证码";
            api_ajax_return($root);
        }

        if ($mobile != '') {
            if (!check_mobile(trim($mobile))) {
                $root['error'] = '手机格式错误';
                api_ajax_return($root);
            }

            if ($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "mobile_verify_code WHERE mobile=" . $mobile . " AND verify_code='" . $verify_code . "'") == 0) {
                $root['error'] = "手机验证码出错";
                api_ajax_return($root);
            }

        } else {
            $root['error'] = "请输入手机号";
            api_ajax_return($root);
        }

        if (!$new_pwd) {
            $root['error'] = "请输入新密码";
            api_ajax_return($root);
        }

        $promoter = $GLOBALS['db']->getRow("select id,pwd from " . DB_PREFIX . "bm_promoter where  user_id=" . $user_id . " and mobile=" . $mobile . "");
        if (!$promoter) {
            $root['error'] = "无效的用户";
            api_ajax_return($root);
        }
        $new_pwd = md5($new_pwd);
        if ($promoter['pwd'] == $new_pwd) {
            $root['error'] = "新密码与原密码相同";
            api_ajax_return($root);
        }

        $re = $GLOBALS['db']->query("update " . DB_PREFIX . "bm_promoter set pwd= '" . $new_pwd . "' where user_id=" . $user_id . "");
        if ($re) {
            $root['error'] = "更新成功";
            $root['status'] = 1;
        } else {
            $root['error'] = "更新失败";
            $root['status'] = 0;
        }

        api_ajax_return($root);
    }

    //推广商二维开启关闭
    public function qrcode_switch()
    {
        //is_effect
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $id = intval($_REQUEST['id']);
        $qrcode_info = $GLOBALS['db']->getRow("select id,is_effect from " . DB_PREFIX . "bm_qrcode where id= " . $id . "");
        if (!$qrcode_info) {
            $root['error'] = "无效的二维码";
            $root['status'] = 0;
            api_ajax_return($root);
        }

        $new_is_effect = $qrcode_info['is_effect'] == 1 ? 0 : 1;
        $re = $GLOBALS['db']->query("update " . DB_PREFIX . "bm_qrcode set is_effect=" . $new_is_effect . " where id=" . $id . "");
        if ($re == 1) {
            $root['error'] = "操作成功";
            $root['status'] = 1;
            $root['new_is_effect'] = $new_is_effect;
        } else {
            $root['error'] = "操作失败";
            $root['status'] = 0;
        }
        return api_ajax_return($root);
    }

    //检查绑定会员
    public function check_date()
    {
        $root = array();
        $request_begin_time = strim($_REQUEST['begin_time']);
        $request_end_time = strim($_REQUEST['end_time']);

        $begin_time = $request_begin_time != '' ? to_timespan($request_begin_time) : to_timespan(to_date(NOW_TIME, 'Y-m-01 00:00:00'));

        $end_time = $request_end_time != '' ? to_timespan($request_end_time . ' 23:59:59') : $begin_time;

        if ($begin_time > $end_time) {
            $root['error'] = '开始时间不能大于结束时间';
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $begin_time_ym = to_date($begin_time, 'Ym');
        $begin_time_d = to_date($begin_time, 'd');

        $end_time_ym = to_date($end_time, 'Ym');
        $end_time_d = to_date($end_time, 'd');

        if ($begin_time_ym != $end_time_ym) {
            $root['error'] = '时长不能跨越自然月';
            $root['status'] = 0;
            api_ajax_return($root);
        }

        $root['begin_time_ym'] = $begin_time_ym;
        $root['begin_time_d'] = $begin_time_d;
        $root['end_time_ym'] = $end_time_ym;
        $root['end_time_d'] = $end_time_d;

        $root['begin_time'] = $begin_time;
        $root['end_time'] = $end_time;
        $root['request_begin_time'] = $request_begin_time;
        $root['request_end_time'] = $request_end_time;

        return $root;
    }

    //获取主播id
    public function get_userlist($user_id, $is_promoter = 0, $is_today = 0)
    {
        //条件
        $nick_name = strim($_REQUEST['nick_name']);
        $promoter_name = strim($_REQUEST['promoter_name']);

        $where = " ";
        $where_p = " ";
        if ($nick_name != "") {
            $where .= " and nick_name = '" . $nick_name . "' ";
        }
        if ($promoter_name != "") {
            $where_p .= " and name = '" . $promoter_name . "' ";
        }

        if ($is_promoter == 0) {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1 and status=1" . $where_p;
            $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);
        } else {
            $sql_p = "select user_id from  " . DB_PREFIX . "bm_promoter where pid=" . $user_id . " and is_effect=1 and status=1" . $where_p;
            $promoter_list = $GLOBALS['db']->getAll($sql_p, true, true);
        }

        if (!$promoter_list) {
            return $user_list = array();
        }

        $id_list = array_map('array_shift', $promoter_list);
        $where .= " and bm_pid  in (" . implode(',', $id_list) . ")";

        if ($is_today == 1) {
            $start = strtotime(date('Y-m-d') . "00:00:00");
            $end = strtotime('+1 day', $start) - 1;
            $where .= " and create_time  between  $start and  $end ";
        }

        $sql = "select id from  " . DB_PREFIX . "user where  is_effect=1 " . $where;

        $user_list = $GLOBALS['db']->getAll($sql, true, true);

        return $user_list;
    }

    public function save_user($user_data, $mode = 'INSERT', $update_status)
    {
        //验证结束开始插入数据
        $user_data['nick_name'] = htmlspecialchars_decode($user_data['nick_name']);
        if (trim($user_data['nick_name']) != '') {
            $user['nick_name'] = trim($user_data['nick_name']);
            //检查昵称
            if (strlen($user['nick_name']) > 60) {
                $res['info'] = "昵称太长";
                $res['status'] = 0;
                return $res;
            }
        } else {
            $res['info'] = "昵称不能为空";
            $res['status'] = 0;
            return $res;
        }

        $head_image = strim($user_data['head_image']);
        if ($head_image) {
            $user['head_image'] = del_domain_url($head_image);
        } else {
            //            $user['head_image'] = "./public/attachment/201608/29/11/57c3ae5abe47d.JPG";
            $res['info'] = "请上传头像";
            $res['status'] = 0;
            return $res;
        }

        //开始数据验证1
        $res = array('status' => 1, 'info' => '', 'data' => ''); //用于返回的数据

        if ($user_data['mobile'] != '' && !check_mobile(trim($user_data['mobile']))) {
            $res['info'] = '手机格式错误:' . $user_data['mobile'];
            $res['status'] = 0;
            return $res;
        }

        if ($user_data['identify_number'] != '' && !isCreditNo($user_data['identify_number']) && $update_status != 1) {
            $res['info'] = '请填写正确的身份证号码';
            $res['status'] = 0;
            return $res;
        }

        $user['create_time'] = get_gmtime();
        //禁播
        if (isset($user_data['is_ban'])) {
            $user['is_ban'] = intval($user_data['is_ban']);
        }

        if (intval($user_data['is_ban'])) {
            $user['ban_time'] = 0;
        } else {
            if (isset($user_data['ban_time'])) {
                $ban_time = strim($user_data['ban_time']);
                $user['ban_time'] = $ban_time != '' ? to_timespan($ban_time) : 0;
            }

        }
        //机器人
        if (isset($user_data['is_robot'])) {
            $user['is_robot'] = intval($user_data['is_robot']);
        }
        if (isset($user_data['user_level'])) {
            $user['user_level'] = intval($user_data['user_level']);
        }

        if (isset($user_data['is_authentication'])) {
            $user['is_authentication'] = intval($user_data['is_authentication']);
        }

        if (isset($user_data['authentication_type'])) {
            $user['authentication_type'] = strim($user_data['authentication_type']);
        }

        if (isset($user_data['identify_number'])) {
            $user['identify_number'] = strim($user_data['identify_number']);
        }

        if (isset($user_data['authentication_name'])) {
            $user['authentication_name'] = strim($user_data['authentication_name']);
        }

        if (isset($user_data['contact'])) {
            $user['contact'] = strim($user_data['contact']);
        }

        if (isset($user_data['from_platform'])) {
            $user['from_platform'] = strim($user_data['from_platform']);
        }

        if (isset($user_data['wiki'])) {
            $user['wiki'] = strim($user_data['wiki']);
        }

        if (isset($user_data['province'])) {
            $user['province'] = str_replace('省', '', $user_data['province']);
        }

        if (isset($user_data['city'])) {
            $user['city'] = str_replace('市', '', $user_data['city']);
        }

        if (isset($user_data['sex'])) {
            $user['sex'] = intval($user_data['sex']);
        }

        if (isset($user_data['is_edit_sex'])) {
            $user['is_edit_sex'] = intval($user_data['is_edit_sex']);
        }

        if (isset($user_data['intro'])) {
            $user['intro'] = strim($user_data['intro']);
        }

        $thumb_head_image = strim($user_data['thumb_head_image']);
        if ($thumb_head_image) {
            $user['thumb_head_image'] = del_domain_url($thumb_head_image);
        }

        if (isset($user_data['signature'])) {
            $user['signature'] = htmlspecialchars_decode(trim($user_data['signature']));
        }

        if (isset($user_data['job'])) {
            $user['job'] = htmlspecialchars_decode(trim($user_data['job']));
        }

        if ($user_data['birthday'] != '') {
            $user['birthday'] = $user_data['birthday'];
        }
        if (isset($user_data['emotional_state'])) {
            $user['emotional_state'] = strim($user_data['emotional_state']);
        }

        if (isset($user_data['identify_hold_image'])) {
            $user['identify_hold_image'] = strim($user_data['identify_hold_image']);
        }

        if (isset($user_data['identify_positive_image'])) {
            $user['identify_positive_image'] = strim($user_data['identify_positive_image']);
        }

        if (isset($user_data['identify_nagative_image'])) {
            $user['identify_nagative_image'] = strim($user_data['identify_nagative_image']);
        }

        if (isset($user_data['v_explain'])) {
            $user['v_explain'] = strim($user_data['v_explain']);
        }

        if (isset($user_data['user_type'])) {
            $user['user_type'] = intval($user_data['user_type']);
        }

        if (isset($user_data['score'])) {
            $user['score'] = intval($user_data['score']);
        }
        //验证结束开始插入数据（这里没写user模块写不进去）
        //会员状态
        if (intval($user_data['is_effect']) != 0) {
            $user['is_effect'] = $user_data['is_effect'];
        } else {
            $user['is_effect'] = 1;
        }

        if (isset($user_data['mobile']) && strim($user_data['mobile'])) {
            $user['mobile'] = strim($user_data['mobile']);
        }

        if (isset($user_data['v_explain']) && strim($user_data['v_explain'])) {
            $user['v_explain'] = strim($user_data['v_explain']);
        }
        if (isset($user_data['v_icon']) && strim($user_data['v_icon'])) {
            $user['v_icon'] = strim($user_data['v_icon']);
        }

        if (isset($user_data['authent_list_id']) && strim($user_data['authent_list_id'])) {
            $user['authent_list_id'] = strim($user_data['authent_list_id']);
        }

        if (isset($user_data['is_authentication'])) {
            if (intval($user_data['is_authentication']) == 3 || intval($user_data['is_authentication']) == 1 || intval($user_data['is_authentication']) == 0) {
                $user['v_icon'] = '';
                $user['v_explain'] = '';
            }
        }

        if (isset($user_data['is_admin'])) {
            $user['is_admin'] = intval($user_data['is_admin']);
        }

        if (isset($user_data['bm_special'])) {
            $user['bm_special'] = intval($user_data['bm_special']);
        }

        if (isset($user_data['bm_pid'])) {
            $user['bm_pid'] = intval($user_data['bm_pid']);
        }

        if (isset($user_data['login_type'])) {
            $user['login_type'] = intval($user_data['login_type']);
        }

        if ($mode == 'INSERT') {
            $user['code'] = ''; //默认不使用code, 该值用于其他系统导入时的初次认证
        } else {
            $user['code'] = $GLOBALS['db']->getOne("select code from " . DB_PREFIX . "user where id =" . $user_data['id']);
        }
        if ($mode == 'INSERT') {
            $user['id'] = $user_data['id'];
            $where = '';
        } else {
            $where = "id=" . intval($user_data['id']);
        }
        if ($GLOBALS['db']->autoExecute(DB_PREFIX . "user", $user, $mode, $where)) {
            if ($mode == 'INSERT') {
                //添加成功，同步信息
                require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
                $api = createTimAPI();
                $ret = $api->account_import((string) $user['id'], $user['nick_name'], $user['head_image']);
                if ($ret['ErrorCode'] == 0) {
                    $GLOBALS['db']->query("update " . DB_PREFIX . "user set synchronize = 1 where id =" . $user['id']);
                }
                //redis化
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $ridis_data = $user_redis->reg_data($user);
                $user_redis->insert_db($user['id'], $ridis_data);
                //$GLOBALS['msg']->manage_msg('MSG_MEMBER_REMIDE',$user_id,array('type'=>'会员注册','content'=>'您于 '.get_client_ip() ."注册成功!"));
            } else {
                $user_id = $user_data['id'];
                user_deal_to_reids(array($user_id));
            }
        }
        $res['data'] = $user_id;

        return $res;
    }

    public function deal_weio_image($url, $type = '', $is_mode = 0)
    {
        if ($type == '') {
            return get_spec_image($url, 200, 200, 1);
        }
        if ($is_mode == 0) {
            switch ($type) {
                case 'photo_info':
                    return get_spec_image($url, 375, 210, 1);
                    break;
                case 'video':
                    return get_spec_image($url, 250, 140, 1);
                    break;
                case 'video_info':
                    return get_spec_image($url, 375, 210, 1);
                    break;
                case 'head_image':
                    return get_spec_image($url, 300, 300, 1);
                    break;
                default:
                    return get_spec_image($url, 200, 200, 1);
                    break;
            }
        } else {
            switch ($type) {
                case 'photo_info':
                    return get_spec_image($url, 375, 210, 1, 30, 15);
                    break;
                case 'video_info':
                    return get_spec_image($url, 375, 210, 1, 30, 15);
                    break;
                default:
                    return get_spec_image($url, 200, 200, 1, 30, 15);
                    break;
            }
        }

    }

    //获取推广商签约主播剩余个数
    public function get_left_sign_anchor($user_id)
    {
        $bm_config = load_auto_cache("bm_config");
        $sign_limit_num = $bm_config['promoter_sign_anchor_limit_num'];
        $sign_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user where bm_pid=" . intval($user_id) . " and is_effect=1 and is_authentication=2 and bm_special=1");

        $left_sign_anchor = $sign_limit_num - $sign_count;
        $left_sign_anchor = $left_sign_anchor > 0 ? $left_sign_anchor : 0;

        return $left_sign_anchor;
    }
}
