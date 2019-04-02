
//用于未来扩展的提示正确错误的JS

$.showToast = function(str,func)
{
    $.weeboxs.open(str, {boxid:'fanwe-toast-box',contentType:'text',position:'center',showButton:false, showCancel:false, showOk:true,title:'提示',timeout:1,type:'alert',width:200,onclose:func});
};

$.showErr = function(str,func)
{
    $.weeboxs.open(str, {boxid:'fanwe-error-box',contentType:'text',showButton:true, showCancel:false, showOk:true,title:'错误',width:300,onclose:func});
};

$.showSuccess = function(str,func)
{
    $.weeboxs.open(str, {boxid:'fanwe-success-box',contentType:'text',position:'center',showButton:false, showCancel:false, showOk:true,title:'提示',timeout:1,type:'alert',width:200,onclose:func});
};

$.showConfirm = function(str,funcok,funcclose)
{
    var okfunc = function(){
        $.weeboxs.close("fanwe-confirm-box");
        funcok.call();
    };
    $.weeboxs.open(str, {boxid:'fanwe-confirm-box',contentType:'text',showButton:true, showCancel:true, showOk:true,title:'确认',width:300,onclose:funcclose,onok:okfunc});
};

$.showLoading = function(str,func)
{
    var str = str || '';
    str = '<div style="margin:-15px;"><img src="'+TMPL+'/images/loading.gif" /><br>'+str+'</div>' || '<div style="margin:-15px;"><img src="'+TMPL+'/images/loading.gif" /></div>';
    $.weeboxs.open(str, {boxid:'fanwe-loading-box',contentType:'text',position:'center',showButton:false, showCancel:false, showOk:true,title:'提示',type:'alert',width:160, onclose:func});
};

$.hideLoading =  function()
{
    $.weeboxs.close("fanwe-loading-box");
}

// 验证
$.minLength = function(value, length , isByte) {
    var strLength = $.trim(value).length;
    if(isByte)
        strLength = $.getStringLength(value);
        
    return strLength >= length;
};
$.maxLength = function(value, length , isByte) {
    var strLength = $.trim(value).length;
    if(isByte)
        strLength = $.getStringLength(value);
        
    return strLength <= length;
};
$.getStringLength=function(str)
{
    str = $.trim(str);
    if(str=="")
        return 0; 
        
    var length=0; 
    for(var i=0;i <str.length;i++) 
    { 
        if(str.charCodeAt(i)>255)
            length+=2; 
        else
            length++; 
    }
    return length;
};
$.checkMobilePhone = function(value){
    if($.trim(value)!='')
        return /^\d{6,}$/i.test($.trim(value));
    else
        return true;
};
$.checkEmail = function(val){
    var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
    return reg.test(val);
};

/**
 * 判断变量是否空值
 * undefined, null, '', false, 0, [], {} 均返回true，否则返回false
 */
$.checkEmpty = function(val){
    switch (typeof val){
        case 'undefined' : return true;
        case 'string'    : if($.trim(val).length == 0) return true; break;
        case 'boolean'   : if(!val) return true; break;
        case 'number'    : if(0 === val) return true; break;
        case 'object'    :
            if(null === val) return true;
            if(undefined !== val.length && val.length==0) return true;
            for(var k in val){return false;} return true;
            break;
    }
    return false;
}

// 限制只能输入金额
function amount(th){
    var regStrs = [
        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
    ];
    for(i=0; i<regStrs.length; i++){
        var reg = new RegExp(regStrs[i][0]);
        th.value = th.value.replace(reg, regStrs[i][1]);
    }
}

