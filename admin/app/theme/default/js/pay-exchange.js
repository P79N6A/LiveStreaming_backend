$(".m-exchange").slide({titCell: ".hd a", mainCell: ".bd", titOnClassName: "active", trigger: "click", delayTime: 500});
var data_rules = {
    "rule_id": rule_id,
    "ticket": "",
    "ratio": ratio,
    "other_diamonds": 0
};
var vm = avalon.define({
    $id: "pay_exchange",
    data: data_rules,
    pay_type_checked: function (e) {
        $(".pay_type").removeClass("checked");
        $(e.target).parent().parent(".pay_type").addClass("checked");
    },
    money_checked: function (e) {
        $(".money-label").removeClass("checked");
        $(e.target).parent(".money-label").addClass("checked");
        vm.data.ticket = '';
    },
    is_other_ticket: function (e) {
        var val = e.target.value;
        if ($.checkint(val)) {
            if (val) {
                vm.data.other_diamonds = Math.floor(val * vm.data.ratio);
                $(".money-label").removeClass("checked").find("input[name='rule_id']").removeAttr("checked");
                vm.data.rule_id = 0;
            }
            else {
                $(".money-label").eq(0).addClass("checked").find("input[name='rule_id']").attr("checked", "checked");
                vm.data.rule_id = vm.data.rule_id;
                vm.data.other_diamonds = 0;
            }
        } else {
            if (val) {
                vm.data.ticket = Math.round(val);
                vm.data.other_diamonds = Math.floor(vm.data.ticket * vm.data.ratio);
                $(".money-label").removeClass("checked").find("input[name='rule_id']").removeAttr("checked");
                vm.data.rule_id = 0;
            } else {
                $(".money-label").eq(0).addClass("checked").find("input[name='rule_id']").attr("checked", "checked");
                vm.data.rule_id = vm.data.rule_id;
                vm.data.other_diamonds = 0;
            }
        }

    },
    submit: function () {
        if (vm.data.rule_id <= 0 && vm.data.other_diamonds <= 0) {
            $.showErr("兑换秀豆数不能为0");
            return false;
        } else {
            // 兑换
            handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=pay&act=do_exchange", vm.data).done(function (msg) {
                $.showSuccess(msg);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }).fail(function (err) {
                $.showErr(err);
            });
        }
    }
});
avalon.scan(document.getElementById('pay_exchange'));