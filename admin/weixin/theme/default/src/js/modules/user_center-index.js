$(document).on('pageInit', '#page-user_center-index', function(){
	
	// 判断是否登录
	$("#vscope-checkLogin").on('click', '.J-check_login', function(){
		handleAjax.handle(check_login_url).done(function(result){
        	console.log(result);

	    }).fail(function(err){
	        $.toast(err);
	    });
	});

});