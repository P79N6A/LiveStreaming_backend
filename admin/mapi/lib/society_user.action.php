<?php
class society_userModule  extends baseModule
{
    protected static function getUserId(){
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }
        return $user_id;
    }

    //主播列表
    public function user_list(){
        $root = array(
            'status' => 1,
            'error'  => ''
        );
        $user_id = self::getUserId();

        //搜索的主播id
        $id = intval($_REQUEST['user_id']);

        $page['page'] = intval($_REQUEST['page']);
        $page['page'] = $page['page']  ? $page['page'] : 1;
        $page_size = intval($_REQUEST['page_size']);
        $page_size = $page_size ? $page_size : 10;

        $status = intval($_REQUEST['status']);//签约状态

        $society_id = intval($_REQUEST['society_id']);
        if ($society_id == 0){//公会长访问，可以查看不同状态的主播
            $society_id   = $GLOBALS['db']->getOne("select society_id from ".DB_PREFIX."user where id=".$user_id);
            if ($status == 1){
                $where = " sa.status=1 and sa.society_id=$society_id";
            }else{
                $where = "sa.status=$status and sa.society_id=$society_id";
            }
        }else{//公会成员访问，只允许查看公会成员
            $where = "sa.status=1  and sa.society_id=$society_id";
        }



        //公会成员总数
        $rs_count   = $GLOBALS['db']->getOne("select user_count from ".DB_PREFIX."society where id=".$society_id);
        //申请退出人数
        $quit_count = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."society_apply where status=3 and society_id=$society_id");
        if($quit_count>0){
            $rs_count = intval($rs_count-$quit_count)>=0?intval($rs_count-$quit_count):0;
        }

        if ($id > 0){
            $where .= " and sa.user_id=".$id;
        }
        $apply_count  = $GLOBALS['db']->getOne("select count(id) from ".DB_PREFIX."society_apply sa where sa.status=0 and sa.society_id=$society_id");

        $field = "u.id as user_id,u.nick_name,u.sex,u.v_type,u.v_icon,u.head_image,u.signature,u.user_level,u.society_chieftain,
                    u.society_settlement_type,sa.status,sa.deal_time";
        $start = ($page['page'] - 1) * $page_size;
        $end   = $page_size;
        $table = DB_PREFIX."user u";
        $left_join = DB_PREFIX."society_apply sa on u.id=sa.user_id";
        $sql  = "select $field from $table left join $left_join where $where limit $start,$end";
        $list = $GLOBALS['db']->getAll($sql);
        $sql_count  = "select $field from $table left join $left_join where $where";
        $list_count = $GLOBALS['db']->getAll($sql_count);
        if(is_array($list)){
            foreach($list as $k=>$v){
                $list[$k]['head_image'] = get_spec_image($v['head_image']);
                $list[$k]['deal_time']  = to_date($v['deal_time'],'Y-m-d H:i:s');
            }
        }else{
            $list = array();
        }

        //公会成员总数
        $count = count($list_count);
        $page['has_next'] = $count>=($page['page'])*$page_size ? 1 : 0;

        $root['page'] = $page;
        $root['rs_count'] = $rs_count;
        $root['apply_count'] = $apply_count;
        $root['quit_count'] = $quit_count;
        $root['list'] = $list;
        api_ajax_return($root);
    }

    //待签约主播列表
    public function signed_list(){
        $root = array(
            'status' => 0,
            'error' => ''
        );
        $user_id = self::getUserId();
        $sign_status = intval($_REQUEST['sign_status']);
        $root['sign_status'] = $sign_status;

        //搜索的主播id
        $id = intval($_REQUEST['user_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page  ? $page : 1;
        $page_size    = 10;

        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=$user_id");
        if ($society_info['society_id'] != 0 && $society_info['society_chieftain'] == 1){
            $field = "sa.society_id,sa.create_time,sa.deal_time as rescind_date,s.name,u.nick_name,u.id as user_id";
            $table = DB_PREFIX."society_apply sa";
            $where = "sa.status=$sign_status and sa.society_id=".$society_info['society_id'];
            if ($id > 0){
                $where .=" and sa.user_id=".$id;
            }
            $left_join1 = DB_PREFIX."society s on sa.society_id=s.id";
            $left_join2 = DB_PREFIX."user u on sa.user_id=u.id";
            $start = ($page - 1) * $page_size;
            $sql = "select $field from $table left join $left_join1 left join $left_join2 where $where limit $start,$page_size";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list){
                foreach ($list as $k=>$v){
                    $list[$k]['rescind_date'] = to_date($v['rescind_date']);
                    $list[$k]['create_time'] = to_date($v['create_time']);
                }
            }
            $root['status'] = 1;
            $root['list'] = $list;
        }else{
            $root['error'] = "您没有权限";
        }
        api_ajax_return($root);
    }

    //解约主播列表
    public function rescind_list(){
        $root = array(
            'status' => 0,
            'error'  => ''
        );

        $user_id = self::getUserId();

        //搜索的主播id1
        $id = intval($_REQUEST['user_id']);
        $sign_status = intval($_REQUEST['sign_status']);
        if($sign_status==0){
            $sign_status=3;
        }
        $root['sign_status'] = $sign_status;

        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page  ? $page : 1;
        $page_size    = 10;

        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);
        if ($society_info['society_id'] != 0 && $society_info['society_chieftain'] == 1){
            $field = "sa.society_id,sa.create_time,sa.deal_time as rescind_date,s.name,u.nick_name,u.id as user_id";
            $table = DB_PREFIX."society_apply sa";
            $where = "sa.status=$sign_status and sa.society_id=".$society_info['society_id'];
            if ($id > 0){
                $where .=" and sa.user_id=".$id;
            }
            $left_join1 = DB_PREFIX."society s on sa.society_id=sa.id";
            $left_join2 = DB_PREFIX."user u on sa.user_id=u.id";
            $start = ($page - 1) * $page_size;
            $sql = "select $field from $table left join $left_join1 left join $left_join2 where $where limit $start,$page_size";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list){
                foreach ($list as $k=>$v){
                    $list[$k]['create_time'] = to_date($v['create_time']);
                    $list[$k]['rescind_date']   = to_date($v['rescind_date']);
                }
            }
            $root['status'] = 1;
            $root['list'] = $list;
        }else{
            $root['error'] = "您没有权限";
        }

        api_ajax_return($root);
    }

    //申请加入
    public function join(){
        $user_id = self::getUserId();//
        $root = array(
            'status' => 0,
            'error' => ''
        );

        $society_id = intval($_REQUEST['society_id']);
        if ($society_id == 0){
            $root['error'] = "公会id不能为空";
            api_ajax_return($root);
        }

        $society_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where id=$society_id");
        if (!$society_exist){
            $root['error'] = "申请的公会不存在";
            api_ajax_return($root);
        }

        $apply_info = $GLOBALS['db']->getRow("select id,society_id from ".DB_PREFIX."society_apply where status=0  and user_id=$user_id");
        if (intval($apply_info['society_id'])!=$society_id&&intval($apply_info['society_id'])>0){
            $root['error'] = '您已经申请过其他公会，请等待其他公会审核';
            api_ajax_return($root);
        }else if (intval($apply_info['society_id'])==$society_id&&intval($apply_info['society_id'])>0){
            $root['error'] = '您已经申请过，请等待审核';
            api_ajax_return($root);
        }
        $apply_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society_apply where status=1 and user_id=$user_id");
        if ($apply_exist){
            $root['error'] = '您已加入公会';
            api_ajax_return($root);
        }


        $data['society_id']  = $society_id;
        $data['user_id']     = $user_id;
        $data['create_time'] = NOW_TIME;
        $data['status']      = 0;

        $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$data,"INSERT");
        if($res){
            $root['status'] = 1;
            $root['error'] = '加入申请已提交';
            $root['society_id'] = $society_id;
            api_ajax_return($root);
        }else{
            $root['error'] = '申请失败';
            api_ajax_return($root);
        }
     }

    //申请退出
    public function logout(){
        $user_id = self::getUserId();
        $root = array(
            'status' => 0,
            'error' => ''
        );

        $society_id = $GLOBALS['db']->getOne("select society_id from ".DB_PREFIX."user where id=$user_id");
        $apply_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society_apply where status=3 and society_id=$society_id and user_id=$user_id");
        if ($apply_exist){
            $root['error'] = '您已经申请过，请等待审核';
            api_ajax_return($root);
        }

        $data['status'] = 3;
//        $data['create_time'] = NOW_TIME;
        $where = "society_id=$society_id and user_id=$user_id and status=1";

        $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$data,"UPDATE",$where);
        if($res){
            $root['status'] = 1;
            $root['error'] = '退出申请已提交';
            $root['society_id'] = $society_id;
            api_ajax_return($root);
        }else{
            $root['error'] = '申请失败';
            api_ajax_return($root);
        }
    }

    //认证初始化
    public function authent(){
        $user_id = self::getUserId();
        $root = array(
            'status' => 0,
            'error' => ''
        );
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $root['status'] = 1;
            $root['error'] = "";

            $user_sql = "select id as user_id,authentication_name,identify_number,contact,identify_positive_image,identify_nagative_image,identify_hold_image,opus_site,opus_explain,is_authentication,remark  from ".DB_PREFIX."user where is_effect =1 and id=".$user_id;

            $user = $GLOBALS['db']->getRow($user_sql,true,true);

            $user['identify_positive_image'] = $user['identify_positive_image'];
            $user['identify_nagative_image'] = $user['identify_nagative_image'];
            $user['identify_hold_image'] = $user['identify_hold_image'];
            $user['opus_explain']=unserialize($user['opus_explain']);

            $user['identify_number'] = !empty($user['identify_number'])?$user['identify_number']:'';

            $root['user'] = $user;
        }
        api_ajax_return($root);
    }

    //主播申请认证
    public function attestation(){
        $user_id = self::getUserId();
        $root = array(
            'status' => 0,
            'error' => ''
        );
        fanwe_require(APP_ROOT_PATH.'system/libs/user.php');
        $authentication_name = strim($_REQUEST['authentication_name']) ;//真实姓名
        $identify_number = strim($_REQUEST['identify_number']) ;//身份证号码
        $contact = strim($_REQUEST['contact']);//联系方式
        $identify_positive_image=strim($_REQUEST['identify_positive_image']);//身份证正面
        $identify_nagative_image=strim($_REQUEST['identify_nagative_image']);//身份证反面
        $identify_hold_image=strim($_REQUEST['identify_hold_image']);//手持身份证正面
        $opus_site = strim($_REQUEST['opus_site']); // 作品地址
        $opus_explain = serialize(strim($_REQUEST['opus_explain '])); // 作品说明
        $remark  =strim($_REQUEST['remark']); // 备注说明
        $show_bill  = strim($_REQUEST['show_bill']); //直播间海报
        $society_id = intval($_REQUEST['society_id']);
        if ($society_id == 0){
            $root['error'] = "公会id不能为空";
            api_ajax_return($root);
        }
        $apply_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society_apply where status=0  and user_id=$user_id");
        if ($apply_exist){
            $root['error'] = '您已经申请过，请等待审核';
            api_ajax_return($root);
        }
        $society_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where id=$society_id");
        if (!$society_exist){
            $root['error'] = "申请的公会不存在";
            api_ajax_return($root);
        }
        $user = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
        $society_status = $GLOBALS['db']->getOne("SELECT status FROM " . DB_PREFIX . "family WHERE id =" . $user['society_id'] . " and user_id=" . $user_id);
        if ($user['society_id'] > 0 && $society_status != 2){
            if ($user['society_chieftain'] == 1){//用户是公会长
                if ($society_status == 1){
                    $root['error'] = '您已有创建成功的公会';
                }
                if ($society_status == 0) {
                    $root['error'] = '您已创建的公会正在审核';
                }
            }else{//用户是公会成员
                $root['error'] = '您已加入公会，请退出后再申请';
            }
            $root['status'] = 0;
        }

        //=============================

        if($authentication_name==''){
            $root['status'] = 0;
            $root['error'] = '请填写真实姓名！';
            api_ajax_return($root);
        }
        if($identify_number==''){
            $root['status'] = 0;
            $root['error'] = '请填写身份证号码！';
            api_ajax_return($root);
        }
        if($contact==''){
            $root['status'] = 0;
            $root['error'] = '请填写联系方式！';
            api_ajax_return($root);
        }
//        if($identify_positive_image==''){
//            $root['status'] = 0;
//            $root['error'] = '请上传身份证正面照片！';
//            api_ajax_return($root);
//        }
//        if($identify_nagative_image==''){
//            $root['status'] = 0;
//            $root['error'] = '请上传身份证背面照片！';
//            api_ajax_return($root);
//        }
//        if($identify_hold_image==''){
//            $root['status'] = 0;
//            $root['error'] = '请上传手持身份证正面！';
//            api_ajax_return($root);
//        }
//          if($show_bill==''){
//            $root['status'] = 0;
//            $root['error'] = '请上传直播间海报！';
//            api_ajax_return($root);
//        }


        if($opus_site==''){
            $root['status'] = 0;
            $root['error'] = '作品地址不能为空';
            api_ajax_return($root);
        }
        if($opus_explain==''){
            $root['status'] = 0;
            $root['error'] = '作品说明不能为空';
            api_ajax_return($root);
        }
        if($remark ==''){
            $root['status'] = 0;
            $root['error'] = '备注说明不能为空';
            api_ajax_return($root);
        }





        //判断该实名是否存在
        $user_info=$GLOBALS['db']->getRow("select id from  ".DB_PREFIX."user where id=".$user_id);
//        if($user_info['is_authentication']==2){
//            $root['status'] = 0;
//            $root['error'] = '您以通过认证,无需再申请';
//            api_ajax_return($root);
//        }
        if($user_info){
            $user_info['is_authentication'] = 1;//认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
            $user_info['authentication_type'] = '公会红人申请';//认证名称
            $user_info['authentication_name'] =  $authentication_name;//真实姓名
            $user_info['identify_number'] =  $identify_number;//身份证号码
            $user_info['contact'] = $contact;//联系方式
            $user_info['identify_hold_image']=get_spec_image($identify_hold_image);//手持身份证正面
            $user_info['identify_positive_image']=get_spec_image($identify_positive_image);//身份证正面
            $user_info['identify_nagative_image']=get_spec_image($identify_nagative_image);//身份证反面
            $user_info['show_bill']=get_spec_image($show_bill);//直播间海报
            $user_info['opus_site']=$opus_site;//作品地址
            $user_info['opus_explain']=$opus_explain;//作品说明
            $user_info['remark']=$remark ;//备注说明

            $res = save_user($user_info,"UPDATE");

            $data['society_id']  = $society_id;
            $data['user_id']     = $user_id;
            $data['create_time'] = NOW_TIME;
            $data['status']      = 0;
            $society_res = $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$data,"INSERT");

            if($res['status']==1&&$society_res){
                //更新session
                $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$res['data']);
                es_session::set("user_info", $user_info);

                $root['status'] = 1;
                $root['error'] = '已提交,等待审核';
                $root['society_id'] = $society_id;
            }else{
                $root['status'] = 0;
                $root['error'] = $res['error'];
            }
        }else{
            $root['status'] = 0;
            $root['error'] = '会员信息不存在';
        }

        api_ajax_return($root);
    }
}

?>