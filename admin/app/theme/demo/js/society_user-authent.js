$(".fw-form-control").bind('propertychange input', function() {
    var cur = $(this);
    var counter = $(this).val().length;
    $(this).siblings(".fw-form-after-right").find("em").text((30 - counter) > 0 ? (30 - counter) : 0);
});
$(function() {
    var vm = avalon.define({
        $id: "ms_agent",
        realname: '',
        tel: '',
        paperstype: '',
        papersnumb: '',
        liveaddress: '',
        textarea: '',
        text: '',
        identify_positive_image:'',
        identify_nagative_image:'',
        identify_hold_image:'',
        show_bill:'',
        created: function() {
            // 验证表单
            if ($.checkEmpty(vm.realname)) {
                $.showToast("请输入您的真实姓名");
                $('input[name="realname"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.tel)) {
                $.showToast("请输入电话号码");
                $('input[name="tel"]').focus();
                return false;
            }
            if ($.trim(vm.tel).length == 0) {
                $.showToast("手机号码不能为空");
                vm.is_errorinput = true;
                $('input[name="tel"]').focus();
                return false;
            }
            if (!$.checkMobilePhone(vm.tel)) {
                $.showToast("手机号码格式错误");
                vm.is_errorinput = true;
                $('input[name="tel"]').focus();
                return false;
            }
            if (!$.maxLength(vm.tel, 11, true)) {
                $.showToast.html("手机号码长度不能超过11位");
                vm.is_errorinput = true;
                $('input[name="tel"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.paperstype)) {
                $.showToast("请选择证件类型");
                return false;
            }
            if ($.checkEmpty(vm.papersnumb)) {
                $.showToast("请输入证件号");
                $('input[name="papersnumb"]').focus();
                return false;
            }
            if (!$.checkIdentityCode(vm.papersnumb)) {
                $.showToast("证件号错误");
                $('input[name="papersnumb"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.liveaddress && vm.textarea)) {
                $.showToast("请填写作品");
                $('input[name="liveaddress"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.text)) {
                $.showToast("请输入备注");
                $('input[name="text"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.identify_positive_image)) {
                $.showToast("请上传身份证正面照");
                $('input[name="text"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.identify_nagative_image)) {
                $.showToast("请上传身份证反面照");
                $('input[name="text"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.identify_hold_image)) {
                $.showToast("请上传手持身份证照");
                $('input[name="text"]').focus();
                return false;
            }
            // if(!$.maxLength(vm.data.family_manifesto, 110)){         
            //     $.showToast("家族宣言超过限制字数");
            //     return false;
            // }
            var url;
            // 创建家族
            handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society_user&act=attestation", { authentication_name: vm.realname, contact: vm.tel, paperstype: vm.paperstype, identify_number: vm.papersnumb, opus_site: vm.liveaddress, opus_explain: vm.textarea, remark:vm.text, society_id:society_id, identify_positive_image:vm.identify_positive_image, identify_nagative_image:vm.identify_nagative_image, identify_hold_image:vm.identify_hold_image, show_bill:vm.show_bill}).done(function(msg) {
                $.showSuccess(msg);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }).fail(function(err) {
                $.showToast(err);
            });
        }
    });
    avalon.scan(document.getElementById('ms_agent'));
});
