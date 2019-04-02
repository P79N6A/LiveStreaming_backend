$(document).on("pageInit","#page-user_center-invite", function(e, pageId, $page) {
	
	// 分享方式
	$(".flex-box").on('click', '.J-fx', function(){
		var self = $(this), share_type = self.attr("data-type");
		self.addClass("active").siblings().removeClass("active");
		data.share_type = share_type;
	});

	$(".J-share").on('click', function(e){
/*	 	if(data.share_type == 'wx'){
            shareAppMessage();
        }else{
            shareTimeline();
        }*/
        window.event? window.event.cancelBubble = true : e.stopPropagation();
        $(".share-tip").addClass('show');
	});

	$("document, body").click(function(e){
		window.event? window.event.cancelBubble = true : e.stopPropagation();
		$(".share-tip").removeClass('show');
	});

    //分享到朋友圈
    function shareTimeline(){
        WeixinJSBridge.invoke('shareTimeline',{
            "img_url":wx_img,
            "link":wx_link,
            "desc": wx_desc,
            "title":wx_title
        });
    }

    //分享给好友
    function shareAppMessage(){
        WeixinJSBridge.invoke('sendAppMessage',{
            "img_url":wx_img,
            "link":wx_link,
            "desc":wx_desc,
            "title":wx_title
        });
    }

	// 微信分享
	wx.ready(function () {

		// 分享到朋友圈
	    wx.onMenuShareTimeline({
	        title: wx_desc, // 分享标题
	        link: wx_link, // 分享链接
	        imgUrl: wx_img, // 分享图标
	        success: function () {
	            // 用户确认分享后执行的回调函数
	        },
	        cancel: function () {
	            // 用户取消分享后执行的回调函数
	        }
	    });

	    // 分享给朋友
	    wx.onMenuShareAppMessage({
	        title: wx_title, // 分享标题
	        desc: wx_desc, // 分享描述
	        link: wx_link,  // 分享链接
	        imgUrl: wx_img, // 分享图标
	        success: function () {
	            // 用户确认分享后执行的回调函数
	        },
	        cancel: function () {
	            // 用户取消分享后执行的回调函数
	        }
	    });

	    // 通过error接口处理失败验证
	    wx.error(function(res){
	        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
	    });
	});



});