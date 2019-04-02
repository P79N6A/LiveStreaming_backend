<?php
/**
 * Date: 2017/7/6
 * Time: 15:25
 */
fanwe_require(APP_ROOT_PATH . 'mapi/lib/user.action.php');
fanwe_require(APP_ROOT_PATH . 'mapi/bm_index/user.action.php');
fanwe_require(APP_ROOT_PATH . 'mapi/app/page.php');
class user_twoCModule extends userCModule
{
    public  $bm_promoter_type;//用户类型：1鱼商，2鱼乐合伙人 ，3平台员工

    function __construct() {
        if (!$GLOBALS['user_info']['id']) {
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));
        }

        $user_id=intval($GLOBALS['user_info']['id']);
        $promoter_id=intval($GLOBALS['promoter_info']['id']);
        if ($promoter_id==0) {

            fanwe_require(APP_ROOT_PATH."system/libs/user.php");
            $result = loginout_user();
            es_session::delete("user_info");
            es_session::delete("promoter_info");

            $GLOBALS['user_info']=es_session::get("user_info");
            $GLOBALS['promoter_info']=es_session::get("promoter_info");
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));

        }

        $promoter_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."bm_promoter where id=".$promoter_id);
        //权限
        $promoter_role=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."bm_role where id=".intval($promoter_info['bm_role_id']));
        if (!$promoter_role) {
        	 api_ajax_return(array(
                'error' => '未授予权限，请联系管理员！',
                'status' => 0,
            ));
        }

        $role_list_str=$promoter_role['role_list'];
        //$role_list=json_decode($role_list_str,true);
        $role_list=unserialize($role_list_str);
        $act_list=array();
        foreach ($role_list as $k=>$v){
        	$act_list[]=$v['action'];
        }
        $act=$_REQUEST['act'];
        if (!in_array($act,$act_list)){
        	api_ajax_return(array(
        	'error' => '未授予该权限，请联系上级管理员！',
            'status' => 0,
        	));
        }


        $bm_promoter_type=intval($GLOBALS['user_info']['bm_promoter_type']);
        
        if($GLOBALS['user_info']['is_robot'] ==1){
            $bm_promoter_type=3;//平台员工
        }elseif(!$bm_promoter_type){
            //$promoter_info=$GLOBALS['db']->getRow("select id,pid from ".DB_PREFIX."bm_promoter where user_id=".$user_id." and status=1");
        	$promoter_info=$GLOBALS['promoter_info'];
            if(!$promoter_info){
                $bm_promoter_type=0;
            }else{
            	if (intval($promoter_info['pid'])>0||intval($promoter_info['member_id'])>0){
            		$bm_promoter_type=1;
            	}else{
            		$bm_promoter_type=2;
            	}
            }
        }

        //$bm_promoter_type 为0 退出
        if(!$bm_promoter_type){

            fanwe_require(APP_ROOT_PATH."system/libs/user.php");
            $result = loginout_user();
            es_session::delete("user_info");
            es_session::delete("promoter_info");

            $GLOBALS['user_info']=es_session::get("user_info");
            $GLOBALS['promoter_info']=es_session::get("promoter_info");
            //有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return(array(
                'error' => '用户未登陆,请先登陆.',
                'status' => 0,
                'user_login_status' => 0,
            ));

        }
        $this->bm_promoter_type=$bm_promoter_type;
        
    }

    //会员列表
    public function users(){
        $root['status']=1;
        $root['page_title']="会员列表";

        $begin_time=strim($_REQUEST['begin_time']);
        $end_time=strim($_REQUEST['end_time']);
        $login_name=strim($_REQUEST['login_name']);
        $bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);

        //条件
        $where="u.is_robot =0 and u.bm_pid>0 and p.member_id =0";

        $begin_time_num=to_timespan(strim($_REQUEST['begin_time']));
        $end_time_num=to_timespan(strim($_REQUEST['end_time']));
        $day_time=24*60*60;
        if($begin_time_num >0){
            $end_time_num=$end_time_num>0?$end_time_num+$day_time:$begin_time_num+$day_time;
            $where .=" and u.create_time >".$begin_time_num." and u.create_time<".$end_time_num."";
        }

        if($begin_time_num ==0 && $end_time_num){
            $begin_time_num=$end_time_num;
            $end_time_num +=$day_tim;
            $where .=" and u.create_time >".$begin_time_num." and u.create_time<".$end_time_num."";
        }

        if($bm_qrcode_id){
            $where .=" and u.bm_qrcode_id ='".$bm_qrcode_id."'";
        }

        if($login_name){
            $where .=" and p.login_name ='".$login_name."'";
        }

        $cur_promoter=$GLOBALS['promoter_info'];
        if($this->bm_promoter_type ==1){
            //鱼商
            $where .=" and u.bm_pid=".intval($GLOBALS['user_info']['id'])."";
        }elseif($this->bm_promoter_type ==2){
//            $p_users=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid= ".intval($GLOBALS['user_info']['id'])." ");
//            if(!$p_users){
//                $root['nick_name']=$nick_name;
//                $root['user_id']=$user_id;
//                $root['mobile']=$mobile;
//                api_ajax_return($root);
//            }
//            $p_users=array_map('array_shift',$p_users);
//            $where .=" and u.bm_pid in(".implode(',',$p_users).")";
            $where .=" and p.pid =".intval($GLOBALS['user_info']['id'])."";
        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select count(*) from ".DB_PREFIX."user  as u left join ".DB_PREFIX."bm_promoter as p on p.user_id =u.bm_pid where ".$where;
        $list_count=$GLOBALS['db']->getOne($sql_count);
        if(!$list_count){
            $root['begin_time']=$begin_time;
            $root['end_time']=$end_time;
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count, $page_size);
        $page_show = $page->show();

        //数据搜索
        $sql="select u.id,u.nick_name,u.head_image,u.bm_pid,u.is_effect,u.bm_qrcode_id,p.name as p_name,p.login_name as p_login_name,q.name qrcode_name,q.promoter_id as qrcode_promoter_id from ".DB_PREFIX."user as u
              left join ".DB_PREFIX."bm_promoter as p on p.user_id =u.bm_pid left join ".DB_PREFIX."bm_qrcode as q on q.id = u.bm_qrcode_id where ".$where.$limit;
        $list=$GLOBALS['db']->getAll($sql);

        foreach($list as $k=>$v){
            $qrcode_promoter_ids[]=$v['qrcode_promoter_id'];
        }
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter_names=$GLOBALS['db']->getAll("select id,name,login_name from ".DB_PREFIX."bm_promoter where id in(".implode(',',$qrcode_promoter_ids).") ");
            foreach($qrcode_promoter_names as $k=>$v){
                $promoter_names[$v['id']]['name']=$v['name'];
                $promoter_names[$v['id']]['login_name']=$v['login_name'];
            }

            foreach($list as $k=>$v){
                $list[$k]['qrcode_promoter_name']=$promoter_names[$v['qrcode_promoter_id']]['name'];
                $list[$k]['qrcode_promoter_login_name']=$promoter_names[$v['qrcode_promoter_id']]['login_name'];
            }
        }


        $root['list']=$list;
        $root['page']=$page_show;
        $root['list_count']=$list_count;

        $root['begin_time']=$begin_time;
        $root['end_time']=$end_time;
        api_ajax_return($root);

    }
    //主播报表
    public function anchor(){
     	$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$id=intval($_REQUEST['id']);
     	$nick_name=trim($_REQUEST['nick_name']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
     	$where = " u.is_authentication = 2 and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($id > 0){
     		$where .= " and  u.id = ".$id." ";
     	}
     	if($nick_name ){
     		$where .= " and u.nick_name like '%".$nick_name."%' ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
     	
     	if($type==1  || $type==3){
     		if($type==1){
     			$where .= " and u.bm_pid = $user_id ";
     		}
     		$sql = "SELECT u.id,u.nick_name,u.head_image,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name  FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY u.create_time DESC limit " . $limit;

	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	if($v['bm_qrcode_id']){
            		//所属业务员名称
            		$list[$k]['bm_qrcode_nick_name'] = $GLOBALS['db']->getOne("select name from ". DB_PREFIX . "bm_qrcode where id=".$v['bm_qrcode_id']);
            	}

            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
     	}else{

     		$where .= " and bp.pid = $user_id ";
     		$sql = "SELECT u.id,u.nick_name,u.head_image,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name   FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY u.create_time DESC limit " . $limit;

	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	if($v['bm_qrcode_id']){
            		//所属业务员名称
            		$list[$k]['bm_qrcode_nick_name'] = $GLOBALS['db']->getOne("select name from ". DB_PREFIX . "bm_qrcode where id=".$v['bm_qrcode_id']);
            	}
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '主播报表';
        $root['total'] = $count;
        api_ajax_return($root);
    }
    //主播收礼报表
    public function anchor_earnings(){
     	$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$id=intval($_REQUEST['id']);
     	$from_user_id=intval($_REQUEST['from_user_id']);
     	$prop_id=intval($_REQUEST['prop_id']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);
     	//时间
        $time = $this->check_date();

        $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . " ";
     	$where .= " and  u.is_authentication = 2 and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($id > 0){
     		$where .= " and  u.id = ".$id." ";
     	}
     	if($from_user_id > 0){
     		$where .= " and  l.from_user_id = ".$from_user_id." ";
     	}
     	if($prop_id > 0){
     		$where .= " and  l.prop_id = ".$prop_id." ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
     	if($type==1  || $type==3){
     		if($type==1){
     			$where .= " and u.bm_pid = $user_id ";
     		}
     		$sql = "SELECT u.id,u.nick_name,l.from_user_id,l.prop_id,l.prop_name,l.create_time,l.total_diamonds,l.total_ticket,l.total_coins,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,if(l.total_coins > 0,'游戏币礼物','秀豆礼物') as gift_type,bp.login_name as bm_pid_login_name   FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY l.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //总秀豆,总秀票,总游戏币
	        $total = $GLOBALS['db']->getRow( "SELECT sum(l.total_diamonds) as total_diamonds,sum(l.total_ticket) as total_ticket,sum(l.total_coins) as total_coins FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ");
     	}else{
     		$where .= " and bp.pid = $user_id ";
     		$sql = "SELECT u.id,u.nick_name,l.from_user_id,l.prop_id,l.prop_name,l.create_time,l.total_diamonds,l.total_ticket,l.total_coins,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,if(l.total_coins > 0,'游戏币礼物','秀豆礼物') as gift_type,bp.login_name as bm_pid_login_name  FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ";
            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY l.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //总秀豆,总秀票,总游戏币
	        $total = $GLOBALS['db']->getRow( "SELECT sum(l.total_diamonds) as total_diamonds,sum(l.total_ticket) as total_ticket,sum(l.total_coins) as total_coins FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");
     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '主播收礼报表';
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['nick_name'] = strim($_REQUEST['nick_name']);
        $root['total'] = $total;
        api_ajax_return($root);
    }
	//会员游戏报表
	public function user_game(){
		$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$id=intval($_REQUEST['id']);
     	$game_name=trim($_REQUEST['game_name']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);
     	//时间
        $time = $this->check_date();
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($id > 0){
     		$where .= " and  u.id = ".$id." ";
     	}
     	if($game_name){
     		$where .=" and gs.name like '%".$game_name."%' ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
     	if($type==1  || $type==3){
     		if($type==1){
     			$where .= " and u.bm_pid = $user_id ";
     		}
     		$sql = "SELECT bpg.create_time,gs.name as game_name,bpg.sum_bet,if(bpg.sum_win > 0, abs(bpg.sum_win),0) as sum_win,if(bpg.sum_win < 0, abs(bpg.sum_win),0) as sum_fail,(bpg.platform_gain + bpg.promoter_gain) as user_gain ,u.id,u.nick_name,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name  FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id  WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id  WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //获胜,失败,会员手续费
	        $total = $GLOBALS['db']->getRow( "SELECT sum(if(bpg.sum_win > 0, bpg.sum_win,0)) as sum_win,sum(if(bpg.sum_win < 0, abs(bpg.sum_win),0)) as sum_fail,sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id  WHERE $where ");
     	}else{
     		$where .= " and bp.pid = $user_id ";
     		$sql = "SELECT bpg.create_time,gs.name as game_name,bpg.sum_bet,if(bpg.sum_win > 0, abs(bpg.sum_win),0) as sum_win,if(bpg.sum_win < 0, abs(bpg.sum_win),0) as sum_fail,(bpg.platform_gain + bpg.promoter_gain) as user_gain ,u.id,u.nick_name,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name  FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //获胜,失败,会员手续费
	        $total = $GLOBALS['db']->getRow( "SELECT sum(if(bpg.sum_win > 0, bpg.sum_win,0)) as sum_win,sum(if(bpg.sum_win < 0, abs(bpg.sum_win),0)) as sum_fail,sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "games as gs on bpg.game_id = gs.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");
     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '会员游戏报表';
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
		$root['total'] = $total;
        api_ajax_return($root);
	}

	//鱼商业务报表
	public function fish_business(){
		$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$login_name=trim($_REQUEST['login_name']);
     	//时间
        $time = $this->check_date();
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($type==1){
     		$where .= " and u.id = $user_id ";
     		$sql = "SELECT u.id,u.nick_name,sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(bpg.platform_gain + bpg.promoter_gain) as total_gain,bp.login_name as bm_pid_login_name   FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id WHERE $where  ";

	        $list = $GLOBALS['db']->getRow($sql, true, true);
            //鱼乐合伙人编号
            $list['bm_login_name'] = $GLOBALS['db']->getOne("SELECT login_name from ". DB_PREFIX . "bm_promoter where id=".$user_id);

			//礼物秀豆,礼物游戏币,游戏手续费
			$total = $GLOBALS['db']->getRow( "SELECT sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id WHERE $where  ");

     	}else{
     		if($type==2)
     		{
     			$where .= " and bp.pid = $user_id ";
     		}

     		$sql = "SELECT u.id,u.nick_name,sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(bpg.platform_gain + bpg.promoter_gain) as total_gain,bp.login_name as bm_pid_login_name  FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ";
          	$p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where  ");
	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY l.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql);
	        if($list){
	        	foreach($list as $k=>$v)
	            {
	            	if($v['id']){
	            		//鱼乐合伙人编号
	            		$list[$k]['bm_login_name'] = $GLOBALS['db']->getOne("select login_name from ". DB_PREFIX . "bm_promoter where user_id=".$v['id']);
	            	}

	            }
	        }


            $total = $GLOBALS['db']->getRow( "SELECT sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain FROM   "
            . DB_PREFIX . "video_prop_" . $time['begin_time_ym'] . " as l LEFT JOIN "
            . DB_PREFIX . "user AS u  ON l.to_user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id WHERE $where ");

     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '鱼商业务报表';
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['nick_name'] = strim($_REQUEST['nick_name']);
		$root['total'] = $total;
		$root['type'] = $type;
        api_ajax_return($root);
	}
	//会员手续费报表
	public function user_poundage(){
		$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$id=intval($_REQUEST['id']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);

     	//时间
        $time = $this->check_date();
     	$where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($id > 0){
     		$where .= " and u.id = ".$id." ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
     	if($type==1  || $type==3){
     		if($type==1){
     			$where .= " and u.bm_pid = $user_id ";
     		}
     		$sql = "SELECT u.id,u.nick_name,(bpg.platform_gain + bpg.promoter_gain) as user_gain,bpg.platform_gain,bpg.promoter_gain,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //会员手续费,平台留存手续费,鱼商留存手续费
	        $total = $GLOBALS['db']->getRow( "SELECT sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain,sum(bpg.platform_gain ) as total_platform_gain,sum(bpg.promoter_gain) as total_promoter_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ");
     	}else{
     		$where .= " and bp.pid = $user_id ";
     		$sql = "SELECT u.id,u.nick_name,(bpg.platform_gain + bpg.promoter_gain) as user_gain,bpg.platform_gain,bpg.promoter_gain,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //获胜,失败,会员手续费
	        $total = $GLOBALS['db']->getRow( "SELECT sum(bpg.platform_gain + bpg.promoter_gain) as sum_user_gain,sum(bpg.platform_gain ) as total_platform_gain,sum(bpg.promoter_gain) as total_promoter_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");
     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '会员手续费报表';
        $root['nick_name'] = strim($_REQUEST['nick_name']);
		$root['total'] = $total;
        api_ajax_return($root);
	}
	//会员盈亏报表
	public function user_earnings(){
		$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$id=intval($_REQUEST['id']);
     	$login_name=trim($_REQUEST['login_name']);
     	$bm_qrcode_id=trim($_REQUEST['bm_qrcode_id']);
     	//时间
        $time = $this->check_date();
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($id > 0){
     		$where .= " and u.id = ".$id." ";
     	}
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($bm_qrcode_id > 0){
     		$where .= " and  u.bm_qrcode_id = ".$bm_qrcode_id." ";
     	}
     	if($type==1  || $type==3){
     		if($type==1){
     			$where .= " and u.bm_pid = $user_id ";
     		}
     		$sql = "SELECT u.id,u.nick_name,bpg.user_gain,bpg.platform_gain,bpg.promoter_gain,bpg.gain,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ";


            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //会员手续费,平台留存手续费,鱼商留存手续费,鱼商客损
	        $total = $GLOBALS['db']->getRow( "SELECT sum(user_gain) as sum_user_gain,sum(bpg.platform_gain ) as total_platform_gain,sum(bpg.promoter_gain) as total_promoter_gain,sum(bpg.gain) as total_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id  WHERE $where ");
     	}else{
     		$where .= " and bp.pid = $user_id ";
     		$sql = "SELECT u.id,u.nick_name,bpg.user_gain,bpg.platform_gain,bpg.promoter_gain,bpg.gain,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id,bp.login_name as bm_pid_login_name FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ";

            $p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	//代理商编号
            	$list[$k]['bm_promoter_login_name'] = $this->login_name($v['bm_promoter_id']);
            }
	        //获胜,失败,会员手续费,鱼商客损
	        $total = $GLOBALS['db']->getRow( "SELECT sum(user_gain) as sum_user_gain,sum(bpg.platform_gain ) as total_platform_gain,sum(bpg.promoter_gain) as total_promoter_gain,sum(bpg.gain) as total_gain FROM   "
            . DB_PREFIX . "bm_promoter_game_log as bpg LEFT JOIN "
            . DB_PREFIX . "user as u on bpg.user_id = u.id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.bm_pid = bp.user_id WHERE $where ");
     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '会员盈亏报表';
        $root['nick_name'] = strim($_REQUEST['nick_name']);
		$root['total'] = $total;
        api_ajax_return($root);
	}
	//保证金账户查询
	public function user_margin(){
		$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$bm_config = load_auto_cache("bm_config");
     	$login_name=trim($_REQUEST['login_name']);

     	//时间
        $time = $this->check_date();
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.member_id = 0 ";
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($type==1){
     		$where .= " and u.id = $user_id ";
     		$sql = "SELECT u.nick_name,bp.login_name,u.coin,sum(bpg.gain) as gain  FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.pid WHERE $where  ";
	        $list = $GLOBALS['db']->getRow($sql, true, true);
	        //保证金
			$list['promoter_deposit'] = $bm_config['promoter_deposit'];
			//可提现
			$list['refund_coin'] = $list['coin'] - $bm_config['promoter_deposit'];
			if($list['refund_coin'] < 0){
				$list['refund_coin'] = 0;
			}
			 //鱼乐合伙人编号
            $list['bm_login_name'] = $GLOBALS['db']->getOne("select login_name from ". DB_PREFIX . "bm_promoter where user_id=".$user_id);
     	}else{
     		if($type==2)
     		{
     			$where .= " and bp.pid = $user_id ";
     		}

     		$sql = "SELECT u.id,u.nick_name,bp.login_name,sum(bpg.gain) as gain,u.id  FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.pid WHERE $where  ";

          	$p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
            . DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = u.id LEFT JOIN "
            . DB_PREFIX . "bm_promoter as bp on u.id = bp.pid WHERE $where  ");

	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);
	        foreach($list as $k=>$v)
            {
            	if($v['id']){
            		//鱼乐合伙人编号
            		$list[$k]['bm_login_name'] = $GLOBALS['db']->getOne("select login_name from ". DB_PREFIX . "bm_promoter where user_id=".$v['id']);
            		
            	}
            	
            	//保证金
            	$list[$k]['promoter_deposit'] = $bm_config['promoter_deposit'];
            	//可提现
				$list[$k]['refund_coin'] = $v['coin'] - $bm_config['promoter_deposit'];
				if($list[$k]['refund_coin'] < 0)
				{
					$list[$k]['refund_coin'] = 0 ;
				}
            }

     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '保证金账户查询';
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        $root['nick_name'] = strim($_REQUEST['nick_name']);
        $root['type'] = $type;

        api_ajax_return($root);
	}
	//编号
	public function login_name($id){
		$login_name =  $GLOBALS['db']->getOne("select login_name from ". DB_PREFIX . "bm_promoter where id=".$id);
		return $login_name;
	}
    //机构列表
    public function promoters(){
        //鱼商不能查看机构列表
        $root['status']=1;
        $root['page_title']="机构列表";
        $root['bm_promoter_type']=$this->bm_promoter_type;
        if($this->bm_promoter_type ==1){
            $root['errors']="哎呀，迷路了";
            $root['status']=0;
            api_ajax_return($root);
        }

        //参数
        //$promoter_name=strim($_REQUEST['promoter_name']);
        //$promoter_id=intval($_REQUEST['promoter_id']);
       //$promoter_type=intval($_REQUEST['promoter_type']);//0：全部，1鱼商，2鱼乐合伙人
        $login_name=strim($_REQUEST['login_name']);
        $begin_time=strim($_REQUEST['begin_time']);
        $end_time=strim($_REQUEST['end_time']);
        $p = intval($_REQUEST['p']);

        //条件
        $where='p.status =1 and p.member_id=0';
//        if($promoter_name !=''){
//            $where .=" and p.name like '%".$promoter_name."%' ";
//        }

        $begin_time_num=to_timespan(strim($_REQUEST['begin_time']));
        $end_time_num=to_timespan(strim($_REQUEST['end_time']));
        $day_time=24*60*60;
        if($begin_time_num >0){
            $end_time_num=$end_time_num>0?$end_time_num+$day_time:$begin_time_num+$day_time;
            $where .=" and p.create_time >".$begin_time_num." and p.create_time<".$end_time_num."";
        }

        if($begin_time_num ==0 && $end_time_num){
            $begin_time_num=$end_time_num;
            $end_time_num +=$day_time;
            $where .=" and p.create_time >".$begin_time_num." and p.create_time<".$end_time_num."";
        }

//        if($promoter_type == 1){
//            $where .=" and p.pid >0";
//        }elseif($promoter_type == 2){
//            $where .=" and p.pid =0";
//        }
//
//
//        if($promoter_id >0){
//            $where=" and p.id = ".$promoter_id."";
//        }

        if($login_name !=''){
           $where=" and p.login_name = ".$login_name."";
        }

        //分页
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //数据搜索
        if($this->bm_promoter_type ==2){
            //个数
            $sql_count="select count(*) from ".DB_PREFIX."bm_promoter as p where p.pid=".intval($GLOBALS['user_info']['id'])." and ".$where;
            $list_count=$GLOBALS['db']->getOne($sql_count);
            if(!$list_count){
                $root['promoter_one_count']=0;
                $root['promoter_two_count']=0;
                $root['begin_time']=$begin_time;
                $root['end_time']=$end_time;
                api_ajax_return($root);
            }

            //输出分页
            $page = new Page($list_count, $page_size);
            $page_show = $page->show();

            $sql="select p.id,p.name,p.pid,p.create_time,p.is_effect,p.login_name from ".DB_PREFIX."bm_promoter as p where p.pid=".intval($GLOBALS['user_info']['id'])." and ".$where.$limit;
            $promoter_list=$GLOBALS['db']->getAll($sql);
            $cur_promoter=$GLOBALS['promoter_info'];
            foreach($promoter_list as $k=>$v){
                $promoter_list[$k]['p_name']=$cur_promoter['name'];
                $promoter_list[$k]['p_login_name']=$cur_promoter['login_name'];
            }

            //汇总
            $promoter_one_count=0;
            $promoter_two_count=$list_count;
        }elseif($this->bm_promoter_type ==3){
            //个数
            $sql_count="select p.id,p.pid from ".DB_PREFIX."bm_promoter as p ".
            "left join (select b.id,b.pid,b.name,b.user_id,b.login_name from ".DB_PREFIX."bm_promoter as b where pid=0) as c on c.user_id = p.pid where ".$where;

            $list_count_row=$GLOBALS['db']->getAll($sql_count);
            $list_count=count($list_count_row);
            if(!$list_count){
                $root['list_count']=$list_count;
                $root['begin_time']=$begin_time;
                $root['end_time']=$end_time;
                api_ajax_return($root);
            }

            //输出分页
            $page = new Page($list_count, $page_size);
            $page_show = $page->show();

            $sql="select p.id,p.name,p.pid,p.create_time,p.is_effect,p.login_name,c.name as p_name,c.login_name p_login_name from ".DB_PREFIX."bm_promoter as p 
            left join (select b.id,b.pid,b.name,b.user_id,b.login_name from ".DB_PREFIX."bm_promoter as b where pid=0) as c on c.user_id = p.pid
            where ".$where.$limit;
            $promoter_list=$GLOBALS['db']->getAll($sql);

            //汇总
            $promoter_one_count=0;
            $promoter_two_count=0;
            foreach($list_count_row as $k=>$v){
                if($v['pid'] ==0){
                    $promoter_one_count +=1;
                }else{
                    $promoter_two_count +=1;
                }
            }
        }

        //汇总

        $root['promoter_list']=$promoter_list;
        $root['page']=$page_show;
        $root['promoter_one_count']=intval($promoter_one_count);
        $root['promoter_two_count']=intval($promoter_two_count);

        $root['begin_time']=$begin_time;
        $root['end_time']=$end_time;
        api_ajax_return($root);
    }

    //机构审核列表
    public function promoter_check(){
        //鱼商不能查看机构列表
        $root['status']=1;
        $root['page_title']="机构审核列表";
        $root['bm_promoter_type']=$this->bm_promoter_type;
        if($this->bm_promoter_type ==1){
            $root['errors']="哎呀，迷路了";
            $root['status']=0;
            api_ajax_return($root);
        }

        //参数
        //$promoter_name=strim($_REQUEST['promoter_name']);
        //$promoter_id=intval($_REQUEST['promoter_id']);
        //$check_state=intval($_REQUEST['check_state']);//0全，状态：1待审核，2通过，3未通过
        $begin_time=strim($_REQUEST['begin_time']);
        $end_time=strim($_REQUEST['end_time']);
        $login_name=strim($_REQUEST['login_name']);
        $p = intval($_REQUEST['p']);

        //条件
        $where='p.status in(0,2) and p.member_id=0';
//        if($promoter_name !=''){
//            $where .=" and p.name like '%".$promoter_name."%' ";
//        }

        $begin_time_num=to_timespan(strim($_REQUEST['begin_time']));
        $end_time_num=to_timespan(strim($_REQUEST['end_time']));
        $day_time=24*60*60;
        if($begin_time_num >0){
            $end_time_num=$end_time_num>0?$end_time_num+$day_time:$begin_time_num+$day_time;
            $where .=" and p.create_time >".$begin_time_num." and p.create_time<".$end_time_num."";
        }

        if($begin_time_num ==0 && $end_time_num){
            $begin_time_num=$end_time_num;
            $end_time_num +=$day_tim;
            $where .=" and p.create_time >".$begin_time_num." and p.create_time<".$end_time_num."";
        }

        if($login_name !=''){
            $where=" and p.login_name = ".$login_name."";
        }

//        if($check_state >0){
//            $check_state -=1;
//            $where=" and p.status = ".$check_state."";
//        }

        //分页
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //数据搜索
        if($this->bm_promoter_type ==2){
            //个数
            $sql_count="select count(*) from ".DB_PREFIX."bm_promoter as p where p.pid=".intval($GLOBALS['user_info']['id'])." and ".$where;
            $list_count=$GLOBALS['db']->getOne($sql_count);
            if(!$list_count){
                $root['list_count']=$list_count;
                $root['begin_time']=$begin_time;
                $root['end_time']=$end_time;
                api_ajax_return($root);
            }
            //输出分页
            $page = new Page($list_count, $page_size);
            $page_show = $page->show();

            $sql="select p.id,p.name,p.pid,p.create_time,p.is_effect,p.status,p.memo from ".DB_PREFIX."bm_promoter as p where p.pid=".intval($GLOBALS['user_info']['id'])." and ".$where.$limit;
            $promoter_list=$GLOBALS['db']->getAll($sql);
            $cur_promoter=$GLOBALS['promoter_info'];
            foreach($promoter_list as $k=>$v){
                $promoter_list[$k]['p_name']=$cur_promoter['name'];
                $promoter_list[$k]['p_login_name']=$cur_promoter['login_name'];
            }
        }elseif($this->bm_promoter_type ==3){
            //个数
            $sql_count="select count(*) from ".DB_PREFIX."bm_promoter as p where p.pid=".intval($GLOBALS['user_info']['id'])." and ".$where;
            $list_count=$GLOBALS['db']->getOne($sql_count);
            if(!$list_count){
                $root['list_count']=$list_count;
                $root['begin_time']=$begin_time;
                $root['end_time']=$end_time;
                api_ajax_return($root);
            }
            //输出分页
            $page = new Page($list_count, $page_size);
            $page_show = $page->show();

            $sql="select p.id,p.name,p.pid,p.create_time,p.is_effect,p.status,p.memo,c.name as p_name,c.login_name as p_login_name from ".DB_PREFIX."bm_promoter as p 
            left join (select b.id,b.pid,b.name,b.user_id,b.login_name from ".DB_PREFIX."bm_promoter as b where pid=0) as c on c.user_id = p.pid
            where ".$where.$limit;
            $promoter_list=$GLOBALS['db']->getAll($sql);
        }

        $root['promoter_list']=$promoter_list;
        $root['list_count']=$list_count;
        $root['page']=$page_show;
        $root['begin_time']=$begin_time;
        $root['end_time']=$end_time;
        api_ajax_return($root);
    }
    //会员送礼报表
    public function send_prop()
    {
        $root['status']=1;
        $root['page_title']="会员送礼报表";
        //参数
        $prop_id=intval($_REQUEST['prop_id']);
        $to_user_id=intval($_REQUEST['to_user_id']);
        $from_user_id=intval($_REQUEST['from_user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();

        $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
        if($prop_id >0){
            $where.=" and l.prop_id =".$prop_id." ";
        }

        if($to_user_id >0){
            $where.=" and l.to_user_id =".$to_user_id." ";
        }

        if($from_user_id >0){
            $where.=" and l.from_user_id =".$from_user_id." ";
        }

        if($this->bm_promoter_type ==1){
            //鱼商
            $where.=" and u.bm_pid =".intval($GLOBALS['user_info']['id'])." ";
        }elseif($this->bm_promoter_type ==2){
            //鱼乐合伙人
            if($login_name !=''){
                $poromter_user_s=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])." and login_name='".$login_name."'");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $where.=" and u.bm_pid = ".intval($poromter_user_s['user_id'])."";
            }else{
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])."");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }
        }elseif($this->bm_promoter_type ==3 && $login_name !=''){
            //平台员工
            $poromter_info_3=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where login_name='".$login_name."' and member_id=0");
            if(!$poromter_info_3){
                $root['total_diamonds']=0;
                $root['total_coins']=0;
                $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                api_ajax_return($root);
            }

            if($poromter_info_3['pid'] ==0){
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($poromter_info_3['user_id'])."");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }else{
                $where.=" and u.bm_pid =".intval($poromter_info_3['user_id'])." ";
            }

        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select count(*) as list_count,sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l left join ".DB_PREFIX."user as u on u.id = l.from_user_id where ".$where;
        $list_count_row=$GLOBALS['db']->getRow($sql_count);
        $list_count=intval($list_count_row['list_count']);
        if(!$list_count){
            $root['total_diamonds']=0;
            $root['total_coins']=0;
            $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
            $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count_row['list_count'], $page_size);
        $page_show = $page->show();

        $sql="select l.id,l.to_user_id,l.from_user_id,l.prop_name,l.prop_id,l.total_diamonds,l.total_coins,l.create_time,l.is_coin,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id as qrcode_promoter_id from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l".
        " left join ".DB_PREFIX."user as u on u.id = l.from_user_id where ".$where.$limit;
        $list=$GLOBALS['db']->getAll($sql);
        foreach($list as $k=>$v){
            $qrcode_promoter_ids[$v['bm_pid']]=$v['bm_pid'];
            $qrcode_promoter_ids[$v['qrcode_promoter_id']]=$v['qrcode_promoter_id'];
        }
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter_names=$GLOBALS['db']->getAll("select id,name,login_name from ".DB_PREFIX."bm_promoter where id in(".implode(',',$qrcode_promoter_ids).") ");
            foreach($qrcode_promoter_names as $k=>$v){
                $promoter_names[$v['id']]['name']=$v['name'];
                $promoter_names[$v['id']]['login_name']=$v['login_name'];
            }

            foreach($list as $k=>$v){
                $list[$k]['p_login_name']=$promoter_names[$v['bm_pid']]['name'];
                $list[$k]['qrcode_promoter_login_name']=$promoter_names[$v['qrcode_promoter_id']]['login_name'];
            }
        }

        $root['list']=$list;
        $root['page']=$page_show;
        $root['total_diamonds']=intval($list_count_row['total_diamonds']);
        $root['total_coins']=intval($list_count_row['total_coins']);
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);
    }

    //会员充值报表
    public function recharge_list(){
        $root['status']=1;
        $root['page_title']="会员充值报表";
        //参数
        $is_paid=intval($_REQUEST['is_paid']);//0全部，1未支付，2支付
        $user_id=intval($_REQUEST['user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
        $where = " n.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . " ";
        if($is_paid >0){
            $is_paid -=1;
            $where .=" and n.is_pay=".$is_pay."";
        }

        if($user_id >0){
            $is_paid -=1;
            $where .=" and n.user_id=".$user_id."";
        }

        if($this->bm_promoter_type ==1){
            //鱼商
            $where.=" and u.bm_pid =".intval($GLOBALS['user_info']['id'])." ";
        }elseif($this->bm_promoter_type ==2){
            //鱼乐合伙人
            if($login_name !=''){
                $poromter_user_s=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])." and login_name='".$login_name."'");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['total_money']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $where.=" and u.bm_pid = ".intval($poromter_user_s['user_id'])."";
            }else{
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])."");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['total_money']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }
        }elseif($this->bm_promoter_type ==3 && $login_name !=''){
            //平台员工
            $poromter_info_3=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where login_name='".$login_name."' and member_id=0");
            if(!$poromter_info_3){
                $root['total_diamonds']=0;
                $root['total_coins']=0;
                $root['total_money']=0;
                $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                $root['errors'] = "未找匹配的机构";
                api_ajax_return($root);
            }

            if($poromter_info_3['pid'] ==0){
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($poromter_info_3['user_id'])."");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['total_money']=0;
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }else{
                $where.=" and u.bm_pid =".intval($poromter_info_3['user_id'])." ";
            }

        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select count(*) as list_count,sum(n.money) as total_money,sum(n.diamonds) as total_diamonds,sum(r.gift_coins) as total_gift_coins  from ".DB_PREFIX."payment_notice as n".
            " left join ".DB_PREFIX."user as u on u.id = n.user_id ".
            " left join ".DB_PREFIX."recharge_rule as r on r.id = n.recharge_id where ".$where;
        $list_count_row=$GLOBALS['db']->getRow($sql_count);
        $list_count=intval($list_count_row['list_count']);
        if(!$list_count){
            $root['total_diamonds']=0;
            $root['total_coins']=0;
            $root['total_money']=0;
            $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
            $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count, $page_size);
        $page_show = $page->show();

        $sql="select n.create_time,n.user_id,n.money,n.diamonds,n.is_paid,p.name as payment_name,r.gift_coins,r.name as r_name,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id as qrcode_promoter_id from ".DB_PREFIX."payment_notice as n".
            " left join ".DB_PREFIX."user as u on u.id = n.user_id ".
            " left join ".DB_PREFIX."payment as p on p.id = n.payment_id ".
            " left join ".DB_PREFIX."recharge_rule as r on r.id = n.recharge_id where ".$where.$limit;
        $list=$GLOBALS['db']->getAll($sql);
        foreach($list as $k=>$v){
            $qrcode_promoter_ids[$v['bm_pid']]=$v['bm_pid'];
            $qrcode_promoter_ids[$v['qrcode_promoter_id']]=$v['qrcode_promoter_id'];
        }
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter_names=$GLOBALS['db']->getAll("select id,name,login_name from ".DB_PREFIX."bm_promoter where id in(".implode(',',$qrcode_promoter_ids).") ");
            foreach($qrcode_promoter_names as $k=>$v){
                $promoter_names[$v['id']]['name']=$v['name'];
                $promoter_names[$v['id']]['login_name']=$v['login_name'];
            }

            foreach($list as $k=>$v){
                $list[$k]['p_login_name']=$promoter_names[$v['bm_pid']]['login_name'];
                $list[$k]['qrcode_promoter_login_name']=$promoter_names[$v['qrcode_promoter_id']]['login_name'];
            }
        }

        $root['list']=$list;
        $root['list_count']=$list_count;
        $root['page']=$page_show;
        $root['total_diamonds']=intval($list_count_row['total_diamonds']);
        $root['total_coins']=intval($list_count_row['total_gift_coins']);
        $root['total_money']=intval($list_count_row['total_money']);
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);

    }

    //会员账户列表
    public function user_account(){
        $root['status']=1;
        $root['page_title']="会员列表";

        $login_name=strim($_REQUEST['login_name']);
        $user_id=intval($_REQUEST['user_id']);
        $bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);

        //条件
        $where="u.is_robot =0 and u.bm_pid>0 and p.member_id =0";

        if($bm_qrcode_id >0){
            $where .=" and u.bm_qrcode_id =".$bm_qrcode_id."";
        }
        if($user_id >0){
            $where .=" and u.id =".$user_id."";
        }

        if($this->bm_promoter_type ==1){
            //鱼商
            $where .=" and u.bm_pid=".intval($GLOBALS['user_info']['id'])."";
        }elseif($this->bm_promoter_type ==2){
            if($login_name){
                $poromter_user_s=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])." and login_name='".$login_name."'");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $where.=" and u.bm_pid = ".intval($poromter_user_s['user_id'])."";
            }else{
                $p_users=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid= ".intval($GLOBALS['user_info']['id'])." ");
                if(!$p_users){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $p_users=array_map('array_shift',$p_users);
                $where .=" and u.bm_pid in(".implode(',',$p_users).")";
            }
        }elseif($this->bm_promoter_type ==3 && $login_name !=''){
            //平台员工
            $poromter_info_3=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where login_name='".$login_name."' and member_id=0");
            if(!$poromter_info_3){
                $root['total_diamonds']=0;
                $root['total_coins']=0;
                $root['errors'] = "未找匹配的机构";
                api_ajax_return($root);
            }

            if($poromter_info_3['pid'] ==0){
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($poromter_info_3['user_id'])."");
                if(!$poromter_user_s){
                    $root['total_diamonds']=0;
                    $root['total_coins']=0;
                    $root['errors'] = "无鱼商";
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }else{
                $where.=" and u.bm_pid =".intval($poromter_info_3['user_id'])." ";
            }
        }

        if($user_id >0){
            $where =" and u.id =".$user_id."";
        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select count(*) as list_count,sum(u.diamonds) as total_diamonds,sum(u.coin) as total_coin from ".DB_PREFIX."user as u left join ".DB_PREFIX."bm_promoter as p on p.user_id =u.bm_pid where ".$where;
        $list_count_row=$GLOBALS['db']->getRow($sql_count);
        $list_count=intval($list_count_row['list_count']);
        if(!$list_count){
            $root['total_diamonds']=0;
            $root['total_coins']=0;
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count, $page_size);
        $page_show = $page->show();

        //数据搜索diamonds
        $sql="select u.id,u.nick_name,u.diamonds,u.coin,u.bm_pid,u.is_effect,u.bm_qrcode_id,p.name as p_name,p.login_name as p_login_name,q.name qrcode_name,q.promoter_id as qrcode_promoter_id from ".DB_PREFIX."user as u
              left join ".DB_PREFIX."bm_promoter as p on p.user_id =u.bm_pid left join ".DB_PREFIX."bm_qrcode as q on q.id = u.bm_qrcode_id where ".$where.$limit;
        $list=$GLOBALS['db']->getAll($sql);

        foreach($list as $k=>$v){
            $qrcode_promoter_ids[]=$v['qrcode_promoter_id'];
        }
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter_names=$GLOBALS['db']->getAll("select id,name,login_name from ".DB_PREFIX."bm_promoter where id in(".implode(',',$qrcode_promoter_ids).") ");
            foreach($qrcode_promoter_names as $k=>$v){
                $promoter_names[$v['id']]['name']=$v['name'];
                $promoter_names[$v['id']]['login_name']=$v['login_name'];
            }

            foreach($list as $k=>$v){
                $list[$k]['qrcode_promoter_name']=$promoter_names[$v['qrcode_promoter_id']]['name'];
                $list[$k]['qrcode_promoter_login_name']=$promoter_names[$v['qrcode_promoter_id']]['login_name'];
            }
        }

        $root['list']=$list;
        $root['page']=$page_show;
        $root['total_diamonds']=intval($list_count_row['total_diamonds']);
        $root['total_coins']=intval($list_count_row['total_coins']);

        api_ajax_return($root);

    }

    //主播鱼粮报表(主播收礼汇总)
    public function anchor_prop()
    {
        $root['status']=1;
        $root['page_title']="主播鱼粮报表";
        //参数
        $bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
        $to_user_id=intval($_REQUEST['to_user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
        $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
        if($bm_qrcode_id >0){
            $where.=" and u.bm_qrcode_id = ".$bm_qrcode_id."";
        }
        if($to_user_id >0){
            $where.=" and u.to_user_id = ".$to_user_id."";
        }

        if($this->bm_promoter_type ==1){
            //鱼商
            $where.=" and u.bm_pid =".intval($GLOBALS['user_info']['id'])." ";
        }elseif($this->bm_promoter_type ==2){
            //鱼乐合伙人
            if($login_name !=''){
                $poromter_user_s=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])." and login_name='".$login_name."'");
                if(!$poromter_user_s){
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $where.=" and u.bm_pid = ".intval($poromter_user_s['user_id'])."";
            }else{
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($GLOBALS['user_info']['id'])."");
                if(!$poromter_user_s){
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }
        }elseif($this->bm_promoter_type ==3 && $login_name !=''){
            //平台员工
            $poromter_info_3=$GLOBALS['db']->getRow("select id,pid,user_id from ".DB_PREFIX."bm_promoter where login_name='".$login_name."' and member_id=0");
            if(!$poromter_info_3){
                $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                api_ajax_return($root);
            }

            if($poromter_info_3['pid'] ==0){
                $poromter_user_s=$GLOBALS['db']->getAll("select user_id from ".DB_PREFIX."bm_promoter where pid=".intval($poromter_info_3['user_id'])."");
                if(!$poromter_user_s){
                    $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
                    $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
                    api_ajax_return($root);
                }
                $poromter_user_s=array_map('array_shift',$poromter_user_s);
                $where.=" and u.bm_pid in(".implode(',',$poromter_user_s).") ";
            }else{
                $where.=" and u.bm_pid =".intval($poromter_info_3['user_id'])." ";
            }

        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select count(*) from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l left join ".DB_PREFIX."user as u on u.id = l.from_user_id where ".$where." GROUP BY l.to_user_id";

        $list_count=$GLOBALS['db']->getOne($sql_count);
        if(!$list_count){
            $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
            $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count_row['list_count'], $page_size);
        $page_show = $page->show();

        $sql="select l.id,l.to_user_id,sum(l.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(l.total_ticket) as total_ticket,l.create_time".
            ",u.nick_name,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id as qrcode_promoter_id from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l".
            " left join ".DB_PREFIX."user as u on u.id = l.to_user_id where ".$where." GROUP BY l.to_user_id".$limit;
        $list=$GLOBALS['db']->getAll($sql);

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

        foreach($list as $k=>$v){
            $qrcode_promoter_ids[$v['bm_pid']]=$v['bm_pid'];
            $qrcode_promoter_ids[$v['qrcode_promoter_id']]=$v['qrcode_promoter_id'];

            //计算
            $list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_average_anchor_revenue / 100);
            $list[$k]['promoter_ticket'] = $list[$k]['total_ticket'] - $list[$k]['user_ticket'];

        }
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter_names=$GLOBALS['db']->getAll("select id,name,login_name from ".DB_PREFIX."bm_promoter where id in(".implode(',',$qrcode_promoter_ids).") ");
            foreach($qrcode_promoter_names as $k=>$v){
                $promoter_names[$v['id']]['name']=$v['name'];
                $promoter_names[$v['id']]['login_name']=$v['login_name'];
            }

            foreach($list as $k=>$v){
                $list[$k]['p_login_name']=$promoter_names[$v['bm_pid']]['login_name'];
                $list[$k]['qrcode_promoter_name']=$promoter_names[$v['qrcode_promoter_id']]['name'];
                $list[$k]['qrcode_promoter_login_name']=$promoter_names[$v['qrcode_promoter_id']]['login_name'];
            }
        }

        //合计
