$(document).on("pageInit", "#page-course-detail", function(e, pageId, $page) {
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
                
                    vm_course_detail.is_audio = false;
                    vm_course_detail.player_position = 0;  // 选集，清空上一个视频的播放时间位置
                    vm_course_detail.course_content = result.data.content;

                    vm_course_detail.runPlayer(result.data)

                    $('.all-vedio-item[rel="' + id + '"]').addClass('active').siblings().removeClass("active");
                    $(event.target).addClass("active").siblings().removeClass("active");

                }).fail(function(err){
                    $.toast(err);
                });

            },
            change_audio: function(id) {
                // 切换音频
                vm_course_detail.is_audio = !vm_course_detail.is_audio;
                if(vm_course_detail.runPlayer(false)){
                    this.is_audio ? $.toast("已切换音频模式") : $.toast("已切换视频模式");
                }
            },
            runPlayer: function(data) {
                // var this =  vm_course_detail == undefined ? this : vm_course_detail;
                if (data != false) {
                    this.id = 0;
                    this.is_vip = 0;
                    this.current_time = 0;
                    this.duration_time = 0;
                    this.vip = false;
                    this.video_url = false;
                    this.sound_url = false;
                    this.img = false;
                    this.no_video = false;
                    this.is_show_err_pop = false;
                    this.no_video = false;
                    this.is_show_err = false;
                    this.is_hide_change_audio = false;
                    this.show_err_text = "";
                    this.no_video = false;
                    this.canot_view = false;
                    for (var i in data) {
                        if (this[i] != undefined) {
                            this[i] = data[i]
                        }
                    }
                }

                var file = this.is_audio ? this.sound_url : this.video_url;
                var current_time = this.current_time;
                this.is_hide_video_type = this.is_vip ? false : true;



                if (this.is_vip > 0 && !this.vip) {
                    this.is_show_err_pop = true;
                    this.canot_view = true;
                    this.is_show_video_bg = false;
                } else if (!file) {
                    this.no_video = true;
                    this.is_hide_change_audio = true;
                    this.is_show_video_bg = false;
                    console.log('no file')
                } else {
                    if (!this.sound_url) {
                        this.is_hide_change_audio = false;
                    }
                    if (playerInit.getState() == null) {
                        playerInit.setup({
                            width: $(window).width(),
                            height: $(window).width() * 9 / 16,
                            image: this.img,
                            stretching: "fill",
                            controls: 1,
                            primary: "html5",
                            autostart: false,
                            file: file,
                            position: this.current_time,
                            events: {
                                onReady: this.onReady,
                                onPlay: this.onPlay,
                                onPause: this.onPause
                            }
                        });
                        playerInit.on('time', function(obj) {
                            if (obj.position - vm_course_detail.current_time > 10) {
                                vm_course_detail.checkCurrentTime(obj.position)
                            }
                            vm_course_detail.duration_time = obj.duration_time;
                            if(obj.position > vm_course_detail.player_position){
                                // 当前播放器播放时间大于当前播放记录，则使用当前播放器播放时间
                                vm_course_detail.player_position = obj.position;
                            }
                            else{
                                // 否则使用当前播放记录
                                vm_course_detail.player_position = vm_course_detail.player_position;
                            }
                            
                        });
                        playerInit.on('play', function(obj) {
                            if(vm_course_detail.player_position>0){
                                // 有当前播放记录
                                if(vm_course_detail.player_position < playerInit.getDuration()){
                                    playerInit.seek(vm_course_detail.player_position);
                                }
                            }
                            else{
                                if(current_time < playerInit.getDuration()){
                                    // 否则使用上一个观看时保存下来的播放记录
                                    playerInit.seek(current_time);
                                }
                            }
                        });
                    } else {
                        playerInit.load([{
                            file: file,
                            image: this.img
                        }]);

                    }
                    this.is_show_video_bg = true;
                    return true;
                }
                return false;
            },
            checkCurrentTime: function(current_time) {
                vm_course_detail.current_time = current_time;
                $.post(APP_ROOT + "/mapi/index.php?ctl=course&act=check&itype=wx", {
                    id: vm_course_detail.id,
                    current_time: vm_course_detail.current_time
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
                vm_course_detail.is_hide_video_type = true;
                vm_course_detail.is_show_video_bg = false;
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
    });

});
$(document).on("pageInit", "#page-course-qi_list, #page-course-yu_list", function(e, pageId, $page) {
	var act;
	init_paramet();

	var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });


    // 无限滚动
 	$($page).on('infinite', function(e) {
    	infinite_scroll($page,APP_ROOT+"/weixin/index.php?ctl=course&act="+act+new_paramet,".course-list",vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
    	pull_refresh($page,ajax_url,".course-list",vm_paging);
    });


    // 初始化参数
 	function init_paramet(){
 		if(pageId == 'page-course-yu_list'){
			act = 'yu_list';
		}
		switch(pageId){
			case 'page-course-yu_list':
				act = 'yu_list';
				break;
			case 'page-course-qi_list':
				act = 'qi_list';
				break;
		}

        new_paramet = paramet.list_type ? '&list_type='+paramet.list_type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=course&act="+act+new_paramet;
        console.log(ajax_url);
    }

});
$(document).on("pageInit","#page-course-index, #page-course-qi_list", function(e, pageId, $page) {
    // 搜索
    $("#form-search").submit(function(){
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=search&search="+$(this).find("input[name='search']").val();
    });
    $(".searchbar-cancel").on('click', function(){
        $("input[name='search']").val('');
    });
});

