$(document).on("pageInit", "#page-share-detail", function(e, pageId, $page) {
	var vm_main = new Vue({
  		el: '#vscope-share_detail',
	  	data: data,
	  	beforeCreate: function(){
	  		$.showIndicator();
	  		var self = this;
	  		handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=share&act=detail", {id: GetQueryString("id")}, '', 1).done(function(result){

	  			self.share = result.data;

	  			self.$nextTick(function(){
	  				$.hideIndicator();
	  			});

		    }).fail(function(err){
		        $.toast(err);
		    });
	  	},
	  	methods: {
	  		close_comment: function(){
	  		// 关闭评论
	  			this.show_float_comment = false;
	  			$("textarea[name='content']").val('');
	  		},
	  		pop_comment: function(type, item){
	  		// 弹出评论
				this.type = type;
	  			this.item = item;
	  			this.id = item.id;
	  			this.item_reply = item.reply_list;
	  			this.show_float_comment = true;
	  			

    	 		this.$nextTick(function(){
    	 			document.getElementById("comment-info").focus();
					$("textarea[name='content']").val('');
    	 		});
				
	  		},
	  		send_comment: function(){
	  		// 发送评论
	  			var self = this;
	  			var data_json = {
  					id: self.id,
  					content: $("textarea[name='content']").val()
  				};

		    	if(self.type == 1){
		    		data_json.reply_user_id = self.item_reply.user_id;
		    	}
		    	if(empty(data_json.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}

		        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=reply",data_json, '', 1).done(function(result){
		        	self.item_reply.push({id:result.id, content:data_json.content, nick_name:self.user_name, reply_user_id:data_json.reply_user_id, reply_user_name: "", user_id:self.user_id});
		        	self.item.reply_count = self.item.reply_count+1;
		        	if(result.status == 1){
		        		$.toast('发送评论成功',1000);
		        	}
		        	else{
		        		$.toast('发送失败',1000);
		        	}
		        	self.show_float_comment = false;

			    }).fail(function(err){
			        $.toast(err);
			    });
	  		},
	  		praise: function(item){
	  		// 点赞
	  			var self = this;
	  			praise_list = item.praise_list;
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=praise",{id: item.id}, '', 1).done(function(result){
	  				self.$nextTick(function(){
			        	var _this = $(event.target);
			 			if(result.status == 1){
			 				var u_praise = {user_id:self.user_id, nick_name:self.user_name};
			 				if(result.is_praise){
			 					item.praise_count = parseInt(item.praise_count)+1;
			 					praise_list.unshift(u_praise);
			        			$(_this).addClass("active");
				        	}
				        	else{
				        		item.praise_count = parseInt(item.praise_count)-1;
				        		praise_list.del(function(obj){
								  return obj.user_id == u_praise.user_id;
								});
				        		$(_this).removeClass("active");
				        	}
			 			}
			 			else{
			 				$.toast(result.error);
			 			}
		 			});

			    }).fail(function(err){
			        $.toast(err);
			    });

	  		},
	  		delete_reply: function(item, reply_list){
	  		// 删除回复
	  			$.confirm('确定要删除此评论么？', function(){
		  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete_reply",{id: reply_list.id}).done(function(result){
			        	$.toast(result,1000);

			        	item.reply_list.del(function(obj){
						  return obj.id == reply_list.id;
						});
						item.reply_count = item.reply_count-1;

				    }).fail(function(err){
				        $.toast(err);
				    });
				});
	  		},
	  		delete_share: function(share){
	  		// 删除分享
	  			$.confirm('删除后，您的分享所有信息都会被删除！', '删除分享', function(){
	  				handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete",{id: share.id}).done(function(result){
			        	share = null;
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