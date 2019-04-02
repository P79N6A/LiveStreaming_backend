<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class VideoCollectnewAction extends CommonAction{
	public function __construct()
	{
		parent::__construct();
		require_once APP_ROOT_PATH."/admin/Lib/Action/VideoCommonAction.class.php";
        require_once APP_ROOT_PATH."/admin/Lib/Action/UserCommonAction.class.php";
	}
/**
 * 视频采集
 *
 */
	public function index(){
		
		$str = file_get_contents('http://api.zbjk.xyz:81/kuke/json.txt');//将整个文件内容读入到一个字符串中
		$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
		//$start = strrpos($str, ",");
		//$str = substr_replace($str,'',$start,1);
		
		$array = json_decode($str,true);
		$array = $array['pingtai'];
		
		$data = array();
        foreach ($array as $row) {
			$newArray = [
                "name" => $row["title"],
                "url" => "http://api.zbjk.xyz:81/kuke/".$row["address"],
                "img" => $row["xinimg"],
            ];
            $data[] = $newArray;
        }


		$this->assign('data',$data);
		$this->display();
	}
	public function tohot(){
		if(!$_POST){
			$this->display();
		}else{
			$video['id'] = $_REQUEST['id'];
			$video['sort'] = $_REQUEST['sort'];
			$video['robot_num'] = rand(3100,9800);
			$video['sort_num'] = $_REQUEST['sort_num'];
			
			$list=M("Video")->save ($video);
			$this->success('已上热门');
		}
	}
	
	public function addroom(){
		if(!$_POST){
			$this->display();
		}else{
			
			$fileName = '';
			
			if ($_FILES["img"]["error"] > 0){
				echo "Error: " . $_FILES["img"]["error"] . "<br />";
				exit();
			}else{
				$fileName = time().$_FILES["img"]["name"];				
				move_uploaded_file($_FILES["img"]["tmp_name"],"./apk/" .  $fileName);
			}		
			
			$wq_nickname = $_REQUEST['name'];
			$wq_logourl = 'http://'.$_SERVER['HTTP_HOST'].'/apk/'.$fileName;
			$wq_url = $_REQUEST['url'];
				
			$m_config=load_auto_cache("m_config");
			//增加虚拟会员
			$userRobot = array();
			$userRobot['nick_name'] = $wq_nickname;
			$userRobot['head_image'] = $wq_logourl;
			$userRobot['is_admin'] = "0";
			$userRobot['mobile'] = "";
			$userRobot['province'] = "";
			$userRobot["city"] = "";
			$userRobot["sex"] = "1";
			$userRobot['user_level'] = "1";
			$userRobot['signature'] = "";
			$userRobot['is_effect'] = "1";
			$userRobot['is_ban'] = "0";
			$userRobot["ban_time"] = "";
			$userRobot["is_authentication"] = "0";
			$userRobot["authentication_type"] = "0";
			$userRobot["v_explain"] = "0";
			$userRobot['identify_positive_image'] = "";
			$userRobot["identify_nagative_image"] = "";
			$userRobot["identify_hold_image"] = "";
			$userRobot["identify_number"] = "";
			$userRobot['member_type'] = "1";
			$userRobot['is_robot'] = "1";
			$userRobot['v_icon'] = "";
			$userRobot["score"] = "10";

			$common = new UserCommon();
			filter_request($userRobot);
			$res = save_user($userRobot,'INSERT',$update_status=1);
			$user_id = intval($res['data']);
			
			
			require_once APP_ROOT_PATH."/mapi/lib/core/common.php";
			$video_id = get_max_room_id(0);
			$data =array();
			$data['id'] = $video_id;
			//room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
			$data['room_type'] = 3;
			$data['virtual_number'] = intval($m_config['virtual_number']);
			$data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

			$data['live_image'] = $wq_logourl;
			$data['head_image'] = $wq_logourl;
			$data['thumb_head_image'] = $wq_logourl;
			$data['sex'] = "2";//性别 0:未知, 1-男，2-女
			$data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

			if($data['video_type'] > 0){
				require_once(APP_ROOT_PATH.'system/tim/TimApi.php');
				$api = createTimAPI();
				$ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
				if ($ret['ActionStatus'] != 'OK'){
					$this->error("加入失败[".$ret['ErrorCode'].$ret['ErrorInfo']."]");
				}
				$data['group_id'] = $ret['GroupId'];
			}

			$data['monitor_time'] = to_date(1803234353,'Y-m-d H:i:s');//主播心跳监听
			$data['title'] = $wq_nickname;
			$data['cate_id'] = 1;
			$data['user_id'] = $user_id;
			$data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
			//$data['watch_number'] = '662';//'当前观看人数';
			$data['vote_number'] = '';//'获得票数';
			$data['province'] = "火星";//'省';
			$data['city'] = "火星";//'城市';
			$data['xpoint'] = "";
			$data['ypoint'] = "";
			$data['robot_num'] = rand(380,2810);

			$data['create_time'] = NOW_TIME;//'创建时间';
			$data['begin_time'] = NOW_TIME;//'开始时间';
			$data['end_time'] = '';//'结束时间';
			$data['is_hot'] = 1;//'1热门; 0:非热门';
			$data['is_new'] =1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

			$data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开
			$sort_init = 0;
			$data['sort_init'] = 200000000 + $sort_init;
			$data['sort_num'] = $data['sort_init'];
			//$data['sort'] = 111222333;

			$data['prop_table'] = createPropTable();
			//直播分类
			$data['classified_id'] = 8;

			$play_url = $wq_url;
			//判断流媒体格式
			//rtmp格式
			if(preg_match("/^rtmp:/",$play_url)){
				//RTMP格式
				$data['play_rtmp'] = $play_url;
			}
			if(preg_match("/flv$/",$play_url)){
				//FLV
				$data['play_flv'] = $play_url;
			}
			if(preg_match("/mp4$/",$play_url)){
				//MP4
				$data['play_mp4'] = $play_url;
			}
			if(preg_match("/m3u8$/",$play_url)){
				//HLS 仅此格式能播放
				$data['play_hls'] = $play_url;
			}
			
			$data['is_live_pay'] = 0;
			$data['live_pay_type'] = 0;
			$data['channelid'] = "gather";

			$GLOBALS['db']->autoExecute(DB_PREFIX."video", $data,'INSERT');

			if($GLOBALS['db']->affected_rows()){
				sync_video_to_redis($video_id,'*',false);
				$this->success("加入成功");
			}else{
				$this->error("加入失败");
			}
		}

	}
	
	//详细页
	public function two(){
        $path = iconv('utf-8', 'gbk', $_GET['url']);
        $str = file_get_contents($path);
		
		//$start = strrpos($str, ",");
		//$str = substr_replace($str,'',$start,1);
		//echo $str;
		
		$array = json_decode($str,true);
		$array = $array['zhubo'];
		$data = array();
        foreach ($array as $row) {
			$newArray = [
                "nickname" => $row["title"],
                "play_url" => $row["address"],
                "logourl" =>  $row["img"],
            ];
            $data[] = $newArray;
        }
				
		$this->assign('data',$data);
		$this->display();
	}
	//加入直播
	public function add_video(){
        $m_config=load_auto_cache("m_config");
	    //增加虚拟会员
        $userRobot = array();
        $userRobot['nick_name'] = $_GET['nickname'];
        $userRobot['head_image'] = $_GET['logourl'];
        $userRobot['is_admin'] = "0";
        $userRobot['mobile'] = "";
        $userRobot['province'] = "";
        $userRobot["city"] = "";
        $userRobot["sex"] = "1";
        $userRobot['user_level'] = "1";
        $userRobot['signature'] = "";
        $userRobot['is_effect'] = "1";
        $userRobot['is_ban'] = "0";
        $userRobot["ban_time"] = "";
        $userRobot["is_authentication"] = "0";
        $userRobot["authentication_type"] = "0";
        $userRobot["v_explain"] = "0";
        $userRobot['identify_positive_image'] = "";
        $userRobot["identify_nagative_image"] = "";
        $userRobot["identify_hold_image"] = "";
        $userRobot["identify_number"] = "";
        $userRobot['member_type'] = "1";
        $userRobot['is_robot'] = "1";
        $userRobot['v_icon'] = "";
        $userRobot["score"] = "10";

        $common = new UserCommon();
        filter_request($userRobot);
        $res = save_user($userRobot,'INSERT',$update_status=1);
        $user_id = intval($res['data']);

        //添加采集流
        $sql = "select id,video_type from ".DB_PREFIX."video where live_in =2 and user_id = ".$user_id;
        $video = $GLOBALS['db']->getRow($sql,true,true);
        if ($video){

            //更新心跳时间，免得被删除了
            $sql = "update ".DB_PREFIX."video set monitor_time = '".to_date(NOW_TIME,'Y-m-d H:i:s')."' where id =".$video['id'];
            $GLOBALS['db']->query($sql);

            if($GLOBALS['db']->affected_rows()){
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $this->success("加入成功");
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from ".DB_PREFIX."video where live_in =1 and user_id = ".$user_id;
        $list = $GLOBALS['db']->getAll($sql,true,true);
        foreach ( $list as $k => $v )
        {
            //结束直播
            do_end_video($v,$v['video_vid'],1,$v['cate_id']);
        }

        require_once APP_ROOT_PATH."/mapi/lib/core/common.php";
        $video_id = get_max_room_id(0);
        $data =array();
        $data['id'] = $video_id;
        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        $data['room_type'] = 3;

        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

        //图片,应该从客户端上传过来,如果没上传图片再用会员头像

        $data['head_image'] = $_GET['logourl'];
        $data['sex'] = "2";//性别 0:未知, 1-男，2-女
        $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

        if($data['video_type'] > 0){
            require_once(APP_ROOT_PATH.'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
            if ($ret['ActionStatus'] != 'OK'){
                $this->error("加入失败[".$ret['ErrorCode'].$ret['ErrorInfo']."]");
            }
            $data['group_id'] = $ret['GroupId'];
        }

        $data['monitor_time'] = to_date(1803234353,'Y-m-d H:i:s');//主播心跳监听
        $data['title'] = $_GET['nickname'];
        $data['cate_id'] = 1;
        $data['user_id'] = $user_id;
        $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = '';//'当前观看人数';
        $data['vote_number'] = '';//'获得票数';
        $data['province'] = "火星";//'省';
        $data['city'] = "火星";//'城市';
        $data['xpoint'] = "";
        $data['ypoint'] = "";
		$data['robot_num'] = rand(380,2810);
		$data['robot_time'] = NOW_TIME;
		

        $data['create_time'] = NOW_TIME;//'创建时间';
        $data['begin_time'] = NOW_TIME;//'开始时间';
        $data['end_time'] = '';//'结束时间';
        //$data['is_hot'] = 1;//'1热门; 0:非热门';
        //$data['is_new'] =1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开
        $sort_init = 0;

		$data['live_image'] = $_GET['logourl'];
        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];
        $data['prop_table'] = createPropTable();
        //直播分类
        $data['classified_id'] = 8;

		/*
		$data['is_live_pay'] = 1;
		$data['live_pay_type'] = 0;
		$data['public_screen'] = 1;
		$data['live_fee'] = 10;
		$data['live_pay_time']=intval(NOW_TIME);*/
		$data['channelid'] = "gather";
		
        $play_url = $_GET['url'];
        if(preg_match("/^rtmp:/",$play_url)){
            //RTMP格式
            $data['play_rtmp'] = $play_url;
            $wq_url = str_replace("rtmp","http",$play_url);
             $data['play_flv'] = $wq_url.'.flv';
        }
        if(preg_match("/flv/i",$play_url)){
            //FLV
            $data['play_flv'] = $play_url;
        }
        if(preg_match("/mp4$/",$play_url)){
            //MP4
            $data['play_mp4'] = $play_url;
        }
        if(preg_match("/m3u8$/",$play_url)){
            //HLS 仅此格式能播放
            $data['play_hls'] = $play_url;
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX."video", $data,'INSERT');

        if($GLOBALS['db']->affected_rows()){
            sync_video_to_redis($video_id,'*',false);
            $this->success("加入成功");
        }else{
            $this->error("加入失败");
        }
	}

public function add_videofee(){
        $m_config=load_auto_cache("m_config");
	    //增加虚拟会员
        $userRobot = array();
        $userRobot['nick_name'] = $_GET['nickname'];
        $userRobot['head_image'] = $_GET['logourl'];
        $userRobot['is_admin'] = "0";
        $userRobot['mobile'] = "";
        $userRobot['province'] = "";
        $userRobot["city"] = "";
        $userRobot["sex"] = "1";
        $userRobot['user_level'] = "1";
        $userRobot['signature'] = "";
        $userRobot['is_effect'] = "1";
        $userRobot['is_ban'] = "0";
        $userRobot["ban_time"] = "";
        $userRobot["is_authentication"] = "0";
        $userRobot["authentication_type"] = "0";
        $userRobot["v_explain"] = "0";
        $userRobot['identify_positive_image'] = "";
        $userRobot["identify_nagative_image"] = "";
        $userRobot["identify_hold_image"] = "";
        $userRobot["identify_number"] = "";
        $userRobot['member_type'] = "1";
        $userRobot['is_robot'] = "1";
        $userRobot['v_icon'] = "";
        $userRobot["score"] = "10";

        $common = new UserCommon();
        filter_request($userRobot);
        $res = save_user($userRobot,'INSERT',$update_status=1);
        $user_id = intval($res['data']);

        //添加采集流
        $sql = "select id,video_type from ".DB_PREFIX."video where live_in =2 and user_id = ".$user_id;
        $video = $GLOBALS['db']->getRow($sql,true,true);
        if ($video){

            //更新心跳时间，免得被删除了
            $sql = "update ".DB_PREFIX."video set monitor_time = '".to_date(NOW_TIME,'Y-m-d H:i:s')."' where id =".$video['id'];
            $GLOBALS['db']->query($sql);

            if($GLOBALS['db']->affected_rows()){
                //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                $this->success("加入成功");
            }
        }

        //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
        $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from ".DB_PREFIX."video where live_in =1 and user_id = ".$user_id;
        $list = $GLOBALS['db']->getAll($sql,true,true);
        foreach ( $list as $k => $v )
        {
            //结束直播
            do_end_video($v,$v['video_vid'],1,$v['cate_id']);
        }

        require_once APP_ROOT_PATH."/mapi/lib/core/common.php";
        $video_id = get_max_room_id(0);
        $data =array();
        $data['id'] = $video_id;
        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        $data['room_type'] = 3;

        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

        //图片,应该从客户端上传过来,如果没上传图片再用会员头像

        $data['head_image'] = $_GET['logourl'];
        $data['sex'] = "2";//性别 0:未知, 1-男，2-女
        $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

        if($data['video_type'] > 0){
            require_once(APP_ROOT_PATH.'system/tim/TimApi.php');
            $api = createTimAPI();
            $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
            if ($ret['ActionStatus'] != 'OK'){
                $this->error("加入失败[".$ret['ErrorCode'].$ret['ErrorInfo']."]");
            }
            $data['group_id'] = $ret['GroupId'];
        }

        $data['monitor_time'] = to_date(1803234353,'Y-m-d H:i:s');//主播心跳监听
        $data['title'] = $_GET['nickname'];
        $data['cate_id'] = 1;
        $data['user_id'] = $user_id;
        $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = '';//'当前观看人数';
        $data['vote_number'] = '';//'获得票数';
        $data['province'] = "火星";//'省';
        $data['city'] = "火星";//'城市';
        $data['xpoint'] = "";
        $data['ypoint'] = "";
		$data['robot_num'] = rand(380,2810);
		$data['robot_time'] = NOW_TIME;
		

        $data['create_time'] = NOW_TIME;//'创建时间';
        $data['begin_time'] = NOW_TIME;//'开始时间';
        $data['end_time'] = '';//'结束时间';
        //$data['is_hot'] = 1;//'1热门; 0:非热门';
        //$data['is_new'] =1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开
        $sort_init = 0;

		$data['live_image'] = $_GET['logourl'];
        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];
        $data['prop_table'] = createPropTable();
        //直播分类
        $data['classified_id'] = 8;

		
		$data['is_live_pay'] = 1;
		$data['live_pay_type'] = 0;
		$data['public_screen'] = 1;
		$data['live_fee'] = 10;
		$data['live_pay_time']=intval(NOW_TIME);
		$data['channelid'] = "gather";
		
        $play_url = $_GET['url'];
        if(preg_match("/^rtmp:/",$play_url)){
            //RTMP格式
            $data['play_rtmp'] = $play_url;
            $wq_url = str_replace("rtmp","http",$play_url);
             $data['play_flv'] = $wq_url.'.flv';
        }
        if(preg_match("/flv/i",$play_url)){
            //FLV
            $data['play_flv'] = $play_url;
        }
        if(preg_match("/mp4$/",$play_url)){
            //MP4
            $data['play_mp4'] = $play_url;
        }
        if(preg_match("/m3u8$/",$play_url)){
            //HLS 仅此格式能播放
            $data['play_hls'] = $play_url;
        }


        $GLOBALS['db']->autoExecute(DB_PREFIX."video", $data,'INSERT');

        if($GLOBALS['db']->affected_rows()){
            sync_video_to_redis($video_id,'*',false);
            $this->success("加入成功");
        }else{
            $this->error("加入失败");
        }
	}
	

    //批量循环加入直播
    public function addall_video(){

        $m_config=load_auto_cache("m_config");
        $str = file_get_contents($_GET['url']);
		
		//$start = strrpos($str, ",");
		//$str = substr_replace($str,'',$start,1);
		
		$array = json_decode($str,true);
		$array = $array['zhubo'];
		$data = array();
		
		
		
        foreach ($array as $row) {
            $newArray = [
                "nickname" => $row["title"],
                "play_url" => $row["address"],
                "logourl" =>  $row["img"],
            ];


            //增加虚拟会员
            $userRobot = array();
            $userRobot['nick_name'] = $newArray['nickname'];
            $userRobot['head_image'] = $newArray['logourl'];
            $userRobot['is_admin'] = "0";
            $userRobot['mobile'] = "";
            $userRobot['province'] = "";
            $userRobot["city"] = "";
            $userRobot["sex"] = "1";
            $userRobot['user_level'] = "1";
            $userRobot['signature'] = "";
            $userRobot['is_effect'] = "1";
            $userRobot['is_ban'] = "0";
            $userRobot["ban_time"] = "";
            $userRobot["is_authentication"] = "0";
            $userRobot["authentication_type"] = "0";
            $userRobot["v_explain"] = "0";
            $userRobot['identify_positive_image'] = "";
            $userRobot["identify_nagative_image"] = "";
            $userRobot["identify_hold_image"] = "";
            $userRobot["identify_number"] = "";
            $userRobot['member_type'] = "1";
            $userRobot['is_robot'] = "1";
            $userRobot['v_icon'] = "";
            $userRobot["score"] = "10";

            $common = new UserCommon();
            filter_request($userRobot);
            $res = save_user($userRobot, 'INSERT', $update_status = 1);
            $user_id = intval($res['data']);

            //添加采集流
            $sql = "select id,video_type from " . DB_PREFIX . "video where live_in =2 and user_id = " . $user_id;
            $video = $GLOBALS['db']->getRow($sql, true, true);
            if ($video) {

                //更新心跳时间，免得被删除了
                $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
                $GLOBALS['db']->query($sql);

                if ($GLOBALS['db']->affected_rows()) {
                    //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                    $this->success("加入成功");
                }
            }

            //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
            $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as $k => $v) {
                //结束直播
                do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
            }

            require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
            $video_id = get_max_room_id(0);
            $data = array();
            $data['id'] = $video_id;
            //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
            $data['room_type'] = 3;

            $data['virtual_number'] = intval($m_config['virtual_number']);
            $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

            //图片,应该从客户端上传过来,如果没上传图片再用会员头像

			
			$data['live_image'] = $newArray['logourl'];
			
            $data['head_image'] = $newArray['logourl'];
            $data['thumb_head_image'] = $newArray['logourl'];
            $data['sex'] = "2";//性别 0:未知, 1-男，2-女
            $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

            if ($data['video_type'] > 0) {
                require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
                if ($ret['ActionStatus'] != 'OK') {
                    $this->error("加入失败[" . $ret['ErrorCode'] . $ret['ErrorInfo'] . "]");
                }
                $data['group_id'] = $ret['GroupId'];
            }

            $data['monitor_time'] = to_date(1803234353,'Y-m-d H:i:s');//主播心跳监听
            $data['title'] = $newArray['nickname'];
            $data['cate_id'] = 1;
            $data['user_id'] = $user_id;
            $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
            $data['watch_number'] = '';//'当前观看人数';
            $data['vote_number'] = '';//'获得票数';
            $data['province'] = "火星";//'省';
            $data['city'] = "火星";//'城市';
            $data['xpoint'] = "";
            $data['ypoint'] = "";
			$data['robot_num'] = rand(380,2810);

            $data['create_time'] = NOW_TIME;//'创建时间';
            $data['begin_time'] = NOW_TIME;//'开始时间';
            $data['end_time'] = '';//'结束时间';
            $data['is_hot'] = 1;//'1热门; 0:非热门';
            $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

            $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

            //sort_init(初始排序权重) = (用户可提现印票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留印票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
            $sort_init = 0;

            $data['sort_init'] = 200000000 + $sort_init;
            $data['sort_num'] = $data['sort_init'];


            // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
            // 2、将礼物表名称写入fanwe_video 中，需新建字段
            // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
            // 4、修改所有读取礼物表的地方，匹配数据
            $data['prop_table'] = createPropTable();
            //直播分类
            $data['classified_id'] = 8;
            /*if ($user_id%2 ==0 ) {
				
				$data['is_live_pay'] = 1;
				$data['live_pay_type'] = 0;
				$data['public_screen'] = 1;
				$data['live_fee'] = rand(8,16);
				$data['live_pay_time']=intval(NOW_TIME);

            }*/

            $play_url = $newArray['play_url'];
            //判断流媒体格式
            //rtmp格式
            if (preg_match("/^rtmp:/", $play_url)) {
                //RTMP格式
                $data['play_rtmp'] = $play_url;
            }
            if (preg_match("/flv$/", $play_url)) {
                //FLV
                $data['play_flv'] = $play_url;
            }
            if (preg_match("/mp4$/", $play_url)) {
                //MP4
                $data['play_mp4'] = $play_url;
            }
            if (preg_match("/m3u8$/", $play_url)) {
                //HLS 仅此格式能播放
                $data['play_hls'] = $play_url;
            }

            $data['channelid'] = "gather";

            $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
            //$video_id =  $GLOBALS['db']->insert_id();

            if ($GLOBALS['db']->affected_rows()) {
                sync_video_to_redis($video_id, '*', false);
                //$this->success("加入成功");
            } else {
                $this->error("加入失败");
            }
            $i = $i + 1;
            //echo $i;
        }
        $this->success("批量加入成功");
    }

	//已添加的视频源
	public function add_list(){
		import('ORG.Util.Page');
		$data=M('collect_data_add')->select();
		$count = M('collect_data_add')->count();
		$Page  = new Page($count,20);
		$show       = $Page->show();
		$list = M('collect_data_add')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('data',$list);
		$this->assign('page',$show);;
		$this->display();

	}
	public function video_delete(){
		if($_GET['id']){
			if(M('collect_data_add')->delete($_GET['id'])){
			$this->success(L("删除成功"));
			}
		}
	}
	//数据添加
	public function add(){
		import('ORG.Net.UploadFile');
		if($_POST){
			$d['name']=$_POST['name'];
			$d['url']=$_POST['url'];
			$upload = new UploadFile();
			$upload->maxSize  = 3145728;
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath =  './Public/Uploads/';
			if(!$upload->upload()) {// 上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$d['img']="/Public/Uploads/".$info[0]['savename'];
				if(M()->execute("insert into fanwe_collect_data_add(name,img,url) VALUE ('$d[name]','$d[img]','$d[url]')")){
					$this->success(L("添加成功"));
				}
			}
		}
		$this->display();
	}



	public function getAction($url='')
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;

}




