var ErrorCode = qcVideo.get('ErrorCode')
	, JSON = qcVideo.get('JSON')
	, Log = qcVideo.get('Log')
	;
ErrorCode.UN_SUPPORT_BROWSE !== qcVideo.uploader.init({
	web_upload_url: 'http://vod.qcloud.com/v2/index.php',
	upBtnId: 'btn_upload',
	isTranscode: true,
	isWatermark: false,
	secretId: secretId,
	getSignature: function (argStr, done) {
		var url = APP_ROOT + '/mapi/index.php?ctl=user&act=sign&itype=app&args=' + encodeURIComponent(argStr);
		$.get(url, function (res) {
			if(res.status){
				done(res['result']);
			} else {
				$.weeboxs.close("upload-video-box");
				$.showErr(res.error);
			}
		}, 'json');
	},
	after_sha_start_upload: true,
	sha1js_path: APP_ROOT + '/public/js/calculator_worker_sha1.js',
	disable_multi_selection: true,
	transcodeNotifyUrl: APP_ROOT + '/mapi/index.php?ctl=user&act=video_callback&itype=app',
	classId: null
}, {
		onFileUpdate: function (args) {
			console.log(args['percent']);
			
			var percent = Math.floor(args['serverFileId'] ? 100 : args['percent'] || args['code'] * 5);
			if(args['code'] >= 5 && percent < 80) {
				percent += 20;
			}
			$("#float-video-progress").show().find("#video-progress").css({"width":percent+"%"}).html(percent+"%");
			if(!args['serverFileId']){
				return;
			}
			$.post('/mapi/index.php?ctl=user&act=upload&itype=app', { file_id: args['serverFileId'] }, function (res) {
				if (res.status) {
					$("#float-video-progress").find("#video-progress").css({"width":"0"}).html("").end().hide();
					$.showSuccess('成功上传');
                	location.reload();
				} else {
					$.showErr(res.error);
				}
			}, 'json');
		},
		onFileStatus: function (info) {
			// Log.debug('各状态总数-->', JSON.stringify(info));
		},
		onFilterError: function (args) {
			$.showErr(args.message);
		}
	});