$(document).on("pageInit", "#page-course-vip", function(e, pageId, $page) {
	var vm_course_vip = new Vue({
  		el: '#vscope-course_vip',
	  	data: data,
	  	beforeCreate: function(){
	  		$.showIndicator();
	  		var self = this;
	  		handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=course&act=vip", '', '', 1).done(function(result){

	  			self.user = result.user;
	  			self.vip_list = result.vip_list;
	  			self.vip_level = result.vip_level;
	  			self.cost = result.vip_list[0].cost;
	  			self.vip_id = result.vip_list[0].id;
	  			self.item_vip_lv = result.vip_list[0].vip_lv;
	  			self.active_name = result.vip_list[0].name;
	  			self.pid = result.pid;

	  			self.$nextTick(function(){
	  				$.hideIndicator();
	  			});

		    }).fail(function(err){
		        $.toast(err);
		    });
	  	},
	  	methods: {
	  		change_vip: function(item){
	  			// 选择会员付费

	  			var self = this;

	  			self.vip_id = item.id;
	  			self.cost = item.cost;
	  			self.item_vip_lv = item.vip_lv;
	  			self.active_name = item.name;


	  		},
	  		wx_pay: function(){
	  			// 微信支付
	  			var self = this, data_json = { "pay_id": this.pay_id, "vip_id": this.vip_id, pid: this.pid };
			 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=pay&act=pay", data_json, '', 1).done(function(result){
			 		console.log(result);
		        	location.href = result.jsApiParameters.notify_url;

			    }).fail(function(err){
			        $.toast(err);
			    });
	  		}
	  	},
	  	filters: {
	  		format_int: function(value){
	  			return Math.round(value);
	  		}
	  	}
  	});

});