//        $sql_total="select sum(u.total_diamonds) as total_diamonds,sum(l.total_coins) as total_coins,sum(l.total_ticket) as total_ticket from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l left join ".DB_PREFIX."user as u on u.id = l.from_user_id where ".$where."";
//        $list_total=$GLOBALS['db']->getRow($sql_count);
//        $list_total['user_ticket_all'] += $list_total['total_ticket'] * ($bm_config['promoter_average_anchor_revenue'] / 100);
//        $list_total['promoter_ticket_all'] = $list_total['total_ticket']-$list_total['user_ticket_all'];

        $root['list']=$list;
        $root['page']=$page_show;
        //$root['list_total']=$list_total;

        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);
    }

    //鱼商成交报表
    public function promoter_bargain()
    {
        $root['status']=1;
        $root['page_title']="主播鱼粮报表";
        //参数
        $bm_qrcode_id=intval($_REQUEST['bm_qrcode_id']);
        $user_id=intval($_REQUEST['user_id']);
        $login_name=strim($_REQUEST['login_name']);
        $time = $this->check_date();
        $where = " l.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . " and p.member_id =0 ";

        if($this->bm_promoter_type ==1){
            //鱼商
            $where.=" and p.user_id =".intval($GLOBALS['user_info']['id'])." ";
        }elseif($this->bm_promoter_type ==2){
            $where.=" and p.pid = ".intval($GLOBALS['user_info']['id'])."";
            if($login_name !=''){
                $where.=" and p.login_name = '".$login_name."'";
            }
        }elseif($this->bm_promoter_type ==3 && $login_name !=''){
            if($login_name !=''){
                $where.=" and p.login_name = '".$login_name."'";
            }
        }

        //分页
        $p = intval($_REQUEST['p']);
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        //个数
        $sql_count="select u.bm_pid,sum(l.total_ticket) as total_ticket from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l left join ".DB_PREFIX."user as u on u.id = l.to_user_id ".
            " left join ".DB_PREFIX."bm_promoter as p on p.user_id = u.bm_pid where ".$where." GROUP BY u.bm_pid".$limit;
        $list_count_all=$GLOBALS['db']->getALL($sql_count);
        $list_count=count($list_count_all);
        if(!$list_count){
            $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
            $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count, $page_size);
        $page_show = $page->show();

        $sql="select l.id,l.to_user_id,sum(l.total_ticket) as total_ticket,p.login_name as p_login_name,p.name as p_name,p.pid as p_pid" .
            ",u.nick_name,u.bm_pid,u.bm_qrcode_id,u.bm_promoter_id as qrcode_promoter_id from ".DB_PREFIX."video_prop_".$time['begin_time_ym']." as l".
            " left join ".DB_PREFIX."user as u on u.id = l.to_user_id ".
            " left join ".DB_PREFIX."bm_promoter as p on p.user_id = u.bm_pid where ".$where." GROUP BY u.bm_pid".$limit;
        $list=$GLOBALS['db']->getAll($sql);

        $bm_config = load_auto_cache("bm_config");
        //代理商普通主播收益分成
        $promoter_average_anchor_revenue = intval($bm_config['promoter_average_anchor_revenue']);
        if ($promoter_average_anchor_revenue < -1 || $promoter_average_anchor_revenue > 100) {
            $promoter_average_anchor_revenue = 70;
        }
        $list_re=array();
        foreach($list as $k=>$v){
            //计算
            $list[$k]['user_ticket'] = $v['total_ticket'] * ($promoter_average_anchor_revenue / 100);
            $list[$k]['promoter_ticket'] = $list[$k]['total_ticket'] - $list[$k]['user_ticket'];

            $page_promoter_pids[$v['p_pid']]=$v['p_pid'];
            $list_re[$v['bm_pid']]=$list[$k];
        }
        //获取所属鱼乐合伙人编号
        $page_promoter_pids=array_filter($page_promoter_pids);
        $page_promoter_pids=array_flip($page_promoter_pids);
        $p_info=$GLOBALS['db']->getAll("select user_id,login_name from ".DB_PREFIX."bm_promoter where user_id in(".implode(',',$page_promoter_pids).") ");
        $p_info_re=array();
        foreach($p_info as $k=>$v){
            $p_info_re[$v['user_id']]=$v['login_name'];
        }

        foreach($list_re as $k=>$v){
            $list_re[$k]['p_parent_login_name']=$p_info_re[$v['p_pid']];
        }

        //游戏流水及合计
        $all_promoter_user_ids=array_map('array_shift',$list_count_all);
        $total_re=array();
        foreach($list_count_all as $k=>$v){
            $all_promoter_user_ids[$v['bm_pid']]=$v['bm_pid'];
            $total_re['total_ticket'] +=$v['total_ticket'];//秀票合计
        }
        $total_re['user_ticket'] = $total_re['total_ticket'] * ($promoter_average_anchor_revenue / 100);//会员秀票合计
        $total_re['promoter_ticket'] = $total_re['total_ticket'] - $total_re['user_ticket'];//平台（鱼商）秀票合计

        if($all_promoter_user_ids){
            $game_log_sql="select bm_pid,sum(promoter_gain) as promoter_gain,sum(platform_gain) as platform_gain,sum(gain) as gain from ".DB_PREFIX."bm_promoter_game_log where bm_pid in(".implode(',',$all_promoter_user_ids).") and sum_win >0 GROUP BY bm_pid";
            $game_log=$GLOBALS['db']->getAll($game_log_sql);
            foreach($game_log as $k=>$v){
                $game_log[$k]['gain']=abs($v['gain']);
                $total_re['promoter_gain'] +=$v['promoter_gain'];//鱼乐合伙人游戏流水合计
                $total_re['platform_gain'] +=$v['platform_gain'];//平台戏流水合计
                $total_re['gain'] +=$game_log[$k]['gain'];//鱼商戏流水合计
                if($list_re[$v['bm_pid']]){
                    $list_re[$v['bm_pid']]['promoter_gain'] =$v['promoter_gain'];
                    $list_re[$v['bm_pid']]['platform_gain'] =$v['platform_gain'];
                    $list_re[$v['bm_pid']]['gain'] =$game_log[$k]['gain'];
                }
            }
        }

        $root['list']=$list_re;
        $root['page']=$page_show;
        $root['total_re']=$total_re;

        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
        api_ajax_return($root);
    }
    //权限列表
    public function role_index(){

    	$root = array("error" => "", "status" => 1, "page_title" => "权限列表");
    	$user_id = intval($GLOBALS['user_info']['id']);

    	//根据用户身份决定数据返回
    	$promoter_id=0;

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}


    	$p = $_REQUEST['p'];
    	if ($p == '') {
    		$p = 1;
    	}
    	$p = $p > 0 ? $p : 1;
    	$page_size = 10;
    	$limit = (($p - 1) * $page_size) . "," . $page_size;

    	$count = $GLOBALS['db']->getOne("SELECT count(*) FROM   " . DB_PREFIX . "bm_role where promoter_id=" . $promoter_id . " and  is_delete=0" );

    	$role_list=$GLOBALS['db']->getAll("select * from  " . DB_PREFIX . "bm_role where promoter_id=" . $promoter_id . " and  is_delete=0 limit " . $limit, true, true);
    	$root['list']=$role_list;

    	$page = new Page($count, $page_size);
    	$page_show = $page->show();
    	$root['page'] = $page_show;

    	api_ajax_return($root);

    }

    //新增权限
    public function role_add(){

    	$root = array("error" => "", "status" => 1, "page_title" => "新增权限");
    	$user_id = intval($GLOBALS['user_info']['id']);

    	//根据用户身份决定数据返回
    	$promoter_id=0;

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}

    	$sql = "select * from  " . DB_PREFIX . "bm_promoter where id=" . intval($GLOBALS['promoter_info']['id']) ;
    	$bm_promoter_info=$GLOBALS['db']->getRow($sql);

    	$role_info=$GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "bm_role where id=" . intval($GLOBALS['promoter_info']['bm_role_id']));

    	$root['role_list']=$this->get_role_list($role_info['all_role_list']);
    	api_ajax_return($root);

    }

    //编辑权限
    public function role_edit(){


    	$root = array("error" => "", "status" => 1, "page_title" => "编辑权限");
    	$user_id = intval($GLOBALS['user_info']['id']);

    	$id = intval($_REQUEST['id']);
    	//根据用户身份决定数据返回
    	$promoter_id=0;

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}

    	$sql = "select * from  " . DB_PREFIX . "bm_promoter where id=" . intval($GLOBALS['promoter_info']['id']) ;
    	$bm_promoter_info=$GLOBALS['db']->getRow($sql);

    	$role_info_all=$GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "bm_role where id=" . intval($GLOBALS['promoter_info']['bm_role_id']));

    	$role_info=$GLOBALS['db']->getRow("select * from  " . DB_PREFIX . "bm_role where id=" . $id);
    	$root['role_info']=$role_info;
    	$root['role_list']=$this->get_role_list($role_info_all['all_role_list'],$role_info['role_list']);
    	api_ajax_return($root);

    }

    //删除权限
    public function role_delete(){


    	$root = array("error" => "", "status" => 1, "page_title" => "删除权限");
    	$user_id = intval($GLOBALS['user_info']['id']);

    	$id = intval($_REQUEST['id']);

    	$bm_promoter_list=$GLOBALS['db']->getAll("select * from  " . DB_PREFIX . "bm_promoter where bm_role_id=" . $id." ");
    	if ($bm_promoter_list) {
    		$root['status']=0;
    		$root['error']="该权限正在使用中，不可删除。请替换权限后实施该操作！";
    		api_ajax_return($root);
    	}

    	$GLOBALS['db']->query("update " . DB_PREFIX . "bm_role set is_delete = 1  where id=" . $id . "");

    	api_ajax_return($root);

    }

    //新增插入权限
    public function role_insert(){


    	$root = array("error" => "", "status" => 1, "page_title" => "更新权限");
    	$user_id = intval($GLOBALS['user_info']['id']);

    	$promoter_id=0;
    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}

    	$role_node['name']=strim($_REQUEST['name']);
    	$role_node['promoter_id']=$promoter_id;
    	$role_node['is_effect']=1;
    	$role_node['is_detele']=0;

    	$role_access = $_REQUEST['role_access'];//被选中的项的id的列表
    	$role_list=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_role_node where  id  in (" . implode(',', $role_access) . ")", true, true);

    	//$role_list_str=json_encode($role_list);
    	$role_list_str=serialize($role_list);
    	$role_node['role_list']=$role_list_str;
    	$role_node['all_role_list']=$role_list_str;
    	$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_role", $role_node, $mode = 'INSERT');

    	api_ajax_return($root);

    }

    //更新权限
    public function role_update(){

    	$root = array("error" => "", "status" => 1, "page_title" => "更新权限");
    	$user_id = intval($GLOBALS['user_info']['id']);
    	$id = intval($_REQUEST['id']);

    	$promoter_id=0;
    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}
    	$role_node=array();
    	$role_node['name']=strim($_REQUEST['name']);
    	$role_node['promoter_id']=$promoter_id;
    	$role_node['is_effect']=intval($_REQUEST['is_effect']);
    	$role_node['is_detele']=0;

    	$role_access = $_REQUEST['role_access'];//被选中的项的id的列表
    	$role_list=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_role_node where  id  in (" . implode(',', $role_access) . ")", true, true);

    	//$role_list_str=json_encode($role_list);
    	$role_list_str=serialize($role_list);
    	$role_node['role_list']=$role_list_str;
    	$role_node['all_role_list']=$role_list_str;
    	$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_role", $role_node, $mode = 'UPDATE', "id=" . $id);

    	api_ajax_return($root);

    }



    //角色列表
    public function admin_index(){
    	$root = array("error" => "", "status" => 1, "page_title" => "角色列表");

    	$promoter_id=0;
    	$member_id=intval($GLOBALS['promoter_info']['member_id']);

    	if ($member_id==0) {
    		$member_id=intval($GLOBALS['promoter_info']['user_id']);
    	}

    	$p = $_REQUEST['p'];
    	if ($p == '') {
    		$p = 1;
    	}
    	$p = $p > 0 ? $p : 1;
    	$page_size = 10;
    	$limit = (($p - 1) * $page_size) . "," . $page_size;

    	$count = $GLOBALS['db']->getOne("SELECT count(*) FROM  " . DB_PREFIX . "bm_promoter where (member_id=".$member_id.")" );

    	$admin_list=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_promoter where (member_id=".$member_id.") limit " . $limit, true, true);
    	//role_name
    	foreach ($admin_list as $k=>$v){
    		$admin_list[$k]['role_name']=$this->role_name($v['bm_role_id']);
    	}
    	$root['list']=$admin_list;

    	$page = new Page($count, $page_size);
    	$page_show = $page->show();
    	$root['page'] = $page_show;
    	api_ajax_return($root);

    }

    //角色新增
    public function admin_add(){
    	$root = array("error" => "", "status" => 1, "page_title" => "角色新增");

    	//根据用户身份决定数据返回
    	$promoter_id=0;

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}

    	$sql = "select * from  " . DB_PREFIX . "bm_role where promoter_id=" . $promoter_id . " and  is_delete=0";
    	$role_list=$GLOBALS['db']->getAll($sql);
    	$root['role_list']=$role_list;

    	api_ajax_return($root);

    }

    //角色编辑
    public function admin_edit(){
    	$root = array("error" => "", "status" => 1, "page_title" => "角色编辑");

    	$id = intval($_REQUEST['id']);

    	$admin_info=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_promoter where (id=".$id.") ", true, true);
    	$root['admin_info']=$admin_info;

    	//根据用户身份决定数据返回
    	$promoter_id=0;

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$promoter_id=0;
    	}else if($member_id==0){
    		$promoter_id=intval($GLOBALS['promoter_info']['id']);
    	}else{
    		$promoter_id=$member_id;
    	}

    	$sql = "select * from  " . DB_PREFIX . "bm_role where promoter_id=" . $promoter_id . " and  is_delete=0";
    	$role_list=$GLOBALS['db']->getAll($sql);
    	$root['role_list']=$role_list;

    	api_ajax_return($root);

    }

    //角色保存
    public function admin_insert(){
    	$root = array("error" => "", "status" => 1, "page_title" => "角色保存");

    	//根据用户身份决定数据返回

    	$member_id=intval($GLOBALS['promoter_info']['member_id']);
    	if ($member_id==-1) {
    		$user_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where is_robot = 1 and is_effect=1");
    		$member_id=-1;
    	}else if($member_id==0){
    		$user_id = intval($GLOBALS['promoter_info']['user_id']);
    		$member_id = intval($GLOBALS['promoter_info']['user_id']);
    	}else{
    		$user_id = intval($GLOBALS['promoter_info']['user_id']);
    		$member_id=$member_id;
    	}


    	$bm_promoter=array();
    	$bm_promoter['name']=strim($_REQUEST['name']);
    	$pwd = strim($_REQUEST['password']);
    	$bm_promoter['pwd'] = $pwd == '' ? md5('123456') : md5(strim($_REQUEST['password']));
    	$bm_promoter['user_id']=$user_id;
    	$bm_promoter['pid']=0;
    	$bm_promoter['is_effect']=intval($_REQUEST['is_effect']);
    	$bm_promoter['status']=1;
    	$bm_promoter['create_time']=NOW_TIME;
    	$bm_promoter['member_id']=$member_id;
    	$bm_promoter['bm_role_id']=intval($_REQUEST['bm_role_id']);


    	$re=$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $bm_promoter, $mode = 'INSERT');
    	if ($re) {
    		$re_id=$GLOBALS['db']->insert_id();
    		if ($member_id==-1) {
    			//平台管理
    			$bm_promoter_add['login_name']="admin_".$re_id;
    		}else {
    			//二级员工
    			$bm_promoter_add['login_name']="member_".$GLOBALS['promoter_info']['id']."_".$re_id;
    		}

    		$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $bm_promoter_add, $mode = 'UPDATE', "id=" . $re_id);
    	}

    	api_ajax_return($root);

    }

    //角色保存
    public function admin_update(){
    	$root = array("error" => "", "status" => 1, "page_title" => "角色保存");

    	$id = intval($_REQUEST['id']);

    	$bm_promoter=array();
    	$bm_promoter['name']=strim($_REQUEST['name']);
    	$bm_promoter['is_effect']=intval($_REQUEST['is_effect']);
    	$bm_promoter['status']=1;
    	$bm_promoter['create_time']=NOW_TIME;
    	$bm_promoter['bm_role_id']=intval($_REQUEST['bm_role_id']);
    	$pwd = strim($_REQUEST['password']);
    	if ($pwd != '') {
    		$bm_promoter['pwd'] = md5($pwd);
    	}

    	$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $bm_promoter, $mode = 'UPDATE', "id=" . $id);

    	api_ajax_return($root);

    }



    //格式化权限列表
    function get_role_list($all_role_list_str,$role_list_str=null){

    	//$all_role_list=json_decode($all_role_list_str,true);
    	$all_role_list=unserialize($all_role_list_str);

    	$cate_name_list=array();
    	foreach ($all_role_list as $k=>$v){
    		/*if (!in_array($v['cate_name'], $cate_name_list)) {
    			$cate_name_list[]['cate_name']=$v['cate_name'];
    		} */
    		$cate_name_list[$v['cate_name']]['cate_name']=$v['cate_name'];
    	}

    	$cate_name_list =array_values($cate_name_list);

    	$role_list=$all_role_list;
    	foreach ($role_list as $rk => $rv) {
    		$role_list[$rk]['node_auth']=0;
    	}

    	foreach($cate_name_list as $k=> $v) {
    		foreach ($role_list as $rk => $rv) {
    			if ($v['cate_name']==$rv['cate_name']) {
    				$cate_name_list[$k]['role_list'][]=$rv;
    			}
    		}
    	}

    	if ($role_list_str) {
    		//$user_role_list = json_decode($role_list_str,true);
    		$user_role_list = unserialize($role_list_str);

    		if (count($user_role_list)>0) {

    			foreach($cate_name_list as $k=> $v) {
    				foreach($cate_name_list[$k]['role_list'] as $k1=> $v1) {
    					foreach($user_role_list as $k2=> $v2) {
    						if ($v2['id']==$v1['id']) {
    							$cate_name_list[$k]['role_list'][$k1]['node_auth']=1;
    						}
    					}
    				}
    			}
    		}
    	}


    	return $cate_name_list;

    }

    //鱼乐合伙人报表
    function partner(){
    	$type = $this->bm_promoter_type;
     	$user_id = intval($GLOBALS['user_info']['id']);
     	$login_name=trim($_REQUEST['login_name']);
     	//时间
        $time = $this->check_date();
        $where = " bpg.create_time between " . $time['begin_time'] . " and " . $time['end_time'] . "";
     	$where .= " and u.is_effect = 1 ";
     	$where .= " and bp.pid = 0 and bp.member_id = 0 ";
     	$where .= " and bp.member_id = 0 ";
     	if($login_name){
     		$where .=" and bp.login_name like '%".$login_name."%' ";
     	}
     	if($type==2 || $type==3){
     		if($type==2)
     		{
     			$where .= " and bp.pid = $user_id ";
     		}

     		$sql = "SELECT bp.login_name,u.nick_name,sum(bpg.promoter_gain) as total_promoter_gain FROM   "
            . DB_PREFIX . "user AS u LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = bp.user_id  WHERE $where ";

          	$p = $_REQUEST['p'];
	        if ($p == '') {
	            $p = 1;
	        }
	        $p = $p > 0 ? $p : 1;
	        $page_size = 10;
	        $limit = (($p - 1) * $page_size) . "," . $page_size;

	        $count = $GLOBALS['db']->getOne("SELECT count(*) FROM  "
            . DB_PREFIX . "user AS u LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = bp.user_id  WHERE $where ");
	        $page = new Page($count, $page_size);
	        $page_show = $page->show();
	        $sql .= " ORDER BY bpg.create_time DESC limit " . $limit;
	        $list = $GLOBALS['db']->getAll($sql, true, true);

            $total = $GLOBALS['db']->getRow( "SELECT sum(bpg.promoter_gain) as total_promoter_gain FROM   "
            . DB_PREFIX . "user AS u  LEFT JOIN "
           	. DB_PREFIX . "bm_promoter as bp on u.id = bp.user_id LEFT JOIN "
           	. DB_PREFIX . "bm_promoter_game_log as bpg on bpg.user_id = bp.user_id  WHERE $where ");

     	}
     	$root['list'] = $list;
        $root['page'] = $page_show;
        $root['page_title'] = '鱼乐合伙人报表';
        $root['begin_time'] = to_date($time['begin_time'], 'Y-m-d');
        $root['end_time'] = to_date($time['end_time'], 'Y-m-d');
		$root['total'] = $total;

        api_ajax_return($root);

    }

    //业务员列表
    public function qrcodes(){
        $root['status']=1;
        $root['page_title']="业务员列表";

        $begin_time=strim($_REQUEST['begin_time']);
        $end_time=strim($_REQUEST['end_time']);
        $login_name=strim($_REQUEST['login_name']);
        $qrcode_id=strim($_REQUEST['qrcode_id']);

        $begin_time_num=to_timespan(strim($_REQUEST['begin_time']));
        $end_time_num=to_timespan(strim($_REQUEST['end_time']));
        $day_time=24*60*60;
        if($begin_time_num >0){
            $end_time_num=$end_time_num>0?$end_time_num+$day_time:$begin_time_num+$day_time;
            $where .=" and q.create_time >".$begin_time_num." and q.create_time<".$end_time_num."";
        }

        if($begin_time_num ==0 && $end_time_num){
            $begin_time_num=$end_time_num;
            $end_time_num +=$day_time;
            $where .=" and q.create_time >".$begin_time_num." and q.create_time<".$end_time_num."";
        }

        if($qrcode_id >0){
            $where .=" and q.id=".intval($qrcode_id)."";
        }

        //分页
        if ($p == '') {
            $p = 1;
        }
        $p = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit = (($p - 1) * $page_size) . "," . $page_size;
        $limit = " limit " . $limit;

        $c_user_id=intval($GLOBALS['user_info']['id']);

        if($this->bm_promoter_type ==1){
            $sql_count="select count(*) from ".DB_PREFIX."bm_qrcode as q where q.p_user_id=".$c_user_id.$where;
            $sql="select q.promoter_id,q.id,q.name,q.is_effect,q.img,q.qrcode_sn,q.create_time,p.name as p_name,p.login_name as p_login_name from ".DB_PREFIX."bm_qrcode as q left join ".DB_PREFIX."bm_promoter as p on p.user_id = q.p_user_id where q.p_user_id=".$c_user_id." and p.member_id =0 ".$where.$limit;
        }elseif($this->bm_promoter_type ==2 || $this->bm_promoter_type ==3){
            if($this->bm_promoter_type ==2){
                $where_2=" p.pid=".$c_user_id."";
            }else{
                $where_2=" p.member_id =0 ";
            }

            if($login_name !=''){
                $where .=" and p.login_name ='".$login_name."'";
            }
            $sql_count="select count(*) from ".DB_PREFIX."bm_qrcode as q left join ".DB_PREFIX."bm_promoter as p on p.user_id = q.p_user_id where {$where_2} ".$where;
            $sql="select q.promoter_id,q.id,q.name,q.is_effect,q.img,q.qrcode_sn,q.create_time,p.name as p_name,p.login_name as p_login_name from ".DB_PREFIX."bm_qrcode as q left join ".DB_PREFIX."bm_promoter as p on p.user_id = q.p_user_id where {$where_2} ".$where.$limit;
        }
        //个数
        $list_count=intval($GLOBALS['db']->getOne($sql_count));
        if(!$list_count){
            $root['list_count']=$list_count;
            $root['begin_time']=$begin_time;
            $root['end_time']=$end_time;
            api_ajax_return($root);
        }

        //输出分页
        $page = new Page($list_count, $page_size);
        $page_show = $page->show();

        //数据搜索
        $list=$GLOBALS['db']->getAll($sql);
        $qrcode_promoter_ids=array_map('array_shift',$list);
        $qrcode_promoter_ids=array_filter($qrcode_promoter_ids);
        $qrcode_promoter_ids=array_flip($qrcode_promoter_ids);
        if($qrcode_promoter_ids){
            $qrcode_promoter=$GLOBALS['db']->getAll("select user_id,name,login_name from ".DB_PREFIX."bm_promoter where user_id in(".implode(',',$qrcode_promoter_ids).") ");
            $qrcode_promoter_re=array();
            foreach($qrcode_promoter as $k=>$v){
                $qrcode_promoter_re[$v['user_id']]=$v;
            }
        }
        foreach($list as $k=>$v){
            if($v['promoter_id']){
                $list[$k]['qrcode_promoter_name']=$qrcode_promoter_re[$v['promoter_id']]['name'];
                $list[$k]['qrcode_promoter_login_name']=$qrcode_promoter_re[$v['promoter_id']]['login_name'];
            }
        }

        $root['list'] = $list;
        $root['page'] = $page_show;
        $root['begin_time'] = $begin_time;
        $root['end_time'] = $end_time;
        $root['list_count'] = $list_count;

        api_ajax_return($root);
    }

    // 创建推广商
    public function create_promoter()
    {
        if($this->bm_promoter_type != 2){
            $root['error'] = "哎呀，没有权限";
            $root['status'] = 0;
            api_ajax_return($root);
        }

        $root = array("error" => ".", "status" => 1);
        $user_id = intval($GLOBALS['user_info']['id']);

        $sql = "select * from  " . DB_PREFIX . "bm_promoter where user_id=" . $user_id . " and is_effect=1";
        $promoter = $GLOBALS['db']->getRow($sql, true, true);
        if (!$promoter || $promoter['pid'] != 0) {
            $root = array(
                "error" => "帐户无登陆权限，请联系管理员",
                "status" => 0,
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
                    "status" => 0,
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
        if($this->bm_promoter_type != 2){
            $root['error'] = "哎呀，没有权限";
            $root['status'] = 0;
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
                $id = $GLOBALS['db']->insert_id();
                $GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", ['login_name'=>$GLOBALS['promoter_info']['login_name'].'_'.$id], "UPDATE"," id=" . $id );
                $root["status"] = 1;
                $root["error"] = "新增成功";
            } else {
                $root["status"] = 0;
                $root["error"] = "提交失败，请刷新页面重试";
            }
        }

        api_ajax_return($root);
    }

    // 创建推广码
    public function create_promotcode()
    {
        if($this->bm_promoter_type != 1){
            $root['error'] = "哎呀，没有权限";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $root = array("error" => ".", "status" => 1);
        api_ajax_return($root);
    }

    // 提交推广码
    public function update_promotcode()
    {
        if($this->bm_promoter_type != 1){
            $root['error'] = "哎呀，没有权限";
            $root['status'] = 0;
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
        $max_id=$GLOBALS['db']->getOne("select max(id) from ".DB_PREFIX."bm_qrcode");
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/models/bm_promoterModel.class.php');
        $bm_promoter_obj = new bm_promoterModel();
        $qrcode_info['qrcode_sn']=$bm_promoter_obj->ToNumberSystem26($max_id+475255);
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

    //推广商二维开启关闭
    public function qrcode_switch()
    {
        //is_effect
        if($this->bm_promoter_type != 1){
            $root['error'] = "哎呀，没有权限";
            $root['status'] = 0;
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

    //编号
    public function role_name($id){
    	$role_name =  $GLOBALS['db']->getOne("select name from ". DB_PREFIX . "bm_role where id=".$id);
    	if ($role_name) {
    		return $role_name;
    	}
    	return "暂无权限";
    }
}