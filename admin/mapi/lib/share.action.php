<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class shareModule  extends baseModule
{
    var $signPackage = '';
    var $user_info = '';
    var $wx_url = '';
    var $video_id = '';
    var $user_id = '';
    //分享首页
	public function index()
	{
        $video_id  =  intval($_REQUEST['video_id']);
        $this->video_id = $video_id;
        $user_id = intval($_REQUEST['user_id']);
        $this->user_id = $user_id;
        $share_id = intval($_REQUEST['share_id']);
       
        $code = strim($_REQUEST['code']);
       
        $is_black = intval($_REQUEST['is_black']);
       
        $call_back = SITE_DOMAIN.'/wap/index.php?ctl=share&act=index&user_id='.$user_id.'&video_id='.$video_id;
        $from = $_REQUEST['from'];
        $isappinstalled = $_REQUEST['isappinstalled'];
        if($from){
            $call_back.='&from='.$from;
        }
        if($isappinstalled){
            $call_back.='&isappinstalled='.$isappinstalled;
        }
        $this->check_user_info($call_back,$code);
        /*if($code&&strlen($code)==32){
        	$call_back = SITE_DOMAIN.'/wap/index.php?ctl=share&act=index&user_id='.$user_id.'&video_id='.$video_id."&code=".$code."&state=1";
        	$this->check_user_info($call_back,$code);
        }else{
        	$call_back = SITE_DOMAIN.'/wap/index.php?ctl=share&act=index&user_id='.$user_id.'&video_id='.$video_id;
        	$this->check_user_info($call_back);
        }*/
        if($GLOBALS['user_info']){
            $root['user_info'] = $GLOBALS['user_info'];
        }else{
            $root['user_info'] = false;
        }
        $user_info  =   $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".$user_id );
        $root['wx_url'] = $this->wx_url;
  		$m_config =  load_auto_cache("m_config");//初始化手机端配置
        $root['app_logo'] = get_spec_image($m_config['app_logo']);
		
		$live_in = $GLOBALS['db']->getOne("select v.live_in from ".DB_PREFIX."video  as v where v.id = ".$video_id );
        fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
		if(intval($live_in)==0 || intval($live_in)==3){
            if(intval($live_in)==3){
                $video =   $GLOBALS['db']->getRow("select vh.id as room_id,vh.group_id as group_id,vh.max_watch_number as watch_number,vh.video_vid,vh.room_type,vh.vote_number,vh.channelid,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex ,vh.live_in, vh.video_type, vh.begin_time,vh.is_live_pay from ".DB_PREFIX."video as vh left join ".DB_PREFIX."user as u on vh.user_id=u.id where vh.room_type=3 and vh.id = ".$video_id );
            }else{
                $video =   $GLOBALS['db']->getRow("select vh.id as room_id,vh.group_id as group_id,vh.max_watch_number as watch_number,vh.video_vid,vh.room_type,vh.vote_number,vh.channelid,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex ,vh.live_in, vh.video_type, vh.begin_time,vh.is_live_pay from ".DB_PREFIX."video_history as vh left join ".DB_PREFIX."user as u on vh.user_id=u.id where vh.room_type=3 and vh.id = ".$video_id );
            }

            $video['viewer_num'] =  $video_redis->get_video_watch_num($video['room_id']);
            $video['podcast'] = getuserinfo($user_id,$video['user_id'],$video['user_id']);

            if($video['video_type'] == 1 && $video['channelid'])
            {
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/video_factory.php');
                $video_factory = new VideoFactory();
                $ret = $video_factory->GetVodUrls($video['channelid'], $video['begin_time']);
                if($video['is_live_pay'] == 1||(intval($m_config['qq_app_api'])&&intval($m_config['wx_app_api'])&&intval($m_config['sina_app_api']))){
                	$video['urls'] = '';
                	$video['file_id'] ='' ;
                }else{
                	$video['urls'] = array_shift($ret['urls']);
                	$video['file_id'] = $ret['file_id'];
                }
            } else {
                //播放地址
                if($video['is_live_pay'] == 1||(intval($m_config['qq_app_api'])&&intval($m_config['wx_app_api'])&&intval($m_config['sina_app_api']))){
                	$video['video_list'] = '';
	                $video['urls'] = '';
	                $video['file_id'] = '';
                }else{
	                $video_vid =  get_vodset_by_video_id($video['room_id']);
	                foreach($video_vid['vodset'] as $k=>$v){
	                    $playSet = $v['fileSet'][0]['playSet'];
	                    foreach($playSet as $kk=>$vv){
	                        if(strpos($vv['url'],'f0.mp4')){
	                            $video_vid_list[$k]['url'] = $vv['url'];
	                        }
	                    }
	                    $video_vid_list[$k]['image_url'] = $v['fileSet'][0]['image_url'];
	                    $video_vid_list[$k]['fileId'] = $v['fileSet'][0]['fileId'];
	                }
	                $video['video_list'] = $video_vid_list;
	                $video['urls'] = $video_vid_list[0]['url'];
	                $video['file_id'] = $video_vid_list[0]['fileId'];
                }
            }

			if(intval($video['room_id'])==0){
				$video =   $GLOBALS['db']->getRow("select v.id as room_id,v.group_id as group_id,v.max_watch_number as watch_number,v.video_vid,v.room_type,v.vote_number,v.channelid,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex ,v.live_in,v.is_live_pay from ".DB_PREFIX."video as v left join ".DB_PREFIX."user as u on v.user_id=u.id where v.room_type=3 and v.id = ".$video_id );
			}

		}else{
			$video =   $GLOBALS['db']->getRow("select v.id as room_id,v.group_id as group_id,v.max_watch_number as watch_number,v.video_vid,v.room_type,v.vote_number,v.channelid,v.play_flv,v.play_hls,v.play_rtmp,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex ,v.live_in,v.is_live_pay from ".DB_PREFIX."video as v left join ".DB_PREFIX."user as u on v.user_id=u.id where v.room_type=3 and v.id = ".$video_id );
            $video['viewer_num'] =  $video_redis->get_video_watch_num($video['room_id']);
			 if(intval($video['live_in'])!=1 && intval($video['live_in'])!=3){
        		$video['live_in'] = 0;
       		 }

		}
		
		
        $root['appid'] =$m_config['vodset_app_id'] ;
        $root['video_type'] =$m_config['video_type'] ;
        if(intval($video['live_in'])!=1 && intval($video['live_in'])!=3){
        	$video['live_in'] = 0;
        }
         //禁用分享
        if($m_config['sina_app_api']==0&&$m_config['wx_app_api']==0&&$m_config['qq_app_api']==0){
        	$is_close_share = 1; 
        }
        // 付费直播提示下载弹窗
        if($video['is_live_pay'] == 1||$is_close_share){
            $video['play_hls'] = '';
            $video['play_url'] = '';
        }
        
		//付费不能观看
		if($video['is_live_pay']==1){
			$video['live_in'] = 4;
		}
        //若当前 视频 不在直播状态，查找当前主播的直播视频
        /*if($video['live_in']!=1){
            //查找当前主播的直播视频
            $noe_video = $GLOBALS['db']->getRow("select v.id as room_id,v.group_id as group_id,v.watch_number,v.video_vid,v.room_type,v.vote_number,v.channelid,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex ,v.live_in from ".DB_PREFIX."video as v left join ".DB_PREFIX."user as u on v.user_id=u.id where  v.user_id = ".$user_id." and v.live_in =1" );
            //若视频有值
            if($noe_video['room_id']){
                $video = $noe_video;
            }
        }*/
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
            $history[$kk]['url'] = SITE_DOMAIN.'/wap/index.php?ctl=share&act=index&user_id='.$vv['user_id'].'&video_id='.$vv['room_id'];//分享链接
        }
        $root['history'] = $history;
        //hot_video  热门视频
        $video_hot = $GLOBALS['db']->getAll("select v.id as room_id,v.group_id as group_id,v.max_watch_number as watch_number,v.video_vid,v.room_type,v.vote_number,v.channelid,v.title,v.live_image,u.id as user_id,u.nick_name as nick_name,u.head_image as head_image,u.user_level as user_level,u.sex as sex from ".DB_PREFIX."video as v left join ".DB_PREFIX."user as u on v.user_id=u.id where v.room_type=3 and (v.live_in = 1 or v.live_in = 3) and v.user_id=u.id and u.head_image <>'' and v.begin_time <> 0 order by v.max_watch_number desc limit 0,10");
        foreach($video_hot as $k=>$v){
            $video_hot[$k]['head_image'] = get_spec_image($v['head_image']);
            $video_hot[$k]['channelid'] = $v['channelid'];
            $video_hot[$k]['user_url'] = url_app('home',array('podcast_id'=>$v['user_id']));
            $video_hot[$k]['url'] = SITE_DOMAIN.'/wap/index.php?ctl=share&act=index&user_id='.$v['user_id'].'&video_id='.$v['room_id'];//分享链接
            $video_hot[$k]['live_image'] = get_spec_image($v['live_image']);
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
		$share['share_desc'] = strim($m_config['share_title']).$user_info['nick_name'].'正在直播,快来一起看~';
		$root['share'] = $share;

        $tim_user_id = $root['user_info']['user_id'] ? $root['user_info']['user_id'] : 0;
        $usersig = load_auto_cache("usersig", array("id"=>$tim_user_id));
        $root['usersig'] = $usersig['usersig'];
        $root['tim_account_id'] = $tim_user_id;

        $m_config =  load_auto_cache("m_config");
        $root['tim_sdkappid'] = $m_config['tim_sdkappid'];
        $root['tim_account_type'] = $m_config['tim_account_type'];

		//分销功能
		if((defined('OPEN_DISTRIBUTION')&&OPEN_DISTRIBUTION==1)&&intval($m_config['distribution'])==1){
			$root['register_url'] = SITE_DOMAIN.'/wap/index.php?ctl=distribution&act=init_register&user_id='.$share_id;
		}
        api_ajax_return($root);

	}

    //新分享首页
    public function live()
    {
        $video_id  =  intval($_REQUEST['video_id']);
        $user_id = intval($_REQUEST['user_id']);
        $share_id = intval($_REQUEST['share_id']);
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
        $root['wx_url'] = $this->wx_url;
        $m_config =  load_auto_cache("m_config");//初始化手机端配置
        $root['app_logo'] = get_spec_image($m_config['app_logo']);

        fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/VideoRedisService.php');
        $video_redis = new VideoRedisService();
        $video = $video_redis->getRow_db($video_id, array('user_id', 'is_live_pay', 'live_in', 'group_id', 'play_hls', 'play_url', 'video_type', 'channelid', 'begin_time', 'create_time'));
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
        }
        else if($video['live_in']==0 || $video['live_in']==3){
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
        $share['share_desc'] = strim($m_config['share_title']).$user_info['nick_name'].'正在直播,快来一起看~';
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

	/**
	 * 分销/邀请注册奖励模块
	 */
	public function init_register()
	{
		fanwe_require(APP_ROOT_PATH.'mapi/xr/core/common.php');
		$root = array('status' => 0,'error'=>'');
		$share_id = intval($_REQUEST['user_id']);
		if(!$share_id){
			$root['error'] = '分享ID错误';
			api_ajax_return($root);
		}
		$from_user = $GLOBALS['db']->getRow("select id,nick_name,head_image from ".DB_PREFIX."user  where id = ".$share_id);
		if(!$from_user){
			$root['error'] = '分享ID错误,该会员不存在';
			api_ajax_return($root);
		}
		$root['from_nick_name'] = $from_user['nick_name'];
		$root['from_head_image'] = deal_weio_image($from_user['head_image'],'head_image');
		$root['url'] =SITE_DOMAIN.'/mapi/index.php?ctl=share&act=register&user_id='.$share_id;
		$root['page_title'] = '手机注册';
		$root['app_down_url'] = SITE_DOMAIN."/appdown.php";
		$root['is_login_user'] = 0;
		$root['nick_name'] = '';
		$root['head_image'] = '';
		$root['status'] = 1;
		$m_config =  load_auto_cache("m_config");//初始化手机端配置
		$root['app_name'] = $m_config['app_name'];
		if($GLOBALS['user_info'])
		{
			$root['is_login_user'] = $GLOBALS['user_info']['id'];
			$row = $GLOBALS['db']->getRow("select nick_name,head_image from ".DB_PREFIX."user  where id = ". $GLOBALS['user_info']['id']);
			$root['nick_name'] = $row['nick_name'];
			$root['head_image'] = deal_weio_image($row['head_image'],'head_image');
		}
		if(OPEN_YPSMS==1){
			$root['status']=1;
			$area=array();
			$area[]=array('id'=>1,'name'=>'中国大陆');
			/*$area[]=array('id'=>2,'name'=>'港澳台');
			 $area[]=array('id'=>3,'name'=>'日本');
			$area[]=array('id'=>4,'name'=>'韩国');*/
			$area[]=array('id'=>5,'name'=>'其他国家(请在号码前加上区号)');
			$root['area']=$area;
		}
		api_ajax_return($root);
	}


	/**
	 * 邀请注册奖励——保存
	 */
	public function register()
	{
		$root = array('status' => 1,'error'=>'');
		$root['url'] = get_domain() . "/mapi/index.php?ctl=app_download&act=index";
		if(!$_REQUEST)
		{
			app_redirect(get_domain()."/");
		}
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = strim($v);
		}
		$p_user_id = intval($_REQUEST['user_id']);
		$mobile = $_REQUEST['mobile'];
		if(!$mobile){
			$root['error'] = "手机号未上传！";
			$root['status'] = 0;
			api_ajax_return($root);
		}
		$p_user_id = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where id =" . $p_user_id);
		$mobile = $GLOBALS['db']->getOne("select id from " . DB_PREFIX . "user where mobile =" . $mobile);
		if(intval($p_user_id)==0){
			$root['error'] = "上级用户不存在！";
			$root['status'] = 0;
			api_ajax_return($root);
		}
		if($mobile){
			$root['error'] = "手机号已被注册";
			$root['status'] = 0;
			api_ajax_return($root);
		}
		if($mobile!=''){
			$user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $mobile);
			es_session::set("user_info", $user_info);
			$root['is_url'] = 1;
			$root['is_lack'] = $user_info['is_lack'];//是否缺少用户信息
			$root['is_agree'] = intval($user_info['is_agree']);//是否同意直播协议 0 表示不同意 1表示同意
			$root['user_id'] = intval($user_info['user']['id']);
			$root['nick_name'] = $user_info['nick_name'];
			$root['family_id']=intval($user_info['family_id']);
			$root['family_chieftain']=intval($user_info['family_chieftain']);
			$root['error'] = "登录成功";
			$root['user_info'] = $user_info;
			$root['status'] = 1;
			api_ajax_return($root);

		}
		if($root['status']!=0){
			fanwe_require(APP_ROOT_PATH."system/libs/user.php");
			$result = do_login_user($_REQUEST['mobile'],$_REQUEST['verify_coder'],$p_user_id);
		}
		if($result['status'])
		{
			$root['user_id'] = $result['user']['id'];
			$root['status'] = 1;
			if($result['user']['head_image']==''||$result['user_info']['head_image']==''){
				//头像
				$m_config =  load_auto_cache("m_config");//初始化手机端配置
				$system_head_image = $m_config['app_logo'];

				if($system_head_image==''){
					$system_head_image = './public/attachment/test/noavatar_11.JPG';
					syn_to_remote_image_server($system_head_image);
				}

				$data = array(
					'head_image' => $system_head_image,
					'thumb_head_image' => get_spec_image($system_head_image,40,40),
					'share_up_id' =>$result['p_user_id'],
				);

				$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE", "id=".$result['user']['id']);

				fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
				$user_redis = new UserRedisService();
				$user_redis->update_db($result['user']['id'],$data);

				//更新session
				$user_info = $GLOBALS['db']->getRow("select * from " . DB_PREFIX . "user where id =" . $result['user']['id']);
				es_session::set("user_info", $user_info);
			}
			$root['is_url'] = 1;
			$root['is_lack'] = $result['is_lack'];//是否缺少用户信息
			$root['is_agree'] = intval($result['user']['is_agree']);//是否同意直播协议 0 表示不同意 1表示同意
			$root['user_id'] = intval($result['user']['id']);
			$root['nick_name'] = $result['user']['nick_name'];
			$root['family_id']=intval($result['user']['family_id']);
			$root['family_chieftain']=intval($result['user']['family_chieftain']);
			$root['error'] = "注册成功";
			$root['user_info'] = $result['user_info'];

		}
		else
		{
			$root['status'] = 0;
			if($root['error']=='')
				$root['error'] = $result['info'];
		}
		api_ajax_return($root);
	}

}

?>