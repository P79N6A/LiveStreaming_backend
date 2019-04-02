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
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<script type="text/javascript">
function memcache()
{
	var cache = $("select[name='CACHE_TYPE']").val();
	if(cache=='Memcached')
	$("input[name='MEMCACHE_HOST']").parent().parent().show();
	else
	$("input[name='MEMCACHE_HOST']").parent().parent().hide();
}
$(document).ready(function(){
	$("select[name='CACHE_TYPE']").bind("change",function(){
		memcache();
	});
	memcache();

	function society_info(type){

		$("select[name='society_profit_platform']").parent().parent().show();
		$("input[name='society_profit_ratio']").parent().parent().show();
		$("input[name='society_public_rate']").parent().parent().show();
		$("input[name='society_user_rate']").parent().parent().show();

		$("input[name='society_lv_videotime']").parent().parent().show();
		$("input[name='society_lv_contribution']").parent().parent().show();
		$("select[name='open_society_code']").parent().parent().show();
		$("input[name='society_list_name']").parent().parent().show();

		if(type == 1){
			$("input[name='society_public_rate']").parent().parent().hide();
			$("input[name='society_user_rate']").parent().parent().hide();
		}else if(type == 2){
			$("select[name='society_profit_platform']").parent().parent().hide();
			$("input[name='society_profit_ratio']").parent().parent().hide();
		}else{
			$("select[name='society_profit_platform']").parent().parent().hide();
			$("input[name='society_profit_ratio']").parent().parent().hide();
			$("input[name='society_public_rate']").parent().parent().hide();
			$("input[name='society_user_rate']").parent().parent().hide();

			$("input[name='society_lv_videotime']").parent().parent().hide();
			$("input[name='society_lv_contribution']").parent().parent().hide();
			$("select[name='open_society_code']").parent().parent().hide();
			$("input[name='society_list_name']").parent().parent().hide();
		}
	}


	society_info($("select[name='society_pattern']").val());

	$("select[name='society_pattern']").bind("change",function(){
		society_info($(this).val());
	})
});
</script>
<div class="main">
<div class="main_title"><?php echo L("CONF_MOBILE");?></div>
<div class="search_row">
	<?php if(is_array($conf)): foreach($conf as $key=>$conf_group): ?><input type="button" class="button button-add conf_btn" rel="<?php echo ($key); ?>" value="<?php echo ($key); ?>" <?php if(($key == 6 && INVEST_TYPE == 1) or ($key == 7 && HOUSE_TYPE == 0 && SELFLESS_TYPE == 0 && ESTATE_TYPE == 0) or ($key == 8 && LICAI_TYPE == 0)): ?>style="display:none;"<?php endif; ?>  /><?php endforeach; endif; ?>
