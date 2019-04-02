<?php
class societyModule  extends baseModule
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

    //公会详情
    public function index(){
        $root = array(
            'status'=> 1,
            'error' => ''
        );
        $id = intval($_REQUEST['society_id']);
        if ($id == 0){
            $user_id = self::getUserId();
            $id = $GLOBALS['db']->getOne("select society_id from ".DB_PREFIX."user where id=$user_id");
        }
        if(intval($id)==0){
            $root['status'] = 3;
            $root['error']  = "你的公会不存在或者已解散";
        }
        $sql = "select s.*,s.id as society_id,u.nick_name from ".DB_PREFIX."society s left join ".DB_PREFIX."user u on s.user_id=u.id where s.id=".$id ;
        $society_info = $GLOBALS['db']->getRow($sql);
        if ($society_info){
            $society_info['logo'] = get_spec_image($society_info['logo']);
            switch ($society_info['status']){
                case 0:
                    $root['error'] = '您的公会正在审核';
                    $root['status'] = 0;
                    $root['society_info'] = $society_info;
                    break;
                case 1:
                    $society_info['create_time'] = to_date($society_info['create_time'],'Y-m-d H:i:s');
                    $root['society_info'] = $society_info;
                    $root['status'] = 1;
                    break;
                case 2:
                    $root['error'] = '您的公会审核未通过';
                    $root['status'] = 2;
                    $root['society_info'] = $society_info;
                    break;
                default:
                    $root['error'] = '您的公会状态异常';
                    $root['status'] = 0;
                    break;
            }
        }else{
            $root['status'] = 3;
            $root['error']  = "你的公会不存在或者已解散";
        }

        api_ajax_return($root);
    }

    //公会列表
    public function society_list(){
        $user_id = self::getUserId();//

        $society_id   = intval($_REQUEST['society_id']);
        $society_name = trim($_REQUEST['society_name']);

        $page['page'] = intval($_REQUEST['page']);
        $page_size    = intval($_REQUEST['page_size']);
        $page['page'] = $page['page']  ? $page['page']  : 1;
        $page_size    = $page_size ? $page_size : 20;

        $field = "s.id as society_id,s.logo,s.name,s.user_id,s.create_time,s.user_count,u.nick_name";
        $is_apply = ",IF((select count(id) as is_apply from " . DB_PREFIX . "society_apply as sa where sa.user_id=" . $user_id . " and sa.society_id=s.id and sa.status=0 )>0,1,
                         IF ((select count(id) as is_apply from " . DB_PREFIX . "society_apply as sa where sa.user_id=" . $user_id . " and sa.society_id=s.id and sa.status=1 )>0,2,0)) as is_apply";
        $table = DB_PREFIX."society s";
        $left_join = DB_PREFIX."user u on s.user_id=u.id";
        //根据输入的参数决定where语句
        if ($society_id != 0 && $society_name != ''){
            $where = "s.status=1 and (s.id=$society_id or s.name like '%".$society_name."%')";
        }elseif ($society_id != 0 and $society_name == ''){
            $where = "s.status=1 and s,id=$society_id";
        }elseif ($society_id == 0 and $society_name != ''){
            $where = "s.status=1 and s.name like '%".$society_name."%'";
        }else{
            $where = "s.status=1";
        }
        $rs_count = $GLOBALS['db']->getOne("select count(s.id) from $table where $where");
        $list = array();
        if($rs_count){
            $start = ($page['page'] - 1) * $page_size;
            $end   = $page_size;
            $sql   = "select $field".$is_apply." from $table left join $left_join where $where limit $start,$end";
            $list  = $GLOBALS['db']->getAll($sql);
            foreach ($list as $k=>$v){
                $list[$k]['logo'] = get_spec_image($v['logo']);
                $list[$k]['name'] = htmlspecialchars_decode($v['name']);
                $list[$k]['nick_name'] = htmlspecialchars_decode($v['nick_name']);
            }
        }
        $page['has_next'] = ($rs_count > $page['page']*$page_size) ? 1 : 0;
        $error  = '';
        $status = 1;
        api_ajax_return(compact('error','status','rs_count','list','page'));
    }

    //创建公会
    public function create(){
        $user_id = self::getUserId();
        $root    = array(
            'status' => 0,
            'error'  => ''
        );

        $data              = array();
        $data['logo']      = trim($_REQUEST['logo']);
        $data['name']      = trim($_REQUEST['name']);
        $data['name']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['name']);
        $data['manifesto'] = trim($_REQUEST['manifesto']);
        $data['manifesto']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['manifesto']);
        $data['notice']    = trim($_REQUEST['notice']);
        $data['notice']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['notice']);//过滤表情
        $data['status'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['memo'] = '无';
        $data['user_count'] = 1;
        $data['user_id'] = $user_id;

        if ($data['logo'] == ''){
            $root['error'] = '公会logo不能为空';
            api_ajax_return($root);
        }
        if ($data['name'] == ''){
            $root['error'] = '公会名称不能为空';
            api_ajax_return($root);
        }
        if ($data['manifesto'] == ''){
            $root['error'] = '公会宣言不能为空';
            api_ajax_return($root);
        }

        if(strlen($data['name']) > 48){
            api_ajax_return(array(
                'status' => '0',
                'error' => '公会名称限制15字以内'
            ));
        }
        $user = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
        $society_status = $GLOBALS['db']->getOne("SELECT status FROM " . DB_PREFIX . "society WHERE id =" . $user['society_id'] . " and user_id=" . $user_id);
        if ($user['society_id'] > 0 && $society_status != 2){
            if ($user['society_chieftain'] == 1){//用户是公会长
                if ($society_status == 1){
                    $root['error'] = '您已有创建成功的公会';
                }
                if ($society_status == 0) {
                    $root['error'] = '您已创建的公会正在审核';
                }
            }else{//用户是公会成员
                $root['error'] = '您已加入公会，请退出后再创建';
            }
            $root['status'] = 0;
        }else{
            $name_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where name='".$data['name']."' and (status=0 or status=1)");
            if ($name_exist > 0){
                $root['error'] = '公会名已存在';
            }else{
                $m_config = load_auto_cache('m_config');
                $data['refund_rate'] = $m_config['society_public_rate'];
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society",$data);
                if ($res){
                    $society_id = $GLOBALS['db']->insert_id();
                    $user_data = array();
                    if ($society_id){
                        $user_data['society_id'] = $society_id;
                        $user_data['society_chieftain'] = 1;
                    }
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $user_data, $mode = 'UPDATE', "id=" . $data['user_id']);
                    //更新redis
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_redis->update_db($data['user_id'], array('society_id' => $society_id, 'society_chieftain' => 1));
                    //更新申请表
                    $apple_count=$GLOBALS['db']->getOne("SELECT COUNT(id) FROM ".DB_PREFIX ."society_apply WHERE status=0 and  user_id = ".$user_id);
                    if($apple_count>0){
                        $join['status']=2;
                        $GLOBALS['db']->autoExecute(DB_PREFIX . "society_apply", $join, $mode = 'UPDATE', "user_id=" . $user_id);
                    }
                    //将会长信息写入公会申请表
                    $society_apply['user_id']     = $user_id;
                    $society_apply['society_id']  = $society_id;
                    $society_apply['create_time'] = NOW_TIME;
                    $society_apply['status']      = 1;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$society_apply,$mode = 'INSERT',"user_id=" . $user_id);
                    $root['society_id'] = $society_id;
                    $root['status']     = 1;
                    $root['error']      = '公会创建成功';

                }else{
                    $root['status'] = 0;
                    $root['error']  = '公会创建失败';
                }
            }
        }

        api_ajax_return($root);
    }

    //保存公会信息
    public function save(){
        $user_id = self::getUserId();
        $root    = array(
            'status' => 0,
            'error'  => ''
        );

        $data = array();
        $data['id'] = intval($_REQUEST['id']);
        if ($data['id'] == 0){
            $root['error'] = '公会ID不能为空';
            api_ajax_return($root);
        }

        //数据处理
        if (!empty($_REQUEST['logo'])){
            $data['logo'] = $_REQUEST['logo'];
        }
        if (!empty($_REQUEST['name'])){
            $data['name'] = trim($_REQUEST['name']);
            $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['name']);
            if(strlen($data['name']) > 48){
                api_ajax_return(array(
                    'status' => '0',
                    'error' => '公会名称限制15字以内'
                ));
            }
        }
        if (!empty($_REQUEST['manifesto'])){
            $data['manifesto'] = trim($_REQUEST['manifesto']);
            $data['manifesto'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['manifesto']);
        }
        $data['notice'] = trim($_REQUEST['notice']);
        $data['notice'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['notice']);

        //权限和异常判断
        $user = $GLOBALS['db']->getRow("select user_id from ".DB_PREFIX."society where id=".$data['id']);
        if ($user_id != $user['user_id']){
            $root['error'] = "您没有权限";
            api_ajax_return($root);
        }else{
            $is_refuse = $GLOBALS['db']->getOne("SELECT id FROM " . DB_PREFIX . "society WHERE user_id = " . $user_id . " and status=2 ");
            if ($is_refuse > 0) {
                if (!empty($_REQUEST['name'])){
                    $data['name'] = trim($_REQUEST['name']);
                    $data['name'] = preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['name']);
                    if(strlen($data['name']) > 48){
                        api_ajax_return(array(
                            'status' => '0',
                            'error' => '公会名称限制15字以内'
                        ));
                    }
                }

                $name_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where name='".$data['name']."' and (status=0 or status=1)");
                if ($name_exist > 0){
                    $root['error'] = '公会名已存在';
                    api_ajax_return($root);
                } else {
                    $data['status'] = 0;
                    $update = $GLOBALS['db']->autoExecute(DB_PREFIX . "society", $data, $mode = 'UPDATE', 'id=' . $is_refuse); //如果是被拒绝状态重新编辑更新
                    if ($update) {
                        $root['error'] = '公会信息修改成功';
                        $root['status'] = 1;
                        $root['society_id'] = $is_refuse;
                        api_ajax_return($root);
                    } else {
                        $root['error'] = '公会信息修改失败';
                        $root['status'] = 0;
                        api_ajax_return($root);
                    }
                }
            }else{


                $exist_name = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where status=1 and name='%".$data['name']."%'");
                if ($exist_name >0 && $exist_name != $data['id']){
                    $root['error'] = '公会名已存在';
                    api_ajax_return($root);
                }
                //更新数据
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society",$data,'UPDATE','id='.$data['id']);
                if ($res){
                    $root['status'] = 1;
                    $root['error']  = '公会信息修改成功';
                    $root['society_id'] = $data['id'];
                    api_ajax_return($root);
                }else{
                    $root['status'] = 0;
                    $root['error']  = '公会信息修改失败';
                    api_ajax_return($root);
                }
            }
        }
    }

    //会员退出审核
    public function logout_confirm(){
        $user_id = self::getUserId();

        $r_user_id = intval($_REQUEST['r_user_id']);
        $is_agree  = intval($_REQUEST['is_agree']);//是否同意 （1：同意，2：拒绝）

        $root = array(
            'status' => 0,
            'error'  => '',
            'r_user_id' => $r_user_id
         );

        $time = NOW_TIME;

        $society_id   = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where status=1 and user_id=".$user_id);
        if (!$society_id ){
            $root['error'] = '您不是公会长';
            api_ajax_return($root);
        }
        $sql = "select id from ".DB_PREFIX."society_apply where status=3 and user_id=".$r_user_id." and society_id=".$society_id;
        $apply_id   = $GLOBALS['db']->getOne($sql);
        if (!$apply_id){
            $root['error'] = '您没有权限';
            api_ajax_return($root);
        }

        if ($is_agree == 1){
            $update_user = "UPDATE ".DB_PREFIX."user set society_id=0,society_chieftain=0,society_settlement_type=0 where id=".$r_user_id;
            $update_society = "UPDATE ".DB_PREFIX."society set user_count=user_count-1 where id=".$society_id;
            $update_society_apply = "UPDATE ".DB_PREFIX."society_apply set status=4,deal_time=$time where id=$apply_id";
           // $update_society_apply = "delete from ".DB_PREFIX."society_apply where id=$apply_id";

            $res_user = $GLOBALS['db']->query($update_user);
            $res_society = $GLOBALS['db']->query($update_society);
            $res_society_apply = $GLOBALS['db']->query($update_society_apply);

            if ($res_user && $res_society && $res_society_apply){
                $root['status'] = 1;
                $root['error']  = "申请已审核";

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($r_user_id, array('society_id' => 0));
            }
        }elseif ($is_agree == 2){//拒绝入会
            //更改审核状态
            $society_apply['status'] = 1;
            $society_apply['deal_time'] = $time;
            $where = "id=".$apply_id;
            $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$society_apply,'UPDATE',$where);
            if ($res){
                $root['status'] = 1;
                $root['error']  = "申请已审核";
            }

        }

        api_ajax_return($root);
    }

    //会员入会审核
    public function confirm(){
        $user_id = self::getUserId();

        $r_user_id = intval($_REQUEST['r_user_id']);
        $is_agree  = intval($_REQUEST['is_agree']);//是否同意 （1：同意，2：拒绝）

        $root = array(
            'status' => 0,
            'error'  => '',
            'r_user_id' => $r_user_id
        );

        $user_society = $GLOBALS['db']->getOne("select society_id from ".DB_PREFIX."user where id=".$r_user_id);
        if ($user_society['society_id']){
            $root['error'] = '用户已有公会';
            api_ajax_return($root);
        }
        $society_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where status=1 and  user_id=".$user_id);
        if (!$society_id){
            $root['error'] = '您不是公会长';
            api_ajax_return($root);
        }
        $apply_id   = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society_apply where status=0 and user_id=$r_user_id and society_id=$society_id");
        if (!$apply_id){//用户不是会长或不是申请用户的会长
            $root['error'] = '您没有权限';
            api_ajax_return($root);
        }

        $time = NOW_TIME;
        if ($is_agree == 1){
            $update_user = "UPDATE ".DB_PREFIX."user set society_id=$society_id,society_chieftain=0,society_settlement_type=0 where id=".$r_user_id;
            $update_society = "UPDATE ".DB_PREFIX."society set user_count=user_count+1 where id=".$society_id;
            $update_society_apply = "UPDATE ".DB_PREFIX."society_apply set status=1,deal_time=$time where id=$apply_id";

            $res_user = $GLOBALS['db']->query($update_user);
            $res_society = $GLOBALS['db']->query($update_society);
            $res_society_apply = $GLOBALS['db']->query($update_society_apply);

            if ($res_user && $res_society && $res_society_apply){
                $update_others = "UPDATE ".DB_PREFIX."society_apply set status=2,deal_time=$time where user_id=$r_user_id and status=0";
                $GLOBALS['db']->query($update_others);

                $root['status'] = 1;
                $root['error']  = "申请已审核";

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($r_user_id, array('society_id' => $society_id));
            }
        }elseif ($is_agree == 2){
            $society_apply['status'] = 2;
            $society_apply['deal_time'] = NOW_TIME;
            $where = "id=$apply_id";
            $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$society_apply,'UPDATE',$where);
            if ($res){
                $root['status'] = 1;
                $root['error']  = "申请已处理";
            }
        }
        api_ajax_return($root);
    }

    //移除公会成员
    public function user_del(){
        $user_id = self::getUserId();//101237

        $r_user_id = intval($_REQUEST['r_user_id']);
        $root = array(
            'status' => 0,
            'error'  => '',
            'r_user_id' => $r_user_id
        );

        //取得公会长信息
        $sql = "select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id. " and society_chieftain = 1";
        $chieftain_info = $GLOBALS['db']->getRow($sql);

        //取被删除会员的的公会信息
        $sql ="select society_id,society_ticket from ".DB_PREFIX."user where id=".$r_user_id ;
        $r_user_info = $GLOBALS['db']->getRow($sql);
        $society_id =intval($r_user_info['society_id']);
        $society_ticket =intval($r_user_info['society_ticket']);

       if (intval($society_id)!= $chieftain_info['society_id']){
            $root['error'] = '会员不存在或不属于该公会';
           api_ajax_return($root);
        }

        if (intval($chieftain_info['society_id']) == 0){
            $root['error'] = '您没有权限';
            api_ajax_return($root);
        }
        if($society_ticket>0){
            $root['error'] = '成员公会贡献不为空，无法踢出公会，先处理成员贡献！';
            api_ajax_return($root);
        }

        $society_apply_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society_apply where user_id=".$r_user_id." and society_id=".$society_id." and status=1");
        log_file($society_apply_id,'user_del');
        $time = NOW_TIME;
        //更新用户公会号
        $update_user = "UPDATE ".DB_PREFIX."user set society_id=0,society_chieftain=0,society_settlement_type=0 where id=".$r_user_id;
        //更新公会人数
        $update_society = "UPDATE ".DB_PREFIX."society set user_count=user_count-1 where id=".$society_id;
        //删除申请记录
        $update_society_apply = "delete from ".DB_PREFIX."society_apply where id=".$society_apply_id;

        $res_user          = $GLOBALS['db']->query($update_user);
        $res_society       = $GLOBALS['db']->query($update_society);
        $res_society_apply = $GLOBALS['db']->query($update_society_apply);

        if ($res_user && $res_society && $res_society_apply) {
            $root['status'] = 1;
            $root['error'] = "公会成员已移除";

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_redis->update_db($r_user_id, array('society_id' => 0,'society_chieftain' => 0));

        }else{
            $root['error'] = "移除失败";
        }
        api_ajax_return($root);
    }

    //主播违规记录
    public function tipoff_index(){
        $root = array(
            'status'=>0,
            'error'=>'',
        );

        $id = intval($_REQUEST['user_id']);

        fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
        $page = intval($_REQUEST['p']);
        $page = $page  ? $page : 1;
        $page_size    = 10;

        $user_id = self::getUserId();
        $society_info = $GLOBALS['db']->getRow("select society_id,society_chieftain from ".DB_PREFIX."user where id=".$user_id);
        if ($society_info['society_id'] > 0 && $society_info['society_chieftain'] == 1){
            $field = "t.id,u.nick_name,t.to_user_id as user_id,t.create_time,tt.name as tipoff_type,u.head_image";
            $table = DB_PREFIX."tipoff t";
            $left_join1 = DB_PREFIX."user u on t.to_user_id=u.id";
            $left_join2 = DB_PREFIX."tipoff_type tt on t.tipoff_type_id=tt.id";
            $where = "u.society_id=".$society_info['society_id'];
            if ($id){
                $where .= " and u.id=$id";
            }
            $start = ($page - 1)*$page_size;
            $sql = "select $field from $table left join $left_join1 left join $left_join2 where $where order by t.create_time desc limit $start,$page_size";
            $list = $GLOBALS['db']->getAll($sql);
            if ($list){
                $rs_count = $GLOBALS['db']->getOne("select count(t.id) from $table left join $left_join1 where $where");
                foreach ($list as $k=>$v){
                    $list[$k]['create_time'] = to_date($v['create_time']);
                }
                $page = new Page($rs_count,$page_size);
                $root['page'] = $page->show();
                $root['rs_count'] = $rs_count;
                $root['list']   = $list;
                $root['status'] = 1;
            }
        }

        api_ajax_return($root);
    }

    //申请公会
    public function apply_sociaty(){

        api_ajax_return();
    }

    public function apply_sociaty_save(){
        $user_id = self::getUserId();
        $root    = array(
            'status' => 0,
            'error'  => ''
        );
        $data              = array();

        $data['name']      = trim($_REQUEST['name']);
        $data['name']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['name']);
        $data['manifesto'] = trim($_REQUEST['manifesto']);
        $data['manifesto']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['manifesto']);
        $data['notice']    = trim($_REQUEST['notice']);
        $data['notice']=preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '';},  $data['notice']);//过滤表情
        $data['status'] = 0;
        $data['create_time'] = NOW_TIME;
        $data['memo'] = '无';
        $data['user_count'] = 1;
        $data['user_id'] = $user_id;
        $data['bank_name'] = strim($_REQUEST['bank_name']); //银行名称
        $data['province'] = strim($_REQUEST['province']);//开户省
        $data['city'] = strim($_REQUEST['city']);//开户市
        $data['area'] = strim($_REQUEST['area']);//开户区
        $data['branch_name'] = strim($_REQUEST['branch_name']);//支行名称
        $data['open_account_num'] = strim($_REQUEST['open_account_num']);//开户账号
        $data['open_account_name'] = strim($_REQUEST['open_account_name']);//开户名称
        $data['contact'] = strim($_REQUEST['contact']);//联系人
        $data['contact_number'] = strim($_REQUEST['contact_number']);//联系电话
        $data['legal'] = strim($_REQUEST['legal']);//法人
        $data['company_name'] = strim($_REQUEST['company_name']);//公司全称
        $data['register_site'] = strim($_REQUEST['register_site']);//注册地址
        $data['contact_site'] = strim($_REQUEST['contact_site']);//联系地址
        $data['receipt'] = strim($_REQUEST['receipt']);//发票
        $data['business_photo']=get_spec_image(strim($_REQUEST['business_photo']));//营业执照


        if ($data['name'] == ''){
            $root['error'] = '公会名称不能为空';
            api_ajax_return($root);
        }
        if(strlen($data['name']) > 48){
            api_ajax_return(array(
                'status' => '0',
                'error' => '公会名称限制15字以内'
            ));
        }


        if (($data['province']==''&&$data['city']==''&&$data['area']=='')  ||($data['province']==''&&$data['city']=='') ){
            $root['error'] = '开户地不能为空';
            api_ajax_return($root);
        }

        if ($data['branch_name'] == ''){
            $root['error'] = '支行名称不能为空';
            api_ajax_return($root);
        }
        if ($data['open_account_num'] == ''){
            $root['error'] = '开户账号不能为空';
            api_ajax_return($root);
        }
        if ($data['open_account_name'] == ''){
            $root['error'] = '开户名称不能为空';
            api_ajax_return($root);
        }
        if ($data['contact'] == ''){
            $root['error'] = '联系人不能为空';
            api_ajax_return($root);
        }
        if ($data['contact_number'] == ''){
            $root['error'] = '联系电话不能为空';
            api_ajax_return($root);
        }
        if ($data['legal'] == ''){
            $root['error'] = '法人不能为空';
            api_ajax_return($root);
        }
        if ($data['company_name'] == ''){
            $root['error'] = '公司全称不能为空';
            api_ajax_return($root);
        }
        if ($data['register_site'] == ''){
            $root['error'] = '注册地址不能为空';
            api_ajax_return($root);
        }
        if ($data['contact_site'] == ''){
            $root['error'] = '联系地址不能为空';
            api_ajax_return($root);
        }
        if ($data['receipt'] == ''){
            $root['error'] = '发票税点不能为空';
            api_ajax_return($root);
        }
