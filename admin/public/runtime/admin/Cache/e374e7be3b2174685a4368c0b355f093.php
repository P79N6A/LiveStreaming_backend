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

<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<?php function tips()
	{
		return "<a sytle='text-decoration:none;'>无</a>";
	}
	function get_payment_user_name($uid)
	{
		return M("User")->where("id=".$uid)->getField("nick_name");
	}
	function get_payment_name($id,$notice)
	{
		$str = "";
		$payment = M("Payment")->getById($notice['payment_id']);
		if($payment)
		{
			$str .= "通过";
			$class_name = $payment['class_name']."_payment";
			$str.=$payment['name'];
			if($notice['bank_id']!="")
			{
				require_once APP_ROOT_PATH."/system/payment/".$class_name.".php";
				$str.=$payment_lang[$notice['bank_id']];
			}
		}
		else
		{
			$str = "管理员收款";
		}

		return $str;
	}
	function get_paymentnotice_title($name,$notice)
	{
		if($notice['memo']=="")$notice['memo']="无";
		if($name)
		return "<span title='".$name."--付款备注:".$notice['memo']."'>".msubstr($name)."</span>";
		else
		return "<span title='在线充值--付款备注:".$notice['memo']."'>在线充值</span>";
	} ?>
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

<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>在线充值单</div>
<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
		<div>主播ID：<input type="text" class="textbox" name="user_id" value="<?php echo trim($_REQUEST['user_id']);?>" style="width:100px" /></div>
		<div><?php echo L("NICK_NAME");?>：<input type="text" class="textbox" name="nick_name" value="<?php echo trim($_REQUEST['nick_name']);?>" style="width:100px;" /></div>
		<div>创建时间 ：
			<input style="margin: 0" type="text" class="textbox" name="start_time" id="start_time" value="<?php echo trim($_REQUEST['start_time']);?>" onfocus="return showCalendar('start_time', '%Y-%m-%d', false, false, 'start_time');" /> - <input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo trim($_REQUEST['end_time']);?>" onfocus="return showCalendar('end_time', '%Y-%m-%d', false, false, 'end_time');" />
		</div>
		<div>付款单号：<input type="text" class="textbox" name="notice_sn" value="<?php echo trim($_REQUEST['notice_sn']);?>" /></div>
		<div>收款方式：
			<select style="width: 100px" name="payment_id">
				<option value="0" <?php if(intval($_REQUEST['payment_id']) == 0): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
				<?php if(is_array($payment_list)): foreach($payment_list as $key=>$payment_item): ?><option value="<?php echo ($payment_item["id"]); ?>" <?php if(intval($_REQUEST['payment_id']) == $payment_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($payment_item["name"]); ?></option><?php endforeach; endif; ?>
			</select>
		</div>
		<div>支付状态：
			<select style="width: 80px;margin:0" name="is_paid">
				<option value="-1" <?php if(intval($_REQUEST['is_paid']) == -1 || !isset($_REQUEST['is_paid'])): ?>selected="selected"<?php endif; ?>><?php echo L("ALL");?></option>
				<option value="0" <?php if(intval($_REQUEST['is_paid']) == 0 && isset($_REQUEST['is_paid'])): ?>selected="selected"<?php endif; ?>>未支付</option>
				<option value="1" <?php if(intval($_REQUEST['is_paid']) == 1): ?>selected="selected"<?php endif; ?>>已支付</option>
			</select>
		</div>
		<div>
			<?php if($open_vip == 1): ?>项目类型：
    		<select style="width: 100px;margin:0" name="type">
                <option value="" selected="selected"><?php echo L("ALL");?></option>
                <option value="0" <?php if($_REQUEST['type'] == 0 && $_REQUEST['type'] != ''): ?>selected="selected"<?php endif; ?>>购买秀豆</option>
                <option value="1"<?php if($_REQUEST['type'] == 1): ?>selected="selected"<?php endif; ?>>购买VIP</option>
            </select><?php endif; ?>
			<input type="hidden" value="RechargeNotice" name="m" />
			<input type="hidden" value="index" name="a" />
			<input type="submit" class="button" value="<?php echo L("SEARCH");?>" /><input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv();" />
		</div>
	</form>
</div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="13" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照<?php echo L("ID");?>   <?php echo ($sortType); ?> "><?php echo L("ID");?>   <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照主播ID   <?php echo ($sortType); ?> ">主播ID   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('notice_sn','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照付款单号   <?php echo ($sortType); ?> ">付款单号   <?php if(($order)  ==  "notice_sn"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('recharge_name','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照项目名称   <?php echo ($sortType); ?> ">项目名称   <?php if(($order)  ==  "recharge_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('outer_notice_sn','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照外部单号   <?php echo ($sortType); ?> ">外部单号   <?php if(($order)  ==  "outer_notice_sn"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('diamonds','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照充值秀豆数   <?php echo ($sortType); ?> ">充值秀豆数   <?php if(($order)  ==  "diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照<?php echo L("NICK_NAME");?>   <?php echo ($sortType); ?> "><?php echo L("NICK_NAME");?>   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('payment_id','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照支付方式   <?php echo ($sortType); ?> ">支付方式   <?php if(($order)  ==  "payment_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照创建时间   <?php echo ($sortType); ?> ">创建时间   <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pay_time','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照支付时间   <?php echo ($sortType); ?> ">支付时间   <?php if(($order)  ==  "pay_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照金额   <?php echo ($sortType); ?> ">金额   <?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_paid','<?php echo ($sort); ?>','RechargeNotice','index')" title="按照是否支付<?php echo ($sortType); ?> ">是否支付<?php if(($order)  ==  "is_paid"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$deal): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($deal["id"]); ?></td><td><?php echo ($deal["user_id"]); ?></td><td><?php echo ($deal["notice_sn"]); ?></td><td><?php echo (get_paymentnotice_title($deal["recharge_name"],$deal)); ?></td><td><?php echo ($deal["outer_notice_sn"]); ?></td><td><?php echo ($deal["diamonds"]); ?></td><td><?php echo (get_payment_user_name($deal["user_id"])); ?></td><td><?php echo (get_payment_name($deal["payment_id"],$deal)); ?></td><td><?php echo (to_date($deal["create_time"])); ?></td><td><?php echo (to_date($deal["pay_time"])); ?></td><td><?php echo (format_price($deal["money"])); ?></td><td><?php echo (get_status($deal["is_paid"])); ?></td><td class="op_action"><a href="javascript:tips('<?php echo ($deal["id"]); ?>')">无</a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="13" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>