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
<script type="text/javascript" src="__TMPL__Common/js/deal.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__Common/js/calendar/calendar.js"></script>
<div class="main">
<div class="main_title_list"><div class="list-line-ico"></div>主播列表 <a href="javascript:clear_view_count()" title="清空累计观看 ">清空累计观看</a>（必须非运营时候清除，否则导致数据库卡死）</div>
<?php function get_level($level)
         {
             $user_level = $GLOBALS['db']->getOne("select `name` from " . DB_PREFIX . "user_level where level = '" . intval($level) . "'");
             return $user_level;
         }
         function head_image($head_image)
         {
             if ($head_image == '') {
                 return "<a style='height:35px;width:35px;'/>头像未上传</a>";
             } else {
                 return "<img src='" . $head_image . "' style='height:35px;width:35px;'/>";
             }
         }
         function get_online($is_online)
         {
             if ($is_online == 1) {
                 return '是';
             } else {
                 return '否';
             }
         }

         function forbid_msg($id, $user)
         {
             if ($user['is_nospeaking'] == 1) {
                 return "<a href=\"javascript:forbid_msg('" . $id . "')\">解除im全局禁言</a>";
             } else {
                 return "<a href=\"javascript:forbid_msg('" . $id . "')\">im全局禁言</a>";
             }
         }
         function get_is_admin($is_admin)
         {
             if ($is_admin) {
                 return "是";
             } else {
                 return "否";
             }
         }
         function get_is_nospeaking($is_nospeaking)
         {
             if ($is_nospeaking) {
                 return "是";
             } else {
                 return "否";
             }
         }
         function get_distribution_log($id, $distribution_log)
         {
             if ($distribution_log) {
                 return "<a href=\"javascript:distribution_log('" . $id . "')\">分销奖励</a>";
             }
             return "";
         }
         function get_share_distribution_log($id)
         {
             return "<a href=\"javascript:distribution_log('" . $id . "')\">分销奖励</a>";
         }
         function get_distribution_user($id, $distribution_log)
         {
             if ($distribution_log) {
                 return "<a href=\"javascript:distribution_user('" . $id . "')\">分销子用户</a>";
             }
             return "";
         }
         function get_coins($id, $coins)
         {
             $open_game = intval(defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE);
             $open_diamond = intval(defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE);
             if ($open_game && !$open_diamond) {
                 return "<a href=\"javascript:coins('" . $id . "')\">游戏币管理</a>";
             }
             return "";
         }
         function weixin_distribution($id)
         {
             if (defined('WEIXIN_DISTRIBUTION') && WEIXIN_DISTRIBUTION) {
                 return "<a href=\"javascript:weixin_distribution('" . $id . "')\">微信分销</a>";
             }
             return '';
         }
         function get_game_rate($id)
         {
             if (intval(defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE) && defined('USER_GAME_RATE') && USER_GAME_RATE) {
                 {
                     return "<a href=\"javascript:game_rate('" . $id . "')\">游戏干预系数</a>";
                 }
                 return "";
             }
         }
         function get_game_distribution($id)
         {
             if (intval(defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION)) {
                 return "<a href=\"javascript:game_distribution('" . $id . "')\">游戏分销系数</a>";
             }
             return "";
         }
         function get_game_distribution_detail($id)
         {
             if (intval(defined('GAME_DISTRIBUTION') && GAME_DISTRIBUTION)) {
                 return "<a href=\"javascript:game_distribution_detail('" . $id . "')\">游戏分销记录</a>";
             }
             return "";
         }
         function get_goods($id, $goods)
         {
             if ($goods) {
                 return "<a href=\"javascript:goods('" . $id . "')\">商品管理</a>";
             }
             return "";
         }
         function forbid_game($id, $open_game)
         {
             if (intval(defined('OPEN_PLUGIN') && OPEN_PLUGIN && defined('OPEN_GAME_MODULE') && OPEN_GAME_MODULE)) {
                 if ($open_game == 0) {
                     return "<a href=\"javascript:forbid_game('" . $id . "')\">禁游戏</a>";
                 } else {
                     return "<a href=\"javascript:forbid_game('" . $id . "')\">取消禁游戏</a>";
                 }
             }
         }
         function forbid_pay($id, $open_pay)
         {
             if (intval(defined('OPEN_PLUGIN') && OPEN_PLUGIN && defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY)) {
                 if ($open_pay == 0) {
                     return "<a href=\"javascript:forbid_pay('" . $id . "')\">禁付费</a>";
                 } else {
                     return "<a href=\"javascript:forbid_pay('" . $id . "')\">取消禁付费</a>";
                 }
             }
         }
         function forbid_auction($id, $open_auction)
         {
             if (intval(defined('OPEN_PLUGIN') && OPEN_PLUGIN && defined('OPEN_PAI_MODULE') && OPEN_PAI_MODULE)) {
                 if ($open_auction == 0) {
                     return "<a href=\"javascript:forbid_auction('" . $id . "')\">禁购物竞拍</a>";
                 } else {
                     return "<a href=\"javascript:forbid_auction('" . $id . "')\">取消禁购物竞拍</a>";
                 }
             }
         }
         function invitation_code($id)
         {
             if (intval(defined('ENTER_INVITATION_CODE') && ENTER_INVITATION_CODE)) {
                 return "<a href=\"javascript:invitation_code('" . $id . "')\">邀请码</a>";
             }
             return "";
         }
         function get_open_invite_code($id)
         {
             if (intval(defined('OPEN_INVITE_CODE') && OPEN_INVITE_CODE)) {
                 return "<a href=\"javascript:invite_distribution_log('" . $id . "')\">邀请码奖励</a>";
             }
             return "";
         }
         function forbid_distribution($id, $distribution_ban)
         {
             if (intval(defined('DISTRIBUTION_SCAN') && DISTRIBUTION_SCAN)) {
                 if ($distribution_ban == 0) {
                     return "<a href=\"javascript:forbid_distribution('" . $id . "')\">禁分销</a>";
                 } else {
                     return "<a href=\"javascript:forbid_distribution('" . $id . "')\">取消禁分销</a>";
                 }
             }
         }
         function forbid_private($id, $open_private)
         {
             if (intval(defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE)) {
                 if ($open_private == 0) {
                     return "<a href=\"javascript:forbid_private('" . $id . "')\">禁私密</a>";
                 } else {
                     return "<a href=\"javascript:forbid_private('" . $id . "')\">取消禁私密</a>";
                 }
             }
         }
         function forbid_lianmai($id, $open_lianmai)
         {
             if (intval(defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE)) {
                 if ($open_lianmai == 0) {
                     return "<a href=\"javascript:forbid_lianmai('" . $id . "')\">禁连麦</a>";
                 } else {
                     return "<a href=\"javascript:forbid_lianmai('" . $id . "')\">取消禁连麦</a>";
                 }
             }
         }
         function get_max_number($id, $allow_max_number)
         {
             if (intval(defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE)) {
                 return "<a href=\"javascript:allow_max_number('" . $id . "')\">直播间人数</a>";
             }
         }

         function get_svideo($id, $user)
         {
            if (intval(defined('OPEN_CAR_MODULE') && OPEN_CAR_MODULE)) {
                 return "<a href=\"javascript:svideo('" . $id . "')\">小视频设置</a>";
            }
             return "";
         } ?>
