$(document).on("pageInit", "#page-pay-qrcode", function(e, pageId, $page) {
	var vm_pay_qrcode = new Vue({
  		el: '#vscope-pay_qrcode',
	  	data: paramets,
	  	mounted: function(){
	  		this.wx_pay();
	  	},
	  	methods: {
	  		wx_pay: function(){
	  			// 微信支付
	  			var self = this, data_json = { pay_id: this.pay_id, vip_id: this.vip_id, qrcode_id: this.qrcode_id };
			 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=pay&act=pay", data_json, '', 1).done(function(result){
			 		self.is_completed = true;
			 		setInterval(function(){self.sec = self.sec + 1;},1000);
		        	location.href = result.jsApiParameters.notify_url;
			    }).fail(function(err){
			        $.toast(err);
			    });
	  		},
	  		start_time: function(){
	  			this.sec = setInterval(function(){this.sec = this.sec + 1;},1000);
	  		},
	  		refresh: function(){
	  			location.reload();
	  		}
	  	},
	  	filters: {
	  		format_int: function(value){
	  			return Math.round(value);
	  		}
	  	}
  	});

});