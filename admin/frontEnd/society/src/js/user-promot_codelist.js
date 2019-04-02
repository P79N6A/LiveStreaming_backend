avalon.define({
    $id: "vm-promot_codelist",
    search_data: {
	  	code_name: '',
		code_state: '',
    },
    search() {
        // 搜素
        layer.load();
        let cls = ".ajax-block";
        $handleAjax.handle({
            url: TMPL_REAL + "/index.php?ctl=user&act=promot_codelist",
            isTip: false,
            dataType: 'html',
            data: this.search_data
        }).done(function(result){
            layer.closeAll();
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find(cls),
            html = $(htmlObject).html();
            $(document).find(cls).html(html);

        }).fail(function(err){
            console.log(err);
        });
    },
    popCreateQrCode(){
        layer.open({
            type: 1,
            skin: 'layui-layer-rim',
            area: ['420px', '220px'],
            content:   `<div style="padding:15px 20px;" ms-important="vm-create_qrcode" id="create_qrcode">
                            <div class="form-group">
                                <label for="code_title">推广码标题</label>
                                <input type="text" class="form-control" id="code_title" ms-duplex="@form_data.name" placeholder="请输入推广码标题" style="width:100%;">
                            </div>
                            <button type="submit" class="btn btn-primary mt-10" ms-click="createQrcode">确认创建</button>
                        </div>`,
            success: function(layero, index){
                avalon.scan(document.getElementById('create_qrcode'));
            }
        });
    },
    viewQrCode(qrcode_url, qrcode_title){
        if(qrcode_url){
            layer.open({
                title: '推广二维码',
                type: 1,
                closeBtn: 0,
                anim: 2,
                content:    `<div style="padding:20px;text-align:center;" ms-important="vm-create_qrcode" id="create_qrcode">
                                <div class="qrcode-inner">
                                    <img src="${qrcode_url}" width="220" height="220">
                                    <div class="mt-10">${qrcode_title}</div>
                                    <div class="mt-10">
                                        <button class="btn btn-default" type="submit" onclick="layer.closeAll();">关闭</button>
                                        <a class="btn btn-default" href="${qrcode_url}" target="blank" role="button">下载二维码</a>
                                    </div>
                                </div>`
            });
        }
        else{
            layer.msg('暂无二维码');
        }
    }
});

avalon.define({
    $id: "vm-create_qrcode",
    form_data: {
        name: ''
    },
    check(){
        if($checkAction.checkEmpty(this.form_data.name)){
            layer.msg('请输入推广码标题');
            return false;
        }
        else{
            return true;
        }
    },
    createQrcode(){
        if(this.check()){
            let self = this, loading = layer.load();
            $handleAjax.handle({
                url: APP_ROOT + "/mapi/index.php?ctl=user&act=update_promotcode",
                isTip: false,
                data: this.form_data
            }).done(function(result){
                if(result.status == 1){
                    layer.closeAll();
                    layer.msg(result.error || '操作成功',{
                        time: 1000
                    });
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
                else{
                    layer.close(loading);
                    layer.msg(result.error || '操作失败');
                }
                self.form_data.name = '';
            }).fail(function(err){
                console.log(err);
            }); 
        }
    }
});