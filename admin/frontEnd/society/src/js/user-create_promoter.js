
var vm = avalon.define({
    $id: "vm-create",
    form_data: {
	  	mobile: $("input[name='hidden_mobile']").val() || '',
		password: '',
        binding_mobile: $("input[name='hidden_binding_mobile']").val() || '',
        promoter_name: $("input[name='hidden_promoter_name']").val() || '',
        login_state: 0,
        id: $objAction.getQueryString("id") || null
    },
    ischeckBindMobile: false,
    bindMobileID: '',
    bindMobileNickName: '',
    check(){
        // 表单验证
        if($checkAction.checkEmpty(this.form_data.mobile)){
            layer.msg('请输入登录手机号');
            return false;
        }
        else if(! $checkAction.checkMobilePhone(this.form_data.mobile)){
            layer.msg('请输入有效的手机号');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.password)){
            layer.msg('请输入密码');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.binding_mobile)){
            layer.msg('请输入绑定会员的手机号');
            return false;
        }
        else if($checkAction.checkEmpty(this.form_data.promoter_name)){
            layer.msg('请输入推广商名称');
            return false;
        }
        else{
            return true;
        }
    },
    submit() {
        // 提交创建
        if(this.check()){
            layer.load();
            $handleAjax.handle({
                url: APP_ROOT + "/mapi/index.php?ctl=user&act=update_promoter",
                isTip: false,
                data: this.form_data
            }).done(function(result){
                layer.closeAll();
                if(result.status == 1){
                    layer.msg(result.error || '操作成功',{
                        time: 1000
                    });
                    setTimeout(function(){
                        location.href = TMPL_REAL + "/index.php?ctl=user&act=promoter_checklist";
                    }, 1000);
                }
                else{
                    layer.msg(result.error || '操作失败');
                }

            }).fail(function(err){
                console.log(err);
            });
        }
    },
    checkBindMobile() {
        // 绑定会员手机号验证
        let self = this, loading = layer.load();
        $handleAjax.handle({
            url: APP_ROOT + "/mapi/index.php?ctl=user&act=check_user",
            data: {
                binding_mobile: this.form_data.binding_mobile
            },
            completeCallBack: function(){
                layer.close(loading);
            },
            isTip: false
        }).done(function(result){
            if(result.status == 1){
                self.ischeckBindMobile = true;
                self.bindMobileID = result.user.id;
                self.bindMobileNickName = result.user.nick_name;
            }
            else{
                self.ischeckBindMobile = false;
                self.bindMobileID = '';
                self.bindMobileNickName = '';
                layer.alert(result.error);
            }
        }).fail(function(err){
            layer.alert(err);
        });
    }
});