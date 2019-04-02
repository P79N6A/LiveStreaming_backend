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

<script type="text/javascript" src="__TMPL__Common/js/conf.js"></script>
<script type="text/javascript" src="__ROOT__/public/region.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/user_edit.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title"><?php echo L("EDIT");?> 
	<?php if($vo['type'] == 'imagetext' ): ?><a href="<?php echo u("WeiboList/imagetext");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a>
	<?php elseif($vo['type'] == 'video'): ?>
		<a href="<?php echo u("WeiboList/video");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a>
	<?php elseif($vo['type'] == 'photo'): ?>
		<a href="<?php echo u("WeiboList/photo");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a>
	<?php elseif($vo['type'] == 'goods'): ?>
		<a href="<?php echo u("WeiboList/goods");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a>
	<?php elseif($vo['type'] == 'red_photo'): ?>
		<a href="<?php echo u("WeiboList/red_photo");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a>
	<?php else: ?>	
		<a href="<?php echo u("WeiboList/index");?>" class="back_list">
			<?php echo L("BACK_LIST");?>
		</a><?php endif; ?>
</div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data" onsubmit="return submit_check();">
<table class="form conf_tab" cellpadding=0 cellspacing=0 >
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
    <tr>
        <td class="item_title"><?php echo L("ID");?>:</td>
        <td class="item_input"><?php echo ($vo["id"]); ?></td>
    </tr>
	<tr>
		<td class="item_title">	发布内容:</td>
		<td class="item_input"><?php echo ($vo["content"]); ?></td>
	</tr>
	<?php if($vo['type'] == 'imagetext' ): ?><tr>
			<td class="item_title">图片:</td>
			<td class="item_input">
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data_item): ++$i;$mod = ($i % 2 )?><a href="<?php echo ($data_item["url"]); ?>" target="_blank">
						<img src="<?php echo ($data_item["url"]); ?>" height="100" width="100" />
					</a><?php endforeach; endif; else: echo "" ;endif; ?>
			</td>
		</tr>
	<?php elseif($vo['type'] == 'video'): ?>
		<tr>
			<td class="item_title">视频:</td>
			<td class="item_input">
				<video id="video" controls="controls" src="<?php echo ($vo["data"]); ?>" class="img-thumbnail" style="max-height: 300px;">
			</td>
		</tr>
	<?php elseif($vo['type'] == 'photo'): ?>
		<tr>
			<td class="item_title">图片:</td>
			<td class="item_input">
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data_item): ++$i;$mod = ($i % 2 )?><div style=" width:100px;float:left; margin:5px 5px;">
							<a href="<?php echo ($data_item["url"]); ?>" target="_blank">
								<img src="<?php echo ($data_item["url"]); ?>" height="100" width="100" />
							</a>
							<p style="text-align:center;">
								<?php if($data_item["is_model"] == 1 ): ?>收费
								<?php else: ?>
									免费<?php endif; ?>
							</p>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</td>
		</tr>
		<tr>
			<td class="item_title">价格:</td>
			<td class="item_input">
				<?php echo ($vo["price"]); ?>元
			</td>
		</tr>
	<?php elseif($vo['type'] == 'goods'): ?>
		<tr>
			<td class="item_title">图片:</td>
			<td class="item_input">
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data_item): ++$i;$mod = ($i % 2 )?><a href="<?php echo ($data_item["url"]); ?>" target="_blank">
						<img src="<?php echo ($data_item["url"]); ?>" height="100" width="100" />
					</a><?php endforeach; endif; else: echo "" ;endif; ?>
			</td>
		</tr>
		<tr>
			<td class="item_title">价格:</td>
			<td class="item_input">
				<?php echo ($vo["price"]); ?>元
			</td>
		</tr>
		<tr style=" display:none;">
			<td class="item_title">买家商品获取方式</td>
			<td class="item_input">
				
			</td>
		</tr>
	<?php elseif($vo['type'] == 'red_photo'): ?>
		<tr>
			<td class="item_title">图片:</td>
			<td class="item_input">
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data_item): ++$i;$mod = ($i % 2 )?><a href="<?php echo ($data_item["url"]); ?>" target="_blank">
						<img src="<?php echo ($data_item["url"]); ?>" height="100" width="100" />
					</a><?php endforeach; endif; else: echo "" ;endif; ?>
			</td>
		</tr>
	<?php else: ?><?php endif; ?>
	<tr>
		<td class="item_title">是否通过:</td>
		<td class="item_input">
			<label>通过<input type="radio" name="status" value="1" <?php if($vo['status'] == 1): ?>checked="checked"<?php endif; ?>  /></label>
			<label>不通过<input type="radio" name="status" value="0" <?php if($vo['status'] == 0): ?>checked="checked"<?php endif; ?> /></label>
		</td>
	</tr>
	
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>
<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="WeiboList" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="update" />
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
			<!--隐藏元素-->
			<input type="submit" class="button" value="<?php echo L("EDIT");?>" />
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