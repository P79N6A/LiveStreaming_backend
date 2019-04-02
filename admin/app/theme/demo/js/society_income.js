// $(".selection").click(function() {
//     $(this).parent().toggleClass('select2-container--open');
//     $(".select2-search_field").focus();
// });
$(".select2-results_option").mouseover(function() {
    $(this).addClass("highlighted");
});
$(".select2-results_option").mouseleave(function() {
    $(this).removeClass("highlighted");
});
$(".select2-results_option").click(function() {
    $(this).attr("aria-selected", "true").siblings('li').attr("aria-selected", "false");
    var txt = $(this).text();
    var user_id = $(this).attr("data-id");
    $(".select2-selection_rendered").text(txt).attr("data-id", user_id);
    $(".selection").parent().removeClass("select2-container--open");
    $('.select2-search_field').val("");
});
$('.select2-search_field').bind('input propertychange', function() {
    $(".select2-selection_rendered").text($(this).val());
});
$('.selection').click(function(event) {
    //取消事件冒泡  
    event.stopPropagation();
    //按钮的toggle,如果div是可见的,点击按钮切换为隐藏的;如果是隐藏的,切换为可见的。  
    $(this).parent().toggleClass('select2-container--open');
    $(".select2-search_field").focus();
    $(".dropdown-menu").css("display", "none");
});
//点击空白处  
$(document).click(function(event) {
    var _con = $(".selection").parent(); // 设置目标区域
    if (!_con.is(event.target) && _con.has(event.target).length === 0) { // Mark 1
        $(".selection").parent().removeClass("select2-container--open");
    }
});

//时间
var startdate = new Date();
var year = startdate.getFullYear();
var Month = '0' + (startdate.getMonth() + 1);



function getPreMonth(date) {
    var arr = date.split('-');
    var year = arr[0]; //获取当前日期的年份
    var month = arr[1]; //获取当前日期的月份
    var year2 = year;
    var month2 = parseInt(month) - 1;
    if (month2 == 0) { //如果是1月份，则取上一年的12月份
        year2 = parseInt(year2) - 1;
        month2 = 12;
    }
    if (month2 < 10) {
        month2 = '0' + month2; //月份填补成2位。
    }
    var t2 = year2 + '-' + month2;
    return t2;
}
var new_time = year + "-" + Month;
var befor_time1 = (getPreMonth(year + "-" + Month));
var befor_time2 = (getPreMonth(befor_time1));
var befor_time3 = (getPreMonth(befor_time2));
var befor_time4 = (getPreMonth(befor_time3));
var befor_time5 = (getPreMonth(befor_time4));
var befor_time6 = (getPreMonth(befor_time5));
$(".newtime").text(new_time);
$(".beforetime1").text(befor_time1);
$(".beforetime2").text(befor_time2);
$(".beforetime3").text(befor_time3);
$(".beforetime4").text(befor_time4);
$(".beforetime5").text(befor_time5);
$(".beforetime6").text(befor_time6);

//管理主播搜索
$('.J-sign-search').click(function(event) {
    var user_id = $(".anchor-id").val();
    if (user_id) {
        // location.href = APP_ROOT + "/index.php?ctl=society_user&act=user_list&itype=app&user_id=" + user_id;
        var cls = ".ajax-block";
        handleAjax.handle(APP_ROOT + "/index.php?ctl=society_user&act=user_list&itype=app",{user_id:user_id},"html").done(function(result){
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find(cls),
            html = $(htmlObject).html();
            $(document).find(cls).html(html);
        }).fail(function(err){
            $.toast(err);
        });
    } else {
        $.showErr("请输入主播ID");
    }
});
$('.J-rescind-search').click(function(event) {
    var user_id = $(".anchor-id").val();
    if (user_id) {
        // location.href = APP_ROOT + "/index.php?ctl=society_user&act=rescind_list&itype=app&user_id=" + user_id;
        var cls = ".ajax-block";
        handleAjax.handle(APP_ROOT + "/index.php?ctl=society_user&act=rescind_list&itype=app",{user_id:user_id},"html").done(function(result){
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find(cls),
            html = $(htmlObject).html();
            $(document).find(cls).html(html);
        }).fail(function(err){
            $.toast(err);
        });
    } else {
        $.showErr("请输入主播ID");
    }
});
$('.J-rescind-search').click(function(event) {
    var user_id = $(".anchor-id").val();
    if (user_id) {
        // location.href = APP_ROOT + "/index.php?ctl=society_user&act=signed_list&itype=app&user_id=" + user_id;
         var cls = ".ajax-block";
        handleAjax.handle(APP_ROOT + "/index.php?ctl=society_user&act=rescind_list&itype=app",{user_id:user_id},"html").done(function(result){
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find(cls),
            html = $(htmlObject).html();
            $(document).find(cls).html(html);
        }).fail(function(err){
            $.toast(err);
        });
    } else {
        $.showErr("请输入主播ID");
    }
});

