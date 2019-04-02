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

<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.weebox.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/user.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript">
	function show_content(id)
	{

		$.ajax({
			url: ROOT+'?m=DealMsgList&a=show_content&id='+id,
			data: "ajax=1",
			dataType: "json",
			success: function(msg){
				if(msg.status==0){
					alert(msg.info);
				}
			},
			error: function(){
				$.weeboxs.open(ROOT+'?m=DealMsgList&a=show_content&id='+id, {contentType:'ajax',showButton:false,title:LANG['SHOW_CONTENT'],width:600});
			}
		});

	}
	function send(id)
	{
		$.ajax({
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=send&id="+id,
				data: "ajax=1",
				success: function(msg){
					alert(msg);
				}
		});
	}
</script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div><?php echo ($main_title); ?></div>
<div class="button_row">

</div>
<div class="search_row">
	<form name="search" action="__APP__" method="get">
		<?php echo L("SEND_DEST");?>：<input type="text" class="textbox" name="dest" value="<?php echo trim($_REQUEST['dest']);?>" style="width:100px;" /><?php echo L("CONTENT");?>：<input type="text" style="margin:0" class="textbox" name="content" value="<?php echo trim($_REQUEST['content']);?>" /><input type="hidden" value="DealMsgList" name="m" /><input type="hidden" value="index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
	</form>
</div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="14" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="90px  "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="80px  "><a href="javascript:sortBy('send_type','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_TYPE");?><?php echo ($sortType); ?> "><?php echo L("SEND_TYPE");?><?php if(($order)  ==  "send_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="120px  "><a href="javascript:sortBy('dest','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_DEST");?><?php echo ($sortType); ?> "><?php echo L("SEND_DEST");?><?php if(($order)  ==  "dest"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px  "><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','DealMsgList','index')" title="按照会员ID<?php echo ($sortType); ?> ">会员ID<?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px  "><a href="javascript:sortBy('title','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("TITLE");?><?php echo ($sortType); ?> "><?php echo L("TITLE");?><?php if(($order)  ==  "title"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="80px  "><a href="javascript:sortBy('content','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("CONTENT");?><?php echo ($sortType); ?> "><?php echo L("CONTENT");?><?php if(($order)  ==  "content"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="80px  "><a href="javascript:sortBy('code','<?php echo ($sort); ?>','DealMsgList','index')" title="按照验证码<?php echo ($sortType); ?> ">验证码<?php if(($order)  ==  "code"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="180px  "><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("CREATE_TIME");?><?php echo ($sortType); ?> "><?php echo L("CREATE_TIME");?><?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="180px  "><a href="javascript:sortBy('send_time','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_TIME");?><?php echo ($sortType); ?> "><?php echo L("SEND_TIME");?><?php if(($order)  ==  "send_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px  "><a href="javascript:sortBy('is_send','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_STATUS");?><?php echo ($sortType); ?> "><?php echo L("SEND_STATUS");?><?php if(($order)  ==  "is_send"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px  "><a href="javascript:sortBy('is_success','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_RESULT");?><?php echo ($sortType); ?> "><?php echo L("SEND_RESULT");?><?php if(($order)  ==  "is_success"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="100px"><a href="javascript:sortBy('result','<?php echo ($sort); ?>','DealMsgList','index')" title="按照<?php echo L("SEND_INFO");?><?php echo ($sortType); ?> "><?php echo L("SEND_INFO");?><?php if(($order)  ==  "result"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$msg): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($msg["id"]); ?>"></td><td><?php echo ($msg["id"]); ?></td><td><?php echo (get_send_type_msg($msg["send_type"])); ?></td><td><?php echo ($msg["dest"]); ?></td><td><?php echo ($msg["user_id"]); ?></td><td><?php echo ($msg["title"]); ?></td><td><?php echo (show_content($msg["content"],$msg['id'])); ?></td><td><?php echo ($msg["code"]); ?></td><td><?php echo (to_date($msg["create_time"])); ?></td><td><?php echo (to_date($msg["send_time"])); ?></td><td><?php echo (get_is_send($msg["is_send"])); ?></td><td><?php echo (get_send_result($msg["is_success"])); ?></td><td><?php echo ($msg["result"]); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:send('<?php echo ($msg["id"]); ?>')"><?php echo L("SEND_NOW");?></a>&nbsp;<a href="javascript:foreverdel('<?php echo ($msg["id"]); ?>')"><?php echo L("FOREVERDEL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="14" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<table class="dataTable">
		<tbody>
			<td colspan="14">
				<input type="button" class="button button-add" value="<?php echo L("FOREVERDEL");?>" onclick="foreverdel();" />
			</td>
		</tbody>
	</table>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>