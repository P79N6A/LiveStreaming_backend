<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH.'mapi/lib/rank.action.php');
class rankCModule  extends rankModule
{
	
	//游戏排行
	public function game(){
		$root = array('status' => 1,'error'=>'');
		//分页
		$page = intval($_REQUEST['p']);//当前页
		$page_size = 30;//分页数量
		if ($page == 0) {
			$page = 1;
		}
	
		$game_id = intval($_REQUEST['game_id']);//游戏类型
		$rank_name = strim($_REQUEST['rank_name']);//贡献榜 名称  日，月，总
		$user_id = intval($GLOBALS['user_info']['id']);//登录用户id
		$table = DB_PREFIX . 'user_game_log_history';
		$rank_names = array('day','month','all');
		$m_config =  load_auto_cache("m_config");//初始化手机端配置
		
		
		if ($game_id==0) {
			$root['status'] = 0;
			$root['error'] = '参数错误！';
			ajax_return($root);
		}
		
		//贡献榜
		if(in_array($rank_name,$rank_names)){
			$rank_cache_name = "rank_".$rank_name;
			$rank_cache_time = $m_config[$rank_cache_name];
			$rank_cache_default =array("rank_day"=>1800,"rank_month"=>28800,"rank_all"=>86400);
			if($rank_cache_time<$rank_cache_default[$rank_cache_name]){
				$rank_cache_time = $rank_cache_default[$rank_cache_name];
			}
			$rank_cache_time = $rank_cache_time!=''?$rank_cache_time:86400;
				
			$param = array('game_id'=>$game_id,'rank_name'=>$rank_name,'table'=>$table,'page'=>$page,'page_size'=>$page_size,'cache_time'=>$rank_cache_time);
			$list = load_auto_cache("rank_game",$param);
				
			fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
			$user_redis = new UserFollwRedisService($user_id);
			$keys = $user_redis->following();
	
			foreach($list as $k=>$v) {
				$list[$k]['head_image'] = get_spec_image($v['head_image']);
				$list[$k]['nick_name'] = htmlspecialchars_decode($v['nick_name']);
				$list[$k]['ticket'] =intval($v['ticket']) ;
				$list[$k]['coins'] =intval($v['ticket']) ;
				if($user_id>0){
					if (in_array($v['user_id'],$keys)){
						$list[$k]['is_focus'] = 1;
					}else{
						$list[$k]['is_focus'] = 0;
					}
				}else{
					$list[$k]['is_focus'] = 0;
				}
			}
				
			$root['list'] = $list;
			$count = count($list);
		}else{
			$root['status'] = 0;
			$root['error'] = '参数错误！';
			ajax_return($root);
		}
		$root['page']=$page;
		$has_next = ($count > $page * $page_size) ? 1 : 0;
		$root['has_next']=$has_next;
		ajax_return($root);
	}
	
}

?>