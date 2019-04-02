<?php

class DollFreightAction extends CommonAction{
	public function index()
	{
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=$this->getActionName();
        $model = D ($name);
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
	}

	public function add()
	{
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            $list = M(MODULE_NAME)->where ( $condition )->delete();
            if ($list!==false) {
                $result['status'] = 1;
                $result['info'] = '删除成功';
            } else {
                $result['status'] = 0;
                $result['info'] = '删除失败';
            }
        } else {
            $result['status'] = 0;
            $result['info'] = '请选择要删除的选项';
		}
		admin_ajax_return($result);
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['min']))
		{
			$this->error("请输入娃娃最小的数量");
		}	
		if($data['min']==1){
			$this->error("娃娃数量要大于1");
		}
		if(!check_empty($data['max']))
		{
			$this->error("请输入娃娃最大的数量");
		}
		if($data['min']>$data['max']){
			$this->error("请输入正确的娃娃数量区间");
		}
		$rel_data = M(MODULE_NAME)->findAll();
        foreach($rel_data as $k => $v)
        {
            if(($v['min']<=$data['min']) && ($data['min']<=$v['max'])){
            	$this->error("请输入正确的娃娃数量区间");
            }
        }
		if(!check_empty($data['freight_multiple']))
		{
			$this->error("运费倍数不能为空");
		}			
		// 更新数据

		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['min']))
		{
			$this->error("请输入娃娃最小的数量");
		}	
		if($data['min']==1){
			$this->error("娃娃数量要大于1");
		}
		if(!check_empty($data['max']))
		{
			$this->error("请输入娃娃最大的数量");
		}
		if($data['min']>$data['max']){
			$this->error("请输入正确的娃娃数量区间");
		}
		$condition['id'] = array('neq',$data['id']);
		$rel_data = M(MODULE_NAME)->where($condition)->findAll();
        foreach($rel_data as $k => $v)
        {
            if(($v['min']<=$data['min']) && ($data['min']<=$v['max'])){
            	$this->error("请输入正确的娃娃数量区间");
            }
        }
		if(!check_empty($data['freight_multiple']))
		{
			$this->error("运费倍数不能为空");
		}	
		
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$this->error(L("UPDATE_FAILED"));
		}
	}
}
?>