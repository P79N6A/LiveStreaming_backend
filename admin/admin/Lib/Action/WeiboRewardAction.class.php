<?php

class WeiboRewardAction extends CommonAction{
    public function index()
    {
//		if(strim($_REQUEST['content'])!=''){
//            $map['content'] = array('like','%'.strim($_REQUEST['content']).'%');
//        }
//        if (method_exists ( $this, '_filter' )) {
//            $this->_filter ( $map );
//        }
//        $name=$this->getActionName();
//        $model = D ($name);
//        if (! empty ( $model )) {
//            $this->_list ( $model, $map );
//        }
//        $this->display ();
		$content = trim($_REQUEST['content']);
    	if($content)
        {
            $sql = " where w.content like concat('%','$content','%') ";
        }
		$now=get_gmtime();
		$sql_str = "select 
		wr.weibo_id as id,
		sum(wr.ticket) as 打赏总秀票
		from ".DB_PREFIX."weibo_reward as wr left join ".DB_PREFIX."weibo as w on w.id = wr.weibo_id  $sql group by wr.weibo_id   ";
		$model = D('');
		$voList = $this->_Sql_list($model, $sql_str, '', false);		
		$this->assign("list",$voList); 
		$this->display();	
    }
    public function foreverdelete() {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $rel_data = M('WeiboReward')->where($condition)->findAll();
            foreach($rel_data as $data)
            {
                $info[] = $data['from_user_id'].'的打赏'.$data['id'];
            }
            if($info) $info = implode(",",$info);
            $list = M('WeiboReward')->where ( $condition )->delete();
            if ($list!==false) {
                save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
                clear_auto_cache("get_help_cache");
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                save_log($info.l("FOREVER_DELETE_FAILED"),0);
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }
    public function detail(){
    	

		$this->assign("weibo_id",intval($_REQUEST['weibo_id'])); 	
		$weibo_id = intval($_REQUEST['weibo_id']);
    	if($weibo_id)
        {
            $sql = " where weibo_id  = $weibo_id ";
        }
		$now=get_gmtime();
		$sql_str = "select * from ".DB_PREFIX."weibo_reward $sql    ";
		$model = D('');
		$voList = $this->_Sql_list($model, $sql_str, '', false);		
		$this->assign("list",$voList); 	
		$this->display ();
    }
}
 ?>   