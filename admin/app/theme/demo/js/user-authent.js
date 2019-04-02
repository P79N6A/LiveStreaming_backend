var data_authent={
    "authentication_type": "",
    "authentication_name": "",
    "identify_number": "",
    "contact": "",
    "identify_positive_image": "",
    "identify_nagative_image": "",
    "identify_hold_image": "",
}
var vm = avalon.define({
    $id: "authent",
    data: data_authent,
    submit: function(){
        if($.checkEmpty(vm.data.authentication_type)){
            $.showErr("请选择认证类型");
            return false;
        }
        if($.checkEmpty(vm.data.authentication_name)){
            $.showErr("请填写真实姓名");
            return false;
        }
        if($.checkEmpty(vm.data.identify_number)){
            $.showErr("请填写身份证号码");
            return false;
        }
        if($.checkEmpty(vm.data.contact)){
            $.showErr("请填写联系方式");
            return false;
        }
        // if($.checkEmpty(vm.data.identify_positive_image)){
        //     $.showErr("请上传身份证正面照片");
        //     return false;
        // }
        // if($.checkEmpty(vm.data.identify_nagative_image)){
        //     $.showErr("请上传身份证背面照片");
        //     return false;
        // }
        // if($.checkEmpty(vm.data.identify_hold_image)){
        //     $.showErr("请上传手持身份证正面");
        //     return false;
        // }
        handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=user&act=attestation",vm.data).done(function(msg){
            $.showSuccess(msg);
            setTimeout(function(){
                location.reload();
            },1000);
        }).fail(function(err){
            $.showErr(err);
        });
    }
});
avalon.scan(document.getElementById('authent'));
