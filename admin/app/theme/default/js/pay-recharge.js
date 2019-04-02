var pay_id = $("input[name='pay_id']").val();
var rule_id = $("input[name='rule_id']").val();
//var data_rules={
//    "pay_id": pay_id,
//    "rule_id":rule_id,
//    "money": "",
//    "ratio": ratio,
//    "other_diamond":0
//};

var vm_pay_recharge = avalon.define({
    $id: "pay_recharge",
    //data: data_rules,
    pay_id: pay_id,
    rule_id: rule_id,
    ratio :ratio,
    money: "",
    other_diamond:0,
    pay_type_checked: function(e) {
        $(".pay_type").removeClass("checked");
        $(e.target).parent().parent(".pay_type").addClass("checked");
    },
    money_checked: function(e) {
        $(".money-label").removeClass("checked");
        $(e.target).parent(".money-label").addClass("checked");
        vm_pay_recharge.money = '';
    },
    is_other_money: function(e) {
        var val = e.target.value;
        if (e.target.value) {
            vm_pay_recharge.other_diamond = Math.floor(val*vm_pay_recharge.ratio);
            $(".money-label").removeClass("checked").find("input[name='rule_id']").removeAttr("checked");
            vm_pay_recharge.rule_id = 0;
        } else {
            $(".money-label").eq(0).addClass("checked").find("input[name='rule_id']").attr("checked", "checked");
            vm_pay_recharge.rule_id = vm_pay_recharge.rule_id;
            vm_pay_recharge.other_diamond=0;
        }
    },
    submit: function() {
        var query = new Object();
        query.pay_id = vm_pay_recharge.pay_id;
        query.rule_id = vm_pay_recharge.rule_id;
        query.money = vm_pay_recharge.money;
        // 充值弹窗
        $.ajax({
            url: APP_ROOT + "/mapi/index.php?ctl=pay&act=pay&itype=app",
            dataType: "json",
            data: query,
            async: false,
            success: function(result) {
                if (result.status == 1) {
                    switch (result.method) {
                        case 'new_tab':
                            window.open(result.url);
                            $.weeboxs.close();
                            break;
                        case 'img':
                            $.weeboxs.open("#pay-qrcode-box", {boxid:'pay-qrcode-box',contentType:'text',position:'center',showButton:false, showCancel:false, showOk:true,title:'微信支付',width:187, onopen: function(){J_qrcode();}});
                            function response(id) {
                                $.ajax({
                                    url:APP_ROOT + "/wxpay_web/response.php?payment_notice_id=" + id,
                                    dataType: "json",
                                    async: false,
                                    success: function(r) {
                                        if (r.status) {
                                            switch (r.info) {
                                                case 'SUCCESS':
                                                    $.showSuccess("支付成功");
                                                    location.reload();return;
                                                    break;
                                                case 'REFUND':
                                                    $.showErr("转入退款");
                                                    location.reload();return;
                                                    break;
                                                case 'NOTPAY':
                                                    // NOTPAY—未支付
                                                    // $.showErr('未支付');
                                                    break;
                                                case 'CLOSED':
                                                    $.showErr("已关闭");
                                                    location.reload();return;
                                                case 'REVOKED':
                                                    $.showErr("已撤销");
                                                    return;
                                                case 'USERPAYING':
                                                    // USERPAYING--用户支付中
                                                    // console.log('用户支付中');
                                                    break;
                                                case 'PAYERROR':
                                                    $.showErr("支付失败");
                                                    return;
                                                default:
                                                    console.log(r);return;
                                            }
                                            setTimeout(function() {
                                                response(id);
                                            }, 1000);
                                        } else {
                                            alert(r.info);
                                        }
                                    }
                                });
                            }
                            function J_qrcode(){
                                var qrcode = new QRCode(document.getElementById("pay-qrcode"), {
                                    width : 147, //设置宽高
                                    height : 147
                                });
                                qrcode.makeCode(result.code_url);
                                response(result.id);
                            }
                            break;
                        default:
                            break;
                    }
                } else {
                    if (result.error) {
                        $.showErr(result.error);
                    } else {
                        $.showErr("操作失败");
                    }
                }
            }
        });
    }
});
avalon.scan(document.getElementById('pay_recharge'));