var play_status;
auto_layout();
// 监听拖动窗口
window.onresize = function(){
	auto_layout();
	show_gift_modal();
	if(is_full_screen){
		auto_full_screen.init();
	}
	if(player){
		var container = $('#js-room-video');
		player.resize(container.width(), container.height());
	}
};

// 关注
var vm_focus = avalon.define({
	$id: "focus",
	data: focusInfo,
	follow: function () {
		follow_video(true);
	}
});
function follow_video(showConfirm)
{
	if(showConfirm && vm_focus.data.has_focus){
		return $.showConfirm("您是否取消关注？",function(){
			follow_video(false);
		});
	}
	$.post('/mapi/index.php?ctl=user&act=follow', {'to_user_id': podcastUserId, 'room_id': roomId}, function(res){
		if(! res.status){
			return $.showErr(res.error);
		}
		if(res.has_focus){
			vm_focus.data.has_focus = true;
			vm_focus.data.color = '#bbb';
			vm_focus.data.html = '已关注';
			vm_focus.data.num += 1;
			if(avChatRoomId){
				im_message.sendTextMsg(avChatRoomId, loginInfo.identifierNick + ' 关注了直播', 9);
			}
		} else {
			vm_focus.data.has_focus = false;
			vm_focus.data.color = '#00dfb2';
			vm_focus.data.html = '关注';
			vm_focus.data.num -= 1;
		}
	}, 'json');
}

// 举报
var vm_tipoff = avalon.define({
	$id: "tipoff",
	tipoff: function (room_id) {
	 	handleAjax.handle(APP_ROOT+"/index.php?ctl=live&act=tipoff&tmpl_pre_dir=inc", {"room_id":room_id}, "html").done(function(result){
       		$.weeboxs.open(result, {title:"我要举报", animate:false, width:1000, showButton:false, showCancel:false, showOk:false});
        }).fail(function(err){
            $.showErr(err);
        });
	}
});

//贡献排行榜
$(function(){
	$("#J-fans-rank").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500, startFun: function( i, c, slider, titCell, mainCell, targetCell){
		if(! roomId || !podcastUserId){
			return;
		}
		var data = i == 0 ? {"room_id": roomId} : {"user_id": podcastUserId};
		$.get('/mapi/index.php?ctl=video&act=cont', data, function(res){
			var container = $(mainCell.children().get(i)).empty();
			container.append(
				'<li class="block-leader clearfix">' +
				'</li>'
			);
			for(var key in res.list){
				var item = res.list[key];
				var rank = 'other';
                var list = 'other';
				var index = parseInt(key) + 1;
				if(index == 1){
					rank = 'first';
                    list = 'list-1';
				} else if(index == 2){
					rank = 'second';
                    list = 'list-2';
				} else if(index == 3){
					rank = 'third';
                    list = 'list-3';
				}
				if(index <= 3){
					$(".block-leader").append(
						'<div class="leader" data-id="' + index + '">' +
						'<div class="Top ' + rank + '">' +
						'<img class="img-tit" src="' + item.head_image + '" alt="">' +
						'<div class="top-list ' + list + '"></div>' +
						'</div>' +
						'<p title="'+item.nick_name+'">' + item.nick_name +'</p>' +
						'<img class="level-img" src="./public/images/rank/rank_' + item.user_level + '.png">' +
						'<div class="clear"></div>' +
						'</div>'
					);
				}else if(index >= 4){
					container.append(
						'<li class="' + rank + '" data-id="' + index + '">' +
						'<img class="level-img f-l" src="./public/images/rank/rank_' + item.user_level + '.png">' +
						'<div class="wrap-name f-l" title="'+item.nick_name+'">' + item.nick_name + '</div>'+
						'<span class="f-r"><em>' + item.num + '</em>&nbsp;'+ TICKET_NAME +'</span>' +
						'</li>'
					);
				}
			}
		}, 'json');
	}});
	$("#J-room-recommend").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500});

	window.player = null;
	window.showBarrage = true; //弹幕开关
	var vm_chat_operate = avalon.define({
	    $id: "chat_operate",
	    is_add: true,
	    addBarrage: function(){
       		showBarrage = true;
       		vm_chat_operate.is_add = true;
	    },
	    closeBarrage: function(){
    		showBarrage = false;
    		vm_chat_operate.is_add = false;
    		player.closeBarrage();
	    }
	});
	avalon.scan(document.getElementById('vm_chat_operate'));
	
	$("input[name='search_key']").bind('keypress',function(event){  
	    if(event.keyCode == "13") vm_login_tip.search();
	});

	if(typeof playerInfo !== 'undefined'){
		var container = $('#js-room-video');
		playerInfo.width = container.width();
		playerInfo.height = container.height();
		window.player = new qcVideo.Player("main-video", playerInfo, {playStatus: function(status, type){
			console.log([status, type]);
			switch(status){
				case 'ready':
				case 'seeking':
					$('#float-video-loading').show();
					break;
				case 'playing':
					$('#float-video-loading').hide();
					play_status = 'playing';
					break;
				case 'suspended':
					play_status = 'suspended';
					break;
				case 'error':
					if(live_in == 2){
						setTimeout(function(){player.play();}, 2000);
					}
					break;
			}
		}});
	}

	var allowSendMsg = true;
	if(! user_id ||live_in == '' || is_live_pay == 1 || live_in == 0 || live_in == 2){
		allowSendMsg = false;
	}
	$('.btn-speak').click(function(){
		if(! allowSendMsg){
			return;
		}
		var msg = $('#input-chat-speak').val();
		im_message.sendTextMsg(avChatRoomId, $.trim(msg));
	});
	$("textarea[id='input-chat-speak']").bind('keypress',function(event){
		if(event.keyCode == "13"){
			event.preventDefault();
			if(! allowSendMsg){
				return;
			}
			var msg = $('#input-chat-speak').val();
			if(msg != ''){
				im_message.sendTextMsg(avChatRoomId, $.trim(msg));
				return msg='';
			}
		}

	});
	$('#input-chat-speak').change(function(){
		allowSendMsg = true;
	});

	// 赠送礼物
	$('.J-send-gift').on('click', function(){
		var from="pc";
		if(! roomId){
			$.showToast("主播暂时不在家，无法发送礼物");
			return false;
		}
		var prop_id = $(this).attr('prop-id');
		if(prop_id <= 0){
			return;
		}
		$.post('/mapi/index.php?ctl=deal&act=pop_prop', {
			"prop_id": prop_id,
			"room_id": roomId,
			"from":from,
			"child_id":child_id
		}, function(res){
			if(! res.status){
				if(res.error != ":70207")$.showErr(res.error);
			}
		}, 'json');
	});

	var j_one = $(".J-one");
	$(".J-fans-con").hover(function () {
		if(j_one.hasClass("active")){
			var j_fans_b = $(".J-fans-b").height();
			$(this).stop().animate({height:j_fans_b},200);
		}else{
			var j_fans_z = $(".J-fans-z").height();
			$(this).stop().animate({height:j_fans_z},200);
		}
	},function () {
		$(this).stop().animate({height:"120"},200);
	});

	show_gift_modal();

});

