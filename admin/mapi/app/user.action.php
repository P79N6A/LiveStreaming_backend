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

    //个人中心-修改直播封面
    public function update_live_image()
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
        $room_id = strim($_REQUEST['room_id']); //要修改的直播
        $live_image = strim($_REQUEST['live_image']); //要修改的封面地址

        if (empty($room_id) || empty($live_image)) {
            api_ajax_return(array('error' => '填写不能为空', 'status' => 0));
        }

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

        $data = array('live_image' => $live_image);
        $info = $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, $mode = 'UPDATE', "id=" . $room_id);
        if ($info) {
            $video_redis->update_db($room_id, $data);
            api_ajax_return(array('error' => '修改成功', 'status' => 1));
        } else {
            api_ajax_return(array('error' => '修改失败', 'status' => 0));
        }
    }

    //个人中心-消息
    public function message()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $page = intval($_REQUEST['p']); //取第几页数据
            if ($page == 0) {
                $page = 1;
            }
            $create_time = $GLOBALS['user_info']['create_time'];
            //每次10条
            $page_size = 10;
            $limit = (($page - 1) * $page_size) . "," . $page_size;
            $list = $GLOBALS['db']->getAll("select sm.*,u.nick_name as send_user_name,u.head_image as head_image from " . DB_PREFIX . "station_message sm left join " . DB_PREFIX . "user u on u.id=sm.send_user_id  where send_status=2 and send_time > (select create_time from " . DB_PREFIX . "user where id= {$user_id}) and send_type !=2 or ( send_define_data like '%" . $user_id . "%'and send_type !=2 )  order by send_time desc limit " . $limit);

            foreach ($list as $k => $v) {
                $list[$k]['title'] = msubstr($v['content'], 1, 50);
                $list[$k]['send_user_name'] = $v['send_user_name'];
                $list[$k]['head_image'] = get_spec_image($v['head_image']);
                $list[$k]['url'] = url('user#message_info', array('id' => $v['id']));
            }
            $root['list'] = $list;
            $rs_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "station_message sm left join " . DB_PREFIX . "user u on u.id=sm.send_user_id where send_status=2 and send_time > (select create_time from " . DB_PREFIX . "user where id= {$user_id}) and send_type !=2 or ( send_define_data like '%" . $user_id . "%'and send_type !=2 )");
            $page = new Page($rs_count, $page_size); //初始化分页对象
            $root['page'] = $page->show();
            $root['status'] = 1;
            $root['page_title'] = "个人中心-消息";
        }
        api_ajax_return($root);
    }

    // 消息详情
    public function message_info()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $id = intval($_REQUEST['id']);
            $message_info = $GLOBALS['db']->getRow("select sm.*,u.nick_name as send_user_name,u.head_image as head_image from " . DB_PREFIX . "station_message sm left join " . DB_PREFIX . "user u on u.id=sm.send_user_id  where send_status=2   and  sm.id=" . $id);

            $message_info['title'] = msubstr($message_info['content'], 1, 50);
            $message_info['send_user_name'] = $message_info['send_user_name'];
            $message_info['head_image'] = get_spec_image($message_info['head_image']);

            $root['info'] = $message_info;
            $root['status'] = 1;
        }
        api_ajax_return($root);
    }

    //个人中心-黑名单
    public function blacklist()
    {

        $root = array('status' => 1, 'error' => '');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $page = intval($_REQUEST['p']); //取第几页数据
            if ($page == 0) {
                $page = 1;
            }

            //每次14条
            $page_size = 14;
            $limit = (($page - 1) * $page_size) . "," . $page_size;

            $user_id = intval($GLOBALS['user_info']['id']);
            $user = $GLOBALS['db']->getAll("select u.id as user_id,u.nick_name,u.signature,u.sex,u.head_image,u.user_level,u.v_icon,b.id as bid from " . DB_PREFIX . "user as u left join " . DB_PREFIX . "black as b on u.id = b.black_user_id  where b.user_id=" . $user_id . " limit " . $limit);
            foreach ($user as $k => $v) {
                $user[$k]['head_image'] = get_spec_image($v['head_image']);

                if ($v['signature'] == '') {
                    $user[$k]['signature'] = '';
                }
                $user[$k]['black_url'] = url('home', array('podcast_id' => $v['id']));
                $user[$k]['signature'] = htmlspecialchars_decode($user[$k]['signature']);
                $user[$k]['nick_name'] = htmlspecialchars_decode($user[$k]['nick_name']);
            }
            $root['user'] = $user;

            $rs_count = $GLOBALS['db']->getOne("select count(*) from " . DB_PREFIX . "user as u left join " . DB_PREFIX . "black as b on u.id = b.black_user_id  where b.user_id=" . $user_id . " ");

            $page = new Page($rs_count, $page_size); //初始化分页对象
            $root['page'] = $page->show();

        }
        $root['page_title'] = "个人中心-黑名单";
        api_ajax_return($root);
    }

    // 我的家族
    public function family()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆."; // es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $is_apply = intval($_REQUEST['is_apply']) == 0 ? 0 : 1; //家族ID
            $user_id = intval($GLOBALS['user_info']['id']); //创建人id
            $nick_name = strim($_REQUEST['nick_name']); //家族成员昵称
            $user = $GLOBALS['db']->getRow("SELECT family_chieftain FROM " . DB_PREFIX . "user WHERE id=" . intval($GLOBALS['user_info']['id']));
            $family_chieftain = intval($user['family_chieftain']); //jai_chieftain 为1时候，身份家族长
            if ($user_id != '' && $family_chieftain == 1) {
                $family = $GLOBALS['db']->getRow("SELECT f.id as family_id,f.logo as family_logo,f.name as family_name,f.create_time,f.memo,f.status,f.manifesto as family_manifesto,(SELECT COUNT(id) FROM " . DB_PREFIX . "user u WHERE u.family_id=f.id) as user_count,(SELECT nick_name FROM " . DB_PREFIX . "user c WHERE c.id=f.user_id) as nick_name FROM " . DB_PREFIX . "family AS f WHERE  f.user_id=" . $user_id,
                    true, true);
            } else {
                $family = $GLOBALS['db']->getRow("SELECT f.id as family_id,f.logo as family_logo,f.name as family_name,f.create_time,f.memo,f.status,f.manifesto as family_manifesto,(SELECT COUNT(id) FROM " . DB_PREFIX . "user u WHERE u.family_id=f.id) as user_count,(SELECT nick_name FROM " . DB_PREFIX . "user c WHERE c.id=f.user_id) as nick_name FROM " . DB_PREFIX . "family AS f LEFT JOIN fanwe_user as u on u.family_id = f.id WHERE u.id =" . $user_id,
                    true, true);
            }

            if ($family) {
                if ($family['status'] == 0) {
                    $root['error'] = '您的家族正在审核';
                    $root['status'] = 0;
                } elseif ($family['status'] == 2) {
                    $root['error'] = '您的家族审核未通过审核';
                    $root['status'] = 2;
                } elseif ($family['status'] == 1) {
                    $root['status'] = 1;
                    $root['error'] = "";
                }
                $family['family_logo'] = get_spec_image($family['family_logo'], 150, 150);
                $root['family_info'] = $family;
                $family_id = intval($family['family_id']); //家族ID
                if ($family_id > 0 && $family) {
                    $root['status'] = 1;
                    $count = $GLOBALS['db']->getOne("SELECT COUNT(id) as rs_count FROM " . DB_PREFIX . "user WHERE family_id=" . $family_id,
                        true, true);
                    $root['rs_count'] = $count; //家族成员总数
                    //申请人数
                    $apply_count = $GLOBALS['db']->getOne("SELECT COUNT(id) as apply_count FROM " . DB_PREFIX . "family_join WHERE family_id=" . $family_id . " and status=0",
                        true, true);
                    $root['apply_count'] = $apply_count;
                    //分页
                    $page = intval($_REQUEST['p']); //当前页
                    $page_size = 20; //分页数量
                    if ($page == 0) {
                        $page = 1;
                    }
                    $limit = (($page - 1) * $page_size) . "," . $page_size;
                    $where = '';
                    if ($nick_name != '') {
                        if ($is_apply) {
                            $where = " and a.nick_name like '%" . $nick_name . "%'";
                        } else {
                            $where = " and nick_name like '%" . $nick_name . "%'";
                        }

                    }
                    if ($is_apply) {
                        $sql = "SELECT a.id as user_id,a.nick_name,a.sex,a.v_type,a.v_icon,a.head_image,a.signature,user_level FROM " . DB_PREFIX . "user as a," . DB_PREFIX . "family_join as b WHERE a.id=b.user_id and b.status=0 and b.family_id=" . $family_id . $where . " limit " . $limit;
                        $user = $GLOBALS['db']->getAll($sql, true, true);
                        if ($nick_name != '') {
                            $root['apply_count'] = count($user);
                        }
                        $root['is_apply'] = 1; //申请

                    } else {
                        $sql = "SELECT id as user_id,nick_name,sex,v_type,v_icon,head_image,signature,user_level,family_chieftain FROM " . DB_PREFIX . "user WHERE family_id=" . $family_id . $where . " ORDER BY family_chieftain desc limit " . $limit;
                        $user = $GLOBALS['db']->getAll($sql, true, true);
                        if ($nick_name != '') {
                            $root['rs_count'] = count($user);
                        } //家族成员总数
                        $root['is_apply'] = 0; //申请
                    }

                    foreach ($user as $k => $v) {
                        $user[$k]['head_image'] = get_spec_image($v['head_image'], 120, 120);
                    }
                    $root['list'] = $user; //家族成员信息
                    $rs_count = count($user);
                    $page = new Page($rs_count, $page_size); //初始化分页对象
                    $root['page'] = $page->show();
                } else {
                    $root['status'] = 0;
                    $root['error'] = "";
                }
            } else {
                $root['status'] = 0;
                $root['error'] = "没有家族";

                app_redirect(url_app('user#userinfo'));
            }
        }
        $root['page_title'] = "个人中心-我的家族";
        api_ajax_return($root);
    }

    // 排行榜
    public function rank()
    {
        $root = array();

        //魅力
        $root['charm_podcast'] = $this->charm_podcast();
        //财富
        $root['rich_list'] = $this->rich_list();

        $root['family'] = $this->family_rank();
        $root['newstar'] = $this->newstar_rank();
        //热门
        $is_hot_list = load_auto_cache("selectpc_video", array('is_hot' => 1, 'pc' => 1));
        $root['is_hot'] = $is_hot_list;
        $root['is_hot_more_url'] = url("video#video_list", array('is_hot' => 1));
        // 排行榜广告列表
        $place_id = 3;
        $root['ad_list'] = load_auto_cache("ad_list", $place_id);
        $root['page_title'] = '排行榜';
        api_ajax_return($root);
    }

    // 我的私信-好友列表
    public function letter()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_redis = new UserFollwRedisService($user_id);
            $list = $user_redis->get_follonging_user($user_id, 1, 100);
