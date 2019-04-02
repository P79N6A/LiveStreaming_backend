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
<div class="main_title"><?php echo L("EDIT");?> <a href="<?php echo u("UserGeneral/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
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
        <td class="item_title"><?php echo L("CREATE_TIME");?>:</td>
        <td class="item_input"><?php echo ($vo["create_time"]); ?></td>
    </tr>
	<tr>
		<td class="item_title"><?php echo L("NICK_NAME");?>:</td>
		<td class="item_input"><input type="text" name="nick_name" class="textbox" value="<?php echo ($vo["nick_name"]); ?>"  /></td>
	</tr>
    <tr>
        <td class="item_title">是否系统管理员:</td>
        <td class="item_input">
            <label>否<input type="radio" name="is_admin" value="0" <?php if($vo['is_admin'] == 0): ?>checked="checked"<?php endif; ?>  /></label>
            <label>是<input type="radio" name="is_admin" value="1" <?php if($vo['is_admin'] == 1): ?>checked="checked"<?php endif; ?> /></label>
        </td>
    </tr>
	<!-- ljz -->
	<?php if($family != 0): ?><tr>
			<td class="item_title">家族:</td>
			<td class="item_input">
				<select name="family" style="width:160px;">
				<option value="" rel="0">请选择家族</option>
					<?php if(is_array($family)): foreach($family as $key=>$val): ?><option value="<?php echo ($val["id"]); ?>" <?php if($vo['family_id'] == $val['id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</td>
		</tr><?php endif; ?>
    <tr>
        <td class="item_title">直播分类:</td>
        <td class="item_input">
            <select name="classified_id" style="width:160px;">
            <option value="" rel="0">请选择直播分类</option>
                <?php if(is_array($video_classified)): foreach($video_classified as $key=>$val): ?><option value="<?php echo ($val["id"]); ?>" <?php if($vo['classified_id'] == $val['id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["title"]); ?></option><?php endforeach; endif; ?>
            </select>
        </td>
    </tr>
	<tr>
		<td class="item_title"><?php echo L("USER_MOBILE");?>:</td>
		<td class="item_input"><input type="text" value="<?php echo ($vo["mobile"]); ?>" class="textbox" name="mobile" /></td>
	</tr>
	<tr>
		<td class="item_title">所属地区:</td>
		<td class="item_input">
			<select name="province">
			<option value="" rel="0">请选择省份</option>
			<?php if(is_array($region_lv2)): foreach($region_lv2 as $key=>$region): ?><option value="<?php echo ($region["name"]); ?>" rel="<?php echo ($region["id"]); ?>" <?php if($region['selected']): ?>selected="selected"<?php endif; ?>><?php echo ($region["name"]); ?></option><?php endforeach; endif; ?>
			</select>

			<select name="city">
			<option value="" rel="0">请选择城市</option>
			<?php if(is_array($region_lv3)): foreach($region_lv3 as $key=>$region): ?><option value="<?php echo ($region["name"]); ?>" rel="<?php echo ($region["id"]); ?>" <?php if($region['selected']): ?>selected="selected"<?php endif; ?>><?php echo ($region["name"]); ?></option><?php endforeach; endif; ?>
			</select>

		</td>
	</tr>

	<tr>
		<td class="item_title">性别:</td>
		<td class="item_input">
			<label>女<input type="radio" name="sex" value="2" <?php if($vo['sex'] == 2): ?>checked="checked"<?php endif; ?> /></label>
			<label>男<input type="radio" name="sex" value="1" <?php if($vo['sex'] == 1 or $vo['sex'] == 0): ?>checked="checked"<?php endif; ?>/></label>
		</td>
	</tr>
	<!--<tr>
		<td class="item_title">会员类型:</td>
		<td class="item_input">
			<select name="user_type">
				<option value="0" <?php if($vo['user_type'] == 0): ?>selected="selected"<?php endif; ?>>普通用户</option>
				<option value="1" <?php if($vo['user_type'] == 1): ?>selected="selected"<?php endif; ?>>企业会员</option>
			</select>
		</td>
	</tr>-->
	<tr>
		<td class="item_title">会员等级:</td>
		<td class="item_input">
			<select name="user_level">
				<?php if(is_array($user_level)): foreach($user_level as $key=>$level): ?><option value="<?php echo ($level["level"]); ?>" <?php if($vo['user_level'] == $level['level']): ?>selected="selected"<?php endif; ?>><?php echo ($level["name"]); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="item_title">个性签名:</td>
		<td class="item_input">
            <input type="text" value="<?php echo ($vo["signature"]); ?>" class="textbox" name="signature" style="width: 450px;" maxlength="32"/>
		</td>
	</tr>

	<!-- 车行定制 ljz -->
	<?php if($open_car == 1): ?><tr>
			<td class="item_title">分类标签:</td>
			<td class="item_input">
	            <!-- <input type="text" value="<?php echo ($vo["signature"]); ?>" class="textbox" name="signature" style="width: 450px;" maxlength="32"/> -->
				<?php if(is_array($car_classify)): foreach($car_classify as $key=>$classify_list): ?><?php if($classify_list['type'] == 1): ?><input type="checkbox" value="<?php echo ($classify_list['id']); ?>" checked="checked" name="classify[]" /> <span><?php echo ($classify_list['title']); ?></span>
					<?php else: ?>
						<input type="checkbox" value="<?php echo ($classify_list['id']); ?>"  name="classify[]" /> <span><?php echo ($classify_list['title']); ?></span><?php endif; ?><?php endforeach; endif; ?>
			</td>
		</tr>

		<tr>
			<td class="item_title">主播类别:</td>
			<td class="item_input">
				<select name="anchor_sort" style="width:160px;">
				<option value="" rel="0">请选类别</option>
					<?php if(is_array($anchor_sort)): foreach($anchor_sort as $key=>$val): ?><option value="<?php echo ($val["id"]); ?>" <?php if($anchor_sort_id == $val['id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</td>
		</tr><?php endif; ?>


	<tr>
		<td class="item_title"><?php echo L("IS_EFFECT");?>:</td>
		<td class="item_input">
			<label><?php echo L("IS_EFFECT_1");?><input type="radio" name="is_effect" value="1" <?php if($vo['is_effect'] == 1): ?>checked="checked"<?php endif; ?>  /></label>
			<label><?php echo L("IS_EFFECT_0");?><input type="radio" name="is_effect" value="0" <?php if($vo['is_effect'] == 0): ?>checked="checked"<?php endif; ?> /></label>
		</td>
	</tr>
	<tr>
		<td class="item_title"><?php echo L("IS_BAN");?>:</td>
		<td class="item_input">
			<label><input type="radio" name="is_ban" value="0" <?php if($vo['is_ban'] == 0): ?>checked="checked"<?php endif; ?>  /><?php echo L("IS_BAN_0");?></label>
			<label><input type="radio" name="is_ban" value="1" <?php if($vo['is_ban'] == 1): ?>checked="checked"<?php endif; ?> /><?php echo L("IS_BAN_1");?></label>
		</td>
	</tr>
    <tr id="ban_type">
        <td class="item_title"><?php echo L("BAN_TYPE");?>:</td>
        <td class="item_input">
            <label><input type="radio" name="ban_type" value="0" <?php if($vo['ban_type'] == 0): ?>checked="checked"<?php endif; ?>  /><?php echo L("BAN_TYPE_0");?></label>
            <label><input type="radio" name="ban_type" value="1" <?php if($vo['ban_type'] == 1): ?>checked="checked"<?php endif; ?> /><?php echo L("BAN_TYPE_1");?></label>
            <label><input type="radio" name="ban_type" value="2" <?php if($vo['ban_type'] == 2): ?>checked="checked"<?php endif; ?> /><?php echo L("BAN_TYPE_2");?></label>
        </td>
    </tr>
	<tr>
		<td class="item_title"><?php echo L("IS_HOT_ON");?>:</td>
		<td class="item_input">
			<label><input type="radio" name="is_hot_on" value="0" <?php if($vo['is_hot_on'] == 0): ?>checked="checked"<?php endif; ?>  /><?php echo L("IS_HOT_ON_0");?></label>
			<label><input type="radio" name="is_hot_on" value="1" <?php if($vo['is_hot_on'] == 1): ?>checked="checked"<?php endif; ?> /><?php echo L("IS_HOT_ON_1");?></label>

		</td>
	</tr>
	<?php if((OPEN_CAR_MODULE == 1)): ?><tr>
			<td class="item_title">主播允许直播的时间段:</td>
			<td class="item_input">
				<select name="allow_start_time">
					<option value="" rel="-1">起始时间</option>
					<?php if(is_array($start_time)): foreach($start_time as $key=>$allow_time): ?><option value="<?php echo ($allow_time); ?>"  <?php if($vo['allow_start_time'] == $allow_time): ?>selected="selected"<?php endif; ?>><?php echo ($allow_time); ?>时</option><?php endforeach; endif; ?>
				</select>
				-
				<select name="allow_end_time">
					<option value="" rel="-1">结束时间</option>
					<?php if(is_array($end_time)): foreach($end_time as $key=>$allow_time): ?><option value="<?php echo ($allow_time); ?>"  <?php if($vo['allow_end_time'] == $allow_time): ?>selected="selected"<?php endif; ?>><?php echo ($allow_time); ?>时</option><?php endforeach; endif; ?>
				</select>
				<span class='tip_span'>时间为空的话，直播状态正常。如果结束时间大于起始时间，默认为第二天</span>
			</td>
		</tr><?php endif; ?>
    <tr id="show_ban_time">
        <td class="item_title"><?php echo L("BAN_TIME");?>:</td>
        <td class="item_input">
            <input type="text" class="textbox" name="ban_time" id="ban_time" value="<?php echo ($vo["ban_time"]); ?>" onfocus="this.blur(); return showCalendar('ban_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_ban_time');" />
            <input type="button" class="button" id="btn_ban_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('ban_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_ban_time');" />
            <input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#ban_time').val('');" />
            <span class='tip_span'>时间为空的话，直播状态正常；否则结束时间之前禁播。</span>
        </td>
    </tr>
	<tr id="live_end_time">
		<td class="item_title">结束直播时间</td>
		<td class="item_input">
			<input type="text" class="textbox" name="end_time" id="end_time" value="<?php echo ($vo["end_time"]); ?>" onfocus="this.blur(); return showCalendar('end_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_end_time');" />
			<input type="button" class="button" id="btn_end_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('end_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_end_time');" />
			<input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#end_time').val('');" />
			<span class='tip_span'>时间为空的话，直播正常；否则到达设定时间强制结束直播。</span>
		</td>
	</tr>
    <?php if($open_vip == 1): ?><tr>
        <td class="item_title">是否VIP:</td>
        <td class="item_input">
            <label><input type="radio" name="is_vip" value="0" <?php if($vo['is_vip'] == 0): ?>checked="checked"<?php endif; ?>  />否</label>
            <label><input type="radio" name="is_vip" value="1" <?php if($vo['is_vip'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
        </td>
    </tr>
    <tr>
        <td class="item_title">会员到期时间:</td>
        <td class="item_input">
            <input type="text" class="textbox" name="vip_expire_time" id="vip_expire_time" value="<?php echo ($vo["vip_expire_time"]); ?>" onfocus="this.blur(); return showCalendar('vip_expire_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_vip_expire_time');" />
            <input type="button" class="button" id="btn_vip_expire_time" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('vip_expire_time', '%Y-%m-%d %H:%M:%S', false, false, 'btn_vip_expire_time');" />
            <input type="button" class="button" value="<?php echo L("CLEAR_TIME");?>" onclick="$('#vip_expire_time').val('');" />
            <span class='tip_span'>是VIP时,时间为空表示永久VIP。</span>
        </td>
    </tr><?php endif; ?>
    <?php if($open_distribution == 1): ?><tr>
        <td class="item_title">分销上级ID:</td>
        <td class="item_input"><input type="text" name="p_user_id" class="textbox" value="<?php echo ($vo["p_user_id"]); ?>" /></td>
    </tr><?php endif; ?>
	<?php if((OPEN_SCALE == 1)): ?><tr>
		<td class="item_title">主播提现比例:</td>
		<td class="item_input"><input type="text" name="alone_ticket_ratio" class="textbox" value="<?php echo ($vo["alone_ticket_ratio"]); ?>" />
			<span class='tip_span'>设置主播提现比例,如果为空,则使用后台通用比例  (如：100秀票*0.01=1元)</span>
		</td>
	</tr><?php endif; ?>
    <tr>
        <td class="item_title">认证审核：</td>
        <td class="item_input">
            <input type="radio" name="is_authentication" <?php if($vo["is_authentication"] == 0): ?>checked="checked"<?php endif; ?> value="0">未认证(取消认证)
            <input type="radio" name="is_authentication" <?php if($vo["is_authentication"] == 1): ?>checked="checked"<?php endif; ?> value="1">待审核
            <input type="radio" name="is_authentication" <?php if($vo["is_authentication"] == 2): ?>checked="checked"<?php endif; ?> value="2">已认证
            <input type="radio" name="is_authentication" <?php if($vo["is_authentication"] == 3): ?>checked="checked"<?php endif; ?> value="3">审核不通过
        </td>
    </tr>
	<tr>
		<td class="item_title">分销上级ID:</td>
		<td class="item_input"><input type="text" name="share_up_id" class="textbox" value="<?php echo ($vo["share_up_id"]); ?>" /></td>
	</tr>
	<tr>
		<td colspan=2 class="bottomTd"></td>
	</tr>
</table>

<div class="blank5"></div>
<table class="form identify_info" cellspacing=0 cellpadding=0 id="identify_info_1" <?php if($vo["is_authentication"] != 0): ?>style="display:block"<?php endif; ?>>
        <tr>
            <td class="item_title">认证类型：</td>
            <td class="item_input">
                <select name="authentication_type" id="authentication_type">
                    <option value="">请选择类型</option>
                    <?php if(is_array($authent_list)): foreach($authent_list as $key=>$authent): ?><option value="<?php echo ($authent["name"]); ?>" <?php if($vo['authentication_type'] == $authent['name']): ?>selected="selected"<?php endif; ?>><?php echo ($authent["name"]); ?></option><?php endforeach; endif; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="item_title">真实姓名:</td>
            <td class="item_input"><input type="text" name="authentication_name" class="textbox" value="<?php echo ($vo["authentication_name"]); ?>"  /></td>
        </tr>
        <tr>
            <td class="item_title">联系方式:</td>
            <td class="item_input"><input type="text" name="contact" class="textbox" value="<?php echo ($vo["contact"]); ?>"  /></td>
        </tr>
        <tr>
            <td class="item_title">+v认证说明：</td>
            <td class="item_input">
                <input type="text" value="<?php echo ($vo["v_explain"]); ?>" name="v_explain" class="textbox" style="width:500px;"  maxlength="16"/>
                <span class='tip_span'>&nbsp;最大长度为16</span>
            </td>
        </tr>
	<tr>
		<td class="item_title">身份证正面:</td>
		<td class="item_input"><span>
        <div style='float:left; height:35px; padding-top:1px;'>
            <input type='hidden' value='<?php echo ($vo["identify_positive_image"]); ?>' name='identify_positive_image' id='keimg_h_identify_positive_image' />
            <div class='buttonActive' style='margin-right:5px;'>
                <div class='buttonContent'>
                    <button type='button' class='keimg ke-icon-upload_image' rel='identify_positive_image'>选择图片</button>
                </div>
            </div>
        </div>
         <a href='<?php if($vo["identify_positive_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_positive_image"]); ?><?php endif; ?>' target='_blank' id='keimg_a_identify_positive_image' ><img src='<?php if($vo["identify_positive_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_positive_image"]); ?><?php endif; ?>' id='keimg_m_identify_positive_image' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
         <div style='float:left; height:35px; padding-top:1px;'>
             <div class='buttonActive'>
                <div class='buttonContent'>
                    <img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["identify_positive_image"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='identify_positive_image' title='删除'>
                </div>
            </div>
        </div>
        </span></td>
	</tr>
	<?php if(app_conf('IDENTIFY_NAGATIVE') == 1): ?><tr>
		<td class="item_title">身份证反面:</td>
		<td class="item_input"><span>
        <div style='float:left; height:35px; padding-top:1px;'>
            <input type='hidden' value='<?php echo ($vo["identify_nagative_image"]); ?>' name='identify_nagative_image' id='keimg_h_identify_nagative_image' />
            <div class='buttonActive' style='margin-right:5px;'>
                <div class='buttonContent'>
                    <button type='button' class='keimg ke-icon-upload_image' rel='identify_nagative_image'>选择图片</button>
                </div>
            </div>
        </div>
         <a href='<?php if($vo["identify_nagative_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_nagative_image"]); ?><?php endif; ?>' target='_blank' id='keimg_a_identify_nagative_image' ><img src='<?php if($vo["identify_nagative_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_nagative_image"]); ?><?php endif; ?>' id='keimg_m_identify_nagative_image' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
         <div style='float:left; height:35px; padding-top:1px;'>
             <div class='buttonActive'>
                <div class='buttonContent'>
                    <img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["identify_nagative_image"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='identify_nagative_image' title='删除'>
                </div>
            </div>
        </div>
        </span></td>
	</tr><?php endif; ?>
	<?php if(app_conf('IDENTIFY_NAGATIVE') == 1): ?><tr>
        <td class="item_title">手持身份证正面:</td>
        <td class="item_input"><span>
        <div style='float:left; height:35px; padding-top:1px;'>
            <input type='hidden' value='<?php echo ($vo["identify_hold_image"]); ?>' name='identify_hold_image' id='keimg_h_identify_hold_image' />
            <div class='buttonActive' style='margin-right:5px;'>
                <div class='buttonContent'>
                    <button type='button' class='keimg ke-icon-upload_image' rel='identify_hold_image'>选择图片</button>
                </div>
            </div>
        </div>
         <a href='<?php if($vo["identify_hold_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_hold_image"]); ?><?php endif; ?>' target='_blank' id='keimg_a_identify_hold_image' ><img src='<?php if($vo["identify_hold_image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["identify_hold_image"]); ?><?php endif; ?>' id='keimg_m_identify_hold_image' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
         <div style='float:left; height:35px; padding-top:1px;'>
             <div class='buttonActive'>
                <div class='buttonContent'>
                    <img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["identify_hold_image"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='identify_hold_image' title='删除'>
                </div>
            </div>
        </div>
        </span></td>
    </tr><?php endif; ?>
    <?php if($show_identify_number == 1): ?><tr>
        <td class="item_title">身份证号码:</td>
        <td class="item_input"><input type="text"  id="identify_number" value="<?php echo ($vo["identify_number"]); ?>" class="textbox" name="identify_number" /></td>
    </tr><?php endif; ?>
    <?php if (defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION): ?>
        <tr>
            <td class="item_title">推荐人ID:</td>
            <td class="item_input"><input type="text" name="game_distribution_id" class="textbox" value="<?php echo ($vo["game_distribution_id"]); ?>" /></td>
        </tr>
    <?php endif ?>
    <?php if($open_society_code == 1): ?><tr>
        <td class="item_title">公会邀请码:</td>
        <td class="item_input"><input type="text" id="society_code"  name="society_code" class="textbox" value="<?php echo ($vo["society_code"]); ?>" /></td>
    </tr>
    <?php else: ?>
    <tr style="display:none;">
        <td class="item_title">公会邀请码:</td>
        <td class="item_input"><input type="text" id="society_code"  name="society_code" class="textbox" value="" /></td>
    </tr><?php endif; ?>
</table>
<script>
	$(function(){
        if($("input[name='is_ban']:checked").val()==1){
            $('#show_ban_time').hide();
        }else{
            $('#show_ban_time').show();
        }

        $("input[name='is_ban']").bind("click",function(){
            var is_ban=$(this).val();
            if(is_ban==1){
                $('#show_ban_time').hide();
            }else{
                $('#show_ban_time').show();
            }
        });

		if($("input[name='is_ban']:checked").val()==1){
			$('#ban_type').show();
		}else{
			$('#ban_type').hide();
		}

		$("input[name='is_ban']").bind("click",function(){
			var is_ban=$(this).val();
			if(is_ban==1){
				$('#ban_type').show();
			}else{
				$('#ban_type').hide();
			}
		});

        if($("input[name='is_authentication']:checked").val()>0){
            $('#identify_info_1').show();
        }else{
            $('#identify_info_1').hide();
        }

        $("input[name='is_authentication']").bind("click",function(){
            var num = $(this).val();
            if(num==0){
                $('#identify_info_1').hide();
            }else{
                $('#identify_info_1').show();
            }
        });

	});

    function submit_check(){
        if($("input[name='is_authentication']:checked").val()==2 || $("input[name='is_authentication']:checked").val()==1){
            if($('#authentication_type option:selected') .val()==''){
                alert("请选择认证类型");
                return false;
            }
            if($.trim($("input[name='authentication_name']").val())==''){
                alert("请输入真实名称");
                return false;
            }
            if($.trim($("input[name='contact']").val())==''){
                alert("请输入联系方式");
                return false;
            }
            if($.trim($("input[name='identify_positive_image']").val())==''){
                alert("请输入身份证正面");
                return false;
            }
            if($.trim($("input[name='identify_nagative_image']").val())==''){
                alert("请输入身份证反面");
                return false;
            }
            if($.trim($("input[name='identify_hold_image']").val())==''){
                alert("请输入手持身份证正面");
                return false;
            }
            var check_id = <?php echo ($show_identify_number); ?>;
            var authentication = <?php echo ($vo["is_authentication"]); ?>;
            if(authentication!=2){
                if(check_id==1 && $.trim($("input[name='identify_number']").val())==''){
                    alert("请输入身份证号码");
                    return false;
                }
            }


        }
        return true;
    }
</script>
<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="UserGeneral" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="update" />
			<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
 			<input type="hidden" name="wx_openid" value="<?php echo ($vo["wx_openid"]); ?>" />
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