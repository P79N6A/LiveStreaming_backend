<?php
	class pai_userService{
		/**
		 * 观众-获得某竞拍记录列表
		 * $data = array("id"=>$pai_id,"user_id"=>$user_id,"limit"=>"0,10");
		 * return array("rs_count"=>$rs_count,"list"=>$limit);
		 */
		function pailogs($data){
			
			$pai_id = (int)$data['pai_id'];
			$page = (int)$data['page'];
			$page_size = (int)$data['page_size'];
			
			$limit = (($page-1)*$page_size).",".$page_size;

			$status = $GLOBALS['db']->getOne("SELECT status FROM ".DB_PREFIX."pai_goods WHERE id =".$pai_id);
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_log  WHERE pai_id =".$pai_id);
			$list = array();
			//print_r($rs_count);echo "<hr/>";
			$pages['page'] = $page;
			$pages['has_next'] = 0;
				
			if($rs_count > 0){
				$list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_log WHERE pai_id=".$pai_id." ORDER BY id DESC");
				//新增头像字段下发
				foreach($list as $k=>$v){
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
					$user_redis = new UserRedisService();
					$fields = array('head_image');
					$user_info=$user_redis->getRow_db(intval($v['user_id']),$fields);
					$list[$k]['head_image'] = get_spec_image($user_info['head_image']);
					
				}
				
				//print_r($list);echo "<hr/>";
				$total = ceil($rs_count/$page_size);
				if($total > $page)
					$pages['has_next'] = 1;
			}
		
			return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages,'status'=>$status);
		}
		/**
		 * 观众-获得参与的竞拍列表
		 * 
		 */
		 function goods($data){
		 	
			$user_id  = (int)$data['user_id'];
			$is_true = (int)$data['is_true'];
			$page = (int)$data['page'];
			$page_size = (int)$data['page_size'];
			
			$limit = (($page-1)*$page_size).",".$page_size;
			$rs_count = $GLOBALS['db']->getOne("SELECT count(pg.id) FROM ".DB_PREFIX."pai_goods AS pg LEFT JOIN ".DB_PREFIX."pai_join as pj ON pj.pai_id = pg.id WHERE pg.is_true =".$is_true." AND pj.user_id =".$user_id);
			//print_r("SELECT count(pg.id) FROM ".DB_PREFIX."pai_goods AS pg LEFT JOIN ".DB_PREFIX."pai_join as pj ON pj.user_id = pg.user_id WHERE pg.is_true =".$is_true." AND pg.user_id =".$user_id);echo"<hr/>";
			//print_r($rs_count);echo"<hr/>";
			$list = array();
			
			$pages['page'] = $page;
			$pages['has_next'] = 0;
			
			if($rs_count > 0){
				$list = $GLOBALS['db']->getAll("SELECT pg.*,pj.order_id as order_id,pj.pai_status as pai_status ,pj.order_status as order_status,pj.pai_diamonds as pai_diamonds,pj.status as join_status FROM ".DB_PREFIX."pai_goods AS pg LEFT JOIN ".DB_PREFIX."pai_join as pj ON pj.pai_id = pg.id WHERE pg.is_true =".$is_true." AND pj.user_id =".$user_id." ORDER BY id DESC LIMIT ".$limit);
				$total = ceil($rs_count/$page_size);
				if($total > $page)
					$pages['has_next'] = 1;
				
				$j_datas =array();
				foreach($list as $k=>$v){
					$j_datas[$v['id']] = $user_id;
				}
				
				
				$ulist = $this->p_join($j_datas,"OR");
				
				foreach($list as $k=>$v){
					$list[$k]['user_pai_info'] = $ulist[$v['id']];
					if (intval($v['order_id']>0)) {
						$order_date = $GLOBALS['db']->getRow("SELECT order_status_time,order_sn,refund_platform,refund_reason FROM ".DB_PREFIX."goods_order WHERE id=".intval($v['order_id'])."");
						$order_status_time=$order_date['order_status_time'];
						$list[$k]['refund_platform']=$order_date['refund_platform'];
						$list[$k]['refund_reason']=$order_date['refund_reason'];
						if (intval($order_status_time)>0) {
							$list[$k]['order_status_time'] = $order_status_time;
						}
						
						$list[$k]['order_sn'] = strim($order_date['order_sn']);

						if(PAI_REAL_BTN == 1 && $is_true == 1){
							$list[$k]['shopinfo']=array('shop_name'=>$v['podcast_name']);
						}

					}else {
						$list[$k]['order_status_time'] = 0;
						$list[$k]['order_sn'] = 0;
					}
					
				}
			}
			return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);		 
		 }
		 /**
		 * 观众-获得某竞拍竞拍详情
		 * 
		 */
		 function goods_detail($data){

		 	$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
			$get_joindata = (int)$data['get_joindata'];
			$get_pailogs = (int)$data['get_pailogs'];
			$page = (int)$data['page'];
			$page_size = (int)$data['page_size'];
			
			//print_r("-goods_detail_data-");echo "<hr/>";
			//print_r($data);echo "<hr/>";
		
			$limit = (($page-1)*$page_size).",".$page_size;
			$count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_goods  WHERE id =".$pai_id);
			//print_r("SELECT count(*) FROM ".DB_PREFIX."pai_goods  WHERE id =".$pai_id." AND user_id =".$user_id);echo"<hr/>";
			//print_r($count);echo"<hr/>";
			$list = array();
			
			$pages['page'] = $page;
			$pages['has_next'] = 0;
			
			$list['has_join'] = 0;

			if($count > 0){
				$list['info'] = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods AS pg  WHERE pg.id =".$pai_id);
				//pai_list
				if ($get_pailogs==1) {
				
					$page = (int)$data['page'];
					$page_size = (int)$data['page_size'];
				
					//$pai_list=getAll("SELECT * FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$podcast_id." and is_true=".$is_true." ORDER BY id ASC limit ".$limit);
					$rs = FanweServiceCall("pai_user","pailogs",array("pai_id"=>$pai_id,"page"=>$page,"page_size"=>$page_size));
					$list['pai_list']=$rs['list'];
					$rs_count=$rs['rs_count'];
					$pages=$rs['page'];
				}
				/*
				if($get_pailogs){
					$rs_count = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_log WHERE pai_id=".$pai_id."");
					$list['rs_count'] = $rs_count;
					$pai_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_log WHERE pai_id=".$pai_id." ORDER BY pai_diamonds DESC LIMIT ".$limit);
					$rs_count = count($pai_list);
					if($rs_count > 0){
						$total = ceil($rs_count/$page_size);
						if($total > $page)
							$pages['has_next'] = 1;
					}
					$list['pai_list'] = $pai_list;
				}else{
					$list['pai_list'] = array();
				}*/

				//join_data
				if($get_joindata){
					$list['join_data'] = $GLOBALS['db']->getRow("SELECT pj.* FROM ".DB_PREFIX."pai_join as pj  WHERE pj.pai_id =".$pai_id." AND pj.user_id =".$user_id);
					if($list['join_data']){
						$list['has_join'] = 1;
					}else{
						$list['has_join'] = 0;
					}
				}else{
					$join_data = $GLOBALS['db']->getRow("SELECT pj.* FROM ".DB_PREFIX."pai_join as pj  WHERE pj.pai_id =".$pai_id." AND pj.user_id =".$user_id);
					if($join_data){
						$list['has_join'] = 1;
					}else{
						$list['has_join'] = 0;
					}
					
				}			
				
				if (SHOPPING_GOODS==1&&$list['info']['is_true']==1&&$list['info']['goods_id']>0) {
					$list['info']['url'] =  SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_podcast&act=getauction_commodity_detail&itype=shop&goods_id='.$list['info']['goods_id'];

					$goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE id=".$list['info']['goods_id']);
					if($goods_info){
						$root['pai_goods'] = array();
						if($goods_info['imgs_details'] != ''){
							foreach(json_decode($goods_info['imgs_details'],1) as $k => $v){
								$img_info = getimagesize($v);
								$goods_detail = array();
								$goods_detail['image_width'] =$img_info[0];
								$goods_detail['image_height'] =$img_info[1];
								$goods_detail['image_url'] =$v;
								$root['goods_detail'][] = $goods_detail;

							}
						}else{
							$root['goods_detail'][] = array();
						}

					}else{
						$root['pai_goods'] = array();
						$root['goods_detail'] = array();
					}
					$list['info']['commodity_detail'] =$root;
				
				}else{
					$list['info']['url'] =  '';
					$list['info']['commodity_detail'] =array('pai_goods'=>array(),'goods_detail'=>array());
				}
			}else{
				return array("status"=>"10008","error"=>"商品不存在");
			}
			
			return array("status"=>1,"rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
		 }
		 /**
		 * 提交保证金
		 * 
		 */
		 function dojoin($data){
		 	//初始化参数
		 	$root = array("status"=>1,'error'=>'');
		 	
		 	$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
			$consignee = (string)$data['consignee'];
			$consignee_mobile = (string)$data['consignee_mobile'];
			$consignee_district = (string)$data['consignee_district'];
			$consignee_address = (string)$data['consignee_address'];
			///判断是否提交过保证金
			$p_join =array(
				$pai_id =>$user_id
			);
			$p_join_list = $this->p_join($p_join,"AND");
			if($p_join_list){
				return array("status"=>10050,'error'=>'已提交过保证金');
			}
			
			$pai =array();
			$pai['pai_id']=$pai_id;
			$pai_info =  $this->p_goodsinfo($pai);
			
			if($pai_info['status']!=1){
				$root['status']=$pai_info['status'];
				$root['error']='商品不存在';
				return $root;
			}else{
				$pai_info = $pai_info['info'];
			}
			if ($pai_info['podcast_id']==$user_id) {
				$root['status']=0;
				$root['error']='不能参与自己发起的竞拍';
				return $root;
			}
			//减少用户秀豆			
			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
			$user_redis = new UserRedisService();
			$sql = "update ".DB_PREFIX."user set diamonds = diamonds - ".intval($pai_info['bz_diamonds'])." where id = '".$user_id."' and diamonds >= ".intval($pai_info['bz_diamonds']);
			$GLOBALS['db']->query($sql);
			$result = false;
			/*if($GLOBALS['db']->affected_rows()){			
				user_deal_to_reids(array(intval($user_id)));
				$account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');
			}else{
				$root['error'] = "用户秀豆不足";
				$root['status'] = 0;
				return $root;
			}*/
			
			if($GLOBALS['db']->affected_rows()){
				//查询用户秀豆
				user_deal_to_reids(array(intval($user_id)));
				$account_diamonds = $user_redis->getOne_db($user_id,'diamonds');
				
				//提交保证金
				$bz_diamonds = $pai_info['bz_diamonds'];
				$time = NOW_TIME;
				$data = array(
					'pai_id' => $pai_id,
					'user_id' => $user_id,
					'bz_diamonds'=>$bz_diamonds,
					'status' => 0,
					'create_time' => $time,
					'create_date' => to_date($time,'Y-m-d H:i:s'),
					'create_time_ymd'  => to_date($time,'Y-m-d'),
					'create_time_y'  => to_date($time,'Y'),
					'create_time_m'  => to_date($time,'m'),
					'create_time_d'  => to_date($time,'d'),
					'consignee'=>$consignee,
					'consignee_mobile'=>$consignee_mobile,
					'consignee_district'=>$consignee_district,
					'consignee_address'=>$consignee_address,
					'order_id' => 0,
					'order_status' => 0,
					'order_time' => 0,
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."pai_join",$data);
				//会员账户 秀豆变更日志表
				$diamonds_log_data = array(
					'pai_id' => $pai_id,
					'user_id' => $user_id,
					'diamonds'=>$bz_diamonds,//变更数额
					'account_diamonds'=>$account_diamonds,//账户余额
					'memo' =>$pai_info['name'].'提交保证金',//备注
					'create_time' => $time,
					'create_date' => to_date($time,'Y-m-d H:i:s'),
					'create_time_ymd'  => to_date($time,'Y-m-d'),
					'create_time_y'  => to_date($time,'Y'),
					'create_time_m'  => to_date($time,'m'),
					'create_time_d'  => to_date($time,'d'),
					'type' =>1,//1 提交保证金
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_diamonds_log",$diamonds_log_data);
				
				//写入用户日志
				$data = array();
				$data['diamonds'] = $bz_diamonds;
				$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
				$log_msg = $pai_info['name'].'提交保证金';//备注
				account_log_com($data,$user_id,$log_msg,$param);

			}else{
				$status = '10013';
				$error = '提交保证金失败';
			}
			return $root;
			
		 }
		 /**
		 * 竞拍出价
		 * 
		 */
		 function dopai($data){
		 	$status = 1;
		 	$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
			$pai_diamonds = (int)$data['pai_diamonds'];
			
			
		 	$list = array();
		 	//查询goods
		 	$pai_goods_data = $GLOBALS['db']->getRow("SELECT pg.id,pg.pai_nums,pg.jj_diamonds,pg.bz_diamonds,pg.qp_diamonds,pg.podcast_id,pg.status FROM ".DB_PREFIX."pai_goods AS pg  WHERE pg.id =".$pai_id." and pg.status=0  and pg.is_delete=0 and  pg.create_time+pg.pai_time*3600+pg.now_yanshi*pg.pai_yanshi*60 >".NOW_TIME);
		 	
			if (intval($pai_goods_data['status']>0)) {
				$status ='10014';
			 	return array("status"=>$status,"data"=>array());
			}
		 	
		 	
			//获取user信息
		 	$join_data = $GLOBALS['db']->getRow("SELECT pj.id,pj.user_id,u.nick_name as user_name,pj.pai_number,pj.pai_diamonds FROM ".DB_PREFIX."pai_join as pj LEFT JOIN ".DB_PREFIX."user as u on u.id=pj.user_id WHERE pj.pai_id =".$pai_id." AND pj.user_id =".$user_id);
		 	if(!$join_data){
		 		return array("status"=>10052,"data"=>array());
				exit();
		 	}
		 	if($pai_diamonds<intval($pai_goods_data['qp_diamonds'])){
		 		$pai_diamonds = intval($pai_goods_data['qp_diamonds']);
		 	}
			if (intval($pai_goods_data['last_pai_diamonds'])>0&&$pai_diamonds<intval($pai_goods_data['last_pai_diamonds'])) {
				$data=array();
				$data['pai_diamonds']=$pai_goods_data['last_pai_diamonds'];
				return array("status"=>10053,"data"=>$data);
				exit();
			}
			//log_result('==dopai==');
			//log_result($pai_diamonds);
		 	//写入 log
	 		$time = NOW_TIME;
	 		$log_data =array(
	 			'podcast_id' =>$pai_goods_data['podcast_id'],
				'user_id' =>$user_id,
				'user_name' =>$join_data['user_name'],
				'pai_id' =>$pai_id,
				'bz_diamonds' =>$pai_goods_data['bz_diamonds'],
				'qp_diamonds' =>$pai_goods_data['qp_diamonds'],
				'jj_diamonds' =>$pai_goods_data['jj_diamonds'],
				'pai_diamonds' =>$pai_diamonds + $pai_goods_data['jj_diamonds'],
				'pai_time_ms' =>get_microtime(),
				'pai_time' =>$time,
				'pai_date' =>to_date($time,'Y-m-d H:i:s'),
				'pai_time_ymd' =>to_date($time,'Y-m-d'),
				'pai_time_y' =>to_date($time,'Y'),
				'pai_time_m' =>to_date($time,'m'),
				'pai_time_d' =>to_date($time,'d'),
				'status' =>0,
	 		);
	 		//log_result($log_data);
	 		$log_data['pai_sort'] = 0;
	 		$insert_id = 0;
	 		//do{
		 		$log_data['pai_sort'] = $log_data['pai_sort']+1;
		 		$GLOBALS['db']->autoExecute(DB_PREFIX."pai_log",$log_data);
		 		$insert_id = $GLOBALS['db']->insert_id();
	 		//}while($insert_id == 0 && $log_data['pai_sort'] <=3);
		 	
		 	if($insert_id>0){
		 		
		 		//更新pai_join
		 		$pai_info = array();
			 	$pai_info['user_id'] = $join_data['user_id'];
			 	$pai_info['user_name'] = $join_data['user_name'];
			 	$data = array(
			 		'pai_number' => $join_data['pai_number']+1,
			 		'pai_diamonds' => $log_data['pai_diamonds'],
			 	);
			 	$where = ' id = '.$join_data['id'];
			 	$result = $GLOBALS['db']->autoExecute(DB_PREFIX."pai_join",$data,'UPDATE',$where);
			 	
			 	if($result!=false){
			 		//获取最新的 pai_number，pai_diamonds
			 		$join_data = $GLOBALS['db']->getRow("SELECT pj.pai_number,pj.pai_diamonds FROM ".DB_PREFIX."pai_join as pj WHERE pj.id =".$join_data['id']);
			 		$pai_info['pai_number'] = $join_data['pai_number'];
			 		$pai_info['pai_diamonds'] = $join_data['pai_diamonds'];
			 		
			 		//更新goods pai_nums
			 		//$GLOBALS['db']->autoExecute(DB_PREFIX."pai_goods",$goods_data,'UPDATE',$goods_where);
			 		$pai_goods_sql = "UPDATE ".DB_PREFIX."pai_goods  SET  pai_nums=pai_nums+1 ,last_pai_diamonds=".$log_data['pai_diamonds']." ,last_user_id=".$pai_info['user_id']." ,last_user_name='".$pai_info['user_name']."' WHERE id =".$pai_id;
					$GLOBALS['db']->query($pai_goods_sql);
					
					//获取最新pai_goods信息
					//$list['info'] = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods AS pg  WHERE pg.id =".$pai_id);
					//更新延时 时间(待测试)
					if(PAI_YANCHI_MODULE==0){
						$now_yanshi_sql = "update  ".DB_PREFIX."pai_goods SET  now_yanshi=now_yanshi+1 where id=".$pai_goods_data['id']." AND (if(pai_less_time=0,60,pai_less_time)) >= (pai_time*3600  + create_time + now_yanshi*pai_yanshi*60) - ".get_gmtime()." and now_yanshi<max_yanshi";

					}else{
						$now_yanshi_sql = "update ".DB_PREFIX."pai_goods SET  now_yanshi=now_yanshi+1,end_time=pai_yanshi*60-(end_time-".get_gmtime().")+end_time where id=".$pai_goods_data['id']." and (pai_yanshi*60) > (end_time-".get_gmtime().") and now_yanshi<max_yanshi";

					}
					$GLOBALS['db']->query($now_yanshi_sql);
					$is_yanshi=0;
					if($GLOBALS['db']->affected_rows()){
						$is_yanshi=1;
					}
					
					//推送
					$pai_goods_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id." ");
					$list['info'] =$pai_goods_info;
					$video_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."video where user_id=".intval($pai_goods_info['podcast_id'])."  and live_in =1");
					//流拍房间内推送
					$ext = array();
					$ext['type'] = 28;
					$ext['room_id'] = intval($video_info['id']);
					$ext['pai_id'] = $pai_id;
					$ext['post_id'] = intval($pai_goods_info['podcast_id']);
					
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
					$user_redis = new UserRedisService();
					$fields = array('head_image','user_level','v_type','v_icon','nick_name');
					
					$ext['user'] = $user_redis->getRow_db($user_id,$fields);
					$ext['user']['user_id'] = $user_id;
					$ext['user']['head_image'] = get_spec_image($ext['user']['head_image']);
					$ext['pai_sort'] = $pai_info['pai_number'];
					$ext['pai_diamonds'] = $pai_info['pai_diamonds'];
					$ext['yanshi']=$is_yanshi;

					if(PAI_YANCHI_MODULE==0){
						$ext['pai_left_time'] = $pai_goods_info['pai_time'] * 3600 + $pai_goods_info['create_time'] + $pai_goods_info['now_yanshi'] * $pai_goods_info['pai_yanshi'] * 60 - NOW_TIME;

					}else{
						$ext['pai_left_time'] = $pai_goods_info['end_time'] - NOW_TIME;
					}

						//$ext['desc'] = $ext['user']['nick_name'].'出价'.$ext['pai_diamonds'];
					$ext['desc'] = '参与了'.$ext['pai_sort'].'次竞拍，出价'.$ext['pai_diamonds'];
					#构造高级接口所需参数
					$tim_data=array();
					$tim_data['ext']=$ext;
					$tim_data['podcast_id']=strim($pai_goods_info['podcast_id']);
					$tim_data['group_id']=strim($video_info['group_id']);
					get_tim_api($tim_data);
					
			 	}else{
			 		$status ='10014';
			 		return array("status"=>$status,"data"=>array());
			 	}
		 	}else{
		 		$status ='10014';
			 	return array("status"=>$status,"data"=>array());
		 	}
		 	$list['pai_info'] = $pai_info; 	
		 	return array("status"=>$status,"data"=>$list);
		 }
		 /**
		 * 参与竞拍的人 - 列表
		 * 
		 */
		 function joins($data){
		 	$pai_id = (int)$data['pai_id'];
			$page = (int)$data['page'];
			$page_size = (int)$data['page_size'];
		
			$limit = (($page-1)*$page_size).",".$page_size;
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_join  WHERE pai_id =".$pai_id);
			//print_r("SELECT count(*) FROM ".DB_PREFIX."pai_join  WHERE pai_id =".$pai_id);echo"<hr/>";
			//print_r($rs_count);echo"<hr/>";
			$list = array();
			$pages['page'] = $page;
			$pages['has_next'] = 0;
			
			if($rs_count > 0){
				$list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." ORDER BY id ASC limit ".$limit);
				//print_r("=list==");echo "<hr/>";
				//print_r($list);echo "<hr/>";
				$total = ceil($rs_count/$page_size);
				if($total > $page)
					$pages['has_next'] = 1;
			}
			
			return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
		 }
		 /**
		 * 参与竞拍的某个人
		 * 
		 */
		 function getjoin($data){
		 	$status = 1;
		 	$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
			//$user_id = (int)$data['user_id'];
			
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_join  WHERE pai_id =".$pai_id." AND user_id =".$user_id);
			//print_r("SELECT count(*) FROM ".DB_PREFIX."pai_join  WHERE pai_id =".$pai_id." AND user_id =".$user_id);echo"<hr/>";
			//print_r($rs_count);echo"<hr/>";
			if($rs_count > 0){
				$list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." AND user_id =".$user_id);
				if($list['consignee_district']==''){
					$list['consignee_district']='';
				}
				//print_r("=list==");echo "<hr/>";
				//print_r($list);echo "<hr/>";
			}else{
				return array("status"=>'10011');
			}

			return array("status"=>$status,"list"=>$list);
			
		 }
		 /**
		 * 支付单支付成功
		 * 
		 */
		function  pay_diamonds($data){
			$status = 1;
			$order_sn = trim($data['order_sn']);
			$user_id  = (int)$data['user_id'];
			$time = NOW_TIME;

			//逻辑处理开始
			$order_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
			if (!$order_info) {
				$status=10037;
				return array("status"=>$status);
			}elseif (intval($order_info['order_status'])==6){
				$status=10063;
				return array("status"=>$status);
			}elseif (intval($order_info['order_status'])!=1){
				$status=10054;
				return array("status"=>$status);
			}


			$pai_id=intval($order_info['pai_id']);
			$pay_diamonds=intval($order_info['goods_diamonds']);
			$podcast_id=intval($order_info['podcast_id']);

			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
			$user_redis = new UserRedisService();

			//减少用户秀豆
			$sql = "update ".DB_PREFIX."user set diamonds = diamonds - ".$pay_diamonds."  where id = '".$user_id."' and diamonds >= ".$pay_diamonds;
			$GLOBALS['db']->query($sql);
			$result = false;
			if($GLOBALS['db']->affected_rows()){

				user_deal_to_reids(array(intval($user_id)));
				$account_diamonds = $user_redis->getOne_db(intval($user_id), 'diamonds');

				//$update_diamonds_status = $user_redis->inc_field($user_id,'use_diamonds',$pay_diamonds);

				//if($update_diamonds_status!=false){
				//查询用户秀豆
				//$account_diamonds = $user_redis->getOne_db($user_id,'use_diamonds');

				//会员账户 秀豆变更日志表
				$diamonds_log_data = array(
					'pai_id' => $pai_id,
					'user_id' => $user_id,
					'diamonds'=>$pay_diamonds,//变更数额
					'account_diamonds'=>$account_diamonds,//账户余额
					'memo' =>'支付竞拍订单，订单号：'.$order_sn,//备注
					'create_time' => $time,
					'create_date' => to_date($time,'Y-m-d H:i:s'),
					'create_time_ymd'  => to_date($time,'Y-m-d'),
					'create_time_y'  => to_date($time,'Y'),
					'create_time_m'  => to_date($time,'m'),
					'create_time_d'  => to_date($time,'d'),
					'type' =>2,//竞拍订单订单付款
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_diamonds_log",$diamonds_log_data);

				//写入用户日志
				$data = array();
				$data['diamonds'] = $pay_diamonds;
				$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
				$log_msg = '支付竞拍订单，订单号：'.$order_sn;//备注
				account_log_com($data,$user_id,$log_msg,$param);

				//}
				$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE order_id=".$order_info['id']);
				$is_true=$info['is_true'];
				if(OPEN_GOODS == 1&&$info['is_true']==1){
					$goods_id = $info['goods_id'];
					$user_name = $GLOBALS['db']->getOne("SELECT nick_name FROM ".DB_PREFIX."user WHERE id=".$user_id);
					$zb_id = $info['podcast_id'];
					$zb_name = $GLOBALS['db']->getOne("SELECT nick_name FROM ".DB_PREFIX."user WHERE id=".$zb_id);
					//for($i=1;$i<6;$i++){
					$rs = create_auction_order($user_id,$goods_id,$user_name,$zb_id,$zb_name);
					if($rs['status'] == 1){
						//$i=6;
						$order_sn = $rs['orderNo'];
						$pai_diamonds = $rs['totalPayPrice'];
					}
					//}
					$sql = "UPDATE ".DB_PREFIX."goods_order SET order_sn=".$order_sn." WHERE id=".$order_info['id'];
					$GLOBALS['db']->query($sql);
				}


				$result = true;
			}else{
				$result = false;
			}



			//逻辑处理结束
			if($result==false){
				$status = '10062';
			}else{

				//数据更新 更新pai_goods/goods_order/pai_join
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$fields = array('head_image','user_level','v_type','v_icon','nick_name');

				$buyer_data=$user_redis->getRow_db($user_id,$fields);

				$pai_goods_update=array();
				$pai_goods_update['user_id']=$user_id;
				$pai_goods_update['user_name']=$buyer_data['nick_name'];
				$pai_goods_update['status']=4;
				$pai_goods_update['pay_time']=to_date($time,'Y-m-d H:i:s');
				$pai_goods_update['order_status']=2;//待收货
				$GLOBALS['db']->autoExecute(DB_PREFIX."pai_goods", $pai_goods_update, $mode = 'UPDATE', "id=".$pai_id);

				$date_time=$GLOBALS['db']->getOne("SELECT date_time FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id);


				$goods_order_update=array();
				$goods_order_update['order_status']=2;//待收货
				$goods_order_update['pay_time']=to_date($time,'Y-m-d H:i:s');
				$goods_order_update['order_status_time']=strtotime($date_time) - 8*3600;//更新为约会时间
				if($is_true == 1){
					$goods_order_update['order_status_time']=NOW_TIME;//付款时间
				}
				$goods_order_update['pay_diamonds']=$pay_diamonds;//付款金额
				$GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $goods_order_update, $mode = 'UPDATE', "order_sn='".$order_sn."'");

				$pai_join_update=array();
				$pai_join_update['pai_status']=4;
				$pai_join_update['order_status']=2;
				$pai_join_update['pay_time']=to_date($time,'Y-m-d H:i:s');
				$GLOBALS['db']->autoExecute(DB_PREFIX."pai_join", $pai_join_update, $mode = 'UPDATE', "order_id=".intval($order_info['id']));

				$pai_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);
				//退回保证金
				$user_list = $GLOBALS['db']->getAll("SELECT id,user_id,bz_diamonds,status,pai_status FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id);
				$user_ids=array();
				foreach ( $user_list as $k => $v )
				{
					$user_ids[]=$v['user_id'];

					//退还保证金 bz_diamonds  不为超时即退保证金
					if(intval($v['status'])==0 && intval($v['pai_status'])==3 && $pai_info['last_user_id'] == $v['user_id'] ){

						$sql = "update ".DB_PREFIX."pai_join set status = 2 where id=".intval($v['id'])." ";
						$GLOBALS['db']->query($sql);

					}else{

						/*fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
						$user_redis = new UserRedisService();
						$user_redis->lock_diamonds(intval($v['user_id']),intval($v['bz_diamonds']));

						$account_diamonds = $user_redis->getOne_db(intval($v['user_id']),'use_diamonds');*/
						fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
						fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
						$user_redis = new UserRedisService();

						$sql = "update ".DB_PREFIX."user set diamonds = diamonds + ".intval($v['bz_diamonds'])." where id = ".intval($v['user_id']);
						$GLOBALS['db']->query($sql);
						user_deal_to_reids(array(intval($v['user_id'])));
						$account_diamonds = $user_redis->getOne_db(intval($v['user_id']), 'diamonds');


						if (intval($v['pai_status'])==2) {
							$sql = "update ".DB_PREFIX."pai_join set status = 1,pai_status = 0 where id=".intval($v['id'])." ";
							$GLOBALS['db']->query($sql);
						}else {
							$sql = "update ".DB_PREFIX."pai_join set status = 1 where id=".intval($v['id'])." ";
							$GLOBALS['db']->query($sql);
						}

						//会员账户 秀豆变更日志表
						$diamonds_log_data = array(
							'pai_id' => $pai_id,
							'user_id' => intval($v['user_id']),
							'diamonds'=>intval($v['bz_diamonds']),//变更数额
							'account_diamonds'=>$account_diamonds,//账户余额
							'memo' =>$pai_info['name'].'退还保证金',//备注
							'create_time' => $time,
							'create_date' => to_date($time,'Y-m-d H:i:s'),
							'create_time_ymd'  => to_date($time,'Y-m-d'),
							'create_time_y'  => to_date($time,'Y'),
							'create_time_m'  => to_date($time,'m'),
							'create_time_d'  => to_date($time,'d'),
							'type' =>1,//1 提交保证金
						);
						$GLOBALS['db']->autoExecute(DB_PREFIX."user_diamonds_log",$diamonds_log_data);

						//写入用户日志
						$data = array();
						$data['diamonds'] = intval($v['bz_diamonds']);
						$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
						$log_msg = $pai_info['name'].'退还保证金';//备注
						account_log_com($data,intval($v['user_id']),$log_msg,$param);

					}

				}


				//更新video
				$sql = "update ".DB_PREFIX."video set pai_id = 0 where user_id=".$podcast_id." ";
				$GLOBALS['db']->query($sql);

				$video_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."video where user_id=".$podcast_id."  and live_in =1");
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
				$video_redis = new VideoRedisService();
				$video_data=array();
				$video_data['pai_id']=0;
				$re =   $video_redis->update_db(intval($video_info['id']),$video_data);


				//流拍房间内推送
				$ext = array();
				$ext['type'] = 29;
				$ext['room_id'] = intval($video_info['id']);
				$ext['pai_id'] = $pai_id;
				$ext['post_id'] = $podcast_id;
				$ext['desc'] = "";
				/*$buyer_data['user_id']=$user_id;
				$buyer_data['type']=4;
				$buyer_data['left_time']=0;
				$buyer_data['pai_diamonds']=$pay_diamonds;
				$ext['buyer'][] = $buyer_data;
				*/
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$fields = array('head_image','user_level','v_type','v_icon','nick_name');
				$ext['user']=array();

				$ext['buyer']=array();
				$user_list_all=$GLOBALS['db']->getAll("select user_id,pai_status,order_id,order_status,pai_diamonds,order_time from ".DB_PREFIX."pai_join where pai_id=".$pai_id." and pai_diamonds>0  ORDER BY pai_diamonds DESC limit 0,3");
				foreach($user_list_all as $k1=>$v1){
					$buyer_data=array();
					if (intval($v1['user_id'])>0) {
						$buyer_data=$user_redis->getRow_db(intval($v1['user_id']),$fields);
						$buyer_data['user_id'] = intval($v1['user_id']);
						$buyer_data['head_image'] = get_abs_img_root($buyer_data['head_image']);
						$buyer_data['type']=intval($v1['pai_status']);
						if ($buyer_data['type']==1) {
							$buyer_data['left_time']=$v1['order_time']+MAX_PAI_PAY_TIME-NOW_TIME;
							$order_sn=$GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."goods_order where id=".intval($v1['order_id'])." ");
							//$buyer_data['pay_url']=SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_user&act=order&order_sn='.$order_sn;
							$buyer_data['goods_name']=$v['name'];
							$buyer_data['order_sn']=$order_sn;
							if ($v['imgs']!='') {
								$v['imgs']=json_decode($v['imgs']);
								foreach($v['imgs'] as $k2=>$v2){
									//$buyer_data['goods_icon']=get_domain().APP_ROOT.$v2;
									$buyer_data['goods_icon']=get_spec_image($v2);
									break;
								}
							}else{
								$buyer_data['goods_icon']="";
							}

						}else{
							if ($buyer_data['type']==4) {
								//$ext['desc'] = '恭喜用户'.$buyer_data['nick_name'].'支付'.intval($v1['pai_diamonds']).'成功拍得'.$v['name'];
								$pai_goods_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);
								$ext['desc'] = '支付'.intval($v1['pai_diamonds']).'成功拍得'.$pai_goods_info['name'];

								$ext['user'] = $buyer_data;

							}
							$buyer_data['left_time']=0;
						}
						$buyer_data['pai_diamonds']=intval($v1['pai_diamonds']);
					}
					$ext['buyer'][]=$buyer_data;
				}

				#构造高级接口所需参数
				$tim_data=array();
				$tim_data['ext']=$ext;
				$tim_data['podcast_id']=strim($podcast_id);
				$tim_data['group_id']=strim($video_info['group_id']);

				get_tim_api($tim_data);


			}
			return array("status"=>$status,"is_true"=>$is_true,'order_sn'=>$order_sn);
		}
		 
		 
		 /**
		 * 观众-获参与竞拍列表
		 * 
		 */
		 private function p_join($j_datas,$type){
			$list = array();
			
			$where=" 1=1 ";
			if ($type=='OR') {
				$where=" 1=0 ";
			}
			foreach($j_datas as $k=>$v){
				$where.=$type." (pai_id =".$k." AND user_id=".$v.")";
			}

			$temp_list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_join WHERE ".$where);
			$list=array();
			foreach($temp_list as $k=>$v){
				
				if($v['consignee_district']==''){
					$v['consignee_district']='';
				}
				$list[$v['pai_id']] = $v;
			}
			
			return $list;
		 }
		 
		 /**
		 * 获竞拍商品信息
		 * int pai_id 商品ID
		 */
		 function p_goodsinfo($data,$condition=''){
			$status = 1;
			$pai_id = (int)$data['pai_id'];
			if($condition==''){
				$condition = "*";
			}
			
		
		 	if($pai_id>0){
		 		$goods_info = $GLOBALS['db']->getRow("SELECT ".$condition." FROM ".DB_PREFIX."pai_goods WHERE id =".$pai_id);
		 	}else{
				$status = 10008;
			}
			
		 	return array("status"=>$status,'info'=>$goods_info);
		 }
		 
		 /**
		 * 观众端查看虚拟订单详情
		 * return $return_data;
		 */
		function virtual_order_details($data){

			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$pai_id = (int)$data['pai_id'];

			$order_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
			$goods_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id);			
			$supplier_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$goods_info['user_id']);

			$return_data=array();
			if (!$order_info) {
				return $return_data;
			}
			if (!$goods_info) {
				return $return_data;
			}
			
			$join_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." and user_id=".intval($order_info['viewer_id']));
			
			$return_data=array();
			$return_data['order_sn']=$order_sn;
			$return_data['supplier_name']=$goods_info['podcast_name'];
			$return_data['user_name']=$goods_info['user_name'];
			$return_data['supplier_tel']=$supplier_info['mobile'];
			$return_data['order_status']=$order_info['order_status'];
			$return_data['order_status_time']=$order_info['order_status_time'];
			$return_data['no_refund']=$order_info['no_refund'];
			$return_data['refund_buyer_status']=$order_info['refund_buyer_status'];
			$return_data['refund_buyer_delivery']=$order_info['refund_buyer_delivery'];
			$return_data['refund_seller_status']=$order_info['refund_seller_status'];
			$return_data['refund_platform']=$order_info['refund_platform'];
			$return_data['refund_over_time']=$order_info['refund_over_time'];
			$return_data['refund_reason']=$order_info['refund_reason'];
			$return_data['number']=$order_info['number'];
			$return_data['total_diamonds']=intval($order_info['total_diamonds']);
			$return_data['goods_diamonds']=$order_info['goods_diamonds'];
			$return_data['pay_diamonds']=$order_info['pay_diamonds'];
			$return_data['refund_diamonds']=$order_info['refund_diamonds'];
			$return_data['freight_diamonds']=$order_info['freight_diamonds'];
			$return_data['memo']=$order_info['memo'];
			$return_data['consignee']=$order_info['consignee'];
			$return_data['consignee_mobile']=$order_info['consignee_mobile'];
			$return_data['consignee_district']=$order_info['consignee_district'];
			$return_data['consignee_address']=$order_info['consignee_address'];
			$return_data['create_time']=$order_info['create_time'];
			$return_data['diamonds']=$GLOBALS['db']->getOne("SELECT diamonds FROM ".DB_PREFIX."user WHERE id=".$podcast_id);
			$return_data['goods_list']=$GLOBALS['db']->getAll("SELECT id as goods_id,name as goods_name ,imgs as goods_icon ,create_time FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id);

			$return_data['bz_diamonds']=$goods_info['bz_diamonds'];
			$return_data['place']=$goods_info['place'];
			$return_data['join_status']=$join_info['status'];
			$return_data['podcast_id']=$order_info['podcast_id'];
			$return_data['user_id']=$order_info['viewer_id'];

			if(PAI_REAL_BTN == 1 && $goods_info['is_true'] == 1){
				$return_data['consignee_district']=json_decode(htmlspecialchars_decode($order_info['consignee_district']),true);
				$return_data['consignee_address']=$return_data['consignee_district']['province'].$return_data['consignee_district']['city'].$return_data['consignee_district']['area'].$order_info['consignee_address'];

				$return_data['time'] = $order_info['delivery_time'];
//				$return_data['express']=$info['express'];
				$return_data['shopinfo']=array('shop_name'=>$goods_info['podcast_name']);
			}

			return $return_data;

		}
		
		/**
		 * 进入直播间-获取拍卖信息
		 * return array("status"=>$status);
		 */
		function get_video($data){
		    $status = 1;
			$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
							
			$info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);
			$buyer=array();
			if (intval($info['status']==1)) {
				
				$user_list=$GLOBALS['db']->getAll("select user_id,pai_status,order_id,order_status,pai_diamonds from ".DB_PREFIX."pai_join where pai_id=".$pai_id." and pai_diamonds>0 ORDER BY pai_diamonds DESC limit 0,3");
				
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$fields = array('head_image','user_level','v_type','v_icon','nick_name');
				
				
				foreach($user_list as $k=>$v){
					$buyer_data=array();
					if (intval($v['user_id'])>0) {					
						$buyer_data=$user_redis->getRow_db(intval($v['user_id']),$fields);
						$buyer_data['user_id'] = intval($v['user_id']);
						$buyer_data['type']=intval($v['pai_status']);
						$buyer_data['head_image'] = get_spec_image($buyer_data['head_image']);
						if ($buyer_data['type']==1) {
							$goods_order=$GLOBALS['db']->getRow("select create_time,order_sn from ".DB_PREFIX."goods_order where id=".intval($v['order_id'])." ");
							$buyer_data['left_time']=$goods_order['create_time']+MAX_PAI_PAY_TIME-NOW_TIME;
							
							$buyer_data['goods_name']=$info['name'];
							$buyer_data['order_sn']=$goods_order['order_sn'];
							if ($info['imgs']!='') {
								$imgs=json_decode($info['imgs']);
								foreach($imgs as $k2=>$v2){
									//$buyer_data['goods_icon']=get_domain().APP_ROOT.$v2;
									$buyer_data['goods_icon']=get_spec_image($v2);
									break;
								}
							}else{
								$buyer_data['goods_icon']="";
							}
							
						}else{
							$buyer_data['left_time']=0;
						}						
						$buyer_data['pai_diamonds']=intval($v['pai_diamonds']);
					}
					$buyer[]=$buyer_data;
				}			
				
			}elseif (intval($info['status']==0)) {
				
			}
			
			$has_join = 0;
			$join_data = $GLOBALS['db']->getRow("SELECT pj.* FROM ".DB_PREFIX."pai_join as pj  WHERE pj.pai_id =".$pai_id." AND pj.user_id =".$user_id);
			if($join_data){
				$has_join = 1;
			}else{
				$join_data=array();
				$has_join = 0;
			}
		
			return array("status"=>$status,'info'=>$info,'buyer'=>$buyer,'join_data'=>$join_data,'has_join'=>$has_join);
		
		}
		
		function test($data){
			//更新延时 时间
			if(PAI_YANCHI_MODULE==0){
				$now_yanshi_sql = "update  ".DB_PREFIX."pai_goods SET  now_yanshi=now_yanshi+1 where id=".$data['id']." AND (if(pai_less_time=0,60,pai_less_time)) >= (pai_time*3600  + create_time + now_yanshi*pai_yanshi*60) - ".get_gmtime()." and now_yanshi<max_yanshi";

			}else{
				$now_yanshi_sql = "update ".DB_PREFIX."pai_goods SET  now_yanshi=now_yanshi+1,end_time=pai_yanshi*60-(end_time-".get_gmtime().")+end_time where id=".$data['id']." and (pai_yanshi*60) > (end_time-".get_gmtime().") and now_yanshi<max_yanshi";

			}
			return 	$now_yanshi_sql;
		}


	/**
	 * 创建购物订单
	 * return $return_data;;
	 */
	function create_shop_order($data){

		$root= array();
		$shop_info = $data['shop_info'];//商品数量//商品ID//主播ID//订单编号
		$viewer_id = intval($data['viewer_id']);//观众ID
		$purchase_type = intval($data['purchase_type']);//0表示买给自己、1表示买给主播
		$address_id = intval($data['address_id']);//收货地址ID
		$time = NOW_TIME;

		if(count($shop_info) > 1){
			$order = array();
			$order['order_source'] = 'local';
			$order['order_type'] = 'shop';
			$order['order_sn'] = to_date($time,"Ymdhis").rand(10,99);
			$order['order_status'] = 1;
			$order['no_refund'] = 0;
			$order['refund_buyer_status'] = 0;
			$order['refund_buyer_delivery'] = 0;
			$order['refund_seller_status'] = 0;
			$order['refund_platform'] = 0;
			$order['number'] = 0;
			$order['total_diamonds'] = 0;
			$order['remote_total_diamonds'] = 0;
			$order['remote_cost_diamonds'] = 0;
			$order['goods_diamonds'] = 0;
			$order['pay_diamonds'] = 0;
			$order['podcast_ticket'] = 0;
			$order['refund_diamonds'] = 0;
			$order['freight_diamonds'] = 0;
			$order['memo'] = '';
			$order['consignee'] = 0;
			$order['consignee_mobile'] = 0;
			$order['consignee_district'] = 0;
			$order['consignee_address'] = 0;
			if($purchase_type == 0){
				$address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$viewer_id." and id=".$address_id);
				if($address){
					$order['consignee'] = strim($address['consignee']);
					$order['consignee_mobile'] = strim($address['consignee_mobile']);
					$order['consignee_district'] = $address['consignee_district'];
					$order['consignee_address'] = strim($address['consignee_address']);
				}
			}
			$order['create_time'] = $time;
			$order['create_date'] = to_date($time,'Y-m-d H:i:s');;
			$order['create_time_ymd']=to_date($time,'Y-m-d');
			$order['create_time_y']=to_date($time,'Y');
			$order['create_time_m']=to_date($time,'m');
			$order['create_time_d']=to_date($time,'d');
			$order['podcast_id'] = $shop_info[0]['podcast_id'];
			$order['viewer_id'] = $viewer_id;
			$order['pai_id'] = 0;
			$order['goods_id'] = $shop_info[0]['goods_id'];
			$order['pay_time'] = 0;
			$order['refund_over_time'] = 0;
			$order['order_status_time'] = 0;
			$order['delivery_time'] = 0;
			$order['refund_reason'] = 0;
			$order['courier_number'] = 0;
			$order['courier_offic'] = 0;
			$order['buy_type'] = $purchase_type;
			$order['is_p'] = 1;
			$order['pid'] = 0;
			$GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order,"INSERT");

			$insert_id = $GLOBALS['db']->insert_id();
			if($insert_id > 0){
				foreach($shop_info as $key =>$value){
					$goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE is_effect=1 and id=".$value['goods_id']);
					$order_data['order_source']='local';
					$order_data['order_type']='shop';
					$order_data['buy_type']=$purchase_type;
					//$order_data['order_sn']=to_date($time,"Ymdhis").rand(10,99);
					$order_data['order_sn'] = $value['order_sn'];
					$order_data['order_status']=1;
					$order_data['no_refund']=0;
					$order_data['refund_buyer_status']=0;
					$order_data['refund_buyer_delivery']=0;
					$order_data['refund_seller_status']=0;
					$order_data['refund_platform']=0;
					$order_data['number']=$value['number'];
					$order_data['total_diamonds'] = floatval(($goods_info['price']*$order_data['number'])+$goods_info['kd_cost']);
					$order_data['remote_total_diamonds']=0;
					$order_data['remote_cost_diamonds']=0;
					$order_data['goods_diamonds']=floatval($goods_info['price']);
					$order_data['pay_diamonds']=0;

					if(floatval($goods_info['podcast_ticket']) == 0){
						$m_config =  load_auto_cache("m_config");//初始化手机端配置
						$platform_on_commission = floatval($m_config['platform_on_commission']/100);//平台抽取佣金比率
						$order_data['podcast_ticket']=round($goods_info['price']*$platform_on_commission*$order_data['number'],2);
					}else{
						$order_data['podcast_ticket']= floatval($goods_info['podcast_ticket']);
					}

					if(define(DISTRIBUTION_MODULE) && DISTRIBUTION_MODULE==1){
						fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
						$user_redis = new UserRedisService();
						$user_level = $user_redis->getOne_db($value['podcast_id'],'user_level');
						$commission = $GLOBALS['db']->getOne("SELECT commission FROM ".DB_PREFIX."user_level WHERE level=".$user_level,true,true);
						$order_data['podcast_ticket']=round($goods_info['price']*($commission/100)*$order_data['number'],2);//根据主播称谓计算主播佣金
					}

					$order_data['refund_diamonds']=0;
					$order_data['freight_diamonds']=floatval($goods_info['kd_cost']);
					$order_data['memo']=$value['memo'];
					$order_data['consignee'] = '';
					$order_data['consignee_mobile'] = 0;
					$order_data['consignee_district'] = '';
					$order_data['consignee_address'] = '';

					if($purchase_type == 0){
						$address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$viewer_id." and id=".$address_id);
						if($address){
							$order_data['consignee'] = strim($address['consignee']);
							$order_data['consignee_mobile'] = strim($address['consignee_mobile']);
							$order_data['consignee_district'] = $address['consignee_district'];
							$order_data['consignee_address'] = strim($address['consignee_address']);
						}
					}

					$order_data['create_time']=$time;
					$order_data['create_date']=to_date($time,'Y-m-d H:i:s');
					$order_data['create_time_ymd']=to_date($time,'Y-m-d');
					$order_data['create_time_y']=to_date($time,'Y');
					$order_data['create_time_m']=to_date($time,'m');
					$order_data['create_time_d']=to_date($time,'d');
					$order_data['podcast_id']=$value['podcast_id'];
					$order_data['viewer_id']=$viewer_id;
					$order_data['pai_id']=0;
					$order_data['goods_id']=$value['goods_id'];
					$order_data['pay_time']=0;
					$order_data['is_p']=0;
					$order_data['pid']=$insert_id;

					$GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order_data,"INSERT");
					$order_id = $GLOBALS['db']->insert_id();

					$root['price'] += $order_data['total_diamonds'];
					$root['number'] += $order_data['number'];
					$shop_info[$key]['order_id'] = $order_id;

					$user_ids=array();
					$user_ids[]=$viewer_id;
					$content="您已购买：‘".$value['name']."’，请在15分钟内完成付款！";
					FanweServiceCall("message","send",array("send_type"=>'tip_to_pay',"user_ids"=>$user_ids,"content"=>$content));

					$sql = "delete from ".DB_PREFIX."shopping_cart where user_id=".$viewer_id." and goods_id=".$value['goods_id']." and podcast_id=".$value['podcast_id'];
					$GLOBALS['db']->query($sql);

				}
				$sql = "UPDATE  ".DB_PREFIX."goods_order SET total_diamonds='".$root['price']."',number='".$root['number']."' WHERE `id`=".$insert_id;
				$GLOBALS['db']->query($sql);
				$root['shop_info'] = $shop_info;
				$root['status']= 1;
				$root['error']= '下单成功';
			}

		}else{

			foreach($shop_info as $key =>$value){

				$order_goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_status>1 and order_sn=".$value['order_sn']." and viewer_id=".$viewer_id);
				if($order_goods_info){
					$root['status'] = 10054;
					$root['error']  = "订单已付款";
					$root['total_diamonds'] = floatval($order_goods_info['total_diamonds']);
					return $root;
				}

				$goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE is_effect=1 and id=".$value['goods_id']);
				if($goods_info != 0 ){

					$order_data['order_source']='local';
					$order_data['order_type']='shop';
					$order_data['buy_type']=$purchase_type;
					//$order_data['order_sn']=to_date($time,"Ymdhis").rand(10,99);
					$order_data['order_sn'] = $value['order_sn'];
					$order_data['order_status']=1;
					$order_data['no_refund']=0;
					$order_data['refund_buyer_status']=0;
					$order_data['refund_buyer_delivery']=0;
					$order_data['refund_seller_status']=0;
					$order_data['refund_platform']=0;
					$order_data['number']=$value['number'];
					$order_data['total_diamonds'] = floatval(($goods_info['price']*$order_data['number'])+$goods_info['kd_cost']);
					$order_data['remote_total_diamonds']=0;
					$order_data['remote_cost_diamonds']=0;
					$order_data['goods_diamonds']=floatval($goods_info['price']);
					$order_data['pay_diamonds']=0;

					if(floatval($goods_info['podcast_ticket']) == 0){
						$m_config =  load_auto_cache("m_config");//初始化手机端配置
						$platform_on_commission = floatval($m_config['platform_on_commission']/100);//平台抽取佣金比率
						$order_data['podcast_ticket']=round($goods_info['price']*$platform_on_commission*$order_data['number'],2);
					}else{
						$order_data['podcast_ticket']= floatval($goods_info['podcast_ticket']);
					}

					if(define(DISTRIBUTION_MODULE) && DISTRIBUTION_MODULE==1){
						fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
						$user_redis = new UserRedisService();
						$user_level = $user_redis->getOne_db($value['podcast_id'],'user_level');
						$commission = $GLOBALS['db']->getOne("SELECT commission FROM ".DB_PREFIX."user_level WHERE level=".$user_level,true,true);
						$order_data['podcast_ticket']=round($goods_info['price']*($commission/100)*$order_data['number'],2);//根据主播称谓计算主播佣金
					}

					$order_data['refund_diamonds']=0;
					$order_data['freight_diamonds']=floatval($goods_info['kd_cost']);
					$order_data['memo']=$value['memo'];
					$order_data['consignee'] = '';
					$order_data['consignee_mobile'] = 0;
					$order_data['consignee_district'] = '';
					$order_data['consignee_address'] = '';

					if($purchase_type == 0){
						$address = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_address WHERE user_id=".$viewer_id." and id=".$address_id);
						if($address){
							$order_data['consignee'] = strim($address['consignee']);
							$order_data['consignee_mobile'] = strim($address['consignee_mobile']);
							$order_data['consignee_district'] = $address['consignee_district'];
							$order_data['consignee_address'] = strim($address['consignee_address']);
						}
					}

					$order_data['create_time']=$time;
					$order_data['create_date']=to_date($time,'Y-m-d H:i:s');
					$order_data['create_time_ymd']=to_date($time,'Y-m-d');
					$order_data['create_time_y']=to_date($time,'Y');
					$order_data['create_time_m']=to_date($time,'m');
					$order_data['create_time_d']=to_date($time,'d');
					$order_data['podcast_id']=$value['podcast_id'];
					$order_data['viewer_id']=$viewer_id;
					$order_data['pai_id']=0;
					$order_data['goods_id']=$value['goods_id'];
					$order_data['pay_time']=0;

					if ($GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order_data,"INSERT")){
						$order_id = $GLOBALS['db']->insert_id();

						$user_ids=array();
						$user_ids[]=$viewer_id;
						$content="您已购买：‘".$goods_info['name']."’，请在15分钟内完成付款！";
						FanweServiceCall("message","send",array("send_type"=>'tip_to_pay',"user_ids"=>$user_ids,"content"=>$content));

						$sql = "delete from ".DB_PREFIX."shopping_cart where user_id=".$viewer_id." and goods_id=".$value['goods_id']." and podcast_id=".$value['podcast_id'];
						$GLOBALS['db']->query($sql);

						$root['status']= 1;
						$root['error']= '下单成功';
						$root['price'] = $order_data['total_diamonds'];
						$shop_info[$key]['order_id'] = $order_id;
						$root['shop_info'] = $shop_info;

					}else{
						$root['status']= 0;
						$root['error']= '下单失败';
					}

				}else{
					$root['status']= 10064;
					$root['error']= '商品库存不足';
				}

			}
		}
		$sql = "select id,name,class_name,logo from " . DB_PREFIX . "payment where class_name in ('Aliapp','WxApp') and is_effect = 1";
		$root['payment_info'] = $GLOBALS['db']->getAll($sql);

		return $root;
	}






	}

		
?>
