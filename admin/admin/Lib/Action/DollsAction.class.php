<?php

class DollsAction extends CommonAction
{
    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $doll = M(MODULE_NAME)->where("id=" . $id)->find();
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M(MODULE_NAME)->where("id=" . $id)->setField("sort", $sort);
        save_log($doll['name'] . l("SORT_SUCCESS"), 1);
        $this->success(l("SORT_SUCCESS"), 1);
    }

    public function set_recommend()
    {
        $id = intval($_REQUEST['id']);
        $suite = M(MODULE_NAME)->where("id=" . $id)->find(); //当前状态
        if ($suite['type'] == 3) {

            $video = M('video')->where("id=" . intval($suite['room_id']))->find();
            if (!$video) {
                $m_config = load_auto_cache("m_config");
                $doll = M('DollCate')->where(['id' => $suite['cate_id']])->find();
                $sql = "select is_authentication,is_ban,ban_time,mobile,login_ip,ban_type,apns_code,sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . 1;
                $user = $GLOBALS['db']->getRow($sql, true, true);
                //插入对应数据

                $video_id = intval($suite['room_id']);
                $data = array();
                $data['id'] = $video_id;
                $data['title'] = intval($doll['title']);
                $data['room_type'] = 3;
                $data['virtual_number'] = intval($m_config['virtual_number']);
                $data['max_robot_num'] = intval($m_config['robot_num']); //允许添加的最大机器人数;

                $data['head_image'] = $user['head_image'];
                $data['thumb_head_image'] = $user['thumb_head_image'];
                $data['sex'] = intval($user['sex']); //性别 0:未知, 1-男，2-女
                $data['video_type'] = intval($m_config['video_type']); //0:腾讯云互动直播;1:腾讯云直播

                $data['group_id'] = $video_id;
                $data['monitor_time'] = to_date(NOW_TIME + 86400 * 365 * 10, 'Y-m-d H:i:s'); //主播心跳监听

                $data['push_url'] = ''; //video_type=1;1:腾讯云直播推流地址
                $data['play_url'] = ''; //video_type=1;1:腾讯云直播播放地址(rmtp,flv)

                $data['user_id'] = 1;
                $data['live_in'] = 1; //live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
                $data['watch_number'] = ''; //'当前观看人数';
                $data['vote_number'] = ''; //'获得票数';
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

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
                $data['prop_table'] = createPropTable();

                $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');

                sync_video_to_redis($video_id, '*', false);
            }

        }

        $n_is_effect = $suite['is_recommend'] == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->where("id=" . $id)->setField("is_recommend", $n_is_effect);
        save_log($suite['id'] . l("SET_RECOMMEND_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_RECOMMEND_" . $n_is_effect), 1);
    }

    public function push_url()
    {
        $id = intval($_REQUEST['id']);
        $doll = M(MODULE_NAME)->where("id=" . $id)->find();

        $video = M('Video')->where("id=" . $doll['room_id'])->find();

        $this->assign('doll', $doll);
        $this->assign('video', $video);
        $this->display();
    }

    public function re_build()
    {
        $id = intval($_REQUEST['id']);
        $doll = M(MODULE_NAME)->where("id=" . $id)->find();

        $GLOBALS['db']->autoExecute(DB_PREFIX . 'dolls', [
            'is_recommend' => 0
        ], 'UPDATE', 'id=' . $doll['id']);

        $video_id = $doll['room_id'];
        $data = ['live_in' => 2];

        fanwe_require(APP_ROOT_PATH . 'mapi/wawa/core/common_wawa.php');
        $channel_info = get_push_url($video_id, 1, 'a');

        $data['channelid'] = $channel_info['channel_id'];
        $data['push_rtmp'] = $channel_info['upstream_address'];
        $data['play_flv'] = $channel_info['downstream_address']['flv'];
        $data['play_rtmp'] = $channel_info['downstream_address']['rtmp'];
        $data['play_hls'] = $channel_info['downstream_address']['hls'];

        $data['expire_time'] = to_date(NOW_TIME + 86400 * 30);

        $channel_info2 = get_push_url($video_id, 0, 'b');
        $data['channelid2'] = $channel_info2['channel_id'];
        $data['push_rtmp2'] = $channel_info2['upstream_address'];
        $data['play_flv2'] = $channel_info2['downstream_address']['flv'];
        $data['play_rtmp2'] = $channel_info2['downstream_address']['rtmp'];
        $data['play_hls2'] = $channel_info2['downstream_address']['hls'];

        $GLOBALS['db']->autoExecute(DB_PREFIX . 'video', $data, 'UPDATE', 'id=' . $doll['room_id']);

        admin_ajax_return([
            'status' => 1,
            'error' => '已生成',
            'video' => $data
        ]);
    }

    public function add()
    {
        $cate_tree = M("DollCate")->findAll();
        $classified_tree = M("VideoClassified")->where("is_effect=1")->findAll();
        $this->assign("cate_tree", $cate_tree);
        $this->assign("classified_tree", $classified_tree);
        $this->display();
    }

    public function edit()
    {
        $id = intval($_REQUEST['id']);
        $vo = M(MODULE_NAME)->where(['id' => $id])->find();
        //获取分类ID
        $vo['classified_id'] = M("Video")->where("id=" . intval($vo['room_id']))->getField("classified_id");
        $this->assign('vo', $vo);
        $cate_tree = M("DollCate")->findAll();
        $classified_tree = M("VideoClassified")->where("is_effect=1")->findAll();
        $this->assign("cate_tree", $cate_tree);
        $this->assign("classified_tree", $classified_tree);
        $this->display();
    }

    private function check_tencent($data)
    {
        if (!check_empty($data['room_id'])) {
            $this->error("请输入房间号");
        }

        if (!check_empty($data['front_push_user'])) {
            $this->error("请输入正面推流用户");
        }
        if (!check_empty($data['side_push_user'])) {
            $this->error("请输入侧面推流用户");
        }
    }

    private function self_build($data, $doll)
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/wawa/core/common_wawa.php');

        create_doll_video($data['room_id'], 1, $doll['title'], $doll['img'], "2");
    }

    public function insert()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        $data['mac'] = strtoupper($data['mac']);

        //获取分类ID
        $data['classified_id'] = intval($_REQUEST['classified_id']);

        //获取音乐地址
        if (strim($_REQUEST['kefile_url'])) {
            $data['music'] = strim($_REQUEST['kefile_url']);
            if (!strpos($data['music'], '.mp3')) {
                $this->error("背景音乐必须为mp3格式");
            }
        } else {
            unset($data['music']);
        }

        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));

        if ($data['cate_id'] == 0) {
            $this->error("请选择娃娃");
        }

        $doll = M('DollCate')->where(['id' => $data['cate_id']])->find();
        if (empty($doll)) {
            $this->error("选择的娃娃不存在");
        }

        switch ($data['type']) {
            case "0":
                $this->check_tencent($data);
                break;
            case "1":
                break;
            case "2":
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
                $data['room_id'] = get_max_room_id();
                $this->self_build($data, $doll);
                break;

            case "3":
                $doll = M(MODULE_NAME)->where("mid=" . $data['mid'])->find();
                if ($doll) {
                    $this->error('机器码重复' . L("INSERT_FAILED"));
                    return false;
                }
                break;
            default:
                throw new Exception;
        }

        //如果有分类ID，则video表对应的记录更新数据
        if ($data['classified_id'] > 0 && $data['room_id'] > 0) {
            $GLOBALS['db']->autoExecute(DB_PREFIX . 'video', [
                'classified_id' => $data['classified_id']
            ], 'UPDATE', 'id=' . $data['room_id']);
        }

        // 更新数据
        $log_info = $data['name'];
        $list = M(MODULE_NAME)->add($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
    }

    public function update()
    {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        $data['mac'] = strtoupper($data['mac']);

        //获取分类ID参数
        $data['classified_id'] = intval($_REQUEST['classified_id']);

        //获取音乐地址
        if (strim($_REQUEST['kefile_url'])) {
            $data['music'] = strim($_REQUEST['kefile_url']);
            if (!strpos($data['music'], '.mp3')) {
                $this->error("背景音乐必须为mp3格式");
            }
        } else {
            unset($data['music']);
        }

        $room_id = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("room_id");

        $log_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("name");
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/edit", array("id" => $data['id'])));

        if ($data['cate_id'] == 0) {
            $this->error("请选择娃娃");
        }

        /*if ($data['classified_id'] == 0) {
        $this->error("请选择分类");
        }*/

        $doll = M('DollCate')->where(['id' => $data['cate_id']])->find();
        if (empty($doll)) {
            $this->error("选择的娃娃不存在");
        }

        //修改video表里的分类
        $GLOBALS['db']->autoExecute(DB_PREFIX . 'video', [
            'classified_id' => $data['classified_id']
        ], 'UPDATE', 'id=' . $room_id);

        if ($data['type'] == 3) {

            //更新
            $m_config = load_auto_cache('m_config');
            $sdkappid = $m_config['tim_sdkappid'];

            $data['front_push_usersig'] = load_auto_cache("usersig", array("id" => $data['front_push_user']))['usersig'];
            $data['side_push_usersig'] = load_auto_cache("usersig", array("id" => $data['side_push_user']))['usersig'];

            //更新云平台
            $data1 = array();
            $data1['customer_sdkappid'] = $sdkappid;
            $data1['front_userid'] = $data['front_push_user'];
            $data1['front_usersig'] = $data['front_push_usersig'];
            $data1['front_camara_match'] = $data['front_push_camara_match'];
            //$data1['front_camara_match']='front_camara_match';
            $data1['side_userid'] = $data['side_push_user'];
            $data1['side_usersig'] = $data['side_push_usersig'];
            $data1['side_camara_match'] = $data['side_push_camara_match'];
            //$data1['side_camara_match']='side_camara_match';
            $data1['deviceID'] = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("mid");
            $data1['callback_url'] = SITE_DOMAIN . '/dolls_callback.php';
            $data1['type'] = 0;
            fanwe_require(APP_ROOT_PATH . 'mapi/wawa_server/apitest/CurlInvoker.php');
            $res = CurlInvoker::invoke("open.device/twwj/update_device", $data1);
            if ($res['errcode'] != 0) {
                print_r($res);exit();
            }

            //更新上线
            $data2 = array();
            $data2['customer_sdkappid'] = $sdkappid;
            $data2['deviceID'] = M(MODULE_NAME)->where("id=" . intval($data['id']))->getField("mid");

            fanwe_require(APP_ROOT_PATH . 'mapi/wawa_server/apitest/CurlInvoker.php');
            $res = CurlInvoker::invoke("open.device/twwj/set_device", $data2);

            if ($res['errcode'] != 0) {
                print_r($res);exit();
            } else {
                $res['data'] = json_decode($res['data'], true);
                if ($res['data']['ErrorCode'] != 0) {
                    print_r($res);exit();
                }

            }

        }

        $list = M(MODULE_NAME)->save($data);
        if (false !== $list) {
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function delete()
    {
        //删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            $info = $id;
            $list = M(MODULE_NAME)->where($condition)->delete();
            if ($list !== false) {
                save_log($info . l("DELETE_SUCCESS"), 1);
                $this->success(l("DELETE_SUCCESS"), $ajax);
            } else {
                save_log($info . l("DELETE_FAILED"), 0);
                $this->error(l("DELETE_FAILED"), $ajax);
            }
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    //查看状态日志
    public function status_log()
    {
        $machine_id = $_REQUEST['machine_id'];
        $play_user_id = $_REQUEST['play_user_id'];
        $update_action = $_REQUEST['update_action'];
        $update_time_1 = $_REQUEST['update_time_1'];

        if (trim($machine_id) != '') {
            $parameter .= "machine_id=" . intval($machine_id) . "&";
            $sql_w .= "machine_id=" . intval($machine_id) . " and ";
        }

        if (trim($play_user_id) != '') {
            $parameter .= "play_user_id=" . intval($play_user_id) . "&";
            $sql_w .= "play_user_id=" . intval($play_user_id) . " and ";
        }

        if (trim($update_action) != '') {
            $parameter .= "update_action like " . urlencode('%' . trim($update_action) . '%') . "&";
            $sql_w .= "update_action like '%" . trim($update_action) . "%' and ";
        }

        $update_time_2 = empty($_REQUEST['update_time_2']) ? to_date(get_gmtime(), 'Y-m-d') : strim($_REQUEST['update_time_2']);
        $update_time_2 = to_timespan($update_time_2);
        if (trim($update_time_1) != '') {
            $parameter .= "update_time between '" . to_timespan($update_time_1) . "' and '" . $update_time_2 . "'&";
            $sql_w .= " (update_time between '" . to_timespan($update_time_1) . "' and '" . $update_time_2 . "' ) and ";
        }

        $model = D();

        $sql_str = "SELECT *," .
            " id,machine_id,status,play_user_id,update_time,update_action,action_status,err_code" .
            " FROM " . DB_PREFIX . "dolls_machine_info WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM " . DB_PREFIX . "dolls_machine_info WHERE 1=1";
        $sql_str .= " and " . $sql_w . " 1=1 ";
        $count_sql .= " and " . $sql_w . " 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'id', 0, $count_sql);
        $this->assign('id', $id);
        $this->assign('list', $voList);
        $this->display();
    }

    public function err_code()
    {
        $err_code = $_REQUEST['err_code'];

        if (trim($err_code) != '') {
            $parameter .= "err_code=" . intval($err_code) . "&";
            $sql_w .= "err_code=" . intval($err_code) . " and ";
        }

        $model = D();

        $sql_str = "SELECT *," .
            " id,err_code,des" .
            " FROM " . DB_PREFIX . "dolls_err_code WHERE 1=1";
        $count_sql = "SELECT count(*) as tpcount" .
            " FROM " . DB_PREFIX . "dolls_err_code WHERE 1=1";
        $sql_str .= " and " . $sql_w . " 1=1 ";
        $count_sql .= " and " . $sql_w . " 1=1 ";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'id', 0, $count_sql);
        $this->assign('list', $voList);
        $this->display();
    }

    public function err_add()
    {
        $this->display();
    }

    public function insertErrCode()
    {
        B('FilterString');
        $err_code = $_REQUEST['err_code'];
        $des = $_REQUEST['des'];

        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/err_code"));

        if ($err_code == '' || $des == '') {
            $this->error("错误码和错误描述必须填写完整");
        }

        $new_err['err_code'] = $err_code;
        $new_err['des'] = $des;

        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "dolls_err_code", $new_err, "INSERT");
        if (!$res) {
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
        //成功提示
        save_log($log_info . L("INSERT_SUCCESS"), 1);
        $this->success(L("INSERT_SUCCESS"));

    }

    public function delete_err()
    {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        if (isset($id)) {
            $sql = "delete FROM " . DB_PREFIX . "dolls_err_code  where id = {$id}";
            $GLOBALS['db']->query($sql);
            save_log($info . l("FOREVER_DELETE_SUCCESS"), 1);
            $this->success(l("FOREVER_DELETE_SUCCESS"), $ajax);
        } else {
            $this->error(l("INVALID_OPERATION"), $ajax);
        }
    }

    public function err_edit()
    {
        $id = $_REQUEST['id'];
        $err = $GLOBALS['db']->getRow("select err_code,des from " . DB_PREFIX . "dolls_err_code where id = {$id}");
        $this->assign("id", $id);
        $this->assign("err_code", $err['err_code']);
        $this->assign("des", $err['des']);
        $this->display();
    }

    public function updateErrDes()
    {
        B('FilterString');
        $id = $_REQUEST['id'];
        $des = $_REQUEST['des'];

        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/err_code"));

        if ($des == '') {
            $this->error("错误描述不能为空");
        }

        $res = $GLOBALS['db']->autoExecute(DB_PREFIX . "dolls_err_code", [
            'des' => $des
        ],
            'UPDATE', "id=" . $id);
        if (!$res) {
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
        //成功提示
        save_log($log_info . L("UPDATE_SUCCESS"), 1);
        $this->success(L("UPDATE_SUCCESS"));
    }

    public function supplier_config_machine()
    {
        $id = $_REQUEST['id'];

        $doll = M(MODULE_NAME)->where(['id' => $id])->find();
        if ($doll['type'] != 3) {
            $this->error('该类型无需同步');
        }

        $video = M('Video')->where(['id' => $doll['room_id']])->find();

        $m_config = load_auto_cache('m_config');
        $subcmd = "supplier_config_machine";
        $sdkappid = $m_config['tim_sdkappid'];
        $identifier = $m_config['tim_identifier'];
        $usersig = load_auto_cache("usersig", array("id" => $identifier))['usersig'];
        $rand = mt_rand(1, 1000);

        $data = [
            "sid" => "40c98410-e45e-11e7-b84a-ed743f160197",
            "mid" => intval($doll['mid']),
            "customer_sdkappid" => $sdkappid,
            "groupid" => $doll['room_id'],

            "front_userid" => $doll['front_push_user'],
            //"front_usersig"=>load_auto_cache("usersig", array("id" => $doll['front_push_user']))['usersig'],
            "front_usersig" => $doll['front_push_usersig'],
            "front_camara_match" => $doll['front_push_camara_match'],

            "side_userid" => $doll['side_push_user'],
            //"side_usersig"=>load_auto_cache("usersig", array("id" => $doll['side_push_user']))['usersig'],
            "side_usersig" => $doll['side_push_usersig'],
            "side_camara_match" => $doll['side_push_camara_match'],

            "push_enable" => 1,
            "monitor_enable" => 1,
            "auth_key" => "customer authkey",
            "callback_url" => SITE_DOMAIN . '/dolls_callback.php'];

        $url = "https://console.tim.qq.com/v4/ilvb_doll_catch/common_interface?ver=1&servicename=ilvb_doll_catch&command=common_interface&sdkappid=$sdkappid&subcmd=$subcmd&identifier=$identifier&usersig=$usersig&random={$rand}&contenttype=json";
        //print_r($url);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
        $trans = new transport();
        $response = $trans->request($url, json_encode($data), 'POST');

        $response = json_decode($response['body'], true);
        if ($response['ActionStatus'] == 'OK' && $response['ErrorCode'] == 0) {
            ;
        } else {
            $this->error($response['ErrorInfo']);
        }
    }

    public function supplier_get_machines()
    {

        $dolls_list = $GLOBALS['db']->getAll("select err_code,des from " . DB_PREFIX . "dolls where type=3");

        if (!$dolls_list) {
            $this->error('无该类型数据');
        }
        $id_array = array();
        foreach ($dolls_list as $v) {
            $id_array[] = intval($v['id']);
        }

        $m_config = load_auto_cache('m_config');
        $subcmd = "supplier_get_machines";
        $sdkappid = $m_config['tim_sdkappid'];
        $identifier = $m_config['tim_identifier'];
        $usersig = load_auto_cache("usersig", array("id" => $identifier))['usersig'];
        $rand = mt_rand(1, 1000);

        $data = [
            "sid" => "40c98410-e45e-11e7-b84a-ed743f160197",
            "mids" => $id_array,
            "page" => 1,
            "size" => 10];

        $url = "https://console.tim.qq.com/v4/ilvb_doll_catch/common_interface?ver=1&servicename=ilvb_doll_catch&command=common_interface&sdkappid=$sdkappid&subcmd=$subcmd&identifier=$identifier&usersig=$usersig&random={$rand}&contenttype=json";
        //print_r($url);
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/transport.php');
        $trans = new transport();
        $response = $trans->request($url, json_encode($data), 'POST');
        $response = json_decode($response['body'], true);

        if ($response['ActionStatus'] == 'OK' && $response['ErrorCode'] == 0) {
            //更新
            $data = $response['data'];
            $machines = $data['machines'];
            foreach ($machines as $k => $v) {
                $doll = array();
                $video = array();
                //$doll['mid']=$v['mid'];
                $doll['room_id'] = $v['groupid'];
                $doll['front_push_user'] = $v['front']['userid'];
                $doll['front_push_usersig'] = $v['front']['usersig'];
                $doll['front_push_camara_match'] = $v['front']['camara_match'];

                $doll['side_push_user'] = $v['side']['userid'];
                $doll['side_push_usersig'] = $v['side']['usersig'];
                $doll['side_push_camara_match'] = $v['side']['camara_match'];

                //$video['id']=$v['mid'];
                //$video['group_id']=$v['groupid'];

                $GLOBALS['db']->autoExecute(DB_PREFIX . 'dolls', [
                    $doll
                ], 'UPDATE', 'mid=' . $v['mid']);

                $log_info = $v['mid'];
                save_log($log_info . L("UPDATE_SUCCESS"), 1);
                $this->success(L("UPDATE_SUCCESS"));
            }

        } else {
            $this->error($response['ErrorInfo']);
        }

    }
    public function get_machines_id()
    {
        //远程获取id
        /*admin_ajax_return([
        'status' => 1,
        'error' => '已生成',
        'id' => 1,
        ]);*/
        $m_config = load_auto_cache('m_config');
        $sdkappid = $m_config['tim_sdkappid'];
        $data = array();
        $data['customer_sdkappid'] = $sdkappid;
        $data['front_userid'] = 'front_userid';
        $data['front_usersig'] = 'front_usersig';
        $data['front_camara_match'] = 'front_camara_match';
        $data['side_userid'] = 'side_userid';
        $data['side_usersig'] = 'side_usersig';
        $data['side_camara_match'] = 'side_camara_match';
        $data['callback_url'] = SITE_DOMAIN . '/dolls_callback.php';
        $data['type'] = 0;

        fanwe_require(APP_ROOT_PATH . 'mapi/wawa_server/apitest/CurlInvoker.php');
        $res = CurlInvoker::invoke("open.device/twwj/create_device", $data);

        header("Content-Type:text/html; charset=utf-8");

        if ($res['data']['deviceID']) {
            echo $res['data']['deviceID'];
        } else {
            echo "查询失败，请联系售后";
        }
        //print_r($res);
    }

}
