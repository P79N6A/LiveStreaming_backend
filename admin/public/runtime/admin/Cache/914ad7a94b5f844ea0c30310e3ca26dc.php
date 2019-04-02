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

<script type="text/javascript">
	function uninstall(id)
	{
		if(confirm("<?php echo L("CONFIRM_DELETE");?>"))
		{
			location.href = ROOT + "?m=Sms&a=uninstall&id="+id;
		}
	}
	function send_demo()
	{
		$.ajax({
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=send_demo&test_mobile="+$.trim($("input[name='test_mobile']").val()),
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					if(obj.status==0)
					{
						alert(obj.info);
					}
					else
					$("#info").html(obj.info);
				}
		});
	}
	$(document).ready(function(){
		$("input[name='test_mobile_btn']").bind("click",function(){
			var mail = $.trim($("input[name='test_mobile']").val());
			if(mail!='')
			send_demo();
		});
	});
</script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div><?php echo ($main_title); ?><input type="text" style="margin:0 15px;vertical-align: top;" class="textbox" name="test_mobile" /><input type="button" class="button" name="test_mobile_btn" value="<?php echo L("TEST");?>" />
</div>
<div class="blank10"></div>

<table cellspacing="0" cellpadding="0" class="dataTable" id="dataTable">
	<tbody>
		<tr>
			<td class="topTd" colspan="3">&nbsp; </td>
			</tr>
			<tr class="row">
				<th><?php echo L("SMS_NAME");?></th>
				<th><?php echo L("DESCRIPTION");?></th>
				<th><?php echo L("TAG_LANG_OPERATE");?></th>
				</tr>
				<?php if(is_array($sms_list)): foreach($sms_list as $key=>$sms_item): ?><tr class="row">
					<td><?php echo ($sms_item["name"]); ?></td>
					<td><?php echo ($sms_item["description"]); ?></td>
					<td>
						<?php if($sms_item['installed'] == 0): ?><a href="<?php echo u("Sms/install",array("class_name"=>$sms_item['class_name']));?>"><?php echo L("INSTALL");?></a>
						<?php else: ?>
							<a href="javascript:uninstall(<?php echo ($sms_item["id"]); ?>);" ><?php echo L("UNINSTALL");?></a>
							<a href="javascript:edit(<?php echo ($sms_item["id"]); ?>);" ><?php echo L("EDIT");?></a>
							<?php if($sms_item['is_effect'] == 0): ?><a href="<?php echo u("Sms/set_effect",array("id"=>$sms_item['id']));?>"><?php echo L("USE_THIS_SMS");?></a>
							<?php else: ?>
								<?php echo L("SMS_USING");?><?php endif; ?><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
				<tr><td class="bottomTd" colspan="3"> &nbsp;</td></tr>
			</tbody>
		</table>


</div>
</body>
</html>