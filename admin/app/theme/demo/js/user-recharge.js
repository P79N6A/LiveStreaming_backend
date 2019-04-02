/*充值*/
var recharge_form = $("#ajax-recharge-form");
// 充值提交表单
$(recharge_form).find("#J-submit").on('click',function(){
	
});
// 充值方式
$(recharge_form).find(".pay_type").on('click',function(){
	$(this).addClass("checked").siblings().removeClass("checked");
});
// 选择充值金额
$(recharge_form).find(".money-label").on('click',function(){
	$(".money-label").removeClass("checked");
	$(this).addClass("checked");
});
// 选择其他金额
$(recharge_form).find("input[name='other_money']").on('input propertychange', function(){
	var other_money=$(this).val();
	if(other_money){
		$(".money-label").removeClass("checked");
	}
	else{
		$(".money-label").eq(0).addClass("checked");
	}
});