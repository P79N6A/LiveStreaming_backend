<?php

class user_centerCModule  extends baseModule
{
    //个人中心
    public function index(){
        $root = array('status'=>1,'error'=>'','user'=>array());
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);

            $user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id,true,true);
            if($user){
            	/*
            	//同步微店，生成数据
            	fanwe_require(APP_ROOT_PATH.'Fanwewd.php');
            	$fanwewd=new Fanwewd();
            	$fanwewd->getOrderCount(intval($user['shop_user_id']));
            	
            	$user['user_order_url'] = $fanwewd->getUserLoginUrl(intval($user['shop_user_id']),$fanwewd->getOrderUrl());//我的订单url
            	$user['user_address_url'] = $fanwewd->getUserLoginUrl(intval($user['shop_user_id']),$fanwewd->getUserAddressUrl());//我的收货地址url
            	*/
                $user['user_order_wait_pay'] = 0;//待支付订单数
                $user['user_order_wait_send'] = 0;//待发货订单数
                $user['user_order_wait_final'] = 0;//待收货订单数
                $user['user_order_final'] = 0;//已完成订单数
                $user['head_image'] = get_spec_image($user['head_image']);
                
                //会员vip信息
                if (intval($user['vip'])>0&&strtotime($user['vip_date'])>=NOW_TIME) {
                	$vip_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip where is_effect=1 and vip_lv = ".intval($user['vip']),true,true);
                	$root['vip_info'] = $vip_info;
                }
                
                
                $root['user'] = $user;
            }else{
                $root['status'] = 0;
                $root['error'] = '会员信息不存在';
            }
        }
        $root['page_title'] = '个人中心';
        api_ajax_return($root);
    }

    //个人信息
    public function user_center(){
        $root = array('status'=>1,'error'=>'','data'=>array());

        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);
            $user = $GLOBALS['db']->getRow("select u.id,u.head_image,u.nick_name,u.real_name,u.company,u.job,u.job_age,u.province,u.city,u.is_authentication from ".DB_PREFIX."user u where id = ".$user_id,true,true);
            if($user){
                $user['head_image'] = get_spec_image($user['head_image']);
                $user['real_name'] = msubstr($user['real_name']);
                $root['data'] = $user;
                $age_list=array('1年以下', '1-3年', '3-5年', '5-10年', '10年以上');
                $root['age_list'] = $age_list;
            }else{
                $root['status'] = 0;
                $root['error'] = '会员信息不存在';
            }
        }
        $root['page_title'] = '个人信息';
        api_ajax_return($root);
    }

    //认证初始化
    public function authent(){
        $root = array('status'=>1,'error'=>'','data'=>array());
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);

            $user_sql = "select id as user_id,company,job,job_age,business_card,work_card,work_contract,is_authentication,investor_send_info from ".DB_PREFIX."user where is_effect =1 and id=".$user_id;
            $user = $GLOBALS['db']->getRow($user_sql,true,true);
            $user['business_card'] = get_spec_image($user['business_card']);
            $user['work_card'] = get_spec_image($user['work_card']);
            $user['work_contract'] = get_spec_image($user['work_contract']);
            $root['data'] = $user;
        }
        $root['page_title'] = '身份认证';
        api_ajax_return($root);
    }
    //提交审核
    public function attestation(){
        $root = array();
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $root['status'] = 1;
            $root['error'] = "";
            fanwe_require(APP_ROOT_PATH.'system/libs/user_wpk.php');
            $business_card = strim($_REQUEST['business_card']);//名片
            $work_card = strim($_REQUEST['work_card']) ;//工作牌
            $work_contract = strim($_REQUEST['work_contract']) ;//工作合同

            //=============================
            if($business_card==''){
                $root['status'] = 0;
                $root['error'] = '请上传名片照片！';
                ajax_return($root);
            }
            if($work_card==''){
                $root['status'] = 0;
                $root['error'] = '请上传工作牌照片！';
                ajax_return($root);
            }
            if($work_contract==''){
                $root['status'] = 0;
                $root['error'] = '请上传工作合同正面！';
                ajax_return($root);
            }


            //判断该实名是否存在
            $user_info=$GLOBALS['db']->getRow("select id from  ".DB_PREFIX."user where id=".$GLOBALS['user_info']['id']);
            if($user_info){
                $user_info['is_authentication'] = 1;//认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
                $user_info['business_card']=get_spec_image($business_card);//名片
                $user_info['work_card']=get_spec_image($work_card);//工作牌
                $user_info['work_contract']=get_spec_image($work_contract);//工作合同

                $res = save_user_wpk($user_info,"UPDATE");
                if($res['status']==1){
                    //更新session
                    $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$res['data']);
                    es_session::set("user_info", $user_info);

                    $root['status'] = 1;
                    $root['error'] = '已提交,等待审核';
                }else{
                    $root['status'] = 0;
                    $root['error'] = $res['error'];
                }
            }else{
                $root['status'] = 0;
                $root['error'] = '会员信息不存在';
            }
        }
        api_ajax_return($root);
    }

    //我的提问列表
    public function question_list()
    {
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);
            //首页轮播
            $root['banner'] = load_auto_cache("banner_list");

            //取用户提问的问题
            $sql = "select id,create_time,content,count,praise_count,is_answered from 
                " . DB_PREFIX . "question where is_question=1 and pid=0 and question_user_id=".$user_id." order by create_time desc";
            $question_list = $GLOBALS['db']->getAll($sql);

            foreach ($question_list as $k=>&$v){
                $time = NOW_TIME;
                $sub = $time - $v['create_time'];
                if ($sub < 3600){
                    $v['create_time'] = floor($sub / 60)."分钟前";
                }elseif ($sub < 86400){
                    $v['create_time'] = floor($sub /3600)."小时前";
                }elseif ($sub < 604800){
                    $v['create_time'] = floor($sub /86400)."天前";
                }else{
                    $v['create_time'] = date('Y-m-d',$time);
                }
            }

            $root['question_list'] = $question_list;
            $root['type'] = "mine";
            $root['status'] = 1;
            ajax_return($root);
        }
    }

    public function share(){
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        } else {
            $user_id = intval($GLOBALS['user_info']['id']);

            //获取分享列表以及作者相关信息
            $sql = "select s.id,s.title,s.content,s.create_time,s.cate_id,s.watch_count,s.praise_count,s.reply_count,s.imgs,s.audit_status
                ,u.id as user_id,u.nick_name,u.signature,u.head_image,u.is_authentication,u.v_type,u.v_icon,u.v_explain from ".DB_PREFIX.
                "share as s left join ".DB_PREFIX."user as u on s.author_id=u.id where s.author_id=".$user_id." order by s.create_time desc";
            $list = $GLOBALS['db']->getAll($sql);

            $list = $this->list_handle($list);
            $root['list'] = $list;
            $root['status'] = 1;
            $root['page_title'] = '我的分享';
            api_ajax_return($root);
        }
    }

    public function time_change($list){
        foreach ($list as $k=>&$v){
            $time = NOW_TIME;
            $sub = $time - $v['create_time'];
            if ($sub < 3600){
                $v['create_time'] = floor($sub / 60)."分钟前";
            }elseif ($sub < 86400){
                $v['create_time'] = floor($sub /3600)."小时前";
            }elseif ($sub < 604800){
                $v['create_time'] = floor($sub /86400)."天前";
            }else{
                $v['create_time'] = date('Y-m-d',$time);
            }
        }
        return $list;
    }

    public function list_handle($list){
        foreach ($list as $k=>&$v){
            //截取字符串
            if (strlen($v['title']) > 30){
                $v['title'] = mb_substr($v['title'], 0, 30, "utf-8")."...";
            }
            if (strlen($v['content']) > 90){
                $v['content'] = mb_substr($v['content'], 0, 90, "utf-8")."...";
            }

            //转换时间
            $time = NOW_TIME;
            $sub = $time - $v['create_time'];
            if ($sub < 3600){
                $v['create_time'] = floor($sub / 60)."分钟前";
            }elseif ($sub < 86400){
                $v['create_time'] = floor($sub /3600)."小时前";
            }elseif ($sub < 604800){
                $v['create_time'] = floor($sub /86400)."天前";
            }else{
                $v['create_time'] = date('Y-m-d',$time);
            }

            //图片处理,json转数组,并拼接为对应的oss地址
            if ($v['imgs'] != '') {
                $v['imgs'] = json_decode($v['imgs']);
                if ($v['imgs'] == "") {
                    $v['imgs'] = array();
                } else {
                    foreach ($v['imgs'] as $kk => $vv) {
                        //$goods['imgs'][$k] = get_domain() . APP_ROOT . $v;
                        $v['imgs'][$kk] = get_spec_image($vv);
                    }
                }
            } else {
                $v['imgs'] = array();
            }
        }
        return $list;
    }

    public function add_feedback(){
        $root = array(
            'status'=>1,
            'error'=>'',
            'page_title'=>'用户反馈'
        );
        api_ajax_return($root);
    }

    public function feedback(){
        $root = array();
        if (!$GLOBALS['user_info']) {
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
            api_ajax_return($root);
        } else {
            $user_id = $GLOBALS['user_info']['id'];

            if ($_REQUEST['content'] == ''){
                $root['error'] = "反馈内容不能为空";
                $root['status'] = 0;
                api_ajax_return($root);
            }

            $data['user_id'] = $user_id;
            $data['content'] = trim($_REQUEST['content']);
            $data['create_time'] = NOW_TIME;
            $res = $GLOBALS['db']->autoExecute(DB_PREFIX."feedback",$data,'INSERT');

            $root['status'] = 1;
            api_ajax_return($root);
        }
    }

    //我的邀请
    public function invite(){
        $root = array('status'=>1,'error'=>'','data'=>array());
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);
            $data['invite_id'] = $user_id;

            $m_config = load_auto_cache("m_config");//初始化手机端配置
            $data['content'] = $m_config['invite_benefit'];

            //邀请连接
            //$invite_url = SITE_DOMAIN.'/weixin/index.php?ref='.base64_encode($user_id);
            $invite_url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&act=invite_index&ref='.base64_encode($user_id);

            //用户邀请二维码生成
            $invite_image_dir =APP_ROOT_PATH."public/invite_image";
            if (!is_dir($invite_image_dir)) {
                @mkdir($invite_image_dir, 0777);
            }
            $path_dir = "/public/invite_image/invite_qrcode_".$user_id.".png";
            $path_logo_dir = "/public/invite_image/invite_qrcode_logo_".$user_id.".png";
            $qrcode_dir = APP_ROOT_PATH.$path_dir;
            $qrcode_dir_logo = APP_ROOT_PATH.$path_logo_dir;
            if(!is_file($qrcode_dir)||!is_file($qrcode_dir_logo)){
                get_qrcode_png($invite_url,$qrcode_dir,$qrcode_dir_logo);
            }
            if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
            {
                syn_to_remote_image_server(".".$path_dir);
                syn_to_remote_image_server(".".$path_logo_dir);
            }
            $data['img'] = get_spec_image(".".$path_logo_dir);
            $root['data'] = $data;
            $share = array();
            $share['wx_title'] = $GLOBALS['user_info']['user_name'].strim($m_config['share_title']);
            $share['wx_desc'] = strim($m_config['share_desc']);
            $share['wx_link'] = $invite_url;
            $share['img'] = get_spec_image($m_config['app_logo']);
            $root['share'] = $share;

            fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
            $jssdk=new JSSDK($m_config['wx_appid'],$m_config['wx_secrit']);
            $url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&act=invite';
            $from = $_REQUEST['from'];
            $isappinstalled = $_REQUEST['isappinstalled'];
            if($from){
                $url.='&from='.$from;
            }
            if($isappinstalled){
                $url.='&isappinstalled='.$isappinstalled;
            }
            $jssdk->set_url($url);
            $signPackage = $jssdk->getSignPackage();
            $root['signPackage'] = $signPackage;
        }
        $root['page_title'] = '邀请朋友';
        api_ajax_return($root);
    }
    
    //我的邀请页面
    public function invite_index(){
    	$m_config = load_auto_cache("m_config");//初始化手机端配置
    	$root['share_img'] = get_spec_image($m_config['share_img']);
    	$root['share_word'] = $m_config['share_word'];
    	$root['page_title'] = '邀请';
    	api_ajax_return($root);
    }

    //我的预约
    public function date_list(){
        $root = array('status'=>1,'error'=>'','list'=>array());
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);
            $sql = "select ud.id,ud.date_id,ud.create_time,ud.status,d.title from ".DB_PREFIX."user_date ud left join ".DB_PREFIX."date d on d.id = ud.date_id where ud.user_id=".$user_id;
            $list = $GLOBALS['db']->getAll($sql);
            if($list){
                foreach($list as $k=>$v){
                    $list[$k]['create_time'] = to_date($v['create_time']);
                }
                $root['list'] = $list;
            }
        }
        $root['page_title'] = '我的预约';
        api_ajax_return($root);
    }
    
    //我的余额
    public function balance(){
    	$root = array('status'=>1,'error'=>'','data'=>array());
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		$user_id=$GLOBALS['user_info']['shop_user_id'];
    		$page      = intval($_REQUEST['page']);
    		$page_size = intval($_REQUEST['page_size']);
    		$page      = $page ? $page : 1;
    		$page_size = $page_size ? $page_size : 20;
    		
    		
    		
    		
    		fanwe_require(APP_ROOT_PATH.'Fanwewd.php');
    		$fanwewd=new Fanwewd();
    		
    		$wd=$fanwewd->getMoneyLog($user_id, $page, 0,$page_size);
    		
    		$list=$wd['lists'];
    		$total_count=$wd['total_count'];
    		$total_page = ceil($total_count /$page_size);
    		
    		$data['list']=$list;    		
    		$data['total_page']=$total_page;
    		$root['data'] = $data;

    	}   	
    	$root['page_title'] = '我的余额';
    	api_ajax_return($root);
    }
    
    
    //保存账户信息
    public function user_save_wpk(){
    	$root = array();
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		$user_id = intval($GLOBALS['user_info']['id']);
    		$user_info_req = $_REQUEST;
    		
    		foreach($user_info_req as $k=>$v){
    			if($v!='user_center'||$v!='user_save_wpk'||$v!='user_save'){
    				$user_info[$k] = strim($v);
    			}
    		}
    		//判断性别是否可修改
    		if(isset($user_info['sex'])){
    			$user_info['is_edit_sex'] = 0;
    		}
    
    		$user_info['id'] =$user_id;
    		$user_info['signature'] = strim($user_info['signature']);
    		$user_info['nick_name'] = strim($user_info['nick_name']);
    		
    		//判断该实名是否存在
    		if ($user_info['job']){
    			$user_info['is_authentication'] = 0;//认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
    		}else if ($user_info['job_age']){
    			$user_info['is_authentication'] = 0;//认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
    		}else if ($user_info['company']){
    			$user_info['is_authentication'] = 0;//认证状态 0指未认证  1指待审核 2指认证 3指审核不通过
    		}

    		if($user_info['signature']=='')unset($user_info['signature']);
    		if($user_info['nick_name']=='')unset($user_info['nick_name']);
    		$m_config =  load_auto_cache("m_config");//初始化手机端配置
    		//昵称如果等于铭感词,则提示,如果包含 则用*代替
    		/*if($m_config['name_limit']==1){
    			$nick_name=$user_info['nick_name'];
    			$limit_sql =$GLOBALS['db']->getCol("SELECT name FROM ".DB_PREFIX."limit_name");
    			$in=in_array($nick_name,$limit_sql);
    			if($in){
    				ajax_return(array("status"=>0,"error"=>'昵称包含敏感词汇'));
    			}elseif($GLOBALS['db']->getCol("SELECT name FROM ".DB_PREFIX."limit_name WHERE '$nick_name' like concat('%',name,'%')")){
    				$user_info['nick_name']=str_replace($limit_sql,'*',$nick_name);
    			}
    		}*/
    		fanwe_require(APP_ROOT_PATH."system/libs/user_wpk.php");
    		//提交空字段不操作
    		if($user_info){
    			$status = save_user_wpk($user_info,'UPDATE');
    		}else{
    			$root['status'] = 1;
    			$root['error'] = '';
    			ajax_return($root);
    		}
    		if($status&&$status['status']!=0){
    			//更新session
    			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".$status['data']);
    			es_session::set("user_info", $user_info);
    
    			$user_id= $status['data'];
    			$sql = "select u.id,u.head_image,u.nick_name,u.real_name,u.company,u.job,u.job_age,u.province,u.city,u.is_authentication from ".DB_PREFIX."user as u where id=".$user_id;
    			$user= $GLOBALS['db']->getRow($sql);
    
    			$user['head_image'] = get_spec_image($user['head_image']);
    
    			$root['status'] = 1;
    			$root['error'] = '编辑成功';
    			$root['user'] = $user;
    		}else{
    			$root['status'] = 0;
    			$root['error'] = $status['error'];
    		}
    	}
    	$root['page_title'] = '个人信息';
    	api_ajax_return($root);//返回信息缺少认证信息
    }
        
    public function open_wd(){
    	$root = array('status'=>1,'error'=>'','user'=>array());
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		
    		$user_id = intval($GLOBALS['user_info']['id']);
    		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id,true,true);
    		//var_dump(intval($user['shop_user_id']));exit();
    		if($user){
    			 
    			//同步微店，生成数据
    			fanwe_require(APP_ROOT_PATH.'Fanwewd.php');
    			$fanwewd=new Fanwewd();    			
    			//app_redirect($fanwewd->getUserLoginUrl(intval($user['shop_user_id']),$fanwewd->getIndexUrl()));
    			$url=$fanwewd->getUserLoginUrl(intval($user['shop_user_id']),$fanwewd->getIndexUrl());
    			Header("Location: $url");
    		}
    	}    		    	
    	api_ajax_return($root);
    }
    
    //我的会员码
    public function vipcode(){
    	$root = array('status'=>1,'error'=>'','code_list'=>array(),'page_title'=>'会员激活码');
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    	
    		$user_id = intval($GLOBALS['user_info']['id']);
    		
    		$code_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vip_exchange where user_id = ".$user_id,true,true);
    		
    		$root['code_list']=$code_list;
    		
    		$m_config = load_auto_cache("m_config");//初始化手机端配置
    		$root['app_logo'] = get_spec_image($m_config['app_logo']);
    		$root['total_page'] = 1;
    	}
    	api_ajax_return($root);
    }
    
    //我的会员码详情页
    public function vipcode_detail(){
    	$root = array('status'=>1,'error'=>'','code_list'=>array(),'page_title'=>'会员激活码');
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		 
    		$user_id = intval($GLOBALS['user_info']['id']);
    		$code_id = intval($_REQUEST['code_id']);
    		$code_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_exchange where id = ".$code_id." and user_id=".$user_id,true,true);
    
    		$root['code_info']=$code_info;
    		
    		$m_config = load_auto_cache("m_config");//初始化手机端配置
    		$root['app_logo'] = get_spec_image($m_config['app_logo']);
    		
    		$invite_url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&ref='.base64_encode($user_id)."&vip_code=".$code_info['code'];
    		
    		$share = array();
    		$share['wx_title'] = $GLOBALS['user_info']['user_name'].strim($m_config['share_code_title']);
    		$share['wx_desc'] = strim($m_config['share_code_desc']);
    		$share['wx_link'] = $invite_url;
    		$share['img'] = get_spec_image($m_config['app_logo']);
    		$root['share'] = $share;
    		
    		fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
    		$jssdk=new JSSDK($m_config['wx_appid'],$m_config['wx_secrit']);
    		$url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&act=vipcode_detail&code_id='.$code_id;
    		$from = $_REQUEST['from'];
    		$isappinstalled = $_REQUEST['isappinstalled'];
    		if($from){
    			$url.='&from='.$from;
    		}
    		if($isappinstalled){
    			$url.='&isappinstalled='.$isappinstalled;
    		}
    		$jssdk->set_url($url);
    		$signPackage = $jssdk->getSignPackage();
    		$root['signPackage'] = $signPackage;
    	}
    	api_ajax_return($root);
    }
    
    //我的会员详情页
    public function vip(){
    	$root = array('status'=>1,'error'=>'','code_list'=>array(),'page_title'=>'会员详情');
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		 
    		$user_id = intval($GLOBALS['user_info']['id']);
    		$root['time_left']=0;
    		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id,true,true);
    		$vip_datetime=strtotime($user['vip_date']);
    		if (intval($user['vip'])>0&&$vip_datetime>=NOW_TIME) {
    			$vip_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip where is_effect=1 and vip_lv = ".intval($user['vip']),true,true);
    			$root['vip_info'] = $vip_info;
    			    			
    			$root['time_left']=intval(($vip_datetime-NOW_TIME)/86400+1);
    			
    		}
    		
    		$root['vip_id']=str_pad($user_id,12,0,STR_PAD_LEFT);
    		$root['vip_date']=$user['vip_date'];
    		$root['vip']=$user['vip'];
    		$root['code_count']=$GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."vip_exchange WHERE user_id=".$user_id,true,true);
    		$m_config = load_auto_cache("m_config");//初始化手机端配置
    		$root['app_logo'] = get_spec_image($m_config['app_logo']);
    	}
    	api_ajax_return($root);
    }
    
    //我的会员分享
    public function vip_share(){
    	$root = array('status'=>1,'error'=>'','code_list'=>array(),'page_title'=>'会员激活码');
    	if(!$GLOBALS['user_info']){
    		$root['error'] = "用户未登陆,请先登陆.";
    		$root['status'] = 0;
    		$root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
    	}else{
    		 
    		$user_id = intval($GLOBALS['user_info']['id']);
    		$code_id = intval($_REQUEST['code_id']);
    		$code_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vip_exchange where id = ".$code_id." and user_id=".$user_id,true,true);
    
    		$root['code_info']=$code_info;
    		
    		$m_config = load_auto_cache("m_config");//初始化手机端配置

    		$invite_url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&ref='.base64_encode($user_id)."&vip_code=".$code_info['code'];
    		
    		$share = array();
    		$share['wx_title'] = $GLOBALS['user_info']['user_name'].strim($m_config['share_code_title']);
    		$share['wx_desc'] = strim($m_config['share_code_desc']);
    		$share['wx_link'] = $invite_url;
    		$share['img'] = get_spec_image($m_config['app_logo']);
    		$root['share'] = $share;
    		
    		fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
    		$jssdk=new JSSDK($m_config['wx_appid'],$m_config['wx_secrit']);
    		$url = SITE_DOMAIN.'/weixin/index.php?ctl=user_center&act=vip_share&code_id='.$code_id;
    		$from = $_REQUEST['from'];
    		$isappinstalled = $_REQUEST['isappinstalled'];
    		if($from){
    			$url.='&from='.$from;
    		}
    		if($isappinstalled){
    			$url.='&isappinstalled='.$isappinstalled;
    		}
    		$jssdk->set_url($url);
    		$signPackage = $jssdk->getSignPackage();
    		$root['signPackage'] = $signPackage;
    		
    	}
    	api_ajax_return($root);
    }
    
}
