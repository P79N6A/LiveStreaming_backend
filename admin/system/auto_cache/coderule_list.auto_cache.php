<?php

class coderule_list_auto_cache extends auto_cache{
	private $key = "coderule:list";
	public function load($param)
	{
		$list = $GLOBALS['cache']->get($this->key);

		if($list === false)
		{
			//unserialize(
			$sql = "select id,name,money,(diamonds + gift_diamonds) as diamonds,gift_coins,iap_money,qr_code from ".DB_PREFIX."recharge_rule where is_effect = 1 and is_delete = 0 and product_id != '' order by sort";
			$list = $GLOBALS['db']->getAll($sql,true,true);
			foreach($list as $k=>$v){
				$list[$k]['name'] = '扫码'.$v['name'];
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