//            $rs_count=count($list);
            //            $page=intval($_REQUEST['p']);
            //            $page_size=35;
            //            $start=($page-1)*$page_size;
            //            $new_list=array_slice($list,$start,$page_size);
            $root['friends'] = array();
            foreach ($list as $v) {
                $root['friends'][] = array(
                    "user_id" => $v['user_id'],
                    "nick_name" => htmlspecialchars_decode($v['nick_name']),
                    "head_image" => $v['head_image'],
                    "unread" => 0
                );
            }
            $sql = "SELECT id as user_id,nick_name FROM " . DB_PREFIX . "user where is_admin=1";
            $system_user = $GLOBALS['db']->getAll($sql, true, true);
            $root['system_uid'] = $system_user;
            $usersig = load_auto_cache("usersig", array("id" => $user_id));
            $root['usersig'] = $usersig['usersig'];
//            $page = new Page($rs_count,$page_size);   //初始化分页对象
            //            $root['page'] = $page->show();
            $m_config = load_auto_cache("m_config");
            $root['tim_sdkappid'] = $m_config['tim_sdkappid'];
            $root['tim_account_type'] = $m_config['tim_account_type'];
            $root['status'] = 1;
            $root['page_title'] = "个人中心-私信";
        }
        api_ajax_return($root);
    }

    // 我的私信-聊天窗口
    public function letter_chat()
    {
        $root = array();
        api_ajax_return($root);
    }

    // 我的直播
    public function live()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        }

        $to_user_id = intval($_REQUEST['to_user_id']); //被查看的用户id
        if ($to_user_id == 0) {
            $to_user_id = intval($GLOBALS['user_info']['id']);
            $user_data = $GLOBALS['user_info'];
        } else {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data = $user_redis->getRow_db($to_user_id,
                array('id', 'nick_name', 'head_image', 'thumb_head_image'));
        }

        $sort = intval($_REQUEST['sort']); //排序类型; 0:最新;1:最热

        $page = intval($_REQUEST['p']); //取第几页数据
        if ($page == 0) {
            $page = 1;
        }
        $page_size = 9;
        $limit = (($page - 1) * $page_size) . "," . $page_size;

        $sort_field = "begin_time desc";
        if ($sort == 1) {
            $sort_field = "max_watch_number desc";
        }

        $sql = "SELECT
    *
FROM
    (SELECT
        id AS room_id,
            live_in,
            title,
            begin_time,
            max_watch_number,
            video_vid,
            head_image,
            thumb_head_image,
            live_image,
            prop_table
    FROM
         " . DB_PREFIX . "video
    WHERE
        user_id = {$to_user_id} AND live_in IN (1 , 3) UNION SELECT
        id AS room_id,
            live_in,
            title,
            begin_time,
            max_watch_number,
            video_vid,
            head_image,
            thumb_head_image,
            live_image,
            prop_table
    FROM
        " . DB_PREFIX . "video_history
    WHERE
       user_id = {$to_user_id}
            AND ((is_delete = 0 AND is_del_vod = 0)
            OR (is_delete = 0 AND is_del_vod = 1
            AND live_in = 2))) AS t
