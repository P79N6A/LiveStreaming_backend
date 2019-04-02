<?php

// 资金管理——统计模块

class StatisticsModuleAction extends CommonAction{




	/**
	 * 统计图表
	 */
	public function chart()
	{

		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$sql_pay .= 'is_paid=1 and ';	
		if($map2['start_time'] == '' && $map2['end_time'] == ''){	
			$_REQUEST['start_time'] =date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')));
			$_REQUEST['end_time'] = date("Y-m-d",mktime(23,59,59,date('m'),date('t'),date('Y')));
			$map2['start_time'] = to_timespan($_REQUEST['start_time']);
			$map2['end_time'] =to_timespan($_REQUEST['end_time'])+86399;
		}	

/*		$map2['start_time'] = 1473177600;
		$map2['end_time'] = 1474632000;*/

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$sql_pay .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";	
		}		

		$model = D ();
		$sql_str = "SELECT sum(money) money,DATE_FORMAT(FROM_UNIXTIME(pay_time+28800),'%Y-%m-%d') bdate FROM ".DB_PREFIX."payment_notice WHERE 1=1 ";
		$user_payment_sql .= " and ".$sql_pay." 1=1 ";

//		if($map2['end_time'] > to_timespan('2017-07-01')){
//			$user_payment_sql .= ' and payment_id !=2 ';
//		}


		$sql_str .= " and ".$sql_pay." 1=1 group by bdate ORDER BY bdate asc ";
        $payRatesql =$GLOBALS['db']->getAll($sql_str);
		$this->assign ( 'list', $payRatesql );

