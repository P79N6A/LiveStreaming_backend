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
                    array("name" => "快速导航", "module" => "Index", "action" => "main"),
                    array("name" => "网站数据统计", "module" => "Indexs", "action" => "statistics")
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
                    array("name" => "主播列表", "module" => "UserGeneral", "action" => "index"),
                    array("name" => "机器人头像", "module" => "UserRobot", "action" => "index"),
                    array("name" => "私信收礼统计", "module" => "UserStatistics", "action" => "private_statistics"),
                    array("name" => "主播观看统计", "module" => "UserGuard", "action" => "index"),
                    array("name" => "主播标签", "module" => "UserTags", "action" => "index")
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
                    array("name" => "认证未通过", "module" => "UserInvestorList", "action" => "index"),
                    array("name" => "认证名称列表", "module" => "AuthentList", "action" => "index")
                )
            ),
            "userlevel" => array(
                "name" => "等级管理",
                "key" => "userlevel",
                "nodes" => array(
                    array("name" => "等级列表", "module" => "UserLevel", "action" => "index"),
                    array("name" => "主播等级", "module" => "AnchorLevel", "action" => "index"),
                    array("name" => "等级效果", "module" => "SpeakLevel", "action" => "index")
                )
            ),
            "prop" => array(
                "name" => "道具管理",
                "key" => "prop",
                "nodes" => array(
                    array("name" => "背包列表", "module" => "PropBackpack", "action" => "index"),
                    array("name" => "守护列表", "module" => "GuardianRecord", "action" => "index"),
                    array("name" => "坐骑列表", "module" => "UserMounts", "action" => "index"),
                    array("name" => "勋章列表", "module" => "UserMedals", "action" => "index")
                )
            ),
            "missiondaily" => array(
                "name" => "任务管理",
                "key" => "missiondaily",
                "nodes" => array(
                    // array("name" => "每日任务列表", "module" => "Mission", "action" => "index"),
                    array("name" => "签到列表", "module" => "Signin", "action" => "index")
                )
            ),
            "family" => array(
                "name" => "家族管理",
                "key" => "family",
                "nodes" => array(
                    array("name" => "家族列表", "module" => "Family", "action" => "index"),
                    array("name" => "家族等级列表", "module" => "FamilyLevel", "action" => "index")
                )
            ),
            "society" => array(
                "name" => "公会管理",
                "key" => "society",
                "nodes" => array(
                    array("name" => "公会列表", "module" => "Society", "action" => "index"),
                    array("name" => "公会收益列表", "module" => "SocietyIncome", "action" => "index"),
                    array("name" => "公会等级列表", "module" => "SocietyLevel", "action" => "index")
                )
            ),
            "distribution" => array(
                "name" => "分销管理",
                "key" => "distribution",
                "nodes" => array(
                    array("name" => "分销列表", "module" => "Distribution", "action" => "index")
                )
            ),
            "wx_distribution" => array(
                "name" => "微信分销",
                "key" => "wx_distribution",
                "nodes" => array(
                    array("name" => "顶级分销商", "module" => "Wx_distribution", "action" => "index")
                )
            ),
            "tg_distribution" => array(
                "name" => "游戏分销",
                "key" => "tg_distribution",
                "nodes" => array(
                    array("name" => "顶级分销商", "module" => "Tg_distribution", "action" => "index")
                )
            )
            // "AnchorSort" => array(
            //     "name" => "主播类别",
            //     "key" => "AnchorSort",
            //     "nodes" => array(
            //         array("name" => "类别名称列表", "module" => "AnchorSort", "action" => "index")
            //     )
            // )
        )
    ),

    "dealcate" => array(
        "name" => "视频管理",
        "key" => "dealcate",
        "groups" => array(
            "videocate" => array(
                "name" => "分类管理",
                "key" => "dealcate",
                "nodes" => array(
                    array("name" => "话题列表", "module" => "VideoCate", "action" => "index"),
                    array("name" => "分类列表", "module" => "VideoClassified", "action" => "index"),
                    array("name" => "URL分类跳转列表", "module" => "VideoClassifiedUrl", "action" => "index"),
                    array("name" => "首页分类列表", "module" => "CarClassify", "action" => "index")
                )
            ),
            "video" => array(
                "name" => "视频管理",
                "key" => "dealorder",
                "nodes" => array(
                    array("name" => "直播中视频", "module" => "Video", "action" => "online_index"),
                    array("name" => "监控", "module" => "VideoMonitor", "action" => "monitor"),
                    array("name" => "警告内容列表", "module" => "WarningMsg", "action" => "index"),
                    array("name" => "直播结束视频", "module" => "VideoEnd", "action" => "endline_index"),
                    array("name" => "回播列表", "module" => "VideoPlayback", "action" => "playback_index"),
                    array("name" => "PK记录", "module" => "PkLiveHistory", "action" => "index"),
                    array("name" => "审核视频列表", "module" => "VideoCheck", "action" => "playback_index"),
                    array("name" => "推送消息列表", "module" => "PushAnchor", "action" => "index"),
                    array("name"=>"视频采集new","module"=>"VideoCollectnew","action"=>"index")
                )
            )
        )
    ),
    "score_mall" => array(
        "name" => "道具管理",
        "key" => "score_mall",
        "groups" => array(
            "score_mall" => array(
                "name" => "道具管理",
                "key" => "score_mall",
                "nodes" => array(
                    array("name" => "道具列表", "module" => "Prop", "action" => "index"),
                    array("name" => "消耗统计", "module" => "PropStatistics", "action" => "consume_statistics"),
                    array("name" => "中奖统计", "module" => "PropWinning", "action" => "index")
                )
            ),
            "guard_mall" => array(
                "name" => "守护管理",
                "key" => "guard_mall",
                "nodes" => array(
                    array("name" => "守护列表", "module" => "Guard", "action" => "index"),
                    array("name" => "守护等级", "module" => "GuardLevel", "action" => "index"),
                    array("name" => "购买记录", "module" => "GuardPayHistory", "action" => "index")
                )
            ),
            "mount_mall" => array(
                "name" => "坐骑管理",
                "key" => "mount_mall",
                "nodes" => array(
                    array("name" => "坐骑列表", "module" => "Mounts", "action" => "index"),
                    array("name" => "购买记录", "module" => "MountPayHistory", "action" => "index")
                )
            ),
            "medal_mall" => array(
                "name" => "勋章管理",
                "key" => "medal_mall",
                "nodes" => array(
                    array("name" => "勋章列表", "module" => "Medals", "action" => "index")
                )
            )
        )
    ),
    "weibo_manage" => array(
        "name" => "小视屏管理",
        "key" => "weibo_manage",
        "groups" => array(
            "weibo_manage" => array(
                "name" => "小视屏管理",
                "key" => "weibo_manage",
                "nodes" => array(
                    array("name" => "小视屏列表", "module" => "WeiboList", "action" => "index")
                )
            ),
            "comment" => array(
                "name" => "评论列表",
                "key" => "comment",
                "nodes" => array(
                    array("name" => "小视屏评论", "module" => "WeiboComment", "action" => "index")
                )
            )
        )
    ),
    // "weibo_manage" => array(
    //     "name" => "动态管理",
    //     "key" => "weibo_manage",
    //     "groups" => array(
    //         "weibo_manage" => array(
    //             "name" => "动态管理",
    //             "key" => "weibo_manage",
    //             "nodes" => array(
    //                 array("name" => "动态列表", "module" => "WeiboList", "action" => "index"),
    //                 array("name" => "图文列表", "module" => "WeiboList", "action" => "imagetext"),
    //                 array("name" => "视频列表", "module" => "WeiboList", "action" => "video"),
    //                 // array("name" => "微信列表", "module" => "WeiboList", "action" => "weixin"),
    //                 array("name" => "写真列表", "module" => "WeiboList", "action" => "photo"),
    //                 array("name" => "红包图片列表", "module" => "WeiboList", "action" => "red_photo"),
    //                 array("name" => "虚拟商品列表", "module" => "WeiboList", "action" => "goods")

    //             )
    //         ),
    //         "weibo_recommend" => array(
    //             "name" => "推荐动态管理",
    //             "key" => "weibo_recommend",
    //             "nodes" => array(
    //                 array("name" => "推荐动态列表", "module" => "WeiboList", "action" => "weibo_recommend")
    //             )
    //         ),
    //         "weibo_order" => array(
    //             "name" => "订单管理",
    //             "key" => "weibo_order",
    //             "nodes" => array(
    //                 array("name" => "订单列表", "module" => "WeiboOrder", "action" => "index"),
    //                 // array("name" => "微信列表", "module" => "WeiboOrder", "action" => "weixin"),
    //                 array("name" => "写真订单列表", "module" => "WeiboOrder", "action" => "photo"),
    //                 array("name" => "红包图片订单列表", "module" => "WeiboOrder", "action" => "red_photo"),
    //                 array("name" => "虚拟商品订单列表", "module" => "WeiboOrder", "action" => "goods"),
    //                 array("name" => "打赏订单列表", "module" => "WeiboOrder", "action" => "reward"),
    //                 array("name" => "聊天订单列表", "module" => "WeiboOrder", "action" => "chat")
    //             )
    //         ),
    //         "weibo_allege" => array(
    //             "name" => "申述管理",
    //             "key" => "weibo_allege",
    //             "nodes" => array(
    //                 array("name" => "申述列表", "module" => "WeiboAllegeList", "action" => "index"),
    //                 array("name" => "待处理申述列表", "module" => "WeiboAllegeList", "action" => "pending_deal"),
    //                 array("name" => "待确认申述列表", "module" => "WeiboAllegeList", "action" => "already_deal")
    //             )
    //         ),
    //         "comment" => array(
    //             "name" => "评论列表",
    //             "key" => "comment",
    //             "nodes" => array(
    //                 array("name" => "动态评论", "module" => "WeiboComment", "action" => "index")
    //             )
    //         )

    //     )
    // ),
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
                    array("name" => "提现列表", "module" => "UserRefundList", "action" => "index"),
                    array("name" => "公会提现列表", "module" => "SocietyRefundList", "action" => "index"),
                    array("name" => "家族提现列表", "module" => "FamilyRefundList", "action" => "index"),
                    array("name" => "等级规则列表", "module" => "RefundRole", "action" => "index"),
                    array("name" => "提现待审核记录", "module" => "UserRefund", "action" => "index"),
                    array("name" => "提现待确认记录", "module" => "UserConfirmRefund", "action" => "index")
                )
            ),
            "statistics" => array(
                "name" => "统计管理",
                "key" => "statistics",
                "nodes" => array(
                    array("name" => "统计图表", "module" => "StatisticsModule", "action" => "chart"),
                    array("name" => "充值统计", "module" => "StatisticsModule", "action" => "statistics_recharge"),
                    array("name" => "后台充值", "module" => "UserRechargeLog", "action" => "index"),
                    array("name" => "提现统计", "module" => "StatisticsModule", "action" => "statistics_refund")
                )
            ),
            "recharge_code" => array(
                "name" => "兑换码管理",
                "key" => "recharge_code",
                "nodes" => array(
                    array("name" => "兑换码列表", "module" => "RechargeCode", "action" => "index")
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
                    array("name" => "举报列表", "module" => "Tipoff", "action" => "index")
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
    "PlugIn" => array(
        "name" => "插件中心",
        "key" => "PlugIn",
        "groups" => array(
            "PlugInconf" => array(
                "name" => "插件管理",
                "key" => "PlugInconf",
                "nodes" => array(
                    array("name" => "插件配置", "module" => "PlugIn", "action" => "index")
                )
            ),

            "goodsconf" => array(
                "name" => "商品设置",
                "key" => "goodsconf",
                "nodes" => array(
                    array("name" => "添加商品", "module" => "Goods", "action" => "add"),
                    array("name" => "商品管理", "module" => "Goods", "action" => "index"),
                    array("name" => "分类列表", "module" => "GoodsCate", "action" => "index"),
                    array("name" => "商品标签", "module" => "GoodsTags", "action" => "index")
                )
            ),
            /*"goodsconf"    =>    array(
            "name"    =>    "商品设置",
            "key"    =>    "goodsconf",
            "nodes"    =>    array(
            array("name"=>"商户列表","module"=>"Shop","action"=>"index"),
            array("name"=>"添加商品","module"=>"ShopGoods","action"=>"add"),
            array("name"=>"商品管理","module"=>"ShopGoods","action"=>"index"),
            array("name"=>"分类列表","module"=>"GoodsCate","action"=>"index"),
            array("name"=>"商品标签","module"=>"GoodsTags","action"=>"index"),
            ),
            ),*/
            "user_goodsconf" => array(
                "name" => "主播商品管理",
                "key" => "user_goodsconf",
                "nodes" => array(
                    array("name" => "主播平台商品", "module" => "User_Goods", "action" => "index"),
                    array("name" => "主播小店商品", "module" => "PodcastGoods", "action" => "index"),
                    array("name" => "购物订单列表", "module" => "PodcastOrder", "action" => "index")
                )
            ),

            "pai_goods" => array(
                "name" => "竞拍商品",
                "key" => "pai_goods",
                "nodes" => array(
                    array("name" => "商品列表", "module" => "PaiGoods", "action" => "index")
                )
            ),
            "goods_order" => array(
                "name" => "竞拍订单",
                "key" => "goods_order",
                "nodes" => array(
                    array("name" => "虚拟竞拍分类", "module" => "PaiTags", "action" => "index"),
                    array("name" => "竞拍订单列表", "module" => "GoodsOrder", "action" => "index"),
                    array("name" => "用户地址", "module" => "UserAddr", "action" => "index"),
                    array("name" => "消息列表", "module" => "UserNotice", "action" => "index"),
                    array("name" => "竞拍列表", "module" => "PaiJoin", "action" => "index"),
                    array("name" => "保证金记录", "module" => "UserDiamondsLog", "action" => "index")
                )
            ),
            "goods_complaint" => array(
                "name" => "订单申诉状态",
                "key" => "goods_complaint",
                "nodes" => array(
                    array("name" => "申诉订单", "module" => "Refund", "action" => "index")
                )
            ),
            "gameconf" => array(
                "name" => "游戏设置",
                "key" => "gameconf",
                "nodes" => array(
                    array("name" => "游戏配置", "module" => "Games", "action" => "index"),
                    array("name" => "游戏记录", "module" => "GameLog", "action" => "index"),
                    array("name" => "游戏历史记录", "module" => "GameLogHistory", "action" => "index"),
                    array("name" => "游戏金币记录", "module" => "Games", "action" => "betLog"),
                    array("name" => "游戏上庄记录", "module" => "Games", "action" => "bankerLog")
                )
            ),
            "awardconf" => array(
                "name" => "礼物中奖设置",
                "key" => "awardconf",
                "nodes" => array(
                    array("name" => "随机中奖的倍数", "module" => "AwardMultiple", "action" => "index")
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
                    array("name" => "广告设置", "module" => "IndexImage", "action" => "index"),
                    array("name" => "广告推送", "module" => "IndexProp", "action" => "index"),
                    array("name" => "兑换规则", "module" => "ExchangeRule", "action" => "index"),
                    array("name" => "购买规则", "module" => "RechargeRule", "action" => "index"),
                    array("name" => "VIP购买规则", "module" => "VipRule", "action" => "index")
                )
            ),
            "ads" => array(
                "name" => "广告配置",
                "key" => "ads",
                "nodes" => array(
                    array("name" => "广告列表", "module" => "Ad", "action" => "index")
                )
            ),
            "mobile" => array(
                "name" => "移动平台设置",
                "key" => "mobile",
                "nodes" => array(
                    array("name" => "手机端配置", "module" => "Conf", "action" => "mobile"),
                    array("name" => "脏字库配置", "module" => "Conf", "action" => "dirty_words"),
                    array("name" => "昵称限制配置", "module" => "LimitName", "action" => "index"),
                    array("name" => "加密KEY配置", "module" => "KeyList", "action" => "index")
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
