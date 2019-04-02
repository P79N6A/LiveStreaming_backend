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
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>动态列表</div>
<?php function get_weibo_type($type){
        switch($type){
			case 'imagetext':
			return '图文动态';
			case 'video':
			return '视频动态';

			case 'weixin':
			return '微信账号';

			case 'goods':
			return '虚拟商品动态';

			case 'red_photo':
			return '红包图片动态';

			case 'photo':
			return '写真动态';
		}
    }
    function get_nickname($id){
        $get_nickname=$GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$id);
        return $get_nickname;
    }
	function screenshot($screenshot){
		return "<img src='".$screenshot."' style='height:35px;width:35px;'/>";
	}
	function get_weibo_status($status){
		if($status){
			return '上架';
		}else{
			return '下架';
		}
	}
	function get_price_info($price){
		if($price>0){
			return $price;
		}else{
			return '免费';

		}
	}
	function get_is_recommend($is_recommend){
		if($is_recommend){
			return '是';
		}else{
			return '否';
		}
	} ?>
<div class="button_row">
	<!--<input type="button" class="button" value="<?php echo L("ADD");?>" onclick="add();" />-->
</div>

<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
        <div>动态ID：<input type="text" class="textbox" name="weibo_id" value="<?php echo trim($_REQUEST['weibo_id']);?>" style="width:100px;" /></div>
		<div>发布人ID：<input type="text" class="textbox" name="user_id" value="<?php echo trim($_REQUEST['user_id']);?>" style="width:100px;" /></div>
        <div>小视频类型：<select name="type" style="width:100px;">
                    <option value="0">全部</option>
					<option value="imagetext"  <?php if($_REQUEST['type'] == 'imagetext'): ?>selected="selected"<?php endif; ?>>图文动态</option>
			<option value="video" <?php if($_REQUEST['type'] == 'video'): ?>selected="selected"<?php endif; ?>>视频动态</option>
			<option value="weixin" <?php if($_REQUEST['type'] == 'weixin'): ?>selected="selected"<?php endif; ?>>微信账号</option>
			<option value="goods"  <?php if($_REQUEST['type'] == 'goods'): ?>selected="selected"<?php endif; ?>>虚拟商品动态</option>
			<option value="red_photo"  <?php if($_REQUEST['type'] == 'red_photo'): ?>selected="selected"<?php endif; ?>>红包图片动态</option>
			<option value="photo"  <?php if($_REQUEST['type'] == 'photo'): ?>selected="selected"<?php endif; ?>>写真动态</option>
                </select>
		</div>
        <div>小视频时间：<span><input type="text" class="textbox" name="create_time_1" id="create_time_1" value="<?php echo ($_REQUEST['create_time_1']); ?>" onfocus="this.blur(); return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /><input type="button" class="button" id="btn_create_time_1" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /></span> - <span><input type="text" class="textbox" name="create_time_2" id="create_time_2" value="<?php echo ($_REQUEST['create_time_2']); ?>" onfocus="this.blur(); return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /><input type="button" class="button" id="btn_create_time_2" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /></span>
			<input type="hidden" value="WeiboList" name="m" />
			<input type="hidden" value="index" name="a" />
			<input type="submit" class="button" value="<?php echo L("SEARCH");?>" />
		</div>
	</form>
</div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="10" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th width="90px    "><a href="javascript:sortBy('id','<?php echo ($sort); ?>','WeiboList','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','WeiboList','index')" title="按照发布人ID    <?php echo ($sortType); ?> ">发布人ID    <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','WeiboList','index')" title="按照发布人    <?php echo ($sortType); ?> ">发布人    <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('type','<?php echo ($sort); ?>','WeiboList','index')" title="按照动态类型    <?php echo ($sortType); ?> ">动态类型    <?php if(($order)  ==  "type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('content','<?php echo ($sort); ?>','WeiboList','index')" title="按照发布内容    <?php echo ($sortType); ?> ">发布内容    <?php if(($order)  ==  "content"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('price','<?php echo ($sort); ?>','WeiboList','index')" title="按照发布价格    <?php echo ($sortType); ?> ">发布价格    <?php if(($order)  ==  "price"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('status','<?php echo ($sort); ?>','WeiboList','index')" title="按照动态状态    <?php echo ($sortType); ?> ">动态状态    <?php if(($order)  ==  "status"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','WeiboList','index')" title="按照发布时间    <?php echo ($sortType); ?> ">发布时间    <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$weibo): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($weibo["id"]); ?>"></td><td><?php echo ($weibo["id"]); ?></td><td><?php echo ($weibo["user_id"]); ?></td><td><?php echo (get_nickname($weibo["user_id"])); ?></td><td><?php echo (get_weibo_type($weibo["type"])); ?></td><td><?php echo ($weibo["content"]); ?></td><td><?php echo (get_price_info($weibo["price"])); ?></td><td><?php echo (get_is_effect($weibo["status"],$weibo['id'])); ?></td><td><?php echo ($weibo["create_time"]); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:edit('<?php echo ($weibo["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp;<a href="javascript:foreverdel('<?php echo ($weibo["id"]); ?>')"><?php echo L("DEL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="10" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<table class="dataTable">
		<tbody>
			<td colspan="8">
				<input type="button" class="button button-del" value="<?php echo L("DEL");?>" onclick="foreverdel();" />
			</td>
		</tbody>
	</table>
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>