		/*admin_ajax_return($payRatesql);*/
		$sql_refund .= 'is_pay=3 and ';	
		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$sql_refund .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";	
		}
		$model = D ();
		$sql_str = "SELECT sum(money) money,DATE_FORMAT(FROM_UNIXTIME(pay_time+28800),'%Y-%m-%d') bdate FROM ".DB_PREFIX."user_refund WHERE 1=1 ";
		$user_refund_sql .= " and ".$sql_refund." 1=1 ";		
		$sql_str .= " and ".$sql_refund." 1=1 group by bdate ORDER BY bdate asc ";
        $refundRatesql =$GLOBALS['db']->getAll($sql_str);
		$this->assign ( 'list2', $refundRatesql );


		$user_payment = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where 1=1 ".$user_payment_sql));
		$this->assign("user_payment",$user_payment);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_refund where 1=1 ".$user_refund_sql));
		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
	}



	public function statistics_analysis(){

	    if($_REQUEST['start_time']){
            $start_time = $_REQUEST['start_time'];
            $this->assign("start_time",$start_time);
            //用户递归中
            $list_data['create_time_1'] = $start_time;

            //用户查询充值人数
            $pay_where = " and pay_time >".strtotime($start_time);
        }

        if($_REQUEST['end_time']){
            $end_time = $_REQUEST['end_time'];
            $this->assign("end_time",$end_time);
            //用户递归中
            $list_data['create_time_2'] = $end_time;

            //用户查询充值人数
            $pay_where = " and pay_time <".strtotime($end_time);
        }


        $list_data['user_admin_id'] = $_SESSION['user_admin_session']['id'];
        $list_data['display'] = "exe";
        $re = $this->index_user_data($list_data);
        foreach($re as $k => $v){
            if($v['user_iden_id'] == 0){
                $ids[] = $v['id'];
            }
        }


        $ids = implode(",",$ids);

        //当前用户信息
        $user =  M("user")->where("id = ".$list_data['user_admin_id'] )->find();
        $this->assign("user",$user);

        $ids_array = explode(",",$ids);
        //注册人数
        if($ids == ""){
            $count_num = 0;
        }else{
            $count_num = count($ids_array);
        }


        $this->assign("count_num",$count_num);

        //充值人数
        $pay_count_num = 0;
        foreach ($ids_array as $k=>$v){
            $pay_log = M("payment_notice")->where("user_id = ".$v." and is_paid = 1".$pay_where)->find();
            if($pay_log){
                $pay_count_num +=1;
            }
        }
        //die;
        $this->assign("pay_count_num",$pay_count_num);

        //充值总额
        $pay_sum_num = 0;
        foreach ($ids_array as $k=>$v){
            $pay_sum_money = M("payment_notice")->field("sum(money) as sum_money")->where("user_id = ".$v." and is_paid = 1".$pay_where)->select();
            $pay_sum_num +=$pay_sum_money[0]['sum_money'];
        }
        $this->assign("pay_sum_num",$pay_sum_num);
	    $this->display();
    }


    //推广用户
    public function statistics_subordinate(){

        $list_data['user_admin_id'] = $_SESSION['user_admin_session']['id'];
        $list_data['display'] = "exe";
        $re = $this->index_user_data($list_data);
        foreach ($re as $k=>$v){
            if($v['user_iden_id'] == 0){
                $for_pay_sum = M("payment_notice")->field("sum(money) as sum_money")->where("user_id = ".$v['id'])->select();
                $re[$k]['sum_money'] = $for_pay_sum[0]['sum_money'];
            }else{
                unset($re[$k]);
            }

        }

        //所有会员
        $where_all = "1=1";
        if($_REQUEST['nick_name']){
            $nick_name = $_REQUEST['nick_name'];
            $this->assign("nick_name",$nick_name);
            $where_all .=" and nick_name = '{$nick_name}'";

        }

        if($_REQUEST['start_time']){
            $start_time = $_REQUEST['start_time'];
            $this->assign("start_time",$start_time);
            $where_all .= " and create_time > ".strtotime($start_time);
        }

        if($_REQUEST['end_time']){
            $end_time = $_REQUEST['end_time'];
            $this->assign("end_time",$end_time);
            $where_all .= " and create_time < ".strtotime($end_time);
        }

        $user_all = M("user")->where($where_all)->select();

        foreach($re as $k=>$v){
            foreach ($user_all as $kk=>$vv){
                if($v['id'] == $vv['id']){
                    $user_list[] = $vv;
                }
            }
        }
        foreach ($user_list as $k=>$v){
            $user_list[$k]["create_time"] = date("Y-m-d H:i:s",$v['create_time']);
            //当前会员的总充值金额
            $for_sum_money = M("payment_notice")->field("sum(money) as sum_money")->where("user_id = ".$v['id']." and is_paid = 1")->select();
            $user_list[$k]["sum_money"] = empty($for_sum_money[0]['sum_money'])?0:$for_sum_money[0]['sum_money'];
        }


        //页数
        $page = empty($_REQUEST['page'])?1:$_REQUEST['page'];
        //每页条数
        $pagesize = 10;
        //总条数
        $count = count($user_list);
        //偏移量，当前页-1乘以每页显示条数
        $start=($page-1)*$pagesize;
        //总页数
        $number_pages = ceil($count/$pagesize);
        $user_list = array_slice($user_list,$start,$pagesize);


        $this->assign('number_pages', $number_pages);
        $this->assign("user_list",$user_list);
        $this->display();

    }


    public function statistics_list(){


        $user_id =$_REQUEST['user_id'];
        $user = M("user")->where("parent_id = ".$user_id)->select();


        if($_REQUEST['start_time']){
            $start_time = $_REQUEST['start_time'];
            $this->assign("start_time",$start_time);
            //用户递归中
            $list_data['create_time_1'] = $start_time;

            //用户查询充值人数
            $pay_where = " and pay_time >".strtotime($start_time);
        }

        if($_REQUEST['end_time']){
            $end_time = $_REQUEST['end_time'];
            $this->assign("end_time",$end_time);
            //用户递归中
            $list_data['create_time_2'] = $end_time;

            //用户查询充值人数
            $pay_where = " and pay_time <".strtotime($end_time);
        }


        foreach ($user as $k=>$v){

            $list_data['user_admin_id'] = $v['id'];
            $list_data['display'] = "exe";
            $re = $this->index_user_data($list_data);

            //循环当前会员的所有下级，查询下级总充值金额和总充值人数
            $vv_sum_money = 0;
            foreach ($re as $kk=>$vv){
                if($vv['user_iden_id'] == 0){
                    $for_pay_sum = M("payment_notice")->field("sum(money) as sum_money")->where("user_id = ".$vv['id']." and is_paid=1 {$pay_where}")->select();
                    $vv_sum_money += $for_pay_sum[0]['sum_money'];
                    $ids[] = $vv['id'];
                }
            }

            //总充值金额
            $user[$k]['sum_money'] = $vv_sum_money;

            $ids = implode(",",$ids);

            $ids_array = explode(",",$ids);
            //当前用户的所有下级注册用户数
            if($ids == ""){
                $user[$k]['user_count_num'] = 0;
            }else{
                $user[$k]['user_count_num'] = count($ids_array);
            }


            //当前用户的所有下级充值人数
            $c_pay_num = 0;
            foreach ($ids_array as $n=>$c){
                $c_pay_log = M("payment_notice")->where("user_id = ".$c." and is_paid = 1 {$pay_where}")->find();
                if($c_pay_log){
                    $c_pay_num +=1;
                }
            }

            $user[$k]['c_pay_num'] = $c_pay_num;

            unset($ids);
            unset($c_pay_num);
        }
        $this->assign("user",$user);
        $this->display();
    }







	public function statistics_recharge(){
        $list_data['user_admin_id'] = $_SESSION['user_admin_session']['id'];
        $list_data['display'] = "exe";
        $re = $this->index_user_data($list_data);
        foreach($re as $k => $v){
            $ids[] = $v['id'];
        }
        $ids = implode(",",$ids);


        $voList = M("payment_notice")->where("user_id in ({$ids})")->select();
        foreach ($voList as $k=>$v){
            $voList[$k]['pay_time'] = date("Y-m-d H:i:s",$v['pay_time']);
        }





        $where = "1=1";
        if($_REQUEST['nick_name']){
            $nick_name = $_REQUEST['nick_name'];
            $where .= " and nick_name = '{$nick_name}' ";
            $this->assign("nick_name",$nick_name);
        }


        if($_REQUEST['parent_id']){
            $parent_id = $_REQUEST['parent_id'];

            $user_bod = M("user")->where("parent_id = ".$parent_id)->select();
            foreach ($user_bod as $k=>$v){
                $user_bod_id[] = $v['id'];
            }
            $user_bod_id = implode(",",$user_bod_id);

            $where .= " and id in ({$user_bod_id}) ";
            $this->assign("parent_id",$parent_id);
        }




        if($_REQUEST['nick_name'] || $_REQUEST['parent_id'] || $_REQUEST['start_time'] || $_REQUEST['end_time'] ){


            $payment_notice_where = "1=1";
            if($_REQUEST['nick_name'] || $_REQUEST['parent_id']){
                $user_where = M("user")->where($where)->select();
                foreach ($user_where as $k=>$v){
                    $user_where_user_id[] = $v['id'];
                }
                $user_where_user_id = implode(",",$user_where_user_id);
                $payment_notice_where .= " and user_id in ({$user_where_user_id})";
            }


            if($_REQUEST['start_time']){
                $start_time = strtotime($_REQUEST['start_time']);
                $payment_notice_where .=" and pay_time > '{$start_time}'";
                $this->assign("start_time",$_REQUEST['start_time']);
            }


            if($_REQUEST['end_time']){
                $end_time = strtotime($_REQUEST['end_time']);
                $payment_notice_where .=" and pay_time < '{$end_time}'";
                $this->assign("end_time",$_REQUEST['end_time']);
            }


            //符合要求的数据
            $who_user_pay = M("payment_notice")->where($payment_notice_where)->select();
            //echo M()->getLastSql();die;

            foreach ($who_user_pay as $k=>$v){
                $who_user_pay[$k]['pay_time'] = date("Y-m-d H:i:s",$v['pay_time']);
            }

            foreach ($who_user_pay as $k=>$v){
                foreach ($voList as $vk=>$vv){
                    if($v['id'] == $vv['id']){
                        $user_list_arr[] = $v;
                    }
                }
            }


        }else{
            $user_list_arr = $voList;
        }


        //页数
        $page = empty($_REQUEST['page'])?1:$_REQUEST['page'];
        //每页条数
        $pagesize = 10;
        //总条数
        $count = count($user_list_arr);
        //偏移量，当前页-1乘以每页显示条数
        $start=($page-1)*$pagesize;
        //总页数
        $number_pages = ceil($count/$pagesize);
        $article = array_slice($user_list_arr,$start,$pagesize);
        $this->assign('number_pages', $number_pages);
        $this->assign ('list', $article );


        $this->display ();
        return;
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













    function bbb($id, $data)
    {

        foreach($data as $k=>$v){
            if($v['parent_id'] == $id){
                $child[] = $v;
                $this->bbb($v['id'],$data);
            }
        }
        dump($child);die;

        /*$child = array();
        foreach ($data as $key => $value) {
            if ($value['parent_id'] == $id) {
                $child[] = $value;
            }
        }

        foreach ($child as $key => $value) {
            $child[$key]['children'] = $this->bbb($value['id'], $data);
        }

        return $child;*/
    }




   /* public function user_sub_id($id,$arr){
        //循环所有会员
        foreach ($arr as $key => $value) {
            if ($value['parent_id'] == $id) {
                $child[] = $value;
            }
        }

        foreach ($child as $key => $value) {
            $child[$key]['children'] = $this->user_sub_id($value['id'], $arr);
        }

        dump($child);die;

    }*/




	/*
     * 充值统计
    */
    public function statistics_recharge_copy(){
        //列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$parameter .= 'is_paid=1&';
		$sql_w .= 'is_paid=1 and ';

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			//unset($map2);
		}		
					
		$model = D ();

		$sql_str = "SELECT sum(money) money,user_id,is_paid" .
		" FROM ".DB_PREFIX."payment_notice WHERE 1=1 ";


//		if($map2['end_time'] > to_timespan('2017-07-01')){
//			$user_refund_sql .= ' and payment_id !=2 ';
//		}

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";

		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'money');
		echo M()->getLastSql();die;
		$this->assign ( 'list', $voList );


		$count = $model->query($sql_str);
		$count = count($count);
		$this->assign("count",$count);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where 1=1 ".$user_refund_sql));
		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
    }

    /**
	 * 提现统计
	 */
	public function statistics_refund()
	{
		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$parameter .= 'is_pay=3&';
		$sql_w .= 'is_pay=3 and ';

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			unset($map2);
		}		
					
		$model = D ();

		$sql_str = "SELECT sum(money) money,sum(ticket) ticket,user_id,is_pay" .
		" FROM ".DB_PREFIX."user_refund WHERE 1=1 ";

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";

		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'money');
		$this->assign ( 'list', $voList );

		$count = $model->query($sql_str);
		$count = count($count);
		$this->assign("count",$count);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_refund where 1=1 ".$user_refund_sql));

		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
	}

	//导出电子表,type=1为充值统计表，否则为提现统计表
	public function export_csv($page = 1)
	{

		$type = $_REQUEST['type'];
		/*admin_ajax_return($type);*/
	
		$pagesize = 10;
		set_time_limit(0);
		$limit = (($page - 1)*intval($pagesize)).",".(intval($pagesize));

		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		if($type)
		{
			$sql_w .= 'is_paid=1 and ';
		}
		else
		{
			$sql_w .= 'is_pay=3 and ';
		}
		

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			//unset($map2);
		}		
		
		if($type)	//充值
		{
			$sql_str = "SELECT t1.user_id,t2.nick_name as name,sum(t1.money) money,t1.is_paid" .
			" FROM ".DB_PREFIX."payment_notice t1,".DB_PREFIX."user t2 WHERE t1.user_id = t2.id and 1=1 ";
		}		
		else
		{
			$sql_str = "SELECT sum(t1.money) as money,t2.nick_name as name,sum(t1.ticket) as ticket,t1.user_id" .
			" FROM ".DB_PREFIX."user_refund t1,".DB_PREFIX."user t2  WHERE  t1.user_id = t2.id and 1=1 ";
		}
		
