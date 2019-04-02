function auto_layout(){
	var w_width = $(window).width();     //屏幕宽度
	var w_mainbody = w_width-(120*2);    //主视区宽度
	var layout_l_w;            //左侧布局宽度       
	var video_ratio = 16/9;    //设置视频窗口比例
	var video_new_h;
	var chat_new_h;
	var float_video_h = $("#float-video").height();
	console.log(float_video_h);

	// 设置页面宽度最小阀值
	if(w_width<1440){
		layout_l_w = 1200-295-20;
		video_new_h = layout_l_w/video_ratio;
		chat_new_h = video_new_h-60;
		float_video_h = (video_new_h/2)-(float_video_h/2)+130;
		console.log(video_new_h);
		document.getElementById("mainheader").style.width = 1200+"px";
		document.getElementById("mainbody").style.width = 1200+"px";
		document.getElementById("J-live-room-normal-left").style.width = (1200-295-20)+"px";
		$('.float-video').width(layout_l_w).height(float_video_h);
	}
	else{
		layout_l_w=w_mainbody-(295+20);
		video_new_h = layout_l_w/video_ratio;
		chat_new_h = video_new_h-60;
		float_video_h = (video_new_h/2)-(float_video_h/2)+130;
		console.log(video_new_h);
		document.getElementById("mainheader").style.width = w_mainbody+"px";
		document.getElementById("mainbody").style.width = w_mainbody+"px";
		document.getElementById("J-live-room-normal-left").style.width = layout_l_w+"px";
		$('.float-video').width(layout_l_w).height(float_video_h);
	}
	document.getElementById("js-room-video").style.height = video_new_h+"px";
	document.getElementById("J-chat-cont").style.height = chat_new_h+"px";
	// 推广图片
	ratio_tgImg = 382/215;   // 推广图片比例
	var w_tgImg=(layout_l_w-(15*2+30*3))/3;
	var h_tgImg=w_tgImg/ratio_tgImg;
	$(".tg-pic").css({width:w_tgImg, height:h_tgImg}).show();

	// 推荐视频
	var w_tjImg=220;  // 宽度最小阀值
	var num_tjImg=Math.floor((layout_l_w-20)/(w_tjImg+20));  // 一行显示图片个数
	var new_tjImg=((layout_l_w-20)/num_tjImg)-20;  // 新宽度值
	$(".room-recommend .block-live").css({width:new_tjImg}).show();
}