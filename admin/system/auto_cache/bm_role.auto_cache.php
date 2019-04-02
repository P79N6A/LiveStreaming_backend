<?php
//
class bm_role_auto_cache extends auto_cache{
	public function load($param)
	{
		
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$return = $GLOBALS['cache']->get($key);
		$return = false;
		if($return === false)
		{
			$cate_name_list = $GLOBALS['db']->getAll("select cate_name from ".DB_PREFIX."bm_role_node  where is_delete=0 and is_effect=1 group by cate_name");
 			$role_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."bm_role_node  where is_delete=0 and is_effect=1 order by cate_name,id");
 			
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
 			
 			if (intval($param['id'])>0) {
 				$role_info  = $GLOBALS['db']->getRow("select * from  ".DB_PREFIX."bm_role where id=".intval($param['id']));
 				//$user_role_list = json_decode($role_info['role_list'],true);
 				$user_role_list = unserialize($role_info['role_list']);
 				
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
 			
 			

			$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
			$GLOBALS['cache']->set($key,$cate_name_list);
		}
		return $cate_name_list;
	}
	public function rm($param)
	{
		$key = $this->build_key(__CLASS__,$param);
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->rm($key);
	}
	public function clear_all()
	{
		$GLOBALS['cache']->set_dir(APP_ROOT_PATH."public/runtime/data/".__CLASS__."/");
		$GLOBALS['cache']->clear();
	}
}
?>