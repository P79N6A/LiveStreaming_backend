<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class VideoClassifiedUrlAction extends CommonAction{
    public function update_class(){
        clear_auto_cache("m_config");
        $sql = $GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");
        return $sql;
    }
    //首页
    public function index()
    {
        if(strim($_REQUEST['title'])!=''){
            $map['title'] = array('like','%'.strim($_REQUEST['title']).'%');
        }
        $map['type'] = 1;//Url分类
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=$this->getActionName();
        $name = substr($name,0,-3);
        
        $model = D ($name);
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
    }
    //添加页面
    public function add()
    {
        $this->assign("new_sort", M("VideoClassified")->max("sort")+1);
        $this->display();
    }
    //更新页面
    public function edit() {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M("VideoClassified")->where($condition)->find();
        $this->assign ( 'vo', $vo );
        $this->display ();
    }
    //更新
    public function update() {
        B('FilterString');
        if(MODULE_NAME == 'VideoClassifiedUrl'){//兼容
            $name = 'VideoClassified';
        }else{
            $name = MODULE_NAME;
        }
        $data = M($name)->create ();
    
        $log_info = M($name)->where("id=".intval($data['id']))->getField("title");
        //开始验证有效性
        $this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
        if(!check_empty($data['title']))
        {
            $this->error("请输入分类名称");
        }
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        $url = $_REQUEST['classified_url']?$_REQUEST['classified_url']:0;
        if(!$url){
            $this->error("Url链接不能为空");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."video_classified where title = '".$data['title']."'");
        if($cate_id && $cate_id!=$data['id']){
            $this->error("分类名称已存在，请重新填写！");
        }
        if(intval($data['is_effect'])==0){
            $video = M('Video')->where("classified_id=".intval($data['id'])." and live_in<>0")->findAll();
            if($video){
                $this->error("该分类下面有直播,不能设置无效");
            }
        }
    
        
        if($url){
            $GLOBALS['db']->query("update ".DB_PREFIX."Video_Classified set classified_url='$url' where id=".$_REQUEST['id']);
        }
    
        $list=M($name)->save ($data);
        if (false !== $list) {
            $this->update_class();
            clear_auto_cache("video_classified");
            save_log($log_info.L("UPDATE_SUCCESS"),1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);
            $this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
        }
    }
    //添加
    public function insert() {
        B('FilterString');
        if(MODULE_NAME == 'VideoClassifiedUrl'){//兼容
            $name = 'VideoClassified';
        }else{
            $name = MODULE_NAME;
        }
        $data = M($name)->create();
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
        $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."video_classified where title = '".$data['title']."'");
        if($cate_id){
            $this->error("分类名称已存在");
        }
        $url = $_REQUEST['classified_url']?$_REQUEST['classified_url']:0;
        if(!$url){
            $this->error("Url链接不能为空");
        }
        // 更新数据
        $log_info = $data['title'];
        $list=M($name)->add($data);
        
        $m_config =  load_auto_cache("m_config");//初始化手机端配置
        $init_version = intval($m_config['init_version']);//手机端配置版本号
        $info['init_version']=$init_version+1;
        
        
        if($url){
            $GLOBALS['db']->query("update ".DB_PREFIX."Video_Classified set classified_url='$url',type=1 where title='".$data['title']."'");
        }else{
            $GLOBALS['db']->query("update ".DB_PREFIX."Video_Classified set type=1 where title='".$data['title']."'");
        }
    
        if (false !== $list) {
            //成功提示
            $this->update_class();
            clear_auto_cache("video_classified");
            save_log($log_info.L("INSERT_SUCCESS"),1);
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("INSERT_FAILED"),0);
            $this->error(L("INSERT_FAILED"));
        }
    }
    //有效无效切换
    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $info = M('VideoClassified')->where("id=".$id)->getField("title");
        if($info == '课程'){
            $this->error("课程不能设置无效");
        }
        $c_is_effect = M('VideoClassified')->where("id=".$id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        if($n_is_effect==0){
            $video = M('Video')->where("classified_id=".$id." and live_in<>0")->findAll();
            if($video){
                $this->error("该分类下面有直播,不能设置无效");
            }
        }
        M('VideoClassified')->where("id=".$id)->setField("is_effect",$n_is_effect);
        save_log($info.l("SET_EFFECT_".$n_is_effect),1);
        $this->update_class();
        clear_auto_cache("video_classified");
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
    }
    //删除
    public function foreverdelete() {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $id = explode ( ',', $id );
            if(in_array(1,$id)){
                $this->error ("分类为课程的不能删除，请重新选择",$ajax);
            }
            //分类下有直播时不删除
            $id_count = count($id);
            $unset_num = '编号';
            foreach($id as $ki=>$vi){
                $video = M('Video')->where("classified_id=".$vi." and live_in<>0")->findAll();
                if($video){
                    $unset_num .= ($vi.',');
                    unset($id[$ki]);
                }
            }
            if(count($id)<$id_count){
                $id = array_values($id);
                $unset_num = substr($unset_num,0,-1);
                $unset_num .= '分类下有直播不能删除';
            }else {
                $unset_num = '';
            }
            $condition = array ('id' => array ('in', $id  ) );
            $rel_data = M('VideoClassified')->where($condition)->findAll();
            foreach($rel_data as $data)
            {
                $info[] = $data['title'];
            }
            if($info) $info = implode(",",$info);
            $list = M('VideoClassified')->where ( $condition )->delete();
            if ($list!==false) {
                $this->update_class();
                clear_auto_cache("video_classified");
                save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
                if($unset_num==''){
                    $this->success (l("FOREVER_DELETE_SUCCESS"));
                }else{
                    $this->success ($unset_num);
                }
            } else {
                save_log($info.l("FOREVER_DELETE_FAILED"),0);
                if($unset_num == ''){
                    $this->error (l("FOREVER_DELETE_FAILED"));
                }else{
                    $this->error ($unset_num);
                }
    
            }
        } else {
            $this->error (l("INVALID_OPERATION"));
        }
    }
    

}
?>