var familyInfo = {
    "family_logo": $("input[name='family_logo']").val(),
    "family_name": $("input[name='family_name']").val(),
    "family_manifesto": $("textarea[name='family_manifesto']").val(),
    "family_id": $("input[name='family_id']").val(),
};
var vm = avalon.define({
    $id: "family_edit",
    data: familyInfo,
    create: function(is_edit) {
        // 验证表单
        if($.checkEmpty(vm.data.family_logo)){         
            $.showErr("请上传家族logo");
            return false;
        }
        if($.checkEmpty(vm.data.family_name)){         
            $.showErr("请输入家族名");
            return false;
        }
        if($.checkEmpty(vm.data.family_manifesto)){         
            $.showErr("请输入家族宣言");
            return false;
        }
        if(!$.maxLength(vm.data.family_manifesto, 110)){         
            $.showErr("家族宣言超过限制字数");
            return false;
        }
        var url;
        is_edit ? url = APP_ROOT+"/mapi/index.php?ctl=family&act=save" : url = APP_ROOT+"/mapi/index.php?ctl=family&act=create";
        // 创建家族
        handleAjax.handle(url,vm.data).done(function(msg){
            $.showSuccess(msg);
            setTimeout(function(){
                location.reload();
            },1000);
        }).fail(function(err){
            $.showErr(err);
        });
    }
});
avalon.scan(document.getElementById('family_edit'));