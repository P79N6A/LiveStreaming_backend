<?php

class DollExchangeThingAction extends CommonAction{
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
		$cate_tree = M("DollExchangeCate")->where('is_effect = 1')->findAll();
		$this->assign("cate_tree",$cate_tree);
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$cate_tree = M("DollExchangeCate")->where('is_effect = 1')->findAll();
		$this->assign("cate_tree",$cate_tree);
		$this->display ();
	}
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
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
			$this->error("实物标题不能为空");
		}	
		if(!check_empty($data['img']))
		{
			$this->error("请上传实物图片");
		}
		if(!check_empty($data['content']))
		{
			$this->error("实物内容不能为空");
		}
		if(!check_empty($data['score']))
        {
            $this->error("请输入积分");
        }
        if(!(intval($data['score'])>0))
        {
            $this->error("积分必须大于0");
        }
        if(!check_empty($data['number']))
        {
            $this->error("请输入数量");
        }
        if(!(intval($data['number'])>0))
        {
            $this->error("数量必须大于0");
        }	
        if($data['doll_exchange_cate_id']==0)
		{
			$this->error(L("分类不能为空"));
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
			$this->error("实物名字不能为空");
		}	
		if(!check_empty($data['img']))
		{
			$this->error("请上传实物图片");
		}
		if(!check_empty($data['content']))
		{
			$this->error("实物内容不能为空");
		}	
		if(!check_empty($data['score']))
        {
            $this->error("请输入积分");
        }
        if(!(intval($data['score'])>0))
        {
            $this->error("积分必须大于0");
        }	
        if(!check_empty($data['number']))
        {
            $this->error("请输入数量");
        }
        if(!(intval($data['number'])>0))
        {
            $this->error("数量必须大于0");
        }		
        if($data['doll_exchange_cate_id']==0)
		{
			$this->error(L("分类不能为空"));
		}
		// 更新数据
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
	
	public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        if(!check_sort($sort))
        {
            $this->error(l("SORT_FAILED"),1);
        }
        M("Faq")->where("id=".$id)->setField("sort",$sort);
        $this->success(l("SORT_SUCCESS"),1);
    }

    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
    }
	
}
?>