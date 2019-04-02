<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo app_conf("SITE_NAME");?><?php echo l("ADMIN_PLATFORM");?></title>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/new/css/reset.css" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/new/css/frame.css" />
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/top.js"></script>
<?php if(app_conf("ADMIN_MSG_SENDER_OPEN")==1){ ?>
<script type="text/javascript">
	var send_span = <?php echo app_conf("SEND_SPAN");?>000;
	
</script>
<script type="text/javascript" src="__TMPL__Common/js/msg_sender.js"></script>
<?php } ?>
<script type="text/javascript" src="__TMPL__Common/js/notify_sender.js"></script>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
</head>

<body>
	
<div class="layout-header">
<div class="logo f_l" style="background:url(/public/images/admin/new/logo.png) 10px center no-repeat;">
	<div class="c-left-nav"><i class="iconfont"></i></div>
	<a href="javascript:void(0)"></a>
</div>
<div class="nav f_l"  id="navs">
	<?php if(is_array($navs)): foreach($navs as $key=>$nav): ?><a class="" href="<?php echo u("Index/left",array("key"=>$nav['key']));?>&change_nav=<?php echo $_REQUEST['change_nav'];  ?>"><?php echo ($nav["name"]); ?></a><?php endforeach; endif; ?>
</div>
<div class="navright f_r">
			
	<div class="navrightlist">
	<div class="prl">	
		<div class="hidenav active">
			<a href="<?php echo u("Public/do_loginout");?>" target="_parent"><?php echo l("LOGIN_OUT");?></a>
			<a href="<?php echo u("Cache/index");?>" target="main"><?php echo l("CLEAR_CACHE");?></a>
			<a href="<?php echo u("Index/change_password");?>" target="main"><?php echo l("CHANGE_PASSWORD");?></a>
		</div>
	</div>
	<div class="pdl40"><?php echo ($admin["adm_name"]); ?><i class="iconfont">&#xe604;</i></div>
	

	</div>
</div>
<div class="blank0"></div>
</div>
</body>
</html>