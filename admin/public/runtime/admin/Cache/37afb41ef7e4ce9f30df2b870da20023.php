<?php if (!defined('THINK_PATH')) exit();?>

<!DOCTYPE html>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<script type="text/javascript" src="__TMPL__Common/js/check_dog.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/IA300ClientJavascript.js"></script>
<script type="text/javascript">
	var ACTION_ID ='<?php echo $action_id ?>';
 	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var MODULE_NAME	=	'<?php echo MODULE_NAME; ?>';
	var ACTION_NAME	=	'<?php echo ACTION_NAME; ?>';
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
	var CURRENT_URL = '<?php echo trim($_SERVER['REQUEST_URI']);?>';
	var INPUT_KEY_PLEASE = "<?php echo L("INPUT_KEY_PLEASE");?>";
	var TMPL = '__TMPL__';
	var APP_ROOT = '<?php echo APP_ROOT; ?>';
	var LOGINOUT_URL = '<?php echo u("Public/do_loginout");?>';
	var WEB_SESSION_ID = '<?php echo es_session::id(); ?>';
	var EMOT_URL = '<?php echo APP_ROOT; ?>/public/emoticons/';
	var MAX_FILE_SIZE = "<?php echo (app_conf("MAX_IMAGE_SIZE")/1000000)."MB"; ?>";
	var FILE_UPLOAD_URL ='<?php echo u("File/do_upload");?>' ;
	CHECK_DOG_HASH = '<?php $adm_session = es_session::get(md5(conf("AUTH_KEY"))); echo $adm_session["adm_dog_key"]; ?>';
	function check_dog_sender_fun()
	{
		window.clearInterval(check_dog_sender);
		check_dog2();
	}
	var check_dog_sender = window.setInterval("check_dog_sender_fun()",5000);
	
</script>
	<script type="text/javascript" src="__TMPL__Common/js/cropper/jquery.min.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>

<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.weebox.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/cropper/cropper.min.css" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/cropper/main.css" />
<script type="text/javascript" src="__TMPL__Common/js/cropper/cropper.min.js"></script>

<script type="text/javascript" src="__TMPL__Common/js/script.js"></script>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/kindeditor.js'></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/lang/zh_CN.js'></script>


</head>
<body onLoad="javascript:DogPageLoad();">
<div id="info"></div>

<?php function time_length($create_time){
        $length = get_gmtime()-$create_time;
        $hour = '0:';
        $minute = '0:';
        $second = '0';
        if($length/3600>1){
            $hour = intval($length/3600).':';
            $length = $length%3600;
        }
        if($length/60>1){
            $minute = intval($length/60).':';
            $length = $length%60;
        }
        if($length>0){
            $second = $length;
        }
        return $hour.$minute.$second;
    }

    function get_nickname($id){
        $get_nickname=$GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$id);
        return emoji_decode($get_nickname);
    } ?>
<script src="//imgcache.qq.com/open/qcloud/video/vcplayer/TcPlayer.js" charset="utf-8"></script>
<script>
    function close_live($user_id,$room_id){
        var r=confirm("确定关闭这个直播？？");
        if (r==true){
            $.ajax({
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=close_live&user_id="+$user_id+"&room_id="+$room_id,
                data: "",
                dataType: "json",
                success: function(obj){
                    $("#info").html(obj.info);
                    if(obj.status==1){
                        if(obj.info){
                            alert(obj.info);
                            window.location.reload();
                        }
                        else{
                            alert('操作成功');
                            window.location.reload();
                        }
                    }
                    else{
                        if(obj.info){
                            alert(obj.info);
                            window.location.reload();
                        }
                        else{
                            alert('操作成功');
                            window.location.reload();
                        }
                    }
                }
            });
        }else{

        }
    }
    function set_ban($id){
        var r=confirm("确定禁播这个用户？");
        if (r==true){
            $.ajax({
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_ban&id="+$id,
                data: "",
                dataType: "json",
                success: function(obj){
                    $("#info").html(obj.info);
                    if(obj.status==1){
                        alert('操作成功');
                    }else{
                        alert('操作失败');
                    }

                }
            });
        }
    }

    function send_warning(room_id)
    {
        $.ajax({
            url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=send_warning&room_id="+room_id,
            data: "ajax=1",
            dataType: "json",
            success: function(msg){
                if(msg.status==0){
                    alert(msg.info);
                }
            },
            error: function(){
                $.weeboxs.open(ROOT+'?'+VAR_MODULE+'='+MODULE_NAME+'&'+VAR_ACTION+'=send_warning&room_id='+room_id, {contentType:'ajax',showButton:false,title:'发送警告',width:700,height:250});
            }
        });

    }

    function close_ban_forbid(user_id,room_id) {
        if(confirm("确定关闭这个直播并禁播、im全局禁言这个用户？")){
            $.ajax({
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=close_ban_forbid&user_id="+user_id+"&room_id="+room_id,
                data: "",
                dataType: "json",
                success: function(obj){
                    $("#info").html(obj.info);
                    if(obj.status==1){
                        alert(obj.info);
                        window.location.reload();
                    }else{
                        alert(obj.info);
                        window.location.reload();
                    }
                }
            });
        }
    }

</script>