ORDER BY {$sort_field}
LIMIT {$limit}";
        $list = $GLOBALS['db']->getAll($sql, true, true);

        foreach ($list as $k => $v) {
            $list[$k]['nick_name'] = $user_data['nick_name'];
            $list[$k]['head_image'] = get_spec_image($list[$k]['head_image']);
            $list[$k]['video_url'] = get_video_url($v['room_id'], $v['live_in']);
            if ($list[$k]['thumb_head_image'] == '' || $user_data['thumb_head_image'] == '') {
                $list[$k]['thumb_head_image'] = get_spec_image($user_data['head_image']);
            } else {
                $list[$k]['thumb_head_image'] = get_spec_image($list[$k]['thumb_head_image']);
            }
            if (empty($v['live_image'])) {
                $list[$k]['live_image'] = $list[$k]['head_image'];
            } else {
                $list[$k]['live_image'] = get_spec_image($v['live_image'], 320, 180, 1);
            }
            $list[$k]['begin_time_format'] = format_show_date($v['begin_time']);
            if ($v['max_watch_number'] > 10000) {
                $list[$k]['watch_number_format'] = round($v['max_watch_number'] / 10000, 2) . "万";
            } else {
                $list[$k]['watch_number_format'] = $v['max_watch_number'];
            }
            $list[$k]['max_watch_number'] = $v['max_watch_number'];
            if ($v['title'] == '') {
                $list[$k]['title'] = "....";
            }
            $list[$k]['total_ticket'] = $GLOBALS['db']->getOne("SELECT sum(total_ticket) FROM " . $v['prop_table'] . "  WHERE video_id = " . $v['room_id'] . " and to_user_id =" . $to_user_id);
            if (!$list[$k]['total_ticket']) {
                $list[$k]['total_ticket'] = 0;
            }
        }
        $root['list'] = $list;
        $sql = "SELECT
    *
FROM
    (SELECT
        id AS room_id,
            live_in,
            title,
            begin_time,
            max_watch_number,
            video_vid,
            head_image,
            thumb_head_image,
            live_image
    FROM
         " . DB_PREFIX . "video
    WHERE
        user_id = {$to_user_id} AND live_in IN (1 , 3) UNION SELECT
        id AS room_id,
            live_in,
            title,
            begin_time,
            max_watch_number,
            video_vid,
            head_image,
            thumb_head_image,
            live_image
    FROM
         " . DB_PREFIX . "video_history
    WHERE
       user_id = {$to_user_id}
            AND ((is_delete = 0 AND is_del_vod = 0)
            OR (is_delete = 0 AND is_del_vod = 1
            AND live_in = 2))) AS t
ORDER BY {$sort_field}
 ";

        $count = $GLOBALS['db']->getAll($sql, true, true);
        $root['count'] = count($count);
        $page = new Page($root['count'], $page_size); //初始化分页对象
        $root['page'] = $page->show();
        $root['status'] = 1;
        $root['page_title'] = '个人中心-我的直播';

        api_ajax_return($root);
    }

    /**
     * 上传视屏签名接口
     *
     * @return 签名
     */
    public function sign()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            ajax_return($root);
        }

        if (empty($GLOBALS['user_info']['nick_name'])) {
            $root = array('status' => 0, 'error' => '请先前往我的资料填写昵称');
            ajax_return($root);
        }

        $args = $_REQUEST['args'];
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        $result = $video_factory->Sign($args);
        $root = array('status' => 1, 'result' => $result);
        ajax_return($root);
    }

    public function new_sign()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            ajax_return($root);
        }
        if (empty($GLOBALS['user_info']['nick_name'])) {
            $root = array('status' => 0, 'error' => '请先前往我的资料填写昵称');
            ajax_return($root);
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        $result = $video_factory->NewSign();
        $root = array('status' => 1, 'signature' => $result);
        ajax_return($root);
    }
    //上传视频
    public function upload_video()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            ajax_return($root);
        }
        //话题
        $cate = $GLOBALS['db']->getAll("select id,title from " . DB_PREFIX . "video_cate where is_effect =1 and is_delete =0 ",
            true, true);
        $root = array(
            "error" => "",
            "status" => 1,
            'cate' => $cate
        );
        api_ajax_return($root);
    }
    public function upload()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            ajax_return($root);
        }

        $m_config = load_auto_cache("m_config");

        if (empty($GLOBALS['user_info']['nick_name'])) {
            $root = array('status' => 0, 'error' => '请先前往我的资料填写昵称');
            ajax_return($root);
        }

        $room_title = strim($_REQUEST['room_title']); //房间名称
        if ($room_title) {
            if (strlen($room_title) < 15) {
                api_ajax_return(array('error' => '直播间名称长度至少5个汉字或10个字母', 'status' => 0));
            }
            if (strlen($room_title) > 40) {
                api_ajax_return(array('error' => '直播间名称长度不超过20个汉字', 'status' => 0));
            }
        }

        $file_id = strim($_REQUEST['file_id']);
        if (!$file_id) {
            $root = array(
                "error" => "请上传视频",
                "status" => 0
            );
            ajax_return($root);
        }

        $live_image = strim($_REQUEST['live_image']); //封面图片
        $user_id = intval($GLOBALS['user_info']['id']);

        $title = strim(str_replace('#', '', $_REQUEST['title']));
        //检查话题长度
        if (strlen($title) > 60) {
            $return['error'] = "话题太长";
            $return['status'] = 0;
            api_ajax_return($return);
        }
        if (!$title) {
            $title = "我的视频";
        }

        $is_private = false;
        // 上传视频延长心跳时间
        $monitor_time = to_date(NOW_TIME + 3600, 'Y-m-d H:i:s'); //主播心跳监听
        $data = $this->create_video($user_id, $title, $is_private, $monitor_time, $cate_id = '', $province = '', $city = '',
            $share_type = '', $room_title, $live_image);

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
        $video_factory = new VideoFactory();
        $ret = $video_factory->ModifyVodInfo($file_id, $data);
        if (!$ret['status']) {
            ajax_return($ret);
        }
        //付费直播
        $is_live_pay = intval($_REQUEST['is_live_pay']);
        if ($is_live_pay) {
            if ($data['live_pay_type'] == 1 && (defined('LIVE_PAY_SCENE') && LIVE_PAY_SCENE == 0)) {
                $root['error'] = "按场付费未开启";
                $root['status'] = 0;
                api_ajax_return($root);
            }
            $data['is_live_pay'] = $is_live_pay;
            $data['live_pay_type'] = 1;
            if (empty($m_config['pc_live_fee'])) {
                $data['live_fee'] = $m_config['live_pay_scene_min'];
            } else {
                $data['live_fee'] = $m_config['pc_live_fee'];
            }
        }
        // 新上传的视频未生成地址
        $data['room_title'] = $GLOBALS['user_info']['nick_name'] . "直播间";
        $data['is_del_vod'] = 1;
        $data['video_vid'] = $file_id;
        $data['end_time'] = NOW_TIME; //'结束时间'
        $GLOBALS['db']->autoExecute(DB_PREFIX . "video_history", $data, 'INSERT');
        sync_video_to_redis($data['id'], '*', false);

        $root['status'] = 1;
        ajax_return($root);
    }

    // 我的关注
    public function focus()
    {
        $root = array();
        $root['status'] = 1;
        $page_size = 9;
        $page = intval($_REQUEST['p']); //取第几页数据
        if ($page == 0 || $page == '') {
            $page = 1;
        }

        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //id
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_redis = new UserFollwRedisService($user_id);

            $focus_list = $user_redis->get_follonging_user($user_id, $page, $page_size);
            $rs_count = $user_redis->follow_count();
            $page = new Page($rs_count, $page_size); //初始化分页对象
            $root['page'] = $page->show();
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoPrivateRedisService.php');
            $video_private_redis = new VideoPrivateRedisService();
            $private_list = $video_private_redis->get_video_list($user_id);
            $param = array('has_private' => 1);
            $list = load_auto_cache("focus_video", $param);
            foreach ($focus_list as $k => $v) {
                if (!$focus_list[$k]['thumb_head_image']) {
                    $focus_list[$k]['thumb_head_image'] = get_spec_image($focus_list[$k]['head_image'], 40, 40);
                }
                foreach ($list as $kk => $vv) {
                    if (empty($focus_list[$k]['live_image'])) {
                        $focus_list[$k]['live_image'] = $list[$k]['live_image'];
                    }
                    if (empty($focus_list[$k]['live_image'])) {
                        $focus_list[$k]['live_image'] = $focus_list[$k]['head_image'];
                    }
                    if ($vv['user_id'] == $v['user_id']) {
                        $focus_list[$k]['watch_number'] = $vv['watch_number'];
                        $focus_list[$k]['live_in'] = $vv['live_in'];
                        $focus_list[$k]['head_image'] = $vv['head_image'];
                        if (!empty($vv['private_key']) && $video_private_redis->check_user_push($vv['room_id'],
                            $user_id) == false
                        ) {
                            $focus_list[$k]['video_url'] = url('live#show', array('podcast_id' => $v['user_id']));
                            $focus_list[$k]['live_in'] = '';
                            break 1;
                        }
                        if ($vv['live_in'] == 1) {
                            $focus_list[$k]['video_url'] = get_video_url($vv['room_id'], $vv['live_in']);
                            break 1;
                        } else {
                            $focus_list[$k]['video_url'] = get_video_url($vv['room_id'], $vv['live_in']);
                        }

                    }
                }
                if (empty($focus_list[$k]['video_url'])) {
                    $focus_list[$k]['video_url'] = url('live#show', array('podcast_id' => $v['user_id']));
                }
            }
        }

        $root['list'] = $focus_list;
        $root['page_title'] = "个人中心-我的关注";
        api_ajax_return($root);
    }

