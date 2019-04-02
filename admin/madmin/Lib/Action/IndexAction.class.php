<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class IndexAction extends AuthAction
{
    //首页
    public function index()
    {

        //判断用户是否登陆
        $user_admin_session = $_SESSION["user_admin_session"];
        $user_admin_id = $user_admin_session['id'];


        if ($user_admin_id == "") {
            //已登录
            $this->redirect(u("Public/login"));
        } else {
            $this->display();
        }

    }

    //框架头
    public function top()
    {
        /*$adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $role_id = intval($adm_session['role_id']);
        $navs = get_admin_nav($role_id, $adm_session['adm_name']);
        $this->assign("navs", $navs);

        $this->assign("admin", $adm_session);*/
        $user_admin_session = $_SESSION['user_admin_session'];
        $this->assign("user_admin_session", $user_admin_session);
        //dump($user_admin_session);die;
        $this->display();
    }
    //框架左侧
    public function left()
    {

        //用户信息
        $user = M("user")->where("id = ".$_SESSION['user_admin_session']['id'])->find();
        $this->assign("user", $user);

        //dump($user['user_iden_id']);die;
        //当前用户可以添加的会员等级
        $user_iden = M("user_iden")->where("id < ".$user['user_iden_id'])->order("id desc")->select();
        $this->assign("user_iden", $user_iden);

        $left_user_iden = M("user_iden")->where("id < ".$user['user_iden_id'])->order("id desc")->find();
        $this->assign("left_user_iden", $left_user_iden);


        $this->display();
    }
    //默认框架主区域

    public function main()
    {

        $user_admin_session = $_SESSION['user_admin_session'];
        $user_admin_id = $user_admin_session['id'];
        $this->qrcode_gen($user_admin_id);


        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $this->assign("adm_session", $adm_session);
        $adm_id = intval($adm_session['adm_id']);
        $login_time = $GLOBALS['db']->getOne("SELECT login_time FROM " . DB_PREFIX . "admin where id = $adm_id ");
        $h = to_date($login_time, "H");
        $login_time = to_date($login_time);
        $this->assign("login_time", $login_time);
        if ($h < 12) {
            $greet = "上午好";
        } elseif ($h < 18) {
            $greet = "下午好";
        } else {
            $greet = "晚上好";
        }
        $this->assign("greet", $greet);

        //$navs = require_once APP_ROOT_PATH."system/admnav_cfg.php";
        $role_id = intval($adm_session['role_id']);
        $navs = get_admin_nav($role_id, $adm_session['adm_name']);
        $this->assign("navs", $navs);


        //会员信息
        $user = M("user")->where("id = {$user_admin_id}")->find();
        $this->assign("user",$user);


        //普通用户
        $user_type_count = M("user")->where("user_type = 0 and parent_id = {$user_admin_id}")->count();
        $this->assign("user_type_count",$user_type_count);

        //认证用户
        $authentication_count = M("user")->where("is_authentication = 2 and parent_id =  {$user_admin_id}")->count();
        $this->assign("authentication_count",$authentication_count);

        //会员总数
        $user_count = M("user")->where("parent_id = {$user_admin_id}")->count();
        $this->assign("user_count",$user_count);








        if ($navs['index']['groups']['index']['nodes'][0]['action'] == main) {

            //待审核
            $register_count = M("User")->where("is_effect=0 and user_type=0 and parent_id = {$user_admin_id}")->count();
            $company_register_count = M("User")->where("is_effect=0  and user_type=1 and parent_id = {$user_admin_id}")->count();
            $this->assign("register_count", $register_count);
            $this->assign("company_register_count", $company_register_count);

            //认证
            $user_authentication = M("User")->where("is_authentication = 1 and is_effect=1 and is_robot = 0  and parent_id = {$user_admin_id}")->count();
            //$business_authentication=M("User")->where("is_authentication = 1 and is_effect=1 and user_type=1 ")->count();
            //$all_authentication=M("User")->where(" (user_type=0 or user_type=1) and is_authentication =1 and is_effect=1 and is_robot = 0")->count();
            //认证未通过
            $authentication_not_allow = M("User")->where("is_authentication = 3 and is_effect=1 and  is_robot = 0  and parent_id = {$user_admin_id}")->count();
            $this->assign("user_authentica", $user_authentication);
            //$this->assign("business_authentica",$business_authentication);
            //$this->assign("all_authentica",$all_authentication);
            $this->assign("authentication_not_allow", $authentication_not_allow);

            //充值
            $pay_count = floatval($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "payment_notice where  payment_id <>0"));
            $this->assign("pay_count", $pay_count);
            // 提现
            $carry_count = (int) $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "user_refund WHERE is_pay = 0");
            $this->assign("carry_count", $carry_count);
            $waitpay_carry_count = (int) $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . DB_PREFIX . "user_refund WHERE is_pay = 1");
            $this->assign("waitpay_carry_count", $waitpay_carry_count);

            //普通用户
            $user_level = M("User")->where("is_authentication<>2 and is_effect=1 and is_robot = 0 and parent_id = {$user_admin_id}")->count();
            $this->assign("user_level", $user_level);
            //认证用户
            $authentication = M("User")->where("is_authentication=2 and is_effect=1 and is_robot = 0 and parent_id = {$user_admin_id}")->count();
            $this->assign("authentication", $authentication);
            //机器人
            $robot = M("User")->where("is_effect=1 and is_robot = 1 and parent_id = {$user_admin_id}")->count();
            $this->assign("robot", $robot);
            //会员总数
            $user_count = M("User")->where("is_effect=1 and parent_id = {$user_admin_id}")->count();
            $this->assign("user_count", $user_count);

            $is_live = M("Video")->where("live_in=1 or live_in = 3")->count(); //直播中
            $this->assign("is_live", $is_live);
            $is_playback = M("VideoHistory")->where("is_delete=0 and is_del_vod = 0 and room_type<>1")->count(); //保存的视频
            $this->assign("is_playback", intval($is_playback));
            //直播中视频观看人数
            $watch_number = intval($GLOBALS['db']->getOne("SELECT sum(watch_number) FROM " . DB_PREFIX . "video where live_in=1"));
            $this->assign("watch_number", $watch_number);
            //统计在线人数
            // $m_config = load_auto_cache("m_config");
            // require_once APP_ROOT_PATH . 'system/tim/TimApi.php';
            // $api = createTimAPI();
            // $show_online_user = 0;
            // if ($m_config['tim_identifier'] && !is_array($api)) {
            //     $ret = $api->group_get_group_member_info($m_config['on_line_group_id'], 0, 0);
            //     $online_user = isset($ret['MemberNum']) ? intval($ret['MemberNum']) : 0; //减去管理员本身
            //     $show_online_user = 1;
            //     $this->assign("online_user", $online_user);
            // }
            // $this->assign("show_online_user", $show_online_user);
            require APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
            require APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php';
            $video_viewer_obj = new UserRedisService();
            $this->assign("online_user", $video_viewer_obj->online_users_num());
        }





        //当前用户所有下级
        $list_data['user_admin_id'] = $_SESSION['user_admin_session']['id'];
        $list_data['display'] = "exe";
        $re = $this->index_user_data($list_data);
        foreach($re as $k => $v){
            if($v['user_iden_id'] == 0){
                $ids[] = $v['id'];
            }else{
                unset($re[$k]);
            }

        }
        $ids = implode(",",$ids);


        //今日充值

        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        //今日注册人数
        $count_reg_user = M("user")->where("create_time > '{$beginToday}' and create_time < '{$endToday}' and id in($ids) ")->count();
        $count_reg_user = empty($count_reg_user)?0:$count_reg_user;
        $this->assign("count_reg_user",$count_reg_user);

        //今日充值
        $pay_sum = M("payment_notice")->field("sum(money) as sum_money")->where("is_paid = 1 and pay_time > '{$beginToday}' and  < '$endToday' and id in($ids) ")->select();
        $pay_sum[0]['sum_money'] = empty($pay_sum[0]['sum_money'])?0:$pay_sum[0]['sum_money'];
        $this->assign("pay_sum",$pay_sum[0]['sum_money']);






        $this->display();
    }



    public function index_user_data($date)
    {

        $voList = M("user")->where("parent_id = ".$date['user_admin_id'])->order("id asc")->select();
        foreach ($voList as $k => $v) {
            //会员等级
            $user_iden = M("user_iden")->where("id = ".$v['user_iden_id'])->find();
            $voList[$k]['nick_name'] = ($v['nick_name']);
            $voList[$k]['head_image'] = get_spec_image($v['head_image']);
            $voList[$k]['ticket'] = $v['ticket'] + $v['no_ticket'];
            $voList[$k]['create_time'] =date("Y-m-d H:i:s",$v['create_time']);
            $voList[$k]['user_iden'] = $user_iden['name'];
            if($v['user_status'] == 0){
                $voList[$k]['user_status'] = "未通过";
            }else{
                $voList[$k]['user_status'] = "已通过";
            }
        }
        $arr = array();
        //执行user_data方法，递归查询当前会员下所有等级是指定等级的会员
        $voList = $this->user_data($voList,$date['user_iden_id'],$voList);
        $whole = "1=1 ";

        if($date['id'] != ''){
            $whole .= " and id = ".$date['id'];
        }
        if($date['mobile'] != ''){
            $whole .= " and mobile = '{$date['mobile']}'";
        }
        if($date['nick_name'] != ''){
            $whole .= " and nick_name = '{$date['nick_name']}'";
        }
        if($date['create_time_1'] != ''){
            $find_create_time_1 = strtotime($date['create_time_1']);
            $whole .= " and create_time > {$find_create_time_1}";
        }
        if($date['create_time_2'] != ''){
            $find_create_time_2 = strtotime($date['create_time_2']);
            $whole .= " and create_time < {$find_create_time_2}";
        }
        //全部符合要求的会员
        $whole_user = M("user")->where($whole)->select();
        foreach ($whole_user as $k=>$v){
            foreach ($voList as $vk=>$vv){
                if($v['id'] == $vv['id']){
                    $user_list_arr[] = $v;
                }
            }
        }
        foreach ($user_list_arr as $k=>$v){
            //会员等级
            $user_iden = M("user_iden")->where("id = ".$v['user_iden_id'])->find();
            $user_list_arr[$k]['nick_name'] = ($v['nick_name']);
            $user_list_arr[$k]['head_image'] = get_spec_image($v['head_image']);
            $user_list_arr[$k]['ticket'] = $v['ticket'] + $v['no_ticket'];
            $user_list_arr[$k]['create_time'] =date("Y-m-d H:i:s",$v['create_time']);
            $user_list_arr[$k]['user_iden'] = $user_iden['name'];
            if($v['user_status'] == 0){
                $user_list_arr[$k]['user_status'] = "未通过";
            }else{
                $user_list_arr[$k]['user_status'] = "已通过";
            }
        }
        $this->assign('list_data', $date);
        if($date['display'] == ""){
            //页数
            $page = empty($date['page'])?1:$date['page'];
            //每页条数
            $pagesize = 10;
            //总条数
            $count = count($voList);
            //偏移量，当前页-1乘以每页显示条数
            $start=($page-1)*$pagesize;
            //总页数
            $number_pages = ceil($count/$pagesize);
            $article = array_slice($user_list_arr,$start,$pagesize);
            $this->assign('number_pages', $number_pages);
            $this->assign('list', $article);
            $this->display();
        }else{
            return $user_list_arr;
        }
    }



    public function user_data($voList,$user_iden_id,$voList_user){
        //循环所有下级会员
        $voList = $voList;
        foreach ($voList as $k => $v){
            //查询下级会员的下级会员
            $user = M("user")->where("parent_id = ".$v['id'])->select();
            if($user){
                foreach ($user as $k1=>$v1){
                    //会员等级
                    $user_iden = M("user_iden")->where("id = ".$v1['user_iden_id'])->find();
                    $user[$k1]['nick_name'] = ($v1['nick_name']);
                    $user[$k1]['head_image'] = get_spec_image($v1['head_image']);
                    $user[$k1]['ticket'] = $v1['ticket'] + $v1['no_ticket'];
                    $user[$k1]['create_time'] =date("Y-m-d H:i:s",$v1['create_time']);
                    $user[$k1]['user_iden'] = $user_iden['name'];
                    if($v1['user_status'] == 0){
                        $user[$k1]['user_status'] = "未通过";
                    }else{
                        $user[$k1]['user_status'] = "已通过";
                    }


                    $v1['nick_name'] = ($v1['nick_name']);
                    $v1['head_image'] = get_spec_image($v1['head_image']);
                    $v1['ticket'] = $v1['ticket'] + $v1['no_ticket'];
                    $v1['create_time'] =date("Y-m-d H:i:s",$v1['create_time']);
                    $v1['user_iden'] = $user_iden['name'];
                    if($v1['user_status'] == 0){
                        $v1['user_status'] = "未通过";
                    }else{
                        $v1['user_status'] = "已通过";
                    }


                    $voList_user[] = $v1;
                }
                $voList_user = $this->user_data($user,$user_iden_id,$voList_user);

            }
        }



        foreach ($voList_user as $k2=>$v2){

            $voList_uses2[] = $v2;
        }



        return $voList_uses2;
    }




























    public function qrcode_gen($user_admin_id){

        $user = M("user")->where("id = {$user_admin_id}")->find();

        //生成二维码
        if($user['qrcode_pic'] == ""){
            $save_path = './madmin/public/qrcode/';  //图片存储的绝对路径
            $web_path = '/madmin/public/qrcode/';        //图片在网页上显示的路径
            $qr_data = isset($_GET['qr_data'])?$_GET['qr_data']:'http://'.$_SERVER['HTTP_HOST']."/index.php";       //二维码内容
            $qr_level = isset($_GET['qr_level'])?$_GET['qr_level']:'H';
            $qr_size = isset($_GET['qr_size'])?$_GET['qr_size']:'10';
            $save_prefix = isset($_GET['save_prefix'])?$_GET['save_prefix']:'EWM';
            if($filename =  $this->createQRcode($save_path,$qr_data,$qr_level,$qr_size,$save_prefix)){
                $pic = $web_path.$filename;
            }
            
            $data['qrcode_pic'] = $pic;
            M("user")->where("id = {$user_admin_id}")->save($data);
        }

    }


    public function index_password_edit(){

        $user_admin_session = $_SESSION['user_admin_session'];
        $user_admin_id = $user_admin_session['id'];

        $curr_password = $_POST['curr_password'];
        $new_password = $_POST['new_password'];
        $new_password_conf = $_POST['new_password_conf'];

        //用户信息
        $user = M("user")->where("id = {$user_admin_id}")->find();
        if($user['user_pwd'] != md5($curr_password)){
            $this->error("原密码错误");
        }
        if($new_password != $new_password_conf){
            $this->error("密码输入不一致");
        }

        $password = md5($new_password);
        $data['user_pwd'] = $password;
        $re = M("user")->where("id = {$user_admin_id}")->save($data);
        if($re){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }


    }



