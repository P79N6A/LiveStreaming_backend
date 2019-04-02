<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class shareCModule  extends baseModule
{
    var $signPackage = '';
    var $user_info = '';
    var $wx_url = '';
    var $video_id = '';
    var $user_id = '';
    //新分享首页
    public function live()
    {
        $video_id  =  intval($_REQUEST['video_id']);
        $user_id = intval($_REQUEST['user_id']);
        $share_id = intval($_REQUEST['share_id']);
        $video_user = intval($_REQUEST['video_user']);
        $code = strim($_REQUEST['code']);
        if (defined('WEIXIN_DISTRIBUTION') && WEIXIN_DISTRIBUTION && $share_id) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/Wx.class.php');
            $wx_info = Wx::getWeixinInfo();
            require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
            Model::$lib = dirname(__FILE__);
            Model::build('weixin_distribution')->addWithUnionId($wx_info['unionid'],$share_id);
        }

        $call_back = SITE_DOMAIN.'/wap/index.php?ctl=share&act=live&user_id='.$user_id.'&video_id='.$video_id.'&share_id='.$share_id;
        $from = $_REQUEST['from'];
        $isappinstalled = $_REQUEST['isappinstalled'];
        if(trim($from)!=''){
            $call_back.='&from='.$from;
        }
        if(trim($isappinstalled)!=''){
            $call_back.='&isappinstalled='.$isappinstalled;
        }
        $this->check_user_info($call_back,$code);

        if($GLOBALS['user_info']){
            $root['user_info'] = $GLOBALS['user_info'];
        }else{
            $root['user_info'] = false;
        }
        $user_info  =   $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".$user_id );
        if($video_user&&$video_id==0){
            $sql = "select id as video_id from ".DB_PREFIX."video where user_id = ".$user_id;
            $video_id  =   $GLOBALS['db']->getOne($sql);
        }

        $root['wx_url'] = $this->wx_url;
        $m_config =  load_auto_cache("m_config");//初始化手机端配置
        $root['app_logo'] = get_spec_image($m_config['app_logo']);

        fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $video = $video_redis->getRow_db($video_id, array('user_id', 'is_live_pay', 'live_in', 'group_id', 'live_image', 'head_image', 'play_hls', 'play_url', 'video_type', 'channelid', 'begin_time', 'create_time', 'create_type'));
        if($video['live_in']!=1 &&  $video['live_in']!=3){
            $live_list=load_auto_cache("select_video");
            foreach($live_list as $k=>$v){
                if($v['user_id']==$user_id){
                    if($video_id != $v['room_id']){
                        $video_id=$v['room_id'];
                        $video = $video_redis->getRow_db($video_id, array('user_id', 'is_live_pay', 'live_in', 'group_id', 'play_hls', 'play_url', 'video_type', 'channelid', 'begin_time', 'create_time'));
                    }
                }
            }
        }
        $video['viewer_num'] =  $video_redis->get_video_watch_num($video_id);
        $video['podcast'] = getuserinfo($user_id,$video['user_id'],$video['user_id']);
        //禁用分享
        if($m_config['sina_app_api']==0&&$m_config['wx_app_api']==0&&$m_config['qq_app_api']==0){
        	$is_close_share = 1; 
        }
        
        // 付费直播提示下载弹窗
        if($video['is_live_pay'] == 1||$is_close_share){
            $video['play_hls'] = '';
            $video['play_url'] = '';
        } else if($video['live_in']==0 || $video['live_in']==3){
            $file_info = load_auto_cache('video_file', array(
                'id' => $video_id,
                'video_type' => $video['video_type'],
                'channelid' => $video['channelid'],
                'begin_time' => $video['begin_time'],
                'create_time' => $video['create_time'],
            ));
            $video['file_id'] = $file_info['file_id'];
            $video['urls'] = $file_info['urls'];
            foreach( $video['urls'] as $url)
            {
                $info = pathinfo($url);
                if($info['extension'] == 'mp4')
                {
                    $video['play_url'] = $url;
                    break;
                }
            }
        }else if ($video['live_in'] != 1) {
            $video['live_in'] = 0;
        }

        // 验证码直播提示下载弹窗
        if (defined('OPEN_EDU_MODULE') && OPEN_EDU_MODULE == 1) {
            if ($this->check_video_is_verify($video_id)) {
                $video['play_hls'] = '';
                $video['play_url'] = '';
                $video['is_verify'] = 1;
            }
        }
        //分享链接
        $video['url'] = $call_back;
        $root['video'] = $video;
        //回播日志
        $now = NOW_TIME-3600*24;
        $history = $GLOBALS['db']->getAll("select vh.id as room_id,vh.begin_time,vh.group_id as group_id,vh.max_watch_number as watch_number,vh.video_vid,vh.room_type,vh.vote_number,vh.channelid,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex from ".DB_PREFIX."video_history as vh left join ".DB_PREFIX."user as u on vh.user_id=u.id where  vh.room_type=3 and vh.is_del_vod =0 and vh.is_delete =0 and vh.user_id=u.id  and vh.user_id = ".$user_id." and vh.begin_time>".$now." order by vh.id desc");

        foreach($history as $kk=>$vv){
            $history[$kk]['end_time'] = format_show_date($vv['begin_time']);
            $history[$kk]['head_image'] = get_spec_image($vv['head_image']);
            $history[$kk]['user_url'] = url_app('home',array('podcast_id'=>$vv['user_id']));
            $history[$kk]['url'] = SITE_DOMAIN.'/wap/index.php?ctl=share&act=live&user_id='.$vv['user_id'].'&video_id='.$vv['room_id'];//分享链接
            $history[$kk]['nick_name'] = ($history[$kk]['nick_name']);
        }
        $root['history'] = $history;
        //hot_video  热门视频
        $video_hot = $GLOBALS['db']->getAll("select v.id as room_id,v.group_id as group_id,v.max_watch_number as watch_number,v.video_vid,v.room_type,v.vote_number,v.channelid,v.title,v.live_image,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex from ".DB_PREFIX."video as v left join ".DB_PREFIX."user as u on v.user_id=u.id where v.room_type=3 and (v.live_in = 1 or v.live_in = 3) and v.user_id=u.id and u.head_image <>'' and v.begin_time <> 0 order by v.max_watch_number desc limit 0,10");
        foreach($video_hot as $k=>$v){
            $video_hot[$k]['head_image'] = get_spec_image($v['head_image']);
            $video_hot[$k]['channelid'] = $v['channelid'];
            $video_hot[$k]['user_url'] = url_app('home',array('podcast_id'=>$v['user_id']));
            $video_hot[$k]['url'] = SITE_DOMAIN.'/wap/index.php?ctl=share&act=live&user_id='.$v['user_id'].'&video_id='.$v['room_id'];//分享链接
            $video_hot[$k]['live_image'] = get_spec_image($v['live_image']);
            $video_hot[$k]['nick_name'] = ($video_hot[$k]['nick_name']);

        }
        $root['video_hot'] = $video_hot;
        $root['app_down'] =  SITE_DOMAIN.'/mapi/index.php?ctl=app_download';

        if ((defined('DISTRIBUTION_SCAN')&&DISTRIBUTION_SCAN==1))   //二级分销开启
        {
            $root['app_down'] = $GLOBALS['db']->getOne("select distribution_url from ".DB_PREFIX."user where id = ".$user_id,true,true);
        }

        $root['signPackage'] = $this->signPackage;
        $share = array();
        $share['short_name'] = strim($m_config['short_name']);
        $share['share_title'] = strim($m_config['share_title']);
        $share['share_img_url'] =get_spec_image($user_info['head_image']);
        $share['share_wx_url'] =  $video['url'];
        $share['share_desc'] = strim($m_config['share_title']).($user_info['nick_name']).'正在直播,快来一起看~';
        $root['share'] = $share;

        $tim_user_id = $root['user_info']['user_id'] > 0 ? $root['user_info']['user_id'] : 0;
        $usersig = load_auto_cache("usersig", array("id" => $tim_user_id));
        $root['tim'] = array(
            'sdkappid' => $m_config['tim_sdkappid'],
            'account_type' => $m_config['tim_account_type'],
            'account_id' => $tim_user_id,
            'usersig' => $usersig['usersig'],
        );

        //分销功能
        if(((defined('OPEN_DISTRIBUTION')&&OPEN_DISTRIBUTION==1)&&intval($m_config['distribution'])==1)||intval(OPEN_REWARD_POINT)==1){
            $root['register_url'] = SITE_DOMAIN.'/wap/index.php?ctl=distribution&act=init_register&user_id='.$share_id;
        }
        
        $root['show_live'] = 1;
        if (defined('OPEN_BM')&&OPEN_BM==1) {
        	$bm_video=$GLOBALS['db']->getRow("select is_bm,private_key from ".DB_PREFIX."video where id=".$video_id );
        	
        	if ($bm_video['is_bm']==1) {
        		$root['room_pwd']=$bm_video['private_key'];
        		$root['show_live'] = 0;
        	}       	        	
        }
        
        
        api_ajax_return($root);

    }

    //检查用户是否登陆
    public  function check_user_info($back_url,$code=''){
    	
    	fanwe_require(APP_ROOT_PATH."system/utils/weixin.php");
    	$m_config =  load_auto_cache("m_config");//手机端配置
    	/*if($m_config['wx_secrit']||$m_config['wx_appid']){
    		return false;
    	}*/
        if($_REQUEST['ttype']==1){
            return true;
        }
        $is_weixin=isWeixin();

        if(!$is_weixin){
            return false;
        }

        $user_info = es_session::get('user_info');

        if(!$user_info){
            //解密
			if($code!=''){
				$weixin=new weixin($m_config['wx_gz_appid'],$m_config['wx_gz_secrit']);
				$wx_info=$weixin->scope_get_userinfo($code);
			}

            if($wx_info['openid']){

                $has_user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where wx_unionid = '".$wx_info['unionid']."'");              

                if(!$has_user){
                    $data=array();
                    $data['user_name']= $wx_info['nickname'];
                    $data['is_effect'] = 1;
                    $data['head_image']= $wx_info['headimgurl'];
                    syn_to_remote_image_server($wx_info['headimgurl']);
                    $data['gz_openid']= $wx_info['openid'];
                    $data['wx_unionid']= $wx_info['unionid'];
                    //用户是否关注公众号
                    $data['subscribe']= $wx_info['subscribe'];
                    $data['create_time']= get_gmtime();
                    $data['user_pwd'] = md5(rand(99999,9999999));
                    $GLOBALS['db']->autoExecute(DB_PREFIX."user",$data);
                    $user_id = $GLOBALS['db']->insert_id();
                    $data['id'] = $user_id ;
                    $user_info = $data;
                }else{
                    if($has_user['subscribe']!=$wx_info['subscribe']){
                        //更新公众号是否关注的状态
                        $GLOBALS['db']->query("update ".DB_PREFIX."user set subscribe = ".$wx_info['subscribe']."  where id ='".$has_user['id']."'");
                        $has_user['subscribe'] = $wx_info['subscribe'];
                    }
                    $user_info = $has_user;
                }

                es_session::set("user_info", $user_info);
                $GLOBALS['db']->query("update ".DB_PREFIX."user set login_time = '".get_gmtime()."'  where wx_unionid ='".$wx_info['unionid']."'");
					
	  			$this->wx_url = '';
	            $this->user_info = $user_info;
	            fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
	            $jssdk=new JSSDK($m_config['wx_gz_appid'],$m_config['wx_gz_secrit']);
				$jssdk->set_url($back_url);
	            $signPackage = $jssdk->getSignPackage();
	            $this->signPackage = $signPackage;
            }else{
                //加密
                /*$weixin=new weixin($m_config['wx_appid'],$m_config['wx_secrit'],$back_url);
				$wx_url=$weixin->scope_get_code();
                $this->wx_url = $wx_url;*/

                $this->wx_url = '';
                $this->user_info = $user_info;
                fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
                $jssdk=new JSSDK($m_config['wx_gz_appid'],$m_config['wx_gz_secrit']);
                $jssdk->set_url($back_url);
                $signPackage = $jssdk->getSignPackage();
                $this->signPackage = $signPackage;
            }
        }else{
            $this->wx_url = '';
            $this->user_info = $user_info;
            fanwe_require(APP_ROOT_PATH."system/utils/jssdk.php");
            $jssdk=new JSSDK($m_config['wx_gz_appid'],$m_config['wx_gz_secrit']);
			$jssdk->set_url($back_url);
            $signPackage = $jssdk->getSignPackage();
            $this->signPackage = $signPackage;
        }

    }

    //判断是否为验证码直播
    public function check_video_is_verify($room_id)
    {
        if (!$room_id) {
            return false;
        }
        $table = DB_PREFIX . "edu_video_info";
        $where = " video_id =" . $room_id;
        $verify = $GLOBALS['db']->getRow("SELECT video_code,is_verify FROM $table WHERE  $where", true,
            true);//获取的直播记录数量
        if (!empty($verify) && $verify['is_verify'] == 1) {
            return true;
        } else {
            return false;
        }
    }

}

?>