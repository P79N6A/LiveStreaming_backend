/**
 * Created by Administrator on 2017/6/19.
 */
var ErrorCode = qcVideo.get('ErrorCode'),
    JSON = qcVideo.get('JSON'),
    Log = qcVideo.get('Log'),
    util = qcVideo.get('util')
    , Code = qcVideo.get('Code')
    , Version = qcVideo.get('Version');
ErrorCode.UN_SUPPORT_BROWSE !== qcVideo.uploader.init({
    web_upload_url: 'http://vod.qcloud.com/v2/index.php',
    upBtnId: 'upload_video',
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
        var desc_video = $("#desc_video");

        $("#float-video-progress").show().find("#video-progress").css({"width":percent+"%"}).html(percent+"%");
        if(percent == 100){
            $("#float-video-progress").find("#video-progress").css({"width":"0"}).html("").end().hide();
        }
        if(args['serverFileId']){
            $("#file_id").val(args['serverFileId']);
            $("#delete").attr("type","button");
        }
    },
    onFileStatus: function (info) {
        // Log.debug('各状态总数-->', JSON.stringify(info));
    },
    onFilterError: function (args) {
        $.showErr(args.message);
    }
});
$('#delete').on('click',function () {
        var fileId = $("#file_id").val();
        Log.debug('delete', fileId);
        //@api 删除文件
        qcVideo.uploader.deleteFile(fileId);
        $("#file_id").val("");
        this.remove();
});