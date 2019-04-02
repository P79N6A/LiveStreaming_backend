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

<?php function live_status($status){
        if($status==1){
            return "直播中";
        }elseif($status==2){
            return "正在创建直播";
        }elseif($status==3){
            return "历史";
        }else{
            return "直播结束";
        }

	}

	function get_level($id){
		$get_level=$GLOBALS['db']->getOne("select ul.name from ".DB_PREFIX."user_level as ul left join ".DB_PREFIX."user as u on u.user_level = ul.level where u.id=".$id);
 		return $get_level;
	}
	function get_nickname($id){
		$get_nickname=$GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$id);
        return emoji_decode($get_nickname);
	}
    function get_room_type($room_type){
        if($room_type==1){
            return "私密";
        }elseif($room_type==2){
            return "聊天室";
        }elseif($room_type==3){
            return "互动聊天室";
        }else{
            return "公开";
        }
    }

    function check_video($id,$video){
        if($video['is_delete']==0){
            return "<a href=\"javascript:check_video('".$id."')\">检查视频</a>";
        }
    }
    function live_pay($is_live_pay){
    if($is_live_pay==0){
    return "否";
    }elseif($is_live_pay==1){
    return "是";
    }}

    function live_pay_type($live_pay_type,$video){
    if($video['is_live_pay']==1){
    if($live_pay_type==1){
    return "按场收费";
    }elseif($live_pay_type==0){
    return "按时收费";
    }elseif($live_pay_type==2){
    return "暂未收费";
    }
    }
    }
    function pay_list($id, $video) {
		if($video['pay_editable']==1){
			return "<a href=\"javascript:pay_list('".$id."')\">付费日志</a>";
		}
	} ?>
<script>
    function check_video(id){
        $.ajax({
            url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=check_video&id="+id,
            data: "ajax=1",
            dataType: "json",
            success: function(obj){
                alert(obj.info);
            }
        });
    }

function edit_index(id)
{
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=edit&id="+id;
}
    function del(id)
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
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=delete&id="+id,
                data: "ajax=1",
                dataType: "json",
                success: function(obj){
                    alert(obj.info);
                    func();
                    function func(){
                        if(obj.status==1){
                            location.href=location.href;
                        }
                    }
                }
            });
    }
   
	function pay_list(id){
        location.href = ROOT+"?"+VAR_MODULE+"=LivePayLog&"+VAR_ACTION+"=index&room_id="+id;
	}
    //礼物列表
    function prop_list(id){
        location.href = ROOT+"?"+VAR_MODULE+"=VideoProp&"+VAR_ACTION+"=index&room_id="+id;
    }