//最新批量采集
    //批量循环加入直播
    public function addall_newvideo(){

        $m_config=load_auto_cache("m_config");
		$url = 'http://ip.zblive.site/index.php?c=api';
        $str = file_get_contents($url);
		
		//$start = strrpos($str, ",");
		//$str = substr_replace($str,'',$start,1);
		
		$array = json_decode($str,true);
		//$array = $array['zhubo'];
		$data = array();
		
		
		
        foreach ($array as $row) {
            $newArray = [
                "nickname" => $row["name"],
                "play_url" => $row["url"],
                "logourl" =>  $row["img"],
            ];


            //增加虚拟会员
            $userRobot = array();
            $userRobot['nick_name'] = $newArray['nickname'];
            $userRobot['head_image'] = $newArray['logourl'];
            $userRobot['is_admin'] = "0";
            $userRobot['mobile'] = "";
            $userRobot['province'] = "";
            $userRobot["city"] = "";
            $userRobot["sex"] = "1";
            $userRobot['user_level'] = "1";
            $userRobot['signature'] = "";
            $userRobot['is_effect'] = "1";
            $userRobot['is_ban'] = "0";
            $userRobot["ban_time"] = "";
            $userRobot["is_authentication"] = "0";
            $userRobot["authentication_type"] = "0";
            $userRobot["v_explain"] = "0";
            $userRobot['identify_positive_image'] = "";
            $userRobot["identify_nagative_image"] = "";
            $userRobot["identify_hold_image"] = "";
            $userRobot["identify_number"] = "";
            $userRobot['member_type'] = "1";
            $userRobot['is_robot'] = "1";
            $userRobot['v_icon'] = "";
            $userRobot["score"] = "10";

            $common = new UserCommon();
            filter_request($userRobot);
            $res = save_user($userRobot, 'INSERT', $update_status = 1);
            $user_id = intval($res['data']);

            //添加采集流
            $sql = "select id,video_type from " . DB_PREFIX . "video where live_in =2 and user_id = " . $user_id;
            $video = $GLOBALS['db']->getRow($sql, true, true);
            if ($video) {

                //更新心跳时间，免得被删除了
                $sql = "update " . DB_PREFIX . "video set monitor_time = '" . to_date(NOW_TIME, 'Y-m-d H:i:s') . "' where id =" . $video['id'];
                $GLOBALS['db']->query($sql);

                if ($GLOBALS['db']->affected_rows()) {
                    //如果数据库中发现，有一个正准备执行中的，则直接返回当前这条记录;
                    $this->success("加入成功");
                }
            }

            //关闭 之前的房间,非正常结束的直播,还在通知所有人：退出房间
            $sql = "select id,user_id,watch_number,vote_number,group_id,room_type,begin_time,end_time,channelid,video_vid,cate_id from " . DB_PREFIX . "video where live_in =1 and user_id = " . $user_id;
            $list = $GLOBALS['db']->getAll($sql, true, true);
            foreach ($list as $k => $v) {
                //结束直播
                do_end_video($v, $v['video_vid'], 1, $v['cate_id']);
            }

            require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
            $video_id = get_max_room_id(0);
            $data = array();
            $data['id'] = $video_id;
            //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
            $data['room_type'] = 3;

            $data['virtual_number'] = intval($m_config['virtual_number']);
            $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

            //图片,应该从客户端上传过来,如果没上传图片再用会员头像

            $data['head_image'] = $newArray['logourl'];
            $data['thumb_head_image'] = $newArray['logourl'];
            $data['sex'] = "2";//性别 0:未知, 1-男，2-女
            $data['video_type'] = intval($m_config['video_type']);//0:腾讯云互动直播;1:腾讯云直播

            if ($data['video_type'] > 0) {
                require_once(APP_ROOT_PATH . 'system/tim/TimApi.php');
                $api = createTimAPI();
                $ret = $api->group_create_group('AVChatRoom', (string)$user_id, (string)$user_id, (string)$video_id);
                if ($ret['ActionStatus'] != 'OK') {
                    $this->error("加入失败[" . $ret['ErrorCode'] . $ret['ErrorInfo'] . "]");
                }
                $data['group_id'] = $ret['GroupId'];
            }

            $data['monitor_time'] = to_date(1803234353,'Y-m-d H:i:s');//主播心跳监听
            $data['title'] = $newArray['nickname'];
            $data['cate_id'] = 1;
            $data['user_id'] = $user_id;
            $data['live_in'] = 1;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
            $data['watch_number'] = '';//'当前观看人数';
            $data['vote_number'] = '';//'获得票数';
            $data['province'] = "火星";//'省';
            $data['city'] = "火星";//'城市';
            $data['xpoint'] = "";
            $data['ypoint'] = "";
			$data['robot_num'] = rand(380,2810);

            $data['create_time'] = NOW_TIME;//'创建时间';
            $data['begin_time'] = NOW_TIME;//'开始时间';
            $data['end_time'] = '';//'结束时间';
            $data['is_hot'] = 1;//'1热门; 0:非热门';
            $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

            $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

            //sort_init(初始排序权重) = (用户可提现印票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留印票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
            $sort_init = 0;

            $data['sort_init'] = 200000000 + $sort_init;
            $data['sort_num'] = $data['sort_init'];


            // 1、创建视频时检查表是否存在，如不存在创建礼物表，表命名格式 fanwe_ video_ prop_201611、格式同fanwe_ video_ prop相同
            // 2、将礼物表名称写入fanwe_video 中，需新建字段
            // 3、记录礼物发送时候读取fanwe_video 的礼物表名，写入对应的礼物表
            // 4、修改所有读取礼物表的地方，匹配数据
            $data['prop_table'] = createPropTable();
            //直播分类
            $data['classified_id'] = 8;
            /*if ($user_id%2==0) {
                $data['is_live_pay'] = 1;
                $data['live_pay_type'] = 0;
                $data['public_screen'] = 1;
                $data['live_fee'] = rand(6,20);
                $data['live_pay_time'] = intval(NOW_TIME);
            }*/

            $play_url = $newArray['play_url'];
            //判断流媒体格式
            //rtmp格式
            if (preg_match("/^rtmp:/", $play_url)) {
                //RTMP格式
                $data['play_rtmp'] = $play_url;
            }
            if (preg_match("/flv$/", $play_url)) {
                //FLV
                $data['play_flv'] = $play_url;
            }
            if (preg_match("/mp4$/", $play_url)) {
                //MP4
                $data['play_mp4'] = $play_url;
            }
            if (preg_match("/m3u8$/", $play_url)) {
                //HLS 仅此格式能播放
                $data['play_hls'] = $play_url;
            }

            $data['channelid'] = "gather";

            $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
            //$video_id =  $GLOBALS['db']->insert_id();

            if ($GLOBALS['db']->affected_rows()) {
                sync_video_to_redis($video_id, '*', false);
                //$this->success("加入成功");
            } else {
                $this->error("加入失败");
            }
            $i = $i + 1;
            //echo $i;
        }
        $this->success("批量加入成功");
    }








}



?>