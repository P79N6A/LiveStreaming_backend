<?php

class DollCateAction extends CommonAction{
	public function index()
	{
		if(trim($_REQUEST['title'])!='')
		{
            $map['title'] = array('like','%'.trim($_REQUEST['title']).'%');
		}

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
		//禁止删除已被房间采用的娃娃
		$is_used = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."dolls where cate_id in (".$id.")");
		if($is_used)
		{
			$result['status'] = 0;
    		$result['info'] = '所选娃娃有房间使用，无法删除';
    		admin_ajax_return($result);
		}

		if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $rel_data = M(MODULE_NAME)->where($condition)->findAll();
            foreach($rel_data as $data)
            {
                $info[] = $data['title'];
            }
            if($info) $info = implode(",",$info);
            $list = M(MODULE_NAME)->where ( $condition )->delete();
            if ($list!==false) {
                save_log($info.l("DELETE_SUCCESS"),1);
                $result['status'] = 1;
                $result['info'] = '删除成功';
            } else {
                save_log($info.l("DELETE_FAILED"),0);
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
		if(!check_empty($data['title']))
		{
			$this->error("娃娃标题不能为空");
		}	
		if(!check_empty($data['img']))
		{
			$this->error("请上传娃娃图片");
		}
		if(!check_empty($data['content']))
		{
			$this->error("娃娃内容不能为空");
		}		
		if(!check_empty($data['equal_diamonds']))	
		{
			$data['equal_diamonds'] = 0;	//默认兑换秀豆量为0
		}	
		// 更新数据
		$log_info = $data['title'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("title");

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['title']))
		{
			$this->error("娃娃名字不能为空");
		}	
		if(!check_empty($data['img']))
		{
			$this->error("请上传娃娃图片");
		}
		if(!check_empty($data['content']))
		{
			$this->error("娃娃内容不能为空");
		}		
		if(!check_empty($data['equal_diamonds']))
		{
			$this->error("兑换秀豆量不能为空");
		}		
		// 更新数据
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		$list=M(MODULE_NAME)->save ($data);

		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	//
	public function detail()
    {
        $id = intval($_REQUEST ['id']);
        $vo = M(MODULE_NAME)->where(['id' => $id])->find();
        $this->assign('vo', $vo);
        $this->display();
    }
	
}
?>