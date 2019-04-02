<?php

class FeedbackAction extends CommonAction{
    public function index()
    {
        $model = M('feedback');

        $sql = "select id,user_id,content,FROM_UNIXTIME(create_time,'%m-%d') as create_time FROM ".DB_PREFIX."feedback order by id desc";
        $list = $model->query($sql);

        $this->assign('list',$list);
        $this->display();
    }

    public function foreverdelete(){
        $model = M('feedback');

        $id = intval($_REQUEST['id']);
        if ($id == 0){
            $this->error("id不能为空");
        }else{
            $model->where('id='.$id)->delete();
            $this->success("删除成功");
        }
    }

}