//送礼物

var timer = null;
function giftlist(domId,gift_bj,list_img,list_name,giftname,n){
if (!domId) return;
var panel = $('#' + domId); 
if (panel.length <= 0) {//如果这个id不存在
        var html = '<div id="' + domId + '" class="gift-list">'
        		 + '<div class="giftbox">'
                 + '<div class="list-back">'
                 + ' <img src="'+gift_bj+'">'
                 + '</div>'
                 + '<div class="list-img">'
                 + ' <img src="'+list_img+'">'
                 + '</div>'
                 + '<div class="list-name">'
                 + list_name
                 + '</div>'
                 + '<div class="list-sent">'
                 + '送出'
                 + '</div>'
                 + '<div class="list-details">'
                 + '<div class="giftname"><span>'
                 + giftname
                 + '</span></div>'
                 + '<div class="m-nbox">'
                 + '</div>'
                 + '</div>'
                 + '</div>'
                 + '</div>'
                 + '</div>';
     $('.j-chat-gift').append(html);
        var panel = $('#' + domId);
        nbox(domId,n);
        panel.find(".giftbox").animate({left:0});
        panel.oneTime('3s',function(){
        //do something...  
            panel.find(".giftbox").animate({left:'295px'},400);
            setTimeout(function(){
                panel.remove();
            },500);
        });
    } else {//如果这个id存在
        panel.stopTime();
        panel.oneTime('3s',function(){
            panel.find(".giftbox").animate({left:'295px'},400);
            setTimeout(function(){
                panel.remove();
            },500);
        });
        nbox(domId,n);
    }
}
function nbox(domId,n){
	if (n>9) {
		var n1=parseInt(n/10);
		var n2=n-n1*10;
		var apend='<span class="n add_n n'+n1+'"></span><span class="n add_n n'+n2+'"></span>';
		$('#' + domId).find(".m-nbox").html(apend);
	}else{
		var apend='<span class="n add_n n'+n+'"></span>';
		$('#' + domId).find(".m-nbox").html(apend);
	};
	setTimeout(function(){
		$('#' + domId).find(".m-nbox .n").removeClass("add_n");
	},200);
}

$(".J-rock-chart").click(function () {
	var title = $(".J-rock-chart").attr('title');
	if (title == '关闭滚屏') {
		$(".J-rock-chart").attr('title','开启滚屏');
	}else {
		$(".J-rock-chart").attr('title','关闭滚屏');
	}
	chart.rock_chart($(this));
});
$(".J-clear-chart").click(function () {
	chart.clear_chart();
});


