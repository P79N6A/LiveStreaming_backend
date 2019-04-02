<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
fanwe_require(APP_ROOT_PATH.'mapi/lib/video.action.php');
class videoCModule  extends videoModule
{
	/**
	 * 当前房间用户列表（包括机器人，但不包括虚拟人数）
	 */
	public function viewer(){
		
		$root = array();
		$group_id = strim($_REQUEST['group_id']);//聊天群id
		$page = intval($_REQUEST['p']);//取第几页数据
		$root = load_auto_cache("video_viewer",array('group_id'=>$group_id,'page'=>$page));
		
		/*if ($group_id=='@TGS#a27QEMEEF') {
			log_result('test1');
			//log_result($root);
			//log_result(OPEN_PAI_MODULE);
			//log_result($page);
		}*/
		if (OPEN_PAI_MODULE==1&&($page==1||$page==0)) {
			//增加竞拍排序
			$sql = "select pai_id from ".DB_PREFIX."video where group_id = '".$group_id."'";
			$video['pai_id'] = $GLOBALS['db']->getOne($sql);
			if (intval($video['pai_id'])>0) {
				$user_list = $GLOBALS['db']->getAll("SELECT user_id,pai_status,order_id,order_status,pai_diamonds FROM ".DB_PREFIX."pai_join WHERE pai_id=".$video['pai_id']." ORDER BY pai_diamonds DESC limit 0,5");
				//log_result("SELECT user_id,pai_status,order_id,order_status,pai_diamonds FROM ".DB_PREFIX."pai_join WHERE pai_id=".$video['pai_id']." ORDER BY pai_diamonds DESC ");
				if ($user_list) {
						
					fanwe_require(APP_ROOT_PATH.'/mapi/lib/redis/BaseRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
					fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
					$user_redis = new UserRedisService();
					$fields = array('is_authentication','head_image','user_level','v_type','v_icon','nick_name','signature','sex','province','city','thumb_head_image','v_explain','emotional_state','job','birthday','apns_code');
						
					foreach($user_list as $k=>$v){
	
						if (intval($v['user_id'])>0) {
							$user_list[$k]=$user_redis->getRow_db(intval($v['user_id']),$fields);
							$user_list[$k]['user_id'] = intval($v['user_id']);
							$user_list[$k]['type']=intval($v['pai_status']);
							$user_list[$k]['pai_diamonds']=intval($v['pai_diamonds']);
							$user_list[$k]['head_image']=get_spec_image($user_list[$k]['head_image']);
						}
	
					}
						
					$root['tag']=1;
				}
				$list_1=$root['list'];
				foreach ($list_1 as $k=>$v){
					foreach ($user_list as $k2=>$v2){
						if ($list_1[$k]['user_id']==$v2['user_id']) {
							unset($list_1[$k]);
							break;
						}
							
					}
				}
				if (!$list_1) {
					$list_1=array();
				}
				if (!$user_list) {
					$user_list=array();
				}
				//log_result("==user_list==");
				//log_result($user_list);
				//log_result("==list_1==");
				//log_result($list_1);
				$root['list']=array_merge($user_list,$list_1);
				//log_result("==list_2==");
				/*if ($group_id=='@TGS#a27QEMEEF') {
					log_result('test2');
				log_result($root['list']);
				}*/
				//$root['list']=$user_list;
				//$root['has_next']=0;
				//$root['page']=1;
				//$root['status']=1;
				ajax_return($root);
			}
		}
		ajax_return($root);
		
	}
}
?>

