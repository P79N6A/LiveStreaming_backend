<?php

class DollQuestionAction extends CommonAction{
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
		if(!check_empty($data['question']))
		{
			$this->error("问题不能为空");
		}			
		// 更新数据
		$data['create_time'] = NOW_TIME;
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
		$data = M(MODULE_NAME)->create();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['question']))
		{
			$this->error("问题不能为空");
		}		
		// 更新数据
		$data['create_time'] = NOW_TIME;
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