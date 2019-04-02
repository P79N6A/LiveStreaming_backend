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

<?php function live_status($status,$video){
		if($status==1){
			//return "<a href='__ROOT__/video.php?channelid=".$video['channelid']."' target='_blank'>直播中</a>";
            return "<a href=\"javascript:get_video_preview('".$video['id']."')\">直播中</a>";
		}elseif($status==2){
            return "正在创建直播";
        }elseif($status==3){
            return "历史";
        }else{
			return "直播结束";
		}

	}

	function push($id,$video){
		return "<a href=\"javascript:push_anchor('".$id."')\">粉丝推送</a>";
	}
	function push_all($id,$video){
		return "<a href=\"javascript:push_anchor_all('".$id."')\">全服推送</a>";
	}
	function stick($id,$video){
	if(intval(defined('OPEN_STICK') && OPEN_STICK)){
	if($video['stick']==1){
	return "<a href=\"javascript:stick('".$id."')\">取消手动置顶</a>";
	}else{
	return "<a href=\"javascript:stick('".$id."')\">手动置顶</a>";
	}}
	}
	function close($id,$video){
        if($video['live_in']==1){
            return "<a href=\"javascript:close_live('".$video['user_id']."','".$video["id"]."')\">关闭房间</a>";
        }else{
            return "<a href=\"javascript:demand_video_status('".$id."')\">下线</a>";
        }
	}

    function forbid_send_msg($id,$video){
        return "<a href=\"javascript:forbid('".$id."')\">被禁言观众</a>";
    }

	function get_level($id){
		$get_level=$GLOBALS['db']->getOne("select ul.name from ".DB_PREFIX."user_level as ul left join ".DB_PREFIX."user as u on u.user_level = ul.level where u.id=".$id);
 		return $get_level;
	}

	function get_nickname($id){
		$get_nickname=$GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$id);
 		return $get_nickname;
	}

    function get_preview($id)
    {
        return "<a href=\"javascript:get_video_preview('".$id."')\">查看</a>";
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
	function live_pay($is_live_pay){
	if($is_live_pay==0){
	return "否";
	}elseif($is_live_pay==1){
	return "是";
	}}

	function live_stick($stick){
	if($stick==0){
	return "否";
	}elseif($stick==1){
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
	function set_live_pay($id,$video){
		if($video['pay_editable']==1){
			return "<a href=\"javascript:set_live_pay('".$id."')\">付费设置</a>";
		}
	}
	function pay_list($id, $video) {
		if($video['pay_editable']==1){
			return "<a href=\"javascript:pay_list('".$id."')\">付费日志</a>";
		}
	} ?>
<script>
    function demand_video_status(id)
    {
        if(confirm("确定要修改状态？"))
            $.ajax({
                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_demand_video_status&id="+id,
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

    function forbid(id){
        location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid&id="+id;
    }

 function get_video_preview(id){
     window.open(ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=play&id="+id);
 }

function virtual(id){
	location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=list_virtual&id="+id;

}
function tipoff_list(id){
	location.href = ROOT+"?"+VAR_MODULE+"=Tipoff&"+VAR_ACTION+"=index&video_id="+id;
}
function stick(id){
		$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=stick&id="+id,
			data: "",
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
function push_anchor(id){
	$.ajax({
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=push_anchor&id="+id,
		data: "",
		dataType: "json",
		success: function(obj){
			console.log(obj);
			$("#info").html(obj.info);
			if(obj.status==1){
				if(obj.info){
					alert(obj.info);
				}
				else{
					alert('操作成功');
				}
			}
			else{
				if(obj.info){
					alert(obj.info);
				}
				else{
					alert('操作成功');
				}
			}
		}
	});
}
function push_anchor_all(id){
	  	$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=push_anchor_all&id="+id,
			data: "",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1){
					if(obj.info){
						alert(obj.info);
					}
					else{
						alert('操作成功');
					}
				}
				else{
					if(obj.info){
						alert(obj.info);
					}
					else{
						alert('操作成功');
					}
				}
			}
		});
}
function refresh(){
  $(document).ready(function(){
       window.location.reload();
  });
}
function close_live($user_id,$room_id){
	var r=confirm("确定关闭这个直播？？");
	if (r==true){
		$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=close_live&user_id="+$user_id+"&room_id="+$room_id,
			data: "",
			dataType: "json",
			success: function(obj){
				$("#info").html(obj.info);
				if(obj.status==1){
					if(obj.info){
						alert(obj.info);
						refresh();
					}
					else{
						alert('操作成功');
						refresh();
					}
				}
				else{
					if(obj.info){
						alert(obj.info);
						refresh();
					}
					else{
						alert('操作成功');
						refresh();
					}
				}
			}
		});
	 }else{

	}
}
    //直播设置
    function video_set(id)
    {
        $.ajax({
            url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=video_set&id="+id,
            data: "ajax=1",
            dataType: "json",
            success: function(msg){
                if(msg.status==0){
                    alert(msg.info);
                }
            },
            error: function(){
                $.weeboxs.open(ROOT+'?'+VAR_MODULE+'='+MODULE_NAME+'&'+VAR_ACTION+'=video_set&id='+id, {contentType:'ajax',showButton:false,title:LANG['USER_VIDEO_SET'],width:600,height:260});
            }
        });

    }
	//推送地址
	function push_url(id)
	{
		$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=push_url&id="+id,
			data: "ajax=1",
			dataType: "json",
			success: function(msg){
				if(msg.status==0){
					alert(msg.info);
				}
			},
			error: function(){
				$.weeboxs.open(ROOT+'?'+VAR_MODULE+'='+MODULE_NAME+'&'+VAR_ACTION+'=push_url&id='+id, {contentType:'ajax',showButton:false,title:'推流地址',width:1024,height:300});
			}
		});

	}

	function set_live_pay(id){
		$.ajax({
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=set_live_pay&id="+id,
			data: "ajax=1",
			dataType: "json",
			success: function(msg){
				if(msg.status==0){
					alert(msg.info);
				}
			},
			error: function(){
				$.weeboxs.open(ROOT+'?'+VAR_MODULE+'='+MODULE_NAME+'&'+VAR_ACTION+'=set_live_pay&id='+id, {contentType:'ajax',showButton:false,title:'付费设置',width:600,height:200});
			}
		});
	}
	//付费日志
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
<script type="text/javascript" src="__TMPL__Common/js/user.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>直播中视频 <a href="/<?php echo ($url_name); ?>?m=Video&a=online_index&&">刷新</a></div>
<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
		<div>主播ID: <input type="text" class="textbox" name="user_id" value="<?php echo trim($_REQUEST['user_id']);?>" style="width:100px;" /></div>
		<div>主播昵称：<input type="text" class="textbox" name="nick_name" value="<?php echo trim($_REQUEST['nick_name']);?>" style="width:100px;" /></div>
		<div>话题：<select name="cate_id">
				<option value="0">全部</option>
				<?php if(is_array($cate_list)): foreach($cate_list as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($_REQUEST['cate_id'] == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item["title"]); ?></option><?php endforeach; endif; ?>
			</select></div>
		<div>分类：<select name="classified_id">
			<option value="0">全部</option>
			<?php if(is_array($classified_list)): foreach($classified_list as $key=>$classified_item): ?><option value="<?php echo ($classified_item["id"]); ?>" <?php if($_REQUEST['classified_id'] == $classified_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($classified_item["title"]); ?></option><?php endforeach; endif; ?>
		</select></div>
		<div>创建时间：<span><input type="text" class="textbox" name="create_time_1" id="create_time_1" value="<?php echo ($_REQUEST['create_time_1']); ?>" onfocus="this.blur(); return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /><input type="button" class="button" id="btn_create_time_1" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /></span> - <span><input type="text" class="textbox" name="create_time_2" id="create_time_2" value="<?php echo ($_REQUEST['create_time_2']); ?>" onfocus="this.blur(); return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /><input type="button" class="button" id="btn_create_time_2" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /></span><input type="hidden" value="Video" name="m" /><input type="hidden" value="online_index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" /></div>
		<input style="margin-top: 5px;" type="button" class="button button-add" value="赠送红包" onclick="location.href='<?php echo u("Video/add");?>';" />
	</form>
</div>

<?php if($is_pay_live == 1): ?><!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="20" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Video','online_index')" title="按照房间号      <?php echo ($sortType); ?> ">房间号      <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Video','online_index')" title="按照主播ID   <?php echo ($sortType); ?> ">主播ID   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Video','online_index')" title="按照主播   <?php echo ($sortType); ?> ">主播   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="50px   "><a href="javascript:sortBy('vote_number','<?php echo ($sort); ?>','Video','online_index')" title="按照<?php echo L("TICKET");?><?php echo ($sortType); ?> "><?php echo L("TICKET");?><?php if(($order)  ==  "vote_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('all_watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照前端显示人数   <?php echo ($sortType); ?> ">前端显示人数   <?php if(($order)  ==  "all_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照实际观看人数   <?php echo ($sortType); ?> ">实际观看人数   <?php if(($order)  ==  "watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('max_watch','<?php echo ($sort); ?>','Video','online_index')" title="按照洪峰观看人数   <?php echo ($sortType); ?> ">洪峰观看人数   <?php if(($order)  ==  "max_watch"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('virtual_watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照当前机器人数   <?php echo ($sortType); ?> ">当前机器人数   <?php if(($order)  ==  "virtual_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('robot_num','<?php echo ($sort); ?>','Video','online_index')" title="按照当前机器人头数量   <?php echo ($sortType); ?> ">当前机器人头数量   <?php if(($order)  ==  "robot_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_in','<?php echo ($sort); ?>','Video','online_index')" title="按照直播状态<?php echo ($sortType); ?> ">直播状态<?php if(($order)  ==  "live_in"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('room_type','<?php echo ($sort); ?>','Video','online_index')" title="按照直播类型   <?php echo ($sortType); ?> ">直播类型   <?php if(($order)  ==  "room_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_live_pay','<?php echo ($sort); ?>','Video','online_index')" title="按照是否收费   <?php echo ($sortType); ?> ">是否收费   <?php if(($order)  ==  "is_live_pay"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_pay_type','<?php echo ($sort); ?>','Video','online_index')" title="按照收费类型   <?php echo ($sortType); ?> ">收费类型   <?php if(($order)  ==  "live_pay_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','Video','online_index')" title="按照创建时间   <?php echo ($sortType); ?> ">创建时间   <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('tipoff_count','<?php echo ($sort); ?>','Video','online_index')" title="按照举报次数<?php echo ($sortType); ?> ">举报次数<?php if(($order)  ==  "tipoff_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('monitor_time','<?php echo ($sort); ?>','Video','online_index')" title="按照心跳时间   <?php echo ($sortType); ?> ">心跳时间   <?php if(($order)  ==  "monitor_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_recommend','<?php echo ($sort); ?>','Video','online_index')" title="按照推荐   <?php echo ($sortType); ?> ">推荐   <?php if(($order)  ==  "is_recommend"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','Video','online_index')" title="按照<?php echo L("SORT");?>   <?php echo ($sortType); ?> "><?php echo L("SORT");?>   <?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort_num','<?php echo ($sort); ?>','Video','online_index')" title="按照热门<?php echo ($sortType); ?> ">热门<?php if(($order)  ==  "sort_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$video): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($video["id"]); ?></td><td><?php echo ($video["user_id"]); ?></td><td><?php echo (get_nickname($video["user_id"],$video['user_id'])); ?></td><td><?php echo ($video["vote_number"]); ?></td><td><?php echo ($video["all_watch_number"]); ?></td><td><?php echo ($video["watch_number"]); ?></td><td><?php echo ($video["max_watch"]); ?></td><td><?php echo ($video["virtual_watch_number"]); ?></td><td><?php echo ($video["robot_num"]); ?></td><td><a href="javascript:preview   ('<?php echo (addslashes($video["id"])); ?>')"><?php echo (live_status($video["live_in"],$video)); ?></a></td><td><?php echo (get_room_type($video["room_type"],$video['room_type'])); ?></td><td><?php echo (live_pay($video["is_live_pay"])); ?></td><td><?php echo (live_pay_type($video["live_pay_type"],$video)); ?></td><td><?php echo (to_date($video["create_time"])); ?></td><td><a href="javascript:tipoff_list   ('<?php echo (addslashes($video["id"])); ?>')"><?php echo ($video["tipoff_count"]); ?></a></td><td><?php echo ($video["monitor_time"]); ?></td><td><?php echo (get_recommend($video["is_recommend"],$video['id'])); ?></td><td><?php echo (get_sort($video["sort"],$video['id'])); ?></td><td><?php echo ($video["sort_num"]); ?></td><td class="op_action"><div class="viewOpBox_demo"> <?php echo (set_live_pay($video["id"],$video)); ?>&nbsp; <?php echo (pay_list($video["id"],$video)); ?>&nbsp; <?php echo (get_preview($video["id"],$video)); ?>&nbsp;<a href="javascript:prop_list('<?php echo ($video["id"]); ?>')">礼物列表</a>&nbsp;<a href="javascript:video_set('<?php echo ($video["id"]); ?>')"><?php echo L("USER_VIDEO_SET");?></a>&nbsp; <?php echo (push($video["id"],$video)); ?>&nbsp; <?php echo (push_all($video["id"],$video)); ?>&nbsp;<a href="javascript:equipment_info('<?php echo ($video["id"]); ?>')"><?php echo L("EQUIPMENT_INFO");?></a>&nbsp; <?php echo (stick($video["id"],$video)); ?>&nbsp; <?php echo (close($video["id"],$video)); ?>&nbsp;<a href="javascript:push_url('<?php echo ($video["id"]); ?>')"><?php echo L("PUSH_URL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="20" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->

<?php else: ?>
	<!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="19" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','Video','online_index')" title="按照房间号      <?php echo ($sortType); ?> ">房间号      <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Video','online_index')" title="按照主播ID   <?php echo ($sortType); ?> ">主播ID   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_id','<?php echo ($sort); ?>','Video','online_index')" title="按照主播   <?php echo ($sortType); ?> ">主播   <?php if(($order)  ==  "user_id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="50px   "><a href="javascript:sortBy('vote_number','<?php echo ($sort); ?>','Video','online_index')" title="按照<?php echo L("TICKET");?><?php echo ($sortType); ?> "><?php echo L("TICKET");?><?php if(($order)  ==  "vote_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('all_watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照前端显示人数   <?php echo ($sortType); ?> ">前端显示人数   <?php if(($order)  ==  "all_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照实际观看人数   <?php echo ($sortType); ?> ">实际观看人数   <?php if(($order)  ==  "watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('max_watch','<?php echo ($sort); ?>','Video','online_index')" title="按照洪峰观看人数   <?php echo ($sortType); ?> ">洪峰观看人数   <?php if(($order)  ==  "max_watch"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('virtual_watch_number','<?php echo ($sort); ?>','Video','online_index')" title="按照当前机器人数   <?php echo ($sortType); ?> ">当前机器人数   <?php if(($order)  ==  "virtual_watch_number"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('robot_num','<?php echo ($sort); ?>','Video','online_index')" title="按照当前机器人头数量   <?php echo ($sortType); ?> ">当前机器人头数量   <?php if(($order)  ==  "robot_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('live_in','<?php echo ($sort); ?>','Video','online_index')" title="按照直播状态<?php echo ($sortType); ?> ">直播状态<?php if(($order)  ==  "live_in"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('room_type','<?php echo ($sort); ?>','Video','online_index')" title="按照直播类型   <?php echo ($sortType); ?> ">直播类型   <?php if(($order)  ==  "room_type"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','Video','online_index')" title="按照创建时间   <?php echo ($sortType); ?> ">创建时间   <?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('tipoff_count','<?php echo ($sort); ?>','Video','online_index')" title="按照举报次数<?php echo ($sortType); ?> ">举报次数<?php if(($order)  ==  "tipoff_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('monitor_time','<?php echo ($sort); ?>','Video','online_index')" title="按照心跳时间   <?php echo ($sortType); ?> ">心跳时间   <?php if(($order)  ==  "monitor_time"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_recommend','<?php echo ($sort); ?>','Video','online_index')" title="按照推荐   <?php echo ($sortType); ?> ">推荐   <?php if(($order)  ==  "is_recommend"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_hot_on','<?php echo ($sort); ?>','Video','online_index')" title="按照<?php echo L("IS_HOT_ON");?>   <?php echo ($sortType); ?> "><?php echo L("IS_HOT_ON");?>   <?php if(($order)  ==  "is_hot_on"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','Video','online_index')" title="按照<?php echo L("SORT");?>   <?php echo ($sortType); ?> "><?php echo L("SORT");?>   <?php if(($order)  ==  "sort"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sort_num','<?php echo ($sort); ?>','Video','online_index')" title="按照热门<?php echo ($sortType); ?> ">热门<?php if(($order)  ==  "sort_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$video): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($video["id"]); ?></td><td><?php echo ($video["user_id"]); ?></td><td><?php echo (get_nickname($video["user_id"],$video['user_id'])); ?></td><td><?php echo ($video["vote_number"]); ?></td><td><?php echo ($video["all_watch_number"]); ?></td><td><?php echo ($video["watch_number"]); ?></td><td><?php echo ($video["max_watch"]); ?></td><td><?php echo ($video["virtual_watch_number"]); ?></td><td><?php echo ($video["robot_num"]); ?></td><td><a href="javascript:preview   ('<?php echo (addslashes($video["id"])); ?>')"><?php echo (live_status($video["live_in"],$video)); ?></a></td><td><?php echo (get_room_type($video["room_type"],$video['room_type'])); ?></td><td><?php echo (to_date($video["create_time"])); ?></td><td><a href="javascript:tipoff_list   ('<?php echo (addslashes($video["id"])); ?>')"><?php echo ($video["tipoff_count"]); ?></a></td><td><?php echo ($video["monitor_time"]); ?></td><td><?php echo (get_recommend($video["is_recommend"],$video['id'])); ?></td><td><?php echo (get_is_hot_on($video["is_hot_on"],$video['user_id'])); ?></td><td><?php echo (get_sort($video["sort"],$video['id'])); ?></td><td><?php echo ($video["sort_num"]); ?></td><td class="op_action"><div class="viewOpBox_demo"> <?php echo (set_live_pay($video["id"],$video)); ?>&nbsp; <?php echo (get_preview($video["id"],$video)); ?>&nbsp;<a href="javascript:prop_list('<?php echo ($video["id"]); ?>')">礼物列表</a>&nbsp;<a href="javascript:video_set('<?php echo ($video["id"]); ?>')"><?php echo L("USER_VIDEO_SET");?></a>&nbsp; <?php echo (push($video["id"],$video)); ?>&nbsp; <?php echo (push_all($video["id"],$video)); ?>&nbsp;<a href="javascript:equipment_info('<?php echo ($video["id"]); ?>')"><?php echo L("EQUIPMENT_INFO");?></a>&nbsp; <?php echo (stick($video["id"],$video)); ?>&nbsp; <?php echo (close($video["id"],$video)); ?>&nbsp;<a href="javascript:push_url('<?php echo ($video["id"]); ?>')"><?php echo L("PUSH_URL");?></a>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="19" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 --><?php endif; ?>

<div class="page"><?php echo ($page); ?></div><!--(数值越大在app热门直播中越靠前)  ,vote_number:<?php echo L("TICKET");?>-->
</div>
</body>
</html>