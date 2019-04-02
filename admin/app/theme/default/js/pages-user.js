// 提示加入或创建家族
var vm = avalon.define({
    $id: "uc_nav",
    tip_nofamily: function() {
    	var html_tip_nofamily = '<div class="m-create-family">'+
    							'	<div class="create-family-cue">'+
    							'		<span>您还没有家族</span>'+
    							'	</div>'+
    							'	<div class="btn-groups clearfix">'+
    							'		<a href="javascript:void(0);" class="btn btn-primary" id="J-create-family" onclick="create_family();">创建家族</a>'+
                                '       <a href="javascript:void(0);" class="btn btn-green" onclick="pop_join_family();">加入家族</a>'+
    							'	</div>'+
    							'</div>';
        $.weeboxs.open(html_tip_nofamily, {boxid:"tip-nofamily-box", title:"我的家族", animate:false, width:390, showButton:false, showCancel:false, showOk:false});
    }
    
});

function pop_live()
{
    handleAjax.handle(APP_ROOT+"/index.php?ctl=user&act=add_video&tmpl_pre_dir=inc","", "html").done(function(result){
        $.weeboxs.open(result, {boxid:"pop_live", title:"我要直播", animate:false, width:608, showButton:false, showCancel:false, showOk:false});
    }).fail(function(err){
        $.showErr(err);
    });
}
// 修改直播间名称
function edit_room_title(room_id)
{
    var title = $('input[name="room_name"]').val();
    if($.checkEmpty(title)){
        $.showToast("请填写名称");
        return false;
    }
    var length = $.getStringLength(title);
    if(length<10){
        $.showToast("直播间名称长度至少5个汉字或10个字母");
        return false;
    }
    if(length>40){
        $.showToast("直播间名称长度不超过20个汉字");
        return false;
    }

    handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=user&act=update",{"room_title": title,"room_id":room_id}, "json").done(function(){
        $.showToast('成功保存');
    }).fail(function(err){
        $.showErr(err);
    });
}
//修改直播封面
function save_live_image(){
    handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=user&act=update",{"room_title": title}, "json").done(function(){
        $.showToast('成功保存');
    }).fail(function(err){
        $.showErr(err);
    });
}
// 结束直播
function end_live(room_id)
{
    handleAjax.handle(APP_ROOT+"/index.php?ctl=video&act=end_video",{"room_id": room_id}, "json").done(function(result){
        $.weeboxs.close("pop_live");
    }).fail(function(err){
        $.showErr(err);
    });
}
//进入直播间
function get_live(room_id){
    $.ajax({
        url: APP_ROOT + "/mapi/index.php?ctl=user&act=add_video&type=video_url&itype=app",
        type: 'GET',
        dataType: "json",
        data: {"room_id":room_id},
        async: false,
        success: function (result) {
            if(result.status==1){
                window.open(result.video_url);
            }else {
                if(result.error){
                    $.showErr(result.error);
                }else {
                    $.showErr("操作失败");
                }
            }
        }
    })
}
// 充值（界面）
function pop_recharge(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=pay&act=recharge&tmpl_pre_dir=inc","", "html").done(function(result){
        $.weeboxs.open(result, {title:"充值", animate:false, width:560, showButton:false, showCancel:false, showOk:false});
    }).fail(function(err){
        $.showErr(err);
    });
}

// 兑换（界面）
function pop_exchange(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=pay&act=exchange&tmpl_pre_dir=inc","", "html").done(function(result){
        $.weeboxs.open(result, {title:"兑换秀豆", animate:false, width:600, showButton:false, showCancel:false, showOk:false});
    }).fail(function(err){
        $.showErr(err);
    });
}

// 创建家族
function create_family(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=family&act=edit&tmpl_pre_dir=inc","", "html").done(function(result){
        $.weeboxs.close("tip-nofamily-box");
        $.weeboxs.open(result, {title:"创建家族", animate:false, width:340, showButton:false, showCancel:false, showOk:false});
    }).fail(function(err){
        $.showErr(err);
    });
}

// 加入家族
function pop_join_family(){
    $.weeboxs.close("tip-nofamily-box");
    var data = {family_name: $("#join-family-list input[name='family_name']").val()};
    handleAjax.handle(APP_ROOT+"/index.php?ctl=family&act=family_list&tmpl_pre_dir=inc",data, "html").done(function(result){
        $.weeboxs.open(result, {boxid:'join-family-list', title:"加入家族", animate:false, width:600, showButton:false, showCancel:false, showOk:false});
        $("#join-family-list input[name='family_name']").bind('keypress',function(event){
            if(event.keyCode == "13"){
                pop_join_family(name);
            }
        });
    }).fail(function(err){
        $.showErr(err);
    });
}