$(document).on("pageInit", "#page-share-add_share", function(e, pageId, $page) {

    var buttons = [];

    $(document).on('click', '.modal-overlay-visible', function() {
        $.closeModal();
    });

    var vm_add_share = new Vue({
        el: '#vscope-add_share',
        data: {
            title: '',
            cate_id: '',
            cate_name: '',
            content: ''
        },
        computed: {
            new_buttons: function () {

                for(var i=0; i<cate_list.length; i++){
                    console.log(cate_list[i]);
                    buttons[i] = {
                        text: cate_list[i].cate_name,
                        id: cate_list[i].id,
                        onClick: function() {
                            vm_add_share.cate_id = this.id;
                            vm_add_share.cate_name = this.text;

                        }
                    };
                }
                return buttons;
            }
        },
        methods: {
            create_actions: function(){
                console.log(vm_add_share.new_buttons);
                this.$nextTick(function () {
                    var groups = [vm_add_share.new_buttons];
                    $.actions(groups);
                });
            },
            check: function(){
            // 表单验证
                var self = this;

                vm_add_share.title = $.emoji2Str(vm_add_share.title);
                vm_add_share.content = $.emoji2Str(vm_add_share.content);
                
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

                var json_imgs = JSON.stringify(imgs);

                var data = {
                    title: self.title,
                    cate_id: self.cate_id,
                    content: self.content,
                    imgs: json_imgs
                };

                handleAjax.handle(APP_ROOT+"/weixin/index.php?ctl=share&act=add",data).done(function(result){
                    $.toast(result,1000);
                    setTimeout(function(){
                        location.href= APP_ROOT+"/weixin/index.php?ctl=user_center&act=share";
                    },1000);

                }).fail(function(err){
                    $.toast(err);
                });
            }
        }
    });


    get_file_more_fun('upload-imgs', 5);

});
