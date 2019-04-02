$(document).on('pageInit', '#page-user_center-user_center', function(){

	get_file_fun('upload-avatar', function(){

		data.head_image = $("#upload-avatar-image").attr("src");

		handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=user_save_wpk",data).done(function(result){
 			$.toast(result, 1000);
	    }).fail(function(err){
	        $.toast(err);
	    });
	});

  	$("#city").cityPicker({
    	toolbarTemplate: '<header class="bar bar-nav">\
    						<button class="button button-link pull-right close-picker">确定</button>\
    						<h1 class="title">选择所在城市</h1>\
    					  </header>',
    	onClose: function(){
    		var city = $("input[name='city']").val(), arry_city = city.split(' ');
    		data.province = arry_city[0];
    		data.city = arry_city[1];
    		handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=user_save_wpk",data).done(function(result){
	 			$.toast(result, 1000);
		    }).fail(function(err){
		        $.toast(err);
		    });
    	}
  	});


	$(document).on('click','.open-setting', function () {
		var self = $(this), popup_type = self.attr("popup_type");
  		$.popup("."+popup_type);
	});
	$(document).on('click', '.J-setting', function(){
		var self = $(this), data_type = self.attr("data_type"), data_type_val = self.find("input[name='"+data_type+"']").val(), data_title = self.find(".item-title").html();
	 	
	 	$(".popup-"+data_type).find(".item-after").html('');
	 	self.find(".item-after").append('<i class="icon iconfont">&#xe61c;</i>');

	 	for(var p in data){
		 	if(p == data_type){
		 		data[p] = data_type_val;
			}
	 	}
	 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=user_save_wpk",data).done(function(result){
 			$.toast(result, 1000);
 			setTimeout(function(){
 				$.closeModal();
 				$("#text-"+data_type).html(data_title);
 			},1000);
	    }).fail(function(err){
	        $.toast(err);
	    });
	});

	$(document).on('click', '.J-setting-text', function(){
		var self = $(this), data_type = self.attr("data_type"), data_tip = self.attr("data_tip"), form_data = $(".popup-"+data_type).find("input[name='"+data_type+"']").val();
		if(empty(form_data)){
			$.toast(data_tip+"不能为空");
			return false;
		}
		for(var p in data){
		 	if(p == data_type){
		 		data[p] = form_data;
			}
	 	}
	 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=user_save_wpk",data).done(function(result){
 			$.toast(result, 1000);
 			setTimeout(function(){
 				$.closeModal();
 				$("#text-"+data_type).html(form_data);
 			},1000);
	    }).fail(function(err){
	        $.toast(err);
	    });
	});


});