<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo app_conf("SITE_NAME");?> - <?php echo l("ADMIN_PLATFORM");?> </title>

<frameset frameborder="10" framespacing="0" border="0" rows="57, *">
	<?php if ($_REQUEST['change_nav']) {
	?>
	<frame src="<?php echo u('Index/top');?>change_nav=<?php echo $_REQUEST['change_nav']; ?>" name="top" frameborder="0" noresize scrolling="no" marginwidth="0" marginheight="0">
	<frameset frameborder="0"  framespacing="0" border="0" cols="221,7, *" id="frame-body">
		<frame src="<?php echo u('Index/left');?>change_nav=<?php echo $_REQUEST['change_nav']; ?>" frameborder="0" id="menu-frame" name="menu">
		<frame src="<?php echo u('Index/drag');?>" id="drag-frame" name="drag-frame" frameborder="no" scrolling="no">
		<frame src="<?php echo u('Index/main');?>" frameborder="0" id="main-frame" name="main">
	</frameset>
	<?php } else {
	?>
	<frame src="<?php echo u('Index/top');?>" name="top" frameborder="0" noresize scrolling="no" marginwidth="0" marginheight="0">
	<frameset frameborder="0"  framespacing="0" border="0" cols="221,7, *" id="frame-body">
		<frame src="<?php echo u('Index/left');?>" frameborder="0" id="menu-frame" name="menu">
		<frame src="<?php echo u('Index/drag');?>" id="drag-frame" name="drag-frame" frameborder="no" scrolling="no">
		<frame src="<?php echo u('Index/main');?>" frameborder="0" id="main-frame" name="main">
	</frameset>
	<?php }
	?>

</frameset>

<noframes></noframes>
</html>