//主播推荐商品弹窗
    $(".person-recommend").click(function () {
        $(".m-recommend").addClass("action");
    });
    $(".recommend-close").click(function () {
        $(".m-recommend").removeClass("action");
    });

// 锁定全屏
var auto_full_screen = {
    win_w: "",
    win_h: "",
    full_screen_l_video_w: "",
    full_screen_l_video_h: "",
    full_screen_r_w: "",
    full_screen_r_chat_h: "",
    full_screen_l_gift_h: "",
    init: function() {
		var e = this;
       	e.win_w = $(window).width();
       	e.win_h = $(window).height();
       	e.full_screen_r_w = $(".live-room-normal-right").width();
       	e.full_screen_l_gift_h = $(".stats-and-actions").height();
       	e.full_screen_l_video_w = e.win_w - e.full_screen_r_w -20;
       	e.full_screen_l_video_h = e.win_h - e.full_screen_l_gift_h - 3;
       	e.full_screen_r_chat_h = e.win_h - 334;
       	if(is_full_screen){
       		e.handle_fullscreen();
       	}
       	else{
			e.handle_out_fullscreen();
       	}
    },
    handle_fullscreen: function(){
    	var e = this;
    	$("#bodywrapper").css("marginTop","0");
		$(".fullpage-container").css("marginTop","0");
		$(".full_display_none").css("display","none");
		$("#mainbody").css({ width: e.win_w +"px", marginRight:"0",marginLeft:"0",minWidth:"1329px"});
		$("#J-live-room-normal-left").css({width: e.full_screen_l_video_w+"px"});
		$(".room-video").css({height:e.full_screen_l_video_h+"px"});
		$("#J-chat-cont").css({height: e.full_screen_r_chat_h +"px"});
		$("#float-video-mask").css({height:e.full_screen_l_video_h-130 +"px"});
		player.resize(e.full_screen_l_video_w, e.full_screen_l_video_h);
    },
    handle_out_fullscreen: function(){
    	auto_layout();
		$("#bodywrapper").css("marginTop","51px");
		$(".fullpage-container").css("marginTop","20px");
		$(".full_display_none").css("display","block");
		$("#mainbody").css({ width: new_w_mainbody + "px", marginRight:"20px",marginLeft:"94px"});
		
		if(player){
			var container = $('#js-room-video');
			player.resize(container.width(), container.height());
		}
    }
}, is_full_screen = false, container_w = $('#js-room-video').width(), container_h = $('#js-room-video').height();

$("#float-video-mask").bind("dblclick", function () {
	is_full_screen = !is_full_screen;
	auto_full_screen.init();
});
$("#float-video-mask").click(function(){ 
	if(play_status == 'playing'){
		player.pause();
	}
	else{
		player.resume();
	}
});

// 浮动礼物充值详请
function show_gift_modal(){
	var ele_gift = $('.J-gift-modal-show');
	var ele_gift_modal = $('.gift-modal');
	ele_gift.hover(function(e){
		window.event? window.event.returnValue = false : e.preventDefault();
		ele_gift_modal.hide();
	    var prop_id = $(this).attr("prop-id");
	    if(is_full_screen){
	    	var top = $(this).offset().top-56;
		 	var left = $(this).offset().left-(326/2)+(47/2);
			$(".gift-modal-"+prop_id).css({"left":left-94,"top":top-71,"display":"block"});
	    }
	    else{
	    	var top = $(this).offset().top-61-(127/2);
		 	var left = $(this).offset().left-(326/2)+(47/2);
			$(".gift-modal-"+prop_id).css({"left":left-94,"top":top-71,"display":"block"});
	    }
	 	
	},function(){
		ele_gift_modal.hover(function() {
			$(this).show();
			/* Stuff to do when the mouse enters the element */
		}, function() {
			$(this).hide();
			/* Stuff to do when the mouse leaves the element */
		});
	});
	$("#js-stats-and-actions").hover(function() {
		/* Stuff to do when the mouse enters the element */
		
	}, function() {
		ele_gift_modal.hide();
	});
}

$.get("/mapi/index.php?ctl=shop&act=mystore&itype=app", {podcast_id: podcastUserId}, function(res){
	if(!res.status){
		return;
	}
	if(res.list.length > 0){
		$('.person-recommend').show();
	}
	var vm_goods = avalon.define({
		$id: "live_goods",
		items: res.list,
	});
	avalon.scan(document.getElementById('live_goods'));
	jQuery("#m-recommend-scroll").slide({titCell:".nav ul",mainCell:".block-inner ul",trigger:"click",autoPage:true,effect:"left",scroll:4,autoPlay:false,vis:4,delayTime:700});
}, 'json');

// 礼物组第一组和最后一组提示
$(document).on('click', '.prevStop', function(){
	$.showToast('已经是第一组');
});
$(document).on('click', '.nextStop', function(){
	$.showToast('已经是最后一组');
});