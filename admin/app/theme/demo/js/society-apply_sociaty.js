$(function() {
    $('input[name="guildtype"]').change(function() {
        var input_val = $('input[name="guildtype"]:checked').val();
        if (input_val == 0) {
            $(".J-sociaty").css("display", "block");
            $(".J-agent").css("display", "none");
        } else if (input_val == 1) {
            $(".J-sociaty").css("display", "none");
            $(".J-agent").css("display", "block");
        }
    });
    $('input[name="bank"]').change(function() {
        var input_val = $('input[name="bank"]:checked').val();
        console.log(input_val);
        if (input_val == 0) {
            $(".J-bank_name").css("display", "block");
        } else if (input_val == 1) {
            $(".J-bank_name").css("display", "none");
        }
    });
    $('input[name="bank1"]').change(function() {
        var input_val = $('input[name="bank1"]:checked').val();
        console.log(input_val);
        if (input_val == 0) {
            $(".J-bank_name1").css("display", "block");
        } else if (input_val == 1) {
            $(".J-bank_name1").css("display", "none");
        }
    });

    var vm = avalon.define({
        $id: "ms_agent",
        guildname: '',
        realname: '',
        tel: '',
        paperstype: '',
        papersnumb: '',
        bankname: '',
        province: '',
        city: '',
        area: '',
        subb_name: '',
        acc_numb: '',
        is_errorinput: false,
        is_disabled: false,
        create: function() {
            //验证表单

            if ($.checkEmpty(vm.guildname)) {
                $.showToast("请输入公会名称");
                $('input[name="guildname"]').focus();
                return false;
            }
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
                $.showToast("手机号码长度不能超过11位");
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
            if ($('input[name="bank"]:checked').val() == 0) {
                if ($.checkEmpty(vm.bankname)) {
                    $.showToast("请选择银行行名称");
                    return false;
                }
            }
            if ($.checkEmpty((vm.province && vm.city && vm.area)||(vm.province && vm.city))) {
                $.showToast("请选择开户地");
                return false;
            }
            if ($.checkEmpty(vm.subb_name)) {
                $.showToast("请输入支行名称");
                $('input[name="subb_name"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.acc_numb)) {
                $.showToast("请输入开户账号");
                $('input[name="acc_numb"]').focus();
                return false;
            }
            if (!$.maxLength(vm.acc_numb, 19, true)) {
                $.showToast("开户账号格式错误");
                vm.is_errorinput = true;
                $('input[name="acc_numb"]').focus();
                return false;
            }
            if (!$.minLength(vm.acc_numb, 19, true)) {
                $.showToast("开户账号格式错误");
                vm.is_errorinput = true;
                $('input[name="acc_numb"]').focus();
                return false;
            }


            // if(!$.maxLength(vm.data.family_manifesto, 110)){         
            //     $.showToast("家族宣言超过限制字数");
            //     return false;
            // }
            var url;
            // 创建家族
            handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=apply_sociaty_save", { guildname: vm.guildname, realname: vm.realname, tel: vm.tel, paperstype: vm.paperstype, papersnumb: vm.papersnumb, bankname: vm.bankname, province: vm.province, city: vm.city, area: vm.area, subb_name: vm.subb_name, acc_numb: vm.acc_numb }).done(function(msg) {
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
$(function() {
    var vm = avalon.define({
        $id: "ms_sociaty",
        guildname1: '',
        bankname1: '',
        province1: '',
        city1: '',
        area1: '',
        subb_name1: '',
        acc_numb1: '',
        acc_name: '',
        contacts: '',
        tel1: '',
        corporation: '',
        comp_name: '',
        regis_address: '',
        cont_address: '',
        invoice: '',
        business_photo:'',
        creates: function() {
            // 验证表单
            if ($.checkEmpty(vm.guildname1)) {
                $.showToast("请输入公会名称");
                $('input[name="guildname1"]').focus();
                return false;
            }
            if ($('input[name="bank1"]:checked').val() == 0) {
                if ($.checkEmpty(vm.bankname1)) {
                    $.showToast("请输入银行行名称");
                    return false;
                }
            }
            if ($.checkEmpty((vm.province1 && vm.city1 && vm.area1)||(vm.province1 && vm.city1))) {
                $.showToast("请输入开户地");
                return false;
            }
            if ($.checkEmpty(vm.subb_name1)) {
                $.showToast("请输入支行名称");
                $('input[name="subb_name1"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.acc_numb1)) {
                $.showToast("请输入开户账号");
                $('input[name="acc_numb1"]').focus();
                return false;
            }
            if (!$.checkMobilePhone(vm.acc_numb1)) {
                $.showToast("开户账号格式错误");
                vm.is_errorinput = true;
                $('input[name="acc_numb1"]').focus();
                return false;
            }
            if (!$.maxLength(vm.acc_numb1, 19, true)) {
                $.showToast("开户账号格式错误");
                vm.is_errorinput = true;
                $('input[name="acc_numb1"]').focus();
                return false;
            }
            if (!$.minLength(vm.acc_numb1, 19, true)) {
                $.showToast("开户账号格式错误");
                vm.is_errorinput = true;
                $('input[name="acc_numb1"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.acc_name)) {
                $.showToast("请输入开户名称");
                $('input[name="acc_name"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.contacts)) {
                $.showToast("请输入联系人姓名");
                $('input[name="contacts"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.tel1)) {
                $.showToast("请输入电话号码");
                $('input[name="tel1"]').focus();
                return false;
            }
            if ($.trim(vm.tel1).length == 0) {
                $.showToast("手机号码不能为空");
                vm.is_errorinput = true;
                $('input[name="tel1"]').focus();
                return false;
            }
            if (!$.checkMobilePhone(vm.tel1)) {
                $.showToast("手机号码格式错误");
                vm.is_errorinput = true;
                $('input[name="tel1"]').focus();
                return false;
            }
            if (!$.maxLength(vm.tel1, 11, true)) {
                $.showToast("长度不能超过11位");
                vm.is_errorinput = true;
                $('input[name="tel1"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.corporation)) {
                $.showToast("请输入法人代表");
                $('input[name="corporation"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.comp_name)) {
                $.showToast("请输公司名称");
                $('input[name="comp_name"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.regis_address)) {
                $.showToast("请输注册地址");
                $('input[name="regis_address"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.cont_address)) {
                $.showToast("请输联系地址");
                $('input[name="cont_address"]').focus();
                return false;
            }
            if ($.checkEmpty(vm.invoice)) {
                $.showToast("可开发票税点");
                return false;
            }
            if ($.checkEmpty(vm.business_photo)) {
                $.showToast("请上传营业执照");
                return false;
            }

            // if(!$.maxLength(vm.data.family_manifesto, 110)){         
            //     $.showToast("家族宣言超过限制字数");
            //     return false;
            // }
            var url;
            // 创建家族
            handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=apply_sociaty_save", { name: vm.guildname1, bank_name: vm.bankname1, province: vm.province1, city: vm.city1, area: vm.area1, branch_name: vm.subb_name1, open_account_num: vm.acc_numb1,open_account_name:vm.acc_name, contact: vm.contacts, contact_number: vm.tel1, legal: vm.corporation, company_name: vm.comp_name, register_site: vm.regis_address, contact_site: vm.cont_address, receipt: vm.invoice, business_photo:vm.business_photo}).done(function(msg) {
                $.showSuccess(msg);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }).fail(function(err) {
                $.showToast(err);
            });
        }
    });
    avalon.scan(document.getElementById('ms_sociaty'));
});
