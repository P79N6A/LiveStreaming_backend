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

<div class="main">
<div class="main_title"><?php echo L("INSTALL");?> <a href="<?php echo u("Sms/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<table class="form" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("SMS_NAME");?>:</td>
		<td class="item_input">
			<?php echo ($data["name"]); ?>
			<input type="hidden" value="<?php echo ($data["name"]); ?>" name="name" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("CLASS_NAME");?>:</td>
		<td class="item_input">
			<?php echo ($data["class_name"]); ?>
			<input type="hidden" value="<?php echo ($data["class_name"]); ?>" name="class_name" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("SMS_ACCOUNT");?>:</td>
		<td class="item_input">
			<input type="text" class="textbox"  name="user_name" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("SMS_PASSWORD");?>:</td>
		<td class="item_input">
			<input type="password" class="textbox"  name="password"  autocomplete="off" />
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("DESCRIPTION");?>:</td>
		<td class="item_input">
			<textarea class="textarea" name="description" ></textarea>
		</td>
	</tr>
	<?php if($data['config']): ?><tr>
		<td class="item_title"><?php echo L("CONFIG_INFO");?>:</td>
		<td class="item_input">
			<?php if(is_array($data["config"])): foreach($data["config"] as $key=>$config): ?><?php $config_name = $key; ?>
				<span class="cfg_title"><?php echo trim($data['lang'][$key]);?>:</span>
				<span class="cfg_content">
				<?php if($config['INPUT_TYPE'] == 0): ?><input type="text" class="textbox" name="config[<?php echo ($key); ?>]" value="<?php echo ($config['INPUT_VALUE']); ?>" />
				<?php elseif($config['INPUT_TYPE'] == 2): ?>
				<input type="password" class="textbox" name="config[<?php echo ($key); ?>]" value="<?php echo ($config['INPUT_VALUE']); ?>" />
				<?php elseif($config['INPUT_TYPE'] == 4): ?>
				<textarea class="textarea" name="config[<?php echo ($key); ?>]"><?php echo ($config['INPUT_VALUE']); ?></textarea>
				<?php else: ?>
				<select name="config[<?php echo ($key); ?>]" >
					<?php if(is_array($config["VALUES"])): foreach($config["VALUES"] as $key=>$val): ?><option value="<?php echo ($val); ?>"><?php echo trim($data['lang'][$config_name."_".$val]);?></option><?php endforeach; endif; ?>
				</select><?php endif; ?>
				</span>
				<div class="blank5"></div><?php endforeach; endif; ?>
		</td>
	</tr><?php endif; ?>
	<tr>
		<td class="item_title"></td>
		<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" value="<?php echo ($data["server_url"]); ?>" name="server_url" />
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Sms" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="insert" />
			<!--隐藏元素-->
			<input type="submit" class="button" value="<?php echo L("ADD");?>" />
			<input type="reset" class="button" value="<?php echo L("RESET");?>" />
		</td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>	 
</form>
</div>
</body>
</html>