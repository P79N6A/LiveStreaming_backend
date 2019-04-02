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
<div class="main_title_list"><div class="list-line-ico"></div>充值规则列表</div>
<div class="blank10"></div>
	<?php function qr_code($qr_code){
		return "<img src='".$qr_code."' style='height:35px;width:35px;'/>";
		} ?>
<<?php if($is_coins_module == 1): ?><!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="14" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="90px"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('name','<?php echo ($sort); ?>','RechargeRule','index')" title="按照名称<?php echo ($sortType); ?> ">名称<?php if(($order)  ==  "name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照秀豆<?php echo ($sortType); ?> ">秀豆<?php if(($order)  ==  "diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','RechargeRule','index')" title="按照价格<?php echo ($sortType); ?> ">价格<?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('iap_diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果支付获得秀豆<?php echo ($sortType); ?> ">苹果支付获得秀豆<?php if(($order)  ==  "iap_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('iap_money','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果支付价格<?php echo ($sortType); ?> ">苹果支付价格<?php if(($order)  ==  "iap_money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('product_id','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果项目ID<?php echo ($sortType); ?> ">苹果项目ID<?php if(($order)  ==  "product_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('gift_diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照赠送秀豆<?php echo ($sortType); ?> ">赠送秀豆<?php if(($order)  ==  "gift_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('gift_coins','<?php echo ($sort); ?>','RechargeRule','index')" title="按照赠送游戏币<?php echo ($sortType); ?> ">赠送游戏币<?php if(($order)  ==  "gift_coins"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("IS_EFFECT");?><?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?><?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("SORT");?><?php echo ($sortType); ?> "><?php echo L("SORT");?><?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('qr_code','<?php echo ($sort); ?>','RechargeRule','index')" title="按照二维码(仅供扫码支付)<?php echo ($sortType); ?> ">二维码(仅供扫码支付)<?php if(($order)  ==  "qr_code"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rechangerule): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($rechangerule["id"]); ?>"></td><td><?php echo ($rechangerule["id"]); ?></td><td><?php echo ($rechangerule["name"]); ?></td><td><?php echo ($rechangerule["diamonds"]); ?></td><td><?php echo ($rechangerule["money"]); ?></td><td><?php echo ($rechangerule["iap_diamonds"]); ?></td><td><?php echo ($rechangerule["iap_money"]); ?></td><td><?php echo ($rechangerule["product_id"]); ?></td><td><?php echo ($rechangerule["gift_diamonds"]); ?></td><td><?php echo ($rechangerule["gift_coins"]); ?></td><td><?php echo (get_is_effect($rechangerule["is_effect"],$rechangerule['id'])); ?></td><td><?php echo (get_sort($rechangerule["sort"],$rechangerule['id'])); ?></td><td><?php echo (qr_code($rechangerule["qr_code"])); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:edit('<?php echo ($rechangerule["id"]); ?>')">编辑</a>&nbsp;<a href="javascript:foreverdel('<?php echo ($rechangerule["id"]); ?>')">删除</a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="14" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<?php else: ?>
	<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="13" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="90px"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('name','<?php echo ($sort); ?>','RechargeRule','index')" title="按照名称<?php echo ($sortType); ?> ">名称<?php if(($order)  ==  "name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照秀豆<?php echo ($sortType); ?> ">秀豆<?php if(($order)  ==  "diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','RechargeRule','index')" title="按照价格<?php echo ($sortType); ?> ">价格<?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('iap_diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果支付获得秀豆<?php echo ($sortType); ?> ">苹果支付获得秀豆<?php if(($order)  ==  "iap_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('iap_money','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果支付价格<?php echo ($sortType); ?> ">苹果支付价格<?php if(($order)  ==  "iap_money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('product_id','<?php echo ($sort); ?>','RechargeRule','index')" title="按照苹果项目ID<?php echo ($sortType); ?> ">苹果项目ID<?php if(($order)  ==  "product_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('gift_diamonds','<?php echo ($sort); ?>','RechargeRule','index')" title="按照赠送秀豆<?php echo ($sortType); ?> ">赠送秀豆<?php if(($order)  ==  "gift_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("IS_EFFECT");?><?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?><?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','RechargeRule','index')" title="按照<?php echo L("SORT");?><?php echo ($sortType); ?> "><?php echo L("SORT");?><?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('qr_code','<?php echo ($sort); ?>','RechargeRule','index')" title="按照二维码(仅供扫码支付)<?php echo ($sortType); ?> ">二维码(仅供扫码支付)<?php if(($order)  ==  "qr_code"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$rechangerule): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($rechangerule["id"]); ?>"></td><td><?php echo ($rechangerule["id"]); ?></td><td><?php echo ($rechangerule["name"]); ?></td><td><?php echo ($rechangerule["diamonds"]); ?></td><td><?php echo ($rechangerule["money"]); ?></td><td><?php echo ($rechangerule["iap_diamonds"]); ?></td><td><?php echo ($rechangerule["iap_money"]); ?></td><td><?php echo ($rechangerule["product_id"]); ?></td><td><?php echo ($rechangerule["gift_diamonds"]); ?></td><td><?php echo (get_is_effect($rechangerule["is_effect"],$rechangerule['id'])); ?></td><td><?php echo (get_sort($rechangerule["sort"],$rechangerule['id'])); ?></td><td><?php echo (qr_code($rechangerule["qr_code"])); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:edit('<?php echo ($rechangerule["id"]); ?>')">编辑</a>&nbsp;<a href="javascript:foreverdel('<?php echo ($rechangerule["id"]); ?>')">删除</a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="13" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 --><?php endif; ?>
	<table class="dataTable">
		<tbody>
			<td colspan="7">
				<input type="button" class="button button-add" value="<?php echo L("ADD");?>" onclick="add();" />
				<input type="button" class="button button-del" value="<?php echo L("DEL");?>" onclick="foreverdel();" />
			</td>
		</tbody>
	</table>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>