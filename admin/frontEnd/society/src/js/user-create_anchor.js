get_file_fun('identify_positive');
get_file_fun('identify_nagative');
get_file_fun('identify_hold');
get_file_fun('head_image');

$("#distpicker").distpicker({ autoSelect: false });

var vm = avalon.define({
    $id: "vm-create",
    form_data: {
        bm_special: 0,
		nick_name: '',
        mobile: '',
        province: '',
        city: '',
        sex: 1,
        realname: '',
        identify_number: '',
        identify_positive_image: '',
        identify_nagative_image: '',
        identify_hold_image: '',
        head_image: ''
    },
    check(){
        if($checkAction.checkEmpty(this.form_data.nick_name)){
            layer.msg('请输入主播昵称');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.head_image)){
            layer.msg('请上传主播头像');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.mobile)){
            layer.msg('请输入手机号');
            return false;
        }
        else if(! $checkAction.checkMobilePhone(this.form_data.mobile)){
            layer.msg('请输入有效的手机号');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.province)){
            layer.msg('请选择省份');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.city)){
            layer.msg('请选择城市');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.realname)){
            layer.msg('请输入真实姓名');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.identify_number)){
            layer.msg('请输入身份证号码');
            return false;
        }
     	else if(! $checkAction.checkID(this.form_data.identify_number)){
            layer.msg('请输入有效的身份证号码');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.identify_positive_image)){
            layer.msg('请上传身份证正面');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.identify_nagative_image)){
            layer.msg('请上传身份证反面');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.identify_hold_image)){
            layer.msg('请上传手持身份证');
            return false;
        }
        else{
            return true;
        }
    },
    submit() {
        if(this.check()){
            layer.load();
            $handleAjax.handle({
                url: APP_ROOT + "/mapi/index.php?ctl=user&act=update_anchor",
                isTip: false,
                data: this.form_data
            }).done(function(result){
                layer.closeAll();
                if(result.status == 1){
                    layer.msg(result.error || '操作成功',{
                        time: 1000
                    });
                    setTimeout(function(){
                        location.href = TMPL_REAL + '/index.php?ctl=user&act=promoter_anchorlist';
                    }, 1000);
                }
                else{
                    layer.msg(result.error || '操作失败');
                }

            }).fail(function(err){
                console.log(err);
            });
        }
    }
});