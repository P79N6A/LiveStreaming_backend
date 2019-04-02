$(document).on("pageInit","#page-user_center-add_feedback", function(e, pageId, $page) {
	var vm_add_feedback = new Vue({
  		el: '#vscope-add_feedback',
	  	data: {
	  		content: "",
	  	},
  	 	methods: {
		    submit: function (event) {

		    	vm_add_feedback.content = $.emoji2Str(vm_add_feedback.content);
		    	
		    	if(empty(this.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}
		    	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=feedback",{"content": this.content}).done(function(msg){
			       	$.toast(msg,1000);
                	setTimeout(function(){
                		location = APP_ROOT+"/weixin/index.php?ctl=user_center&act=index";
                	},1000);
			    }).fail(function(err){
			        $.toast(err);
			    });
		    }
	  	}
	});
});