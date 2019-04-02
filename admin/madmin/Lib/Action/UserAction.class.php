<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class UserAction extends AuthAction
{

    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/madmin/Lib/Action/UserCommonAction.class.php";
        require_once APP_ROOT_PATH . "/system/libs/user.php";
    }

    //添加管理
    public function adduser(){
        //查询小于当前会员的会员级别
        $user_iden = M("user_iden")->where("id < ".$_SESSION['user_admin_session']['user_iden_id'])->order("id asc")->select();
        $this->assign("user_iden",$user_iden);
        //dump($_SESSION['user_admin_session']['user_iden_id']);die;

        $this->assign("user_id",$_SESSION['user_admin_session']['id']);


        $this->display();
    }
    //保存添加管理
    public function adduser_save(){

        //昵称
        $nick_name = $_POST['nick_name'];
        //判断昵称不重复
        $is_nick_name = M("user")->where("nick_name = '{$nick_name}'")->find();
        if($is_nick_name){
            $this->error("昵称已经存在");
        }
        //手机号
        $mobile = $_POST['mobile'];
        //判断手机号存在
        $is_mobiel = M("user")->where("mobile = '{$mobile}'")->find();
        if($is_mobiel){
            $this->error("手机号已经存在");
        }
        $user_pwd = $_POST['user_pwd'];

        //推荐人id
        $parent_id = $_REQUEST['parent_id'];

        //判断推荐人是否存在
        $is_parent_id = M("user")->where("id = ".$parent_id)->find();
        //echo M()->getLastSql();die;
        if(!$is_parent_id){
            $this->error("推荐人不存在");
        }
        //根据推荐人获取等级
        if($is_parent_id['user_iden_id'] == 1){
            $user_iden_id = 0;
        }else{
            $user_iden_id = $is_parent_id['user_iden_id']-1;
        }


        //级别
        //$user_iden_id = $_POST['user_iden_id'];
        //查询id最大的用户
        $max_user_id = M("user")->field("id")->order("id desc")->find();

        //添加用户
        $data['id'] = $max_user_id['id']+1;
        $data['nick_name'] = $nick_name;
        $data['user_name_reg'] = $_POST['user_name_reg'];
        $data['mobile'] = $mobile;
        $data['user_pwd'] = md5($user_pwd);
        $data['parent_id'] = $parent_id;
        $data['user_iden_id'] = $user_iden_id;
        $data['create_time'] = time();
        $data['is_effect'] = 1;

        $user_id = $_SESSION['user_admin_session']['id'];
        $user = M("user")->where("id = ".$user_id)->find();


        //创建的会员等级是代理，那么创建的会员状态为未审核
        if($user_iden_id == 2 ){
            $data['user_status'] = 0;
        }else{
            $data['user_status'] = 1;
        }



        $re = M("user")->add($data);
        if($re){
            $this->success("保存成功");
        }else{
            $this->error("保存失败");
        }
    }


    public function index()
    {

        $common = new UserCommon();
        $data = $_REQUEST;


        $data['is_robot'] = 0;

        //要查询的会员等级
        $user_iden_id = $_REQUEST['user_iden_id'];

        $user_iden = M("user_iden")->where("id = {$user_iden_id}")->find();
        $this->assign("user_iden",$user_iden);


        //当前会员id
        $user_id = $_SESSION['user_admin_session']['id'];
        $data['user_iden_id'] = $user_iden_id;
        $data['user_admin_id'] = $user_id;
        $this->assign("user_iden_id",$user_iden_id);


        $common->index_user_data($data);
    }


    public function edit()
    {
        $common = new UserCommon();
        $data = $_REQUEST;

        $common->edit($data);
    }

    public function update()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->user_update($data);

    }


    //账户日志
    public function account_detail()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->account_detail($data);
    }

    //秀票贡献榜
    public function contribution_list()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->contribution_list($data);
    }

    //礼物日志
    public function prop()
    {
        $common = new UserCommon();
        $data = $_REQUEST;
        $common->prop($data);
    }

    //收礼物日志
    public function closed_prop()
    {
        $data = $_REQUEST;
        $now = get_gmtime();
        $user_id = intval($_REQUEST['id']);
        $user_info = M("User")->getById($user_id);
        $prop_list = M("prop")->where("is_effect <>0")->findAll();

        $where = "l.to_user_id=" . $user_id;
        $model = D("video_prop");
        //赠送时间

        $current_Year = date('Y');

        $current_YM = date('Ym');
        for ($i = 0; $i < 5; $i++) {
            $years[$i] = $current_Year - $i;
        }

        for ($i = 01; $i < 13; $i++) {
            $month[$i] = str_pad(0 + $i, 2, 0, STR_PAD_LEFT);
        }

        if ((!empty($data['years']) && strim($data['years']) != -1) && (!empty($data['month']) && strim($data['month'] != -1))) {
            $time = $data['years'] . '' . $data['month'];
        } else {
            $time = $current_YM;
        }
        if (strim($data['years']) != -1 && strim($data['month'] == -1)) {
            $this->error("请选择月份");
        }
        if (strim($data['years']) == -1 && strim($data['month'] != -1)) {
            $this->error("请选择年份");
        }

        //查询ID
        if (strim($data['from_user_id']) != '') {
            $parameter .= "l.from_user_id=" . intval($data['from_user_id']) . "&";
            $sql_w .= "l.from_user_id=" . intval($data['from_user_id']) . " and ";
        }
        //查询昵称
        if (trim($data['nick_name']) != '') {
            $parameter .= "u.nick_name like " . urlencode('%' . trim($data['nick_name']) . '%') . "&";
            $sql_w .= "u.nick_name like '%" . trim($data['nick_name']) . "%' and ";

        }
        if (!isset($_REQUEST['prop_id'])) {
            $_REQUEST['prop_id'] = -1;
        }
        //查询礼物
        if ($_REQUEST['prop_id'] != -1) {
            if (isset($data['prop_id'])) {
                $parameter .= "l.prop_id=" . intval($data['prop_id']) . "&";
                $sql_w .= "l.prop_id=" . intval($data['prop_id']) . " and ";
            }
        }

        //默认查询本月的记录,选择查询时间时,如果查询时间 不等于当前时间,则查询他表
        if ($data['years'] != '' && $data['month'] != '') {
            $sql_str = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,l.is_coin FROM   " . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "  and " . $sql_w . " 1=1  ";

            $count_sql = "SELECT count(l.id)  as tpcount FROM   " . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "  and " . $sql_w . " 1=1  ";

            $total_ticket_sql = "SELECT SUM(l.total_ticket)  as tpcount FROM   " . DB_PREFIX . "video_prop_" . $time . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "   and " . $sql_w . " 1=1  ";
        } else {

            $sql_str = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,l.is_coin FROM   " . DB_PREFIX . "video_prop_" . date('Ym', NOW_TIME) . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "   and " . $sql_w . " 1=1  ";

            $count_sql = "SELECT count(l.id)  as tpcount FROM   " . DB_PREFIX . "video_prop_" . date('Ym', NOW_TIME) . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "   and " . $sql_w . " 1=1  ";

            $total_ticket_sql = "SELECT SUM(l.total_ticket)  as tpcount FROM   " . DB_PREFIX . "video_prop_" . date('Ym', NOW_TIME) . " as l LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . " WHERE $where " . "   and " . $sql_w . " 1=1  ";
        }

        $table = DB_PREFIX . 'video_prop_' . $time;
        $result = $GLOBALS['db']->getRow("SHOW TABLES LIKE'$table'");
        if ($result) {
            $count = $GLOBALS['db']->getOne($count_sql);
            $total_ticket = $GLOBALS['db']->getOne($total_ticket_sql);
        } else {
            $count = 0;
            $total_ticket = 0;
        }
        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            if ($volist[$k]['prop_id'] == 12) {
                $volist[$k]['total_ticket'] = '';
            }
            $volist[$k]['create_time'] = date('Y-m-d H:i:s', $volist[$k]['create_time']);
        }

        $this->assign("user_info", $user_info);
        $this->assign("prop", $prop_list);
        $this->assign("years", $years);
        $this->assign("month", $month);
        $this->assign("list", $volist);
        $this->assign("count", intval($count));
        $this->assign('total_ticket', intval($total_ticket));
        $this->display();
    }

    public function _Sql_list($model, $sql_str, $parameter = '', $sortBy = '', $asc = false, $count_sql = false)
    {
        //排序字段 默认为主键名
        if (isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        } else {
            $order = $sortBy;
        }

        if ($sortBy == 'nosort') {
            unset($order);
        }

        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        //取得满足条件的记录数
        if ($count_sql) {
            $sql_tmp = $count_sql;
        } else {
            $sql_tmp = 'select count(*) as tpcount from (' . $sql_str . ') as a';
        }

        //dump($sql_tmp);
        $rs = $model->query($sql_tmp, false);

        $count = intval($rs[0]['tpcount']);
        //dump($count);
        if ($count > 0) {
            //创建分页对象
            if (!empty($_REQUEST['listRows'])) {
                $listRows = $_REQUEST['listRows'];
            } else {
                $listRows = '';
            }

            import("@.ORG.Page");
            $p = new Page($count, $listRows);
            //分页跳转的时候保证查询条件
            //dump($parameter);
            if ((!empty($parameter)) && (substr($parameter, 1, 1) != '&')) {
                //add by chenfq 2010-06-19 添加分页条件连接缺少 & 问题
                $parameter = '&' . $parameter;
            }
            $p->parameter = $parameter;

            //排序
            if (!empty($order)) {
                $sql_str .= ' ORDER BY ' . $order . ' ' . $sort;
            }

            //分页查询数据
            $sql_str .= ' LIMIT ' . $p->firstRow . ',' . $p->listRows;

            //dump($sql_str);
            $voList = $model->query($sql_str, false);
            //dump($voList);
            //分页显示
            $page = $p->show();
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? L('SORT_ASC') : L('SORT_DESC'); //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式

            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign('list', $voList);
            $this->assign("page", $page);
        }
        //Cookie::set ( '_currentUrl_', U($this->getActionName()."/index") );
        return $voList;
    }








    //导出
    public function export_excel(){
        $user_admin_session = $_SESSION["user_admin_session"];
        $user_admin_id = $user_admin_session['id'];

        $list_data['id'] = $_GET['id'];
        $list_data['mobile'] = $_GET['mobile'];
        $list_data['nick_name'] = $_GET['nick_name'];
        $list_data['create_time_1'] = $_GET['create_time_1'];
        $list_data['create_time_2'] = $_GET['create_time_2'];
        //查询的指定会员的等级
        $list_data['user_iden_id'] = $_GET['user_iden_id'];


        $common = new UserCommon();
        //当前会员id

        $list_data['user_admin_id'] = $_SESSION['user_admin_session']['id'];
        $list_data['display'] = "exe";
        $re = $common->index_user_data($list_data);


         set_time_limit(3600);
         vendor('PHPExcel.PHPExcel');
        //include 'madmin/ThinkPHP/Vendor/';
         //$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
         //PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
         /*$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
         $cacheSettings = array('memoryCacheSize' => '16MB');
         PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);*/
         $objPHPExcel = new PHPExcel();
         //$where = "1=1 and user_iden_id = {$user_iden_id} and parent_id = ".$user_admin_id;
         //$count = M('user')->where($where)->count();
         $count = count($re);

         $page = 1;
         $page_number = 1024;
         $count += $page_number;
         $arr = array();
         $is_drug = C(IS_DRUG);
         $begin = ($page -1) * $page_number;


         while($begin < $count){
             //$res_data = M('user')->field("id,nick_name,mobile,user_type,create_time,login_time,login_ip,parent_id,diamonds,use_diamonds,ticket,refund_ticket,score,user_level")->where($where)->limit($begin, $page_number)->select();
             $res_data = $re;

             foreach($res_data as $key=>$val){
                 //等级
                 $level = M("user_level")->where("id = ".$val['user_level'])->find();

                 //注册等级
                 $user_iden = M("user_iden")->where("id = {$val['user_iden_id']}")->find();

                 $res[$key]['id'] = $val['id'];
                 $res[$key]['nick_name'] = $val['nick_name'];
                 $res[$key]['mobile'] = $val['mobile'];

                 $res[$key]['user_type'] = $user_iden['name'];


                 $res[$key]['diamonds'] = $val['diamonds'];
                 $res[$key]['use_diamonds'] = $val['use_diamonds'];

                 $res[$key]['create_time'] = date("Y-m-d H:i:s",$val['create_time']);
                 $res[$key]['login_time'] = $val['login_time'];
                 $res[$key]['login_ip'] = $val['login_ip'];
                 $res[$key]['parent_id'] = $val['parent_id'];
                 $res[$key]['level'] = $level['name'];

                 $new_key = $begin + $key;
                 $arr[] = $res[$key];
                 //$arr[] = $val;
             }

             $page ++;
             $begin = $page * $page_number;
         }

         $letter = array('A','B','C','D','E','F','G','H','I','J','K');
         $title = array("主播ID", "主播昵称", "手机号", "用户类型", "可用钻石", "累计消费钻石", "注册时间", "	登陆时间","登陆ip","所属分销商","等级");
         $objPHPExcel->setActiveSheetIndex(0);
         $j =0;
         foreach($title as $t_val){
             $index = $letter[$j];
             $objPHPExcel->getActiveSheet()->setCellValue($index."1", $t_val);
             $j++;
         }
         $arr_count = count($arr);
         $i = 2;
         foreach($arr as $key=>$val){
             if($key < $arr_count){
                 $j =0;
                 foreach($val as $val2){
                     $index = $letter[$j];
                     $objPHPExcel->getActiveSheet()->setCellValue($index.$i, $val2);
                     $j++;
                 }
                 $i++;
             }
         }
        $file_name = "user";
         $objPHPExcel->getActiveSheet()->setTitle('USER');
         header('Content-Type:application/vnd.ms-excel');
         header('Content-Disposition:attachment;filename="'.$file_name.'.xls"');
         header('Cache-Control:max-age=0');
         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
         $objWriter->save('php://output');
         exit;
    }

    function remove_repeat($array, $stkey = false, $ndkey = true)
    {
        //任意string
        $joinstr = ',';
        //判断是否保留一级数组
        if ($stkey){
            $stArr = array_keys($array);
        }
        //判断是否保留二级数组
        if ($ndkey) {
            $ndArr = array_keys(end($array));
        }

        //降维,也可以用implode(),拼接成字符串，用逗号隔开
        foreach ($array as $v) {
            $v = join($joinstr, $v);
            $temp[] = $v;
        }

        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp);
        //再将拆开的数组重新组装成二维数组
        foreach ($temp as $k => $v) {
            if ($stkey) {
                $k = $stArr[$k];
            }
            if ($ndkey) {
                $tempArr = explode($joinstr, $v);
                foreach ($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
            } else{
                $output[$k] = explode($joinstr, $v);
            }
        }
        //返回去重后的二维数组
        return $output;
    }
}
