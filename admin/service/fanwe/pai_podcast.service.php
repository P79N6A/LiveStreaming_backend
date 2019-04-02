<?php
	class pai_podcastService{
			
		
		/**
		 * 主播-创建竞拍
		 * return array("status"=>$status,"data"=>$data);
		 */
		function addpai($data){
				
			$pai_goods['podcast_id']  = (int)$data['podcast_id'];

			$result_data=array();
			//合法性判断
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$pai_goods['podcast_id']." and (status=0 or status=1)  and is_delete=0");
			if ($rs_count>0) {
				$status=10049;
				return array("status"=>$status,"data"=>$result_data);
			}
			
			//判断是否开启直播
			$video_info=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."video WHERE user_id=".$pai_goods['podcast_id']." and live_in=1 ");
			if (!$video_info) {
			 $status=10055;
			return array("status"=>$status,"data"=>$result_data);
			}

			//是否被禁言
			$is_nospeaking = $GLOBALS['db']->getOne("SELECT is_nospeaking FROM ".DB_PREFIX."user WHERE id=".$pai_goods['podcast_id']);
			if ($is_nospeaking == 1) {
				$status=0;
				return array("status"=>$status,"data"=>$result_data);
			}
			
			$pai_goods['is_true']  = (int)$data['is_true'];
			$pai_goods['goods_id']  = (int)$data['goods_id'];
			$pai_goods['imgs']  = $data['imgs'];
			$pai_goods['tags']  = trim($data['tags']);
			$pai_goods['name']  = htmlspecialchars_decode($data['name']);
			$pai_goods['description']  = trim($data['description']);
			$pai_goods['date_time']  = trim($data['date_time']);
			$pai_goods['place']  = trim($data['place']);
			$pai_goods['district']  = trim($data['district']);
			$pai_goods['contact']  = trim($data['contact']);
			$pai_goods['mobile']  = trim($data['mobile']);
			$pai_goods['qp_diamonds']  = (int)$data['qp_diamonds'];
			$pai_goods['bz_diamonds']  = (int)$data['bz_diamonds'];
			$pai_goods['jj_diamonds']  = (int)$data['jj_diamonds'];
			$pai_goods['pai_time']  = $data['pai_time'];
			//$pai_goods['pai_time']  = 0.05;			
			
			$pai_goods['pai_yanshi']  = $data['pai_yanshi'];
			$pai_goods['max_yanshi']  = $data['max_yanshi'];
			$pai_goods['pai_less_time']  = 60;
			$pai_goods['podcast_name']  = $GLOBALS['db']->getOne("SELECT nick_name FROM ".DB_PREFIX."user WHERE id=".$pai_goods['podcast_id']);
			$time=NOW_TIME;
			$pai_goods['create_time']  = $time;
			$pai_goods['create_date']  = to_date($time,'Y-m-d H:i:s');
			$pai_goods['create_time_ymd']  = to_date($time,'Y-m-d');
			$pai_goods['create_time_y']  = to_date($time,'Y');
			$pai_goods['create_time_m']  = to_date($time,'m');
			$pai_goods['create_time_d']  = to_date($time,'d');
			
			$pai_goods['user_id']  = 0;
			$pai_goods['user_name']  = '';
			$pai_goods['status']  = 0;
			$pai_goods['order_id']  = '';
			$pai_goods['order_status']  = 0;
			$pai_goods['last_user_id']  = 0;
			$pai_goods['last_user_name']  = '';
			$pai_goods['last_pai_diamonds']  = 0;
			$pai_goods['end_time']  = intval($data['end_time']);

			if ($pai_goods['is_true']==1) {
				
				if (OPEN_GOODS==1) {
					$pai_goods['shop_id'] = intval($_REQUEST['shop_id']);
					$pai_goods['shop_name'] = strim($_REQUEST['shop_name']);
				}else{
					//真实商品
					$goods_info=$GLOBALS['db']->getRow("SELECT gs.* FROM ".DB_PREFIX."goods as gs,".DB_PREFIX."user_goods as ug WHERE gs.id=ug.goods_id and gs.inventory>0 and gs.id=".$pai_goods['goods_id']."  and ug.user_id=".$pai_goods['podcast_id']." and gs.is_effect=1 ");
					if (!$goods_info) {
						$status=10010;
						return array("status"=>$status,"data"=>$result_data,"error"=>'商品暂无库存');
					}

					$sql = "update ".DB_PREFIX."goods set inventory=inventory-1 where id=".$pai_goods['goods_id']."";
					$GLOBALS['db']->query($sql);//竞拍前减去库存...1

					$sql = "update ".DB_PREFIX."goods set sales=sales+1 where id=".$pai_goods['goods_id']."";
					$GLOBALS['db']->query($sql);//销售量增加
					
					$pai_goods['imgs']=$goods_info['imgs'];
					$pai_goods['name']=htmlspecialchars_decode($goods_info['name']);
					$pai_goods['description']=$goods_info['description'];
					$pai_goods['qp_diamonds']=intval($goods_info['pai_diamonds']);
				}
			
			}
			
			if ($GLOBALS['db']->autoExecute(DB_PREFIX."pai_goods",$pai_goods,"INSERT")) {
				$status=1;
				$pai_id = $GLOBALS['db']->insert_id();
				$sql = "update ".DB_PREFIX."video set pai_id = ".$pai_id." where user_id=".$pai_goods['podcast_id']." and live_in=1";
				$GLOBALS['db']->query($sql);
				
				//$video_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."video where user_id=".$pai_goods['podcast_id']." ");
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
				$video_redis = new VideoRedisService();
				$video_data=array();
				$video_data['pai_id']=$pai_id;
				$re =   $video_redis->update_db(intval($video_info['id']),$video_data);
				//log_result("==re==");
				//log_result($re);
				//推送流程
				//流拍房间内推送
				$ext = array();
				$ext['type'] = 30;
				$ext['room_id'] = intval($video_info['id']);
				$ext['pai_id'] = $pai_id;
				$ext['post_id'] = $pai_goods['podcast_id'];		
				$ext['desc'] = "主播发起了竞拍";
				$ext['info'] = $GLOBALS['db']->getRow("select name,goods_id,bz_diamonds,qp_diamonds,jj_diamonds from ".DB_PREFIX."pai_goods where id=".$pai_id);
				
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$fields = array('head_image','user_level','v_type','v_icon','nick_name');
				$ext['user'] = $user_redis->getRow_db(intval($pai_goods['podcast_id']),$fields);
				$ext['user']['user_id'] = intval($pai_goods['podcast_id']);
				$ext['user']['head_image'] = get_spec_image($ext['user']['head_image']);
				#构造高级接口所需参数
				$tim_data=array();
				$tim_data['ext']=$ext;
				$tim_data['podcast_id']=strim($pai_goods['podcast_id']);
				$tim_data['group_id']=strim($video_info['group_id']);
				get_tim_api($tim_data);
				
				/*$pai_goods_id = $GLOBALS['db']->insert_id();
				
				
				$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_goods_id);
				
				$pai_list=array();
				
				$pages['page'] = 1;
				$pages['has_next'] = 0;
				
				$result_data['info']=$info;
				$result_data['pai_list']=$pai_list;
				$result_data['pages']=$pages;*/
			}else{
				$status=10025;
			}
								
			 return array("status"=>$status,"pai_id"=>$pai_id);
		}
		
		/**
		 * 主播-编辑竞拍（备用，暂不用）
		 * return array("status"=>$status,"data"=>$data);
		 */
		function editpai($data){
				
			$id  = intval($data['id']);
			$pai_goods['podcast_id']  = intval($data['podcast_id']);
			$pai_goods['is_true']  = intval($data['is_true']);
			$pai_goods['goods_id']  = intval($data['goods_id']);
			$pai_goods['imgs']  = $data['imgs'];
			$pai_goods['tags']  = trim($data['tags']);
			$pai_goods['name']  = trim($data['name']);
			$pai_goods['description']  = trim($data['description']);
			$pai_goods['date_time']  = trim($data['date_time']);
			$pai_goods['place']  = trim($data['place']);
			$pai_goods['district'] = strim($data['district']);
			$pai_goods['contact']  = trim($data['contact']);
			$pai_goods['mobile']  = trim($data['mobile']);
			$pai_goods['qp_diamonds']  = intval($data['qp_diamonds']);
			$pai_goods['bz_diamonds']  = intval($data['bz_diamonds']);
			$pai_goods['jj_diamonds']  = intval($data['jj_diamonds']);
			$pai_goods['pai_time']  = $data['pai_time'];
			$pai_goods['pai_yanshi']  = $data['pai_yanshi'];
			$pai_goods['max_yanshi']  = $data['max_yanshi'];
			
			$result_data=array();
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."pai_goods", $pai_goods, $mode = 'UPDATE', "id=".$id);
			if($status){
				$status=1;
				
				$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$id);
				
				$pai_list=array();
				
				$pages['page'] = 1;
				$pages['has_next'] = 0;
				
				$result_data['info']=$info;
				$result_data['pai_list']=$pai_list;
				$result_data['pages']=$pages;
			}else{
				$status=10026;
				
			}
			
			return array("status"=>$status,"data"=>$result_data);
		}
		
		/**
		 * 主播-创建的竞拍列表
		 * return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
		 */
		function goods($data){
			
			$podcast_id = (int)$data['podcast_id'];
			$is_true  = (int)$data['is_true'];
			$page = (int)$data['page'];
			$page_size = (int)$data['page_size'];
			
			$limit = (($page-1)*$page_size).",".$page_size;
			
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$podcast_id." and is_true=".$is_true." and is_delete=0");
			
			$list = array();
				
			$pages['page'] = $page;
			$pages['has_next'] = 0;
			
			if($rs_count > 0){
				$list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$podcast_id." and is_true=".$is_true." and is_delete=0 ORDER BY id DESC limit ".$limit);
				foreach($list as $k=>$v){
					if(intval($v['order_id']) > 0){
						$order_date = $GLOBALS['db']->getRow("SELECT pj.user_id as user_id,go.order_status_time,go.order_sn,go.podcast_ticket,go.goods_diamonds as pai_diamonds,pj.status as bz_status,go.refund_platform as refund_platform,go.refund_reason as refund_reason FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."pai_join as pj WHERE go.id=pj.order_id and go.id=".intval($v['order_id'])."");
						$order_status_time=$order_date['order_status_time'];
						$list[$k]['refund_platform']=$order_date['refund_platform'];
						$list[$k]['refund_reason']=$order_date['refund_reason'];
						if (intval($order_status_time)>0) {
							$list[$k]['order_status_time'] = $order_status_time;
						}
						
						$list[$k]['order_sn'] = strim($order_date['order_sn']);
						$list[$k]['pai_diamonds'] = intval($order_date['pai_diamonds']);
						$list[$k]['podcast_ticket'] = intval($order_date['podcast_ticket']);

						$list[$k]['user_id'] = intval($order_date['user_id']);
						$list[$k]['user_name'] = $GLOBALS['db']->getOne("select nick_name FROM ".DB_PREFIX."user where id=".$list[$k]['user_id'],true,true);
						$list[$k]['join_status'] = intval($order_date['bz_status']);
						$list[$k]['refund_platform'] = intval($order_date['refund_platform']);

						if(PAI_REAL_BTN == 1 && $is_true == 1){
							$list[$k]['shopinfo']=array('shop_name'=>$v['podcast_name']);
						}

					}
					else{
						$list[$k]['order_status_time'] =0;
						$list[$k]['pai_diamonds'] = 0;
						$list[$k]['podcast_ticket'] = 0;
						$list[$k]['bz_status'] = 0;
						$list[$k]['refund_platform'] = 0;
					}

				}
				$total = ceil($rs_count/$page_size);
				if($total > $page)
					$pages['has_next'] = 1;

			}
			
			return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
			
		}
		
		/**
		 * 主播-获得某竞拍竞拍详情
		 * return array("info"=>$info,"pai_list"=>$pai_list,"page"=>$pages);
		 */
		function goods_detail($data){
				
			$podcast_id = (int)$data['podcast_id'];
			$pai_id  = (int)$data['pai_id'];
			$get_pailogs = (int)$data['get_pailogs'];
			
			$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id);
			
			if(intval($info['order_id']) > 0){
				$order_date = $GLOBALS['db']->getRow("SELECT go.order_status_time,go.order_sn,go.podcast_ticket,go.goods_diamonds as pai_diamonds,pj.status as bz_status FROM ".DB_PREFIX."goods_order as go,".DB_PREFIX."pai_join as pj WHERE go.id=pj.order_id and go.id=".intval($info['order_id'])."");
				$order_status_time=$order_date['order_status_time'];
				if (intval($order_status_time)>0) {
					$info['order_status_time'] = $order_status_time;
				}
			
				$info['order_sn'] = strim($order_date['order_sn']);
				$info['pai_diamonds'] = intval($order_date['pai_diamonds']);
				$info['podcast_ticket'] = intval($order_date['podcast_ticket']);
				$info['bz_status'] = intval($order_date['bz_status']);
			}
			else{
				$info['order_status_time'] =0;
				$info['pai_diamonds'] = 0;
				$info['podcast_ticket'] = 0;
				$info['bz_status'] = 0;
			}
			$pai_list=array();
			if ($get_pailogs==1) {
				
				$page = (int)$data['page'];
				$page_size = (int)$data['page_size'];

				//$pai_list=getAll("SELECT * FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$podcast_id." and is_true=".$is_true." ORDER BY id ASC limit ".$limit);
				$rs = FanweServiceCall("pai_user","pailogs",array("pai_id"=>$pai_id,"page"=>$page,"page_size"=>$page_size));				
				$pai_list=$rs['list'];
				$rs_count=$rs['rs_count'];
				$pages=$rs['page'];
			}
			if (SHOPPING_GOODS==1&&$info['is_true']==1&&$info['goods_id']>0) {
				$info['url'] =  SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=pai_podcast&act=getauction_commodity_detail&itype=shop&goods_id='.$info['goods_id'];

				$goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE id=".$info['goods_id']);
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
				$info['commodity_detail'] =$root;
				
			}else{
				$info['url'] =  '';
				$info['commodity_detail'] =array('pai_goods'=>array(),'goods_detail'=>array());
			}	
			
					
			return array("info"=>$info,"rs_count"=>$rs_count,"pai_list"=>$pai_list,"page"=>$pages);
		}
		
		/**
		 * 主播关闭 （关闭时候，要将信息推送给 所有观看和参与竞拍的会员）
		 * return array("status"=>$status);
		 */
		function stop_pai($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$pai_id  = (int)$data['pai_id'];
			$video_id = intval($_REQUEST['video_id']);
			
			$pai_goods['status']  = 3;
			$status=0;
			
			$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id." and status=0");
			if (!$info) {
				$status = 10027;
				return array("status"=>$status);
			}
			$sql = "update ".DB_PREFIX."goods set inventory=inventory+1 where id =".$info['goods_id'];
			$GLOBALS['db']->query($sql);//主播关闭竞拍库存增加

			$sql = "update ".DB_PREFIX."pai_goods set status = 3 where id = ".$pai_id." and podcast_id = ".$podcast_id;
			$GLOBALS['db']->query($sql);
			if($GLOBALS['db']->affected_rows()){
				
				$sql = "update ".DB_PREFIX."video set pai_id = 0 where user_id=".$podcast_id." ";
				$GLOBALS['db']->query($sql);
				
				$time=NOW_TIME;
				
				$pai_violations=array();
				$pai_violations['podcast_id']=$podcast_id;
				$pai_violations['create_time']=$time;
				$pai_violations['create_date']=to_date($time,'Y-m-d H:i:s');
				$pai_violations['create_time_y']=to_date($time,'Y');
				$pai_violations['create_time_m']=to_date($time,'m');
				$pai_violations['create_time_d']=to_date($time,'d');
				$pai_violations['create_time_ym']=to_date($time,'Y-m');
				$GLOBALS['db']->autoExecute(DB_PREFIX."pai_violations",$pai_violations,"INSERT");
				
				if ($video_id>0) {
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
					$video_redis = new VideoRedisService();
					$video_data=array();
					$video_data['pai_id']=0;
					$re =   $video_redis->update_db($video_id,$video_data);
				}else {
					$video_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."video where user_id=".$podcast_id." ");
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
					$video_redis = new VideoRedisService();
					$video_data=array();
					$video_data['pai_id']=0;
					$re =   $video_redis->update_db(intval($video_info['id']),$video_data);
				}
				
				
				
				$user_list = $GLOBALS['db']->getAll("SELECT id,user_id,bz_diamonds,status FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id);
				$user_ids=array();
				foreach ($user_list as $k => $v)
				{
					$user_ids[]=$v['user_id'];
					
					//退还保证金 bz_diamonds
					if (intval($v['status'])==0) {
						
						fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
						fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
						$user_redis = new UserRedisService();
						
						$sql = "update ".DB_PREFIX."user set diamonds = diamonds + ".intval($v['bz_diamonds'])." where id = ".intval($v['user_id']);
						$GLOBALS['db']->query($sql);
						user_deal_to_reids(array(intval($v['user_id'])));
						$account_diamonds = $user_redis->getOne_db(intval($v['user_id']), 'diamonds');
						
						$sql = "update ".DB_PREFIX."pai_join set status = 1 where id=".intval($v['id'])." ";
						$GLOBALS['db']->query($sql);
						
						//会员账户 秀豆变更日志表
						$diamonds_log_data = array(
								'pai_id' => $pai_id,
								'user_id' => intval($v['user_id']),
								'diamonds'=>intval($v['bz_diamonds']),//变更数额
								'account_diamonds'=>$account_diamonds,//账户余额
								'memo' =>$info['name'].'退还保证金',//备注
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
						$log_msg = $info['name'].'退还保证金';//备注
						account_log_com($data,intval($v['user_id']),$log_msg,$param);
						
					}
					
				}
				//$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id." ");
				$content="您参与的竞拍：‘".$info['name']."’ 已关闭，退还您缴纳的保证金！";
				$rs = FanweServiceCall("message","send",array("send_type"=>'pai_close',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status = 10027;
			}
		
			return array("status"=>$status);
		}
		
		/**
		 * 竞拍的时间到达 （结束时候，要将信息推送给参与竞拍的会员）(暂时不用，使用common/deal_pai_timeout)
		 * （1）时间到了 无人参与竞拍，竞拍失败，此时 通知用户竞拍失败
		 * （2）时间到了， 有人参与竞拍，选出前3名 ，并通知用户 领先的会员
		 * return array("status"=>$status);
		 */
		/*function end_pai($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$pai_id  = (int)$data['pai_id'];
			
			$status = 0;
			$user_list = $GLOBALS['db']->getAll("SELECT user_id FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." ");
			if ($user_list) {
				//通知用户 领先的会员
				$user_ids=array();
				foreach ( $user_list as $k => $v )
				{
					$user_ids[]=$v['user_id'];
				}
				$user_ids[]=$podcast_id;
				FanweServiceCall("message","send",array("send_type"=>'pai_close',"user_ids"=>$user_ids));
				
				$user_zb=array();
				$user_zb[]=getOne("SELECT user_id FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id." and podcast_id = ".$podcast_id);
				$rs=FanweServiceCall("message","send",array("send_type"=>'pai_wait_pay',"user_ids"=>$user_zb));
				$status = $rs['status'];
			}else{
				//通知用户竞拍失败
				$user_ids=array();
				$user_ids[]=$podcast_id;
				$rs=FanweServiceCall("message","send",array("send_type"=>'pai_close',"user_ids"=>$user_ids));
				$status = $rs['status'];
			}
			
			
			return array("status"=>$status);
		}*/
		
		/**
		 * 主播提醒买家付款//缺少更新字段
		 * return array("status"=>$status);
		 */
		function remind_buyer_pay($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['to_buyer_id'];
			
			$user_ids=array();
			$user_ids[]=$to_buyer_id;
					
			$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");		
			$content="您参与的竞拍：‘".$info['name']."’ 已中拍，主播‘".$info['podcast_name']."’亲切地提醒您请在15分钟内完成付款，否者将扣除您缴纳的保证金！";
			$rs=FanweServiceCall("message","send",array("send_type"=>'tip_to_pay',"user_ids"=>$user_ids,"content"=>$content));
			$status = $rs['status'];
			return array("status"=>$status);
		}

		/**
		 * 主播提醒买家收货//缺少更新字段
		 * return array("status"=>$status);
		 */
		function remind_buyer_receive($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['to_buyer_id'];
				
			$user_ids=array();
			$user_ids[]=$to_buyer_id;
			
			$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
			$content="主播‘".$info['podcast_name']."’亲切地提醒您确认‘".$info['name']."’约会啦";
			if($info['is_true']==1){
				$content="主播‘".$info['podcast_name']."’亲切地提醒您签收‘".$info['name']."’啦";
			}
			$rs=FanweServiceCall("message","send",array("send_type"=>'pai_to_delivery',"user_ids"=>$user_ids,"content"=>$content));
			$status = $rs['status'];
			return array("status"=>$status);
		
		}

		/**
		 * 主播提醒买家约会//缺少更新字段
		 * return array("status"=>$status);
		 */
		function remind_buyer_to_date($data){		
		
			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['to_buyer_id'];
			
			$user_ids=array();
			$user_ids[]=$to_buyer_id;
			$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
			$content="您参与的竞拍：‘".$info['name']."’ ，主播‘".$info['podcast_name']."’提醒您约会时间为‘".$info['date_time']."’";
			$rs=FanweServiceCall("message","send",array("send_type"=>'tip_viewer_to_tryst',"user_ids"=>$user_ids,"content"=>$content));
			$status = $rs['status'];
			return array("status"=>$status);
		}

		/**
		 * 主播确认完成虚拟竞拍
		 * return array("status"=>$status);
		 */
		function confirm_virtual_auction($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['to_buyer_id'];
				
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =2");
			
			if (!$goods_order_info) {
				$status=10028;
				return array("status"=>$status);
			}
			
			$order_info=array();
			$order_info['order_status']=3;
			$order_info['order_status_time']=NOW_TIME;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
				
				$user_ids=array();
				$user_ids[]=$to_buyer_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				
				$sql = "update ".DB_PREFIX."pai_goods set order_status = 3 where id=".intval($info['id'])." ";
				$GLOBALS['db']->query($sql);
				
				$sql = "update ".DB_PREFIX."pai_join set order_status = 3 where user_id=".$to_buyer_id." and pai_id=".intval($info['id'])." ";
				$GLOBALS['db']->query($sql);
				
				
				$content="主播‘".$info['podcast_name']."’已确认完成‘".$info['name']."’";
				if($info['is_true']==1){
					$content="主播‘".$info['podcast_name']."’已确认‘".$info['name']."’发货";
				}
				$rs=FanweServiceCall("message","send",array("send_type"=>'podcast_to_over_tryst',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status=10028;
			}						
			
			return array("status"=>$status);
		}

		/**
		 * 主播虚拟商品订单-同意退款(后续补充完整)
		 * return array("status"=>$status);
		 */
		function return_virtual_pai($data){

			$to_podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =5 and refund_buyer_status=1");
			if (!$goods_order_info) {
				$status=10029;
				return array("status"=>$status);
			}
			
			$to_buyer_id = intval($goods_order_info['viewer_id']);
		
			$order_info['refund_buyer_status']=3;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
				
				$order_id=intval($goods_order_info['id']);
				$pai_id=intval($goods_order_info['pai_id']);
				$diamonds=intval($goods_order_info['podcast_ticket']);
				
				
				//退款
				fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$sql = "update ".DB_PREFIX."user set diamonds = diamonds + ".$diamonds." where id = ".intval($to_buyer_id);
				$GLOBALS['db']->query($sql);
				user_deal_to_reids(array(intval($to_buyer_id)));
				$account_diamonds = $user_redis->getOne_db(intval($to_buyer_id), 'diamonds');
				
				$pai_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);
				//会员账户 秀豆变更日志表
				$time=NOW_TIME;
				$diamonds_log_data = array(
						'pai_id' => $pai_id,
						'user_id' => intval($to_podcast_id),
						'diamonds'=>$diamonds,//变更数额
						'account_diamonds'=>$account_diamonds,//账户余额
						'memo' =>$pai_info['name'].'竞拍退款',//备注
						'create_time' => $time,
						'create_date' => to_date($time,'Y-m-d H:i:s'),
						'create_time_ymd'  => to_date($time,'Y-m-d'),
						'create_time_y'  => to_date($time,'Y'),
						'create_time_m'  => to_date($time,'m'),
						'create_time_d'  => to_date($time,'d'),
						'type' =>3,//3 竞拍收益
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_diamonds_log",$diamonds_log_data);
				
				//写入用户日志
				$data = array();
				$data['diamonds'] = $diamonds;
				$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
				$log_msg = $pai_info['name'].'竞拍退款';//备注
				account_log_com($data,intval($to_buyer_id),$log_msg,$param);
				
				$user_ids=array();
				$user_ids[]=$to_buyer_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 已退款！";
				$rs=FanweServiceCall("message","send",array("send_type"=>'viewer_to_over_tryst',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
				
				
			}else{
				$status=10029;
			}
			return array("status"=>$status);
		}
		
		/**
		 * 主播申诉虚拟商品订单
		 * return array("status"=>$status);
		 */
		function complaint_virtual_goods($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$order_sn  = trim($data['order_sn']);
			
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' ");//and order_status =5 and refund_buyer_status=1
			if (!$goods_order_info) {
				$status=10029;
				return array("status"=>$status);
			}
			
			$order_info['refund_platform']=1;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
				
				
			}else{
				$status=10030;
			}
			return array("status"=>$status);
		}
		
		/**
		 * 主播端查看虚拟订单详情
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
									
			$return_data['order_sn']=$order_sn;
			$return_data['supplier_name']=$goods_info['podcast_name'];
			$return_data['user_name']=$goods_info['user_name'];
			$return_data['supplier_tel']=strim($supplier_info['mobile']);
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
			$return_data['podcast_ticket']=$order_info['podcast_ticket'];
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
		 * 买家确认约会
		 * return array("status"=>$status);
		 */
		function buyer_confirm_date($data){
		
			$user_id = (int)$data['user_id'];
			$order_sn  = trim($data['order_sn']);
			$to_podcast_id = (int)$data['to_podcast_id'];
			$time=NOW_TIME;
			
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =3");				
			if (!$goods_order_info) {
				$status=10031;
				return array("status"=>$status);
			}
			
			$order_info=array();
			$order_info['order_status']=4;
			$order_info['order_status_time']=NOW_TIME;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
			
				$order_data=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
				$order_id=intval($order_data['id']);
				$pai_id=intval($order_data['pai_id']);
				$podcast_ticket=round($order_data['podcast_ticket'],2);

				if(DISTRIBUTION_MODULE == 1){
					if($order_data['order_type'] == 'shop'){
						if (intval($GLOBALS['user_info']['p_user_id'])<0) {

							$data = array(
								'p_user_id' =>$to_podcast_id,
							);
							$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$user_id);

							fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
							$user_redis = new UserRedisService();
							$user_redis->update_db($user_id,$data);
							//更新session
							$user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $user_id);
							es_session::set("user_info", $user_info);
						}
					}
				}

				$sql = "update ".DB_PREFIX."pai_goods set order_status = 4 where id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
				
				$sql = "update ".DB_PREFIX."pai_join set order_status = 4 where user_id=".$user_id." and pai_id=".$pai_id." ";
				$GLOBALS['db']->query($sql);

				fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();

				if($order_data['goods_id'] > 0){
					$goods_score=$GLOBALS['db']->getOne("SELECT score FROM ".DB_PREFIX."goods WHERE id='".$order_data['goods_id']."'");
					if($goods_score > 0){
						$user_score = $GLOBALS['db']->query("update ".DB_PREFIX."user set score=score+".intval($goods_score)." where id = ".$order_data['viewer_id']);
						$podcast_score = $GLOBALS['db']->query("update ".DB_PREFIX."user set score=score+".intval($goods_score)." where id = ".$order_data['podcast_id']);
						if($user_score && $podcast_score){
							//更新经验
							$user_redis->inc_score($order_data['viewer_id'],intval($goods_score));
							$user_redis->inc_score($order_data['podcast_id'],intval($goods_score));
						}
					}
				}

				$sql = "update ".DB_PREFIX."user set ticket = ticket + ".$podcast_ticket." where id = ".intval($to_podcast_id);
				$GLOBALS['db']->query($sql);
				user_deal_to_reids(array(intval($to_podcast_id)));
				//$account_ticket = $user_redis->getOne_db(intval($to_podcast_id), 'ticket');
				
				$pai_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);

				//写入用户日志
				$data = array();
				$data['ticket'] = $podcast_ticket;
				$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
				$log_msg = $pai_info['name'].'竞拍收益';//备注
				if($order_data['order_type'] == 'shop'){
					$log_msg = '购物收益';//备注
				}
				account_log_com($data,intval($to_podcast_id),$log_msg,$param);
				
				//分销功能 计算抽成
				if(OPEN_DISTRIBUTION == 1){

					$total_ticket=$podcast_ticket;
					$m_config =  load_auto_cache("m_config");//初始化手机端配置
					$table = DB_PREFIX.'distribution_log';
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
					$user_redis = new UserRedisService();
					$to_user_id = $user_redis->getOne_db($user_id,'p_user_id');//用户总的：秀票数
					$ticket = 0;
					$result = 0;
					if(intval($to_user_id)>0&&intval($m_config['distribution'])==1&&$user_id>0){
						$ticket = floatval($m_config['distribution_rate']*0.01*$total_ticket);
						$sql = "select id from ".$table." where to_user_id = ".$to_user_id." and from_user_id = ".$user_id;
						$distribution_id = $GLOBALS['db']->getOne($sql);
						if(intval($distribution_id)>0){
							$sql = "update ".$table." set ticket = ticket + ".$ticket." where id = ".$distribution_id;
							$result = $GLOBALS['db']->query($sql);
						}else{
							//插入:分销日志
							$video_prop = array();
							$video_prop['from_user_id'] = $user_id;
							$video_prop['to_user_id'] = $to_user_id;
							$video_prop['create_date'] = "'".to_date(NOW_TIME,'Y-m-d')."'";
							$video_prop['ticket'] = $ticket;
							$video_prop['create_time'] = NOW_TIME;
							$video_prop['create_ym'] = to_date($video_prop['create_time'],'Ym');
							$video_prop['create_d'] = to_date($video_prop['create_time'],'d');
							$video_prop['create_w'] = to_date($video_prop['create_time'],'W');

							//将日志写入mysql表中
							$field_arr = array('from_user_id', 'to_user_id','create_date','ticket', 'create_time','create_ym','create_d','create_w');
							$fields = implode(",",$field_arr);
							$valus = implode(",",$video_prop);

							$sql = "insert into ".$table."(".$fields.") VALUES (".$valus.")";
							$GLOBALS['db']->query($sql);
							$result = $GLOBALS['db']->insert_id();
						}
						if(intval($result)>0){
							$sql = "update ".DB_PREFIX."user set ticket = ticket + ".$ticket." where id = ".$to_user_id;
							$info =$GLOBALS['db']->query($sql);
						}
					}

				}
											
				
				$user_ids=array();
				$user_ids[]=$to_podcast_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 买家已确认！";
				if($info['is_true']==1){
					$content="竞拍：‘".$info['name']."’ 买家已确认收货！";
				}
				if($order_data['order_type'] == 'shop'){
					$goods_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods WHERE id=".$order_data['goods_id']);
					$content="商品：‘".$goods_info['name']."’ 买家已确认收货！";
				}
				$rs=FanweServiceCall("message","send",array("send_type"=>'viewer_to_over_tryst',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status=10031;
			}						
				
			return array("status"=>$status);
		
		}
		
		/**
		 * 买家提醒约会
		 * return array("status"=>$status);
		 */
		function remind_podcast_to_date($data){
		
			$podcast_id = (int)$data['to_podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['user_id'];
			
			$user_ids=array();
			$user_ids[]=$podcast_id;
			$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
			$content="买家提醒您‘".$info['name']."’ 的约会时间为‘".$info['date_time']."’";
			$send_type='tip_podcast_to_tryst';
			if($info['is_true']==1){
				$content="买家提醒您‘".$info['name']."’ 的发货啦";
				$send_type='tip_podcast_to_goods';
			}
			$rs=FanweServiceCall("message","send",array("send_type"=>$send_type,"user_ids"=>$user_ids,"content"=>$content));
			$status = $rs['status'];
			return array("status"=>$status);
		
		}
		
		/**
		 * 买家提醒主播确认约会
		 * return array("status"=>$status);
		 */
		function remind_podcast_to_confirm_date($data){
		
			$podcast_id = (int)$data['to_podcast_id'];
			$order_sn  = trim($data['order_sn']);
			$to_buyer_id = (int)$data['user_id'];
				
			$user_ids=array();
			$user_ids[]=$podcast_id;
			$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
			$content="买家提醒您确认完成约会‘".$info['name']."’";
			if($info['is_true']==1){
				$content="买家提醒您‘".$info['name']."’ ，的发货啦";
			}
			$rs=FanweServiceCall("message","send",array("send_type"=>'tip_podcast_to_over_tryst',"user_ids"=>$user_ids,"content"=>$content));
			$status = $rs['status'];
			return array("status"=>$status);
		
		}
		
		/**
		 * 买家要求退款
		 * return array("status"=>$status);
		 */
		function buyer_to_refund($data){
		
			$user_id = (int)$data['user_id'];
			$order_sn  = trim($data['order_sn']);
			$to_podcast_id = (int)$data['to_podcast_id'];
			
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =3");
				
			if (!$goods_order_info) {
				$status=10031;
				return array("status"=>$status);
			}
				
			$order_info=array();
			$order_info['order_status']=5;
			$order_info['refund_buyer_status']=1;
			$order_info['order_status_time']=NOW_TIME;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
					
				$order_data=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
				$order_id=intval($order_data['id']);
				$pai_id=intval($order_data['pai_id']);
		
				$sql = "update ".DB_PREFIX."pai_goods set order_status = 5 where id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
				
				$sql = "update ".DB_PREFIX."pai_join set order_status = 5 where user_id=".$user_id." and pai_id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
		
				$user_ids=array();
				$user_ids[]=$to_podcast_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 买家要求退款！";
				$rs=FanweServiceCall("message","send",array("send_type"=>'to_refund',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status=10031;
			}
		
			return array("status"=>$status);
		
		}
		
		
		/**
		 * 买家投诉/（实物，申请售后）
		 * return array("status"=>$status);
		 */
		function buyer_to_complaint($data){
		
			$user_id = (int)$data['user_id'];
			$order_sn  = trim($data['order_sn']);
			$to_podcast_id = (int)$data['to_podcast_id'];
				
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' ");
		
			if (!$goods_order_info) {
				$status=10031;
				return array("status"=>$status);
			}
		
			$order_info=array();
			//$order_info['order_status']=5;
			$order_info['refund_platform']=3;
			//$order_info['order_status_time']=NOW_TIME;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
					
				$order_data=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
				$order_id=intval($order_data['id']);
				$pai_id=intval($order_data['pai_id']);
		/*
				$sql = "update ".DB_PREFIX."pai_goods set order_status = 5 where id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
		
				$sql = "update ".DB_PREFIX."pai_join set order_status = 5 where user_id=".$user_id." and pai_id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
		*/
				$user_ids=array();
				$user_ids[]=$to_podcast_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 买家提交申诉！";
				$rs=FanweServiceCall("message","send",array("send_type"=>'to_refund',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status=10031;
			}
		
			return array("status"=>$status);
		
		}
		
		/**
		 * 买家确认退货
		 * return array("status"=>$status);
		 */
		function buyer_confirm_to_refund($data){
		
			$user_id = (int)$data['user_id'];
			$order_sn  = trim($data['order_sn']);
			$to_podcast_id = (int)$data['to_podcast_id'];
				
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =5");
		
			if (!$goods_order_info) {
				$status=10031;
				return array("status"=>$status);
			}
		
			$order_info=array();
			$order_info['refund_buyer_status']=2;
			$order_info['order_status_time']=NOW_TIME;
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){
		
				$user_ids=array();
				$user_ids[]=$to_podcast_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 买家已经退货！";
				$rs=FanweServiceCall("message","send",array("send_type"=>'to_refund',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
			}else{
				$status=10031;
			}
		
			return array("status"=>$status);
		
		}
		
		/**
		 * 主播推送商品//缺少商品表
		 * return array("status"=>$status,"data"=>$data);
		 */
		function push_goods($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$goods_id = (int)$data['goods_id'];
			//缺少商品表
		}
		
		/**
		 * 买家撤销
		 * return array("status"=>$status);
		 */
		function oreder_revocation($data){
		
			$user_id = (int)$data['user_id'];
			$order_sn  = trim($data['order_sn']);
			$to_podcast_id = (int)$data['to_podcast_id'];
			
			$goods_order_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order  WHERE order_sn='".$order_sn."' and order_status =5 and refund_buyer_status=1");
			
			if (!$goods_order_info) {
				$status=10031;
				return array("status"=>$status);
			}
			
			$order_info=array();
			$order_info['refund_buyer_status']=4;
			/*$order_info['order_status']=4;
			$order_info['order_status_time']=NOW_TIME;*/
			$status = $GLOBALS['db']->autoExecute(DB_PREFIX."goods_order", $order_info, $mode = 'UPDATE', "order_sn='".$order_sn."'");
			if($status){

				$order_data=$GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."goods_order WHERE order_sn='".$order_sn."'");
				$order_id=intval($order_data['id']);
				$pai_id=intval($order_data['pai_id']);
				$diamonds=intval($order_data['podcast_ticket']);
				
				/*$sql = "update ".DB_PREFIX."pai_goods set order_status = 4 where id=".$pai_id." ";
				$GLOBALS['db']->query($sql);
				
				$sql = "update ".DB_PREFIX."pai_join set order_status = 4 where user_id=".$user_id." and pai_id=".$pai_id." ";
				$GLOBALS['db']->query($sql);*/
				
				fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
				fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$sql = "update ".DB_PREFIX."user set diamonds = diamonds + ".$diamonds." where id = ".intval($to_podcast_id);
				$GLOBALS['db']->query($sql);
				user_deal_to_reids(array(intval($to_podcast_id)));
				$account_diamonds = $user_redis->getOne_db(intval($to_podcast_id), 'diamonds');
				
				$pai_info=$GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id=".$pai_id);
				//会员账户 秀豆变更日志表
				$time = NOW_TIME;
				$diamonds_log_data = array(
						'pai_id' => $pai_id,
						'user_id' => intval($to_podcast_id),
						'diamonds'=>$diamonds,//变更数额
						'account_diamonds'=>$account_diamonds,//账户余额
						'memo' =>$pai_info['name'].'竞拍收益',//备注
						'create_time' => $time,
						'create_date' => to_date($time,'Y-m-d H:i:s'),
						'create_time_ymd'  => to_date($time,'Y-m-d'),
						'create_time_y'  => to_date($time,'Y'),
						'create_time_m'  => to_date($time,'m'),
						'create_time_d'  => to_date($time,'d'),
						'type' =>3,//3 竞拍收益
				);
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_diamonds_log",$diamonds_log_data);
				//卖方收款
				//写入用户日志
				$data = array();
				$data['diamonds'] = $diamonds;
				$param['type'] = 8;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 8竞拍记录
				$log_msg = $pai_info['name'].'竞拍收益';//备注
				account_log_com($data,intval($to_podcast_id),$log_msg,$param);
				
				$user_ids=array();
				$user_ids[]=$to_podcast_id;
				$info = $GLOBALS['db']->getRow("SELECT pg.* FROM ".DB_PREFIX."pai_goods as pg LEFT JOIN ".DB_PREFIX."goods_order as po ON po.pai_id = pg.id  WHERE po.order_sn='".$order_sn."'");
				$content="竞拍：‘".$info['name']."’ 买家撤销退款！";
				$rs=FanweServiceCall("message","send",array("send_type"=>'viewer_to_over_tryst',"user_ids"=>$user_ids,"content"=>$content));
				$status = $rs['status'];
					
			}else{
				$status=10032;
			}
											
			return array("status"=>$status);
			
		}
		
		/**
		 * 虚拟产品标签
		 * return array("list"=>$list);
		 */
		function tags($data){
		
			$list=$GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_tags order by sort");
			return array("list"=>$list);
		}
		
		/**
		 * 创建订单
		 * return $return_data;;
		 */
		function create_order($data){
		
			$pai_id = (int)$data['pai_id'];
			$user_id = (int)$data['user_id'];
			$auth = trim($data['auth']);
			
			$info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_goods WHERE id=".$pai_id." ");
			$user_pai_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." and user_id=".$user_id."  ORDER BY id DESC ");
			$diamonds=$user_pai_info['qp_diamonds']+$user_pai_info['jj_diamonds']*$user_pai_info['pai_sort'];
			$time=NOW_TIME;
			
			$order_data=array();
			$order_data['order_source']='local';
			$order_data['no_refund']=0;
			$order_data['order_type']='pai';
			if ($info['is_true']==1) {
				$order_data['order_type']='pai_goods';
				$order_data['goods_id']=$info['goods_id'];
			}
			$order_data['order_sn']=to_date($time,"Ymdhis").rand(10,99);
			$order_data['order_status']=1;
			$order_data['refund_buyer_status']=0;
			$order_data['refund_buyer_delivery']=0;
			$order_data['refund_seller_status']=0;
			$order_data['refund_platform']=0;
			$order_data['number']=1;
			$order_data['total_diamonds']=$user_pai_info['pai_diamonds'];
			$order_data['remote_total_diamonds']=0;
			$order_data['remote_cost_diamonds']=0;
			$order_data['goods_diamonds']=$user_pai_info['pai_diamonds'];
			$order_data['pay_diamonds']=0;

			$m_config =  load_auto_cache("m_config");//初始化手机端配置
			$ticket_exchange_rate = $m_config['ticket_exchange_rate'];
			if (intval($info['is_true'])==1) {
				$order_data['podcast_ticket']=round(($user_pai_info['pai_diamonds']-$info['qp_diamonds'])*$ticket_exchange_rate,2);
			}else{
				$order_data['podcast_ticket']=round($user_pai_info['pai_diamonds']*$ticket_exchange_rate,2);
			}

			$order_data['refund_diamonds']=0;
			$order_data['freight_diamonds']=0;
			$order_data['memo']="";
			$order_data['consignee']=$user_pai_info['consignee'];
			$order_data['consignee_mobile']=$user_pai_info['consignee_mobile'];
			$order_data['consignee_district']=$user_pai_info['consignee_district'];
			$order_data['consignee_address']=$user_pai_info['consignee_address'];
			$order_data['create_time']=$time;
			$order_data['create_date']=to_date($time,'Y-m-d H:i:s');
			$order_data['create_time_ymd']=to_date($time,'Y-m-d');
			$order_data['create_time_y']=to_date($time,'Y');
			$order_data['create_time_m']=to_date($time,'m');
			$order_data['create_time_d']=to_date($time,'d');
			
			$order_data['podcast_id']=$info['podcast_id'];
			$order_data['viewer_id']=$user_id;
			$order_data['pai_id']=$pai_id;
			$return_data=array();
			if ($GLOBALS['db']->autoExecute(DB_PREFIX."goods_order",$order_data,"INSERT")) {
				$order_id = $GLOBALS['db']->insert_id();
				$return_data = $GLOBALS['db']->getRow("SELECT id as order_id,order_source,order_type,order_sn,order_status,no_refund,number,total_diamonds,goods_diamonds,pay_diamonds,podcast_ticket,memo,consignee,consignee_mobile,consignee_district,consignee_address,create_time FROM ".DB_PREFIX."goods_order WHERE id=".$order_id);
				
				//更新pai_goods 与 pai_join 中的order_id
				$sql = "update ".DB_PREFIX."pai_goods set order_id =".$order_id.",order_status=1 ,order_time='".$order_data['create_date']."' where id=".$pai_id ;
				$GLOBALS['db']->query($sql);
				//更新pai_join 中的order_time为int型
				$sql = "update ".DB_PREFIX."pai_join set order_id =".$order_id.",order_status=1,order_time=".$time." ,pai_status = 1  where id=".$user_pai_info['id'] ;
				$GLOBALS['db']->query($sql);
				
				$user_ids=array();
				$user_ids[]=$user_id;
				$content="您参与的竞拍：‘".$info['name']."’ 已中拍，请在15分钟内完成付款，否则将扣除您缴纳的保证金！";
				FanweServiceCall("message","send",array("send_type"=>'tip_to_pay',"user_ids"=>$user_ids,"content"=>$content));
				
				
				//推送
				
				fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
        		fanwe_require(APP_ROOT_PATH . 'system/schedule/android_list_schedule.php');
        		fanwe_require(APP_ROOT_PATH . 'system/schedule/ios_list_schedule.php');
				
				$user_type = $GLOBALS['db']->getRow("SELECT apns_code,device_type FROM ".DB_PREFIX."user WHERE id=".$user_id);
				if (intval($user_type['device_type'])==1) {
					//安卓推送信息
					$apns_app_code_list=array();
					$apns_app_code_list[]=$user_type['apns_code'];
					
					$AndroidList = new android_list_schedule();
					$data = array(
							'dest' =>implode(",",$apns_app_code_list),
							'content' =>$content,
							'user_id'=>$user_id,
							'room_id' => 0,
							'url'=>$auth.'/wap/index.php?ctl=pai_user&act=goods',
							'type'=>5,
					);
					$ret_android =$AndroidList->exec($data);
				
				}elseif (intval($user_type['device_type'])==2) {
					//ios 推送信息
					$apns_ios_code_list=array();
					$apns_ios_code_list[]=$user_type['apns_code'];
					
					$IosList = new ios_list_schedule();
					$ios_data = array(
							'dest' =>implode(",",$apns_ios_code_list),
							'content' =>$content,
							'user_id'=>$user_id,
							'room_id' => 0,
							'url'=>$auth.'/wap/index.php?ctl=pai_user&act=goods',
							'type'=>5,
					);
					$ret_ios = $IosList->exec($ios_data);
				}



			}	
			return $return_data;
		}
		
		

		/**
		 * 创建竞拍时检查
		 * return array("status"=>$status);
		 */
		function check($data){
			return array("status"=>1);
			$podcast_id = (int)$data['podcast_id'];
				
			//判断是否禁止中
			$time=NOW_TIME;
			//$violations_month=to_date($time,'Y-m');
			//$now_day=to_date($time,'Y-m-d');			
			
			$violations_begin_time=$time-(30+PAI_CLOSE_VIOLATIONS)*86400;
			$violations_end_time=$time-PAI_CLOSE_VIOLATIONS*86400;
			
			$violations_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."violations WHERE create_time BETWEEN ".$violations_begin_time."  and ".$violations_end_time);
			if ($violations_count>=PAI_MAX_VIOLATIONS) {
				$status=10051;
				return array("status"=>$status);
			}
			
			//判断30天内是否违规超标
			$begin_time=$time-(30)*86400;
			$end_time=$time;
			$now_violations_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."violations WHERE create_time BETWEEN ".$begin_time."  and ".$end_time);
			if ($now_violations_count>=PAI_MAX_VIOLATIONS) {
				$status=10051;
				return array("status"=>$status);
			}
			
			
			//是否有开启的竞拍
			$rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_goods WHERE podcast_id=".$podcast_id." and status=0");
			if ($rs_count>0) {
				$status=10049;
				return array("status"=>$status);
			}
			
			return array("status"=>1);
				
		}
		
		/**
		 * 主播 - 竞拍下架（暂留）
		 * return array("status"=>$status);
		 */
		function del($data){
		
			$podcast_id = (int)$data['podcast_id'];
			$pai_id = (int)$data['pai_id'];
			
			$sql = "update ".DB_PREFIX."pai_goods set is_delete =1  where id=".$pai_id." and podcast_id=".$podcast_id  ;
			$GLOBALS['db']->query($sql);
				
			return array("status"=>1);
		
		}


	}
?>
