$(document).on("pageInit", "#page-user_center-authent", function(e, pageId, $page) {

	// 身份认证
	get_file_fun('upload-business_card');
	get_file_fun('upload-work_card');
	get_file_fun('upload-work_contract');

	$("#J-save").on('click', function(){

		var data = {
			"business_card": $("#upload-business_card-image").attr("src"),
			"work_card": $("#upload-work_card-image").attr("src"),
			"work_contract": $("#upload-work_contract-image").attr("src")
		};

		if($.checkEmpty(data.business_card)){
			$.toast("请上传名片照片");
			return false;
		}
		else if($.checkEmpty(data.work_card)){
			$.toast("请上传工作牌照片");
			return false;
		}
		else if($.checkEmpty(data.work_contract)){
			$.toast("请上传工作合同正面");
			return false;
		}

		handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=attestation",data).done(function(result){
 			$.toast(result, 1000);
	    }).fail(function(err){
	        $.toast(err);
	    });
	});

});