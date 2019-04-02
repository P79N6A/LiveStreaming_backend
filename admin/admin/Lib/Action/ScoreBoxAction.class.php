<?php

class ScoreBoxAction extends CommonAction{
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
		if(!check_empty($data['chance']))
        {
            $this->error("请输入中奖概率");
        }
        if(!(intval($data['chance'])>0))
        {
            $this->error("中奖概率必须大于0");
        }
        $chance = 0;
        $score_box = M('ScoreBox')->select();
        foreach($score_box as $k=>$v)
        {
        	$chance = $chance + $v['chance'];
            
        }
        $chance = $chance + $data['chance'];
        if($chance> 100)
        {
        	$this->error("总中奖概率不能大于100%");
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
        if(!check_empty($data['chance']))
        {
            $this->error("请输入中奖概率");
        }
        if(!(intval($data['chance'])>0))
        {
            $this->error("中奖概率必须大于0");
        }
        $score_box = M('ScoreBox')->where("id !=".$data['id'])->select();
        foreach($score_box as $k=>$v)
        {
        	$chance = $chance + $v['chance'];
            
        }
        $chance = $chance + $data['chance'];
        if($chance> 100)
        {
        	$this->error("总中奖概率不能大于100%");
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

}
?>