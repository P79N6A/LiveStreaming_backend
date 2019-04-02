// 修改昵称
var nick_name = $("#inputhidden_nick_name").val();
var vm_modifyName = avalon.define({
    $id: "modify-name",
    modify_name: function() {
        var html_modify_name = '<div ms-controller="weebox-modify-name" id="weebox-modify-name" class="m-modify-name">'+
                                '<form>'+
                                    '<div class="form-group clearfix">'+
                                    '   <label class="control-label" for="recharge_type">当前昵称：</label>'+
                                        '<div class="control-content">'+
                                            '<span>'+nick_name+'</span>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="form-group clearfix">'+
                                        '<div class="control-content control-content-auto">'+
                                            '<input type="text" class="form-control" maxlength="15" ms-duplex="@nick_name" ms-rules="{required:true}" maxlength="15" placeholder="请输入您的新昵称">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="submit-group t-c">'+
                                        '<a href="javascript:void(0);" class="btn btn-primary" ms-click="@ajax_submit">保存修改</a>'+
                                    '</div>'+
                                '</form>'+
                              '</div>';
        $.weeboxs.open(html_modify_name, {title:"修改昵称", animate:false, width:350, showButton:false, showCancel:false, showOk:false,onopen:function(){onopen_callback();}});
        // 弹窗后回调函数
        function onopen_callback(){
            vm_modifyName.vm_weebox_modifyName_fuc();
            avalon.scan(document.getElementById('weebox-modify-name'));
        }
    },
    vm_weebox_modifyName_fuc: function(){
        // 修改昵称（weebox）
        var vm_weebox_modifyName = avalon.define({
            $id: "weebox-modify-name",
            nick_name: nick_name,
            ajax_submit: function() {
                // 提交已修改的昵称
                var query = new Object();
                query.nick_name = vm_weebox_modifyName.nick_name;
                if($.checkEmpty(query.nick_name)){
                    $.showErr("请输入昵称");
                    return false;
                }
                if($.getStringLength(query.nick_name)>48){
                    $.showErr("昵称长度不超过15个汉字");
                    return false;
                }
                $.ajax({
                    url:APP_ROOT+"/mapi/index.php?ctl=user&act=update&itype=app",
                    data:query,
                    type:"POST",
                    dataType:"json",
                    success:function(result){
                        if(result.status == 1){
                            $.weeboxs.close();
                            if(result.error){
                                $.showSuccess(result.error,function(){
                                    location.reload();
                                });
                            }
                            else{
                                $.showSuccess("操作成功",function(){
                                    location.reload();
                                });
                            }
                        }
                        else{
                            if(result.error){
                                $.showErr(result.error);
                            }
                            else{
                                $.showErr("操作失败");
                            }
                        }
                    }
                });
            }
        });
    }
});

// 绑定手机
var vm_bind_mobile = avalon.define({
    $id: "pop_bind_mobile",
    pop: function(){
        var html = $("#hidden-bind-mobile").html();
        html = '<div ms-controller="bind_mobile" id="bind_mobile">'+html+'</div>';
        $.weeboxs.open(html, {title:"绑定手机", animate:false, width:360, showButton:false, showCancel:false, showOk:false, onopen:function(){}});
    },
    pop_pwd: function(){
        if(!user_mobile){
            $.showErr("未绑定手机号，请先绑定！");
            return false;
        }
        var html = $("#hidden-set-pwd").html();
        html = '<div ms-controller="set_pwd" id="set_pwd">'+html+'</div>';
        $.weeboxs.open(html, {title:"设置密码", animate:false, width:360, showButton:false, showCancel:false, showOk:false, onopen:function(){}});
    }
});

// 认证
var vm = avalon.define({
    $id: "bind-prove",
    bind_prove: function() {
        handleAjax.handle(APP_ROOT+"/index.php?ctl=user&act=authent&tmpl_pre_dir=inc", "", "html").done(function(result){
           $.weeboxs.open(result, {title:"认证", animate:false, width:435, showButton:false, showCancel:false, showOk:false});
        }).fail(function(err){
            $.showErr(err);
        });
    }
});


// 修改头像
/*function upload_avatar(w,h){
    var scale_w = parseFloat(w/h);
    scale_w = scale_w.toFixed(1);
    scale = scale_w+'/1';
    bind_CropAvatar(scale, function(){
        var head_image = $("input[name='image-avatar']").val();
        handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=user&act=update",{"head_image":head_image}).done(function(msg){
            $.showSuccess(msg);
        }).fail(function(err){
            $.showErr(err);
        });
    });
}*/