$(document).on("pageInit","#page-course-yu_list, #page-course-qi_list", function(e, pageId, $page) {
    // 搜索
    $("#form-search").submit(function(){
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=search&search="+$(this).find("input[name='search']").val();
    });
    $(".searchbar-cancel").on('click', function(){
        $("input[name='search']").val('');
    });
});


$(document).on("pageInit","#page-course-search", function(e, pageId, $page) {
	if(!empty($("input[name='search']").val())){
		$(".searchbar").addClass("searchbar-active");
	}

	var searching = false;
 	$("#form-search").submit(function(){
 		var self = $(this), ele_input = self.find("input[name='search']"), search =ele_input.val();
        // $.router.load(APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val(), true);
        // location.href = APP_ROOT+"/wap/index.php?ctl=task&act=search&key="+$(this).val();

        if(!searching){
        	if(empty(search)) return;
        	searching = true;

        	ele_input.blur();

	        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=course&act=search",{search: search},"html").done(function(result){
	        	searching = false;

        	  	var tplElement = $('<div id="tmpHTML"></div>').html(result),
		        htmlObject = tplElement.find(".course-item-list"),
		        html = $(htmlObject).html();
	        	$($page).find(".course-item-list").addClass('animated fadeIn').html(html);

			    setTimeout(function(){
			        $($page).find(".course-item-list").removeClass('fadeIn');
			    }, 1000);

                $('.lazyload').picLazyLoad();

		    }).fail(function(err){
		        $.toast(err);
		    });
        } 
    });

 	$("input[name='search']").blur(function(){
    	if(empty($(this).val())) return;
 		$(".searchbar").addClass("searchbar-active");
  	});

  	$(".searchbar-cancel").on('click', function(){
  		$("input[name='search']").val('');
  	});
});
$(document).on("pageInit", "#page-course-vip", function(e, pageId, $page) {
	var vm_course_vip = new Vue({
  		el: '#vscope-course_vip',
	  	data: data,
	  	methods: {
/*	  		choice_price: function(cost, name, id, event){
	  			// 选择会员付费
	  			$(event.target).addClass("active").siblings().removeClass("active");
	  			vm_course_vip.vip_id = id;
	  			vm_course_vip.cost = cost;
	  			vm_course_vip.name = name;
	  		},*/
	  		list: function(data) {
	            console.log(data);
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
	  			wx.chooseWXPay(vm_course_vip.jsApiParameters);
	  		},
	  		jsApiCall: function(){
  			 	jsApiParameters = JSON.parse(vm_course_vip.jsApiParameters);
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
		                   setTimeout(function(){;
		                		location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=detail&pid=";
		                   },1000);

		                }
		                else{

		                }
		            }
		        );
	  		}
	  	}
  	});

	$(".J-vip-price").on('click', function(){
		// 选择会员付费
		var self = $(this); 
		
		$(this).addClass("active").siblings().removeClass("active");
		vm_course_vip.vip_id = self.attr("data-id");
		vm_course_vip.cost = self.attr("data-cost");
		vm_course_vip.name = self.attr("data-name");
	});

});
$(document).on("pageInit", "#page-course-vip_exchange", function(e, pageId, $page) {
	var vm_vip_exchange = new Vue({
  		el: '#vscope-vip_exchange',
	  	data: {
	  		code: ''
	  	},
	  	methods: {
	  		submit: function(){
	  			// 兑换码兑换
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=course&act=vip_exchange",{code: this.code}).done(function(result){

                    $.toast(result,1000);
                    setTimeout(function(){
                    	history.back();
                    },1000);


                }).fail(function(err){
                    $.toast(err);
                });
	  		}
	  	}
  	});
});
$(document).on("pageInit", "#page-date-index, #page-date-detail", function(e, pageId, $page) {
	var has_more_character_bief;
	$(".character-bief span").height() >= 87 ? has_more_character_bief = true : has_more_character_bief = false;


	var vm_meet = new Vue({
  		el: '#vscope-meet',
	  	data: {
	  		name: '',
	  		mobile: '',
	  		verify: '',
	  		date_id: '',
	  		date_text: '',
	  		is_disabled: false,
	        code_lefttime: 0,
	        code_timeer: null,
	        is_open_date: false,
	        is_more_bief: false,
	        has_wanna: has_wanna,
	        wanna_count: wanna_count,
	        has_more_character_bief: has_more_character_bief,
	  	},
	  	methods: {
	  		select_date: function(event){
	  			vm_meet.date_text = $(event.target).find('option').not(function(){ return !this.selected }).text();
	  		},
	  		show_more_bief: function(){
	  			vm_meet.is_more_bief = !vm_meet.is_more_bief;
	  		},
	  		open_date: function(id){
	  			vm_meet.date_id = id;
	  			vm_meet.is_open_date = true;
	  		},
	  		cancel_date: function(){
	  			vm_meet.is_open_date = false;
	  		},
	  		check: function(){
            // 表单验证
                var self = this;

                vm_meet.name = $.emoji2Str(vm_meet.name);

                if(pageId == 'page-date-detail'){
            	 	if(empty(self.name)){
	                    $.toast('姓名不能为空');
	                    return false;
	                }
	                else if(empty(self.mobile)){
	                    $.toast('手机号不能为空');
	                    return false;
	                }
	                else if(!$.checkMobilePhone(self.mobile)){
	                    $.toast('请输入有效的手机号');
	                    return false;
	                }
	                else if(empty(self.verify)){
	                    $.toast('请填写验证码');
	                    return false;
	                }
	                else{
	                    return true;
	                }
                }
                else{
                	if(empty(self.name)){
	                    $.toast('姓名不能为空');
	                    return false;
	                }
	                else if(empty(self.mobile)){
	                    $.toast('手机号不能为空');
	                    return false;
	                }
	                else if(!$.checkMobilePhone(self.mobile)){
	                    $.toast('请输入有效的手机号');
	                    return false;
	                }
	                else if(empty(self.verify)){
	                    $.toast('请填写验证码');
	                    return false;
	                }
	                else if(empty(self.date_id)){
	                    $.toast('请选择分类');
	                    return false;
	                }
	                else{
	                    return true;
	                }
                }              
            },
            submit: function(){
            // 提交预约
        	 	self = this;
                if(!self.check()){
                    return false;
                }

                var data = {
                    name: self.name,
                    mobile: self.mobile,
                    verify: self.verify,
                    date_id: self.date_id
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=date&act=date",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        vm_meet.is_open_date = false;
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            },
	  		send_code: function(event){
	        // 发送验证码
	            var self = this;
	            if(self.is_disabled){
	                return false; 
	            }
	            else{
	                var thiscountdown=$("#j-send-code"); 
	                var query = new Object();
	                query.mobile = self.mobile;
	                $.ajax({
	                    url:APP_ROOT+"/weixin/index.php?ctl=date&act=send_mobile_verify&itype=wx&post_type=json",
	                    data:query,
	                    type:"POST",
	                    dataType:"json",
	                    success:function(result){
	                        if(result.status == 1){    
	                            countdown = 60;
	                            // 验证码倒计时
	                            vm_meet.code_lefttime = 60;
	                            self.code_lefttime_fuc("#j-send-code", self.code_lefttime);
	                            // $.showSuccess(result.info);
	                            return false;
	                        }
	                        else{
	                            $.toast(result.error);
	                            return false;
	                        }
	                  }
	                });
	            }
	        },
	        code_lefttime_fuc: function(verify_name,code_lefttime){
	        // 验证码倒计时
	            var self = this;
	            clearTimeout(self.code_timeer);
	            $(verify_name).css("color", "#999");
	            $(verify_name).html("重新发送 "+code_lefttime);
	            code_lefttime--;
	            if(code_lefttime >0){
	                $(verify_name).attr("disabled","disabled");
	                self.is_disabled=true;
	                vm_meet.code_timeer = setTimeout(function(){self.code_lefttime_fuc(verify_name,code_lefttime);},1000);
	            }
	            else{
	                code_lefttime = 60;
	                self.is_disabled=false;
	                $(verify_name).removeAttr("disabled");
	                $(verify_name).html("发送验证码");
	            }
	        },
	        wanna: function(event){
	        // 想预约(取消想预约)
	        	self = this;
        	 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=date&act=wanna",{date_id: self.date_id}, '', 1).done(function(result){
        	 		if(result.has_wanna){
        	 			vm_meet.has_wanna = 1;
        	 			vm_meet.wanna_count = vm_meet.wanna_count+1;
        	 		}
        	 		else{
        	 			vm_meet.has_wanna = 0
        	 			vm_meet.wanna_count = vm_meet.wanna_count-1;
        	 		}
                }).fail(function(err){
                    $.toast(err);
                });
	        }
	  	}
	});

});
$(document).on("pageInit", "#page-course-index", function(e, pageId, $page) {
    $(".go-top").on("click", function() {
        $(".content").scrollTop(0);
    });

    $(".content").scroll(function() {
        var scroll = $(".content").scrollTop();
        if (scroll == 0) {
            $(".go-top").css("display", "none");
        } else {
            $(".go-top").css("display", "block");
        }
    });
    var mySwiper = new Swiper('.swiper-container', {
        autoplay: 5000,
        pagination : '.swiper-pagination'
    });
});

