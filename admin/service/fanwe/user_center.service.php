<?php

class user_centerService{

    /**
     * 我的收益（竞拍+商品）
     * $data = array("user_id"=>$user_id,"year"=>$year,"month"=>$month,"is_pai"=>$is_pai,"type"=>$type,"page"=>$page,"page_size"=>$page_size);
     * return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$page);
     */
    public function income_detail($data){

        $user_id = (int)$data['user_id'];
        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $is_pai = (int)$data['is_pai'];
        $type = (int)$data['type'];
        $page = (int)$data['page'];
        $page_size = (int)$data['page_size'];

        $limit = (($page-1)*$page_size).",".$page_size;

        $condition = "podcast_id=".$user_id;
        if($year>0){
        	$condition .= " and  create_time_y=".$year;
        	
        	if($month>0){
        		$condition .= " and  create_time_m=".$month;
        	}
        }        
        
        if($is_pai>0){
        	$condition .= " and  order_type='pai'";
        }else{
        	$condition .= " and  order_type='shop'";
        }
        
        $ticket=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE ".$condition." and order_status=4"));
        $pending=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE ".$condition." and order_status=3"));
        
        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."goods_order WHERE ".$condition." and order_status=4  GROUP BY create_time_ymd");
        $pages['page'] = $page;
        $pages['has_next'] = 0;
        
        $list = array();
        if($rs_count > 0){
        	$list=$GLOBALS['db']->getAll("SELECT create_time_ymd,SUM(podcast_ticket) as total FROM ".DB_PREFIX."goods_order WHERE ".$condition." and order_status=4  GROUP BY create_time_ymd limit".$limit);
        	foreach($list as $k=>$v){
        		$list[$k]['goods_list']=getAll("SELECT order_status as status,order_sn,podcast_ticket as diamond  FROM ".DB_PREFIX."goods_order WHERE ".$condition." and create_time_ymd=".$v['create_time_ymd']." and  and order_status<6 ");
        		 
        	}
        	$total = ceil($rs_count/$page_size);
        	if($total > $page)
        		$pages['has_next'] = 1;
        }

        return array("status"=>1,"ticket"=>$ticket,"pending"=>$pending,"list"=>$list,"page"=>$pages);
    }
    
    
    public function profit($data){
		$user_id = (int)$data['user_id'];
		$data=array();
		$pai_income_done=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE podcast_id=".$user_id." and  order_type='pai' and order_status=4"));
		//$pai_income_undone=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE podcast_id=".$user_id." and  order_type='pai' and order_status=3"));
		//$pai_income=$pai_income_done+$pai_income_undone;

		$goods_income_done=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE podcast_id=".$user_id." and  order_type='shop' and order_status=4"));
		//$goods_income_undone=intval($GLOBALS['db']->getOne("SELECT SUM(podcast_ticket) FROM ".DB_PREFIX."goods_order WHERE podcast_id=".$user_id." and  order_type='shop' and order_status=3"));
		//$goods_income=$goods_income_done+$goods_income_undone;

		//$data['pai_income']=$pai_income;
		$data['pai_income_done']=$pai_income_done;
		//$data['pai_income_undone']=$pai_income_undone;

		//$data['goods_income']=$goods_income;
		$data['goods_income_done']=$goods_income_done;
		//$data['goods_income_undone']=$goods_income_undone;

		return $data;
    }


	//竞拍收入明细
	//$type  0 --已结算
	//$type  1 --待结算
	public function pai_income_details($data){
		$user_id = intval($data['user_id']);
		$year = intval($data['year']);
		$month = intval($data['month']);
		$type = intval($data['type']);
		$is_pai = intval($data['is_pai']);

		$user = $GLOBALS['db']->getOne("SELECT * FROM ".DB_PREFIX."user WHERE id=".$user_id);

		$count = 'SUM(podcast_ticket)';
		$field = 'go.order_status as status,go.order_sn,go.podcast_ticket as diamond,pg.name,go.create_time_ymd';
		$goods_order = DB_PREFIX.'goods_order';//goods_order--数据表
		$go_pg = DB_PREFIX.'goods_order as go,'.DB_PREFIX.'pai_goods as pg';//goods_order,pai_goods--数据表

		$total_revenue = "order_status=4 and create_time_y={$year} and create_time_m={$month} and podcast_id={$user_id}";//月份累计结算条件
		$for_the = "order_status in(2,3) and create_time_y={$year} and create_time_m={$month} and podcast_id={$user_id}";//月份待结算条件
		if($type == 0){
			$details_conditions = "go.id=pg.order_id and go.order_status=4 and go.create_time_y={$year} and go.create_time_m={$month} and go.podcast_id={$user_id}";
		}elseif($type == 1){
			$details_conditions = "go.id=pg.order_id and go.order_status in(2,3) and go.create_time_y={$year} and go.create_time_m={$month} and go.podcast_id={$user_id}";
		}

		if($user){
			$cumulative = $GLOBALS['db']->getOne("SELECT $count FROM $goods_order WHERE $total_revenue;");//累计收入
			if(!$cumulative){
				$cumulative = 0;
			}
			$settlement = $GLOBALS['db']->getOne("SELECT $count FROM $goods_order WHERE $for_the;");//待结算
			if(!$settlement){
				$settlement = 0;
			}
			$details = $GLOBALS['db']->getAll("SELECT $field FROM $go_pg WHERE $details_conditions;");//详情
			if($details){
				$info = array();
				foreach($details as $key => $vaule){
					$time=strtotime($vaule['create_time_ymd']);
					$ri=date('m-d',$time);
					$zhou=date('N',$time);
					switch ($zhou) {
						case '1':
							$zhou = '星期一';
							break;
						case '2':
							$zhou = '星期二';
							break;
						case '3':
							$zhou = '星期三';
							break;
						case '4':
							$zhou = '星期四';
							break;
						case '5':
							$zhou = '星期五';
							break;
						case '6':
							$zhou = '星期六';
							break;
						default:
							$zhou = '星期日';
							break;
					}
					//$info[$ri.'-'.$zhou]['time'] = $ri.'-'.$zhou;
					$info[$ri.'-'.$zhou]['total']+=$vaule['diamond'];
					$info[$ri.'-'.$zhou]['goods_list'][] = $vaule;
				}
				foreach($info as $k => $v){
					$info[$k]['time'] = $k;
				}
				$info = array_values($info);
			}else{
				$info = array();
			}
		}

		return array("status"=>1,"cumulative"=>$cumulative,"settlement"=>$settlement,"details"=>$info);
	}



}


?>