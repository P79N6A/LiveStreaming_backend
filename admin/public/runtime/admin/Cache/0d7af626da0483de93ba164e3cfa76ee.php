<?php if (!defined('THINK_PATH')) exit();?>

<?php function to_money($money){
		return format_price($money);
	}
	function get_refund_user_name($uid)
	{
		return M("User")->where("id=".$uid)->getField("nick_name");
	}
	function get_confirm($id,$vo)
	{
		if($vo['is_pay']==0){
			return "<a href='javascript:refund_allow(".$id.");'>允许</a> <a href='javascript:refund_not_allow(".$id.");'>不允许</a> ";
 		}elseif($vo['is_pay']==1){
			return "<a href='javascript:refund_confirm(".$id.");'>确认支付</a> ";
		}elseif($vo['is_pay']==2){
			return "<a>未允许支付</a>";
		}elseif($vo['is_pay']==3){
			return "<a>支付成功</a>";
		}else{
			return "<a>操作失败</a>";
		}
	}
	function get_authentication_name($uid)
	{
	return M("User")->where("id=".$uid)->getField("authentication_name");
	}
	function get_alipay_account($uid)
	{
	return M("User")->where("id=".$uid)->getField("alipay_account");
	}
	function get_bank_name($uid)
	{
	return M("User")->where("id=".$uid)->getField("bank_name");
	}
	function get_branch_name($uid)
	{
	return M("User")->where("id=".$uid)->getField("branch_name");
	}
	function get_open_account_num($uid)
	{
	return M("User")->where("id=".$uid)->getField("open_account_num");
	}
	function get_open_account_name($uid)
	{
	return M("User")->where("id=".$uid)->getField("open_account_name");
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
<script type="text/javascript">
function refund_confirm(id)
{
	$.weeboxs.open(ROOT+'?m=UserConfirmRefund&a=refund_confirm&id='+id, {contentType:'ajax',showButton:false,title:"确认提现",width:600,height:320});
}
function refund_allow(id){
	$.ajax({
			url: ROOT+'?m=UserRefund&a=refund_allow&status=1&id='+id,
			data: "ajax=1",
			dataType: "json",
			success: function(msg){
				if(msg.status==0){
					alert(msg.info);
				}
			},
			error: function(){
				$.weeboxs.open(ROOT+'?m=UserRefund&a=refund_allow&status=1&id='+id, {contentType:'ajax',showButton:false,title:"确认允许提现",width:600,height:140});
			}
		});
}
function refund_not_allow(id){
	$.ajax({
			url: ROOT+'?m=UserRefund&a=refund_allow&status=0&id='+id,
			data: "ajax=1",
			dataType: "json",
			success: function(msg){
				if(msg.status==0){
					alert(msg.info);
				}
			},
			error: function(){
				$.weeboxs.open(ROOT+'?m=UserRefund&a=refund_allow&status=0&id='+id, {contentType:'ajax',showButton:false,title:"确认不允许提现",width:600,height:140});
			}
		});
}
function batch_confirm(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert('请选择要提现的用户');
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");

	}
	$.weeboxs.open(ROOT+'?m=UserConfirmRefund&a=batch_confirm&id='+id,{contentType:'ajax',showButton:false,title:'批量提现',width:1200,height:600});
	

}

function batch_examine(id)
{
	if(!id)
	{
		idBox = $(".key:checked");
		if(idBox.length == 0)
		{
			alert('请选择要审核的用户');
			return;
		}
		idArray = new Array();
		$.each( idBox, function(i, n){
			idArray.push($(n).val());
		});
		id = idArray.join(",");

	}
	$.weeboxs.open(ROOT+'?m=UserConfirmRefund&a=batch_examine&id='+id,{contentType:'ajax',showButton:false,title:'批量审核',width:1200,height:600});


}
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>提现列表</div>
<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
		<div>主播ID：<input type="text" class="textbox" name="user_id" value="<?php echo trim($_REQUEST['user_id']);?>" style="width:100px" /></div>
        <div><?php echo L("NICK_NAME");?>: <input type="text" class="textbox" name="nick_name" value="<?php echo trim($_REQUEST['nick_name']);?>" style="width:100px;" /></div>
		<div>状态:<select name="is_pay" style="width: 100px;margin:0">
			<option value="">所有</option>
			<option value="0" <?php if($_REQUEST['is_pay']!='' && intval($_REQUEST['is_pay']) == 0): ?>selected="selected"<?php endif; ?> >待审核</option>
			<option value="1" <?php if($_REQUEST['is_pay'] == '1'): ?>selected="selected"<?php endif; ?> >允许支付</option>
			<option value="2" <?php if($_REQUEST['is_pay'] == '2'): ?>selected="selected"<?php endif; ?> >不允许支付</option>
			<option value="3" <?php if($_REQUEST['is_pay'] == '3'): ?>selected="selected"<?php endif; ?> >提现确认成功</option>
		</select><input type="hidden" value="UserRefundList" name="m" /><input type="hidden" value="index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" /><input type="button" class="button" value="<?php echo L("EXPORT");?>" onclick="export_csv();" /></div>
	</form>
