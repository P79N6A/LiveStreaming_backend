(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'));
    } 
    else {
        factory(jQuery);
    }
});

/**
    图片裁剪
    参数：aspectRatio_num 默认裁剪尺寸
**/
function bind_CropAvatar(aspectRatio_num,callbackFuc) {
    'use strict';
    var console = window.console || { log: function () {} };
    var aspectRatio_num = aspectRatio_num;
    var callbackFuc = callbackFuc || null;

    function CropAvatar($element) {
        this.$container = $element;

        this.$avatarView = this.$container.find('.avatar-view');
        this.$avatar = this.$avatarView.find('img');
        this.$avatarModal = this.$container.find('#avatar-modal');
        this.$loading = this.$container.find('.loading');

        this.$avatarForm = this.$avatarModal.find('.avatar-form');
        this.$avatarUpload = this.$avatarForm.find('.avatar-upload');
        this.$avatarSrc = this.$avatarForm.find('.avatar-src');
        this.$avatarData = this.$avatarForm.find('.avatar-data');
        this.$avatarInput = this.$avatarForm.find('.avatar-input');
        this.$avatarSave = this.$avatarForm.find('.avatar-save');
        this.$avatarBtns = this.$avatarForm.find('.avatar-btns');

        this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
        this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

        this.init();
        // console.log(this);
    }

    CropAvatar.prototype = {
        constructor: CropAvatar,

        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },

        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;

            if (!this.support.formData) {
                this.initIframe();
            }

            //this.initTooltip();
            //this.initModal();
            this.addListener();
        },

        addListener: function () {
            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
        },

        initPreview: function () {
            var url = this.$avatar.attr('src');

            this.$avatarPreview.empty().html('<img src="' + url + '">');
        },

        initIframe: function () {
            var target = 'upload-iframe-' + (new Date()).getTime(),
            $iframe = $('<iframe>').attr({
                name: target,
                src: ''
            }),
            _this = this;

            // Ready ifrmae
            $iframe.one('load', function () {
                // respond response
                $iframe.on('load', function () {
                    var data;
                    try {
                        data = $(this).contents().find('body').text();
                    } 
                    catch (e) {
                        console.log(e.message);
                    }
                    if (data) {
                        try {
                            data = $.parseJSON(data);
                        }
                        catch (e) {
                            console.log(e.message);
                        }
                        _this.submitDone(data);
                    }
                    else {
                        _this.submitFail('Image upload failed!');
                    }
                    _this.submitEnd();
                });
            });

            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },

        click: function () {
            this.$avatarModal.modal('show');
            this.initPreview();
        },

        change: function () {
            var files,file;

            if (this.support.datauri) {
                files = this.$avatarInput.prop('files');
                if (files.length > 0) {
                    file = files[0];
                    if (this.isImageFile(file)) {
                        if (this.url) {
                            URL.revokeObjectURL(this.url); // Revoke the old one
                        }

                        this.url = URL.createObjectURL(file);
                        this.startCropper();
                    }
                }
            }
            else {
                file = this.$avatarInput.val();

                if (this.isImageFile(file)) {
                    this.syncUpload();
                }
            }
        },

        submit: function () {
            if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                return false;
            }

            if (this.support.formData) {
                this.ajaxUpload();
                return false;
            }
        },

        rotate: function (e) {
            var data;

            if (this.active) {
                data = $(e.target).data();

                if (data.method) {
                    this.$img.cropper(data.method, data.option);
                }
            }
        },

        isImageFile: function (file) {
            if (file.type) {
                return /^image\/\w+$/.test(file.type);
            }
            else {
                return /\.(jpg|jpeg|png|gif)$/.test(file);
            }
        },

        // 开始裁剪
        startCropper: function () {
            var _this = this;

            if (this.active) {
                this.$img.cropper('replace', this.url);
            }
            else {
                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: aspectRatio_num,
                    preview: this.$avatarPreview.selector,
                    strict: false,
                    autoCropArea:1,

                    // 当改变剪裁容器或图片时的事件函数
                    crop: function (data) {
                        var json = [
                          '{"x":' + data.x,
                          '"y":' + data.y,
                          '"height":' + data.height,
                          '"width":' + data.width,
                          '"rotate":' + data.rotate + '}'
                        ].join();
                        _this.$avatarData.val(json);
                    }
                });

                this.active = true;
            }
        },

        // 结束裁剪
        stopCropper: function () {
            if (this.active) {
                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
            }
        },

        // 上传图片
        ajaxUpload: function () {
            var url = this.$avatarForm.attr('action'),
                data = new FormData(this.$avatarForm[0]),
                _this = this;
            $.ajax(url, {
                type: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,

                beforeSend: function () {
                  _this.submitStart();
                },

                success: function (data) {
                    _this.submitDone(data);
                    if(callbackFuc!=null){
                        callbackFuc.call(this);
                    }
                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {
                  _this.submitFail(textStatus || errorThrown);
                },

                complete: function () {
                  _this.submitEnd();
                }
            });
        },

        syncUpload: function () {
            this.$avatarSave.click();
        },

        submitStart: function () {
            // this.$loading.show();
            // $.showIndicator();
            $.showLoading('<span class="alert-text">图片上传中...</span>');
        },

        submitDone: function (data) {
            // console.log(data);

            if ($.isPlainObject(data)) {
                if (data.state === 200 && data.result) {
                    this.url = data.result;

                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        $("input[name='"+data.dst+"']").val(this.url);
                        $("#"+data.dst).attr('src', this.url);
                        $("#"+data.dst+"_a").attr('href', this.url);
                        this.cropDone();
                    }
                    else {
                        this.uploaded = true;

                        this.$avatarSrc.val(this.url);
                        this.startCropper();
                    }

                    this.$avatarInput.val('');
                }
                else if (data.message) {
                    this.alert(data.message);
                }
            }
            else {
                this.alert('Failed to response');
            }
        },

        submitFail: function (msg) {
            this.alert(msg);
        },

        submitEnd: function () {
            // this.$loading.hide();
            // $.hideIndicator();
            $.hideLoading();
        },

        cropDone: function () {
            $.weeboxs.close('avatar-box');
            this.$avatarForm.get(0).reset();
           // alert(this.url);
           // $("#new_view").attr('src', this.url);
            this.stopCropper();
            //this.$avatarModal.modal('hide');

        },

        alert: function (msg) {
            $(".alert-danger").remove();
            var $alert = [
                '<div class="alert alert-danger avater-alert f_red">',
                  '<i class="icon iconfont">&#xe630;</i>&nbsp;',
                  msg,
                '</div>'
              ].join('');

            this.$avatarUpload.after($alert);
        }
    };
    $(function () {
        return new CropAvatar($('#crop-avatar'));
    });
};