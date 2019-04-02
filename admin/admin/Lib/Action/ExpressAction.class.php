<?php

class ExpressAction extends CommonAction{

    public function index()
	{
		$config = M("ExpressQuery")->order("sort asc")->findAll();
		//print_r($config);exit;
  		$this->assign("config",$config);
		$this->display();
	}
	
	public function save_express_query()
	{
		foreach($_POST as $k=>$v)
		{
			M("ExpressQuery")->where("code='".$k."'")->setField("val",trim($v));
		}
		$this->success("保存成功");
	}	
}
?>