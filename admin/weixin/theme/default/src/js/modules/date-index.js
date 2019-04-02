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