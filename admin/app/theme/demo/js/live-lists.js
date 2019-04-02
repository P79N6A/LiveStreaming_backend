//左侧列表
function auto_left_height() {
	//左侧列表高度
	var w_height = $(window).height();
	var b_height = $("#bodywrapper").height();
	var live_height = $(".live-wrap").height();
	// console.log(w_height);
	$("#left-list").css('height', w_height - 51);
};
function auto_layout_l_w() {
	var w_width = $(window).width();
	var w_mainbody = w_width - 94;
	// 推荐视频

	var ratio = 275 / 157;
	var w_tjImg = 240;  // 宽度最小阀值
	var num_tjImg = Math.floor((w_mainbody) / (w_tjImg + 40));  // 一行显示图片个数
	var new_tjImg_w = ((w_mainbody) / num_tjImg) - 40;  // 新宽度值
	var new_Img_w = new_tjImg_w - 20;
	var new_Img_h = new_Img_w / ratio;
	$(".block-live").css({ width: new_tjImg_w }).show().find("span.block-live-img").css({ height: new_Img_h });
};
//话题
$(function () {
	/*var func={
		sbsb:function(){
			auto_layout_l_w();
		}
	};
	init_ajax_page_click(func);*/
	init_ajax_page_click(1);
	auto_left_height();
	auto_layout_l_w();
	// 监听拖动窗口
	window.onresize = function () {
		auto_layout_l_w();
	};

	//点击切换直播
	$(".j-left-item").click(function () {
		$(".j-left-item").removeClass('active');
		$(this).addClass('active');
	});
	$(".all-live").click(function () {
		$(".m-live").show();
		$(".follow-live").hide();
		$(".history-live").hide();
	});
	$(".topic").click(function () {
		$(".m-live").hide();
		$(".topic-live").show();
		$(".history-live").hide();
	});
	
	$(".history").click(function () {
		$(".m-live").hide();
		$(".topic-live").hide();
		$(".history-live").show();
	});
	$(".follow").click(function () {
		$(".m-live").hide();
		$(".follow-live").show();
	});
	$(".topic-item").each(function () {
		var cate_id = $(this).attr("data-id");
		var jump_cate = GetQueryString("cate_id");
		var cate_val = cate_id && getQueryStringValue(cate_id, "cate_id");
		if (jump_cate == undefined || null) {
			return false; //跳出循环
		}
		if (jump_cate == cate_val) {
			$(".topic-item").removeClass('active');
			$(this).addClass('active');
			return false; //跳出循环
		}
	});
	$(".j-topic-live").on('click', function () {
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=live_list&tmpl_pre_dir=inc", "", "html").done(function (html) {
			var dom = $('<div>' + html + '</div>');
			$(".topic-live .m-live-list").html(dom.find(".m-live-list").html());
			$(".topic-live .m-page").html(dom.find(".m-page").html());
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
	});
	
	//话题
	$(".topic-live").on('click', ".topic-item", function () {
		$(".topic-item").removeClass('active');
		$(this).addClass('active');

		var cate_id = $(this).attr("data-id");
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=live_list&tmpl_pre_dir=inc", { cate_id: cate_id }, "html").done(function (html) {
			var dom = $('<div>' + html + '</div>');
			$(".topic-live .m-live-list").html(dom.find(".m-live-list").html());
			$(".topic-live .m-page").html(dom.find(".m-page").html());
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
	});

	$(".topic-live").on('click', ".m-page a", function () {
		var url = $(this).attr("href").replace('act=video_list', 'act=live_list');
		handleAjax.handle(url + "&tmpl_pre_dir=inc", {}, "html").done(function (html) {
			var dom = $('<div>' + html + '</div>');
			$(".topic-live .m-live-list").html(dom.find(".m-live-list").html());
			$(".topic-live .m-page").html(dom.find(".m-page").html());
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
		return false;
	});
	if(jump_type==2){
		$(".m-live").hide();
		$(".follow-live").show();
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=focus_list&tmpl_pre_dir=inc", "", "html").done(function (html) {
			$(".follow-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
	}
	// 关注
	$(".j-focus-live").on('click', function () {
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=focus_list&tmpl_pre_dir=inc", "", "html").done(function (html) {
			$(".follow-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
	});

	$(".follow-live").on('click', ".m-page a", function () {
		var url = $(this).attr("href");
		handleAjax.handle(url, "", "html").done(function (html) {
			$(".follow-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
		return false;
	});

	// 历史
	$(".j-history-live").click(function () {
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=history_list&tmpl_pre_dir=inc", "", "html").done(function (html) {
			$(".history-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
	});

	$(".history-live").on('click', ".m-page a", function () {
		var url = $(this).attr("href");
		handleAjax.handle(url, "", "html").done(function (html) {
			$(".history-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
		return false;
	});

	$(".history-live").on('click', ".btn-del", function () {
		var room_id = $(this).data("id");
		handleAjax.handle(APP_ROOT + "/index.php?ctl=video&act=history_list&tmpl_pre_dir=inc", {del_room_id: room_id}, "html").done(function (html) {
			$(".history-live").html(html);
			auto_layout_l_w();
		}).fail(function (err) {
			$.showToast('加载失败');
		});
		return false;
	});
});