</div>
<form method='post' id="form" name="form" action="__APP__" enctype="multipart/form-data" onsubmit="return submit_check();">
	<?php if(is_array($conf)): foreach($conf as $key=>$conf_group): ?><table class="form conf_tab" cellpadding=0 cellspacing=0 rel="<?php echo ($key); ?>" <?php if($key == 6&& INVEST_TYPE == 1 ): ?>style="display:none;"<?php endif; ?>  >
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<?php if(is_array($conf_group)): foreach($conf_group as $key=>$conf_item): ?><tr <?php if($conf_item['name'] == 'DB_VOL_MAXSIZE'): ?>style="display:none;"<?php endif; ?> <?php if(($conf_item["name"] == 'INVEST_STATUS'&& INVEST_TYPE > 0) or ($conf_item["name"] == 'IS_FINANCE'&& FINANCE_TYPE == 0) or ($conf_item["name"] == 'IS_SELFLESS'&& SELFLESS_TYPE == 0) or ($conf_item["name"] == 'IS_HOUSE' && HOUSE_TYPE == 0)  or ($conf_item["name"] == 'STOCK_TRANSFER_IS_VERIFY' && INVEST_TYPE == 1)  or ($conf_item["name"] == 'STOCK_TRANSFER_COMMISION' && INVEST_TYPE == 1)  or ($conf_item["name"] == 'IS_STOCK_TRANSFER' && INVEST_TYPE == 1) or ($conf_item["name"] == 'IS_ESTATE' && ESTATE_TYPE == 0)): ?>style="display:none;"<?php endif; ?> <?php if(($conf_item["code"] == 'lucky_num' && OPEN_LUCK_NUM == 1) or ($conf_item["code"] == 'open_share_ticket' && OPEN_SHARE_EXPERIENCE == 0) or ($conf_item["code"] == 'share_ticket' && OPEN_SHARE_EXPERIENCE == 0)): ?>style="display:none;"<?php endif; ?> >
			<td class="item_title"><?php echo ($conf_item['title']); ?>:</td>
			<td class="item_input">
				<?php if($conf_item['type'] == 1): ?><div  style='margin-bottom:5px; '><textarea id='<?php echo ($conf_item["code"]); ?>' name='<?php echo ($conf_item["code"]); ?>' class='ketext' style=' height:150px;width:750px;' ><?php echo ($conf_item["val"]); ?></textarea> </div><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 2): ?><span>
        <div style='float:left; height:35px; padding-top:1px;'>
            <input type='hidden' value='<?php echo ($conf_item["val"]); ?>' name='<?php echo ($conf_item["code"]); ?>' id='keimg_h_<?php echo ($conf_item["code"]); ?>' />
            <div class='buttonActive' style='margin-right:5px;'>
                <div class='buttonContent'>
                    <button type='button' class='keimg ke-icon-upload_image' rel='<?php echo ($conf_item["code"]); ?>'>选择图片</button>
                </div>
            </div>
        </div>
         <a href='<?php if($conf_item["val"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($conf_item["val"]); ?><?php endif; ?>' target='_blank' id='keimg_a_<?php echo ($conf_item["code"]); ?>' ><img src='<?php if($conf_item["val"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($conf_item["val"]); ?><?php endif; ?>' id='keimg_m_<?php echo ($conf_item["code"]); ?>' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
         <div style='float:left; height:35px; padding-top:1px;'>
             <div class='buttonActive'>
                <div class='buttonContent'>
                    <img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($conf_item["val"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='<?php echo ($conf_item["code"]); ?>' title='删除'>
                </div>
            </div>
        </div>
        </span><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 0): ?><input type="text" class="textbox" name="<?php echo ($conf_item["code"]); ?>" value="<?php echo ($conf_item["val"]); ?>" /><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 3): ?><textarea class="textbox " name="<?php echo ($conf_item["code"]); ?>"  style="height:100px;width:250px;"><?php echo ($conf_item["val"]); ?></textarea><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 4): ?><select name="<?php echo ($conf_item["code"]); ?>">
						<?php if(is_array($conf_item["value_scope"])): foreach($conf_item["value_scope"] as $preset_key=>$preset_value): ?><option value="<?php echo ($preset_value); ?>" <?php if($conf_item['val'] == $preset_value): ?>selected="selected"<?php endif; ?>>
								<?php echo ($conf_item['title_scope'][$preset_key]); ?>
							</option><?php endforeach; endif; ?>
					</select><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 5): ?><select name="<?php echo ($conf_item["code"]); ?>">
						<?php if(is_array($is_limit_time_h)): foreach($is_limit_time_h as $key=>$h_value): ?><option value="<?php echo ($h_value); ?>" <?php if($conf_item["val"] == $h_value): ?>selected="selected"<?php endif; ?>>
								<?php echo ($h_value); ?>
							</option><?php endforeach; endif; ?>
					</select>
					<?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 6): ?><input type="text" class="textbox" name="<?php echo ($conf_item["code"]); ?>" value="<?php echo ($conf_item["val"]); ?>" disabled/><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
				<?php if($conf_item['type'] == 7): ?><input type="text" class="textbox" name="<?php echo ($conf_item["code"]); ?>" id="_time_<?php echo ($conf_item["code"]); ?>" value="<?php echo ($conf_item["val"]); ?>" onfocus="return showCalendar('_time_<?php echo ($conf_item["code"]); ?>', '%Y-%m-%d %H:%M:%S', false, false, '_btn_time_<?php echo ($conf_item["code"]); ?>');" readonly="readonly" /><input type="button" class="button" id="_btn_time_<?php echo ($conf_item["code"]); ?>" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('_time_<?php echo ($conf_item["code"]); ?>', '%Y-%m-%d %H:%M:%S', false, false, '_btn_time_<?php echo ($conf_item["code"]); ?>');" /><?php if($conf_item['desc'] != ''): ?><span style="color:#999;font-size:12px;">&nbsp;<?php echo ($conf_item["desc"]); ?></span><?php endif; ?><?php endif; ?>
			</td>

		</tr><?php endforeach; endif; ?>
		<tr>
			<td class="item_title">APP下载链接:</td>
			<td class="item_input">
				<span><?php echo ($domain_url); ?>/appdown.php</span>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table><?php endforeach; endif; ?>
	<div class="blank5"></div>
	<table class="form" cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=2 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="Conf" />
			<input type="hidden" name="<?php echo conf("VAR_ACTION");?>" value="savemobile" />
			<!--隐藏元素-->
			<input type="submit" class="button button-add" value="<?php echo L("EDIT");?>" /><input type="reset" class="button button-del" value="<?php echo L("RESET");?>" />
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table>
</form>
    <script>

        function submit_check(){
           /* if($("select[name='sina_app_api']").val()==1){
                if($("select[name='has_sina_login']").val()==0){
                    alert("必须支持新浪登录才能支持新浪分享");
                    return false;
                }
            }
            if($("select[name='qq_app_api']").val()==1){
                if($("select[name='has_qq_login']").val()==0){
                    alert("必须支持qq登录才能支持qq分享");
                    return false;
                }
            }
            if($("select[name='wx_app_api']").val()==1){
                if($("select[name='has_wx_login']").val()==0){
                    alert("必须支持微信登录才能支持微信分享");
                    return false;
                }
            }

            if($("select[name='has_sina_login']").val()==0){
                $("select[name='sina_app_api']").val(0);
            }
            if($("select[name='has_qq_login']").val()==0){
                $("select[name='qq_app_api']").val(0);
            }
            if($("select[name='has_wx_login']").val()==0){
                $("select[name='wx_app_api']").val(0);
            }*/
            return true;
        }
    </script>
<div class="blank5"></div>
</div>
</body>
</html>