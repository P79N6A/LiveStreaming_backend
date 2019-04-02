<?php

class ExchangeScoreAction extends CommonAction{
    public function index()
    {
        parent::index();
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


    public function foreverdelete() {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $list = M(MODULE_NAME)->where ( $condition )->delete();
            if ($list!==false) {
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
        $ajax = intval($_REQUEST['ajax']);
        $data = M(MODULE_NAME)->create ();
        //开始验证有效性
        $this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['score']))
        {
            $this->error("请输入积分");
        }
        if(!(intval($data['score'])>0))
        {
            $this->error("积分必须大于0");
        }
        if(!check_empty($data['diamonds']))
        {
            $this->error("请输入秀豆数量");
        }
        if(!(intval($data['diamonds'])>0))
        {
            $this->error("秀豆数量必须大于0");
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
        if(!check_empty($data['score']))
        {
            $this->error("请输入积分");
        }
        if(!(intval($data['score'])>0))
        {
            $this->error("积分必须大于0");
        }
        if(!check_empty($data['diamonds']))
        {
            $this->error("请输入秀豆数量");
        }
        if(!(intval($data['diamonds'])>0))
        {
            $this->error("秀豆数量必须大于0");
        }
        $list=M(MODULE_NAME)->save ($data);
        if (false !== $list) {
            //成功提示
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            $this->error(L("UPDATE_FAILED"));
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