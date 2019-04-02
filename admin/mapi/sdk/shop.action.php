<?php
// +----------------------------------------------------------------------
// | Fanwe 千秀p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class shopCModule extends baseCModule
{

	public function init()
	{
		$video_user_id = $_POST['video_user_id'];
		$session_id    = $_POST['session_id'];
		$user_id       = intval(isset($GLOBALS['user_info']['id']) ? $GLOBALS['user_info']['id'] : 0);
		if ($video_user_id == $user_id && $session_id == es_session::id()) {
			api_ajax_return(array('status' => 1, 'video_user_id' => $video_user_id, 'session_id' => $session_id));
		}
	}

    /**
     * 我的小店接口
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=552
     * @return [type] [description]
     */
    public function mystore()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $podcast_id = intval($_REQUEST['podcast_user_id']);
        
        $page_size = 20;
        $page = intval($_REQUEST['page']);
        $page = $page > 0 ? $page : 1;

        if(O2O_OPEN_GOODS == 1){
            $m_config =  load_auto_cache("m_config");
            $user_info = $GLOBALS['db']->getRow("SELECT is_authentication,is_shop,shop_user_id,store_url FROM ".DB_PREFIX."user WHERE id=".$podcast_id);
            if($m_config['must_authentication']==0 || ($m_config['must_authentication']==1&&$user_info['is_authentication'] == 2)){
                $head_args['page']=$page;
                $head_args['user_id']=$user_info['shop_user_id'];
                $head_args['goods_id']='';
                $head_args['ctl']='mystore';
                $head_args['act']='shop';
                $res = third_o2o_mall('http://o2onewlive.fanwe.net/saas_api_server.php',$head_args);
                if($res['list']){
                    foreach($res['list'] as $k => $v){
                        $res['list'][$k]['price'] = number_format($v['price'],2,'.','');
                    }
                }else{
                    $res['list']=array();
                }

                $goods = $res['list'];
                $page = $res['page'];
                $podcast['store_url'] = $res['store_url'];
                $status = 1;
            }else{
                $status = 0;
                $goods = array();
                $page = array('page' => 1,'has_next' => 0);
            }
        }else {

            $table      = DB_PREFIX . 'user';
            $sql        = "SELECT id,store_url FROM $table WHERE id = $podcast_id";
            $podcast    = $GLOBALS['db']->getRow($sql);
            if (!$podcast) {
                self::returnErr(10009);
            }
            $table = DB_PREFIX . 'goods';
            $field = 'id,name,imgs,price,url,description,kd_cost';
            $sql   = "SELECT $field FROM $table WHERE user_id = $podcast_id AND is_delete = 0";
            $goods = $GLOBALS['db']->getAll($sql);

            foreach($goods as $k=>$v){
                if($goods[$k]['imgs']!=''){
                    $goods[$k]['imgs'] = json_decode($v['imgs']);
                    if($goods[$k]['imgs']==""){
                        $goods[$k]['imgs'] = array();
                    }else{
                        foreach($goods[$k]['imgs'] as $k1=>$v1){
                            //$goods[$k]['imgs'][$k1]=get_domain().APP_ROOT.$v1;
                            $goods[$k]['imgs'][$k1]=get_spec_image($v1);
                        }
                    }
                }
                else{
                    $goods[$k]['imgs'] = array();
                }
            }
            $status = 1;
        }
        if ($goods === false) {
            self::returnErr(10001);
        }else{

            api_ajax_return(array(
                'status' => $status,
                'error'  => '',
                'list'   => $goods,
                'url'    => $podcast['store_url'],
                'page'   => $page,//array('page' => $page, 'has_next' => intval($has_next)),
            ));
        }
    }

    /**
     * 添加商品
     * int is_delete  0正常 1锁定
     */
    public function add_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods = array();
        $goods['user_id'] = $user_id; //主播ID

        $goods['name'] = strim($_REQUEST['name']); //商品名称
        $imgs = json_decode($_REQUEST['imgs']);
        $result_imgs = array();
        foreach ($imgs as $k => $v) {
        	$result_imgs[] = str_replace(get_domain().APP_ROOT,'',$v);
            //$result_imgs[] = $v;
        }
        $goods['imgs']        = json_encode($result_imgs); //商品图片JSON数据
        $goods['price']       = strim($_REQUEST['price']); //商品价钱
        $goods['url']         = htmlspecialchars_decode(strim($_REQUEST['url'])); //商品详情URL地址
        $goods['description'] = strim($_REQUEST['description']); //商品描述
        $goods['kd_cost']     = strim($_REQUEST['kd_cost']); //快递费用

        if ($goods['name'] == '') {
        	$root['status'] = 10038;
        	$root['error']  = "名称不能为空";
        	api_ajax_return($root);
        } elseif ($goods['imgs'] == '') {
        	$root['status'] = 10059;
        	$root['error']  = "商品图片不能为空";
        	api_ajax_return($root);
        } elseif ($goods['price'] == '') {
        	$root['status'] = 10061;
        	$root['error']  = "商品价格不能为0";
        	api_ajax_return($root);
        } elseif ($goods['url'] == '') {
        	$root['status'] = 10060;
        	$root['error']  = "商品详情不能为空";
        	api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "add_goods", $goods);

        $root['status'] = intval($rs['status']);
        if ($root['status']==1) {
            $root['error']  = "添加商品成功";

            $goods= $rs['info'];

            if($goods['imgs']!=''){
            		$goods['imgs'] = json_decode($goods['imgs']);
            		if($goods['imgs']==""){
            			$goods['imgs'] = array();
            		}else{
            			foreach($goods['imgs'] as $k=>$v){
            				//$goods['imgs'][$k]=get_domain().APP_ROOT.$v;
            				$goods['imgs'][$k]=get_spec_image($v);
            			}
            		}
            	}
            	else{
            		$goods['imgs'] = array();
            	}

            $root['info']  =$goods;

        } elseif($root['status'] == 10057){
            $root['error']  = "添加商品失败";
        }
        api_ajax_return($root);
    }

    /**
     * 编辑商品
     */
    public function edit_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods = array();
        $goods['id']   = intval($_REQUEST['id']); //商品ID
        $goods['user_id'] = $user_id;//主播ID
        $goods['name'] = strim($_REQUEST['name']); //商品名称
        $imgs = json_decode($_REQUEST['imgs']);
        $result_imgs = array();
        foreach ($imgs as $k => $v) {
        	$result_imgs[] = str_replace(get_domain().APP_ROOT,'',$v);
            //$result_imgs[] = $v;
        }
        $goods['imgs']        = json_encode($result_imgs); //商品图片JSON数据
        $goods['price']       = strim($_REQUEST['price']); //商品价钱
        $goods['url']         =  htmlspecialchars_decode(strim($_REQUEST['url'])); //商品详情URL地址
        $goods['description'] = strim($_REQUEST['description']); //商品描述
        $goods['kd_cost']     = strim($_REQUEST['kd_cost']); //快递费用

        if ($goods['name'] == '') {
            $root['status'] = 10038;
            $root['error']  = "名称不能为空";
            api_ajax_return($root);
        } elseif ($goods['imgs'] == '') {
            $root['status'] = 10059;
            $root['error']  = "商品图片不能为空";
            api_ajax_return($root);
        } elseif ($goods['price'] == '') {
            $root['status'] = 10061;
            $root['error']  = "商品价格不能为0";
            api_ajax_return($root);
        } elseif ($goods['url'] == '') {
            $root['status'] = 10060;
            $root['error']  = "商品详情不能为空";
            api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "edit_goods", $goods);

        $root['status'] = intval($rs['status']);
        if ($root['status']==1) {
            $root['error']  = "修改商品成功";
            $goods= $rs['info'];

            if($goods['imgs']!=''){
                    $goods['imgs'] = json_decode($goods['imgs']);
                    if($goods['imgs']==""){
                        $goods['imgs'] = array();
                    }else{
                        foreach($goods['imgs'] as $k=>$v){
                            //$goods['imgs'][$k]=get_domain().APP_ROOT.$v;
                        	$goods['imgs'][$k] = get_spec_image($v);
                        }
                    }
                }
                else{
                    $goods['imgs'] = array();
                }

            $root['info']  =$goods;
        } else if ($root['status']==10008) {
            $root['error']  = "商品不存在";
        } else if ($root['status']==10057) {
            $root['error']  = "添加商品失败";
        }
        api_ajax_return($root);
    }

    /**
     * 删除商品
     */
    public function del_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $goods = array();
        $goods['user_id'] = $user_id;//主播ID
        $goods['id'] = intval($_REQUEST['id']); //商品ID

        $rs = FanweServiceCall("shop", "del_goods", $goods);

        $root['status'] = intval($rs['status']);
        if(intval($root['status']) == 1) {
            $root['error'] = "删除成功";

        }elseif($root['status'] == 10008){
            $root['error']  = "商品不存在";

        }else{
            $root['status'] = 10058;
            $root['error']  = "删除商品失败";
        }

        api_ajax_return($root);
    }

    /**
     * 商品推送
     */
    public function push_goods()
    {

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error'] = "请先登录";
            api_ajax_return($root);
        }

        $goods_id = intval($_REQUEST['goods_id']);

        $user_info = $GLOBALS['db']->getRow("SELECT is_shop,shop_user_id,store_url,is_nospeaking FROM " . DB_PREFIX . "user WHERE id=" . $user_id);
        if ($user_info['is_nospeaking'] == 0) {
            if (O2O_OPEN_GOODS == 1) {
                $head_args['user_id'] = $user_info['shop_user_id'];
                $head_args['goods_id'] = $goods_id;
                $head_args['ctl'] = 'mystore';
                $head_args['act'] = 'shop';
                $info = third_o2o_mall('http://o2onewlive.fanwe.net/saas_api_server.php', $head_args);
                if ($info['list']) {
                    $info['list']['goods_id'] = $info['list']['id'];
                    unset($info['list']['id']);
                    $goods_info = $info['list'];
                }
            } else {
                $goods_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "goods where id=" . $goods_id . " and is_delete=0");
                if ($goods_info['imgs'] != '') {
                    $goods_info['imgs'] = json_decode($goods_info['imgs']);
                    if ($goods_info['imgs'] == "") {
                        $goods_info['imgs'] = array();
                    } else {
                        foreach ($goods_info['imgs'] as $k => $v) {
                            $goods_info['imgs'][$k] = get_spec_image($v);
                        }
                    }
                } else {
                    $goods_info['imgs'] = array();
                }

                if ($goods_info[imgs][0]) {
                    $goods_info[imgs] = $goods_info[imgs][0];
                }
            }

            if (!$goods_info) {
                $root['status'] = 10008;
                $root['error'] = "商品不存在";
                api_ajax_return($root);
            }

            $video_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "video where user_id=" . $user_id . " and live_in =1");
            //tim推送
            $ext = array();
            $ext['type'] = 31;
            $ext['room_id'] = intval($video_info['id']);
            $ext['post_id'] = $user_id;
            $ext['desc'] = "主播推送了商品“" . $goods_info['name'] . "”";

            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserFollwRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $fields = array('head_image', 'user_level', 'v_type', 'v_icon', 'nick_name');
            $ext['user'] = $user_redis->getRow_db($user_id, $fields);
            $ext['user']['user_id'] = $user_id;
            $ext['user']['head_image'] = get_spec_image($ext['user']['head_image']);

            $ext['goods'] = $goods_info;

            #构造高级接口所需参数
            $tim_data = array();
            $tim_data['ext'] = $ext;
            $tim_data['podcast_id'] = strim($user_id);
            $tim_data['group_id'] = strim($video_info['group_id']);
            get_tim_api($tim_data);

            $root['status'] = 1;
            $root['error'] = "推送成功";
        } else {
            $root['status'] = 0;
            $root['error'] = "您已被永久禁言";
        }
        api_ajax_return($root);
    }


    /**
     * 编辑小店URL
     */
    public function edit_store_url(){

        $user_id = intval($GLOBALS['user_info']['id']);
        if ($user_id == 0) {
            $root['status'] = 10007;
            $root['error']  = "请先登录";
            api_ajax_return($root);
        }

        $user = array();
        $user['id'] = $user_id; //主播ID
        $user['store_url'] =  htmlspecialchars_decode(strim($_REQUEST['store_url'])); //商品详情URL地址

        if ($user['store_url'] == '') {
            $root['status'] = 10060;
            $root['error']  = "商品详情不能为空";
            api_ajax_return($root);
        }

        $rs = FanweServiceCall("shop", "edit_store_url", $user);

        $root['status'] = intval($rs['status']);
        if($root['status'] == 1){
            $root['error']  = "修改成功";
        }elseif($root['status'] == 10009){
            $root['status'] = 10009;
            $root['error']  = "主播不存在";
        }
        api_ajax_return($root);
    }

    /**
     * 呼出直播小屏
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=728
     * @return [type] [description]
     */
    public function getvideo()
    {
    	
        self::checkLogin();
        $is_small_screen = $_REQUEST['is_small_screen'];
        $podcast_id      = $_REQUEST['podcast_id'];
        $session_id      = $_REQUEST['session_id'];

        $table   = DB_PREFIX . 'user';
        $where   = "shop_user_id='" . strim($podcast_id)."'";
        $user = $GLOBALS['db']->getRow("SELECT *,id as user_id FROM $table WHERE $where");
        
        $user_id = $user ? $user['user_id'] : api_ajax_return(array('status' => 0, 'error' => '主播不存在'));

        $field = 'id,group_id,user_id,head_image';
        $table = DB_PREFIX . 'video';
        $where = 'user_id=' . intval($user_id).' and live_in=1 ';
        $video = $GLOBALS['db']->getRow("SELECT $field FROM $table WHERE $where");
        $video = $video ? $video : api_ajax_return(array('status' => 0, 'error' => '直播不存在'));
        
        $user_info=$GLOBALS['user_info'];
        $user_info['user_id']=$user_info['id'];
        $user_info['ticket']=intval($user_info['ticket']);
        $user_info['refund_ticket']=intval($user_info['refund_ticket']);
        api_ajax_return(array(
            'status'               => 1,
            'error'                =>'',
            'session_id'           => $session_id,
            'is_small_screen'      => $is_small_screen,
            'roomId'               => $video['id'],
            'groupId'              => $video['group_id'],
            'createrId'            => $video['user_id'],
            'loadingVideoImageUrl' => get_spec_image($video['head_image']),
            'user' => $user_info,
        ));
    }
    /**
     * 呼出直播应用
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=729
     * @return [type] [description]
     */
    public function getapp()
    {
//    	log_result("sess");
//    	log_result($_REQUEST);
//    	log_result("sess_id=".$_REQUEST['session_id']);
        self::checkLogin();
        $session_id = $_REQUEST['session_id'];
        $field = 'id as user_id,nick_name,signature,create_time,update_time,is_authentication,login_type,is_effect,money,login_ip,province,city,is_edit_sex,sex,birthday,is_remind,focus_count,intro,code,sina_id,sina_token,sina_secret,sina_url,tencent_id,tencent_token,tencent_secret,tencent_url,verify,user_level,mobile,user_type,is_has_send_success,verify_setting_time,authentication_type,authentication_name,contact,from_platform,wiki,identify_hold_image,identify_positive_image,identify_nagative_image,wx_openid,gz_openid,qq_openid,investor_send_info,paypassword,source_url,pid,score,point,emotional_state,job,head_image,thumb_head_image,qq_id,qq_token,v_type,v_explain,v_icon,fans_count,ticket,refund_ticket,diamonds,use_diamonds,usersig,expiry_after,is_online,wx_unionid,user_name,synchronize,login_time,logout_time,is_agree,online_time,subscribe,is_robot,apns_code,device_type,video_count,is_ban,ban_time,identify_number,authent_list_id,is_shop,shop_user_id,store_url';
        $table = DB_PREFIX . 'user';
        $where = 'id=' . intval(isset($GLOBALS['user_info']['id']) ? $GLOBALS['user_info']['id'] : 0);
        $user  = $GLOBALS['db']->getRow("SELECT $field FROM $table WHERE $where");
        if (empty($user)) {
            api_ajax_return(array('status' => 0, 'info' => '直播不存在'));
        }
        $user['ticket']=intval($user['ticket']);
        $user['refund_ticket']=intval($user['refund_ticket']);
        ajax_return(array(
            'status'     => 1,
            'session_id' => $session_id,
            'user'       => $user,
        ));
    }
    /**
     * 呼出发起直播
     * http://doc.fanwe.net/index.php?s=/fanwelive&page_id=730
     * @return [type] [description]
     */
    public function openvideo()
    {
        self::checkLogin();
        $session_id      = $_REQUEST['session_id'];
        //$GLOBALS['user_info']=es_session::get('user_info');
        $user_id         = intval(isset($GLOBALS['user_info']['id']) ? $GLOBALS['user_info']['id'] : 0);
        //log_result(es_session::get('user_info'));
        $table   = DB_PREFIX . 'user';
        $where   = "id=" . $user_id;
        $user = $GLOBALS['db']->getRow("SELECT *,id as user_id FROM $table WHERE $where");
        $user['ticket']=intval($user['ticket']);
        $user['refund_ticket']=intval($user['refund_ticket']);
        if ($session_id == es_session::id()) {
            api_ajax_return(array('status' => 1, 'user'=> $user, 'session_id' => $session_id));
        } else {
            api_ajax_return(array('status' => 0, 'user' => array(), 'session_id' => $session_id));
        }
    }



}
