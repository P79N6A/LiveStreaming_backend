var vm = avalon.define({
    $id: "tipoff",
    data: data_tipoff,
    submit: function(){
        if($.checkEmpty(vm.data.screenshot)){
            $.showToast("请上传截图");
            return false;
        }
        if($.checkEmpty(vm.data.card_type)){
            $.showToast("请选择举报类型");
            return false;
        }
        if($.checkEmpty(vm.data.reason)){
            $.showToast("请填写举报原因");
            return false;
        }
        if($.checkEmpty(vm.data.verify_code)){
            $.showToast("请填写验证码");
            return false;
        }
        if($.checkEmpty(vm.data.qq)){
            $.showToast("请填写qq");
            return false;
        }
        handleAjax.handle(APP_ROOT+"/mapi/index.php?ctl=live&act=do_tipoff",vm.data).done(function(result){
            $.showSuccess(result);
            setTimeout(function(){
                location.reload();
            },1000);
        }).fail(function(err){
            $.showErr(err);
        });
    },
    refresh_verify: function(){
        var timenow = new Date().getTime();
        $('#v_code').attr("src",$('#v_code').attr("alt")+"&rand="+timenow);
    }
});
avalon.scan(document.getElementById('tipoff'));