//管理主播-待签约主播
$('.J-agree').click(function(event) {
    var is_agree = 1;
    var r_user_id = $(this).attr("data-id");
    
    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=confirm", { is_agree: is_agree, r_user_id: r_user_id }).done(function(resp) {
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }).fail(function(err) {
        console.log(r_user_id);
    });
});
$('.J-refuse').click(function(event) {
    var is_agree = 2;
    var r_user_id = $(this).attr("data-id");

    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=confirm", { is_agree: is_agree, r_user_id: r_user_id }).done(function(resp) {
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }).fail(function(err) {
        console.log(r_user_id);
    });
});
//管理主播-解约主播
$('.J-relieve-agree').click(function(event) {
    var is_agree = 1;
    var r_user_id = $(this).attr("data-id");
    
    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=logout_confirm", { is_agree: is_agree, r_user_id: r_user_id }).done(function(resp) {
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }).fail(function(err) {
        console.log(r_user_id);
    });
});
$('.J-relieve-refuse').click(function(event) {
    var is_agree = 2;
    var r_user_id = $(this).attr("data-id");

    handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society&act=logout_confirm", { is_agree: is_agree, r_user_id: r_user_id }).done(function(resp) {
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }).fail(function(err) {
        console.log(r_user_id);
    });
});


//收入管理-公会月收入查询
$('.J-month').click(function(event) {
    var date_str = $(".Data option:selected").text();
    // location.href = APP_ROOT + "/index.php?ctl=society_income&act=society_income_month&date_str="+date_str;
    var cls = ".ajax-block";
    handleAjax.handle(APP_ROOT + "/index.php?ctl=society_income&act=society_income_month",{date_str:date_str},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(document).find(cls).html(html);
    }).fail(function(err){
        $.toast(err);
    });
});
//收入管理-公会月收入导出
$('.J-export').click(function(event) {
    var date_str = $(".Data option:selected").text();

    location.href = APP_ROOT + "/index.php?ctl=society_income&act=society_csv&date_str=" + date_str;
});

//收入管理-主播月收入查询
$('.J-anchor_month').click(function(event) {
    var date_str = $(".Data option:selected").text();
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }
    //location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_income_month&date_str="+date_str+"&id="+id;
    var cls = ".ajax-block";
    handleAjax.handle(APP_ROOT + "/index.php?ctl=society_income&act=user_income_month",{date_str:date_str,id:id},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(document).find(cls).html(html);
    }).fail(function(err){
        $.showErr("错误");
    });
});
//收入管理-主播月收入导出
$('.J-anchor-month-export').click(function(event) {
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }
    var date_str = $(".Data option:selected").text();

    location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_month_csv&itype=app&date_str=" + date_str + "&id=" + id;

});

//收入管理-主播收入查询
$('.J-time-user_id').click(function(event) {
    var new_src = $(".form-control").val();
    var six_numb = new_src.split("-");
    if (six_numb[1].length == 1 ) {
        six_numb[1] = '0'+six_numb[1];
    }
    if (six_numb[2].length == 1 ) {
        six_numb[2] = '0'+six_numb[2];
    }
    var date_str = six_numb[0] + '-' + six_numb[1] + '-' + six_numb[2];
    console.log(new_src);
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }
    //location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_income&date_str="+date_str+"&id="+id;
    var cls = ".ajax-block";
    handleAjax.handle(APP_ROOT + "/index.php?ctl=society_income&act=user_income&itype=app",{date_str:date_str,user_id:id},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(document).find(cls).html(html);
    }).fail(function(err){
        $.showErr("错误");
    });
});
//收入管理-主播收入导出
$('.J-time-export').click(function(event) {
    var date_str = $(".form-control").val();
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }

    location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_day_csv&itype=app&date_str=" + date_str + "&id=" + id;

});

//收入管理-主播直播时长查询
$('.J-data-user_id').click(function(event) {
    var date_str = $(".form-control").val();
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }
    // location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_live_length&date_str="+date_str+"&id="+id;
    var cls = ".ajax-block";
    handleAjax.handle(APP_ROOT + "/index.php?ctl=society_income&act=user_live_length",{date_str:date_str,id:id},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(document).find(cls).html(html);
    }).fail(function(err){
        $.showErr("错误");
    });
});
//收入管理-主播直播时长导出
$('.J-data-export').click(function(event) {
    var date_str = $(".form-control").val();
    var inptus_val = $('.select2-search_field').val();
    if (inptus_val) {
        var select_text = $(".select2-selection_rendered").text();
    } else {
        var id = $(".select2-selection_rendered").attr("data-id");
    }

    location.href = APP_ROOT + "/index.php?ctl=society_income&act=user_live_length_csv&itype=app&date_str=" + date_str + "&id=" + id;

    // handleAjax.handle(APP_ROOT + "/mapi/index.php?ctl=society_income&act=user_live_length_csv", { date_str: date_str, id: id }).done(function(resp) {
    //     setTimeout(function(result) {
    //         if (result.status == 1) {

    //         }
    //     }, 1000);
    // }).fail(function(err) {});

});

//违规
$('.J-tipoff-search').click(function(event) {
    var user_id = $(".search_user_id").val();
    if (user_id) {
        location.href = APP_ROOT + "/index.php?ctl=society&act=tipoff_index&itype=app&user_id=" + user_id;
    } else {
        $.showErr("请输入主播ID");
    }
});