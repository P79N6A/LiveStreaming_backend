// 聊天消息
var chart = {
    is_lock:false,
    rock_chart: function(obj){
    // 锁定
        var e = this;
        e.is_lock = !e.is_lock;
        if(this.is_lock){
            obj.html("&#xe63f;");
            obj.addClass("active");
        }else{
            obj.html("&#xe63d;");
            obj.removeClass("active");
        }
    },
    clear_chart: function(){
    // 清屏
        $("#video_sms_list").html("");
    }
};
(function () {
    if(typeof loginInfo === 'undefined'){
        return;
    }

    var vm_send_letter = avalon.define({
        $id: "send-letter",
        show_letter: function () {
            if(! podcast.user_id){
                return;
            }
            var item = podcast;
            im_message.recieveMsg(item.user_id, function (msgList) {
                var sess = null;
                var el = $('#chat_panel').find('.m-letter-info').empty();
                for (var i in msgList) {
                    var msg = msgList[i];
                    sess = msg.getSession();
                    addMsg(el, msg, item.head_image);
                }

                webim.setAutoRead(sess, true, true);
                $.weeboxs.open($('#chat_panel').html(), {
                    boxid: vm_send_letter.$id,
                    title: "和" + item.nick_name + "的私信",
                    animate: false,
                    width: 560,
                    height: 560,
                    showButton: false,
                    showCancel: false,
                    showOk: false,
                    onclose:function(){
                        im_message.selToID = null;
                        listeners.curr = null;
                        webim.setAutoRead(sess, false, true);
                    }
                });
                $('.m-letter-info').scrollTop(el.prop("scrollHeight"));
                $('.weedialog.dialogbox').on('click', '.btn-send', function () {
                    sendMsg(item.user_id);
                });
            });
        }
    });

    var listeners = {
        loginSuccess: function (resp) {
            webim.syncMsgs(this.recieveMsg);
            if(typeof avChatRoomId !== 'undefined'){
                im_message.applyJoinBigGroup(avChatRoomId);
            }
        },
        recieveMsg: function (newMsgList) {
            var el = $('.weedialog.dialogbox').find('.m-letter-info');
            for (var j in newMsgList) {//遍历新消息
                var newMsg = newMsgList[j];
                if (newMsg.getSession().id() == im_message.selToID) {//为当前聊天对象的消息
                    webim.setAutoRead(newMsg.getSession(), true, true);
                    //在聊天窗体中新增一条消息
                    addMsg(el, newMsg, listeners.curr.head_image);
                }
            }
            !chart.is_lock && $('.m-letter-info').scrollTop(el.prop("scrollHeight"));

            //获取所有聊天会话
            var sessMap = webim.MsgStore.sessMap();
            var unread = 0;
            for (var i in sessMap) {
                var sess = sessMap[i];
                if (im_message.selToID != sess.id()) {//更新其他聊天对象的未读消息数
                    unread += updateSess(sess);
                }
            }

            $('#unread_msg').text(unread);
        },
        recieveGroupMsg: function (newMsgList) {
            for (var j in newMsgList) {//遍历新消息
                var newMsg = newMsgList[j];
                addGroupMsg(newMsg);
            }
			
			var el = $('#J-chat-cont');
            !chart.is_lock && el.scrollTop(el.prop("scrollHeight"));
        },
        sendMsgOk: function (msg) {
            // 如果是群聊
            $('#input-chat-speak').val('');
            // 以下是私聊
            $('.weedialog.dialogbox .input-txt').val('');
            var el = $('.weedialog.dialogbox .m-letter-info');
            addMsg(el, msg, loginInfo.head_image);
            el.scrollTop(el.prop("scrollHeight"));
        },
        sendMsgFail: function(error) {
            var err= error.replace(/network error!/, "您已被禁言");
            $.showErr(err);
        }
    };
    im_message.init(loginInfo, listeners);

    // 私聊窗口
    var vm = avalon.define({
        $id: "letter",
        items: typeof friends === 'undefined' ? [] : friends,
        search_val: "",
        page: 1,
        page_size: 28,
        pages: [],
        search: function(el,i){
            // 过滤模糊查询好友
            var reg = new RegExp(vm.search_val, 'gi');
            var result = reg.test(el.nick_name);
            if(i == 0){
                vm.count = 0;
            }
            if(result){
                vm.count += 1;
            }
            $('#friend_num').text(vm.count);
            return result;
        },
        show_letter: function (item) {
            im_message.recieveMsg(item.user_id, function (msgList) {
                var unread = $('#unread_msg').text();
                $('#unread_msg').text(unread - item.unread);
                listeners.curr = item;
                item.unread = 0;
                var sess = null;
                var el = $('#chat_panel').find('.m-letter-info').empty();
                for (var i in msgList) {
                    var msg = msgList[i];
                    sess = msg.getSession();
                    addMsg(el, msg, item.head_image);
                }

                webim.setAutoRead(sess, true, true);
                $.weeboxs.open($('#chat_panel').html(), {
                    title: "和" + item.nick_name + "的私信",
                    animate: false,
                    width: 560,
                    height: 560,
                    showButton: false,
                    showCancel: false,
                    showOk: false,
                    onclose:function(){
                        im_message.selToID = null;
                        listeners.curr = null;
                        webim.setAutoRead(sess, false, true);
                    }
                });
                $('.m-letter-info').scrollTop(el.prop("scrollHeight"));
                $('.weedialog.dialogbox').on('click', '.btn-send', function () {
                    sendMsg(item.user_id);
                });
            });
        },
        current: function(i) {
            vm.page = i;
        },
        prev: function() {
            if(vm.page > 1) {
                vm.page -= 1;
            }
        },
        next: function() {

            if(vm.page < vm.pages.length) {
                vm.page += 1;
            }
        }
    });

    function reSize() {
        var l = Math.ceil(vm.items.length / vm.page_size);
        vm.pages = [];
        for(var i = 1; i <= l; i++ ){
            vm.pages.push(i);
        }
    }

    reSize();
    vm.$watch("items.length", function () {
        reSize();
    });
    vm.$watch("page", function () {
        if(vm.search_val){
            vm.begin = 0;
        } else {
            vm.begin = (vm.page - 1) * vm.page_size;
        }
    });

    function addMsg(el, msg, head_image) {
        var is_send = msg.getIsSend();
        var uid = msg.getFromAccount();
        var nickname = msg.getFromAccountNick();
        var time = webim.Tool.formatTimeStamp(msg.getTime());
        var lr = is_send ? 'f-r' : 'f-l';
        var you_or_me = is_send ? 'me' : 'you';
        var head = is_send ? loginInfo.head_image : head_image;
        var text = convertMsg(msg);

        if(typeof text == 'object'){
            text = text.text;
        }

        el.append('<div class="m-letter-send mb-20 clearfix">' +
            '<img src="' + head + '" alt="" class="sender-img ' + lr + '">' +
            '<div class="letter-content ' + you_or_me + ' ' + lr + ' clearfix">' +
            '<div class="arrow-' + you_or_me + ' ' + lr + '"></div>' +
            '<div class="letter-inner ' + lr + '">' + '<p style="word-break:break-all;">'+text+'</p>' +  '</div>' +
            '</div>' +
            '</div>');
    }

	function addGroupMsg(msg) {
		var time = webim.Tool.formatTimeStamp(msg.getTime());
		var data = convertMsg(msg);
		if(! data){
			return;
		}

		if(typeof data !== 'object'){
			data = {
				"user_level": 122,
				"nick_name": "[群提示消息]",
				"text": data
			};
		}

		if ((data.type == 0 || data.type == 2) && showBarrage && player) {
			var barrage = [
				{ "type": "content", "content": data.text, "time": "0" },
			];
			player.addBarrage(barrage);
		}
        if(data.user_level){
            $('#video_sms_list').append('<li class="J-chartli sys-msg" data-level="6" data-type="list">' +
                '<img src="/public/images/rank/rank_' + data.user_level + '.png" style="vertical-align: text-bottom;height:1.3em;" art="当前等级">&nbsp;<span>' + data.nick_name + '：</span>' + data.text +
                '</li>');
        }else {
            $('#video_sms_list').append('<li class="J-chartli sys-msg" data-level="6" data-type="list">' +
                '<span>' + data.nick_name + '：</span>' + data.text +
                '</li>');
        }

	}

    function sendMsg(to_uid) {
        if (!to_uid) {
            return;
        }
        var text = $('.weedialog.dialogbox').find('.input-txt').val();
        if (!text) {
            return;
        }

        im_message.sendTextMsg(to_uid, text, 20, true);
    };

    function updateSess(sess) {
        var sender = null;
        var msg = null;
        for (var i = 0, l = sess.msgCount(); i < l; i++) {
            var elems = sess.msg(i).getElems();//获取消息包含的元素数组
            for(var k in elems){
                var elem = elems[k];
                if (elem.getType() == webim.MSG_ELEMENT_TYPE.CUSTOM) {
                    var content = elem.getContent();
                    var data = JSON.parse(content.getData());
                    if (data.type > 0 && data.type < 20) {
                        webim.setAutoRead(sess, true, true);
                        break;
                    }
                    if (data.sender) {
                        sender = data.sender;
                        msg = sess.msg(i);
                    }
                }
            }
        }

        if (!sender) {
            return 0;
        }

        system_uid = typeof system_uid === 'undefined' ? [] : system_uid;
        for(var i=0;i<system_uid.length;i++){
            var system_item=system_uid[i];
            if(system_item.user_id==sender.user_id){

                listeners.recieveGroupMsg([msg]);
                return 0;
            }
        }
        for (var i = 0; i < vm.items.length; i++) {
            var item = vm.items[i];
            if (item.user_id == sess.id()) {
                item.unread = sess.unread();
                vm.items.splice(i, 1);
                vm.items.unshift(item);
                return sess.unread();
            }
        }

        vm.items.unshift({
            "user_id": sender.user_id,
            "nick_name": sender.nick_name,
            "head_image": sender.head_image,
            "unread": sess.unread()
        });
        return sess.unread();
    }
} ());