/*
    下拉刷新
    url：刷新请求数据接口链接
    page：当前刷新的页面ID
    cls：刷新内容层的class
    content：当前触发刷新层class
    callback：执行回调
*/
function refresh(url,page,cls,content,callback){
    var refreshing = false;
    if (refreshing) return;
    refreshing =true;
    var query = new Object();
    query.p  =  1;
    query.page_size = 10;
    $.ajax({
        url:url,
        type:"post",
        data:query,
        dataType:"html",
        success:function(result){
            refreshing = false;
            var tplElement = $('<div id="tmpHTML"></div>').html(result),
            htmlObject = tplElement.find("#"+page).find(cls),
            html = $(htmlObject).html(),
            html_list_length = $(htmlObject).find(".block-good-virtual").length,
            value = html.replace(/\s+/g,"");
            $("#"+page).find(cls).html(value.length > 0 ? html : '<div style="text-align:center;color:#999;font-size:0.75rem;">暂无数据</div>');
            html_list_length >= 10 ? document.querySelector(".m-infinite-scroll-preloader").innerHTML = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>' : document.querySelector(".m-infinite-scroll-preloader").innerHTML = '';
            $.pullToRefreshDone(content);
            if(callback){
                callback.call(this);
            }
        }
    });
}
//判断是否为整数
$.checkint=function isInteger(obj) {
    return obj%1 === 0
}
// ajax公用封装
var handleAjax;
if (!handleAjax) handleAjax = {};
(function (h) {
    h.ajax = function(url, param, dataType){
        // 利用了jquery延迟对象回调的方式对ajax封装，使用done()，fail()，always()等方法进行链式回调操作
        // 如果需要的参数更多，比如有跨域dataType需要设置为'jsonp'等等，也可以不做这一层封装，还是根据工程实际情况判断吧，重要的还是链式回调
        dataType ? dataType = dataType : dataType = '';
        if(dataType == "html"){
            param ? param = param : param = '';
        }
        else{
            param ? param = $.extend(param, {itype:'app'}) : param = {itype:'app'};
        }
        return $.ajax({
            url: url,
            data: param || {},
            type: 'POST',
            dataType: dataType || 'json',
            beforeSend:function(){
                $.showLoading();
            }
        });
    };
    h.handle=function(url, param, dataType){
        return h.ajax(url, param, dataType).then(function(result){
            // 成功回调
            $.hideLoading();
            dataType ? dataType = dataType : dataType = '';
            if(dataType == 'html'){
                return result;
                // 直接返回要处理的数据，作为默认参数传入之后done()方法的回调

            }
            else{
                if(result.status == 1){
                    if(result.error)
                        return result.error;
                    else
                        return '操作成功';
                    // 直接返回要处理的数据，作为默认参数传入之后done()方法的回调
                }
                else{
                    return $.Deferred().reject(result.error ? result.error : "操作失败"); // 返回一个失败状态的deferred对象，把错误代码作为默认参数传入之后fail()方法的回调
                }
            }
        }, function(err){
            // 失败回调
            // $.hideIndicator();
            $.showErr("请求失败，请检查网络");
            // console.log(err.status); // 打印状态码
        });
    };
})(handleAjax);

// 倒计时
var left_time = function(left_time,element){
    if(left_time){
        var i = setInterval(function() {
            if(left_time){
                var day  =  parseInt(left_time / 24 /3600);
                var hour = parseInt((left_time % (24 *3600)) / 3600);
                var min = parseInt((left_time % 3600) / 60);
                var sec = parseInt((left_time % 3600) % 60);
                var cc = document.getElementById(element);
                $(element).html(day+"天"+hour+"时"+min+"分"+sec+"秒");
                left_time--;
            }
            else{
                clearInterval(i);
                $.showPreloader();
                window.location.reload();
            }
        }, 1000);
        document.addEventListener("visibilitychange", function (e) {
            clearInterval(i);
            $(element).html("--天--时--分--秒");
        }, false);
    }
};