<!-- <div class="button_row">
	<input type="button" class="button" value="<?php echo L("ADD");?>" onclick="add();" />
</div>
 -->
    <script>
        function forbid_msg($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_msg&user_id="+$id,
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
            }else{

            }
        }

        function goods(id){
            location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=goods&user_id="+id;
        }

		function clear_view_count()
	    {
	        if(confirm("确定要清空累计观看？"))
	            $.ajax({
	                url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=clear_view_count",
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
        function forbid_game($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_game&user_id="+$id,
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
            }else{

            }
        }
        function forbid_pay($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_pay&user_id="+$id,
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
            }else{

            }
        }
        function forbid_auction($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_auction&user_id="+$id,
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
            }else{

            }
        }
        function forbid_distribution($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_distribution&user_id="+$id,
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
            }else{

            }
        }
        //--['车行定制'] start--
        //禁私密
        function forbid_private($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_private&user_id="+$id,
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
            }else{

            }
        }
        //禁连麦
        function forbid_lianmai($id){
            var r=confirm("确定要修改状态？");
            if (r==true){
                $.ajax({
                    url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=forbid_lianmai&user_id="+$id,
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
            }else{

            }
        }
        //--['车行定制'] end--
    </script>
<div class="search_row">
	<form name="search" action="__APP__" method="get" class="clearfix">
	<div>主播ID：<input type="text" class="textbox" name="id" value="<?php echo trim($_REQUEST['id']);?>" style="width:100px;" /></div>
	<div>主播类型：<select name="is_authentication">
	<option value="-1" selected="selected">所有</option>
	<option value="0,1,3" <?php if($_REQUEST['is_authentication'] == 0 ): ?>selected="selected"<?php endif; ?>>普通主播</option>
	<option value="2"<?php if($_REQUEST['is_authentication'] == 2): ?>selected="selected"<?php endif; ?>>认证主播</option>
	</select></div>
    <?php if($open_vip == 1): ?><div>是否VIP：<select name="is_vip">
        <option value="" selected="selected">所有</option>
        <option value="0" <?php if($_REQUEST['is_vip'] == 0 && $_REQUEST['is_vip'] != ''): ?>selected="selected"<?php endif; ?>>否</option>
        <option value="1"<?php if($_REQUEST['is_vip'] == 1): ?>selected="selected"<?php endif; ?>>是</option>
    </select></div><?php endif; ?>
    <!-- 车行定制 ljz -->
    <?php if($open_car == 1): ?><div>主播类别：<select name="anchor_sort_id">
        <option value="" selected="selected">所有</option>
        <?php if(is_array($anchor_sort)): foreach($anchor_sort as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if($_REQUEST['anchor_sort_id'] == $vo["id"] && $_REQUEST['anchor_sort_id'] != ''): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
    </select></div><?php endif; ?>
	<div>手机号：<input type="text" class="textbox" name="mobile" value="<?php echo trim($_REQUEST['mobile']);?>" style="width:100px;" /></div>
	<div>主播昵称：<input type="text" class="textbox" name="nick_name" value="<?php echo trim($_REQUEST['nick_name']);?>" style="width:100px;" /></div>
	<div>注册时间：<span><input type="text" class="textbox" name="create_time_1" id="create_time_1" value="<?php echo ($_REQUEST['create_time_1']); ?>" onfocus="this.blur(); return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /><input type="button" class="button" id="btn_create_time_1" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_1', '%Y-%m-%d', false, false, 'btn_create_time_1');" /></span> - <span><input type="text" class="textbox" name="create_time_2" id="create_time_2" value="<?php echo ($_REQUEST['create_time_2']); ?>" onfocus="this.blur(); return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /><input type="button" class="button" id="btn_create_time_2" value="<?php echo L("SELECT_TIME");?>" onclick="return showCalendar('create_time_2', '%Y-%m-%d', false, false, 'btn_create_time_2');" /></span><input type="hidden" value="UserGeneral" name="m" /><input type="hidden" value="index" name="a" /><input type="submit" class="button" value="<?php echo L("SEARCH");?>" /></div>
	</form>
</div>
    <?php if((USER_PROP_CLOSED == 1)): ?><!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="20" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','UserGeneral','index')" title="按照主播ID         <?php echo ($sortType); ?> ">主播ID         <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('nick_name','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("NICK_NAME");?><?php echo ($sortType); ?> "><?php echo L("NICK_NAME");?><?php if(($order)  ==  "nick_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('head_image','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_HEADIMAGE");?>         <?php echo ($sortType); ?> "><?php echo L("USER_HEADIMAGE");?>         <?php if(($order)  ==  "head_image"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('diamonds','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("DIAMONDS");?>         <?php echo ($sortType); ?> "><?php echo L("DIAMONDS");?>         <?php if(($order)  ==  "diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('use_diamonds','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_DIAMONDS");?>         <?php echo ($sortType); ?> "><?php echo L("USER_DIAMONDS");?>         <?php if(($order)  ==  "use_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('ticket','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_TICKET");?>         <?php echo ($sortType); ?> "><?php echo L("USER_TICKET");?>         <?php if(($order)  ==  "ticket"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('useable_ticket','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USEABLE_TICKET");?>         <?php echo ($sortType); ?> "><?php echo L("USEABLE_TICKET");?>         <?php if(($order)  ==  "useable_ticket"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('u_score','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_SCORE");?>         <?php echo ($sortType); ?> "><?php echo L("USER_SCORE");?>         <?php if(($order)  ==  "u_score"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('focus_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_FOCUS");?><?php echo ($sortType); ?> "><?php echo L("USER_FOCUS");?><?php if(($order)  ==  "focus_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('fans_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_FANS");?><?php echo ($sortType); ?> "><?php echo L("USER_FANS");?><?php if(($order)  ==  "fans_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_level','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("LEVEL");?>         <?php echo ($sortType); ?> "><?php echo L("LEVEL");?>         <?php if(($order)  ==  "user_level"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_ban','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_BAN");?>         <?php echo ($sortType); ?> "><?php echo L("IS_BAN");?>         <?php if(($order)  ==  "is_ban"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('luck_num','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("LUCK_NUM");?>         <?php echo ($sortType); ?> "><?php echo L("LUCK_NUM");?>         <?php if(($order)  ==  "luck_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('view_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照累计观看         <?php echo ($sortType); ?> ">累计观看         <?php if(($order)  ==  "view_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('login_ip','<?php echo ($sort); ?>','UserGeneral','index')" title="按照登录IP         <?php echo ($sortType); ?> ">登录IP         <?php if(($order)  ==  "login_ip"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_EFFECT");?>         <?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?>         <?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_hot_on','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_HOT_ON");?>         <?php echo ($sortType); ?> "><?php echo L("IS_HOT_ON");?>         <?php if(($order)  ==  "is_hot_on"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_admin','<?php echo ($sort); ?>','UserGeneral','index')" title="按照是否管理员         <?php echo ($sortType); ?> ">是否管理员         <?php if(($order)  ==  "is_admin"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_nospeaking','<?php echo ($sort); ?>','UserGeneral','index')" title="按照im全局禁言<?php echo ($sortType); ?> ">im全局禁言<?php if(($order)  ==  "is_nospeaking"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($user["id"]); ?></td><td><a href="javascript:edit         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["nick_name"]); ?></a></td><td><?php echo (head_image($user["head_image"])); ?></td><td><?php echo ($user["diamonds"]); ?></td><td><?php echo ($user["use_diamonds"]); ?></td><td><?php echo ($user["ticket"]); ?></td><td><?php echo ($user["useable_ticket"]); ?></td><td><?php echo ($user["u_score"]); ?></td><td><a href="javascript:focus_list         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["focus_count"]); ?></a></td><td><a href="javascript:fans_list         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["fans_count"]); ?></a></td><td><?php echo (get_level($user["user_level"],$user['user_level'])); ?></td><td><?php echo (get_is_ban($user["is_ban"],$user['id'])); ?></td><td><?php echo ($user["luck_num"]); ?></td><td><?php echo ($user["view_count"]); ?></td><td><?php echo ($user["login_ip"]); ?></td><td><?php echo (get_is_effect($user["is_effect"],$user['id'])); ?></td><td><?php echo (get_is_hot_on($user["is_hot_on"],$user['id'])); ?></td><td><?php echo (get_is_admin($user["is_admin"])); ?></td><td><?php echo (get_is_nospeaking($user["is_nospeaking"])); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:edit('<?php echo ($user["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp; <?php echo (get_share_distribution_log($user["id"])); ?>&nbsp; <?php echo (forbid_game($user["id"],$user['open_game'])); ?>&nbsp; <?php echo (forbid_pay($user["id"],$user['open_pay'])); ?>&nbsp; <?php echo (forbid_auction($user["id"],$user['open_auction'])); ?>&nbsp; <?php echo (forbid_private($user["id"],$user['open_private'])); ?>&nbsp; <?php echo (forbid_lianmai($user["id"],$user['open_lianmai'])); ?>&nbsp; <?php echo (get_max_number($user["id"],$user['get_max_number'])); ?>&nbsp; <?php echo (get_svideo($user["id"],$user)); ?>&nbsp;<a href="javascript: account('<?php echo ($user["id"]); ?>')"><?php echo L("USER_ACCOUNT");?></a>&nbsp;<a href="javascript:account_detail('<?php echo ($user["id"]); ?>')"><?php echo L("USER_ACCOUNT_DETAIL");?></a>&nbsp;<a href="javascript:contribution_list('<?php echo ($user["id"]); ?>')"><?php echo L("TICKET_CONTRIBUTION");?></a>&nbsp;<a href="javascript:prop('<?php echo ($user["id"]); ?>')"><?php echo L("USER_PROP_DETAIL");?></a>&nbsp;<a href="javascript:closed_prop('<?php echo ($user["id"]); ?>')"><?php echo L("USER_PROP_CLOSED");?></a>&nbsp; <?php echo (forbid_msg($user["id"],$user)); ?>&nbsp; <?php echo (get_distribution_log($user["id"],$user['distribution_log'])); ?>&nbsp; <?php echo (get_distribution_user($user["id"],$user['distribution_log'])); ?>&nbsp; <?php echo (get_coins($user["id"],$user['coins'])); ?>&nbsp; <?php echo (get_game_rate($user["id"])); ?>&nbsp; <?php echo (get_game_distribution($user["id"])); ?>&nbsp; <?php echo (get_game_distribution_detail($user["id"])); ?>&nbsp; <?php echo (invitation_code($user["id"])); ?>&nbsp; <?php echo (get_goods($user["id"],$user['goods'])); ?>&nbsp; <?php echo (get_open_invite_code($user["id"])); ?>&nbsp; <?php echo (weixin_distribution($user["id"])); ?>&nbsp; <?php echo (forbid_distribution($user["id"],$user['distribution_ban'])); ?>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="20" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 -->


    <?php else: ?>
            <!-- Think 系统列表组件开始 -->
<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 ><tr><td colspan="20" class="topTd" ></td></tr><tr class="row" ><th><a href="javascript:sortBy('id','<?php echo ($sort); ?>','UserGeneral','index')" title="按照主播ID         <?php echo ($sortType); ?> ">主播ID         <?php if(($order)  ==  "id"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('nick_name','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("NICK_NAME");?><?php echo ($sortType); ?> "><?php echo L("NICK_NAME");?><?php if(($order)  ==  "nick_name"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('head_image','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_HEADIMAGE");?>         <?php echo ($sortType); ?> "><?php echo L("USER_HEADIMAGE");?>         <?php if(($order)  ==  "head_image"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('diamonds','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("DIAMONDS");?>         <?php echo ($sortType); ?> "><?php echo L("DIAMONDS");?>         <?php if(($order)  ==  "diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('use_diamonds','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_DIAMONDS");?>         <?php echo ($sortType); ?> "><?php echo L("USER_DIAMONDS");?>         <?php if(($order)  ==  "use_diamonds"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('ticket','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_TICKET");?>         <?php echo ($sortType); ?> "><?php echo L("USER_TICKET");?>         <?php if(($order)  ==  "ticket"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('useable_ticket','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USEABLE_TICKET");?>         <?php echo ($sortType); ?> "><?php echo L("USEABLE_TICKET");?>         <?php if(($order)  ==  "useable_ticket"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('u_score','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_SCORE");?>         <?php echo ($sortType); ?> "><?php echo L("USER_SCORE");?>         <?php if(($order)  ==  "u_score"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('focus_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_FOCUS");?><?php echo ($sortType); ?> "><?php echo L("USER_FOCUS");?><?php if(($order)  ==  "focus_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('fans_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("USER_FANS");?><?php echo ($sortType); ?> "><?php echo L("USER_FANS");?><?php if(($order)  ==  "fans_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_level','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("LEVEL");?>         <?php echo ($sortType); ?> "><?php echo L("LEVEL");?>         <?php if(($order)  ==  "user_level"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_ban','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_BAN");?>         <?php echo ($sortType); ?> "><?php echo L("IS_BAN");?>         <?php if(($order)  ==  "is_ban"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('luck_num','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("LUCK_NUM");?>         <?php echo ($sortType); ?> "><?php echo L("LUCK_NUM");?>         <?php if(($order)  ==  "luck_num"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('view_count','<?php echo ($sort); ?>','UserGeneral','index')" title="按照累计观看         <?php echo ($sortType); ?> ">累计观看         <?php if(($order)  ==  "view_count"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('login_ip','<?php echo ($sort); ?>','UserGeneral','index')" title="按照登录IP         <?php echo ($sortType); ?> ">登录IP         <?php if(($order)  ==  "login_ip"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_effect','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_EFFECT");?>         <?php echo ($sortType); ?> "><?php echo L("IS_EFFECT");?>         <?php if(($order)  ==  "is_effect"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_hot_on','<?php echo ($sort); ?>','UserGeneral','index')" title="按照<?php echo L("IS_HOT_ON");?>         <?php echo ($sortType); ?> "><?php echo L("IS_HOT_ON");?>         <?php if(($order)  ==  "is_hot_on"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_admin','<?php echo ($sort); ?>','UserGeneral','index')" title="按照是否管理员         <?php echo ($sortType); ?> ">是否管理员         <?php if(($order)  ==  "is_admin"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_nospeaking','<?php echo ($sort); ?>','UserGeneral','index')" title="按照im全局禁言<?php echo ($sortType); ?> ">im全局禁言<?php if(($order)  ==  "is_nospeaking"): ?><img src="__TMPL__Common/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="60px">操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$i;$mod = ($i % 2 )?><tr class="row" ><td><?php echo ($user["id"]); ?></td><td><a href="javascript:edit         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["nick_name"]); ?></a></td><td><?php echo (head_image($user["head_image"])); ?></td><td><?php echo ($user["diamonds"]); ?></td><td><?php echo ($user["use_diamonds"]); ?></td><td><?php echo ($user["ticket"]); ?></td><td><?php echo ($user["useable_ticket"]); ?></td><td><?php echo ($user["u_score"]); ?></td><td><a href="javascript:focus_list         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["focus_count"]); ?></a></td><td><a href="javascript:fans_list         ('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["fans_count"]); ?></a></td><td><?php echo (get_level($user["user_level"],$user['user_level'])); ?></td><td><?php echo (get_is_ban($user["is_ban"],$user['id'])); ?></td><td><?php echo ($user["luck_num"]); ?></td><td><?php echo ($user["view_count"]); ?></td><td><?php echo ($user["login_ip"]); ?></td><td><?php echo (get_is_effect($user["is_effect"],$user['id'])); ?></td><td><?php echo (get_is_hot_on($user["is_hot_on"],$user['id'])); ?></td><td><?php echo (get_is_admin($user["is_admin"])); ?></td><td><?php echo (get_is_nospeaking($user["is_nospeaking"])); ?></td><td class="op_action"><div class="viewOpBox_demo"><a href="javascript:edit('<?php echo ($user["id"]); ?>')"><?php echo L("EDIT");?></a>&nbsp; <?php echo (forbid_game($user["id"],$user['open_game'])); ?>&nbsp; <?php echo (forbid_pay($user["id"],$user['open_pay'])); ?>&nbsp; <?php echo (forbid_auction($user["id"],$user['open_auction'])); ?>&nbsp; <?php echo (forbid_private($user["id"],$user['open_private'])); ?>&nbsp; <?php echo (forbid_lianmai($user["id"],$user['open_lianmai'])); ?>&nbsp; <?php echo (get_max_number($user["id"],$user['get_max_number'])); ?>&nbsp; <?php echo (get_svideo($user["id"],$user)); ?>&nbsp;<a href="javascript:account('<?php echo ($user["id"]); ?>')"><?php echo L("USER_ACCOUNT");?></a>&nbsp;<a href="javascript:account_detail('<?php echo ($user["id"]); ?>')"><?php echo L("USER_ACCOUNT_DETAIL");?></a>&nbsp;<a href="javascript:contribution_list('<?php echo ($user["id"]); ?>')"><?php echo L("TICKET_CONTRIBUTION");?></a>&nbsp;<a href="javascript:prop('<?php echo ($user["id"]); ?>')"><?php echo L("USER_PROP_DETAIL");?></a>&nbsp; <?php echo (forbid_msg($user["id"],$user)); ?>&nbsp; <?php echo (get_distribution_log($user["id"],$user['distribution_log'])); ?>&nbsp; <?php echo (get_distribution_user($user["id"],$user['distribution_log'])); ?>&nbsp; <?php echo (get_coins($user["id"],$user['coins'])); ?>&nbsp; <?php echo (get_game_rate($user["id"])); ?>&nbsp; <?php echo (get_game_distribution($user["id"])); ?>&nbsp; <?php echo (get_game_distribution_detail($user["id"])); ?>&nbsp; <?php echo (invitation_code($user["id"])); ?>&nbsp; <?php echo (get_goods($user["id"],$user['goods'])); ?>&nbsp; <?php echo (get_open_invite_code($user["id"])); ?>&nbsp; <?php echo (weixin_distribution($user["id"])); ?>&nbsp;</div><a href="javascript:void(0);" class="opration"><span>操作</span><i></i></a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td colspan="20" class="bottomTd"> &nbsp;</td></tr></table>
<!-- Think 系统列表组件结束 --><?php endif; ?>

        <script type="text/javascript">
        function coins(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=Games&" + VAR_ACTION + "=addCoin&user_id=" + user_id;
            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '游戏币管理',
                        width: 600,
                        height: 260
                    });
                }
            });
        }
        function game_rate(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=game_rate&user_id=" + user_id;
            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '游戏干预系数',
                        width: 600,
                        height: 260
                    });
                }
            });
        }
        function allow_max_number(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=number&user_id=" + user_id;

            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '直播间最大人数',
                        width: 600,
                        height: 260
                    });
                }
            });
        }

        function svideo(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=svideo&user_id=" + user_id;

            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '小视频设置',
                        width: 600,
                        height: 260
                    });
                }
            });
        }

        function game_distribution(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=game_distribution&user_id=" + user_id;
            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '分销系数',
                        width: 600,
                        height: 260
                    });
                }
            });
        }
        function weixin_distribution(user_id)
        {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=weixin_distribution&user_id=" + user_id;
            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '微信分销',
                        width: 650,
                        height: 300
                    });
                }
            });
        }
        function game_distribution_detail(user_id) {
            window.location.href = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=game_distribution_detail&user_id=" + user_id;
        }
        function invitation_code(user_id) {
            var url = ROOT + "?" + VAR_MODULE + "=UserGeneral&" + VAR_ACTION + "=invitation_code&user_id=" + user_id;
            $.ajax({
                url: url,
                data: "ajax=1",
                dataType: "json",
                success: function(msg) {
                    if (msg.status == 0) {
                        alert(msg.info);
                    }
                },
                error: function() {
                    $.weeboxs.open(url, {
                        contentType: 'ajax',
                        showButton: false,
                        title: '邀请码',
                        width: 600,
                        height: 260
                    });
                }
            });
        }

        function closed_prop(id)
        {
            location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=closed_prop&id="+id;
        }
        function invite_distribution_log(id)
        {
            location.href = ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=invite_distribution_log&id="+id;
        }
    </script>

    <!-- del:<?php echo L("DEL");?>, -->
	<!--<table class="dataTable">
		<tbody>
			<td colspan="14">
				<input type="button" class="button button-del" value="<?php echo L("DEL");?>" onclick="del();" />
			</td>
		</tbody>
	</table>-->
<div class="page"><?php echo ($page); ?></div>
</div>
</body>
</html>