//    // 秀票贡献榜
    //    public function contribution_list(){
    //      $root = array();
    //      $user_id = intval($_REQUEST['user_id']);//被查看的用户id
    //      if($user_id == 0){
    //          $user_id = intval($GLOBALS['user_info']['id']);//取当前用户的id
    //      }
    //      if($user_id == 0){
    //          $root['error'] = "用户ID为空";
    //          $root['status'] = 0;
    //      }else{
    //          /*$page = intval($_REQUEST['p']);//取第几页数据
    //          $page_size=10;
    //
    //          fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoContributionRedisService.php');
    //          $video_con = new VideoContributionRedisService($user_id);
    //
    //          if ($user_id > 0){
    //              //用户总票数
    //              $root = $video_con->get_podcast_contribute($user_id,$page,$page_size);
    //              $root['total_num'] = $root['user']['ticket'];
    //              $rs_count =count($root['list']);
    //          }
    //          $page = new Page($rs_count,$page_size);   //初始化分页对象
    //          $root['page'] = $page->show();*/
    //          $root['list'] = $this->rich_list();
    //
    //      }
    //        api_ajax_return($root);
    //    }

    //移除黑名单
    public function del_black()
    {
        $root = array('status' => 1, 'error' => '');
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['id']);
        $black_user_id = intval($_REQUEST['black_user_id']);
        if ($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "black WHERE user_id=" . $user_id . ' and black_user_id=' . $black_user_id) == 0) {
            $root['status'] = 0;
            $root['error'] = '黑名单不存在！';
            api_ajax_return($root);
        }
        $GLOBALS['db']->query("delete from " . DB_PREFIX . "black where user_id = " . $user_id . ' and black_user_id=' . $black_user_id);
        if ($GLOBALS['db']->affected_rows() > 0) {
            $root['status'] = 1;
            $root['error'] = '移除成功！';
            api_ajax_return($root);
        } else {
            $root['status'] = 0;
            $root['error'] = '移除失败！';
            api_ajax_return($root);
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
                    $where = "  u.is_effect=1 and v.create_w = week(curdate())";
                }

                $video_prop_table_name = createPropTable();
                $sql = "select u.id as user_id ,u.nick_name,u.v_type,u.v_icon,u.head_image,u.sex,u.user_level,sum(v.total_ticket) as use_ticket ,u.is_authentication from " . DB_PREFIX . "user as u INNER JOIN  " . $video_prop_table_name . " as v ON u.id=v.from_user_id where v.is_red_envelope=0 and v.to_user_id=" . $user_id . " and " . $where . " GROUP BY v.from_user_id order BY use_ticket desc ";
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

    public function is_live($data, $live_list)
    {
        foreach ($data as $k => $v) {
            foreach ($live_list as $kk => $vv) {
                if ($vv['user_id'] == $v['user_id']) {
                    $data[$k]['live_in'] = $vv['live_in'];

                    if ($vv['live_in'] == 3) {
                        $data[$k]['video_url'] = get_video_url($vv['room_id'], $vv['live_in']);
                    } else {
                        $data[$k]['video_url'] = "/" . intval($vv['user_id']);
                    }
                }
            }
            if (empty($data[$k]['video_url'])) {
                $data[$k]['video_url'] = "/" . intval($v['user_id']);
            }
            $data[$k]['user_level_ico'] = get_spec_image("./public/images/rank/rank_" . $v['user_level'] . ".png");
        }
        return $data;
    }

    public function get_live()
    {
        $sql = "SELECT v.id AS room_id, v.sort_num, v.group_id, v.user_id, v.city, v.title, v.cate_id, v.live_in, v.video_type, v.room_type,
                        (v.robot_num + v.virtual_watch_number + v.watch_number) as watch_number, v.head_image,v.thumb_head_image, v.xpoint,v.ypoint,
                        u.v_type, u.v_icon, u.nick_name,u.user_level FROM " . DB_PREFIX . "video v
                    LEFT JOIN " . DB_PREFIX . "user u ON u.id = v.user_id where v.live_in in (1,3) order by v.create_time,v.sort_num desc,v.sort desc";
        $live_list = $GLOBALS['db']->getAll($sql, true, true);

        return $live_list;
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

    //认证初始化
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

            $user_sql = "select id,id as user_id,investor_send_info,authentication_type,authentication_name,identify_number, contact,from_platform,wiki,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication from " . DB_PREFIX . "user where is_effect =1 and id=" . $user_id;

            $user = $GLOBALS['db']->getRow($user_sql, true, true);

            $user['identify_positive_image'] = get_spec_image($user['identify_positive_image']);
            $user['identify_nagative_image'] = get_spec_image($user['identify_nagative_image']);
            $user['identify_hold_image'] = get_spec_image($user['identify_hold_image']);

            $user['identify_number'] = !empty($user['identify_number']) ? $user['identify_number'] : '';

            $authent_list_sql = "select id,`name` from " . DB_PREFIX . "authent_list order by sort desc";
            $authent_list = $GLOBALS['db']->getAll($authent_list_sql, true, true);

            $root['user'] = $user;
            $root['authent_list'] = $authent_list;

            $root['investor_send_info'] = $user['investor_send_info'];
        }

        api_ajax_return($root);
    }

    //提交认证
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
            $authentication_name = strim($_REQUEST['authentication_name']); //真实姓名
            $identify_number = strim($_REQUEST['identify_number']); //身份证号码
            $contact = strim($_REQUEST['contact']); //联系方式
            //$from_platform = '';//来自平台
            $wiki = strim($_REQUEST['wiki']); //百度百科
            $identify_hold_image = strim($_REQUEST['identify_hold_image']); //手持身份证正面
            $identify_positive_image = strim($_REQUEST['identify_positive_image']); //身份证正面
            $identify_nagative_image = strim($_REQUEST['identify_nagative_image']); //身份证反面

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
            if ($identify_number == '') {
                $root['status'] = 0;
                $root['error'] = '请填写身份证号码！';
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

            //判断该实名是否存在
            $user_info = $GLOBALS['db']->getRow("select id from  " . DB_PREFIX . "user where id=" . $GLOBALS['user_info']['id']);
            if ($user_info) {
                $user_info['is_authentication'] = 1; //认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
                $user_info['user_type'] = 0; //用户类型
                $user_info['authentication_type'] = $authentication_type; //认证类型
                $user_info['authentication_name'] = $authentication_name; //真实姓名
                $user_info['identify_number'] = $identify_number; //身份证号码
                $user_info['contact'] = $contact; //联系方式
                //$user_info['from_platform'] = $from_platform;//来自平台
                $user_info['wiki'] = $wiki; //百度百科
                $user_info['identify_hold_image'] = $identify_hold_image; //手持身份证正面
                $user_info['identify_positive_image'] = $identify_positive_image; //身份证正面
                $user_info['identify_nagative_image'] = $identify_nagative_image; //身份证反面

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

    public function family_ceil($type)
    {
        $where = " 1=1 ";
        if ($type == 'day') {
            $where = " create_d = day(@dt) ";
        }
        if ($type == 'weeks') {
            $where = " create_w = week(@dt) ";
        }
        if ($type == 'month') {
            $where = " create_m = month(@dt) ";
        }

        $sql = "SELECT j.id as family_id,j.logo as family_logo,j.name as family_name,j.user_id,j.create_time,(SELECT icon FROM " . DB_PREFIX . "family_level fl where fl.level=j.family_level ) as v_icon,(SELECT COUNT(id) FROM " . DB_PREFIX . "user c WHERE c.family_id=j.id) as user_count FROM " . DB_PREFIX . "family as j where $where and j.status=1 order by user_count>j.contribution>j.video_time >j.create_time desc limit 0,10";

        $family = $GLOBALS['db']->getAll($sql);
        foreach ($family as $k => $v) {
            $family[$k]['family_url'] = url("family#info", array("family_id" => $v['family_id']));
            $family[$k]['v_icon'] = replace_public($v['v_icon']);
        }

        return $family;

    }
    public function add_video()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        //用户是否禁播，$is_ban=1 永久禁播；$is_ban=0非永久禁播，$ban_time禁播结束时间
        $user_id = intval($GLOBALS['user_info']['id']);
        $user = $GLOBALS['db']->getRow("select is_ban,ban_time,is_effect,is_authentication,is_agree from " . DB_PREFIX . "user where id = " . $user_id,
            true, true);
        if (!$user['is_agree']) {
            $is_agree = intval($_REQUEST['is_agree']);
            if ($is_agree > 0) {
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set is_agree =1 where   id=" . $user_id . "  and is_agree = 0  ");

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $data['is_agree'] = 1;
                $user_redis->update_db($user_id, $data);
            } else {
                api_ajax_return(array("status" => 0, "error" => "请先查看主播协议", "need_agree" => 1));
            }
        }
        $m_config = load_auto_cache("m_config");
        //obs 推流延长首次心跳时间
        $obs_monitor_time = intval($m_config['obs_monitor_time']) ? intval($m_config['obs_monitor_time']) : 300;
        $monitor_time = to_date(NOW_TIME + $obs_monitor_time, 'Y-m-d H:i:s'); //主播心跳监听
        $root = array();
        $sql = "select id,push_rtmp,live_image,live_in from " . DB_PREFIX . "video where create_type = 1 and live_in = 2 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {
            //更新心跳时间，免得被删除了
            $sql = "update " . DB_PREFIX . "video set monitor_time = '" . $monitor_time . "' where id =" . $video['id'];
            $GLOBALS['db']->query($sql);

            if ($GLOBALS['db']->affected_rows()) {
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $return['status'] = 1;
                $return['error'] = '';
                $return['room_id'] = $video['id'];
                $return['live_image'] = empty($video['live_image']) ? $GLOBALS['user_info']['head_image'] : $video['live_image'];
                $return['push_rtmp'] = $video['push_rtmp'];
                $return['live_in'] = $video['live_in'];
                $return['video_url'] = get_video_url($video['id'], $video['live_in']);
                api_ajax_return($return);
            }
        }

        //话题
        $cate = $GLOBALS['db']->getAll("select id,title from " . DB_PREFIX . "video_cate where is_effect =1 and is_delete =0 ",
            true, true);
        $root['cate'] = $cate;
        $root['title'] = '';

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,push_rtmp,live_image,live_in,cate_id from " . DB_PREFIX . "video where create_type = 1 and live_in = 1 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {
            $return['status'] = 1;
            $return['error'] = '';
            $return['room_id'] = $video['id'];
            $return['live_image'] = empty($video['live_image']) ? $GLOBALS['user_info']['head_image'] : $video['live_image'];
            $return['push_rtmp'] = $video['push_rtmp'];
            $return['live_in'] = $video['live_in'];
            $return['video_url'] = get_video_url($video['id'], $video['live_in']);
            $cate = load_auto_cache("cate_id", array('id' => $video['cate_id']));
            $return['title'] = $cate['title'];

            api_ajax_return($return);
        }
        $video = $GLOBALS['db']->getRow("select id,live_image,cate_id from " . DB_PREFIX . "video where live_in = 1  and user_id = " . $user_id,
            true, true);
        if (strim($_REQUEST['type']) == 'edu') {
            $root['type'] = strim($_REQUEST['type']);
            $root['deal_id'] = intval($_REQUEST['deal_id']);
            $root['cate_id'] = intval($_REQUEST['cate_id']);
        }
        if (strim($_REQUEST['type']) == 'booking') {
            $root['type'] = strim($_REQUEST['type']);
            $root['deal_id'] = intval($_REQUEST['deal_id']);
        }
        if (!empty($video)) {
            $root['status'] = 1;
            $root['room_id'] = $video['id'];
            $root['live_image'] = $video['live_image'];
            $cate = load_auto_cache("cate_id", array('id' => $video['cate_id']));
            $root['title'] = $cate['title'];
        } else {
            $root['status'] = 1;
            $root['room_id'] = '';
            $root['live_image'] = '';
        }

        api_ajax_return($root);

    }
    public function create_room()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        if (trim($_REQUEST['type']) == 'video_url') {
            $room_id = $_REQUEST['room_id'];
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $live_in = $video_redis->getOne_db($room_id, 'live_in');
            if ($live_in != 1) {
                $root['error'] = '房间正在创建中，请推流后重试！';
                $root['status'] = 0;
            } else {
                $root['video_url'] = get_video_url($room_id, $live_in);
                $root['status'] = 1;
            }
            api_ajax_return($root);
        }

        if (empty($GLOBALS['user_info']['nick_name'])) {
            $root = array('status' => 0, 'error' => '请先前往我的资料填写昵称');
            api_ajax_return($root);
        }

        //用户是否禁播，$is_ban=1 永久禁播；$is_ban=0非永久禁播，$ban_time禁播结束时间
        $user_id = intval($GLOBALS['user_info']['id']);
        $user = $GLOBALS['db']->getRow("select is_ban,ban_time,is_effect,is_authentication,is_agree from " . DB_PREFIX . "user where id = " . $user_id,
            true, true);
        if (intval($user['is_effect']) == 0) {
            $return = array(
                'status' => 0,
                'error' => '请求房间id失败，您被禁播，请联系客服处理。'
            );
            api_ajax_return($return);
        }
        if (intval($user['is_ban']) != 0 || intval($user['ban_time']) >= get_gmtime()) {
            $return = array('status' => 0);
            if (intval($user['is_ban'])) {
                $return['error'] = '请求房间id失败，您被禁播，请联系客服处理。';
            } else {
                $return['error'] = '由于您的违规操作，您被封号暂时不能直播，封号时间截止到：' . to_date(intval($user['ban_time']),
                    'Y-m-d H:i:s') . '。';
            }
            api_ajax_return($return);
        }

        $m_config = load_auto_cache("m_config");
        if ($m_config['must_authentication'] == 1) {
            if ($user['is_authentication'] != 2) {
                // 未认证
                api_ajax_return(array("status" => 0, "error" => "请认证后再发起直播"));
            }
        }
        $room_title = strim($_REQUEST['room_title']); //要修改的房间名称
        if ($room_title) {
            if (strlen($room_title) < 15) {
                api_ajax_return(array('error' => '直播间名称长度至少5个汉字或10个字母', 'status' => 0));
            }
            if (strlen($room_title) > 40) {
                api_ajax_return(array('error' => '直播间名称长度不超过20个汉字', 'status' => 0));
            }
        }
        if (!$user['is_agree']) {
            $is_agree = intval($_REQUEST['is_agree']);
            if ($is_agree > 0) {
                $GLOBALS['db']->query("update " . DB_PREFIX . "user set is_agree =1 where   id=" . $user_id . "  and is_agree = 0  ");
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $data['is_agree'] = 1;
                $user_redis->update_db($user_id, $data);
            } else {
                api_ajax_return(array("status" => 0, "error" => "请先查看主播协议", "need_agree" => 1));
            }
        }

        $live_image = strim($_REQUEST['live_image']); //封面图片
        $title = strim(str_replace('#', '', $_REQUEST['title']));
        if (!$title) {
            $title = "我要直播";
        }
        $cate_id = intval($_REQUEST['cate_id']);
        $location_switch = intval($_REQUEST['location_switch']); //1-上传当前城市名称
        $province = strim($_REQUEST['province']); //省
        $city = strim($_REQUEST['city']); //市
        $is_private = intval($_REQUEST['is_private']); //1：私密聊天; 0:公共聊天
        $share_type = strtolower(strim($_REQUEST['share_type'])); //WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,EMAIL,SMS,SINA
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
        $monitor_time = to_date(NOW_TIME + $obs_monitor_time, 'Y-m-d H:i:s'); //主播心跳监听
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
                $return['status'] = 1;
                $return['error'] = '';
                $return['room_id'] = $video['id'];
                $return['live_image'] = empty($video['live_image']) ? $GLOBALS['user_info']['head_image'] : $video['live_image'];
                $return['push_rtmp'] = $video['push_rtmp'];
                $return['live_in'] = $video['live_in'];
                $return['video_url'] = get_video_url($video['id'], $video['live_in']);
                api_ajax_return($return);
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,push_rtmp,live_image,live_in from " . DB_PREFIX . "video where create_type = 1 and live_in = 1 and user_id = " . $user_id;
        $video = $GLOBALS['db']->getRow($sql, true, true);
        if ($video) {
            $return['status'] = 1;
            $return['error'] = '';
            $return['room_id'] = $video['id'];
            $return['live_image'] = empty($video['live_image']) ? $GLOBALS['user_info']['head_image'] : $video['live_image'];
            $return['push_rtmp'] = $video['push_rtmp'];
            $return['live_in'] = $video['live_in'];
            $return['video_url'] = get_video_url($video['id'], $video['live_in']);
            api_ajax_return($return);
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
            /*
            //客户端没有定位到,服务端则用ip再定位一次
            fanwe_require APP_ROOT_PATH . "system/extend/ip.php";
            $ip = new iplocate ();
            $area = $ip->getaddress ( CLIENT_IP );
            $location = $area ['area1'];
             */

            $ipinfo = get_ip_info();

            $province = $ipinfo['province'];
            $city = $ipinfo['city'];

            //$title = print_r($ipinfo,1);
        }

        //
        $data = $this->create_video($user_id, $title, $is_private, $monitor_time, $cate_id, $province, $city,
            $share_type, $room_title, $live_image);
        //付费直播
        $is_live_pay = intval($_REQUEST['is_live_pay']);
        if ($is_live_pay) {
            if ($data['live_pay_type'] == 1 && (defined('LIVE_PAY_SCENE') && LIVE_PAY_SCENE == 0)) {
                $root['error'] = "按场付费未开启";
                $root['status'] = 0;
                api_ajax_return($root);
            }
            $data['is_live_pay'] = $is_live_pay;
            $data['live_pay_type'] = 1;
            if (empty($m_config['pc_live_fee'])) {
                $data['live_fee'] = $m_config['live_pay_scene_min'];
            } else {
                $data['live_fee'] = $m_config['pc_live_fee'];
            }
        }
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
            $return['status'] = 1;
            $return['error'] = '';
            $return['room_id'] = $data['id'];
            $return['video_type'] = $data['video_type'];
            $return['live_image'] = $data['live_image'];
            $return['push_rtmp'] = $data['push_rtmp'];
            $return['live_in'] = $data['live_in'];
            $return['video_url'] = get_video_url($data['id'], 1);
            sync_video_to_redis($data['id'], '*', false);
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $status['online_status'] = 1;
            $video_redis->update_db($data['id'], $status);
        } else {
            $return['status'] = 0;
            $return['error'] = '创建房间失败！';
        }
        api_ajax_return($return);
    }

    //是否有家族
    public function have_family()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆."; // es_session::id();
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            $data = $GLOBALS['db']->getRow("select status as family_status,id as family_id from " . DB_PREFIX . "family where user_id=" . $user_id);
            if (empty($data)) {
                $root['status'] = 0;
                $root['error'] = "没有家族";
                $root['family_status'] = 0;
                $root['family_id'] = 0;
            } else {
                $root['status'] = 1;
                $root['error'] = "";
                $root['family_status'] = $data['family_status'];
                $root['family_id'] = $data['family_id'];
            }
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
        $share_type = '',
        $room_title = '',
        $live_image = ''
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
            $data['private_key'] = md5($video_id . rand(1, 9999999)); //私密直播key
        } else {
            $data['room_type'] = 3;
        }

        $m_config = load_auto_cache("m_config");
        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']); //允许添加的最大机器人数;

        $sql = "select sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $user_id;
        $user = $GLOBALS['db']->getRow($sql, true, true);

        $info = origin_image_info($user['head_image']);
        $data['head_image'] = get_spec_image($info['file_name']);
        $data['thumb_head_image'] = $user['thumb_head_image'];
        if (empty($live_image)) {
            $data['live_image'] = $data['head_image'];
        } else {
            $data['live_image'] = $live_image;
        }
        $data['room_title'] = $room_title;
        $data['sex'] = intval($user['sex']); //性别 0:未知, 1-男，2-女
        $data['video_type'] = $m_config['video_type']; //0:腾讯云互动直播;1:腾讯云直播;2:千秀云直播

        require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
        $api = createTimAPI();
        $ret = $api->group_create_group('AVChatRoom', (string) $user_id, (string) $user_id, (string) $video_id);
        if ($ret['ActionStatus'] != 'OK') {
            api_ajax_return(array(
                'status' => 0,
                'error' => $ret['ErrorCode'] . $ret['ErrorInfo']
            ));
        }

        $data['group_id'] = $ret['GroupId'];
        $data['monitor_time'] = $monitor_time;

        $data['create_type'] = 1; // 0:APP端创建的直播;1:PC端创建的直播
        $data['push_url'] = ''; //video_type=1;1:腾讯云直播推流地址
        $data['play_url'] = ''; //video_type=1;1:腾讯云直播播放地址(rmtp,flv)

        $data['share_type'] = $share_type;
        $data['title'] = $title;
        $data['cate_id'] = $cate_id;
        $data['user_id'] = $user_id;
        $data['live_in'] = 2; //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = ''; //'当前观看人数';
        $data['vote_number'] = ''; //'获得票数';
        $data['province'] = $province; //'省';
        $data['city'] = $city; //'城市';

        $data['create_time'] = NOW_TIME; //'创建时间';
        $data['begin_time'] = NOW_TIME; //'开始时间';
        $data['end_time'] = ''; //'结束时间';
        $data['is_hot'] = 1; //'1热门; 0:非热门';
        $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1; //主播在线状态;1:在线(默认); 0:离开

        //sort_init(初始排序权重) = (用户可提现秀票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留秀票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
        $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);

        $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
        $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);

        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];
        return $data;
    }

    public function goods()
    {
        if (!$GLOBALS['user_info']) {
            $root = array(
                "error" => "用户未登陆,请先登陆.",
                "status" => 0,
                "user_login_status" => 0 //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            );
            api_ajax_return($root);
        }
        $user_id = intval($GLOBALS['user_info']['id']); //自己ID

        $page_size = 20;
        $page = intval($_REQUEST['page']);
        $page = $page > 0 ? $page : 1;

        $table = DB_PREFIX . 'pc_goods';
        $field = 'count(1) as count';
        $where = "user_id = $user_id AND is_delete = 0";
        $count = $GLOBALS['db']->getOne("SELECT $field FROM $table WHERE $where");

        $field = 'id,name,imgs,price,url,description,kd_cost';
        $limit = ($page - 1) * $page_size . ',' . $page_size;
        $goods = $GLOBALS['db']->getAll("SELECT $field FROM $table WHERE $where  order by id desc LIMIT $limit");
        foreach ($goods as $k => $v) {
            if ($goods[$k]['imgs'] != '') {
                $goods[$k]['imgs'] = json_decode($v['imgs']);
                if ($goods[$k]['imgs'] == "") {
                    $goods[$k]['imgs'] = array();
                } else {
                    foreach ($goods[$k]['imgs'] as $k1 => $v1) {
                        $goods[$k]['imgs'][$k1] = get_spec_image($v1);
                    }
                }
            } else {
                $goods[$k]['imgs'] = array();
            }
        }

        $page_model = new Page($count, $page_size);
        $root = array('page_title' => '个人中心-购物推荐', 'list' => $goods, 'page' => $page_model->show());
        api_ajax_return($root);
    }

    public function add_goods()
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
        $root['status'] = 1;
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

    //修改密码
    public function set_pwd()
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
        $user_pwd = md5(trim($_REQUEST['pwd']));
        $verify_code = trim($_REQUEST['verify_coder']);
        $mobile = $GLOBALS['user_info']['mobile'];
        if (empty($mobile)) {
            $mobile = $GLOBALS['db']->getOne('SELECT mobile FROM ' . DB_PREFIX . 'user WHERE id = ' . $user_id);
            if (empty($mobile)) {
                $root['status'] = 0;
                $root['error'] = "请先绑定手机号";
                ajax_return($root);
            }
        }
        if ($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "mobile_verify_code WHERE mobile = " . $mobile . " AND verify_code = '" . $verify_code . "'") == 0) {
            $root['error'] = "手机验证码出错";
            $root['status'] = 0;
            ajax_return($root);
        } else {
            $expired_time = app_conf("EXPIRED_TIME");
            if ($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "mobile_verify_code WHERE mobile = " . $mobile . " AND verify_code = '" . $verify_code . "' AND " . NOW_TIME . "-create_time <={$expired_time}") == 0) {
                $root['error'] = "手机验证码超时，请重新发送";
                $root['status'] = 0;
                ajax_return($root);
            }
            if ($GLOBALS['db']->query("UPDATE " . DB_PREFIX . "user SET user_pwd = '" . $user_pwd . "' WHERE id = " . $user_id)) {
                $root['status'] = 1;
                $root['error'] = "修改成功";
                ajax_return($root);
            } else {
                $root['status'] = 0;
                $root['error'] = "修改失败";
                ajax_return($root);
            }
        }
    }
    // 我的私信-好友列表
    public function friend_list()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0; //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        } else {
            $user_id = intval($GLOBALS['user_info']['id']); //id
            $video_id = intval($_REQUEST['room_id']);

