<?php if (!defined('THINK_PATH')) exit();?>﻿
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

<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>采集列表<a href="<?php echo U('VideoCollectnew/guanggao');?>">加入广告</a>  <a href="<?php echo U('VideoCollectnew/addroom');?>">新建房间</a>   </div>
    <div class="search_row" style="display:none;">
        <a href="<?php echo U('VideoCollect/add');?>">
        <button  class="button"  style="margin-bottom: 5px">
            添加视频
        </button></a>
        <a href="<?php echo U('VideoCollect/add_list');?>">
            <button  class="button"  style="margin-bottom: 5px">
                查看已添加的视频
            </button></a>

        <!--<a href="<?php echo U('videocollect/collect',array('code'=>'ok'));?>"  title="危险"  ><button class="button" style="margin-bottom: 5px;float:right">接口数据重写入数据库</button></a>-->
    </div>

	<table class="dataTable" >
		<tbody>
			<tr>
                <td>ID</td>
                <td>LOGO</td>
                <td>名称</td>
                <td>URL</td>
                <td>操作</td>
            </tr>
            <?php foreach($data as $k=>$v){ ?>
            <tr>
                <td><?php echo $k+1 ?></td>
                <td><img src="<?php echo $v['img'] ?>"  width="40" height="40" onerror="this.src='/public/images/live_img12.jpg'"/></td>
                <td><?php echo $v['name'] ?></td>
                <td><?php echo $v['url'] ?></td>
                <td>
                    <a href="<?php echo U('VideoCollectnew/two',array('url'=>$v['url']));?>" class="button" style="text-decoration:none;">查看二级</a>
                    <a href="<?php echo U('VideoCollectnew/addall_video',array('url'=>$v['url']));?>" class="button" style="text-decoration:none;margin-left: 10px;">批量加入直播</a>
                </td>
            </tr>
        <?php }?>


        
		</tbody>

	</table>
</div>

</body>
</html>