</script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.weebox.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/deal.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>结束的直播</div>
<div class="button_row">
</div>
<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
        <div>房间号：<input type="text" class="textbox" name="room_id" value="<?php echo trim($_REQUEST['room_id']);?>" style="width:100px;" /></div>
        <div>主播ID: <input type="text" class="textbox" name="user_id" value="<?php echo trim($_REQUEST['user_id']);?>" style="width:100px;" /></div>
		<div>主播昵称：<input type="text" class="textbox" name="nick_name" value="<?php echo trim($_REQUEST['nick_name']);?>" style="width:100px;" /></div>
        <div>话题：<select name="cate_id">
				<option value="0">全部</option>
				<?php if(is_array($cate_list)): foreach($cate_list as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($_REQUEST['cate_id'] == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["title"]); ?></option><?php endforeach; endif; ?>
			</select>
        </div>
        <div>创建时间：<span><input type="text" class="textbox" name="create_time_1" id="create_time_1" value="<?php echo ($_REQUEST['create_time_1']); ?>" onfocus="this.blur(); return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /><input type="button" class="button" id="btn_create_time_1" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /></span> - <span><input type="text" class="textbox" name="create_time_2" id="create_time_2" value="<?php echo ($_REQUEST['create_time_2']); ?>" onfocus="this.blur(); return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /><input type="button" class="button" id="btn_create_time_2" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /></span><input type="hidden" value="VideoEnd" name="m" /><input type="hidden" value="endline_index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" /></div>
	</form>
</div>
 <?php if($is_pay_live == 1): ?><!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="14" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照房间号     <?php echo ($sortType); ?> ">房间号     <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照用户ID     <?php echo ($sortType); ?> ">用户ID     <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照主播     <?php echo ($sortType); ?> ">主播     <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('title','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播标题     <?php echo ($sortType); ?> ">直播标题     <?php if(($order)  ==  "title"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('max_watch_number','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照累计观看人数     <?php echo ($sortType); ?> ">累计观看人数     <?php if(($order)  ==  "max_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('vote_number','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照<?php echo L("TICKET");?>     <?php echo ($sortType); ?> "><?php echo L("TICKET");?>     <?php if(($order)  ==  "vote_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_in','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播状态     <?php echo ($sortType); ?> ">直播状态     <?php if(($order)  ==  "live_in"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('room_type','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播类型     <?php echo ($sortType); ?> ">直播类型     <?php if(($order)  ==  "room_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_live_pay','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照是否收费     <?php echo ($sortType); ?> ">是否收费     <?php if(($order)  ==  "is_live_pay"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_pay_type','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照收费类型     <?php echo ($sortType); ?> ">收费类型     <?php if(($order)  ==  "live_pay_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照创建时间     <?php echo ($sortType); ?> ">创建时间     <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('end_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照结束时间     <?php echo ($sortType); ?> ">结束时间     <?php if(($order)  ==  "end_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('len_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播时长<?php echo ($sortType); ?> ">直播时长<?php if(($order)  ==  "len_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$video): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($video["id"]); ?></td><td><?php echo ($video["user_id"]); ?></td><td><?php echo (get_nickname($video["user_id"],$video['user_id'])); ?></td><td><?php echo ($video["title"]); ?></td><td><?php echo ($video["max_watch_number"]); ?></td><td><?php echo ($video["vote_number"]); ?></td><td><?php echo (live_status($video["live_in"],$video['live_id'])); ?></td><td><?php echo (get_room_type($video["room_type"],$video['room_type'])); ?></td><td><?php echo (live_pay($video["is_live_pay"])); ?></td><td><?php echo (live_pay_type($video["live_pay_type"],$video)); ?></td><td><?php echo (to_date($video["create_time"])); ?></td><td><?php echo (to_date($video["end_time"])); ?></td><td><?php echo ($video["len_time"]); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:contribution_list('<?php echo ($video["id"]); ?>')"><?php echo L("TICKET_CONTRIBUTION");?></a>&nbsp; <?php echo (check_video($video["id"],$video)); ?>&nbsp; <?php echo (pay_list($video["id"],$video)); ?>&nbsp;<a href="javascript:prop_list('<?php echo ($video["id"]); ?>')">礼物列表</a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="14" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

 <?php else: ?>
 <!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="12" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照房间号     <?php echo ($sortType); ?> ">房间号     <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照主播ID     <?php echo ($sortType); ?> ">主播ID     <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照主播     <?php echo ($sortType); ?> ">主播     <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('title','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播标题     <?php echo ($sortType); ?> ">直播标题     <?php if(($order)  ==  "title"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('max_watch_number','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照累计观看人数     <?php echo ($sortType); ?> ">累计观看人数     <?php if(($order)  ==  "max_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('vote_number','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照<?php echo L("TICKET");?>     <?php echo ($sortType); ?> "><?php echo L("TICKET");?>     <?php if(($order)  ==  "vote_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_in','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播状态     <?php echo ($sortType); ?> ">直播状态     <?php if(($order)  ==  "live_in"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('room_type','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播类型     <?php echo ($sortType); ?> ">直播类型     <?php if(($order)  ==  "room_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照创建时间     <?php echo ($sortType); ?> ">创建时间     <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('end_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照结束时间     <?php echo ($sortType); ?> ">结束时间     <?php if(($order)  ==  "end_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('len_time','<?php echo ($sort); ?>','VideoEnd','endline_index')" title="按照直播时长<?php echo ($sortType); ?> ">直播时长<?php if(($order)  ==  "len_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$video): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($video["id"]); ?></td><td><?php echo ($video["user_id"]); ?></td><td><?php echo (get_nickname($video["user_id"],$video['user_id'])); ?></td><td><?php echo ($video["title"]); ?></td><td><?php echo ($video["max_watch_number"]); ?></td><td><?php echo ($video["vote_number"]); ?></td><td><?php echo (live_status($video["live_in"],$video['live_id'])); ?></td><td><?php echo (get_room_type($video["room_type"],$video['room_type'])); ?></td><td><?php echo (to_date($video["create_time"])); ?></td><td><?php echo (to_date($video["end_time"])); ?></td><td><?php echo ($video["len_time"]); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:contribution_list('<?php echo ($video["id"]); ?>')"><?php echo L("TICKET_CONTRIBUTION");?></a>&nbsp; <?php echo (check_video($video["id"],$video)); ?>&nbsp;<a href="javascript:prop_list('<?php echo ($video["id"]); ?>')">礼物列表</a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="12" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 --><?php endif; ?>

	<!--<table class="dataTable">
		<tbody>
			<td colspan="12">
				<input type="button" class="button button-del" value="删除" onclick="del();" />
			</td>
		</tbody>
	</table>-->

<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>