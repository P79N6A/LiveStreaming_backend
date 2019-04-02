function p(e) {
    var t = $(e).find(".stars").width(),
    n = $(window).width();
    t + 60 > n ? $(e).find(".arrows").addClass("insetArrow") : $(e).find(".arrows").removeClass("insetArrow")
}
function v() {
    var e = {},
    t = $(".index-shuffer").find(".stars-wall").width();
    return t < 1200 ? (e.width = t / 4, e.maxSlides = 4) : t >= 1460 ? (e.width = t / 5, e.maxSlides = 5) : (e.width = t / 5, e.maxSlides = 5),
    e
}
function h(e) {
    var t = !1;
    $(e).width();
    return t = !0
}

function s(){
    var e, t, n = v(),
    r = {
        slideWidth: n.width,
        maxSlides: n.maxSlides + 3,
        moveSlides: n.maxSlides,
        slideMargin: 0,
        speed: 500,
        easing: $("html").hasClass("ie8") ? "linear": "ease-in",
        pager: !1,
        infiniteLoop: !0,
        nextSelector: $(".index-shuffer .arrow-right a"),
        prevSelector: $(".index-shuffer .arrow-left a"),
        nextText: "",
        prevText: "",
        onSliderResize: function(e) {
            if (h(".index-shuffer .stars-wall")) {
                var n = v();
                t.resetSlider({
                    slideWidth: n.width,
                    moveSlides: n.maxSlides
                }),
                t.redrawSlider(),
                t.goToSlide(0, null, 0)
            }
            p(".index-shuffer")
        }
    };
    p(".index-shuffer"),
    t = $(".index-shuffer .stars-lists").bxSlider(r);
}
$(function(){

    s();

	//视频切换
	$("#j-select_video").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:300});
	$(".j-room-a a").on('click',function(){
		var a_top = $(this).position().top;
		var arrow_top = a_top+10;
		$(".slider-arrow").css("top",+arrow_top+"px");
	});
    //视频进入直播间按钮
    $("#js_room_video").hover(
        function () {
            $(".enter-link").css("opacity","1");
        },
        function () {
            $(".enter-link").css("opacity","0");
        }
    );
    $(".enter-link").hover(
        function () {
            $(this).css("opacity","1");
        },
        function () {
            $(this).css("opacity","0");
        }
    );

	//排行榜切换
	$("#J-ranking-charm").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500});
	$("#J-ranking-plute").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500});
    //排行榜格式化
    $('.m-Top-list').each(function(){
        var li_length = $(this).children("li").length;
        var li_even = $(this).children("li:even");
        var li_odd = $(this).children("li:odd");
        li_even.addClass("list-odd");
        li_odd.addClass("list-even");
        if(li_length % 2){
            $(this).children("li").last().addClass("list-last2");
        }else{
            $(this).children("li").last().addClass("list-last");
            $(this).children("li").eq(li_length-2).addClass("list-last2");
        };
    });
	// 首屏直播
    if(typeof playerInfo !== 'undefined' && playerInfo.length > 0){
        var container = $("#js_room_video");
        var new_playerInfo = playerInfo[0];
        new_playerInfo.width = $(container).width();
        new_playerInfo.height = $(container).height();
        var player =  new TcPlayer('js_room_video', new_playerInfo);

    	$(".j-room-a").find("a").on('click',function(i){
            new_playerInfo = playerInfo[$(this).index()];
			new_playerInfo.width = $(container).width();
			new_playerInfo.height = $(container).height();
            $('#js_room_video').empty();
			player = new TcPlayer("js_room_video", new_playerInfo);

            $('.enter-link').attr('href', $(this).attr('href'));
            return false;
    	});
    }
});