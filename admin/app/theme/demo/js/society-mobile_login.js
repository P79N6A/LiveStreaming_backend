$(function() {
    var code_lefttime;
    var code_timeer = null;
    var vm = avalon.define({
        $id: "login",
        mobile: '',
        verify_coder: '',
        is_errorinput: false,
        is_disabled: false,
        login: function() {
            if (!this.check()) {
                return false;
            }
            // 登录
            var query = new Object();
            query.mobile = vm.mobile;
            query.verify_coder = vm.verify_coder;
            query.pc = 1;
            $.ajax({
                url: APP_ROOT + "/mapi/index.php?ctl=society&act=mobile_login_save&itype=app",
                data: query,
                type: "POST",
                dataType: "json",
                success: function(result) {
                    if (result.status == 1) {
                        location.href = APP_ROOT+"/index.php?ctl=society_user&act=authent&society_id="+society_id;
                    } else {
                        if (result.error) {
                            $.showErr(result.error);
                        } else {
                            $.showErr("操作失败");
                        }
                    }
                }
            });
        },
        check: function(el) {
            // 验证表单
            if ($.trim(vm.mobile).length == 0) {
                $(".errortip").html("手机号码不能为空");
                vm.is_errorinput = true;
                return false;
            }
            if (!$.checkMobilePhone(vm.mobile)) {
                $(".errortip").html("手机号码格式错误");
                vm.is_errorinput = true;
                return false;
            }
            if (!$.maxLength(vm.mobile, 11, true)) {
                $(".errortip").html("长度不能超过11位");
                vm.is_errorinput = true;
                return false;
            } else {
                $(".errortip").html("");
                vm.is_errorinput = false;
                return true;
            }
        },
        send_code: function() {
            // 发送验证码
            if (vm.is_disabled) {
                $.showErr("发送速度太快了");
                return false;
            } else {
                var thiscountdown = $("#j-send-code");
                var query = new Object();
                query.mobile = vm.mobile;
                $.ajax({
                    url: APP_ROOT + "/mapi/index.php?ctl=login&act=send_mobile_verify",
                    data: query,
                    type: "POST",
                    dataType: "json",
                    success: function(result) {
                        console.log(result);
                        if (result.status == 1) {
                            countdown = 60;
                            // 验证码倒计时

                            code_lefttime = 60;
                            vm.code_lefttime_fuc("#j-send-code", code_lefttime);
                            // $.showSuccess(result.info);
                            return false;
                        } else {
                            $.showErr(result.error);
                            return false;
                        }
                    }
                });
            }

        },
        code_lefttime_fuc: function(verify_name, code_lefttime) {
            // 验证码倒计时
            clearTimeout(code_timeer);
            $(verify_name).html("重新发送 " + code_lefttime);
            code_lefttime--;
            if (code_lefttime > 0) {
                $(verify_name).attr("disabled", "disabled");
                vm.is_disabled = true;
                code_timeer = setTimeout(function() { vm.code_lefttime_fuc(verify_name, code_lefttime); }, 1000);
            } else {
                code_lefttime = 60;
                vm.is_disabled = false;
                $(verify_name).removeAttr("disabled");
                $(verify_name).html("发送验证码");
            }
        }
    });
    avalon.scan(document.getElementById('login'));
});
