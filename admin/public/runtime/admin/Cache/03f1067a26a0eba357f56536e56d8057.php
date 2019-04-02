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
	function edit(id)
	{
		location.href = ROOT+"?"+VAR_MODULE+"=StationMessage&"+VAR_ACTION+"=edit&id="+id;
	}
	function view_result(id)
	{
		location.href = ROOT+"?"+VAR_MODULE+"=StationMessage&"+VAR_ACTION+"=msg_list&msg_id="+id;
	}
</script>
        <?php function get_nickname($id){
                $get_nickname=$GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$id);
				return emoji_decode($get_nickname);
            } ?>

<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>广告推送列表</div>
<div class="blank10"></div>
<div class="button_row">
</div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="7" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px  "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','IndexProp','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_url','<?php echo ($sort); ?>','IndexProp','index')" title="按照广告链接  <?php echo ($sortType); ?> ">广告链接  <?php if(($order)  ==  "send_url"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_content','<?php echo ($sort); ?>','IndexProp','index')" title="按照<?php echo L("CONTENT");?>  <?php echo ($sortType); ?> "><?php echo L("CONTENT");?>  <?php if(($order)  ==  "send_content"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_time','<?php echo ($sort); ?>','IndexProp','index')" title="按照<?php echo L("SEND_TIME");?>  <?php echo ($sortType); ?> "><?php echo L("SEND_TIME");?>  <?php if(($order)  ==  "send_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('send_status','<?php echo ($sort); ?>','IndexProp','index')" title="按照<?php echo L("SEND_STATUS");?><?php echo ($sortType); ?> "><?php echo L("SEND_STATUS");?><?php if(($order)  ==  "send_status"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$StationMessage): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($StationMessage["id"]); ?>"></td><td><?php echo ($StationMessage["id"]); ?></td><td><?php echo ($StationMessage["send_url"]); ?></td><td><?php echo (msubstr($StationMessage["send_content"])); ?></td><td><?php echo (to_date($StationMessage["send_time"])); ?></td><td><?php echo (get_send_status($StationMessage["send_status"])); ?></td><td class="op_action"><a href="javascript:foreverdel('<?php echo ($StationMessage["id"]); ?>')"><?php echo L("FOREVERDEL");?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="7" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<table class="dataTable">
		<tbody>
			<td colspan="7">
				<input type="button" class="button button-add" value="<?php echo L("ADD");?>" onclick="location.href='<?php echo u("IndexProp/add");?>';" />
				<input type="button" class="button button-del" value="<?php echo L("FOREVERDEL");?>" onclick="foreverdel();" />
			</td>
		</tbody>
	</table>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>