</div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="20" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="50px   "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照主播ID   <?php echo ($sortType); ?> ">主播ID   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照真实姓名   <?php echo ($sortType); ?> ">真实姓名   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照支付宝账号   <?php echo ($sortType); ?> ">支付宝账号   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照银行名称   <?php echo ($sortType); ?> ">银行名称   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照支行名称   <?php echo ($sortType); ?> ">支行名称   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照开户账号   <?php echo ($sortType); ?> ">开户账号   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照开户名称   <?php echo ($sortType); ?> ">开户名称   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('money','<?php echo ($sort); ?>','UserRefundList','index')" title="按照金额   <?php echo ($sortType); ?> ">金额   <?php if(($order)  ==  "money"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('ticket','<?php echo ($sort); ?>','UserRefundList','index')" title="按照<?php echo L("TICKET");?>   <?php echo ($sortType); ?> "><?php echo L("TICKET");?>   <?php if(($order)  ==  "ticket"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','UserRefundList','index')" title="按照<?php echo L("NICK_NAME");?>   <?php echo ($sortType); ?> "><?php echo L("NICK_NAME");?>   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','UserRefundList','index')" title="按照申请时间   <?php echo ($sortType); ?> ">申请时间   <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('memo','<?php echo ($sort); ?>','UserRefundList','index')" title="按照申请备注   <?php echo ($sortType); ?> ">申请备注   <?php if(($order)  ==  "memo"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_pay','<?php echo ($sort); ?>','UserRefundList','index')" title="按照是否审核   <?php echo ($sortType); ?> ">是否审核   <?php if(($order)  ==  "is_pay"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pay_time','<?php echo ($sort); ?>','UserRefundList','index')" title="按照确认支付时间   <?php echo ($sortType); ?> ">确认支付时间   <?php if(($order)  ==  "pay_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('reply','<?php echo ($sort); ?>','UserRefundList','index')" title="按照操作备注   <?php echo ($sortType); ?> ">操作备注   <?php if(($order)  ==  "reply"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('partner_trade_no','<?php echo ($sort); ?>','UserRefundList','index')" title="按照业务单号   <?php echo ($sortType); ?> ">业务单号   <?php if(($order)  ==  "partner_trade_no"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('pay_log','<?php echo ($sort); ?>','UserRefundList','index')" title="按照支付备注<?php echo ($sortType); ?> ">支付备注<?php if(($order)  ==  "pay_log"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$deal): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($deal["id"]); ?>"></td><td><?php echo ($deal["id"]); ?></td><td><?php echo ($deal["user_id"]); ?></td><td><?php echo (get_authentication_name($deal["user_id"])); ?></td><td><?php echo (get_alipay_account($deal["user_id"])); ?></td><td><?php echo (get_bank_name($deal["user_id"])); ?></td><td><?php echo (get_branch_name($deal["user_id"])); ?></td><td><?php echo (get_open_account_num($deal["user_id"])); ?></td><td><?php echo (get_open_account_name($deal["user_id"])); ?></td><td><?php echo (to_money($deal["money"])); ?></td><td><?php echo ($deal["ticket"]); ?></td><td><?php echo (get_refund_user_name($deal["user_id"])); ?></td><td><?php echo (to_date($deal["create_time"])); ?></td><td><?php echo (get_title($deal["memo"])); ?></td><td><?php echo (get_status($deal["is_pay"])); ?></td><td><?php echo (to_date($deal["pay_time"])); ?></td><td><?php echo (get_title($deal["reply"])); ?></td><td><?php echo (get_title($deal["partner_trade_no"])); ?></td><td><?php echo (get_title($deal["pay_log"])); ?></td><td class="op_action"><div class="viewOpBox_demo"> <?php echo (get_confirm($deal["id"],$deal)); ?>&nbsp; <?php echo ($deal[""]); ?>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="20" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<table class="dataTable">
		<tobdy>
			<td colspan="5">
				<input type="button" class="button button-add" value="批量审核" onclick="batch_examine();" />
				<input type="button" class="button button-add" value="批量提现" onclick="batch_confirm();" />
			</td>
		</tobdy>
	</table>

<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>