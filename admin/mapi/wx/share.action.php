<?php

class shareCModule extends baseCModule
{

    var $signPackage = '';
    var $user_info = '';
    var $wx_url = '';
    var $video_id = '';
    var $user_id = '';
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if (!$user_id) {
            api_ajax_return(array(
                'status' => 0,
                'error'  => '未登录',
            ));
        }
        return $user_id;
    }

    public function list_handle($list)
    {
        foreach ($list as $k => &$v) {
            //截取字符串
            if (strlen($v['title']) > 30) {
                $v['title'] = mb_substr($v['title'], 0, 30, "utf-8") . "...";
            }
            if (strlen($v['content']) > 90) {
                $v['content'] = mb_substr($v['content'], 0, 90, "utf-8") . "...";
            }

            //转换时间
            $time = NOW_TIME;
            $sub  = $time - $v['create_time'];
            if ($sub < 3600) {
                $v['create_time'] = floor($sub / 60) . "分钟前";
            } elseif ($sub < 86400) {
                $v['create_time'] = floor($sub / 3600) . "小时前";
            } elseif ($sub < 604800) {
                $v['create_time'] = floor($sub / 86400) . "天前";
            } else {
                $v['create_time'] = date('Y-m-d', $v['create_time']);
            }

            //图片处理,json转数组,并拼接为对应的oss地址
            
            if ($v['imgs'] != '') {
                $v['imgs'] = json_decode($v['imgs'],1);
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

    public function row_handle(&$data)
    {
        //转换时间
        $time = NOW_TIME;
        $sub  = $time - $data['create_time'];
        if ($sub < 3600) {
            $data['create_time'] = floor($sub / 60) . "分钟前";
        } elseif ($sub < 86400) {
            $data['create_time'] = floor($sub / 3600) . "小时前";
        } elseif ($sub < 604800) {
            $data['create_time'] = floor($sub / 86400) . "天前";
        } else {
            $data['create_time'] = date('Y-m-d', $data['create_time']);
        }

        //图片处理,json转数组,并拼接为对应的oss地址
        if ($data['imgs'] != '') {
            $data['imgs'] = json_decode($data['imgs']);
            if ($data['imgs'] == "") {
                $data['imgs'] = array();
            } else {
                foreach ($data['imgs'] as $kk => $vv) {
                    //$goods['imgs'][$k] = get_domain() . APP_ROOT . $v;
                    $data['imgs'][$kk] = get_spec_image($vv);
                }
            }
        } else {
            $data['imgs'] = array();
        }

        return $data;
    }

    public function index()
    {
        $user_id   = self::getUserId();//
        //用户是否是群星分享作者
        $sql_is_author = "select is_star_share from ".DB_PREFIX."user where id=".$user_id;
        $is_star_share = intval($GLOBALS['db']->getOne($sql_is_author));

        $cate_id   = intval($_REQUEST['cate_id']);
        $page      = intval($_REQUEST['page']);
        $page_size = intval($_REQUEST['page_size']);
        $page      = $page ? $page : 1;
        $page_size = $page_size ? $page_size : 20;
        //获取话题列表
        $model     = Model::build("share_cate");
        $cate_list = $model->field("id,cate_name")->select();

        //获取分享列表以及作者相关信息
        $table = DB_PREFIX.'share as s';
        $table1 = "share s";
        $field = 's.id,s.title,s.content,s.create_time,s.cate_id,s.watch_count,s.praise_count,s.reply_count,s.imgs,
                 u.nick_name,u.signature,u.head_image,u.is_authentication,u.v_type,u.v_icon,u.v_explain,count(p.id) as is_praised';
        $left_join1 = DB_PREFIX."user as u on s.author_id=u.id";
        $left_join2 = DB_PREFIX."share_praise as p on (p.share_id=s.id and p.user_id=".$user_id.")";
//        $where = array('s.author_id' => array('u.id'));
        $where1 = array('s.audit_status' => 1);
        if ($cate_id) {
            $where1['s.cate_id'] = $cate_id;
        }


        $count      = $model->table($table1)->field(array(array('count(1) count')))->where($where1)->selectOne();
        $count      = $count ? $count['count'] : 0;
        $total_page = ceil($count / $page_size);
        $list       = array();
        $start      = ($page - 1) * $page_size;
        $end        = $page_size;

        if ($count) {
//            $list = $model->table($table)->field($field)->limit(array(($page - 1) * $page_size, $page_size))->select($where);
            $where2 = "s.audit_status=1";
            if ($cate_id) {
                $where2 .= " and s.cate_id=".$cate_id;
            }
            $sql = "select $field from $table left join $left_join1 left join $left_join2 where $where2 group by s.id order by s.id desc limit $start,$end ";
            $list  = $GLOBALS['db']->getAll($sql);
            foreach ($list as $k => $v) {
            	$list[$k]['head_image']= get_spec_image($list[$k]['head_image']);
                $list[$k]['is_praised'] = intval($v['is_praised']);
                $list[$k]['praise_count'] = intval($v['praise_count']);
                $list[$k]['reply_count'] = intval($v['reply_count']);
                $list[$k]['detail_url'] = APP_ROOT.'/weixin/index.php?ctl=share&act=detail&id='.$v['id'];
            	//$list[$k]['imgs']= get_spec_image($user['imgs']);
                //获取文章点赞列表
                $table = 'share_praise s,user u';
                $field = 's.user_id,u.nick_name';
                $where = array('s.user_id' => array('u.id'), 's.share_id' => $v['id']);
                $list[$k]['praise_list'] = $model->table($table)->field($field)->order('s.id desc')->limit(5)->select($where);
                //获取文章评论列表
                $field = 's.id,s.user_id,s.content,s.reply_user_id,u.nick_name,u2.nick_name reply_user_name';
                $table = array(
                    'share_reply s' => array(
                        'right join',
                        'user u2',
                        array('s.reply_user_id' => array('u2.id')),
                    ),
                    'user u',
                );
                $where = array('s.user_id' => array('u.id'), 's.share_id' => $v['id']);
                $list[$k]['reply_list'] = $model->table($table)->field($field)->order('s.create_time')->limit(5)->select($where);
            }
            //对得到的分享列表做一些处理
            $list = $this->list_handle($list);
        }
        $status = 1;
        $error = '';
        $page_title = '实战分享';
        api_ajax_return(compact('status', 'error', 'cate_id', 'cate_list', 'list', 'total_page','is_star_share','page_title'));
    }

    //分享详情
    public function detail()
    {
        $id   = intval($_REQUEST['id']);
        $root = array('status' => 1,'page_title'=>'分享详情');
        $user_id = self::getUserId();

        if ($id == 0) {
            $root['status'] = 0;
            $root['error']  = 'id不能为空';
            api_ajax_return($root);
        }

        //获取分享列表以及作者相关信息
        $sql = "select s.id,s.title,s.content,s.create_time,s.cate_id,s.watch_count,s.praise_count,s.reply_count,s.imgs,u.id as user_id,
                u.nick_name,u.signature,u.head_image,u.is_authentication,u.v_type,u.v_icon,u.v_explain,s.audit_status from " . DB_PREFIX .
            "share as s left join " . DB_PREFIX . "user as u on s.author_id=u.id where s.id=" . $id;
        $data = $GLOBALS['db']->getRow($sql);
        $data['head_image']   = get_spec_image($data['head_image']);
        $data['praise_count'] = intval($data['praise_count']);
        $data['reply_count'] = intval($data['reply_count']);

        $sql_is_praised = "select id from ".DB_PREFIX."share_praise where share_id=$id and user_id=$user_id";
        $is_praised = $GLOBALS['db']->getOne($sql_is_praised);
        $data['is_praised'] = $is_praised ? 1 : 0;

        //获取文章点赞列表
        $sql_praise = "select s.user_id,u.nick_name from " . DB_PREFIX . "share_praise as s left join " . DB_PREFIX . "user
                        as u on s.user_id=u.id where s.share_id=" . $id . " order by s.id desc limit 5 ";
        $data['praise_list'] = $GLOBALS['db']->getAll($sql_praise);
        $data['praise_list'] = $data['praise_list'] ? $data['praise_list'] : array();
        //获取文章评论列表
        $sql_reply = "select s.id,s.user_id,s.content,u.nick_name,s.reply_user_id,u2.nick_name as reply_user_name from " . DB_PREFIX . "share_reply
                        as s left join " . DB_PREFIX . "user as u on s.user_id=u.id left join " . DB_PREFIX . "user as u2
                      on s.reply_user_id=u2.id where s.share_id=" . $id . " order by s.create_time";
        $data['reply_list'] = $GLOBALS['db']->getAll($sql_reply);
        $data['reply_list'] = $data['reply_list'] ? $data['reply_list'] : array();

        $sql_add = "update ".DB_PREFIX."share set watch_count=watch_count+1 where id=".$id;//阅读数加1
        $GLOBALS['db']->query($sql_add);

        //对得到的分享列表做一些处理
        $data           = $this->row_handle($data);
        $root['data']   = $data;
        $root['status'] = 1;
        api_ajax_return($root);
    }

    //添加分享页
    public function add_share()
    {
        $sql = "select id,cate_name from ".DB_PREFIX."share_cate where is_effect=1 and is_delete=0";
        $cate_list = $GLOBALS['db']->getAll($sql);

        $root = array();
        $root['status'] = 1;
        $root['cate_list'] = $cate_list;
        $root['page_title'] = '添加分享';
        api_ajax_return($root);
    }

    //添加分享
    public function add()
    {
        $root    = array();
        $user_id = self::getUserId();

        $data                = array();
        $data['author_id']   = $user_id;
        $data['content']     = filterEmoji(strim($_REQUEST['content']));
        $data['title']       = filterEmoji(strim($_REQUEST['title']));
        $data['cate_id']     = intval($_REQUEST['cate_id']);
        $data['create_time'] = NOW_TIME;

        if ($data['content'] == '' || $data['title'] == '') {
            $root['status'] = 0;
            $root['error']  = '文章标题或内容不能为空';
        }

        //处理上传的图片
        if (trim($_REQUEST['imgs']) != '') {
            $imgs        = json_decode(htmlspecialchars_decode($_REQUEST['imgs']));
            $result_imgs = array();
            foreach ($imgs as $k => $v) {
                $result_imgs[] = str_replace(get_domain() . APP_ROOT, '', $v);
            }
            $data['imgs'] = json_encode($result_imgs);
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "share", $data, "INSERT");
        $sql_new = "select max(id) from ".DB_PREFIX."share";
        $new_id = $GLOBALS['db']->getOne($sql_new);

        $root['id'] = $new_id;
        $root['status'] = 1;
        api_ajax_return($root);
    }

    //删除分享
    public function delete()
    {
        $root    = array('status' => 1);
        $user_id = self::getUserId();
        $id      = intval($_REQUEST['id']);
        $sql     = "DELETE FROM " . DB_PREFIX . "share WHERE id=" . $id ." and author_id=".$user_id;//删除分享
        $sql_reply = "DELETE FROM ". DB_PREFIX . "share_reply where share_id=" . $id;//删除对应评论
        $sql_praise = "DELETE FROM ". DB_PREFIX . "share_praise where share_id=" . $id;//删除对应点赞
        $res = $GLOBALS['db']->query($sql);
        if (!$res){
            $root['status'] = 0;
            $root['error'] = '删除失败';
            api_ajax_return($root);
        }
        $GLOBALS['db']->query($sql_reply);
        $GLOBALS['db']->query($sql_praise);

        api_ajax_return($root);
    }

    //点赞
    public function praise()
    {
        $root = array();
        $user_id = self::getUserId();
        $id = intval($_REQUEST['id']); //点赞的分享id

        //查询是否已经点赞过了
        $sql_get   = "select id from " . DB_PREFIX . "share_praise where user_id=" . $user_id . " and share_id=" . $id;
        $praise_id = $GLOBALS['db']->getOne($sql_get);

        if (intval($praise_id) > 0) {
            //如果已经赞过了，就取消点赞，从点赞表中删除
            $sql_del = "delete from " . DB_PREFIX . "share_praise where id=" . $praise_id;
            $GLOBALS['db']->query($sql_del);
            //问题点赞数减1
            $sql_sub = "update " . DB_PREFIX . "share set praise_count=IF(praise_count<1,0,praise_count-1) where id=" . $id;
            $GLOBALS['db']->query($sql_sub);
            $root['is_praise'] = 0; //点赞状态改为未点赞
            $root['error'] = "已取消点赞"; //点赞状态改为未点赞
        } else {
            //如果没赞过，则加入点赞表
            $data['user_id']  = $user_id;
            $data['share_id'] = $id;
            $GLOBALS['db']->autoExecute(DB_PREFIX . share_praise, $data, 'INSERT');
            $sql_add = "update " . DB_PREFIX . "share set praise_count=praise_count+1 where id=" . $id;
            $GLOBALS['db']->query($sql_add);
            $root['is_praise'] = 1; //点赞状态改为已点赞
            $root['error'] = "已点赞"; //点赞状态改为已点赞
        }

        $root['status'] = 1;
        api_ajax_return($root);
    }

    //回复
    public function reply()
    {
        $user_id = self::getUserId();
        $root    = array('status' => 1);
        $id      = intval($_REQUEST['id']); //回复的分享id
        $content = strim($_REQUEST['content']);
        if ($id == 0) {
            $root['status'] = 0;
            $root['error']  = "分享id不能为空";
            api_ajax_return($root);
        }
        if ($content == '') {
            $root['status'] = 0;
            $root['error']  = "回复内容不能为空";
            api_ajax_return($root);
        }

        $reply_user_id = intval($_REQUEST['reply_user_id']); //回复的用户id
        $data['reply_user_id'] = $reply_user_id ? $reply_user_id : 0;

        $data['user_id']     = $user_id;
        $data['share_id']    = $id;
        $data['content']     = $content;
        $data['create_time'] = NOW_TIME;
        $GLOBALS['db']->autoExecute(DB_PREFIX . share_reply, $data, 'INSERT');
        $root['id'] = intval($GLOBALS['db']->insert_id());
        $sql_add = "update " . DB_PREFIX . "share set reply_count=reply_count+1 where id=" . $id;
        $GLOBALS['db']->query($sql_add);

        api_ajax_return($root);
    }

    public function delete_reply()
    {
        $root    = array('status' => 1);
        $user_id = self::getUserId();
        $id      = intval($_REQUEST['id']);
        $share_id = $GLOBALS['db']->getOne("select share_id from ".DB_PREFIX."share_reply where id=$id");
        $sql     = "DELETE FROM " . DB_PREFIX . "share_reply WHERE id=" . $id;
        $GLOBALS['db']->query($sql);
        $sql_del = "update " . DB_PREFIX . "share set reply_count=reply_count-1 where id=" . $share_id;
        $GLOBALS['db']->query($sql_del);

        api_ajax_return($root);
    }

    //新分享首页
    public function live()
    {
        $video_id  =  intval($_REQUEST['video_id']);
        $user_id = intval($_REQUEST['user_id']);
        $share_id = intval($_REQUEST['share_id']);
        $code = strim($_REQUEST['code']);

        $call_back = SITE_DOMAIN.'/weixin/index.php?ctl=share&act=live&user_id='.$user_id.'&video_id='.$video_id.'&share_id='.$share_id;
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
        if((defined('OPEN_DISTRIBUTION')&&OPEN_DISTRIBUTION==1)&&intval($m_config['distribution'])==1){
            $root['register_url'] = SITE_DOMAIN.'/weixin/index.php?ctl=distribution&act=init_register&user_id='.$share_id;
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
}
