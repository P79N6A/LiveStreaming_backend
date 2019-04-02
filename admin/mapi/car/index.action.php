<?php

class indexCModule extends baseModule
{
    /**
     *  首页
     */
    public function index()
    {
        $root = array();
        $sex = intval($_REQUEST['sex']);//性别 0:全部, 1-男，2-女
        $cate_id = intval($_REQUEST['cate_id']);//话题id
        $city = strim($_REQUEST['city']);//城市(空为:热门)
        if($city=='热门' || $city=='null'){
            $city = '';
        }

        if ($cate_id ==0){
            //首页 轮播
            $root['banner'] = load_auto_cache("banner_list");
            if($root['banner']==false){
                $root['banner'] = array();
            }
        }else{
            //主题相关内容
            $cate = load_auto_cache("cate_id",array('id'=>$cate_id));
            if ($cate['url'] != '' && $cate['image'] != ''){
                $root['banner'] = $cate['banner'];
                $root['cate'] = $cate;
            }
        }

        $root['sex'] = $sex;//
        $root['cate_id'] = $cate_id;//
        $root['city'] = $city;//

        $m_config =  load_auto_cache("m_config");//初始化手机端配置
        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
        $dev_type = strim($_REQUEST['sdk_type']);
        if($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name){
            $list = $this->check_video_list("select_video_check",array('sex_type'=>$sex,'area_type'=>$city,'cate_id'=>$cate_id));
        }else{
            $list = load_auto_cache("select_heat_video",array('sex_type'=>$sex,'area_type'=>$city,'cate_id'=>$cate_id));
        }

        $root['list'] = $list;
        $root['status'] = 1;
        $root['has_next'] = 0;
        $root['page'] = 1;//

        $root['init_version'] = intval($m_config['init_version']);//手机端配置版本号
        ajax_return($root);
    }

    /**
     * 首页热度排行 600s 更新
     */
    public function rank_heat(){
        fanwe_require(APP_ROOT_PATH.'mapi/car/core/common_car.php');
        $root = array();
        $rank = get_heat_rank_cache();
        if(intval($rank['is_first'])&&$rank['list']){
            //开启全服推送
            $notify_data = array();
            $notify_data['user_id'] =$rank['list'][0]['user_id'];
            $notify_data['user_name'] =$rank['list'][0]['nick_name'];
            $notify_data['room_id'] = $rank['list'][0]['video_id'];
            $notify_data['head_image']= $rank['list'][0]['head_image'];
            $a = HeatNotify($notify_data);
        }
		 if($_REQUEST['is_debug']){
		    ajax_return(array($rank['list'],$a));
		 }
         ajax_return($rank['list']);
    }

    /**
     *  热度实时排行 60s 更新
     */
    function rank_now(){
        $root = array('error'=>'','status'=>1);   //定义接口
        fanwe_require(APP_ROOT_PATH.'mapi/car/core/common_car.php');
        $podcast_id = intval($_REQUEST['podcast_id']);//主播id，fanwe_user.id
        $root = get_user_now_rank($podcast_id);
        ajax_return($root);
    }
    
    /** 
     *  首页分类更多列表
     */
    public function c_more(){
        $date['status'] = 1;
        $date['error'] = '';
        $date['data'] = load_auto_cache('car_classify_multi');
        api_ajax_return($date);
    }

    /**
     * 热度总榜
     */
    public function rank_heat_all(){
        //分页
        $page = intval($_REQUEST['p']);//当前页
        $page_size = 30;//分页数量
        if ($page == 0) {
            $page = 1;
        }
        fanwe_require(APP_ROOT_PATH.'mapi/car/core/common_car.php');
        $param = array('page'=>$page,'page_size'=>$page_size);
        $rank = get_heat_all_rank($param);
        if($rank){
            $rank['status'] = 1;
            $rank['error'] = '';
        }else{
            $rank['status'] = 0;
            $rank['error'] = '暂无数据';
        }
        ajax_return($rank);

    }
}