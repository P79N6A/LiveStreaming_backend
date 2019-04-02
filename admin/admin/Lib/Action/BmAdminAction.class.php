<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmAdminAction extends CommonAction{
	public function index()
	{
		
		//$map['member_id'] = -1;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model = D ("BmPromoter");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	
	public function add()
	{
		//输出分组列表
		$this->assign("role_list",M("BmRole")->where("is_delete = 0 and promoter_id=0")->findAll());
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M("BmPromoter")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		$this->assign("role_list",M("BmRole")->where("is_delete = 0 and promoter_id=0")->findAll());
		$this->display ();
	}
	//相关操作
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("BmPromoter")->where("id=".$id)->getField("adm_name");		
		$c_is_effect = M("BmPromoter")->where("id=".$id)->getField("is_effect");  //当前状态
		
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("BmPromoter")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	public function insert() {
		B('FilterString');
		$data = M("BmPromoter")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error(L("ADM_NAME_EMPTY_TIP"));
		}	
		if(!check_empty($data['pwd']))
		{
			$this->error(L("ADM_PASSWORD_EMPTY_TIP"));
		}
		if($data['bm_role_id']==0)
		{
			$this->error(L("ROLE_EMPTY_TIP"));
		}
		if(M("BmPromoter")->where("name='".$data['name']."'")->count()>0)
		{
			$this->error(L("ADMIN_EXIST_TIP"));
		}
		// 更新数据
		$log_info = $data['name'];
		$data['pwd'] = md5(trim($data['pwd']));
		$list=M("BmPromoter")->add($data);
		if (false !== $list) {
			//成功提示
			//二级更新
			
			$bm_promoter['create_time']=NOW_TIME;
			$bm_promoter['status']=1;
			$bm_promoter['member_id']=-1;
			$bm_promoter['login_name']="admin_".$list;
			$bm_promoter['user_id']=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."user where is_robot = 1 and is_effect=1");
			
			$GLOBALS['db']->autoExecute(DB_PREFIX . "bm_promoter", $bm_promoter, $mode = 'UPDATE', "id=" . $list);
			
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
		$data = M("BmPromoter")->create ();
		$log_info = M("BmPromoter")->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['pwd']))
		{
			unset($data['pwd']);  //不更新密码
		}
		else
		{
			$data['pwd'] = md5(trim($data['pwd']));
		}
		if($data['bm_role_id']==0)
		{
			$this->error(L("ROLE_EMPTY_TIP"));
		}
		
		// 更新数据
		$list=M("BmPromoter")->save ($data);
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

	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['adm_name'];
					if(conf("DEFAULT_ADMIN")==$data['adm_name'])
					{
						$this->error ($data['adm_name'].l("DEFAULT_ADMIN_CANNOT_DELETE"),$ajax);
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
					$info[] = $data['adm_name'];	
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
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['adm_name'];	
					if(conf("DEFAULT_ADMIN")==$data['adm_name'])
					{
						$this->error ($data['adm_name'].l("DEFAULT_ADMIN_CANNOT_DELETE"),$ajax);
					}	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
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
	
	public function set_default()
	{
		$adm_id = intval($_REQUEST['id']);
		$admin = M("Admin")->getById($adm_id);
		if($admin)
		{
			M("Conf")->where("name = 'DEFAULT_ADMIN'")->setField("value",$admin['adm_name']);
			//开始写入配置文件
			$sys_configs = M("Conf")->findAll();
			$config_str = "<?php\n";
			$config_str .= "return array(\n";
			foreach($sys_configs as $k=>$v)
			{
				$config_str.="'".$v['name']."'=>'".addslashes($v['value'])."',\n";
			}
			$config_str.=");\n ?>";
						
			$filename = get_real_path()."public/sys_config.php";
			
		    if (!$handle = fopen($filename, 'w')) {
			     $this->error(l("OPEN_FILE_ERROR").$filename);
			}
			
			    
			if (fwrite($handle, $config_str) === FALSE) {
			     $this->error(l("WRITE_FILE_ERROR").$filename);
			}
			
	    	fclose($handle);
	    
			
			save_log(l("CHANGE_DEFAULT_ADMIN"),1);
			clear_cache();
			$this->success(L("SET_DEFAULT_SUCCESS"));
		}
		else
		{
			$this->error(L("NO_ADMIN"));
		}
	}

}
?>