<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/live_connect.js"></script>
<div class="main">
    <div class="main_title_list"><div class="list-line-ico"></div>监控   
	<a href="javascript:sortBy('tipoff_count','0','VideoMonitor','monitor')" title="按照举报次数    降序排列 ">举报次数<img src="/admin/Tpl/default/Common/images/asc.gif" width="12" height="17" border="0" align="absmiddle"></a>  
	<a href="javascript:sortBy('watch_number','0','VideoMonitor','monitor')" title="按照观看人数    降序排列 ">观看人数<img src="/admin/Tpl/default/Common/images/asc.gif" width="12" height="17" border="0" align="absmiddle"></a>
    <a href="/<?php echo ($url_name); ?>?m=VideoMonitor&a=monitor&">刷新</a></div>
    <style>
        .clear{
            display: block;
            width: 100%;
            clear: both;
        }
        .js-ajax-form li {
            list-style:none;
            width:290px;
            min-height:333px;
            border: 1px solid #C2D1D8;
            float:left;
            margin:10px;
        }
        .js-ajax-form .lf{
            width: 120px;;
            float: left;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .js-ajax-form .rm{
            float: left;
        }
        .js-ajax-form .rmt{
            float: right;
            padding-top: 3px;
            margin-left: 10px;
        }
        .js-ajax-form .rt{
            float: right;
        }
        .js-ajax-form  .tit{
            height: 40px ;
            line-height: 40px;
            padding: 0 10px;
        }
        .js-ajax-form  .info{
            padding: 0 10px;
            line-height: 25px;
        }
        .search_row .js-ajax-form span .button{
            position: relative;
        }
        .js-ajax-form span em{
            font-style: normal;
            color: #222;
            padding-right: 3px;
        }
        .js-ajax-form span i{
            font-style: normal;
            color:#ff9600;
            font-size: 16px;
            line-height: 37px;
        }
        .alink{
            color: #5775b5;
            font-size: 16px;
            line-height: 37px;

        }
        .blank{
            display: block;
            clear: both;
        }
        .videobox object{
            float: left;
        }
        .videobox:after{
            content: "";
            display: block;
            clear: both;
        }
        .sp{
            float: right;
            bottom: 35px;
        }
    </style>
    <script>
        var video_num = 0;
    </script>
    <div class="search_row">
    <form class="js-ajax-form">
        <ul style="clear: both;overflow: hidden;">
            <input type="hidden" id="app_id" value="<?php echo ($app_id); ?>"/>

            <?php if(is_array($list)): foreach($list as $key=>$video_item): ?><?php if($video_item["channelid"] != ''): ?><li>
                <div class="tit">
                    <span class="lf"><em>房间号:</em> <i> <?php echo ($video_item["id"]); ?></i></span>
                    <span class="rm"><em>举报次数:</em><a class="alink" href="/<?php echo ($url_name); ?>?m=Tipoff&a=index&video_id=<?php echo ($video_item["id"]); ?>"><?php echo ($video_item["tipoff_count"]); ?></a></span>
                    <span class="rmt"><input type="button" style="height:32px;" class="button button-del" value="警告" onclick="send_warning('<?php echo ($video_item["id"]); ?>');" /></span>
                    <div class="clear"></div>
                </div>
                    <div style="width:29ppx; height:210px;">
                        <div id="video_container_<?php echo ($key); ?>" class="videobox" style="width: 100%;height: 1px;"></div>
                    </div>
                    <div class="blank"></div>
                    <input type="hidden" id="video_<?php echo ($key); ?>" value="<?php echo ($video_item["channelid"]); ?>"/>
                    <input type="hidden" id="video_type_<?php echo ($key); ?>" value="<?php echo ($video_item["video_type"]); ?>"/>
                    <input type="hidden" id="live_url_<?php echo ($key); ?>" value="<?php echo ($video_item["play_hls"]); ?>"/>
                    <input type="hidden" id="live_url2_<?php echo ($key); ?>" value="<?php echo ($video_item["play_flv"]); ?>"/>
						<script>
                        var video_type = $("#video_type_"+video_num).val();
                        var live_url = $("#live_url_"+video_num).val();
                        var live_url2 = $("#live_url2_"+video_num).val();

                        var player =  new TcPlayer("video_container_"+video_num, {
                            "m3u8": live_url,
                            "flv": live_url2, //增加了一个flv的播放地址，用于PC平台的播放 请替换成实际可用的播放地址
                            "autoplay" : true,      //iOS下safari浏览器，以及大部分移动端浏览器是不开放视频自动播放这个能力的
                            "width" :  '290',//视频的显示宽度，请尽量使用视频分辨率宽度
                            "height" : '210'//视频的显示高度，请尽量使用视频分辨率高度
                        });
                        video_num ++;
                    	</script>
					
                    <div class="info">
                        
                    
                        <div class="detail">
                            <span class="lf"><em>主播:</em> <?php echo (get_nickname($video_item["user_id"])); ?></span>
                            <span class="rt"><em>累计观看人数:</em><?php echo ($video_item["watch_number"]); ?></span>
                            <span class="lf"><em>主播ID:</em><?php echo ($video_item["user_id"]); ?></span>
                            <div class="clear"></div>
                        </div>
                        <div class="caozuo">
                            <span class="lf"><em>开播时长:</em><?php echo (time_length($video_item["create_time"])); ?></span>
                            <div class="clear"></div>
                        </div>
                        <span class="sp" ><input type="button" class="button button-del" value="关闭" onclick="close_live('<?php echo ($video_item["user_id"]); ?>','<?php echo ($video_item["id"]); ?>');" /></span>
                        <span class="sp"><input type="button" class="button button-del" value="禁播" onclick="set_ban('<?php echo ($video_item["user_id"]); ?>');" /></span>
                        <span class="sp" ><input type="button" class="button button-del" value="关闭并禁播禁言" style="width: 125px;" onclick="close_ban_forbid('<?php echo ($video_item["user_id"]); ?>','<?php echo ($video_item["id"]); ?>');" /></span>
                    </div>
                </li><?php endif; ?><?php endforeach; endif; ?>
        </ul>
    </form>
    </div>
    <div class="page"><?php echo ($page); ?></div><!--(数值越大在app热门直播中越靠前)  ,vote_number:<?php echo L("TICKET");?>-->
</div>
</body>
</html>