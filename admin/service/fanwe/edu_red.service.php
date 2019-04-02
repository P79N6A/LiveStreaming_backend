<?php

class  edu_redService
{

	public function exchange_red($param){
		$red=$param['red'];
		$user_id=intval($param['user_id']);
		
		$pInTrans = $GLOBALS['db']->StartTrans();
		try{
			$GLOBALS['db']->query("update ".DB_PREFIX."edu_red set exchange_time='".to_date(NOW_TIME)."',user_id=$user_id where sn='".$red['sn']."' and exchange_time='0000-00-00 00:00:00'");
	        if($GLOBALS['db']->affected_rows())
	        {
	        	//加秀豆
	        	$diamonds_status=$this->update_diamonds(array(
	    	 		'ext_id'=>$red['id'],
	    	 		'diamonds'=>$red['diamonds'],
	    	 		'user_id'=>$user_id,
		 			'msg'=>$red['title']."-红包对换成功"
	    	 	));
	        	
	        	$status=1;
	        }else{
	        	$status=0;
	        }
	        $GLOBALS['db']->Commit($pInTrans);
		}catch(Exception $e){
		 	$GLOBALS['db']->Rollback($pInTrans);
          	$status=0;
		}
		
        //更新redis中用户的 diamonds
		fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
		$user_redis = new UserRedisService();
		user_deal_to_reids(array($user_id));
		//$account_diamonds = $user_redis->getOne_db($insert_data['user_id'], 'diamonds'); 
        
        return $status;
	}
	public function update_diamonds($param){
		$diamonds=intval($param['diamonds']);
		$user_id=intval($param['user_id']);
		$ext_id=intval($param['ext_id']);
		$msg=strim($param['msg']);
		
		$time = NOW_TIME;
		
		//减少用户秀豆
		$sql = "update ".DB_PREFIX."user set diamonds = diamonds + ".$diamonds.",use_diamonds = use_diamonds + ".$diamonds." where id = '".$user_id."' ";
		
		$GLOBALS['db']->query($sql);
		
		if($GLOBALS['db']->affected_rows())
		{
			$account_diamonds=$GLOBALS['db']->getOne("select diamonds from ".DB_PREFIX."user where id= ".$user_id." ");
			//会员账户 秀豆变更日志表
			$diamonds_log_data = array(
				'user_id' => $user_id,
				'ext_id' => $ext_id,
				'diamonds'=>$diamonds,//变更数额
				'account_diamonds'=>$account_diamonds,//账户余额
				'memo' =>$msg,//备注
				'create_time' => $time,
				'create_date' => to_date($time,'Y-m-d H:i:s'),
				'create_time_ymd'  => to_date($time,'Y-m-d'),
				'create_time_y'  => to_date($time,'Y'),
				'create_time_m'  => to_date($time,'m'),
				'create_time_d'  => to_date($time,'d'),
				'type' =>4,//1课堂购买   2线下预约   3线上约课  4红包兑换 
			);
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."edu_user_diamonds_log",$diamonds_log_data);
			
			//写入用户日志
			$data = array();
			$data['diamonds'] = $diamonds;
			$param['type'] = 20;//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票 4表示保证金操作 5表示竞拍模块消费 6表示竞拍模块收益 20教育
			$log_msg = $msg;//备注
			account_log_com($data,$user_id,$log_msg,$param);
			$status=1;
		}
		else{
			$status=0;
		}
	}
	
}