//推广二维码
    public function createQRcode($save_path,$qr_data,$qr_level='L',$qr_size=5,$save_prefix='qrcode'){

        $re = vendor("phpqrcode.phpqrcode");

        //include '/ThinkPHP/Library/Vendor/phpqrcode/phpqrcode.php';
        $qr=new \QRcode();
        if(!isset($save_path)) return '';
        //设置生成png图片的路径
        $PNG_TEMP_DIR = & $save_path;
        //导入二维码核心程序
        //检测并创建生成文件夹
        if (!file_exists($PNG_TEMP_DIR)){
            mkdir($PNG_TEMP_DIR);
        }
        $filename = $PNG_TEMP_DIR.'test.png';
        $errorCorrectionLevel = 'L';
        if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
            $errorCorrectionLevel = & $qr_level;
        }
        $matrixPointSize = 4;
        if (isset($qr_size)){
            $matrixPointSize = & min(max((int)$qr_size, 1), 10);
        }
        if (isset($qr_data)) {
            if (trim($qr_data) == ''){
                die('data cannot be empty!');
            }
            //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
            $filename = $PNG_TEMP_DIR.$save_prefix.md5($qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
            //开始生成
            $qr::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        } else {
            //默认生成
            $qr::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        }
        if(file_exists($PNG_TEMP_DIR.basename($filename)))
            return basename($filename);
        else
            return FALSE;
    }















    //底部
    public function footer()
    {
        $this->display();
    }

    //修改管理员密码
    public function change_password()
    {
        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $this->assign("adm_data", $adm_session);
        $this->display();
    }
    public function do_change_password()
    {
        $adm_id = intval($_REQUEST['adm_id']);
        if (!check_empty($_REQUEST['adm_password'])) {
            $this->error(L("ADM_PASSWORD_EMPTY_TIP"));
        }
        if (!check_empty($_REQUEST['adm_new_password'])) {
            $this->error(L("ADM_NEW_PASSWORD_EMPTY_TIP"));
        }
        if ($_REQUEST['adm_confirm_password'] != $_REQUEST['adm_new_password']) {
            $this->error(L("ADM_NEW_PASSWORD_NOT_MATCH_TIP"));
        }
        if (M("Admin")->where("id=" . $adm_id)->getField("adm_password") != md5($_REQUEST['adm_password'])) {
            $this->error(L("ADM_PASSWORD_ERROR"));
        }
        M("Admin")->where("id=" . $adm_id)->setField("adm_password", md5($_REQUEST['adm_new_password']));
        save_log(M("Admin")->where("id=" . $adm_id)->getField("adm_name") . L("CHANGE_SUCCESS"), 1);
        $this->success(L("CHANGE_SUCCESS"));

    }

    public function reset_sending()
    {
        $field = trim($_REQUEST['field']);
        if ($field == 'DEAL_MSG_LOCK' || $field == 'PROMOTE_MSG_LOCK' || $field == 'APNS_MSG_LOCK') {
            M("Conf")->where("name='" . $field . "'")->setField("value", '0');
            $this->success(L("RESET_SUCCESS"), 1);
        } else {
            $this->error(L("INVALID_OPERATION"), 1);
        }
    }

    /*
     * 网站数据统计
     */
    public function statistics()
    {

        //$user_count=M("User")->where("is_robot=0")->count();
        $user_count = M("User")->count();
        $no_effect = M("User")->where(" is_effect=0 or is_effect=2")->count(); //无效
        $is_effect = M("User")->where(" is_effect=1")->count(); //有效

        //认证
        $user_authentication = M("User")->where("is_authentication = 2 and user_type=0  and is_effect=1 and is_robot = 0")->count();
        $business_authentication = M("User")->where("is_authentication = 2 and user_type=1 and is_effect=1 and is_robot = 0")->count();
        $all_authentication = M("User")->where(" (user_type=0 or user_type=1) and is_authentication =2 and is_effect=1 and is_robot = 0")->count();

        //树苗订单
        $tree_order_count = M("QkTreeOrder")->count();
        $total_tree_order_money = M("QkTreeOrder")->sum('pay');
        $m_config = load_auto_cache('m_config');
        $diamonds_name = $m_config['diamonds_name'];
        //资金进出
        //线上充值
        $online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "payment_notice where is_paid = 1 and payment_id>0  "));
        $day_online_pay = floatval($GLOBALS['db']->getOne("SELECT sum(`money`) FROM " . DB_PREFIX . "payment_notice where is_paid = 1 and payment_id>0 AND create_time BETWEEN " . strtotime('00:00:00') . " AND " . strtotime('23:59:59') . ""));
        $this->assign("online_pay", $online_pay);
        $this->assign("day_online_pay", $day_online_pay);
        //总计
        $total_usre_money = $online_pay;
        $this->assign("total_usre_money", $total_usre_money);

        $this->assign("diamonds_name", $diamonds_name);
        $this->assign("tree_order_count", $tree_order_count);
        $this->assign("total_tree_order_money", $total_tree_order_money);
        $this->assign("user_count", $user_count);
        $this->assign("no_effect", $no_effect);
        $this->assign("is_effect", $is_effect);
        $this->assign("user_authentication", $user_authentication);
        $this->assign("business_authentication", $business_authentication);
        $this->assign("all_authentication", $all_authentication);
        $this->display();
    }

    public function main_weibo()
    {
        $adm_session = es_session::get(md5(conf("AUTH_KEY")));
        $this->assign("adm_session", $adm_session);
        $adm_id = intval($adm_session['adm_id']);
        $login_time = $GLOBALS['db']->getOne("SELECT login_time FROM " . DB_PREFIX . "admin where id = $adm_id ");
        $h = to_date($login_time, "H");
        $login_time = to_date($login_time);
        $this->assign("login_time", $login_time);
        if ($h < 12) {
            $greet = "上午好";
        } elseif ($h < 18) {
            $greet = "下午好";
        } else {
            $greet = "晚上好";
        }
        $this->assign("greet", $greet);

        $navs = require_once APP_ROOT_PATH . "system/admnav_cfg.php";

        $this->assign("navs", $navs);

        //待审核
        $register_count = M("User")->where("is_effect=0 and user_type=0 ")->count();
        $company_register_count = M("User")->where("is_effect=0  and user_type=1")->count();
        $this->assign("register_count", $register_count);
        $this->assign("company_register_count", $company_register_count);

        //认证
        $user_authentication = M("User")->where("is_authentication = 1 and is_effect=1 and user_type=0 and is_robot = 0 ")->count();
        $business_authentication = M("User")->where("is_authentication = 1 and is_effect=1 and user_type=1 ")->count();
        $all_authentication = M("User")->where(" (user_type=0 or user_type=1) and is_authentication =1 and is_effect=1 and is_robot = 0")->count();
        //认证未通过
        $authentication_not_allow = M("User")->where("is_authentication = 3 and is_effect=1 and user_type=0 and is_robot = 0 ")->count();
        $this->assign("user_authentica", $user_authentication);
        $this->assign("business_authentica", $business_authentication);
        $this->assign("all_authentica", $all_authentication);
        $this->assign("authentication_not_allow", $authentication_not_allow);

        //充值
        $pay_count = floatval($GLOBALS['db']->getOne("SELECT count(*) FROM " . DB_PREFIX . "payment_notice where  payment_id <>0"));
        $this->assign("pay_count", $pay_count);

        //普通用户
        $user_level = M("User")->where("is_authentication<>2 and is_effect=1 and is_robot = 0")->count();
        $this->assign("user_level", $user_level);
        //认证用户
        $authentication = M("User")->where("is_authentication=2 and is_effect=1 and is_robot = 0")->count();
        $this->assign("authentication", $authentication);

        //会员总数
        $user_count = M("User")->where("is_effect=1")->count();
        $this->assign("user_count", $user_count);

        $this->display();
    }
}
