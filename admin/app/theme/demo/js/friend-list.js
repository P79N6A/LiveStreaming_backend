/**
 * Created by Administrator on 2017/4/26.
 */
(function () {
    if(typeof loginInfo === 'undefined'){
        return;
    }
    // 好友列表
    var vm_friends_list = avalon.define({
        $id: "friends_list",
        items: typeof friends_list === 'undefined' ? [] : friends_list,
        search_val: "",
        page: 1,
        page_size: 15,
        pages: [],
        search: function (el, i) {
            // 过滤模糊查询好友
            var reg = new RegExp(vm_friends_list.search_val, 'gi');
            var result = reg.test(el.nick_name);
            if (i == 0) {
                vm_friends_list.count = 0;
            }
            if (result) {
                vm_friends_list.count += 1;
            }
            //$('#friend_num').text(vm.count);
            return result;
        },
        submit:function(user_ids){
            var data ={"user_ids":user_ids,"room_id":roomId};
            $.ajax({
                url:APP_ROOT+"/mapi/index.php?ctl=video&act=private_push_user&itype=lib",
                type:"POST",
                data:data,
                dataType:"json",
                success:function(result){
                    if(result.status == 1){
                        $.showToast(result.error);
                    }
                    else{
                        $.showErr(result.error);
                        return false;
                    }
                }
            });
        },
        current: function (i) {
            vm_friends_list.page = i;
        },
        prev: function () {
            if (vm_friends_list.page > 1) {
                vm_friends_list.page -= 1;
            }
        },
        next: function () {

            if (vm_friends_list.page < vm_friends_list.pages.length) {
                vm_friends_list.page += 1;
            }
        }
    });
    console.log(vm_friends_list.items.length);
    function reSize() {
        var l = Math.ceil(vm_friends_list.items.length / vm_friends_list.page_size);
        vm_friends_list.pages = [];
        for (var i = 1; i <= l; i++) {
            vm_friends_list.pages.push(i);
        }
    }

    reSize();
    vm_friends_list.$watch("items.length", function () {
        reSize();
    });
    vm_friends_list.$watch("page", function () {
        if (vm_friends_list.search_val) {
            vm_friends_list.begin = 0;
        } else {
            vm_friends_list.begin = (vm_friends_list.page - 1) * vm_friends_list.page_size;
        }
    });
}());
