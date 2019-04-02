// 登录成功后个人资料
(function() {
	var a_width = $(".block-search-user").outerWidth();
	var userAgent = navigator.userAgent.toLowerCase();
	// Figure out what browser is being used 
	jQuery.browser = {
		version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
		safari: /webkit/.test(userAgent),
		opera: /opera/.test(userAgent),
		msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
		mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent)
	};
	if($.browser.msie) {
		a_width = a_width - 4;
	}
	var _right = a_width - 30;
	var _width = a_width + 5

	$(".block-search-user").hover(function() {
		$(".search-user").addClass("hover");
		$(".search-user-name i.up").css("display", "inline-block");
		$(".search-user-name i.down").css("display", "none");
	}, function() {
		$(".search-user").removeClass("hover");
		$(".search-user-name i.up").css("display", "none");
		$(".search-user-name i.down").css("display", "inline-block");
	});
	$(".search-user-top").css("right", _right + "px");
	$(".null").css("width", _width + "px");
})();

avalon.ready(function() {
	var search_key = $("input[name='search_key']").val();
	// 页面头部搜索
	var vm_login_tip = avalon.define({
	    $id: "login_tip",
	    search_key: search_key,
	    search: function(){
	        // 头部搜索
	        key = vm_login_tip.search_key;
	        location.href = APP_ROOT+"/index.php?ctl=live&act=search&key="+key;
	    }
	});
	avalon.scan(document.getElementById('login_tip'));
	
	$("input[name='search_key']").bind('keypress',function(event){  
	    if(event.keyCode == "13") vm_login_tip.search();
	});
});

// 登录
function login(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=login&act=pop&tmpl_pre_dir=inc","","html").done(function(result){
       $.weeboxs.open(result, {boxid:"login_box", title:"登录", animate:false, width:520, showButton:false, showCancel:false, showOk:false});
    }).fail(function(err){
        $.showErr(err);
    });
}

// 退出登录
function login_out(){
    handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=login&act=logout").done(function(result){
       $.showSuccess(result,function(){
            location.href = APP_ROOT+"/index.php?ctl=index&act=index";
        });
    }).fail(function(err){
        $.showErr(err);
    });
}

// 微信登录
function weixin_login(){
    handleAjax.handle(APP_ROOT+"/index.php?ctl=login&act=weixin_login&tmpl_pre_dir=inc", "", "html").done(function(result){
        $.weeboxs.open(result, {
            boxid: 'pop_weixin_login',
            title:"微信登录",
            width:400,
            animate:false,
            showButton:false,
            showCancel:false,
            showOk:false
        });
    }).fail(function(err){
        $.showErr(err);
    });
}
//公会微信登录
function society_wx_login(){
	handleAjax.handle(APP_ROOT+"/index.php?ctl=society&act=wx_entry&tmpl_pre_dir=inc", "", "html").done(function(result){
		$.weeboxs.open(result, {
			boxid: 'pop_society_wx_login',
			title:"微信登录",
			width:400,
			animate:false,
			showButton:false,
			showCancel:false,
			showOk:false
		});
	}).fail(function(err){
		$.showErr(err);
	});
}

// 保存图片
function save_img(w,h,el,ajax_url){
    var scale_w = parseFloat(w/h);
    var el = el;
    scale_w = scale_w.toFixed(1);
    scale = scale_w+'/1';
    bind_CropAvatar(scale, function(){
        var image_url = $("input[name='"+el+"']").val();
		if(!image_url){
			return;
		}
        var data = {};
        data[el] = image_url;
        handleAjax.handle(ajax_url,data).done(function(msg){
            $.showSuccess(msg);
        }).fail(function(err){
            $.showErr(err);
        });
    });
}