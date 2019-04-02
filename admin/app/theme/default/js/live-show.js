auto_layout();
// 监听拖动窗口
var giftqueue=new Array();

window.onresize = function(){
	auto_layout();
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
			vm_focus.data.html = '<i class="icon iconfont">&#xe638;</i>&nbsp;已关注';
			vm_focus.data.num += 1;
			
			im_message.sendTextMsg(avChatRoomId, loginInfo.identifierNick + ' 关注了直播', 9);
		} else {
			vm_focus.data.has_focus = false;
			vm_focus.data.color = '#ff630e';
			vm_focus.data.html = '<i class="icon iconfont">&#xe638;</i>&nbsp;关注';
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

$(function(){
	$("#J-fans-rank").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500, startFun: function( i, c, slider, titCell, mainCell, targetCell){
		if(! roomId || !podcastUserId){
			return;
		}
		var data = i == 0 ? {"room_id": roomId} : {"user_id": podcastUserId};
		$.get('/mapi/index.php?ctl=video&act=cont', data, function(res){
			var container = $(mainCell.children().get(i)).empty();
			for(var key in res.list){
				var item = res.list[key];
				var rank = 'other';
				var index = parseInt(key) + 1;
				if(index == 1){
					rank = 'leader first';
				} else if(index == 2){
					rank = 'leader second';
				} else if(index == 3){
					rank = 'leader third';
				}

				container.append('<li class="' + rank + '" data-id="' + index + '">' +
									'<i class="ui-grade grade-stars" data-grade="' + item.user_level + '">' + item.user_level + '</i>&nbsp;' + item.nick_name +
									'<span class="f-r"><em>' + item.num + '</em>&nbsp;' + TICKET_NAME + '</span>' +
								'</li>');
			}
		}, 'json');
	}});
	$("#J-room-recommend").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500});

	window.player = null;
	var showBarrage = true;
	$('#J-barrage').click(function(){
		showBarrage = !showBarrage;
		if(!showBarrage && player){
			player.closeBarrage();
		}
		
		$(this).toggleClass('icon-barrage-open icon-barrage-close');
	});
	if(typeof playerInfo !== 'undefined'){
		var container = $('#js-room-video');
		playerInfo.width = container.width();
		playerInfo.height = container.height();
		window.player = new qcVideo.Player("js-room-video", playerInfo, {playStatus: function(status, type){
			console.log([status, type]);
			switch(status){
				case 'ready':
				case 'seeking':
					$('#live-loading').show();
					break;
				case 'playing':
					live_in = 1;
					$('#live-loading').hide();
					break;
				case 'error':
					if(type == 'streamNotFound' && live_in == 2){
						setTimeout(function(){player.play();}, 2000);
					}
					break;
			}
		}});
	}

	function addMsg(msg) {
		var time = webim.Tool.formatTimeStamp(msg.getTime());
		var data = convertMsg(msg);
		if(! data){
			return;
		}

		if(typeof data !== 'object'){
			data = {
				"user_level": 122,
				"nick_name": "[群提示消息]",
				"text": data,
			};
		}

		if ((data.type == 0 || data.type == 2) && showBarrage && player) {
			var barrage = [
				{ "type": "content", "content": data.text, "time": "0" },
			];
			player.addBarrage(barrage);
		}

		$('#video_sms_list').append('<li class="J-chartli sys-msg" data-level="6" data-type="list" style="word-break:break-all;">' +
				'<img src="/public/images/rank/rank_' + data.user_level + '.png" style="vertical-align: text-bottom;height:1.3em;" art="当前等级">&nbsp;<span >' + data.nick_name + '：</span>' + data.text +
			'</li>');
	}
	var listeners = {
        loginSuccess: function () {
            im_message.applyJoinBigGroup(avChatRoomId);
        },
        recieveGroupMsg: function (newMsgList) {
            for (var j in newMsgList) {//遍历新消息
                var newMsg = newMsgList[j];
                addMsg(newMsg);
            }
			
			var el = $('#video_sms_list');
            el.scrollTop(el.prop("scrollHeight"));
        },
        sendMsgOk: function (msg) {
            $('#input-chat-speak').val('');
        },
        sendMsgFail: function(error) {
            $.showErr(error);
        }
    };
	if(typeof loginInfo !== 'undefined'){
		im_message.init(loginInfo, listeners);
	}

	var allowSendMsg = true;
	$('.btn-speak').click(function(){
		if(! allowSendMsg){
			return;
		}
		var msg = $('#input-chat-speak').val();
		im_message.sendTextMsg(avChatRoomId, $.trim(msg));
	});

	$('#input-chat-speak').change(function(){
		allowSendMsg = true;
	});

	$('.gift-list').on('click', 'a.gift', function(){
		if(! roomId){
			$.showToast("主播暂时不在家，无法发送礼物");
			return false;
		}else {
			var prop_id = $(this).attr('prop-id');
			if(prop_id <= 0){
				return;
			}
			$.post('/mapi/index.php?ctl=deal&act=pop_prop', {
				"prop_id": prop_id,
				"room_id": roomId,
			}, function(res){
				if(! res.status){
					$.showErr(res.error);
				}
			}, 'json');
		}
	});
});
//送礼物

var timer = null;

function giftlist(domId,gift_bj,list_img,list_name,giftname,n,ifexecute){
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
                 + '<div class="m-nbox" data_n="'+n+'">'
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
            panel.find(".giftbox").animate({left:'295px'},400,function(){
            	panel.remove();
            });
        });
    } else {//如果这个id存在
        panel.stopTime();
        var data_n= panel.find(".m-nbox").attr("data_n");
        //console.log("data_n备注："+data_n);
        //console.log("我n："+n);
		if (n>data_n) {
	    	for(var i=0;n-data_n>=i;i++){
	    		var num=parseInt(data_n) +i;
	    		//console.log("循环："+num);
				nbox(domId,num);
				panel.find(".m-nbox").attr("data_n",num);
			}
			//console.log("这是一个节点"+num); 
			nbox(domId,n);
			panel.find(".m-nbox").attr("data_n",n);
		};
        panel.oneTime('3s',function(){
            panel.find(".giftbox").animate({left:'295px'},400,function(){
            	panel.remove();
            });
        });
    }
 //console.log("一个循环结束啦"+ifexecute);  
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
	},80);
}