// 提示加入或创建家族
var vm = avalon.define({
    $id: "uc_nav",
    tip_nofamily: function () {
        var html_tip_nofamily = '<div class="m-create-family">' +
            '	<div class="create-family-cue">' +
            '		<span>您还没有家族</span>' +
            '	</div>' +
            '	<div class="btn-groups clearfix">' +
            '		<a href="javascript:void(0);" class="btn btn-primary" id="J-create-family" onclick="create_family();">创建家族</a>' +
            '       <a href="javascript:void(0);" class="btn btn-red" onclick="pop_join_family();">加入家族</a>' +
            '	</div>' +
            '</div>';
        $.weeboxs.open(html_tip_nofamily, {
            boxid: "tip-nofamily-box",
            title: "我的家族",
            animate: false,
            width: 400,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }

});

function pop_live(is_agree) {
    handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=add_video&tmpl_pre_dir=inc", {is_agree: is_agree}, "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: "pop_live",
            title: "我要直播",
            animate: false,
            width: 608,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}
//众筹直播
function pop_zc_live(is_agree, deal_id, cate_id) {
    var type = 'edu';
    handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=add_video&tmpl_pre_dir=inc", {
        is_agree: is_agree,
        "deal_id": deal_id,
        "cate_id": cate_id,
        "type": type
    }, "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: "pop_live",
            title: "我要直播",
            animate: false,
            width: 608,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}
function pop_book_live(is_agree, deal_id) {
    var type = 'booking';
    handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=add_video&tmpl_pre_dir=inc", {
        is_agree: is_agree,
        "deal_id": deal_id,
        "type": type
    }, "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: "pop_live",
            title: "我要直播",
            animate: false,
            width: 608,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}
