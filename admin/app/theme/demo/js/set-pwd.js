/**
 * Created by Administrator on 2017/3/13.
 */
var code_lefttime;
var code_timeer =null;
var vm_set_pwd = avalon.define({
    $id: "set_pwd",
    pwd: '',
    verify_coder: '',
    is_errorinput: false,
    is_disabled: false,
    set: function() {
        if(! this.check()){
            return false;
        }
        if(!this.verify()){
            return false;
        }
        // 修改密码
        var url = APP_ROOT+"/mapi/index.php?ctl=user&act=set_pwd&itype=app";
        var data = {"pwd": vm_set_pwd.pwd, "verify_coder": vm_set_pwd.verify_coder};
        handleAjax.handle(url,data).done(function(msg){
            $.showSuccess(msg, function(){
                location.reload();
            });
        }).fail(function(err){
            $.showErr(err);
        });
    },
    verify:function(el){
        if($.trim(vm_set_pwd.verify_coder).length == 0)
        {
            $(".errortip").html("<i class='iconfont'></i>验证码不能为空");
            vm_set_pwd.is_errorinput = true;
            return false;
        } else{
            $(".errortip-yzm").html("");
            vm_set_pwd.is_errorinput = false;
            return true;
        }
    },
    check: function(el){
        // 验证表单
        if($.trim(vm_set_pwd.pwd).length == 0)
        {
            $(".errortip").html("<i class='iconfont'></i>密码不能为空");
            vm_set_pwd.is_errorinput = true;
            return false;
        }

        if($.trim(vm_set_pwd.pwd).length < 3)
        {
            $(".errortip").html("<i class='iconfont'></i>密码长度不能小于3位");
            vm_set_pwd.is_errorinput = true;
            return false;
        }
        if(!$.maxLength(vm_set_pwd.pwd,16,true))
        {
            $(".errortip").html("长度不能超过16位");
            vm.is_errorinput = true;
            return false;
        }
        else{
            $(".errortip").html("");
            vm_set_pwd.is_errorinput = false;
            return true;
        }
    },
    send_code: function(e){
        // 发送验证码
        if(vm_set_pwd.is_disabled){
            $.showErr("发送速度太快了");
            return false;
        }
        else{
            var thiscountdown=$(e.target);
            $.ajax({
                url:APP_ROOT+"/mapi/index.php?ctl=user&act=send_mobile_verify&itype=app",
                type:"POST",
                dataType:"json",
                success:function(result){
                    console.log(result);
                    if(result.status == 1){
                        countdown = 60;
                        // 验证码倒计时

                        code_lefttime = 60;
                        vm_set_pwd.code_lefttime_fuc(e.target,code_lefttime);
                        // $.showSuccess(result.info);
                        return false;
                    }
                    else{
                        $.showErr(result.error);
                        return false;
                    }
                }
            });
        }

    },
    code_lefttime_fuc: function(verify_name,code_lefttime){
        // 验证码倒计时
        clearTimeout(code_timeer);
        $(verify_name).html("重新发送 "+code_lefttime);
        code_lefttime--;
        if(code_lefttime >0){
            $(verify_name).attr("disabled","disabled");
            vm_set_pwd.is_disabled=true;
            code_timeer = setTimeout(function(){vm_set_pwd.code_lefttime_fuc(verify_name,code_lefttime);},1000);
        }
        else{
            code_lefttime = 60;
            vm_set_pwd.is_disabled=false;
            $(verify_name).removeAttr("disabled");
            $(verify_name).html("发送验证码");
        }
    }
});
avalon.scan(document.getElementById('set_pwd'));


