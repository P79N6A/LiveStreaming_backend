<?php
return array(
	"index"	=>	array(
		"name"	=>	"系统首页",
		"key"	=>	"index",
		"groups"	=>	array(
			"index"	=>	array(
				"name"	=>	"系统首页",
				"key"	=>	"index",
				"nodes"	=>	array(
					array("name"=>"快速导航","module"=>"Index","action"=>"main"),
					array("name"=>"网站数据统计","module"=>"Indexs","action"=>"statistics"),
				),
			),
			"syslog"	=>	array(
				"name"	=>	"系统日志",
				"key"	=>	"syslog",
				"nodes"	=>	array(
					array("name"=>"系统日志列表","module"=>"Log","action"=>"index"),
				),
			),

		),
	),
 	"user"	=>	array(
			"name"	=>	"主播管理",
			"key"	=>	"user",
			"groups"	=>	array(
				"user"	=>	array(
					"name"	=>	"主播管理",
					"key"	=>	"user",
					"nodes"	=>	array(
						array("name"=>"主播列表","module"=>"UserGeneral","action"=>"index"),
                        array("name"=>"机器人头像","module"=>"UserRobot","action"=>"index"),
					),
				),
				"useraudit"	=>	array(
					"name"	=>	"无效主播",
					"key"	=>	"useraudit",
					"nodes"	=>	array(
						array("name"=>"无效主播","module"=>"UserAudit","action"=>"index"),
					),
				),
				"usercert"	=>	array(
					"name"	=>	"认证管理",
					"key"	=>	"usercert",
					"nodes"	=>	array(
						array("name"=>"主播待审认证","module"=>"UserInvestor","action"=>"index"),
						array("name"=>"认证未通过","module"=>"UserInvestorList","action"=>"index"),
                        array("name"=>"认证名称列表","module"=>"AuthentList","action"=>"index"),
					),
				),
				"userlevel"	=>	array(
					"name"	=>	"等级管理",
					"key"	=>	"userlevel",
					"nodes"	=>	array(
						array("name"=>"等级列表","module"=>"UserLevel","action"=>"index"),
					),
				),
				"family"	=>	array(
					"name"	=>	"家族管理",
					"key"	=>	"family",
					"nodes"	=>	array(
						array("name"=>"家族列表","module"=>"Family","action"=>"index"),
						array("name"=>"家族等级列表","module"=>"FamilyLevel","action"=>"index"),
					),
				),
			),
	),
	
	"dealcate"	=>	array(
			"name"	=>	"视频管理",
			"key"	=>	"dealcate",
			"groups"	=>	array(
					"videocate"	=>	array(
							"name"	=>	"分类管理",
							"key"	=>	"dealcate",
							"nodes"	=>	array(
								array("name"=>"话题列表","module"=>"VideoCate","action"=>"index"),
                                array("name"=>"分类列表","module"=>"VideoClassified","action"=>"index"),
  							),
					),
 					"video"	=>	array(
							"name"	=>	"视频管理",
							"key"	=>	"dealorder",
							"nodes"	=>	array(
									array("name"=>"直播中视频","module"=>"Video","action"=>"online_index"),
                                    array("name"=>"监控","module"=>"VideoMonitor","action"=>"monitor"),
                                    array("name"=>"警告内容列表","module"=>"WarningMsg","action"=>"index"),
 									array("name"=>"直播结束视频","module"=>"VideoEnd","action"=>"endline_index"),
 									array("name"=>"回播列表","module"=>"VideoPlayback","action"=>"playback_index"),
 									array("name"=>"审核视频列表","module"=>"VideoCheck","action"=>"playback_index"),
                                    array("name"=>"推送消息列表","module"=>"PushAnchor","action"=>"index")
							),
					),


			),
	),
	"score_mall"	=>	array(
		"name"	=>	"道具管理",
		"key"	=>	"score_mall",
		"groups"	=>	array(
			"score_mall"	=>	array(
				"name"	=>	"道具管理",
				"key"	=>	"score_mall",
				"nodes"	=>	array(
					array("name"=>"道具列表","module"=>"Prop","action"=>"index"),
				),
			),

		),
	),
	"payment"	=>	array(
			"name"	=>	"资金管理",
			"key"	=>	"payment",
			"groups"	=>	array(
					"payment"	=>	array(
							"name"	=>	"支付接口",
							"key"	=>	"payment",
							"nodes"	=>	array(
									array("name"=>"支付接口列表","module"=>"Payment","action"=>"index"),
   							),
					),
					"recharge"	=>	array(
						"name"	=>	"充值管理",
						"key"	=>	"recharge",
						"nodes"	=>	array(
							array("name"=>"在线充值","module"=>"RechargeNotice","action"=>"index"),
						),
					),
				"cash"	=>	array(
					"name"	=>	"提现管理",
					"key"	=>	"cash",
					"nodes"	=>	array(
						array("name"=>"提现列表","module"=>"UserRefundList","action"=>"index"),
                        array("name"=>"家族提现列表","module"=>"FamilyRefundList","action"=>"index"),
						array("name"=>"提现待审核记录","module"=>"UserRefund","action"=>"index"),
						array("name"=>"提现待确认记录","module"=>"UserConfirmRefund","action"=>"index"),
					),
				),
			),
	),
	
    "tipoff"	=>	array(
        "name"	=>	"举报管理",
        "key"	=>	"tipoff",
        "groups"	=>	array(
            "payment"	=>	array(
                "name"	=>	"举报管理",
                "key"	=>	"tipoff",
                "nodes"	=>	array(
                    array("name"=>"举报类型列表","module"=>"TipoffType","action"=>"index"),
                    array("name"=>"举报列表","module"=>"Tipoff","action"=>"index"),
                ),
            ),
        )
    ),
	"nav"	=>	array(
			"name"	=>	"文章管理",
			"key"	=>	"nav",
			"groups"	=>	array(
				"articlecate"	=>	array(
							"name"	=>	"关于我们",
							"key"	=>	"articlecate",
							"nodes"	=>	array(
									array("name"=>"分类管理列表","module"=>"ArticleCate","action"=>"index"),
									array("name"=>"分类管理回收站","module"=>"ArticleCateTrash","action"=>"trash"),
									array("name"=>"文章管理列表","module"=>"Article","action"=>"index"),
									array("name"=>"文章管理回收站","module"=>"ArticleTrash","action"=>"trash"),
 							),
					),
				"help"	=>	array(
							"name"	=>	"帮助与反馈",
							"key"	=>	"help",
							"nodes"	=>	array(
									array("name"=>"常见问题","module"=>"Faq","action"=>"index"),
 							),
					),

			),
	),
	"msgtemplate"	=>	array(
			"name"	=>	"短信管理",
			"key"	=>	"msgtemplate",
			"groups"	=>	array(
					"sms"	=>	array(
							"name"	=>	"短信管理",
							"key"	=>	"sms",
							"nodes"	=>	array(
									array("name"=>"短信接口列表","module"=>"Sms","action"=>"index","action_id"=>"58"),
 							),
					),
					"stationmessage"	=>	array(
							"name"	=>	"系统消息管理",
							"key"	=>	"StationMessage",
							"nodes"	=>	array(
 									array("name"=>"系统消息列表","module"=>"StationMessage","action"=>"index"),//LS
 							),
					),
					"dealmsgList"	=>	array(
							"name"	=>	"队列管理",
							"key"	=>	"dealmsgList",
							"nodes"	=>	array(
									array("name"=>"业务队列列表","module"=>"DealMsgList","action"=>"index"),
							),
					),


			),
	),
	"system"	=>	array(
		"name"	=>	"系统设置",
		"key"	=>	"system",
		"groups"	=>	array(
			"sysconf"	=>	array(
				"name"	=>	"系统设置",
				"key"	=>	"sysconf",
				"nodes"	=>	array(
					array("name"=>"系统配置","module"=>"Conf","action"=>"index"),
					array("name"=>"广告设置","module"=>"IndexImage","action"=>"index"),
                    array("name"=>"兑换规则","module"=>"ExchangeRule","action"=>"index"),
                    array("name"=>"购买规则","module"=>"RechargeRule","action"=>"index"),
 				),
			),
		 	"ads"	=>	array(
				"name"	=>	"广告配置",
				"key"	=>	"ads",
				"nodes"	=>	array(
					array("name"=>"广告列表","module"=>"Ad","action"=>"index"),
				),
					),
		 	"mobile"	=>	array(
						"name"	=>	"移动平台设置",
						"key"	=>	"mobile",
						"nodes"	=>	array(
							array("name"=>"手机端配置","module"=>"Conf","action"=>"mobile"),
							array("name"=>"脏字库配置","module"=>"Conf","action"=>"dirty_words"),
                            array("name"=>"昵称限制配置","module"=>"LimitName","action"=>"index"),
							array("name"=>"加密KEY配置","module"=>"KeyList","action"=>"index"),
						),
					),
			"admin"	=>	array(
				"name"	=>	"系统管理员",
				"key"	=>	"admin",
				"nodes"	=>	array(
					array("name"=>"管理员分组列表","module"=>"Role","action"=>"index","action_id"=>"11"),
					array("name"=>"管理员分组回收站","module"=>"RoleTrash","action"=>"trash","action_id"=>"13"),
					array("name"=>"管理员列表","module"=>"Admin","action"=>"index","action_id"=>"14"),
					array("name"=>"管理员回收站","module"=>"AdminTrash","action"=>"trash","action_id"=>"15"),
				),
			),
			"slbgroupconf"	=>	array(
				"name"	=>	"集群组配置",
				"key"	=>	"slbgroupconf",
				"nodes"	=>	array(
					array("name"=>"集群组列表","module"=>"SlbGroup","action"=>"index",),
				),
			),
			"lucknum"	=>	array(
				"name"	=>	"靓号管理",
				"key"	=>	"lucknum",
				"nodes"	=>	array(
					array("name"=>"靓号管理","module"=>"LuckNum","action"=>"index",),
				),
			),
		),
	),
);
?>