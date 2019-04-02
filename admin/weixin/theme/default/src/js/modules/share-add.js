$(document).on("pageInit", "#page-share-add", function(e, pageId, $page) {
    var data = ["2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件", "2017年互联网大事件"];
    $(document).on('click', '.create-actions', function() {
        var buttons1 = [{
            text: data[0],
            onClick: function() {
                $("input[name='type']").val(data[0]);
            }
        }, {
            text: data[1],
            onClick: function() {
                $("input[name='type']").val(data[1]);
            }
        }, {
            text: data[2],
            bold: true,
            color: 'danger',
            onClick: function() {
                $("input[name='type']").val(data[2]);
            }
        }, {
            text: data[3],
            onClick: function() {
                $("input[name='type']").val(data[3]);
            }
        }, {
            text: data[4],
            onClick: function() {
                $("input[name='type']").val(data[4]);
            }
        }, {
            text: data[4],
            onClick: function() {
                $("input[name='type']").val(data[4]);
            }
        }];

        var groups = [buttons1];
        $.actions(groups);
    });
    $(document).on('click', '.modal-overlay-visible', function() {
        $.closeModal();
    });

    var vm_add_share = new Vue({
        el: '#vscope-add_share',
        data: {
            title: '',
            cate_id: '',
            content: '',
            imgs: ''
        },
        methods: {
            check: function(){
            // 表单验证
                var self = this;
                if(empty(self.title)){
                    $.toast('分享标题不能为空');
                    return false;
                }
                else if(empty(self.cate_id)){
                    $.toast('请选择分类');
                    return false;
                }
                else if(empty(self.content)){
                    $.toast('分享内容不能为空');
                    return false;
                }
                else{
                    return true;
                }
            },
            submit: function(){
            // 发起分享

                self = this;
                if(!self.check()){
                    return false;
                }

                var data = {
                    title: self.title,
                    cate_id: self.cate_id,
                    content: self.content,
                    imgs: self.imgs
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=add",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        // location.reload();
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            }
        }
    });

});
