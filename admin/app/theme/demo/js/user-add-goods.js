var vm = avalon.define({
    $id: "add_goods",
    name: "",
    imgs: "",
    price: "",
    url: "",
    submit: function(){
        if($.checkEmpty(vm.name)){
            $.showErr("请输入物品名称");
            return false;
        }
        if($.checkEmpty(vm.imgs)){
            $.showErr("请选择物品图片");
            return false;
        }
        if($.checkEmpty(vm.price)){
            $.showErr("请填写物品价格");
            return false;
        }
        if($.checkEmpty(vm.url)){
            $.showErr("请填写物品链接");
            return false;
        }

        var data = {
            name: vm.name,
            imgs: JSON.stringify([vm.imgs]),
            price: vm.price,
            url: vm.url,
        };

        $.post(APP_ROOT+"/mapi/index.php?ctl=shop&act=add_goods&itype=app", data, function(res){
            if(res.status){
                $.showSuccess(res.error);
                location.reload();
            } else {
                $.showErr(res.error);
            }
        }, 'json');
    }
});
avalon.scan(document.getElementById('add_goods'));