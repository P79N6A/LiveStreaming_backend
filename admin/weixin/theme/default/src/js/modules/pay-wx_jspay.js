$(document).on("pageInit", "#page-pay-wx_jspay", function(e, pageId, $page) {

	var vm_wx_pay = new Vue({
  		el: '#vscope-wx_pay',
	  	data: '',
	  	methods: {
	  		wx_pay: function(){
	  			// 微信支付
	  			self = this;
	  			if(self.type == "V4"){
                    self.callpay_1();
                }
                else{
                    self.callpay();
                }
	  		},
	  		callpay: function(){
	  			var self = this;
		 		if (typeof WeixinJSBridge == "undefined"){
		            if( document.addEventListener){
		                document.addEventListener('WeixinJSBridgeReady', self.jsApiCall, false);
		            }else if (document.attachEvent){
		                document.attachEvent('WeixinJSBridgeReady', self.jsApiCall); 
		                document.attachEvent('onWeixinJSBridgeReady', self.jsApiCall);
		            }
		        }else{
		            self.jsApiCall();
		        }
	  		},
	  		callpay_1: function(){
	  			wx.chooseWXPay(jsApiParameters);
	  		},
	  		jsApiCall: function(){
  			 	// jsApiParameters = JSON.parse(jsApiParameters);
		        //alert(typeof(jsApiParameters));
		        WeixinJSBridge.invoke(
		            'getBrandWCPayRequest',
		            jsApiParameters,
		            function(res){
		                //alert(jsApiParameters);
		                //alert(JSON.stringify(res));
		                if(res.err_msg=='get_brand_wcpay_request:fail'){
		                    $.alert('支付失败');
		                }
		                if(res.err_msg=='get_brand_wcpay_request:cancel '){
		                    $.alert('支付取消');
		                }
		                if(res.err_msg=='get_brand_wcpay_request:ok'){
		                   	// 支付成功
		                   	$.toast('支付成功',1000);
		                   	setTimeout(function(){
		                   		pid ? location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=detail&pid="+pid : location.href = APP_ROOT+"/weixin/index.php?ctl=user_center&act=index";
		                   	},1000);

		                }
		                else{

		                }
		            }
		        );
	  		}
	  	}
  	});
});