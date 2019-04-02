(function () {
    var listeners = {
        loginSuccess: function (resp) {
            webim.syncMsgs(this.recieveMsg);
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
            $('.m-letter-info').scrollTop(el.prop("scrollHeight"));

            //获取所有聊天会话
            var sessMap = webim.MsgStore.sessMap();
            for (var i in sessMap) {
                var sess = sessMap[i];
                if (im_message.selToID != sess.id()) {//更新其他聊天对象的未读消息数
                    updateSess(sess);
                }
            }
        },
        sendMsgOk: function (msg) {
            $('.weedialog.dialogbox .input-txt').val('');
            var el = $('.weedialog.dialogbox .m-letter-info');
            addMsg(el, msg, loginInfo.head_image);
            el.scrollTop(el.prop("scrollHeight"));
        },
    };
    im_message.init(loginInfo, listeners);

    // 私聊窗口
    var vm = avalon.define({
        $id: "letter",
        items: friends,
        system_uid:system_uid,
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
                    },
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
        vm.begin = (vm.page - 1) * vm.page_size;
    });
    vm.$watch("search_val", function () {
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
            '<div class="letter-inner ' + lr + '">' + '<p style="word-break:break-all;">'+text+'</p>' + '</div>' +
            '</div>' +
            '</div>');
    }

    function sendMsg(to_uid) {
        if (!to_uid) {
            return;
        }
        var text = $('.weedialog.dialogbox').find('.input-txt').val();
        if (!text) {
            return;
        }

        im_message.sendTextMsg(to_uid, text, 20);
    };

    function updateSess(sess) {
        var sender = null;
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
                return;
            }
        }

        for (var i = 0; i < vm.items.length; i++) {
            var item = vm.items[i];
            if (item.user_id == sess.id()) {
                item.unread = sess.unread();
                vm.items.splice(i, 1);
                vm.items.unshift(item);
                return;
            }
        }

        vm.items.unshift({
            "user_id": sender.user_id,
            "nick_name": sender.nick_name,
            "head_image": sender.head_image,
            "unread": sess.unread(),
        });
    }
} ());

