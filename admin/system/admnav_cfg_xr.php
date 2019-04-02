<?php
return array(
    "index" => array(
        "name" => "系统首页",
        "key" => "index",
        "groups" => array(
            "index" => array(
                "name" => "系统首页",
                "key" => "index",
                "nodes" => array(
                    array("name" => "快速导航", "module" => "Index", "action" => "main_weibo"),
                    array("name" => "网站数据统计", "module" => "Index", "action" => "statistics")
                )
            ),
            "syslog" => array(
                "name" => "系统日志",
                "key" => "syslog",
                "nodes" => array(
                    array("name" => "系统日志列表", "module" => "Log", "action" => "index")
                )
            )

        )
    ),
    "user" => array(
        "name" => "主播管理",
        "key" => "user",
        "groups" => array(
            "user" => array(
                "name" => "主播管理",
                "key" => "user",
                "nodes" => array(
                    array("name" => "主播列表", "module" => "WeiboUserGeneral", "action" => "index")
                )
            ),
            "useraudit" => array(
                "name" => "无效主播",
                "key" => "useraudit",
                "nodes" => array(
                    array("name" => "无效主播", "module" => "UserAudit", "action" => "index")
                )
            ),
            "usercert" => array(
                "name" => "认证管理",
                "key" => "usercert",
                "nodes" => array(
                    array("name" => "主播待审认证", "module" => "UserInvestor", "action" => "index"),
                    //array("name"=>"企业待审认证","module"=>"UserBusinessInvestor","action"=>"index"),
                    array("name" => "认证未通过", "module" => "UserInvestorList", "action" => "index"),
                    array("name" => "认证名称列表", "module" => "AuthentList", "action" => "index")
                )
            ),
            "userlevel" => array(
                "name" => "等级管理",
                "key" => "WeiboUserLevel",
                "nodes" => array(
                    array("name" => "等级列表", "module" => "WeiboUserLevel", "action" => "index")
                )
            ),
            "distribution" => array(
                "name" => "分销管理",
                "key" => "distribution",
                "nodes" => array(
                    array("name" => "分销列表", "module" => "Distribution", "action" => "index")
                )
            )

        )
    ),
    "weibo_manage" => array(
        "name" => "动态管理",
        "key" => "weibo_manage",
        "groups" => array(
            "weibo_manage" => array(
                "name" => "动态管理",
                "key" => "weibo_manage",
                "nodes" => array(
                    array("name" => "动态列表", "module" => "WeiboList", "action" => "index"),
                    array("name" => "图文列表", "module" => "WeiboList", "action" => "imagetext"),
                    array("name" => "视频列表", "module" => "WeiboList", "action" => "video"),
                    //array("name"=>"微信列表","module"=>"WeiboList","action"=>"weixin"),
                    array("name" => "写真列表", "module" => "WeiboList", "action" => "photo"),
                    array("name" => "红包图片列表", "module" => "WeiboList", "action" => "red_photo"),
                    array("name" => "虚拟商品列表", "module" => "WeiboList", "action" => "goods")

                )
            ),
            "weibo_recommend" => array(
                "name" => "推荐动态管理",
                "key" => "weibo_recommend",
                "nodes" => array(
                    array("name" => "推荐动态列表", "module" => "WeiboList", "action" => "weibo_recommend")
                )
            ),
            "weibo_order" => array(
                "name" => "订单管理",
                "key" => "weibo_order",
                "nodes" => array(
                    array("name" => "订单列表", "module" => "WeiboOrder", "action" => "index"),
                    array("name" => "微信列表", "module" => "WeiboOrder", "action" => "weixin"),
                    array("name" => "写真订单列表", "module" => "WeiboOrder", "action" => "photo"),
                    array("name" => "红包图片订单列表", "module" => "WeiboOrder", "action" => "red_photo"),
                    array("name" => "虚拟商品订单列表", "module" => "WeiboOrder", "action" => "goods"),
                    array("name" => "打赏订单列表", "module" => "WeiboOrder", "action" => "reward"),
                    array("name" => "聊天订单列表", "module" => "WeiboOrder", "action" => "chat")
                )
            ),
            "weibo_allege" => array(
                "name" => "申述管理",
                "key" => "weibo_allege",
                "nodes" => array(
                    array("name" => "申述列表", "module" => "WeiboAllegeList", "action" => "index"),
                    array("name" => "待处理申述列表", "module" => "WeiboAllegeList", "action" => "pending_deal"),
                    array("name" => "待确认申述列表", "module" => "WeiboAllegeList", "action" => "already_deal")
                )
            ),
            "comment" => array(
                "name" => "评论列表",
                "key" => "comment",
                "nodes" => array(
                    array("name" => "动态评论", "module" => "WeiboComment", "action" => "index")
                )
            )

        )
    ),
    "payment" => array(
        "name" => "资金管理",
        "key" => "payment",
        "groups" => array(
            "payment" => array(
                "name" => "支付接口",
                "key" => "payment",
                "nodes" => array(
                    array("name" => "支付接口列表", "module" => "Payment", "action" => "index")
                )
            ),
            "recharge" => array(
                "name" => "充值管理",
                "key" => "recharge",
                "nodes" => array(
                    array("name" => "在线充值", "module" => "RechargeNotice", "action" => "index")
                )
            ),
            "cash" => array(
                "name" => "提现管理",
                "key" => "cash",
                "nodes" => array(
                    array("name" => "提现列表", "module" => "WeiboRefundList", "action" => "index"),
                    array("name" => "提现待审核记录", "module" => "WeiboRefund", "action" => "index"),
                    array("name" => "提现待确认记录", "module" => "WeiboConfirmRefund", "action" => "index")
                )
            )

        )
    ),

    "tipoff" => array(
        "name" => "举报管理",
        "key" => "tipoff",
        "groups" => array(
            "payment" => array(
                "name" => "举报管理",
                "key" => "tipoff",
                "nodes" => array(
                    array("name" => "举报类型列表", "module" => "TipoffType", "action" => "index"),
                    array("name" => "举报列表", "module" => "WeiboTipoff", "action" => "index")
                )
            )
        )
    ),
    "nav" => array(
        "name" => "文章管理",
        "key" => "nav",
        "groups" => array(

            "articlecate" => array(
                "name" => "关于我们",
                "key" => "articlecate",
                "nodes" => array(
                    array("name" => "分类管理列表", "module" => "ArticleCate", "action" => "index"),
                    array("name" => "分类管理回收站", "module" => "ArticleCateTrash", "action" => "trash"),
                    array("name" => "文章管理列表", "module" => "Article", "action" => "index"),
                    array("name" => "文章管理回收站", "module" => "ArticleTrash", "action" => "trash")
                )
            ),
            "help" => array(
                "name" => "帮助与反馈",
                "key" => "help",
                "nodes" => array(
                    array("name" => "常见问题", "module" => "Faq", "action" => "index")
                )
            )

        )
    ),
    "msgtemplate" => array(
        "name" => "短信管理",
        "key" => "msgtemplate",
        "groups" => array(

            "sms" => array(
                "name" => "短信管理",
                "key" => "sms",
                "nodes" => array(
                    array("name" => "短信接口列表", "module" => "Sms", "action" => "index", "action_id" => "58")
                    //array("name"=>"短信列表","module"=>"PromoteMsgSms","action"=>"sms_index","action_id"=>"668"),
                )
            ),
            "stationmessage" => array(
                "name" => "系统消息管理",
                "key" => "StationMessage",
                "nodes" => array(
                    array("name" => "系统消息列表", "module" => "StationMessage", "action" => "index") //LS
                )
            ),
            "dealmsgList" => array(
                "name" => "队列管理",
                "key" => "dealmsgList",
                "nodes" => array(
                    array("name" => "业务队列列表", "module" => "DealMsgList", "action" => "index")
                )
            )

        )
    ),

    "system" => array(
        "name" => "系统设置",
        "key" => "system",
        "groups" => array(
            "sysconf" => array(
                "name" => "系统设置",
                "key" => "sysconf",
                "nodes" => array(
                    array("name" => "系统配置", "module" => "Conf", "action" => "index"),
                    array("name" => "广告设置", "module" => "WeiboIndex", "action" => "index")

                )
            ),

            "mobile" => array(
                "name" => "移动平台设置",
                "key" => "mobile",
                "nodes" => array(
                    array("name" => "手机端配置", "module" => "WeiboConf", "action" => "mobile"),
                    array("name" => "脏字库配置", "module" => "Conf", "action" => "dirty_words"),
                    array("name" => "昵称限制配置", "module" => "LimitName", "action" => "index")
                    //array("name"=>"手机端广告列表","module"=>"MAdv","action"=>"index"),
                )
            ),
            "admin" => array(
                "name" => "系统管理员",
                "key" => "admin",
                "nodes" => array(
                    array("name" => "管理员分组列表", "module" => "Role", "action" => "index", "action_id" => "11"),
                    array("name" => "管理员分组回收站", "module" => "RoleTrash", "action" => "trash", "action_id" => "13"),
                    array("name" => "管理员列表", "module" => "Admin", "action" => "index", "action_id" => "14"),
                    array("name" => "管理员回收站", "module" => "AdminTrash", "action" => "trash", "action_id" => "15")
                )
            ),
            "slbgroupconf" => array(
                "name" => "集群组配置",
                "key" => "slbgroupconf",
                "nodes" => array(
                    array("name" => "集群组列表", "module" => "SlbGroup", "action" => "index")
                )
            ),
            "lucknum" => array(
                "name" => "靓号管理",
                "key" => "lucknum",
                "nodes" => array(
                    array("name" => "靓号管理", "module" => "LuckNum", "action" => "index")
                )
            )
        )
    )
);
