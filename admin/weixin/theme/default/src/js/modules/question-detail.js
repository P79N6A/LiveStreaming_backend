$(document).on("pageInit", "#page-question-detail", function(e, pageId, $page) {
	var vm_question_detail = new Vue({
  		el: '#vscope-question_detail',
  		data:{
  			id: '',
  			answer: '',
  			praise_count: praise_count,
  			show_float_comment: false
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
	    	close_comment: function(){
	  		// 关闭评论
	  			this.show_float_comment = false;
	  			this.$nextTick(function(){
	  				$("textarea[name='content']").val('');
	  			});
	  		},
		    pop_comment: function(id){
	    	// 弹窗评论框
	    		this.id = id;
	    		this.show_float_comment = true;
	    		this.$nextTick(function(){
	    			$("textarea[name='content']").val('');
	    			document.getElementById("comment-info").focus();
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