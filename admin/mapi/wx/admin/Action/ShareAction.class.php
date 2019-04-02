<?php

class ShareAction extends CommonAction{
    public function index()
    {
        if (intval($_REQUEST['audit_status']) >= 0 && $_REQUEST['audit_status'] != ''){
            $parameter = "audit_status=" . intval($_REQUEST['audit_status']). "&";
            $sql_w = "s.audit_status=".intval($_REQUEST['audit_status'])." and ";
        }

        $model = D();
        $sql = "select s.*,u.nick_name from ".DB_PREFIX."share s left join ".DB_PREFIX."user u on s.author_id=u.id 
                 where 1=1 and ".$sql_w." 1=1 order by s.id desc";
        $list = $this->_Sql_list($model,$sql,'&'.$parameter);

        //转换时间
        foreach ($list as $k=>&$v){
            $v['create_time'] = to_date($v['create_time'],'Y-m-d H:i:s');
        }
        $this->assign('list',$list);
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

    public function cate_list(){
        $model = M('share_cate');
        $list = $model->select();
        //转换时间
        foreach ($list as $k=>&$v){
            $v['create_time'] = to_date($v['create_time'],'Y-m-d H:i:s');
        }

        $this->assign('list',$list);

        $this->display();
    }

    public function add_cate(){
        $this->display();
    }

    public function insert_cate(){
        $model = M('share_cate');

        $data = array();
        $data['cate_name'] = trim($_REQUEST['cate_name']);
        $data['introduce'] = trim($_REQUEST['introduce']);
        if ($data['cate_name'] == ''){
            $this->error("分类名称不能为空");
        }

        $data['create_time'] = NOW_TIME;
        $res = $model->add($data);

        if ($res === false){
            $this->error("添加话题失败");
        }else{
            $this->success("添加话题成功");
        }
    }

    public function edit_cate(){
        $id = intval($_REQUEST['id']);

        $model = M('share_cate');

        $list = $model->where('id='.$id)->find();
        $this->assign('list',$list);
        $this->display();
    }

    public function update_cate(){
        $model = M('share_cate');

        $id = intval($_REQUEST['id']);
        $data = array();
        $data['cate_name'] = trim($_REQUEST['cate_name']);
        $data['introduce'] = trim($_REQUEST['introduce']);
        if ($data['cate_name'] == ''){
            $this->error("分类名称不能为空");
        }

        $res = $model->where('id='.$id)->save($data);

        if ($res === false){
            $this->error("编辑话题失败");
        }else{
            $this->assign("jumpUrl",u(MODULE_NAME."/cate_list"));

            $this->success("编辑话题成功");
        }
    }

    public function del_cate(){
        $model = M('share_cate');

        $id = intval($_REQUEST['id']);
        $res= $model->where('id='.$id)->delete();

        if ($res === false){
            $this->error("删除话题失败");
        }else{
            $this->success("删除话题成功");
        }
    }
}