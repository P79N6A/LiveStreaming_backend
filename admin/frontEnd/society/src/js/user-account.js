var code_lefttime;
var code_timeer = null;
avalon.define({
    $id: "vm-set_pwd",
    setPwd: function(){
 		layer.open({
            type: 1,
            title: '修改密码',
            skin: 'layui-layer-rim',
            area: ['500px', '300px'],
            content:   `<div style="padding:15px 20px;" ms-important="vm-pop_set_pwd" id="pop_set_pwd">
            				<form class="form-inline">
	                            <div class="form-group">
	                                <label for="mobile" class="mr-10">手机号</label>
	                                <input type="text" class="form-control mr-10" id="mobile" ms-duplex="@form_data.mobile" placeholder="请输入手机号">
	                                <a href="javascript:void(0)" class="btn btn-primary" id="j-send-code" ms-click="send_code">发送验证码</a>
	                            </div>
	                            <div class="form-group">
	                                <label for="verify_coder" class="mr-10">验证码</label>
	                                <input type="text" class="form-control" id="verify_coder" ms-duplex="@form_data.verify_coder" placeholder="请输入验证码">
	                            </div>
	                            <div class="form-group" style="displaty:block;">
	                                <label for="new_pwd" class="mr-10">新密码</label>
	                                <input type="password" class="form-control" id="new_pwd" ms-duplex="@form_data.new_pwd" placeholder="">
	                            </div>
                            </form>
                            <button type="submit" class="btn btn-primary mt-10" ms-click="submit" style="margin-left:52px;">确认修改</button>
                        </div>`,
            success: function(layero, index){
                avalon.scan(document.getElementById('pop_set_pwd'));
            }
        });
    }
});

avalon.define({
    $id: "vm-pop_set_pwd",
    is_disabled: false,
    form_data: {
	  	mobile: '',
        verify_coder: '',
		new_pwd: ''
    },
    check(){
    	let self = this;
	 	if ($checkAction.checkEmpty(self.form_data.mobile)) {
            layer.msg('请输入手机号');
            return false;
        }
        if($checkAction.checkEmpty(self.form_data.verify_coder)){
        	layer.msg('请输入验证码');
            return false;
        }
        if($checkAction.checkEmpty(self.form_data.new_pwd)){
        	layer.msg('请输入新密码');
            return false;
        }
        else{
        	return true;
        }
    },
    submit: function(){
        let self = this;
    	if(self.check()){
    		let loading = layer.load();
	        $handleAjax.handle({
	            url: APP_ROOT + "/mapi/index.php?ctl=user&act=update_promoter_pwd",
	            isTip: false,
	            data: self.form_data
	        }).done(function(result){
	        	layer.close(loading);
	            if(result.status == 1){
	            	layer.msg(result.error || '操作成功',{
	            		time: 1000
	            	});
	            	setTimeout(function(){
	            		location.reload();
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
    send_code: function() {
        var countdown = 0, self = this;
        // 发送验证码
        if (self.is_disabled) {
            layer.msg('发送速度太快了');
            return false;
        } else {
            var thiscountdown = $("#j-send-code");
            var query = new Object();
            query.mobile = self.form_data.mobile;
            $.ajax({
                url: APP_ROOT + "/mapi/index.php?ctl=login&act=send_mobile_verify&itype=bm_index",
                data: query,
                type: "POST",
                dataType: "json",
                success: function(result) {
                    if (result.status == 1) {
                        countdown = 60;
                        // 验证码倒计时

                        code_lefttime = 60;
                        self.code_lefttime_fuc("#j-send-code", code_lefttime);
                        // $.showSuccess(result.info);
                        return false;
                    } else {
                        layer.msg(result.error);
                        return false;
                    }
                }
            });
        }

    },
    code_lefttime_fuc: function(verify_name, code_lefttime) {
    	var self = this;
        // 验证码倒计时
        clearTimeout(code_timeer);
        $(verify_name).html("重新发送 " + code_lefttime);
        code_lefttime--;
        if (code_lefttime > 0) {
            $(verify_name).attr("disabled", "disabled");
            self.is_disabled = true;
            code_timeer = setTimeout(function() { self.code_lefttime_fuc(verify_name, code_lefttime); }, 1000);
        } else {
            code_lefttime = 60;
            self.is_disabled = false;
            $(verify_name).removeAttr("disabled");
            $(verify_name).html("发送验证码");
        }
    }
});