// 获取地址栏参数
function GetQueryString(name) {
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

// 发送手机验证码
function send_mobile_verify_sms_custom(type,mobile,verify_name){
    var sajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=send_change_mobile_verify_code";
    var squery = new Object();
    if(type!=2){
        if($.trim(mobile).length == 0)
        {           
            $.showErr("手机号码不能为空");
            return false;
        }
        if(!$.checkMobilePhone(mobile))
        {
            $.showErr("手机号码格式错误");
            return false;
        }
            if(!$.maxLength(mobile,11,true))
        {
            $.showErr("长度不能超过11位");
            return false;
        }
        squery.mobile = $.trim(mobile);
    }
    squery.step =type;
    $.ajax({ 
        url: sajaxurl,
        data:squery,
        type: "POST",
        dataType: "json",
        success: function(sdata){
            if(sdata.status==1)
            {
                code_lefttime = 60;
                code_lefttime_func_custom(type,mobile,verify_name,'mobile');
                $.showSuccess(sdata.info);
                return false;
            }
            else
            {
                $.showErr(sdata.info);
                return false;
            }
        }
    }); 
    
}

// 发送邮箱验证码
function send_email_verify(type,email,verify_name){
    var sajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=send_email_verify_code";
    var squery = new Object();
    if(type!=2){
        if($.trim(email).length == 0)
        {           
            $.showErr("邮箱不能为空");
            return false;
        }
        if(!$.checkEmail(email))
        {
            $.showErr("邮箱格式错误");
            return false;
        }
        squery.email = $.trim(email);
    }
    squery.step =type;
    $.ajax({ 
        url: sajaxurl,
        data:squery,
        type: "POST",
        dataType: "json",
        success: function(sdata){
        if(sdata.status==1)
            {
                code_lefttime = 60;
                code_lefttime_func_custom(type,email,verify_name,'email');
                $.showSuccess(sdata.info);
                return false;
            }
            else
            {
                $.showErr(sdata.info);
                return false;
            }
        }
    }); 
    
}

// 重新发送验证码
function code_lefttime_func_custom(type,mobile,verify_name,fun_name){
    clearTimeout(code_timeer);
    $(verify_name).val(code_lefttime+"秒后重新发送");
    $(verify_name).addClass("ui-button-sms-activer").removeClass("bg_red").removeClass("bg_red1");
    code_lefttime--;
    if(code_lefttime >0){
        $(verify_name).attr("disabled","disabled");
        code_timeer = setTimeout(function(){code_lefttime_func_custom(type,mobile,verify_name);},1000);
    }
    else{
        code_lefttime = 60;
        $(verify_name).removeAttr("disabled");
        $(verify_name).val("发送验证码");
        $(verify_name).css("color","#fff");
        $(verify_name).addClass("bg_red").removeClass("ui-button-sms-activer");
        $(verify_name).bind("click",function(){
            if(fun_name=='mobile'){
                send_mobile_verify_sms_custom(type,mobile,verify_name);
            }else{
                if(fun_name=='email'){
                    send_email_verify(type,mobile,verify_name);
                }
                
            }
            
        });
    }
    
}

// 裁剪图片
function open_avatar_view(w,h,obj,callbackFuc){
    attr_id = obj;
    var callbackFuc = callbackFuc || null;
    var has_callback;
    callbackFuc!=null ? has_callback=1 : has_callback=0;
    $.weeboxs.open(APP_ROOT+"/index.php?ctl=ajax&act=upload_img&tmpl_pre_dir=inc&w="+w+"&h="+h+"&dst="+attr_id+"&has_callback="+has_callback, {boxid:'avatar-box',contentType:'ajax',showButton:false, showCancel:false, showOk:false,title:'上传图片',width:880, onopen:function(){if(callbackFuc!=null){callbackFuc.call(this);}}});
}

/**
* 获取url中参数值
* @name    getQueryString
* @param   {String}    url中的参数
* @return  {String}    返回参数值
*/
function getQueryString(key){
    var reg = new RegExp("(^|&)"+key+"=([^&]*)(&|$)");
    var result = window.location.search.substr(1).match(reg);
    return result?decodeURIComponent(result[2]):null;
}