//            $page = intval($_REQUEST['p']);//取第几页数据
            //
            //            if($page==0){
            //                $page = 1;
            //            }
            //
            //            $page_size=20;

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            $user_redis = new UserFollwRedisService($user_id);
            $root = $user_redis->get_private_user(1, 100);

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
            $video_redis = new VideoRedisService();
            $video_data = $video_redis->getRow_db($video_id, array('group_id', 'user_id'));

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoViewerRedisService.php');
            $video_viewer_redis = new VideoViewerRedisService();
            $group_id = $video_data['group_id']; //聊天群id

            if ($group_id) {
                $users = $video_viewer_redis->get_viewer_list($group_id, 1);
                if ($users['list']) {
                    $user_ids = array_column($users['list'], 'user_id');
                    $friends = $root['list'];
                    foreach ($friends as $k => $v) {
                        if (in_array($v['user_id'], $user_ids) || $v['user_id'] == $video_data['user_id']) {
                            unset($friends[$k]);
                        }
                    }
                    $root['list'] = array_values($friends);
                }
            }

//            $rs_count=count($list);
            //            $page=intval($_REQUEST['p']);
            //            $page_size=35;
            //            $start=($page-1)*$page_size;
            //            $new_list=array_slice($list,$start,$page_size);
            $root['friends'] = array();
            foreach ($root['list'] as $v) {
                $root['friends'][] = array(
                    "user_id" => $v['user_id'],
                    "nick_name" => htmlspecialchars_decode($v['nick_name']),
                    "head_image" => $v['head_image'],
                    "unread" => 0
                );
            }
            $sql = "SELECT id as user_id,nick_name FROM " . DB_PREFIX . "user where is_admin=1";
            $system_user = $GLOBALS['db']->getAll($sql, true, true);
            $root['system_uid'] = $system_user;
            $usersig = load_auto_cache("usersig", array("id" => $user_id));
            $root['usersig'] = $usersig['usersig'];
