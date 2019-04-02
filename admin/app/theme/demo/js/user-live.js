//上传
function do_upload_video(){
	var file_id = $("#video_upload input[name='file_id']").val();
	var cate = $(".modify-live-FMS input[name='title']").val();
    var room_title = $("input[name='room_name']").val();
    var live_image = $("input[name='live_image']").val();
    var is_live_pay = $("select[name='is_live_pay']").val();
	$.ajax({
		url: APP_ROOT + "/mapi/index.php?ctl=user&act=upload&itype=app",
		type: 'POST',
		dataType: "json",
		data: {
            "file_id": file_id,
            "title":cate,
            "room_title": room_title,
            "live_image": live_image,
            "is_live_pay": is_live_pay
        },
		async: false,
		success: function (result) {
			if (result.status == 1) {
				$.showSuccess("上传视频成功",function(){location.reload()});
			} else {
				if (result.error) {
					$.showErr(result.error);
				} else {
					$.showErr("操作失败");
				}
			}
		}
	})
}
var getSignature = function(callback){
    $.ajax({
        url: APP_ROOT + '/mapi/index.php?ctl=user&act=new_sign&itype=app',
        type: 'POST',
        dataType: 'json',
        success: function(res){
            if(res.status ==1 && res.signature) {
                callback(res.signature);
            } else {
                $.showErr('获取签名失败');
            }

        }
    });
};
$("#video_file").on('change',function(e){
    var videoFile = this.files[0];
    var resultMsg = qcVideo.ugcUploader.start({
        videoFile: videoFile,
        getSignature: getSignature,
        success: function(result){
            console.log('上传成功的文件类型：' + result.type);
        },
        error: function(result){
            console.log('上传失败的文件类型：' + result.type);
            console.log('上传失败的原因：' + result.msg);
        },
        progress: function(result){
            var percent =  Math.floor(result.curr * 100);
            $("#float-video-progress").show().find("#video-progress").css({"width":percent+"%"}).html(percent+"%");
            if(percent == 100){
                $("#float-video-progress").find("#video-progress").css({"width":"0"}).html("").end().hide();
            }
            console.log('上传进度的文件类型：' + result.type);
            console.log('上传进度的文件名称：' + result.name);
            console.log('上传进度：' + result.curr);
        },
        finish: function(result){
            $("#file_id").val(result.fileId);
            console.log('上传结果的fileId：' + result.fileId);
            console.log('上传结果的视频名称：' + result.videoName);
            console.log('上传结果的视频地址：' + result.videoUrl);
        }
    });
    if(resultMsg){
        $('#video_file1')[0].reset();
    }
});

$("#btn_upload").on('click',function(){
    $('#video_file').click();
});