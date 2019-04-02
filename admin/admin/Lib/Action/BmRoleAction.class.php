<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmRoleAction extends CommonAction{
	public function index()
	{
		$condition['is_delete'] = 0;
		$condition['promoter_id'] = 0;
		$this->assign("default_map",$condition);
		parent::index();
	}
	/*public function trash()
	{
		if(intval($_REQUEST['action_id'])!='')
		{
			$action_id= intval($_REQUEST['action_id']);
		}
		$this->assign('action_id',$action_id);
		$condition['is_delete'] = 1;
		$this->assign("default_map",$condition);
		parent::index();
	}*/
	public function add()
	{
		$admin_role= load_auto_cache("bm_role");

		$this->assign("navs",$admin_role);
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		//输出module与action
		//@by slf
		$admin_role= load_auto_cache("bm_role",array('id'=>$vo['id']));
		$this->assign("navs",$admin_role);
		$this->display ();
	}
	//相关操作
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	public function insert() {		
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));

		if(!check_empty($data['name']))
		{
			$this->error(L("ROLE_NAME_EMPTY_TIP"));
		}	
		// 更新数据
		$log_info = $data['name'];
		$role_id=M(MODULE_NAME)->add($data);
		if (false !== $role_id) {
			//开始关联节点
			$role_access = $_REQUEST['role_access'];
			$role_list=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_role_node where  id  in (" . implode(',', $role_access) . ")", true, true);

			//$role_list_str=json_encode($role_list);
			$role_list_str=serialize($role_list);
			$role_node['role_list']=$role_list_str;
			$role_node['all_role_list']=$role_list_str;
			$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_role", $role_node, $mode = 'UPDATE', "id=" . $role_id);
						
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
		//print_r(MODULE_NAME);exit;
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		$data_info = M(MODULE_NAME)->where("id=" . intval($data['id']))->find();
		$log_info = $data_info['name'];
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("ROLE_NAME_EMPTY_TIP"));
		}
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {

			//开始关联节点
			$role_access = $_REQUEST['role_access'];
			$role_list=$GLOBALS['db']->getAll("select * from " . DB_PREFIX . "bm_role_node where  id  in (" . implode(',', $role_access) . ")", true, true);
			
			//$role_list_str=json_encode($role_list);
			$role_list_str=serialize($role_list);
			$role_node['role_list']=$role_list_str;
			$role_node['all_role_list']=$role_list_str;
			$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_role", $role_node, $mode = 'UPDATE', "id=" . intval($data['id']));
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));

			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"));
		}
	}

	public function del_admin() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];
					//开始验证分组下是否存在管理员
					if(M("BmPromoter")->where("is_effect = 1 and is_delete = 0 and role_id=".$data['id'])->count()>0)
					{
                        $this->error ($data['name'].'分组正在被使用',$ajax);
					}
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	public function restore() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					save_log($info.l("RESTORE_SUCCESS"),1);
					$this->success (l("RESTORE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("RESTORE_FAILED"),0);
					$this->error (l("RESTORE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$role_access_condition = array ('role_id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];
					//开始验证分组下是否存在管理员
					if(M("BmPromoter")->where("is_effect = 1 and is_delete = 0 and role_id=".$data['id'])->count()>0)
					{
						$this->error ($data['name'].l("EXIST_ADMIN"),$ajax);
					}	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				M("RoleAccess")->where($role_access_condition)->delete();
				M("Admin")->where($role_access_condition)->delete();
				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
}
?>