//        if ($data['business_photo'] == ''){
//            $root['error'] = '营业执照不能为空';
//            api_ajax_return($root);
//        }



        $user = $GLOBALS['db']->getRow("SELECT society_id,society_chieftain FROM " . DB_PREFIX . "user WHERE id =" . $user_id);
        $society_status = $GLOBALS['db']->getOne("SELECT status FROM " . DB_PREFIX . "society WHERE id =" . $user['society_id'] . " and user_id=" . $user_id);
        if ($user['society_id'] > 0 && $society_status != 2){
            if ($user['society_chieftain'] == 1){//用户是公会长
                if ($society_status == 1){
                    $root['error'] = '您已有申请成功的公会';
                }
                if ($society_status == 0) {
                    $root['error'] = '您已申请的公会正在审核';
                }
            }else{//用户是公会成员
                $root['error'] = '您已加入公会，请退出后再申请';
            }
            $root['status'] = 0;
        }else{
            $name_exist = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where name='".$data['name']."' and (status=0 or status=1)");
            if ($name_exist > 0){
                $root['error'] = '公会名已存在';
            }else{
                $m_config = load_auto_cache('m_config');
                $data['refund_rate'] = $m_config['society_public_rate'];
                $res = $GLOBALS['db']->autoExecute(DB_PREFIX."society",$data);
                if ($res){
                    $society_id = $GLOBALS['db']->insert_id();
                    $user_data = array();
                    if ($society_id){
                        $user_data['society_id'] = $society_id;
                        $user_data['society_chieftain'] = 1;
                    }
                    $GLOBALS['db']->autoExecute(DB_PREFIX . "user", $user_data, $mode = 'UPDATE', "id=" . $data['user_id']);
                    //更新redis
                    fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                    $user_redis = new UserRedisService();
                    $user_redis->update_db($data['user_id'], array('society_id' => $society_id, 'society_chieftain' => 1));

                    //将会长信息写入公会申请表
                    $society_apply['user_id']     = $user_id;
                    $society_apply['society_id']  = $society_id;
                    $society_apply['create_time'] = NOW_TIME;
                    $society_apply['status']      = 1;
                    $GLOBALS['db']->autoExecute(DB_PREFIX."society_apply",$society_apply);

                    $root['society_id'] = $society_id;
                    $root['status']     = 1;
                    $root['error']      = '公会申请成功';
                }else{
                    $root['status'] = 0;
                    $root['error']  = '公会申请失败';
                }
            }
        }

        api_ajax_return($root);
    }

    //手机登录
    public function mobile_login(){

        api_ajax_return();

    }

    public function mobile_login_save(){
        $root = array('status' => 0,'error'=>'');
        if(!$_REQUEST)
        {
            app_redirect(APP_ROOT."/");
        }
        foreach($_REQUEST as $k=>$v)
        {
            $_REQUEST[$k] = strim($v);
        }
        fanwe_require(APP_ROOT_PATH."system/libs/user.php");
        $result = do_login_user($_REQUEST['mobile'],$_REQUEST['verify_coder']);
        if($result['status'])
        {
            $root['user_id'] = $result['user']['id'];
            $root['status'] = 1;

            if($result['user']['head_image']==''||$result['user_info']['head_image']==''){
                //头像
                $m_config =  load_auto_cache("m_config");//初始化手机端配置
                $system_head_image = $m_config['pc_default_headimg'];

                if($system_head_image==''){
                    $system_head_image = './public/images/defaulthead.png';
                    syn_to_remote_image_server($system_head_image);
                }

                $data = array(
                    'head_image' => $system_head_image,
                    'thumb_head_image' => get_spec_image($system_head_image,40,40),
                );

                $GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$result['user']['id']);

                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
                $user_redis = new UserRedisService();
                $user_redis->update_db($result['user']['id'],$data);

                //更新session
                $user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $result['user']['id']);
                es_session::set("user_info", $user_info);
            }
            $root['is_lack'] = $result['is_lack'];//是否缺少用户信息
            $root['is_agree'] = intval($result['user']['is_agree']);//是否同意直播协议 0 表示不同意 1表示同意
            $root['user_id'] = intval($result['user']['id']);
            $root['nick_name'] = $result['user']['nick_name'];
            $root['family_id']=intval($result['user']['family_id']);
            $root['family_chieftain']=intval($result['user']['family_chieftain']);
            $root['error'] = "登录成功";
            $root['user_info'] = $result['user_info'];
        }
        else
        {
            $root['error'] = $result['info'];
        }
        api_ajax_return($root);

    }

    //微信登录
    public function wx_entry()
    {
        $state = intval($_REQUEST['pid']);

        $last = pathinfo($_SERVER["HTTP_REFERER"]);
        $back_url = SITE_DOMAIN.url("society#wx_callback", array('last' => urlencode($last['basename'])));

        $m_config =  load_auto_cache("m_config");
        $root = array('status'=>1,'error'=>'');
        $root['appid'] = $m_config['wx_web_appid'];
        $root['back_url'] = urlencode($back_url);
        $root['state'] = $state;
        api_ajax_return($root);
    }

    //微信登录回调
    public function wx_callback(){
        $code = strim($_REQUEST['code']);
        if(!$code) {
            $root = array('status' => 0, 'error' => '参数为空');
            api_ajax_return($root);
        }

        fanwe_require(APP_ROOT_PATH."system/utils/weixin.php");
        $m_config =  load_auto_cache("m_config");//初始化手机端配置

        $wx_appid = strim($m_config['wx_web_appid']);
        $wx_secrit = strim($m_config['wx_web_secrit']);
        //获取微信配置信息
        if($wx_appid==''||$wx_secrit==''){
            $root['status'] = 0;
            $root['error'] = "wx_appid或wx_secrit不存在";
            api_ajax_return($root);
        }

        $jump_url = SITE_DOMAIN.url("society#wx_callback");

        $weixin=new weixin($wx_appid,$wx_secrit,$jump_url);
        if($_REQUEST['code']!=""){
            $wx_info = $weixin->scope_get_userinfo($code);
            fanwe_require(APP_ROOT_PATH."system/libs/user.php");
            $root = wxxMakeUser($wx_info);
            if(!$root['status']){
                api_ajax_return($root);
            }
        }else{
            $root['status'] = 0;
            $root['error'] = "微信登录失败";
            api_ajax_return($root);
        }

        $last =urldecode($_REQUEST['last']);

        $society = substr($last,38,52);
        if($root['status']){

            app_redirect( url('society_user#authent'.$society));
        }
    }

    //QQ登录
    public function qq_entry(){
        fanwe_require(APP_ROOT_PATH."system/QQloginApi/qqConnectAPI.php");
        $qc = new QC();
        $last = pathinfo($_SERVER["HTTP_REFERER"]);
        $back_url = SITE_DOMAIN.url("society#qq_callback", array('last' => $last['basename']));
        $qc->qq_login($back_url);
    }

    //QQ登录回调
    function qq_callback(){
        $m_config =  load_auto_cache("m_config");//初始化手机端配置

        $last = pathinfo(urldecode($_REQUEST['last']));
        log_result($last);
        $back_url = SITE_DOMAIN.url("society#qq_callback", array('last' => $last));
        fanwe_require(APP_ROOT_PATH."system/QQloginApi/qqConnectAPI.php");
        $qc = new QC();
        $access_token  = $qc->qq_callback($back_url);
        $openid = $qc->get_openid();
        $ret = $qc->get_pc_user_info($access_token, $openid);
        $ret['openid'] = $openid;
        fanwe_require(APP_ROOT_PATH."system/libs/user.php");
        $root = qqMakeUser($ret);
        $last =urldecode($_REQUEST['last']);

        $society = substr($last,38,52);
        if($root['status']){

            app_redirect( url('society_user#authent'.$society));
        }

    }

    //退出
    public function drop_out(){

        fanwe_require(APP_ROOT_PATH."system/libs/user.php");
        $result = loginout_user();

        es_session::delete("user_info");
        $root['status'] = 1;
        $root['error'] = "登出成功";

        api_ajax_return($root);
    }

    public function pop(){
        $root = array('status'=>1,'error'=>'');
        api_ajax_return($root);
    }
}

?>