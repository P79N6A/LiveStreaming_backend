<?php

class society_app_auto_cache extends auto_cache{
	private $key = "society:app";
	public function load($param)
	{
		$user_id = intval($param['user_id']);//用户ID
		//$car_city = $param['car_city'];//用户地址
		$society_filtrate = $param['society_filtrate'];//模糊查询
		
		$key_bf = $this->key.'_bf';
		
		$list = $GLOBALS['cache']->get($this->key,true);
		if ($list === false) {
			$is_ok =  $GLOBALS['cache']->set_lock($this->key);
			if(!$is_ok){
				$list = $GLOBALS['cache']->get($key_bf,true);
			}else{
			    $where = "s.status=1";
			    if($society_filtrate){
			        $where .= " and (s.id like '%".$society_filtrate."%' or s.name like '%".$society_filtrate."%')";
			    }
		        //首选主播所在城市的公会
		        $sql = "select s.id,s.logo,s.name,s.user_count,s.status,u.nick_name,u.id as uid,u.province,u.city,IF(u.luck_num=0,u.id,u.luck_num) as luck_id,s.society_level,s.society_rank,u.province from ".DB_PREFIX."society s inner join ".DB_PREFIX."user u on s.user_id=u.id where ".$where;
		        $list = $GLOBALS['db']->getAll($sql);
		        
		        if(empty($list)){
		            $list = [];
		            return $list;
		        }
			    
				$GLOBALS['cache']->set($this->key, $list, 60, true);
				$GLOBALS['cache']->set($key_bf, $list, 60, true);//备份
			}
 		}
 		if ($list == false) $list = array();
		return $list;
	}
	
	public function rm()
	{

		//$GLOBALS['cache']->clear_by_name($this->key);
	}
	
	public function clear_all()
	{
		
		$GLOBALS['cache']->clear_by_name($this->key);
	}
}
?>