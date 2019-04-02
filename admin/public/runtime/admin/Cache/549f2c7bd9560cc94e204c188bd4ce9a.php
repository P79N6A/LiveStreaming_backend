<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo app_conf("SITE_NAME");?><?php echo l("ADMIN_PLATFORM");?></title>
<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type="text/javascript">
	var version = '<?php echo app_conf("DB_VERSION");?>';
</script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/main.css" />
	<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>

</head>

<body>


<!--
	新版开始
-->
<div class="main">
	<div class="main_title">
		<!--<?php echo conf("APP_NAME");?><?php echo l("ADMIN_PLATFORM");?> <?php echo L("HOME");?>-->
		<table width=100%>
			<tr>
				<td align=left><?php echo ($greet); ?>，<?php echo ($adm_session["adm_name"]); ?>！<?php echo L("APP_VERSION");?>:<?php echo conf("DB_VERSION");?></td>
				<td align=right>你的最后登录时间为:<?php echo ($adm_session["login_time"]); ?><?php echo ($login_time); ?></td>
			</tr>
		</table>
	</div>
	<div class="notify_box">
		<table>
			<tr>
				<!--<td class="statbox tuan_box">
					<table>
						<tr>
							<th>会员审核</th>
						</tr>
						<tr>
							<td>
								<div class="row">
					                <span class="t">个人会员待审核：</span>
									<span class="bx"><a href="<?php echo u("UserAudit/index");?>" <?php if($register_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($register_count); ?></a></span>
									<div class="blank0"></div>
								</div>
								&lt;!&ndash;<div class="row">
					                <span class="t">企业会员审核：</span>
									<span class="bx"><a href="<?php echo u("UserBusinessAudit/index");?>" <?php if($company_register_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($company_register_count); ?></a></span>
									<div class="blank0"></div>
								</div>&ndash;&gt;
							</td>
						</tr>
					</table>
				</td>-->
				<td class="statbox tuan_box" style="padding:12px 10px 0">
					<table>
						<tr>
							<th>会员认证审核</th>
						</tr>
						<tr>
							<td>
								<div class="row">
					                <span class="t">个人会员认证待审核：</span>
									<span class="bx"><a href="<?php echo u("UserInvestor/index");?>" <?php if($user_authentica == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($user_authentica); ?></a></span>
									<div class="blank0"></div>
								</div>
								<!--<div class="row">
					                <span class="t">企业会员认证：</span>
									<span class="bx"><a href="<?php echo u("UserBusinessInvestor/index");?>" <?php if($business_authentica == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($business_authentica); ?></a></span>
									<div class="blank0"></div>
								</div>-->
								<div class="row">
					                <span class="t">个人认证审核未通过：</span>
									<span class="bx"><a href="<?php echo u("UserInvestorList/index");?>" <?php if($authentication_not_allow == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($authentication_not_allow); ?></a></span>
									<div class="blank0"></div>
								</div>
							</td>
						</tr>
					</table>
				</td>
				<td class="statbox tuan_box">
					<table>
                        <tr>
                            <th>充值提现</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">充值单：</span>
                                    <span class="bx">
                                        <a href="<?php echo u("RechargeNotice/index");?>" <?php if($pay_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($pay_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">提现待审核：</span>
                                    <span class="bx"><a href="<?php echo u('UserRefundList/index', array('is_pay'=>0));?>" <?php if($carry_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($carry_count); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">提现待确认：</span>
                                    <span class="bx">
                                        <a href="<?php echo u('UserRefundList/index', array('is_pay'=>1));?>" <?php if($waitpay_carry_count == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($waitpay_carry_count); ?></a>
                                    </span>
                                    <div class="blank0"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
				</td>


			</tr>
			<tr>
                <td align="center"  colspan ="4">
                    <div style="font-size:13px; background-color:#f8f9fb; height:40px;line-height: 40px; ">
                        <span class="bx"><?php echo to_date(get_gmtime()); ?></span> 数据实时指标
                    </div>

                </td>
            </tr>
            <tr>

                <td class="statbox tuan_box">
                    <table>
                        <tr>
                            <th>会员统计</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">普通用户：</span>
                                    <span class="bx"><a href="<?php echo u("UserGeneral/index");?>" <?php if($user_level == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($user_level); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                               <!--<div class="row">
                                    <span class="t">企业会员：</span>
                                    <span class="bx"><a href="<?php echo u("UserBusiness/index");?>" <?php if($authentication == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($authentication); ?></a></span>
                                    <div class="blank0"></div>
                                </div>-->
                                <div class="row">
                                    <span class="t">认证用户：</span>
                                    <span class="bx"><a href="<?php echo u("User/index");?>" <?php if($authentication == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($authentication); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">机器人：</span>
                                    <span class="bx"><a href="<?php echo u("UserRobot/index");?>" <?php if($robot == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($robot); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">会员总数：</span>
                                    <span class="bx"><a href="<?php echo u("UserGeneral/index");?>" <?php if($user_count-$robot == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($user_count-$robot); ?></a></span>
                                    <div class="blank0"></div>
                                </div>

                            </td>
                        </tr>
                    </table>
                </td>
                <td class="statbox event_box" style="padding: 12px 10px 0;">
                    <table>
                        <tr>
                            <th>直播统计</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <span class="t">直播中视频：</span><span class="bx"><a href="<?php echo u("Video/online_index");?>" <?php if($is_live == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($is_live); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">结束已保存视频：</span><span class="bx"><a href="<?php echo u("VideoPlayback/playback_index");?>" <?php if($is_playback == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($is_playback); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">直播中视频观看总人数：</span><span class="bx"><a href="<?php echo u("Video/online_index");?>" <?php if($watch_number == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($watch_number); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                                <div class="row">
                                    <span class="t">实际在线人数：</span><span class="bx"><a href="javascript:void(0);" <?php if($online_user == 0): ?>style="color:#000000;"<?php endif; ?>><?php echo ($online_user); ?></a></span>
                                    <div class="blank0"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
		</table>
	</div>
	<div class="blank5"></div>

    <script type="text/javascript">
	var nav_json_data = JSON.parse('<?php echo json_encode($navs); ?>');
	loadModule();
	$("#J_nav").change(function(){
		loadModule();
	});
	$("#J_m").change(function(){
		loadAction();
	});
	function loadModule(){
		var nav =$("#J_nav").val();
		var html = "";
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					html += '<option value="'+ii+'">'+vv.name+'</option>';
				});
			}
		});

		$("#J_m").html(html);
		loadAction();
	}

	function loadAction(){
		var nav =$("#J_nav").val();
		var m =  $("#J_m").val();
		var a_html = '<option value="">请选择</option>';
		$.each(nav_json_data,function(i,v){
			if(i==nav){
				$.each(v.groups,function(ii,vv){
					if(ii==m){
						$.each(vv.nodes,function(iii,vvv){
							a_html += '<option value="'+vvv.action+'" module="'+vvv.module+'">'+vvv.name+'</option>';
						});
					}
				});
			}
		});

		$("#J_a").html(a_html);
	}

	$("#J_a").change(function(){
		if($.trim($(this).val())!=""){
			location.href = ROOT + '?m='+$(this).find("option:selected").attr("module")+'&a='+$(this).val();
		}
	})
</script>
</body>
</html>