//            $page = new Page($rs_count,$page_size);   //初始化分页对象
            //            $root['page'] = $page->show();
            $m_config = load_auto_cache("m_config");
            $root['tim_sdkappid'] = $m_config['tim_sdkappid'];
            $root['tim_account_type'] = $m_config['tim_account_type'];
            $root['status'] = 1;
        }
        api_ajax_return($root);
    }
    protected static function getUserId()
    {
        $id = intval($GLOBALS['user_info']['id']);
        if (!$id) {
            self::returnError('用户未登陆,请先登陆', 0, ['user_login_status' => 0]);
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/Model.class.php');
        Model::$lib = APP_ROOT_PATH . 'mapi/lib/';
        return $id;
    }
    protected static function returnError($error = '出错了！', $status = 0, $data = [])
    {
        $data['status'] = $status;
        $data['error'] = $error;
        if ($error == '参数错误') {
            $data['data'] = $_REQUEST;
        }
        api_ajax_return($data);
    }
    public function weixin_distribution()
    {
        $id = self::getUserId();
        $type = intval($_REQUEST['type']);
        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $user_id = intval($_REQUEST['user_id']);
        $game_log_id = intval($_REQUEST['game_log_id']);

        $where = [];

        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year = $y;
            $month = $m;
        }
        $start = strtotime("{$year}-{$month}-1 00:00:00");
        $end = strtotime('+1 month', $start);
        $where['l.create_time'] = ['between', [$start, $end]];
        if ($user_id) {
            $where['d.user_id'] = $user_id;
        }
        if ($game_log_id) {
            $where['l.game_log_id'] = $game_log_id;
        }
        $d = Model::build('weixin_distribution')->selectOne(['user_id' => $id]);
        if ($d['topid']) {
            $model = Model::build('weixin_distribution_log');
            switch ($type) {
                case 1:
                    $root = $model->childPropDistribution($id, $where);
                    break;
                case 2:
                    $root = $model->childGame($id, $where);
                    break;
                case 3:
                    unset($where['l.create_time']);
                    unset($where['l.game_log_id']);
                    $root = $model->childProp($id, $where, $start);
                    break;
                case 4:
                    $root = $model->paymentNotice($id, $where);
                    break;
                default:
                    $root = $model->childGameDistribution($id, $where);
                    break;
            }
        }
        $root['type'] = $type;
        $root['page_title'] = "个人中心-微信分销";
        $root['year'] = $year;
        $root['month'] = $month;
        $root['user_id'] = $user_id;
        $root['game_log_id'] = $game_log_id;
        $root['years'] = range($y, $y - 5);
        $root['months'] = range(1, 12);
        $root['act'] = 'weixin_distribution';
        $root['first_rate'] = $d['first_rate'];
        $root['second_rate'] = $d['second_rate'];
        $m_config = load_auto_cache("m_config");
        $root['first_rate'] = $d['first_rate'] ? $d['first_rate'] : $m_config['weixin_first_rate'];
        $root['second_rate'] = $d['second_rate'] ? $d['second_rate'] : $m_config['weixin_second_rate'];
        $money = isset($root['total_diamonds']) ? $root['total_diamonds'] : $root['total_diamonds'];
        $root['ticheng'] = intval($root['first_distribution'] * $root['first_rate'] + ($money - $root['first_distribution']) * $root['second_rate']);
        api_ajax_return($root);
    }
    public function game_distribution()
    {
        $id = self::getUserId();
        $type = intval($_REQUEST['type']);
        $year = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $user_id = intval($_REQUEST['user_id']);
        $game_log_id = intval($_REQUEST['game_log_id']);

        $where = [];

        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year = $y;
            $month = $m;
        }
        $start = strtotime("{$year}-{$month}-1 00:00:00");
        $end = strtotime('+1 month', $start);
        $where['l.create_time'] = ['between', [$start, $end]];
        if ($user_id) {
            $where['d.id'] = $user_id;
        }
        if ($game_log_id) {
            $where['l.game_log_id'] = $game_log_id;
        }
        $d = Model::build('user')->field('game_distribution_top_id topid,game_distribution1 first_rate,game_distribution2 second_rate')->selectOne(['id' => $id]);
        if ($d['topid']) {
            $model = Model::build('game_distribution');
            switch ($type) {
                case 1:
                    $root = $model->childPropDistribution($id, $where);
                    break;
                case 2:
                    $root = $model->childGame($id, $where);
                    break;
                case 3:
                    unset($where['l.create_time']);
                    unset($where['l.game_log_id']);
                    $root = $model->childProp($id, $where, $start);
                    break;
                default:
                    $root = $model->childGameDistribution($id, $where);
                    break;
            }
        }
        $root['type'] = $type;
        $root['page_title'] = "个人中心-游戏分销";
        $root['year'] = $year;
        $root['month'] = $month;
        $root['user_id'] = $user_id;
        $root['game_log_id'] = $game_log_id;
        $root['years'] = range($y, $y - 5);
        $root['months'] = range(1, 12);
        $root['act'] = 'game_distribution';
        $m_config = load_auto_cache("m_config");
        $root['first_rate'] = $d['first_rate'] ? $d['first_rate'] : $m_config['game_distribution1'];
        $root['second_rate'] = $d['second_rate'] ? $d['second_rate'] : $m_config['game_distribution2'];
        api_ajax_return($root);
    }
}
