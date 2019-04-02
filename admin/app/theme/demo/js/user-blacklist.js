$(".supervise").click(function(){
	var elems = $(".btn-icon");
	if(! elems.length && this.innerHTML=='解除黑名单'){
		$.showToast('列表为空，无需操作');
		return false;
	}
	this.innerHTML=(this.innerHTML=='解除黑名单'?'取消解除黑名单':'解除黑名单');
	$(".btn-icon").slideToggle(100);
});
$(".j-remove-blacklist").on('click',function(){
	var obj = $(this);
	$.showConfirm('<p style="padding:20px 0;text-align:center;">确认解除黑名单</p>',function(){
		$(obj).parent().remove();
		var id=$(obj).attr("data-id");
		var url=APP_ROOT+"/mapi/index.php?ctl=user&act=del_black&itype=app&black_user_id="+id;
		$.ajax({
	      url:url,
	      type:"POST",
	      dataType:"json",
	      success:function(html)
	      {
	      	var error=html.error;
	        $.showSuccess(error);
	      },
	      error:function()
	      {
	      	$.showErr("移除失败 （ T 。T ）");
	      }
	    });
	});
});