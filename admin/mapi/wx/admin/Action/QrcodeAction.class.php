<?php

/**
 *
 */
class QrcodeAction extends CommonAction
{
    public function index()
    {
        /*$_REQUEST['type'] = intval($_REQUEST['type']);
        $map              = array('type' => $_REQUEST['type'], 'is_delete' => 0);
        $id               = intval($_REQUEST['id']);
        $title            = trim($_REQUEST['title']);
        if ($id) {
            $map['id'] = $id;
        }
        if ($title) {
            $map['title'] = array('like', '%' . trim($title) . '%');
        }*/
        $model = D(MODULE_NAME);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    public function add()
    {
    	
    	$vip_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vip where is_effect = 1 and is_delete=0 order by id asc");  //二级地址
    	
    	$this->assign("vip_list",$vip_list);
    	$this->display();
    }
    
    public function insert() {
    	B('FilterString');
    	$ajax = intval($_REQUEST['ajax']);
    	$data = M(MODULE_NAME)->create ();
    	    	
    	
    	//开始验证有效性
    	$this->assign("jumpUrl",u(MODULE_NAME."/add"));
    	if(!check_empty($data['vip_id']))
    	{
    		$this->error("请输入分销的vip_id!");
    	}
    	if(!check_empty($data['user_id']))
    	{
    		$this->error("请输入分销的用户id!");
    	}
    	$data['create_time']=NOW_TIME;
    	
    	$list=M(MODULE_NAME)->add($data);
    	if (false !== $list) {
    		
    		log_result(APP_ROOT_PATH."public/sell_image");
    		//生成二维码
    		//用户邀请二维码生成
    		$invite_image_dir =APP_ROOT_PATH."public/sell_image";
    		if (!is_dir($invite_image_dir)) {
    			@mkdir($invite_image_dir, 0777);
    		}
    		
    		$url=SITE_DOMAIN.'/weixin/index.php?ctl=pay&act=qrcode&pay_id=1&vip_id='.$data['vip_id'].'&qrcode_id='.$list;
    		$path_dir = "/public/sell_image/sell_qrcode_".$data['user_id']."_".$list.".png";
    		$path_logo_dir = "/public/sell_image/sell_qrcode_".$data['user_id']."_".$list.".png";
    		$qrcode_dir = APP_ROOT_PATH.$path_dir;
    		$qrcode_dir_logo = APP_ROOT_PATH.$path_logo_dir;
    		if(!is_file($qrcode_dir)||!is_file($qrcode_dir_logo)){
    			get_qrcode_png($url,$qrcode_dir,$qrcode_dir_logo);
    		}
    		if($GLOBALS['distribution_cfg']['OSS_TYPE']&&$GLOBALS['distribution_cfg']['OSS_TYPE']!='NONE')
    		{
    			syn_to_remote_image_server(".".$path_dir);
    			syn_to_remote_image_server(".".$path_logo_dir);
    		}
    		
    		$GLOBALS['db']->query("update ".DB_PREFIX."qrcode set img = '".SITE_DOMAIN.$path_logo_dir."' where id=".intval($list)."");
    		
    		save_log($log_info.L("INSERT_SUCCESS"),1);
    		$this->success(L("INSERT_SUCCESS"));
    	} else {
    		//错误提示
    		save_log($log_info.L("INSERT_FAILED"),0);
    		$this->error(L("INSERT_FAILED"));
    	}
    }
    
    public function payment_notice()
    {
    	$map              = array('qrcode_id' => $_REQUEST['id']);
    	if (isset($_REQUEST['is_paid'])&&$_REQUEST['is_paid']>=0) {
    		$map['is_paid'] = $_REQUEST['is_paid'];
    	}
    	$model = D('payment_notice');
    	if (!empty($model)) {
    		$this->_list($model, $map);
    	}
    	$this->assign("qrcode_id",$_REQUEST['id']);
    	$this->display();
    }
    
    /*
     * //生成二维码
    	
    	$m_config = load_auto_cache("m_config");
    	fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
    	$jssdk=new JSSDK($m_config['wx_appid'],$m_config['wx_secrit']);
    	 
    	$Token=$jssdk->getAccessToken();
    	$expire_seconds=604800;
    	if (intval($data['is_limit'])==0) {
    		$action_name='QR_SCENE';
    	}else{
    		$action_name='QR_LIMIT_SCENE';
    	}
    	$scene=array();
    	$scene['scene_id']=NOW_TIME;
    	
    	$scene['user_id']=$data['user_id'];
    	$scene['vip_id']=$data['vip_id'];
    	
    	$action_info['scene']=$scene;
     */
   
}