//		if($map2['end_time'] > to_timespan('2017-07-01')){
//			$user_refund_sql .= ' and payment_id !=2 ';
//		}

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";

        $time ='1970-01-01 16:00:00';
        $sql =$sql_str." limit ";
        $sql .= $limit;
        /*admin_ajax_return($sql);*/
        $list=$GLOBALS['db']->getAll($sql);
/*        admin_ajax_return($sql);*/
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
            $m_config = load_auto_cache('m_config');
            $ticket_name = $m_config['ticket_name']!=''?$m_config['ticket_name']:'秀票';
            if($type)
			$refund_value = array( 'user_id'=>'""','name'=>'""', 'money'=>'""');
			else
			$refund_value = array( 'user_id'=>'""','name'=>'""','ticket'=>'""', 'money'=>'""');	
			if($page == 1)
			{
				if($type)
				$content = iconv("utf-8","gbk","用户ID,用户昵称,充值金额￥");
				else
				$content = iconv("utf-8","gbk","用户ID,用户昵称,提现秀票,提现金额￥");	
				$content = $content . "\n";
			}
			foreach($list as $k=>$v)
			{
				$refund_value['user_id'] = '"' . iconv('utf-8','gbk',$list[$k]['user_id']) . '"';
				$refund_value['money'] = '"' . iconv('utf-8','gbk',$list[$k]['money']) . '"';
				$refund_value['name'] = '"' . iconv('utf-8','gbk',$list[$k]['name']) . '"';
				if(!$type)
				{
					$refund_value['ticket'] = '"' . iconv('utf-8','gbk',$list[$k]['ticket']) . '"';
				}
				$content .= implode(",", $refund_value) . "\n";
			}

			//
			if($type)
			header("Content-Disposition: attachment; filename=recharge_statistics.csv");
			else
			header("Content-Disposition: attachment; filename=refund_statistics.csv");
			echo $content ;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}

	}


}