function infinite_scroll($page,ajax_url,cls,vm_paging,func) {
 	if (loading || vm_paging.page>total_page){
 		$(".content-inner").css({paddingBottom:"0"});
		return;
 	}
  	loading = true;

  	handleAjax.handle(ajax_url,{tmpl_pre_dir:'inc', page:vm_paging.page},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(html).find(".total_page").remove();
        vm_paging.page++;
		loading = false;
		$($page).find(cls).append(html);
		$.refreshScroller();
 		if(func!=null){
        	func();
        }
        $('.lazyload').picLazyLoad();
    }).fail(function(err){
        $.toast(err);
    });
}
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
		                   setTimeout(function(){;
		                		location.href = APP_ROOT+"/weixin/index.php?ctl=course&act=detail&pid="+pid;
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
function pull_refresh($page,ajax_url,cls,vm_paging,callback){
    var loading = false;
    console.log(ajax_url);
    if (loading) return;
    loading =true;
    

    handleAjax.handle(ajax_url,'',"html").done(function(result){
        refreshing = false;
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        list_ele = $($page).find(cls),
        html = $(htmlObject).html();
        value = html.replace(/\s+/g,"");

        var result = $(result).find(".content").html(), total_page = htmlObject.find("input[name='total_page']").val();
        loading =false;
        vm_paging.page = 2;
        vm_paging.total_page = total_page;
        setTimeout(function() {

            list_ele.addClass('animated fadeInUp').html(value.length > 0 ? html : '<div style="text-align:center;color:#999;font-size:0.75rem;">暂无数据</div>');

            setTimeout(function(){
                list_ele.removeClass('fadeInUp');
            }, 1000);

            // 加载完毕需要重置
            $.pullToRefreshDone('.pull-to-refresh-content');
            $(".pull-to-refcontainerresh-layer").css({"visibility":"visible"});

            // 初始化分页数据
            page = 2;

            // 初始化懒加载图片
            $('.lazyload').picLazyLoad();

            if(typeof(callback) == 'function'){
                callback.call(this);
            }

        }, 300);

    }).fail(function(err){
        $.toast(err);
    });
}
$(document).on("pageInit", "#page-question-detail", function(e, pageId, $page) {
	var vm_question_detail = new Vue({
  		el: '#vscope-question_detail',
  		data:{
  			id: '',
  			answer: '',
  			praise_count: praise_count
  		},
	    methods: {
	    	del_question: function(id){
	    		$.confirm('确认删除您的该提问？', function(){
	    			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=question&act=delete",{id: id}).done(function(result){
	                    $.toast(result,1000);
	                    setTimeout(function(){
	                        location.href = APP_ROOT+"/weixin/index.php?ctl=question&act=index&type=mine";
	                    },1000);

	                }).fail(function(err){
	                    $.toast(err);
	                });
	    		});
	    	},
		    pop_comment: function(id){
	    	// 弹窗评论框
	    		vm_question_detail.id = id;
				document.getElementById("comment-info").focus();
			 	$(".float-comment, .float-comment-mask").addClass('show');
		 		$(".float-comment-mask").click(function(){
			 		$(".float-comment, .float-comment-mask").removeClass('show');
			 	});
		    },
	    	send_comment: function(){
	    	// 发送回复
	    		self = this;

	    		vm_question_detail.answer = $.emoji2Str(vm_question_detail.answer);

	    		if($.checkEmpty(this.answer)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}
		    	var data = {
		    		id: self.id,
		    		answer: self.answer
		    	};

		        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=question&act=answer",data).done(function(result){

		 			$.toast(result,1000);
				    setTimeout(function(){
				        vm_question_detail.answer = '';
			 			$(".float-comment, .float-comment-mask").removeClass('show');
				 		$(".invest-bar").removeClass('hide');

				 		location.reload();
				    }, 1000);

			    }).fail(function(err){
			        $.toast(err);
			    });
	    	},
	    	praise: function(id, event){
	    	// 点赞
    		 	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=question&act=praise",{id: id}, '', 1).done(function(result){

    		 		if(result.is_praise){
    		 			$(event.target).addClass("active");
    		 			vm_question_detail.praise_count = vm_question_detail.praise_count+1;
    		 		}
    		 		else{
    		 			$(event.target).removeClass("active");

    		 			if(vm_question_detail.praise_count>0){
    		 				vm_question_detail.praise_count = vm_question_detail.praise_count-1;
    		 			}
    		 			else{
    		 				vm_question_detail.praise_count = 0
    		 			}

    		 		}

			    }).fail(function(err){
			        $.toast(err);
			    });
	    	}
	    }
	});   
});
$(document).on("pageInit", "#page-question-form_question", function(e, pageId, $page) {
	var vm_question = new Vue({
  		el: '#vscope-question',
	  	data: {
	  		type: '',
	  		title: '',
	  		question: '',
	  		is_open: true,
	  		pid: ''
	  	},
	  	methods: {
	  		// 是否私密
	  		select_open: function(){
	  			vm_question.is_open = !vm_question.is_open;
	  		},
	  		check: function(){
            // 表单验证
                var self = this;

                vm_question.title = $.emoji2Str(vm_question.title);
                vm_question.question = $.emoji2Str(vm_question.question);

                if(empty(vm_question.title)){
                    $.toast('问题标题不能为空');
                    return false;
                }
				else if(empty(self.question)){
                    $.toast('问题描述不能为空');
                    return false;
                }
                else{
                    return true;
                }
            },
	  		submit: function(){
            // 提交预约
        	 	self = this;
                if(!self.check()){
                    return false;
                }

                var data = {
                    title: self.title,
                    question: self.question,
                    is_open: self.is_open
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=question&act=question",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        location.href = APP_ROOT+"/weixin/index.php?ctl=question&act=index&type=mine";
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            },
	  		cancel: function(){
	  			history.back();
	  		}
	  	}
	});


});
$(document).on("pageInit", "#page-question-index, #page-course-yu_list", function(e, pageId, $page) {
    
    init_paramet();

    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });
    
    // 无限滚动
 	$($page).on('infinite', function(e) {
    	infinite_scroll($page,ajax_url,".question-list",vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
    	pull_refresh($page,ajax_url,".question-list",vm_paging);
    });


    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.type ? '&type='+paramet.type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=question&act=index"+new_paramet;
        console.log(ajax_url);
    }

    (function(){
        // 针对微信浏览器返回上一页默认读取缓存解决方案
        handleAjax.handle(ajax_url,'','',1).done(function(result){

            var view_count, ele = $(".question-list").find(".view-counts"), question_list = result.question_list;
            $(ele).each(function(i){
                $(this).html(question_list[i].count);
            });

        }).fail(function(err){
            $.toast(err);
        });
    })();

});
$(document).on("pageInit", "#page-question-question", function(e, pageId, $page) {
	var vm_question = new Vue({
  		el: '#vscope-question',
	  	data: {
	  		type: '',
	  		title: '',
	  		question: '',
	  		is_open: true,
	  		pid: ''
	  	},
	  	methods: {
	  		// 是否私密
	  		select_open: function(){
	  			vm_question.is_open = !vm_question.is_open;
	  		},
	  		check: function(){
            // 表单验证
                var self = this;
				if(empty(self.question)){
                    $.toast('问题描述不能为空');
                    return false;
                }
                else{
                    return true;
                }
            },
	  		submit: function(){
            // 提交预约
        	 	self = this;
                if(!self.check()){
                    return false;
                }

                var data = {
                    type: self.type,
                    title: self.title,
                    question: self.question,
                    is_open: self.is_open,
                    pid: self.pid
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=date&act=date",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        // location.reload();
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            },
	  		cancel: function(){
	  			// vm_meet.is_open_date = false;
	  		}
	  	}
	});


});
$(document).on("pageInit", "#page-share-add", function(e, pageId, $page) {
    var data = ["2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件"];
    $(document).on('click', '.create-actions', function() {
        var buttons1 = [{
            text: data[0],
            onClick: function() {
                $("input[name='type']").val(data[0]);
            }
        }, {
            text: data[1],
            onClick: function() {
                $("input[name='type']").val(data[1]);
            }
        }, {
            text: data[2],
            bold: true,
            color: 'danger',
            onClick: function() {
                $("input[name='type']").val(data[2]);
            }
        }, {
            text: data[3],
            onClick: function() {
                $("input[name='type']").val(data[3]);
            }
        }, {
            text: data[4],
            onClick: function() {
                $("input[name='type']").val(data[4]);
            }
        }, {
            text: data[4],
            onClick: function() {
                $("input[name='type']").val(data[4]);
            }
        }];

        var groups = [buttons1];
        $.actions(groups);
    });
    $(document).on('click', '.modal-overlay-visible', function() {
        $.closeModal();
    });

    var vm_add_share = new Vue({
        el: '#vscope-add_share',
        data: {
            title: '',
            cate_id: '',
            content: '',
            imgs: ''
        },
        methods: {
            check: function(){
            // 表单验证
                var self = this;
                if(empty(self.title)){
                    $.toast('分享标题不能为空');
                    return false;
                }
                else if(empty(self.cate_id)){
                    $.toast('请选择分类');
                    return false;
                }
                else if(empty(self.content)){
                    $.toast('分享内容不能为空');
                    return false;
                }
                else{
                    return true;
                }
            },
            submit: function(){
            // 发起分享

                self = this;
                if(!self.check()){
                    return false;
                }

                var data = {
                    title: self.title,
                    cate_id: self.cate_id,
                    content: self.content,
                    imgs: self.imgs
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=add",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        // location.reload();
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            }
        }
    });

});

$(document).on("pageInit", "#page-share-add_share", function(e, pageId, $page) {

    var buttons = [];

    $(document).on('click', '.modal-overlay-visible', function() {
        $.closeModal();
    });

    var vm_add_share = new Vue({
        el: '#vscope-add_share',
        data: {
            title: '',
            cate_id: '',
            cate_name: '',
            content: ''
        },
        computed: {
            new_buttons: function () {

                for(var i=0; i<cate_list.length; i++){
                    console.log(cate_list[i]);
                    buttons[i] = {
                        text: cate_list[i].cate_name,
                        id: cate_list[i].id,
                        onClick: function() {
                            vm_add_share.cate_id = this.id;
                            vm_add_share.cate_name = this.text;

                        }
                    };
                }
                return buttons;
            }
        },
        methods: {
            create_actions: function(){
                console.log(vm_add_share.new_buttons);
                this.$nextTick(function () {
                    var groups = [vm_add_share.new_buttons];
                    $.actions(groups);
                });
            },
            check: function(){
            // 表单验证
                var self = this;

                vm_add_share.title = $.emoji2Str(vm_add_share.title);
                vm_add_share.content = $.emoji2Str(vm_add_share.content);
                
                if(empty(self.title)){
                    $.toast('分享标题不能为空');
                    return false;
                }
                else if(empty(self.cate_id)){
                    $.toast('请选择分类');
                    return false;
                }
                else if(empty(self.content)){
                    $.toast('分享内容不能为空');
                    return false;
                }
                else{
                    return true;
                }
            },
            submit: function(){
            // 发起分享

                self = this;
                if(!self.check()){
                    return false;
                }

                var json_imgs = JSON.stringify(imgs);

                var data = {
                    title: self.title,
                    cate_id: self.cate_id,
                    content: self.content,
                    imgs: json_imgs
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=add",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        location.href= APP_ROOT+"/weixin/index.php?ctl=user_center&act=share";
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            }
        }
    });


    get_file_more_fun('upload-imgs', 5);

});

$(document).on("pageInit", "#page-share-detail", function(e, pageId, $page) {
	var vm_share_detail = new Vue({
  		el: '#vscope-share_detail',
	  	data: data,
	  	methods: {
	  		pop_comment: function(type, id, reply_user_id){
	  		// 弹出评论
	  			vm_share_detail.type = type;
	  			vm_share_detail.id = id;
	  			vm_share_detail.reply_user_id = reply_user_id;

    	 		$(".footer-bar").hide();
				document.getElementById("comment-info").focus();
			 	$(".float-comment, .float-comment-mask").addClass('show');
		 		$(".float-comment-mask").click(function(){
			 		$(".float-comment, .float-comment-mask").removeClass('show');
			 		$(".footer-bar").show();
			 	});
	  		},
	  		send_comment: function(){
	  		// 发送评论
  				var self = this;

  				// vm_share_detail.content = $.emoji2Str(vm_share_detail.content);

  				var data_json = {
  					id: self.id,
  					content: $("textarea[name='content']").val()
  				};

		    	if(self.type == 1){
		    		data_json.reply_user_id = self.reply_user_id;
		    	}
		    	if(empty(data_json.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}

		        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=reply",data_json).done(function(result){
		        	$.toast(result,1000);
		        	setTimeout(function(){
		        		location.reload();
		        		vm_share_detail.content = '';
		        	},1000);

			    }).fail(function(err){
			        $.toast(err);
			    });
	  		},
	  		praise: function(id){
	  		// 赞
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=praise",{id: id}).done(function(result){
		        	$.toast(result,1000);
		        	setTimeout(function(){
		        		// $("#item-praise-"+id).html(Number($("#item-praise-"+id).html())+1);
		        		location.reload();
		        	},1000);

			    }).fail(function(err){
			        $.toast(err);
			    });

	  		},
	  		delete_reply: function(id){
	  		// 删除回复
	  			$.confirm('确定要删除此评论么？', function(){
	  				handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete_reply",{id: id}).done(function(result){
			        	$.toast(result,1000);
			        	setTimeout(function(){
			        		location.reload();
			        	},1000);

				    }).fail(function(err){
				        $.toast(err);
				    });
	  			});
	  		},
	  		delete_share: function(id){
	  		// 删除分享
	  			$.confirm('删除后，您的分享所有信息都会被删除！', '删除分享', function(){
	  				handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete",{id: id}).done(function(result){
			        	$.toast(result,1000);
			        	setTimeout(function(){
			        		location.href= APP_ROOT+"/weixin/index.php?ctl=user_center&act=index";
			        	},1000);

				    }).fail(function(err){
				        $.toast(err);
				    });
	  			});
	  		}
	  	}
	});
	
	// 输入过滤emoji表情
    $("textarea[name='content']").on('input propertychange', function(){
    	var val = $(this).val();
    	$(this).val($.emoji2Str(val));
    });
});
$(document).on("pageInit", "#page-share-index", function(e, pageId, $page) {
    var swiper = new Swiper('.swiper-container', {
        scrollbar: '.swiper-scrollbar',
        freeMode: true,
        scrollbarHide: true,
        slidesPerView: 'auto',
        spaceBetween: 0,
        grabCursor: true
    });

	init_paramet();

	var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

	var vm_obj_one = {
  		el: "#vscope-share_index_1",
	  	data: data,
	  	methods: {
	  		pop_comment: function(type, id, reply_user_id){
	  		// 弹出评论
	  			vm_comment_one.type = type;
	  			vm_comment_one.id = id;
	  			vm_comment_one.reply_user_id = reply_user_id;

    	 		$(".footer-bar").hide();
				document.getElementById("comment-info").focus();
			 	$(".float-comment, .float-comment-mask").addClass('show');
		 		$(".float-comment-mask").click(function(){
			 		$(".float-comment, .float-comment-mask").removeClass('show');
			 		$(".footer-bar").show();
			 	});
	  		},
	  		praise: function(id, event){
	  		// 赞
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=praise",{id: id}, '', 1).done(function(result){
		        	var self = $(event.target), praise_count = parseInt($("#item-praise-"+id).attr("data-praise_count")), item_user_nick_name;
		        	if(praise_count){
		        		item_user_nick_name = user_nick_name+"、";
		        	}
		        	praise_count ? item_user_nick_name = user_nick_name+"、" : item_user_nick_name = user_nick_name;
		 			if(result.status == 1){
		 				if(result.is_praise){
			        		praise_count=praise_count+1;
		        			$(self).addClass("active");
			        		$("#praise-name-"+id).prepend(item_user_nick_name);
			        		$("#item-praise-"+id).attr("data-praise_count", praise_count).html(praise_count);
			        		$("#praise-count-"+id).html(praise_count);
			        	}
			        	else{
			        		var item_praise = ($("#praise-name-"+id).html()).toString();
			        		praise_count=praise_count-1;
			        		$(self).removeClass("active");
			        		$("#praise-name-"+id).html(item_praise.replace(item_user_nick_name, ""));
			        		$("#item-praise-"+id).attr("data-praise_count", praise_count).html(praise_count);
			        		$("#praise-count-"+id).html(praise_count);
			        	}
		 			}
		 			else{
		 				$.toast(result.error);
		 			}
		 			

			    }).fail(function(err){
			        $.toast(err);
			    });

	  		},
	  		delete_reply: function(id){
	  		// 删除回复
	  			$.confirm('确定要删除此评论么？', function(){
		  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete_reply",{id: id}).done(function(result){
			        	$.toast(result,1000);
			        	setTimeout(function(){
			        		location.reload();
			        	},1000);

				    }).fail(function(err){
				        $.toast(err);
				    });
				});
	  		}
	  	}
	}, vm_obj_two = {
		el: "#vscope-share_index_2",
		data:{
			content: ''
		},
	  	methods: {
	  		send_comment: function(){
	  		// 发送评论
	  			var self = this;

	  			// vm_obj_two.content = $.emoji2Str(vm_obj_two.content);
	  			
  				var data_json = {
  					id: vm_comment_one.id,
  					content: $("textarea[name='content']").val()
  				};

		    	if(vm_comment_one.type == 1){
		    		data_json.reply_user_id = vm_comment_one.reply_user_id;
		    	}
		    	if(empty(data_json.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}

		        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=reply",data_json).done(function(result){
		        	$.toast(result,1000);
		        	setTimeout(function(){
		        		location.reload();
		        	},1000);

			    }).fail(function(err){
			        $.toast(err);
			    });
	  		}
	  	}
	};

    // 无限滚动
 	$($page).on('infinite', function(e) {
    	infinite_scroll($page,ajax_url,".share-list",vm_paging, function(){
    		var vm_comment_one = new Vue(vm_obj_one);
			var vm_comment_two = new Vue(vm_obj_two);
    	});
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
    	pull_refresh($page,ajax_url,".share-list",vm_paging, function(){
    		var vm_comment_one = new Vue(vm_obj_one);
			var vm_comment_two = new Vue(vm_obj_two);
    	});
    });

	var vm_comment_one = new Vue(vm_obj_one);
	var vm_comment_two = new Vue(vm_obj_two);

    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.cate_id ? '&cate_id='+paramet.cate_id : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=share&act=index"+new_paramet;
        console.log(ajax_url);
    }

    // 输入过滤emoji表情
    $("textarea[name='content']").on('input propertychange', function(){
    	var val = $(this).val();
    	$(this).val($.emoji2Str(val));
    });

});
$(document).on("pageInit","#page-user_center-add_feedback", function(e, pageId, $page) {
	var vm_add_feedback = new Vue({
  		el: '#vscope-add_feedback',
	  	data: {
	  		content: "",
	  	},
  	 	methods: {
		    submit: function (event) {

		    	vm_add_feedback.content = $.emoji2Str(vm_add_feedback.content);
		    	
		    	if(empty(this.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}
		    	handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=user_center&act=feedback",{"content": this.content}).done(function(msg){
			       	$.toast(msg,1000);
                	setTimeout(function(){
                		location = APP_ROOT+"/weixin/index.php?ctl=user_center&act=index";
                	},1000);
			    }).fail(function(err){
			        $.toast(err);
			    });
		    }
	  	}
	});
});
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
$(document).on("pageInit", "#page-user_center-balance", function(e, pageId, $page) {

    init_paramet();

    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });
    
    // 无限滚动
 	$($page).on('infinite', function(e) {
    	infinite_scroll($page,ajax_url,".trade-list",vm_paging);
    });

    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.type ? '&type='+paramet.type : '',

        ajax_url = APP_ROOT+"/weixin/index.php?ctl=user_center&act=balance"+new_paramet;

    }

});
$(document).on('pageInit', '#page-user_center-index', function(){
	
	// 判断是否登录
	$("#vscope-checkLogin").on('click', '.J-check_login', function(){
		handleAjax.handle(check_login_url).done(function(result){
        	console.log(result);

	    }).fail(function(err){
	        $.toast(err);
	    });
	});

});
$(document).on("pageInit","#page-user_center-invite", function(e, pageId, $page) {
	
	// 分享方式
	$(".flex-box").on('click', '.J-fx', function(){
		var self = $(this), share_type = self.attr("data-type");
		self.addClass("active").siblings().removeClass("active");
		data.share_type = share_type;
	});

	$(".J-share").on('click', function(e){
/*	 	if(data.share_type == 'wx'){
            shareAppMessage();
        }else{
            shareTimeline();
        }*/
        window.event? window.event.cancelBubble = true : e.stopPropagation();
        $(".share-tip").addClass('show');
	});

	$("document, body").click(function(e){
		window.event? window.event.cancelBubble = true : e.stopPropagation();
		$(".share-tip").removeClass('show');
	});

    //分享到朋友圈
    function shareTimeline(){
        WeixinJSBridge.invoke('shareTimeline',{
            "img_url":wx_img,
            "link":wx_link,
            "desc": wx_desc,
            "title":wx_title
        });
    }

    //分享给好友
    function shareAppMessage(){
        WeixinJSBridge.invoke('sendAppMessage',{
            "img_url":wx_img,
            "link":wx_link,
            "desc":wx_desc,
            "title":wx_title
        });
    }

	// 微信分享
	wx.ready(function () {

		// 分享到朋友圈
	    wx.onMenuShareTimeline({
	        title: wx_desc, // 分享标题
	        link: wx_link, // 分享链接
	        imgUrl: wx_img, // 分享图标
	        success: function () {
	            // 用户确认分享后执行的回调函数
	        },
	        cancel: function () {
	            // 用户取消分享后执行的回调函数
	        }
	    });

	    // 分享给朋友
	    wx.onMenuShareAppMessage({
	        title: wx_title, // 分享标题
	        desc: wx_desc, // 分享描述
	        link: wx_link,  // 分享链接
	        imgUrl: wx_img, // 分享图标
	        success: function () {
	            // 用户确认分享后执行的回调函数
	        },
	        cancel: function () {
	            // 用户取消分享后执行的回调函数
	        }
	    });

	    // 通过error接口处理失败验证
	    wx.error(function(res){
	        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
	    });
	});



});
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