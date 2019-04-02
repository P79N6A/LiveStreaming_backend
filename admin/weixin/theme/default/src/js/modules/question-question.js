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