$(document).on("pageInit", "#page-course-vip_exchange", function(e, pageId, $page) {
	var vm_vip_exchange = new Vue({
  		el: '#vscope-vip_exchange',
	  	data: {
	  		code: ''
	  	},
	  	methods: {
	  		submit: function(){
	  			// 兑换码兑换
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=course&act=vip_exchange",{code: this.code}).done(function(result){

                    $.toast(result,1000);
                    setTimeout(function(){
                    	history.back();
                    },1000);


                }).fail(function(err){
                    $.toast(err);
                });
	  		}
	  	}
  	});
});