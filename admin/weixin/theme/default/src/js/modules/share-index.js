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

	// 评论列表
	var vm_main = new Vue({
  		el: "#vm-vscope",
	  	data: data,
	  	beforeCreate: function(){
	  		$.showIndicator();
	  		var self = this;
	  		handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=share&act=index", '', '', 1).done(function(result){

	  			self.share_list = result.list;
	  			vm_comment.is_star_share = result.is_star_share;

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
	  			vm_main.show_float_comment = false;
	  			vm_comment.show_float_comment = false;
	  			$(".footer-bar").show();
	  			$("textarea[name='content']").val('');
	  		},
	  		pop_comment: function(type, list){
	  		// 弹出评论
	  			this.type = type;
	  			this.list = list;
	  			this.id = list.id;
	  			this.item_reply = list.reply_list;
	  			this.show_float_comment = true;
	  			vm_comment.show_float_comment = true;

	  			this.$nextTick(function(){
		  			$(".footer-bar").hide();
					document.getElementById("comment-info").focus();
					$("textarea[name='content']").val('');
	  			});
	  		},
	  		praise: function(id, item, praise_list, event){
	  		// 点赞
	  			var self = this;
	  			praise_list = item.praise_list;
	  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=praise",{id: id}, '', 1).done(function(result){
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
	  		delete_reply: function(item, list){
	  		// 删除回复
	  			$.confirm('确定要删除此评论么？', function(){
		  			handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=delete_reply",{id: item.id}).done(function(result){
			        	$.toast(result,1000);

			        	list.reply_list.del(function(obj){
						  return obj.id == item.id;
						});
						list.reply_count = list.reply_count-1;

				    }).fail(function(err){
				        $.toast(err);
				    });
				});
	  		}
	  	}
	});

	// 评论文本区
	var vm_comment = new Vue({
  		el: "#vscope-float_comment",
	  	data: {
	  		is_star_share: 0,
	  		show_float_comment: vm_main.show_float_comment
	  	},
	  	methods: {
	  		close_comment: vm_main.close_comment,
	  		send_comment: function(){
	  		// 发送评论
	  			var self = this;
	  			var data_json = {
  					id: vm_main.id,
  					content: $("textarea[name='content']").val()
  				};

		    	if(vm_main.type == 1){
		    		data_json.reply_user_id = vm_main.item_reply.user_id;
		    	}
		    	if(empty(data_json.content)){
		    		$.toast("评论内容不能为空");
		    		return false;
		    	}

		        handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=reply",data_json, '', 1).done(function(result){
		        	vm_main.item_reply.push({id:result.id, content:data_json.content, nick_name:vm_main.user_name, reply_user_id:data_json.reply_user_id, reply_user_name: "", user_id:vm_main.user_id});
		        	vm_main.list.reply_count = vm_main.list.reply_count+1;
		        	if(result.status == 1){
		        		$.toast('发送评论成功',1000);
		        	}
		        	else{
		        		$.toast('发送失败',1000);
		        	}
		        	self.show_float_comment = false;
		        	vm_main.show_float_comment = false;
	  				$(".footer-bar").show();
			    }).fail(function(err){
			        $.toast(err);
			    });
	  		}
	  	}
	});

    // 无限滚动
 	$($page).on('infinite', function(e) {

    	if (vm_main.loading || vm_main.page > vm_main.total_page){
	 		$(".content-inner").css({paddingBottom:"0"});
			return;
	 	}
	  	vm_main.loading = true;

	  	handleAjax.handle(ajax_url, {page: vm_main.page}, '', 1).done(function(result){
	  		setTimeout(function(){
	  			for (var i=0; i < result.list.length; i++) {
				    vm_main.share_list.push( result.list[i] );
				}

		        vm_main.page++;
				vm_main.loading = false;

		        $('.lazyload').picLazyLoad();
				$.refreshScroller();
	  		}, 1000);
	  		
	    }).fail(function(err){
	        $.toast(err);
	    });


    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {

  		handleAjax.handle(ajax_url,'', '', 1).done(function(result){
  			setTimeout(function(){
  				vm_main.share_list = result.list;
	  			vm_comment.is_star_share = result.is_star_share;

	  			// 加载完毕需要重置
		        $.pullToRefreshDone('.pull-to-refresh-content');
  			}, 1000);
	    }).fail(function(err){
	        $.toast(err);
	    });

    });


    // 初始化参数
 	function init_paramet(){

        new_paramet = paramet.cate_id ? '&cate_id='+paramet.cate_id : '',

        ajax_url = APP_ROOT+"/mapi/index.php?ctl=share&act=index"+new_paramet;
        console.log(ajax_url);
    }

    // 输入过滤emoji表情
    $("textarea[name='content']").on('input propertychange', function(){
    	var val = $(this).val();
    	$(this).val($.emoji2Str(val));
    });

});