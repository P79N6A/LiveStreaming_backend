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
<div class="main_title"><?php echo L("EDIT");?> <a href="<?php echo u("IndexImage/index");?>" class="back_list"><?php echo L("BACK_LIST");?></a></div>
<div class="blank5"></div>
<form name="edit" action="__APP__" method="post" enctype="multipart/form-data">
<table class="form" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=2 class="topTd"></td>
	</tr>
	<tr>
		<td class="item_title">标题:</td>
		<td class="item_input"><input type="text" class="textbox require" name="title" value="<?php echo ($vo["title"]); ?>" /></td>
	</tr>
	<tr>
		<td class="item_title">图片:</td>
		<td class="item_input"><span>
        <div style='float:left; height:35px; padding-top:1px;'>
            <input type='hidden' value='<?php echo ($vo["image"]); ?>' name='image' id='keimg_h_image' />
            <div class='buttonActive' style='margin-right:5px;'>
                <div class='buttonContent'>
                    <button type='button' class='keimg ke-icon-upload_image' rel='image'>选择图片</button>
                </div>
            </div>
        </div>
         <a href='<?php if($vo["image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["image"]); ?><?php endif; ?>' target='_blank' id='keimg_a_image' ><img src='<?php if($vo["image"] == ''): ?>./admin/Tpl/default/Common/images/no_pic.gif<?php else: ?><?php echo ($vo["image"]); ?><?php endif; ?>' id='keimg_m_image' width=35 height=35 style='float:left; border:#ccc solid 1px; margin-left:5px;' /></a>
         <div style='float:left; height:35px; padding-top:1px;'>
             <div class='buttonActive'>
                <div class='buttonContent'>
                    <img src='/admin/Tpl/default/Common/images/del.gif' style='<?php if($vo["image"] == ''): ?>display:none<?php endif; ?>; margin-left:10px; float:left; border:#ccc solid 1px; width:35px; height:35px; cursor:pointer;' class='keimg_d' rel='image' title='删除'>
                </div>
            </div>
        </div>
        </span>
            <span class="tip_span" id="tip_span">&nbsp;[启动广告图标规格为：750px*1334px][其他图片规格为：828px*240px]</span>
		</td>
	</tr>
    <tr>
        <td class="item_title">显示位置:</td>
        <td class="item_input">
            <select name="show_position" id="position">
                <?php if(is_array($position)): foreach($position as $k=>$position_item): ?><option value="<?php echo ($k); ?>" <?php if($vo['show_position'] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($position_item); ?></option><?php endforeach; endif; ?>
            </select>

        </td>
    </tr>
	<tr  id="select_type">
		<td class="item_title">类型:</td>
		<td class="item_input">
			<select name="type" id="type">
				<?php if(is_array($type_list)): foreach($type_list as $k=>$type_item): ?><option value="<?php echo ($k); ?>" <?php if($vo['type'] == $k): ?>selected="selected"<?php endif; ?> ><?php echo ($type_item); ?></option><?php endforeach; endif; ?>
			</select>
		</td>
	</tr>
    <tr  id="url">
        <td class="item_title">链接:</td>
        <td class="item_input"><input type="text" class="textbox" name="url" value="<?php echo ($vo["url"]); ?>" /></td>
    </tr>
    <tr id="family_id" <?php if($vo['type'] != 1): ?>style="display: none"<?php endif; ?>>
        <td class="item_title">家族:</td>
        <td class="item_input">
            <select name="show_id" style="width:300px">
                <?php if(is_array($family)): foreach($family as $k=>$f_item): ?><option value="<?php echo ($f_item["id"]); ?>" <?php if($vo['show_id'] == $f_item['id']): ?>selected="selected"<?php endif; ?> ><?php echo ($f_item["name"]); ?></option><?php endforeach; endif; ?>
            </select>
        </td>
    </tr>
	<tr id="edu_show_id" style="display: none">
        <td class="item_title" id="edu_show_id_title" >房间id:</td>
        <td class="item_input">
           <input type="text" class="textbox" name="show_id" value="<?php echo ($vo["show_id"]); ?>" />
        </td>
    </tr>
	<tr>
		<td class="item_title"><?php echo L("SORT");?>:</td>
		<td class="item_input"><input type="text" class="textbox" name="sort" value="<?php echo ($vo["sort"]); ?>" /></td>
	</tr>
	
	<tr>
		<td class="item_title"></td>
		<td class="item_input">
			<!--隐藏元素-->
			<input type="hidden" name="<?php echo conf("VAR_MODULE");?>" value="IndexImage" />
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
    <script>
        $(function(){
            var type = $('#type option:selected').val();
            var position = $('#position option:selected').val();
            if(type==1){
                $('#family_id').show();
            }else{
                $('#family_id').hide();
            }

            if(type==0){
                $("#url").show();
            }else{
                $("#url").hide();
            }
			
			if(type>=6 && type<=9){
                    $('#edu_show_id').show();
                    $('#edu_show_id input').attr('name','show_id');
					if( type==8)
						$('#edu_show_id_title').html("房间号：");
					else if( type==6)
						$('#edu_show_id_title').html("机构id：");
                    else if( type==9)
                        $('#edu_show_id_title').html("课程id：");
					else
						$('#edu_show_id_title').html("会员id：");
						
                }else{
                    $('#edu_show_id').hide();
                    $('#edu_show_id input').removeAttr('name');
                }


            if(position == 3){
                $("#select_type").hide();
                $("#type").val(0);
                $("#url").show();
            }else{
                $("#select_type").show();
            }

            $("#type").change(function(){
                type = $(this).val();
                if(type==1){
                    $('#family_id').show();
                }else{
                    $('#family_id').hide();
                }

                if(type!=0){
                    $("#url").hide();
                    $("input[name='url']").val('');
                }else{
                    $("#url").show();
                }
				
				if(type>=6 && type<=9){
                    $('#edu_show_id').show();
                    $('#edu_show_id input').attr('name','show_id');
					if( type==8)
						$('#edu_show_id_title').html("房间号：");
					else if( type==6)
						$('#edu_show_id_title').html("机构id：");
                    else if( type==9)
                        $('#edu_show_id_title').html("课程id：");
					else
						$('#edu_show_id_title').html("会员id：");
						
                }else{
                    $('#edu_show_id').hide();
                    $('#edu_show_id input').removeAttr('name');
                }
            });

            $("#position").change(function(){
                position = $(this).val();
                if(position == 3){
                    $("#select_type").hide();
                    $("#type").val(0);
                    $("#url").show();
                }else{
                    type = $('#type option:selected').val();
                    if(type==0){
                        $("#url").show();
                    }else{
                        $("#url").hide();
                        $("input[name='url']").val('');
                    }
                    $("#select_type").show();
                }
            });

        });
    </script>
</form>
</div>
</body>
</html>