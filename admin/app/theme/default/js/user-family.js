// 家族管理
var user_family = (function(){
	// 成员管理开关
	function show_edit(data_list){
		console.log(data_list);
		console.log(data_list.length);
		if(data_list.length){
			$(".btn-icon").slideToggle(100);
		}
		else{
			$.showErr("暂无数据");
			return false;
		}
	}

	// 审核申请中的成员   is_agree 1: 审核通过 2: 审核不通过 
    function review(r_user_id,is_agree){
    	var confirm_text;
		is_agree == 1 ? confirm_text = "确认审核通过？" : confirm_text = "确认审核不通过？";
		$.showConfirm(confirm_text,function(){
			handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=family_user&act=confirm",{"r_user_id":r_user_id, "is_agree":is_agree}).done(function(msg){
		        $.showSuccess(msg);
		        setTimeout(function(){
		            location.reload();
		        },1000);
		    }).fail(function(err){
		        $.showErr(err);
		    });
		});
    }

    // 移除家族成员
    function del(r_user_id){
    	$.showConfirm("确认移除该成员？",function(){
			handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=family_user&act=user_del",{"r_user_id":r_user_id}).done(function(msg){
		        $.showSuccess(msg);
		        setTimeout(function(){
		            location.reload();
		        },1000);
		    }).fail(function(err){
		        $.showErr(err);
		    });
		});
    }

    // 退出家族（非族长）
	function logout(){
		$.showConfirm("确认退出该家族？",function(){
			handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=family_user&act=logout").done(function(msg){
		        $.showSuccess(msg);
		        setTimeout(function(){
		            location.href = APP_ROOT+"/index.php?ctl=user&act=userinfo";
		        },1000);
		    }).fail(function(err){
		        $.showErr(err);
		    });
		});
	}

 	return { 
        show_edit: show_edit, review: review, del: del, logout: logout
    };
})();

$(".J-supervise").click(function(){
	user_family.show_edit(data_list);
});


// 修改头像
function upload_family_logo(w,h){
    var scale_w = parseFloat(w/h);
    scale_w = scale_w.toFixed(1);
    scale = scale_w+'/1';
    bind_CropAvatar(scale, function(){
        var logo = $("input[name='image-logo']").val();
        handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=family_user&act=update",{"logo":logo}).done(function(msg){
            $.showSuccess(msg);
        }).fail(function(err){
            $.showErr(err);
        });
    });
}

// 搜索家族成员
var vm_family_search = avalon.define({
    $id: "family_search",
    nick_name: "",
    is_apply: is_apply,
    do_search: function() {
	 	// 家族成员搜索
        location.href = APP_ROOT+"/index.php?ctl=user&act=family&is_apply="+this.is_apply+"&nick_name="+this.nick_name;
    }
});
avalon.scan(document.getElementById('family_search'));
$("input[name='nick_name']").bind('keypress',function(event){  
    if(event.keyCode == "13") vm_family_search.do_search();
});

$("#do_family_search").bind('click',function(event){
	vm_family_search.do_search();
});