(function() {
    var $ = qcVideo.get('$');
    var Version = qcVideo.get('Version');
    var account = accountDone();
})();
/**
 *
 * @param upBtnId 上传按钮ID
 * @param secretId 云api secretId
 * @param isTranscode 是否转码
 * @param isWatermark 是否设置水印
 * @param [transcodeNotifyUrl] 转码成功后的回调
 * @param [classId] 分类ID
 */
function accountDone() {
    file_args_id = false;
    var $ = qcVideo.get('$'),
        ErrorCode = qcVideo.get('ErrorCode'),
        Log = qcVideo.get('Log'),
        JSON = qcVideo.get('JSON'),
        util = qcVideo.get('util'),
        Code = qcVideo.get('Code'),
        Version = qcVideo.get('Version');
    qcVideo.uploader.init(
        //1: 上传基础条件
        {
            web_upload_url: 'http://vod.qcloud.com/v2/index.php',
            secretId: qcloud_secret_id, // 云api secretId
            secretKey: qcloud_secret_key,
            //server端实现逻辑
            // 1:首先 将argStr ，使用secretKey做sha1加密，得到结果 result
            // 2:最后 将result做base64后返回
            getSignature: function(argStr, cb) {
                //注意：出于安全考虑， 服务端接收argStr这个参数后，需要校验其中的Action参数是否为 "MultipartUploadVodFile",用来证明该参数标识上传请求
                console.log('init')
                $.ajax({
                    'dataType': 'json',
                    'url': APP_ROOT + 'm.php?m=VideoPlayback&a=sign&argStr=' + encodeURIComponent(argStr),
                    'success': function(d) {
                        cb(d['result']);
                    }
                });
            },
            upBtnId: 'btn_upload', //上传按钮ID（任意页面元素ID）
            isTranscode: true, //是否转码
            isWatermark: false //是否设置水印
                ,
            after_sha_start_upload: false //sha计算完成后，开始上传 (默认关闭立即上传)
                ,
            sha1js_path: APP_ROOT + 'admin/Tpl/default/Common/js/calculator_worker_sha1.js' //计算sha1的位置
                ,
            disable_multi_selection: true //禁用多选 ，默认为false
                ,
            transcodeNotifyUrl: APP_ROOT + "m.php?m=Course&a=video_callback" //(转码成功后的回调地址)isTranscode==true,时开启； 回调url的返回数据格式参考  http://www.qcloud.com/wiki/v2/MultipartUploadVodFile
                ,
            classId: null
        }
        //2: 回调
        , {
            /**
             * 更新文件状态和进度
             * @param args { id: 文件ID, size: 文件大小, name: 文件名称, status: 状态, percent: 进度 speed: 速度, errorCode: 错误码,serverFileId: 后端文件ID }
             */
            onFileUpdate: function(args) {
                console.log(args)
                switch (args.code) {
                    case 1:
                    case 3:
                        $('#start_upload').hide();
                        message = (args.percent ? args.percent : '0') + '%' + '(' + args.code_name + (args.speed ? ' 速度：' + args.speed + '' : '') + ')';
                        $('#progress').show();
                        $('#file_name').html(args.name);
                        $('#sha').css('width', args.percent + '%');
                        $('#sha>span').html(message);
                        break;
                    case 2:
                        message = '100%' + '(' + args.code_name + ')';
                        $('#sha').css('width', '100%').removeClass('progress-bar-striped active');
                        $('#sha>span').html(message);
                        if (file_args_id == false) {
                            file_args_id = args.id;
                        } else if (file_args_id != args.id) {
                            qcVideo.uploader.deleteFile(file_args_id);
                            file_args_id = args.id;
                        }
                        file_type = args.name.replace(args.filename + '.', "").toLowerCase();
                        if (file_type != 'mp4' && file_type != 'ogg') {
                            $('#error').html('HTML5播放器只支持mp4,ogg格式视频');
                            $('#error').show();
                        } else {
                            $('#error').hide();
                            $('#start_upload').show();
                        }
                        break;
                    case 4:
                    case 5:
                        $.get(APP_ROOT + "m.php?m=CourseSeason&a=uploadHeart");
                        message = (args.percent ? args.percent : '0') + '%' + '(' + args.code_name + (args.speed ? ' 速度：' + args.speed + '' : '') + ')';
                        $('#up').css('width', args.percent + '%');
                        $('#up>span').html(message);
                        break;
                    case 6:
                        $('#start_upload').hide();
                        message = '100%' + '(' + args.code_name + ')';
                        $('#up').css('width', '100%').removeClass('progress-bar-striped active');
                        $('#up>span').html(message);
                        break;
                }
                if (args.code == Code.UPLOAD_DONE) {
                    $('#file_id').val(args.serverFileId);
                    $('#up>span').html('100% 上传完成!');
                    $('#video_url').val('');
                    times = '.';
                    getVideoUrlById = function() {
                        $.post(APP_ROOT + "m.php?m=CourseSeason&a=getVideoUrlById", {
                            id: args.serverFileId
                        }, function(result) {
                            if (result.status == '1') {
                                $('#video').attr("src", result.url);
                                $('#video_url').val(result.url);
                                $('#up>span').html('100% 上传完成!');
                                if ($('#auto_save').prop('checked')) {
                                    insert_video_tx();
                                }
                            } else {
                                switch (times) {
                                    case '.':
                                    case '..':
                                    case '...':
                                    case '....':
                                    case '.....':
                                        times += '.';
                                        break;
                                    default:
                                        times = '.';
                                }
                                $('#up>span').html('100% 上传完成!(' + result.error + times + ')');
                                $('#video').attr("src", '');
                                console.log(result);
                                setTimeout(getVideoUrlById, 1000);
                            }
                        }, 'json');
                    }
                    getVideoUrlById();
                }
            },
            /**
             * 文件状态发生变化
             * @param info  { done: 完成数量 , fail: 失败数量 , sha: 计算SHA或者等待计算SHA中的数量 , wait: 等待上传数量 , uploading: 上传中的数量 }
             */
            onFileStatus: function(info) {
                // $('#count').text('各状态总数-->' + JSON.stringify(info));
                // console.log(info)
                // $('#error').hide();
            },
            /**
             *  上传时错误文件过滤提示
             * @param args {code:{-1: 文件类型异常,-2: 文件名异常} , message: 错误原因 ， solution: 解决方法}
             */
            onFilterError: function(args) {
                // console.log(args)
                var msg = args.message + (args.solution ? (';solution==' + args.solution) : '');
                $('#error').html(msg);
                $('#error').show();
            }
        });
    //事件绑定
    $('#start_upload').on('click', function() {
        //@api 上传
        qcVideo.uploader.startUpload();
    });
    $('#stop_upload').on('click', function() {
        //@api 暂停上传
        qcVideo.uploader.stopUpload();
    });
    $('#re_upload').on('click', function() {
        //@api 恢复上传（错误文件重新）
        qcVideo.uploader.reUpload();
    });
    $('#result').on('click', '[data-act="del"]', function(e) {
        var $line = $(this).parent();
        var fileId = $line.get(0).id;
        Log.debug('delete', fileId);
        // $line.remove();
        //@api 删除文件
        qcVideo.uploader.deleteFile(fileId);
    });
};