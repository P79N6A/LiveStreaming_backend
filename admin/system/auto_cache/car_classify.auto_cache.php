<?php

class car_classify_auto_cache extends auto_cache{
	private $key = "car:classify";
	
	public function load($param)
	{
		$list = array();
		
	    if($list == false)
		{
		    //一级分类信息
            $sql = "select id as c1_id,title,classify_image,classify_type,is_show from ".DB_PREFIX."car_classify where is_effect = 1 and classify_type in(1,2) order by sort desc";
            $classify1_list = $GLOBALS['db']->getAll($sql,true,true);
            
            //二级分类信息
            $sql2 = "select id as c2_id,classify1_id,title,classify_image,classify_type,is_show from ".DB_PREFIX."car_classify where is_effect = 1 and classify_type = 3 order by sort desc";
            $classify2_list = $GLOBALS['db']->getAll($sql2,true,true);
            
            //首页导航栏显示的标题
            $sql3 = "select id as c1_id,classify1_id,title,classify_image,classify_type,is_show from ".DB_PREFIX."car_classify where is_effect = 1 and classify_type in(1,2,3) and is_show = 1 order by sort desc";
            $classify3_list = $GLOBALS['db']->getAll($sql3,true,true);
            foreach($classify3_list as $ky => $vl){
                unset($classify3_list[$ky]['is_show']);
                $classify3_list[$ky]['classify2_list'] = [];
                if($vl['classify_type'] == 2){
                    foreach($classify2_list as $k => $v){
                        unset($classify2_list[$k]['is_show']);
                        if($vl['c1_id'] == $v['classify1_id']){
                            $classify3_list[$ky]['classify2_list'][] = $classify2_list[$k];
                        }
                    }
                }
            }
            $list = $classify3_list;
            
            //合并
            /* foreach($classify1_list as $key => $val){
                $classify1_list[$key]['classify2_list'] = [];
                if($val['classify_type'] == 2){
                    foreach($classify2_list as $k => $v){
                        if($val['c1_id'] == $v['classify1_id']){
                            $classify1_list[$key]['classify2_list'][] = $classify2_list[$k];
                        }
                    }
                }
            } */
            //$list['classify_more'] = $classify1_list;
			
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