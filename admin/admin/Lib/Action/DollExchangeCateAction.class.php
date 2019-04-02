<?php

class DollExchangeCateAction extends CommonAction{
    public function index()
    {
        if(strim($_REQUEST['title'])!=''){
            $map['title'] = array('like','%'.strim($_REQUEST['title']).'%');
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
        $this->assign("new_sort", M("DollExchangeCate")->max("sort")+1);
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
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
        	if(M("DollExchangeThing")->where(array ('doll_exchange_cate_id' => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$this->error(l("该分类有实物，不能删除！"),$ajax);
			}
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $list = M(MODULE_NAME)->where ( $condition )->delete();
            if ($list!==false) {
            	$GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }



    public function insert() {
        B('FilterString');
        $data = M(MODULE_NAME)->create();
        //开始验证有效性
        $this->assign("jumpUrl",u(MODULE_NAME."/add"));
        $data['title'] = strim($data['title']);
        if(!check_empty($data['title']))
        {
            $this->error("请输入分类名称");
        }
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."doll_exchange_cate where title = '".$data['title']."'");
        if($cate_id){
            $this->error("分类名称已存在");
        }
        // 更新数据
        $log_info = $data['title'];
        $list=M(MODULE_NAME)->add($data);

        if (false !== $list) {
        	$GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");
            
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
            $this->error("请输入分类名称");
        }
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."doll_exchange_cate where title = '".$data['title']."'");
        if($cate_id && $cate_id!=$data['id']){
            $this->error("分类名称已存在，请重新填写！");
        }
        if(intval($data['is_effect'])==0){
            $doll_exchange_thing = M('DollExchangeThing')->where("doll_exchange_cate_id=".intval($data['id'])." ")->findAll();
            if($doll_exchange_thing){
                $this->error("该分类下面有兑换实物设置,不能设置无效");
            }
        }
        $list=M(MODULE_NAME)->save ($data);
        if (false !== $list) {
        	$GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");
            save_log($log_info.L("UPDATE_SUCCESS"),1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);
            $this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $info = M(MODULE_NAME)->where("id=".$id)->getField("title");
        $c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        if($n_is_effect==0){
            $doll_exchange_thing = M('DollExchangeThing')->where("doll_exchange_cate_id=".$id." ")->findAll();
            if($doll_exchange_thing){
                $this->error("该分类下面有实物,不能设置无效");
            }
        }
        M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);
        $GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");     
        save_log($info.l("SET_EFFECT_".$n_is_effect),1);
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("title");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
    }

}
?>