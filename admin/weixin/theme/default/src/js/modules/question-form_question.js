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