$(".m-exchange").slide({titCell:".hd a", mainCell:".bd", titOnClassName:"active", trigger:"click", delayTime:500});

var ajax_diamonds_form = $("#ajax-diamonds-form");
var ajax_envelopes_form = $("#ajax-envelopes-form");

/* 兑换秀豆 */
// 兑换秀豆提交表单
$(ajax_diamonds_form).find("#J-submit-diamonds").on('click',function(){
	
});

// 选择兑换数额
$(ajax_diamonds_form).find(".money-label").on('click',function(){
	$(ajax_diamonds_form).find(".money-label").removeClass("checked");
	$(this).addClass("checked");
});
// 选择兑换其他数额
$(ajax_diamonds_form).find("input[name='other_money']").on('input propertychange', function(){
	var other_money=$(this).val();
	if(other_money){
		$(ajax_diamonds_form).find(".money-label").removeClass("checked");
		
	}
	else{
		$(ajax_diamonds_form).find(".money-label").eq(0).addClass("checked");
	}
});

/* 兑换红包 */
// 兑换红包提交表单
$("#J-submit-envelopes").on('click',function(){

});
// 选择兑换数额
$(ajax_envelopes_form).find(".money-label").on('click',function(){
	$(ajax_envelopes_form).find(".money-label").removeClass("checked");
	$(this).addClass("checked");
});
// 选择兑换其他数额
$(ajax_envelopes_form).find("input[name='other_money']").on('input propertychange', function(){
	var other_money=$(this).val();
	if(other_money){
		$(ajax_envelopes_form).find(".money-label").removeClass("checked");
	}
	else{
		$(ajax_diamonds_form).find(".money-label").eq(0).addClass("checked");
	}
});