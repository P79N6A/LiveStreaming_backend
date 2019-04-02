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
<!-- <script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script> -->
<?php function get_classified_image_selected($classify_image){
        return "<img src='".$classify_image."' style='height:35px;width:35px;'/>";
    }
    function classify_add($id,$classify)
	{
		if($classify['classify_type']==2&&$id!='')
		{
			return "<a href=\"javascript:classify_item('".$id."')\">二级列表</a>";

		}
	} ?>
<script>
	//编辑跳转
	function classify_item(id)
	{
		//console.log(ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=classify_item&id="+id);
		location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=classify2_list&id="+id;
	}
	function classify_item(id)
	{
		//console.log(ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=classify_item&id="+id);
		location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=classify2_list&id="+id;
	}
</script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>分类列表</div>
    <div class="search_row">
        <form name="search" action="__APP__" method="get" class="clearfix">
            <div>分类名称：<input type="text" class="textbox" name="title" value="<?php echo trim($_REQUEST['title']);?>" style="width:100px;" /></div>
            <div><input type="hidden" value="CarClassify" name="m" /><input type="hidden" value="index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" /></div>
        </form>
    </div>
<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="9" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('dataTable')"></th><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','CarClassify','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('title','<?php echo ($sort); ?>','CarClassify','index')" title="按照<?php echo L("TITLE_SHOW");?><?php echo ($sortType); ?> "><?php echo L("TITLE_SHOW");?><?php if(($order)  ==  "title"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('classify_image','<?php echo ($sort); ?>','CarClassify','index')" title="按照图标<?php echo ($sortType); ?> ">图标<?php if(($order)  ==  "classify_image"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','CarClassify','index')" title="按照<?php echo L("IS_EFFECT");?><?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?><?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','CarClassify','index')" title="按照<?php echo L("SORT");?><?php echo ($sortType); ?> "><?php echo L("SORT");?><?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_show','<?php echo ($sort); ?>','CarClassify','index')" title="按照是否首页导航栏显示<?php echo ($sortType); ?> ">是否首页导航栏显示<?php if(($order)  ==  "is_show"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('classify_type_name','<?php echo ($sort); ?>','CarClassify','index')" title="按照等级<?php echo ($sortType); ?> ">等级<?php if(($order)  ==  "classify_type_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$classify): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><input type="checkbox" name="key" class="key" value="<?php echo ($classify["id"]); ?>"></td><td><?php echo ($classify["id"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($classify["id"])); ?>')"><?php echo ($classify["title"]); ?></a></td><td><?php echo (get_classified_image_selected($classify["classify_image"],$classify['classify_image'])); ?></td><td><?php echo (get_is_effect($classify["is_effect"],$classify['id'])); ?></td><td><?php echo (get_sort($classify["sort"],$classify['id'])); ?></td><td><?php echo ($classify["is_show"]); ?></td><td><?php echo ($classify["classify_type_name"]); ?></td><td class="op_action"><div class="viewOpBox_demo"> <?php echo (classify_add($classify["id"],$classify)); ?>&nbsp;<a href="javascript:edit('<?php echo ($classify["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp;<a href="javascript: del2('<?php echo ($classify["id"]); ?>')"><?php echo L("DEL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="9" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

	<table class="dataTable">
		<tbody>
			<td colspan="5">
				<input type="button" class="button button-add" value="<?php echo L("ADD");?>" onclick="add();" />
                <input type="button" class="button button-del" value="<?php echo L("DEL");?>" onclick="del2();" />
			</td>
		</tbody>
	</table>
<div class="page"><?php echo ($page); ?></div>
</div>
<script>
    function del2(id)
    {
        if(!id)
        {
            idBox = $(".key:checked");
            if(idBox.length == 0)
            {
                alert(LANG['DELETE_EMPTY_WARNING']);
                return;
            }
            idArray = new Array();
            $.each( idBox, function(i, n){
                idArray.push($(n).val());
            });
            id = idArray.join(",");
        }
        if(confirm(LANG['CONFIRM_DELETE']))
            $.ajax({
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=foreverdelete&id="+id,
                data: "ajax=1",
                dataType: "json",
                success: function(obj){
                    $("#info").html(obj.info);
                    //if(obj.status==1)
                    alert(obj.info);
                    location.href=location.href;
                }
            });
    }
</script>
</body>
</html>