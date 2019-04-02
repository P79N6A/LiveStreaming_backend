<?php

class car_classify_multi_auto_cache extends auto_cache{
	private $key = "car:classify:multi:";
	
	public function load($param)
	{
		$list = array();
		
	    if($list == false)
		{
		    //分类信息不包含二级菜单,不包含已在导航栏显示的分类
            $sql = "select id as c2_id,classify1_id,title,classify_image,classify_type,is_show from ".DB_PREFIX."car_classify where is_effect = 1 and classify_type in(1,3) and is_show != 1 order by sort desc";
            $list = $GLOBALS['db']->getAll($sql,true,true);
            foreach ($list as $key => $val){
                unset($list[$key]['is_show']);
            }
			
            $GLOBALS['cache']->set($this->key,$list);
		}
		
		return $list;
	}
	
	public function rm($param)
	{
		$GLOBALS['cache']->rm($this->key);
	}
	
	public function clear_all()
	{
		$GLOBALS['cache']->rm($this->key);
	}
}
?>