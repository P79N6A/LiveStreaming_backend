$(document).on("pageInit", "#page-course-detail", function(e, pageId, $page) {
    var current_time = 0;
    var vm_course_detail = new Vue({
        el: "#vscope-course_detail",
        data: {
            is_show_all_count: false,
            is_show_err_pop: false,
            is_show_err: false,
            is_show_more_bief: false,
            is_hide_video_type: false,
            is_hide_change_audio: false,
            show_err_text: "",
            is_audio: false,
            no_video: false,
            canot_view: false,
            is_vip: 0,
            vip: false,
            video_url: false,
            sound_url: false,
            current_time: 0,
            img: false,
            id: 0,
            course_content: course_content,
            is_show_video_bg: false,
            player_position: 0,
            playing_audio: false,  // 当前音频是否在播放
            is_normal_play: false  // 是否正常播放，去除播放记录定位
        },
        mounted: function() {
            this.reset_videobox();
            this.reSetConTop();
            this.has_more_bief();
            this.init_vedio();
        },
        methods: {
            play: function(){
               playerInit.play();
            },
            has_more_bief: function() {
                // 判断是否有查看详情
                var bief_inner_height = $(".bief-inner").height();
                var bief_con_height = $(".bief-con").height();
                if (bief_inner_height > bief_con_height) {
                    $(".j-show-more-bief").show();
                } else {
                    $(".j-show-more-bief").hide();
                }
            },
            reset_videobox: function() {
                var video_width = $(window).width();
                var video_height = $(window).width() * 9 / 16;
                $(".video-box").css({
                    "width": video_width,
                    "height": video_height
                });
            },
            reSetConTop: function() {
                // $(".other-con").css('top', $(window).width() * 0.5625);
                $(".pop-up").css('top', $(window).width() * 0.5625 + $(".detail-head").height());
                $.refreshScroller();
            },
            init_vedio: function() {
                this.runPlayer(course.data);
            },
            change_course: function(id, event) {
                // 选集

                handleAjax.handle(APP_ROOT + "/weixin/index.php?ctl=course&act=detail",{id: id},'', 1).done(function(result){
                    
                    vm_course_detail.is_show_more_bief = false;
                    vm_course_detail.is_audio = false;
                    vm_course_detail.player_position = 0;  // 选集，清空上一个视频的播放时间位置
                    vm_course_detail.course_content = result.data.content;

                    vm_course_detail.runPlayer(result.data)

                    $('.all-vedio-item[rel="' + id + '"]').addClass('active').siblings().removeClass("active");
                    $(event.target).addClass("active").siblings().removeClass("active");

                    document.title = result.data.title;
                    var $body = $('body');
                    var $iframe = $('<iframe src="/favicon.ico"></iframe>');
                    $iframe.on('load',function() {
                      setTimeout(function() {
                          $iframe.off('load').remove();
                      }, 0);
                    }).appendTo($body);

                }).fail(function(err){
                    $.toast(err);
                });

            },
            change_audio: function(id) {
                // 切换音频
                vm_course_detail.is_audio = !vm_course_detail.is_audio;
                vm_course_detail.playing_audio = false;
                if(vm_course_detail.runPlayer(false)){
                    this.is_audio ? $.toast("已切换音频模式") : $.toast("已切换视频模式");
                }
            },
            runPlayer: function(data) {
                var self = this;
                // var this =  vm_course_detail == undefined ? this : vm_course_detail;
                if (data != false) {
                    self.id = 0;
                    self.is_vip = 0;
                    self.vip = false;
                    self.video_url = false;
                    self.sound_url = false;
                    self.img = false;
                    self.no_video = false;
                    self.is_show_err_pop = false;
                    self.no_video = false;
                    self.is_show_err = false;
                    self.is_hide_change_audio = false;
                    self.show_err_text = "";
                    self.no_video = false;
                    self.canot_view = false;
                    self.playing_audio = false;
                    self.is_show_video_bg = false;
                    for (var i in data) {
                        if (self[i] != undefined) {
                            self[i] = data[i]
                        }
                    }
                }

                var file = self.is_audio ? self.sound_url : self.video_url;

                current_time = self.current_time;

                self.is_hide_video_type = self.is_vip ? false : true;


                self.$nextTick(function(){
                    if (self.is_vip > 0 && !self.vip) {
                        self.is_show_err_pop = true;
                        self.canot_view = true;
                        self.is_show_video_bg = false;
                    } else if (!file) {
                        self.no_video = true;
                        self.is_hide_change_audio = true;
                        self.is_show_video_bg = false;
                        console.log('no file')
                    } else {
                        if (!self.sound_url) {
                            self.is_hide_change_audio = false;
                        }
                        if (playerInit.getState() == null) {
                            playerInit.setup({
                                width: $(window).width(),
                                height: $(window).width() * 9 / 16,
                                stretching: "fill",  // 调整图像和视频以适应播放器尺寸 (uniform exactfit fill none)
                                controls: 1,
                                primary: "html5",
                                autostart: false,
                                file: file,
                                position: current_time,
                                preload:"auto",
                                skin:{
                                    name: 'roundster'
                                },
                                events: {
                                    onReady: self.onReady,
                                    onPlay: self.onPlay,
                                    onPause: self.onPause,
                                    onBuffer: self.onBuffer
                                }
                            });
                            playerInit.on('time', function(obj) {

                                if (obj.position - current_time > 10) {
                                    self.checkCurrentTime(obj.position)
                                }

                                // 当前播放位置是否与上一次播放记录位置相同
                                obj.position != current_time ? self.player_position = obj.position : self.player_position = current_time;


                                
                            });
                            playerInit.on('play', function(obj) {
                                !(self.is_normal_play) && playerInit.seek(current_time);
                            });
                            playerInit.on('seeked', function(obj) {
                                self.is_normal_play = true;
                            });
                        } else {
                            playerInit.load([{
                                file: file,
                                image: self.img
                            }]);

                        }
                        self.is_show_video_bg = true;
                        return true;
                    }
                    return false;
                })

            },
            checkCurrentTime: function(current_time) {
                var self = this;
                self.current_time = current_time;
                $.post(APP_ROOT + "/mapi/index.php?ctl=course&act=check&itype=wx", {
                    id: self.id,
                    current_time: self.current_time
                });
            },
            onReady: function() {
                // 视频准备就绪
                console.log("onPlay");
                vm_course_detail.is_show_all_count = false;
                this.is_vip ? vm_course_detail.is_hide_video_type = false : vm_course_detail.is_hide_video_type = true;

                var i = $("#video-top").find("video");
                i.attr({
                    "x5-video-player-type": "h5",
                    "x5-video-player-fullscreen": "true"
                }),
                i.attr("playsinline", !0);

            },
            onPlay: function() {
                // 视频播放中
                console.log("onPlay");
                vm_course_detail.checkCurrentTime(playerInit.getPosition());
                // vm_course_detail.is_hide_change_audio = true;
                vm_course_detail.is_show_all_count = false;
                vm_course_detail.is_show_video_bg = false;
                vm_course_detail.is_hide_video_type = true;

                if(this.is_audio){
                    // 当前播放是音频，则依旧显示封面（去除播放按钮）
                    vm_course_detail.playing_audio = true;
                }
                else{
                    // 当前播放是视频,则去除封面
                    vm_course_detail.playing_audio = false;
                }

            },
            onPause: function() {
                // 视频暂停
                console.log("onPause");
                vm_course_detail.checkCurrentTime(playerInit.getPosition());
                if (this.sound_url != undefined) {
                    this.is_hide_change_audio = false;
                }
                vm_course_detail.is_hide_video_type = false;
                vm_course_detail.is_show_video_bg = true;
                vm_course_detail.playing_audio = false;
            },
            onBuffer: function(){
                // 视频暂停
                console.log("onBuffer");
                vm_course_detail.is_show_video_bg = false;
            },
            show_more_bief: function() {
                vm_course_detail.is_show_more_bief = !vm_course_detail.is_show_more_bief;
            },
            show_all_count: function() {
                // 查看全集
                vm_course_detail.is_show_all_count = true;
            },
            hide_all_count: function() {
                // 关闭全集
                vm_course_detail.is_show_all_count = false;
            },
            cannel_err_pop: function() {
                vm_course_detail.is_show_err_pop = false;
            }
        }
    });
    $(window).resize(function() {
        
        vm_course_detail.reset_videobox();
        playerInit.resize($(window).width(), $(window).width() * 9 / 16);
    });

});