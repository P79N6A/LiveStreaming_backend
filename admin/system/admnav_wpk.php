<?php
return array(
    "index"          => array(
        "name"   => "系统首页",
        "key"    => "index",
        "groups" => array(
            "index"  => array(
                "name"  => "系统首页",
                "key"   => "index",
                "nodes" => array(
                    array("name" => "后台概况", "module" => "Index", "action" => "main"),
                ),
            ),
            "syslog" => array(
                "name"  => "系统日志",
                "key"   => "syslog",
                "nodes" => array(
                    array("name" => "系统日志列表", "module" => "Log", "action" => "index"),
                ),
            ),

        ),
    ),
    "user"           => array(
        "name"   => "会员",
        "key"    => "user",
        "groups" => array(
            "user" => array(
                "name"  => "会员管理",
                "key"   => "user",
                "nodes" => array(
                    array("name" => "会员列表", "module" => "UserGeneral", "action" => "index"),
                    array("name" => "会员待审认证", "module" => "UserInvestor", "action" => "index"),
                    array("name" => "会员配置", "module" => "Vip", "action" => "index"),
                    array("name" => "会员兑换码", "module" => "Vip", "action" => "exchange"),
                ),
            ),
        ),
    ),
    "course"     => array(
        "name"   => "课程管理",
        "key"    => "course",
        "groups" => array(
            "courseList" => array(
                "name"  => "余世维说",
                "key"   => "courseList",
                "nodes" => array(
                    array("name" => "课程列表", "module" => "Course", "action" => "index&type=0"),
                ),
            ),
            "managementCourse" => array(
                "name"  => "齐家学堂",
                "key"   => "managementCourse",
                "nodes" => array(
                    array("name" => "课程列表", "module" => "Course", "action" => "index&type=1"),
                ),
            ),
            "Statistics" => array(
                "name"  => "统计",
                "key"   => "Statistics",
                "nodes" => array(
                    array("name" => "观看记录", "module" => "Course", "action" => "pageviewsStatistics"),
                ),
            ),
        ),
    ),
    "leaderdialogue" => array(
        "name"   => "企业特诊",
        "key"    => "leaderdialogue",
        "groups" => array(
            "questionconf" => array(
                "name"  => "线上提问",
                "key"   => "question",
                "nodes" => array(
                    array("name" => "对话单", "module" => "Question", "action" => "index"),
                    array("name" => "近日新增", "module" => "Question", "action" => "day_count"),
                ),
            ),
            "reservation"  => array(
                "name"  => "线下预约",
                "key"   => "reservation",
                "nodes" => array(
                    array("name" => "预约项目", "module" => "Date", "action" => "index"),
                    array("name" => "预约配置", "module" => "ReservationConfig", "action" => "index"),
                ),
            ),
        ),
    ),
    "starsshare"     => array(
        "name"   => "实战分享",
        "key"    => "starsshare",
        "groups" => array(
            "shareconf" => array(
                "name"  => "分享管理",
                "key"   => "share",
                "nodes" => array(
                    array("name" => "文章列表", "module" => "Share", "action" => "index"),
                    array("name" => "文章分类", "module" => "Share", "action" => "cate_list"),
                ),
            ),
        ),
    ),
    "payment"        => array(
        "name"   => "资金管理",
        "key"    => "payment",
        "groups" => array(
            "payment"  => array(
                "name"  => "支付接口",
                "key"   => "payment",
                "nodes" => array(
                    array("name" => "支付接口列表", "module" => "Payment", "action" => "index"),
                ),
            ),
            "recharge" => array(
                "name"  => "充值管理",
                "key"   => "recharge",
                "nodes" => array(
                    array("name" => "在线充值", "module" => "RechargeNotice", "action" => "index"),
                ),
            ),
            "cash"     => array(
                "name"  => "提现管理",
                "key"   => "cash",
                "nodes" => array(
                    array("name" => "提现列表", "module" => "UserRefundList", "action" => "index"),
                    array("name" => "提现待审核记录", "module" => "UserRefund", "action" => "index"),
                    array("name" => "提现待确认记录", "module" => "UserConfirmRefund", "action" => "index"),
                ),
            ),
        ),

    ),
    "nav"            => array(
        "name"   => "文章管理",
        "key"    => "nav",
        "groups" => array(
            "articlecate" => array(
                "name"  => "关于我们",
                "key"   => "articlecate",
                "nodes" => array(
                    array("name" => "分类管理列表", "module" => "ArticleCate", "action" => "index"),
                    array("name" => "分类管理回收站", "module" => "ArticleCateTrash", "action" => "trash"),
                    array("name" => "文章管理列表", "module" => "Article", "action" => "index"),
                    array("name" => "文章管理回收站", "module" => "ArticleTrash", "action" => "trash"),
                ),
            ),
            "help"        => array(
                "name"  => "帮助与反馈",
                "key"   => "help",
                "nodes" => array(
                    array("name" => "常见问题", "module" => "Faq", "action" => "index"),
                ),
            ),
        		"feedback"     => array(
        				"name"  => "反馈列表",
        				"key"   => "feedback",
        				"nodes" => array(
        						array("name" => "用户反馈", "module" => "Feedback", "action" => "index"),
        				),
        		),

        ),
    ),
    "msgtemplate"   =>  array(
        "name"  =>  "短信管理",
        "key"   =>  "msgtemplate",
        "groups"    =>  array(
            "sms"   =>  array(
                "name"  =>  "短信管理",
                "key"   =>  "sms",
                "nodes" =>  array(
                    array("name"=>"短信接口列表","module"=>"Sms","action"=>"index","action_id"=>"58"),
                ),
            ),
            "dealmsgList"   =>  array(
                "name"  =>  "队列管理",
                "key"   =>  "dealmsgList",
                "nodes" =>  array(
                    array("name"=>"业务队列列表","module"=>"DealMsgList","action"=>"index"),
                ),
            ),
        ),
    ),
    "system"         => array(
        "name"   => "系统设置",
        "key"    => "system",
        "groups" => array(
            "sysconf" => array(
                "name"  => "系统设置",
                "key"   => "sysconf",
                "nodes" => array(
                    array("name" => "系统配置", "module" => "Conf", "action" => "index"),
                    array("name" => "广告设置", "module" => "IndexImage", "action" => "index"),
                    //array("name"=>"兑换规则","module"=>"ExchangeRule","action"=>"index"),
                    //array("name"=>"购买规则","module"=>"RechargeRule","action"=>"index"),
                ),
            ),
            "ads"     => array(
                "name"  => "广告配置",
                "key"   => "ads",
                "nodes" => array(
                    array("name" => "广告列表", "module" => "Ad", "action" => "index"),
                    array("name" => "广告区域", "module" => "AdPlace", "action" => "index"),
                ),
            ),
            "mobile"  => array(
                "name"  => "移动平台设置",
                "key"   => "mobile",
                "nodes" => array(
                    array("name" => "手机端配置", "module" => "Conf", "action" => "mobile"),
                    array("name" => "脏字库配置", "module" => "Conf", "action" => "dirty_words"),
                    //array("name"=>"手机端广告列表","module"=>"MAdv","action"=>"index"),
                ),
            ),
            "admin"   => array(
                "name"  => "系统管理员",
                "key"   => "admin",
                "nodes" => array(
                    array("name" => "管理员分组列表", "module" => "Role", "action" => "index", "action_id" => "11"),
                    array("name" => "管理员分组回收站", "module" => "RoleTrash", "action" => "trash", "action_id" => "13"),
                    array("name" => "管理员列表", "module" => "Admin", "action" => "index", "action_id" => "14"),
                    array("name" => "管理员回收站", "module" => "AdminTrash", "action" => "trash", "action_id" => "15"),
                ),
            ),
        ),
    ),
);