function pop_video(type, deal_id, cate_id) {
    var room_title = $("input[name='room_name']").val();
    var live_image = $("input[name='live_image']").val();
    var is_private = $("select[name='is_private']").val();
    var is_live_pay = $("select[name='is_live_pay']").val();
    var title = $(".modify-live-FMS input[name='title']").val();
    var deal_id = deal_id;
    var cate_id = cate_id;
    var type = type;
    if (type == 'edu') {
        $.ajax({
            url: APP_ROOT + "/mapi/index.php?ctl=video&act=add_video&itype=edu",
            type: 'POST',
            dataType: "json",
            data: {
                "room_title": room_title,
                "live_image": live_image,
                "is_private": is_private,
                "deal_id": deal_id,
                "cate_id": cate_id,
                "is_live_pay":is_live_pay,
                "title":title
            },
            async: false,
            success: function (result) {
                if (result.status == 1) {
                    var html = '<style type="text/css">' + '.m-modify-live' + '{' +
                        'margin-top: -2px;' + '}' + ' .m-modify-live div{margin-bottom: 18px;}' + ' .m-modify-live .modify-end-btn' + '{' + 'padding: 12px 0 0;' +
                        'margin-left: 90px;' +
                        'text-align: center;' +
                        'display: inline-block;' +
                        'margin-bottom: 10px;' + '}' + ' .m-modify-live .modify-room-btn' + '{' +
                        'padding: 12px 0 0;' +
                        'margin-left: 85px;' +
                        'text-align: center;' +
                        'display: inline-block;' +
                        'margin-bottom: 10px;' + '}' + '.m-modify-live .modify-end-btn a' + '{' +
                        'padding:9px 46px;' + '}' + ' .m-modify-live .modify-room-btn a' + '{' +
                        'padding:9px 46px;' + '}' + ' .m-modify-live p' + '{' +
                        'font-size: 14px;' +
                        'font-weight: bold;' +
                        'text-align: left;' +
                        'line-height: 14px;' +
                        'margin-bottom: 8px;' + '}' + ' .m-modify-live input' + '{' +
                        'width: 548px;' +
                        'padding:6px 10px ;' +
                        'border: 1px solid #cdcdcd;' +
                        'box-sizing: content-box;' +
                        '-moz-box-sizing: content-box;' +
                        'border-radius: 5px;' +
                        '-moz-box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        '-webkit-box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        'box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        'color: #6e6e6e;' + '}' + '</style>' +
                        '<div class="m-modify-live">' +
                        '<div class="modify-live-FMS">' +
                        '<p>FMS&nbsp;URL</p>' +
                        '<input type="text" readonly="readonly" value="' + result.push_url + '" class="FMS-URL">' +
                        '</div>' +
                        '<div class="playback-path">' +
                        '<p>播放路径</p>' +
                        '<input type="text" readonly="readonly" value="' + result.push_code + '" class="playback">' +
                        '</div>' +
                        '<div class="modify-end-btn">' +
                        '<a href="javascript:end_live(' + result.room_id + ');" class="btn btn-red">结束直播</a>' +
                        '</div>' +
                        '<div class="modify-room-btn">' +
                        '<a href="javascript:get_live(' + result.room_id + ');" class="btn btn-green">进入直播间</a>' +
                        '</div>' +
                        '</div>';
                    $.weeboxs.open(html, {
                        boxid: "pop_video",
                        title: "我的直播",
                        animate: false,
                        width: 608,
                        showButton: false,
                        showCancel: false,
                        showOk: false,
                        onopen: function () {
                            $.weeboxs.close("pop_live")
                        }
                    });
                } else {
                    if (result.error) {
                        $.showErr(result.error);
                    } else {
                        $.showErr("操作失败");
                    }
                }
            }
        });
        //handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=video&act=add_video&itype=edu",{"room_title":room_title,"live_image":live_image,"is_private":is_private,"deal_id":deal_id,"cate_id":cate_id}, "json").done(function(result){
        //    $.weeboxs.open(result, {boxid:"pop_video", title:"我的直播", animate:false, width:608, showButton:false, showCancel:false, showOk:false,onopen:function(){$.weeboxs.close("pop_live")}});
        //}).fail(function(err){
        //    $.showErr(err);
        //});
    }
    else if(type == 'booking')
    {
        $.ajax({
            url: APP_ROOT + "/mapi/index.php?ctl=video&act=add_video&itype=edu",
            type: 'POST',
            dataType: "json",
            data: {
                "room_title": room_title,
                "live_image": live_image,
                "is_private": is_private,
                "booking_class_id": deal_id,
                "is_live_pay":is_live_pay,
                "title":title
            },
            async: false,
            success: function (result) {
                if (result.status == 1) {
                    var html = '<style type="text/css">' + '.m-modify-live' + '{' +
                        'margin-top: -2px;' + '}' + ' .m-modify-live div{margin-bottom: 18px;}' + ' .m-modify-live .modify-end-btn' + '{' + 'padding: 12px 0 0;' +
                        'margin-left: 90px;' +
                        'text-align: center;' +
                        'display: inline-block;' +
                        'margin-bottom: 10px;' + '}' + ' .m-modify-live .modify-room-btn' + '{' +
                        'padding: 12px 0 0;' +
                        'margin-left: 85px;' +
                        'text-align: center;' +
                        'display: inline-block;' +
                        'margin-bottom: 10px;' + '}' + '.m-modify-live .modify-end-btn a' + '{' +
                        'padding:9px 46px;' + '}' + ' .m-modify-live .modify-room-btn a' + '{' +
                        'padding:9px 46px;' + '}' + ' .m-modify-live p' + '{' +
                        'font-size: 14px;' +
                        'font-weight: bold;' +
                        'text-align: left;' +
                        'line-height: 14px;' +
                        'margin-bottom: 8px;' + '}' + ' .m-modify-live input' + '{' +
                        'width: 548px;' +
                        'padding:6px 10px ;' +
                        'border: 1px solid #cdcdcd;' +
                        'box-sizing: content-box;' +
                        '-moz-box-sizing: content-box;' +
                        'border-radius: 5px;' +
                        '-moz-box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        '-webkit-box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        'box-shadow: inset 0px 1px 1px #f2f2f2;' +
                        'color: #6e6e6e;' + '}' + '</style>' +
                        '<div class="m-modify-live">' +
                        '<div class="modify-live-FMS">' +
                        '<p>FMS&nbsp;URL</p>' +
                        '<input type="text" readonly="readonly" value="' + result.push_url + '" class="FMS-URL">' +
                        '</div>' +
                        '<div class="playback-path">' +
                        '<p>播放路径</p>' +
                        '<input type="text" readonly="readonly" value="' + result.push_code + '" class="playback">' +
                        '</div>' +
                        '<div class="modify-end-btn">' +
                        '<a href="javascript:end_live(' + result.room_id + ');" class="btn btn-red">结束直播</a>' +
                        '</div>' +
                        '<div class="modify-room-btn">' +
                        '<a href="javascript:get_live(' + result.room_id + ');" class="btn btn-green">进入直播间</a>' +
                        '</div>' +
                        '</div>';
                    $.weeboxs.open(html, {
                        boxid: "pop_video",
                        title: "我的直播",
                        animate: false,
                        width: 608,
                        showButton: false,
                        showCancel: false,
                        showOk: false,
                        onopen: function () {
                            $.weeboxs.close("pop_live")
                        }
                    });
                } else {
                    if (result.error) {
                        $.showErr(result.error);
                    } else {
                        $.showErr("操作失败");
                    }
                }
            }
        });
        //handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=video&act=add_video&itype=edu",{"room_title":room_title,"live_image":live_image,"is_private":is_private,"deal_id":deal_id,"cate_id":cate_id}, "json").done(function(result){
        //    $.weeboxs.open(result, {boxid:"pop_video", title:"我的直播", animate:false, width:608, showButton:false, showCancel:false, showOk:false,onopen:function(){$.weeboxs.close("pop_live")}});
        //}).fail(function(err){
        //    $.showErr(err);
        //});
    }
    else
    {
        handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=create_room&tmpl_pre_dir=inc", {
            "room_title": room_title,
            "live_image": live_image,
            "is_private": is_private,
            "is_live_pay":is_live_pay,
            "title":title
        }, "html").done(function (result) {
            $.weeboxs.open(result, {
                boxid: "pop_video",
                title: "我的直播",
                animate: false,
                width: 608,
                showButton: false,
                showCancel: false,
                showOk: false,
                onopen: function () {
                    $.weeboxs.close("pop_live")
                }
            });
        }).fail(function (err) {
            $.showErr(err);
        });
    }

}
// 修改直播间名称
function edit_room_title(room_id) {
    var title = $('input[name="room_name"]').val();
    if ($.checkEmpty(title)) {
        $.showToast("请填写名称");
        return false;
    }
    var length = $.getStringLength(title);
    if (length < 10) {
        $.showToast("直播间名称长度至少5个汉字或10个字母");
        return false;
    }
    if (length > 40) {
        $.showToast("直播间名称长度不超过20个汉字");
        return false;
    }

    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=user&act=update", {
        "room_title": title,
        "room_id": room_id
    }, "json").done(function () {
        $.showToast('成功保存');
    }).fail(function (err) {
        $.showErr(err);
    });
}
//修改直播封面
function save_live_image() {
    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=user&act=update", {"room_title": title}, "json").done(function () {
        $.showToast('成功保存');
    }).fail(function (err) {
        $.showErr(err);
    });
}
// 结束直播
function end_live(room_id) {
    handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=end_video", {"room_id": room_id}, "json").done(function (result) {
        $.weeboxs.close("pop_live");
        $.weeboxs.close("pop_video");
    }).fail(function (err) {
        $.showErr(err);
    });
}
//进入直播间
function get_live(room_id) {
    $.ajax({
        url: APP_ROOT + "/mapi/index.php?ctl=user&act=add_video&type=video_url&itype=app",
        type: 'GET',
        dataType: "json",
        data: {"room_id": room_id},
        async: false,
        success: function (result) {
            if (result.status == 1) {
                window.open(result.video_url);
            } else {
                if (result.error) {
                    $.showErr(result.error);
                } else {
                    $.showErr("操作失败");
                }
            }
        }
    })
}
// 充值（界面）
function pop_recharge() {
    handleAjax.handle(APP_ROOT + "/index.php?ctl=pay&act=recharge&tmpl_pre_dir=inc", "", "html").done(function (result) {
        $.weeboxs.open(result, {
            title: "充值",
            animate: false,
            width: 560,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}

// 兑换（界面）
function pop_exchange() {
    handleAjax.handle(APP_ROOT + "/index.php?ctl=pay&act=exchange&tmpl_pre_dir=inc", "", "html").done(function (result) {
        $.weeboxs.open(result, {
            title: "兑换秀豆",
            animate: false,
            width: 600,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}

// 创建家族
function create_family() {
    handleAjax.handle(APP_ROOT + "/index.php?ctl=family&act=edit&tmpl_pre_dir=inc", "", "html").done(function (result) {
        $.weeboxs.close("tip-nofamily-box");
        $.weeboxs.open(result, {
            title: "创建家族",
            animate: false,
            width: 340,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}

// 加入家族
function pop_join_family() {
    $.weeboxs.close("tip-nofamily-box");
    var data = {family_name: $("#join-family-list input[name='family_name']").val()};
    handleAjax.handle(APP_ROOT + "/index.php?ctl=family&act=family_list&tmpl_pre_dir=inc", data, "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: 'join-family-list',
            title: "加入家族",
            animate: false,
            width: 600,
            showButton: false,
            showCancel: false,
            showOk: false
        });
        $("#join-family-list input[name='family_name']").bind('keypress', function (event) {
            if (event.keyCode == "13") {
                pop_join_family(name);
            }
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}
// 邀请好友
function pop_friend_list(room_id) {
    var data = {room_id: room_id};
    handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=friend_list&tmpl_pre_dir=inc", data, "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: 'friend_list',
            title: "好友列表",
            animate: false,
            width: 600,
            showButton: false,
            showCancel: false,
            showOk: false,
            onopen: function () {
                avalon.scan(document.getElementById('friend_list'));
            }
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}

//上传视频
function upload_video(){
    handleAjax.handle(APP_ROOT + "/index.php?ctl=user&act=upload_video&tmpl_pre_dir=inc", "", "html").done(function (result) {
        $.weeboxs.open(result, {
            boxid: 'do_upload_video',
            title: "上传视频",
            animate: false,
            width: 600,
            showButton: false,
            showCancel: false,
            showOk: false
        });
    }).fail(function (err) {
        $.showErr(err);
    });
}

