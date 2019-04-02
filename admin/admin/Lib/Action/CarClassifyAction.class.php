<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class CarClassifyAction extends CommonAction{
    //更新手机版本号
    public function update_class(){
        clear_auto_cache("m_config");
        $sql = $GLOBALS['db']->query( "update ".DB_PREFIX."m_config set val=val+1 where code ='init_version'");
        return $sql;
    }
    
    //一级分类列表
    public function index()
    {
        $condition = $_REQUEST['title']?$_REQUEST['title']:0;
        $where = 'classify_type in(1,2) ';
        if($condition){
            $where = "title like '%$condition%'";
        }
        
        $list = M(MODULE_NAME)->where($where)->select();
        
        foreach ($list as $key => $val){
            if($list[$key]['classify_type'] == 1){
                $list[$key]['classify_type_name'] = '一级分类';
            }elseif($list[$key]['classify_type'] == 2){
                $list[$key]['classify_type_name'] = '二级分类';
            }
            if($list[$key]['is_show']){
                $list[$key]['is_show'] = '是';
            }else{
                $list[$key]['is_show'] = '否';
            }
        }
        //log_ljz($list);
        $this->assign("list", $list);
        $this->display ();
    }
    
    //添加一级或二级分类
    public function insert() {
    
        $data['title'] = $_REQUEST['title']?$_REQUEST['title']:0;
        $data['sort'] = $_REQUEST['sort']?$_REQUEST['sort']:0;
        $data['classify_image'] = $_REQUEST['classify_image']?$_REQUEST['classify_image']:0;
        $data['classify_type'] = $_REQUEST['classify_type']?$_REQUEST['classify_type']:1;//类型
        $data['is_show'] = $_REQUEST['is_show']?$_REQUEST['is_show']:0;//是否显示
        $data['is_effect'] = $_REQUEST['is_effect']?$_REQUEST['is_effect']:1;//是否有效
    
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        
        if($data['title']){
            $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."car_classify where title = '".$data['title']."'");
            if($cate_id){
                $this->error("分类名称已存在");
            }
        }else{
            $this->error("请输入分类名称");
        }
    
        if(!$data['classify_image']){
            $this->error("请选择图标");
        }
        $list=M(MODULE_NAME)->add ($data);
        if (false !== $list) {
            $this->update_class();
            clear_auto_cache("car_classify");
            save_log($log_info.L("UPDATE_SUCCESS"),1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);
            $this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
        }
    }
    
    
    //二级菜单列表
    public function classify2_list(){
        $classify1_id = $_REQUEST['id']?$_REQUEST['id']:0;
        if(classify_id){
            $where = "classify1_id=".$classify1_id;
            $list = M(MODULE_NAME)->where($where)->select();
            foreach ($list as $key => $val){
                if($list[$key]['is_show']){
                    $list[$key]['is_show'] = '是';
                }else{
                    $list[$key]['is_show'] = '否';
                }
            }
        }
        $this->assign('list',$list);
        $res = $this->assign('classify1_id',$classify1_id);
        $this->display();
    }
    //添加二级分类跳转
    public function classify2_add(){
        $classify1_id = $_REQUEST['classify1_id']?$_REQUEST['classify1_id']:0;
        $this->assign('classify1_id',$classify1_id);
        $this->display();
    }
    //新增二级分类
    public function classify2_insert(){
        $data['title'] = $_REQUEST['title']?$_REQUEST['title']:0;
        $data['sort'] = $_REQUEST['sort']?$_REQUEST['sort']:0;
        $data['classify_image'] = $_REQUEST['classify_image']?$_REQUEST['classify_image']:0;
        $data['is_effect'] = $_REQUEST['is_effect']?$_REQUEST['is_effect']:1;
        $data['classify1_id'] = $_REQUEST['classify1_id']?$_REQUEST['classify1_id']:0;
        $data['is_show'] = $_REQUEST['is_show']?$_REQUEST['is_show']:0;//是否显示
        $data['classify_type'] = 3;//类型
        
        if(!$data['classify1_id']){
            $this->error("获取父级id失败");
        }
        
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        
        if($data['title']){
            $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."car_classify where title = '".$data['title']."'");
            if($cate_id){
                $this->error("分类名称已存在");
            }
        }else{
            $this->error("请输入分类名称");
        }
        
        if(!$data['classify_image']){
            $this->error("请选择图标");
        }
        
        $list=M('car_classify')->add($data);
        if($list){
            $this->success(L("UPDATE_SUCCESS"));
        }
    }
    
    //一级分类编辑界面
    public function edit() {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign ( 'vo', $vo );
        $this->display ();
    }
    
    //二级分类编辑界面
    public function classify2_edit(){
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M(MODULE_NAME)->where($condition)->find();
        $this->assign ( 'c1_id', $vo['classify1_id'] );
        $this->assign ( 'vo', $vo );
        $this->display ();
    }
    
    //更新
    public function update() {
        B('FilterString');
        $data = M(MODULE_NAME)->create ();
        $log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("title");
        //区分父级还是子集更新
        $data['classify_type'] = $_REQUEST['c1_id']?3:$data['classify_type'];
        if($data['classify_type'] == 3){
            $this->assign("jumpUrl",u(MODULE_NAME."/classify2_edit",array("id"=>$data['id'])));
        }else{
            $this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
        }
        
        
        //开始验证有效性
        if(!check_empty($data['title']))
        {
            $this->error("请输入分类名称");
        }
        if(mb_strlen($data['title'],'utf-8')>10){
            $this->error("分类名称不能大于10个字符");
        }
        $cate_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."car_classify where title = '".$data['title']."'");
        if($cate_id && $cate_id!=$data['id']){
            $this->error("分类名称已存在，请重新填写！");
        }
        /* if(intval($data['is_effect'])==0){
         $video = M('Video')->where("classified_id=".intval($data['id'])." and live_in<>0")->findAll();
         if($video){
         $this->error("该分类下面有直播,不能设置无效");
         }
         } */
        
    
        $list=M(MODULE_NAME)->save ($data);
        if (false !== $list) {
            $this->update_class();
            clear_auto_cache("car_classify");
            save_log($log_info.L("UPDATE_SUCCESS"),1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info.L("UPDATE_FAILED"),0);
            $this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
        }
    }
    
    //删除
    public function foreverdelete(){
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id']?$_REQUEST ['id']:0;
        if($id){
            $id = explode ( ',', $id );
            log_ljz($id);
            foreach ($id as $key => $val){
                //存在子集分类时不能删除
                $classify2 = M(MODULE_NAME)->where("classify1_id in ($val)")->findAll();
                if(count($classify2)>0){
                    $this->error ("选中的分类中包含子类无法删除",$ajax);
                }
                $user_classify = M('user_classify')->where("classify_id in ($val)")->findAll();
                if(count($user_classify)>0){
                    $this->error ("该分类下存在主播，无法删除",$ajax);
                }
            }
            //$condition = array('id'=>array('in',$id));
            $condition = array ('id' => array ('in', $id  ) );
            $list = M(MODULE_NAME)->where ($condition)->delete();
            
            $this->success (l("FOREVER_DELETE_SUCCESS"));
            if ($list!==false){
                $this->update_class();
                clear_auto_cache("car_classify");
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            }
        }
    }
    
    //是否有效
    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $info = M(MODULE_NAME)->where("id=".$id)->getField("title");
        /* if($info == '课程'){
            $this->error("课程不能设置无效");
        } */
        $c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        /* if($n_is_effect==0){
            $video = M('Video')->where("classified_id=".$id." and live_in<>0")->findAll();
            if($video){
                $this->error("该分类下面有直播,不能设置无效");
            }
        } */
        M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);
        save_log($info.l("SET_EFFECT_".$n_is_effect),1);
        $this->update_class();
        clear_auto_cache("car_classify");
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;
    }

    //排序
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
        $this->update_class();
        clear_auto_cache("car_classify");
        $this->success(l("SORT_SUCCESS"),1);
    }

}
?>