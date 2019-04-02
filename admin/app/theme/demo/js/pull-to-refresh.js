$(document).on("pageInit","#page-pai_podcast-goods,#page-pai_user-goods", function(e, pageId, $page) {
	// 下拉刷新
	if(pageId == 'page-pai_user-goods'){
		var pull_to_refresh_url = TMPL+"index.php?ctl=pai_user&act=goods";
	    var $content = $($page).find(".content").on('refresh', function(e) {
	    	refresh(pull_to_refresh_url,pageId,".ul-good-virtual",$content,function(){
	    		// 倒计时
			    $(".left_time").each(function(){
			    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
			    	left_time(leftTime,$(this));
			    });
	    	});
	    });
	}
	if(pageId == 'page-pai_podcast-goods'){
		var pull_to_refresh_url = TMPL+"index.php?ctl=pai_podcast&act=goods";
	    var $content = $($page).find(".content").on('refresh', function(e) {
	    	refresh(pull_to_refresh_url,pageId,".ul-good-virtual",$content,function(){
	    		// 倒计时
			    $(".left_time").each(function(){
			    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
			    	left_time(leftTime,$(this));
			    });
	    	});
	    });
	}
});