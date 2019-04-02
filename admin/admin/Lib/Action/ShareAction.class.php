<?php

class ShareAction extends CommonAction{
    public function index()
    {
        $model = M('Share s');

        $list = $model->join("LEFT JOIN fanwe_user u ON s.author_id=u.id")->field("s.*,u.nick_name")->order('id asc')->select();
        //转换时间
        foreach ($list as $k=>&$v){
            $v['create_time'] = date('Y-m-d',$v['create_time']);
        }
        $this->assign('list',$list);
//        var_dump($list);
        $this->display();
    }

    public function detail(){
        if (!isset($_REQUEST['id']) || $_REQUEST['id']==''){
            $this->error (l("INVALID_OPERATION"));
        }
        $condition['id'] = intval($_REQUEST['id']);
        $model = M(MODULE_NAME);
        $data = $model->where($condition)->find();

        $this->assign('data',$data);
        $this->display();
    }

    public function foreverdelete(){
        if (!isset($_REQUEST['id']) || $_REQUEST['id']==''){
            $this->error (l("INVALID_OPERATION"));
        }
        $id = intval($_REQUEST['id']);
        $condition = array('id' => array("in",explode(',',$id)));
        $rel_data = M(MODULE_NAME)->where($condition)->findAll();
        foreach($rel_data as $data)
        {
            $info[] = $data['id'];
        }
        if($info) $info = implode(",",$info);
        $list = M(MODULE_NAME)->where ( $condition )->delete();

        if ($list != false){
            $map = array();
            $map['share_id'] = $id;
            $list_reply = M('Share_reply')->where($map)->delete();

            $this->success (l("FOREVER_DELETE_SUCCESS"),1);
        }else{
            $this->error (l("FOREVER_DELETE_FAILED"),0);
        }
    }

    //审核分享
    public function audit(){
        if (!isset($_REQUEST['id'])){
            $this->error (l("INVALID_OPERATION"));
        }
        $id = intval($_REQUEST['id']);

        $data = array();
        $data['audit_status'] = intval($_REQUEST['audit_status']);
        $data['audit_log'] = strim($_REQUEST['audit_log']);
        $res = M(MODULE_NAME)->where("id=".$id)->save($data);
        if ($res === false){
            $this->error("审核失败");
        }else{
            $this->success("审核成功");
        }

    }

}