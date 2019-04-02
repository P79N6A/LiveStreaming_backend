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
					array("name"=>"快速导航","module"=>"Index","action"=>"main_wawa"),
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
			"name"	=>	"用户管理",
			"key"	=>	"user",
			"groups"	=>	array(
				"user"	=>	array(
					"name"	=>	"用户管理",
					"key"	=>	"user",
					"nodes"	=>	array(
						array("name"=>"用户列表","module"=>"UserGeneral","action"=>"index_wawa"),
                        array("name"=>"机器人头像","module"=>"UserRobot","action"=>"index"),
                        /*array("name"=>"私信收礼统计","module"=>"UserStatistics","action"=>"private_statistics"),
                        array("name"=>"主播观看统计","module"=>"UserGuard","action"=>"index"),*/
					),
				),
				"useraudit"	=>	array(
					"name"	=>	"无效主播",
					"key"	=>	"useraudit",
					"nodes"	=>	array(
						array("name"=>"无效主播","module"=>"UserAudit","action"=>"index"),
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
                "society"	=>	array(
                    "name"	=>	"公会管理",
                    "key"	=>	"society",
                    "nodes"	=>	array(
                        array("name"=>"公会列表","module"=>"Society","action"=>"index"),
                        array("name"=>"公会收益列表","module"=>"SocietyIncome","action"=>"index"),
                        array("name"=>"公会等级列表","module"=>"SocietyLevel","action"=>"index"),
                    ),
                ),
				"distribution"	=>	array(
					"name"	=>	"分销管理",
					"key"	=>	"distribution",
					"nodes"	=>	array(
						array("name"=>"分销列表","module"=>"Distribution","action"=>"index"),
					),
				),
				"wx_distribution"	=>	array(
					"name"	=>	"微信分销",
					"key"	=>	"wx_distribution",
					"nodes"	=>	array(
						array("name"=>"顶级分销商","module"=>"Wx_distribution","action"=>"index"),
					),
				),
				"tg_distribution"	=>	array(
					"name"	=>	"游戏分销",
					"key"	=>	"tg_distribution",
					"nodes"	=>	array(
						array("name"=>"顶级分销商","module"=>"Tg_distribution","action"=>"index"),
					),
				),
				"sign_in"	=>	array(
							"name"	=>	"签到管理",
							"key"	=>	"sign_in",
							"nodes"	=>	array(
									array("name"=>"签到配置","module"=>"DollSignIn","action"=>"index"),
									array("name"=>"签到日志","module"=>"DollSignIn","action"=>"user_list"),
						),
				),	
				"invite"	=>	array(
							"name"	=>	"邀请管理",
							"key"	=>	"invite",
							"nodes"	=>	array(
									array("name"=>"邀请记录列表","module"=>"UserDollList","action"=>"invite_record"),
						),
				),
			),
	),
	
	"wawa"	=>	array(
			"name"	=>	"娃娃管理",
			"key"	=>	"wawa",
			"groups"	=>	array(
				"dolls"	=>	array(
					"name"	=>	"娃娃机管理",
					"key"	=>	"dolls",
					"nodes"	=>	array(
						array("name"=>"娃娃机列表","module"=>"Dolls","action"=>"index"),
						array("name"=>"错误码描述列表","module"=>"Dolls","action"=>"err_code"),
					),
				),
				"doll_order"	=>	array(
					"name"	=>	"订单管理",
					"key"	=>	"doll_order",
					"nodes"	=>	array(
						array("name"=>"所有订单","module"=>"UserDollList","action"=>"all"),
						array("name"=>"未领取订单","module"=>"UserDollList","action"=>"unget"),
						array("name"=>"待发货订单","module"=>"UserDollList","action"=>"index"),
						array("name"=>"已领取订单","module"=>"UserDollList","action"=>"arrived"),
						array("name"=>"已兑换订单","module"=>"UserDollList","action"=>"exchanged"),
						array("name"=>"已关闭订单","module"=>"UserDollList","action"=>"closed")
					),
				),
				"activelist"	=>	array(
					"name"	=>	"发现管理",
					"key"	=>	"activelist",
					"nodes"	=>	array(
						array("name"=>"发现列表","module"=>"ActiveList","action"=>"index"),
					),
				),
				"dollcate"	=>	array(
						"name"	=>	"娃娃管理",
						"key"	=>	"dollcate",
						"nodes"	=>	array(
								array("name"=>"娃娃列表","module"=>"DollCate","action"=>"index"),
								array("name"=>"游戏记录列表","module"=>"UserDollList","action"=>"game_record"),
								array("name"=>"运费列表","module"=>"DollFreight","action"=>"index"),
						),
				),
				"videocate"	=>	array(
						"name"	=>	"分类管理",
						"key"	=>	"dealcate",
						"nodes"	=>	array(
							//array("name"=>"话题列表","module"=>"VideoCate","action"=>"index"),
                            array("name"=>"分类列表","module"=>"VideoClassified","action"=>"index"),
							),
				),
				"reserve" =>  array(
			            "name"  =>  "预约管理",
			            "key"   =>  "reserve",
			            "nodes" =>  array(
			                array("name"=>"预约列表","module"=>"ReserveManage","action"=>"index"),
			                ),
			    ),
				"dollquestion" =>  array(
			            "name"  =>  "问题管理",
			            "key"   =>  "dollquestion",
			            "nodes" =>  array(
			                	array("name"=>"问题分类列表","module"=>"DollQuestion","action"=>"index"),
			                	array("name"=>"问题反馈列表","module"=>"DollQuestionLog","action"=>"index"),
			                ),
			    ),
			    "exchangescore" =>  array(
			            "name"  =>  "兑换管理",
			            "key"   =>  "exchangescore",
			            "nodes" =>  array(
			                	array("name"=>"兑换券列表","module"=>"ExchangeScore","action"=>"index"),
			                	array("name"=>"兑换记录列表","module"=>"ExchangeScoreLog","action"=>"index"),
			                	array("name"=>"兑换分类列表","module"=>"DollExchangeCate","action"=>"index"),
			                	array("name"=>"兑换实物配置","module"=>"DollExchangeThing","action"=>"index"),
			                	array("name"=>"兑换实物订单","module"=>"DollUserThing","action"=>"all"),
			                ),
			    ),
			    "scorebox" =>  array(
			            "name"  =>  "积分宝箱管理",
			            "key"   =>  "scorebox",
			            "nodes" =>  array(
			                	array("name"=>"积分宝箱配置","module"=>"ScoreBox","action"=>"index"),
			                	array("name"=>"积分宝箱记录","module"=>"ScoreBoxLog","action"=>"index"),
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
					"statistics"	=>	array(
						"name"	=>	"统计管理",
						"key"	=>	"statistics",
						"nodes"	=>	array(
							array("name"=>"统计图表","module"=>"StatisticsModule","action"=>"chart"),
							array("name"=>"充值统计","module"=>"StatisticsModule","action"=>"statistics_recharge"),
						),
					),
			),
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
                    array("name"=>"购买规则","module"=>"RechargeRule","action"=>"index"),
                    //array("name"=>"VIP购买规则","module"=>"VipRule","action"=>"index"),
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
			"express"	=>	array(
				"name"	=>	"快递设置",
				"key"	=>	"lucknum",
				"nodes"	=>	array(
					array("name"=>"查询快递设置","module"=>"Express","action"=>"index",),
				),
			),
		),
	),
	
);
?>