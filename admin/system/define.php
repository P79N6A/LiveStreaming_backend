<?php

define("IS_DEBUG", 1);
define("DE_BUGE", 0); //AES加密调试
define("SHOW_DEBUG", 0);
define('DE_BUGE_AES', 0);
define("SHOW_LOG", 0);
define("MAX_DYNAMIC_CACHE_SIZE", 1000); //动态缓存最数量
define("SMS_TIMESPAN", 60); //短信验证码发送的时间间隔
define("SMS_EXPIRESPAN", 300); //短信验证码失效时间

define("PAI_PAGE_SIZE", 10);
define("PIN_PAGE_SIZE", 80);
define("PIN_SECTOR", 10);
define("MAX_SP_IMAGE", 20); //商家的最大图片量
define("MAX_LOGIN_TIME", 0); //登录的过期时间,单位：秒
define("ORDER_DELIVERY_EXPIRE", 7); //延期收货天

define("AES_DECRYPT_KEY", 'fanwe');
define("SESSION_TIME", 3600 * 1); //session超时时间
define("SMS_MOBILE_SEND_COUNT", 9); //每个手机号每天最多只能发XX条
define("SMS_IP_SEND_COUNT", 9); //每个IP每小时最多只能发XX条

define("OPEN_CHECK_ACCOUNT", 0); //后台手机验证码功能
/*
 * 是否开启 MYSQL 和 REDIS的长链接
 * 在高并发的情况下开启，链接数 会和 php进程数一致，nginx要重启服务后生效
 * 默认是关闭的
 */
define('IS_LONG_LINK', false);

define('IS_LONG_LINK_MYSQL', false);
/*
 * 是否开启事务
 */
define('IS_REDIS_WORK', false);

//关于竞拍的一些配置
define('OPEN_PAI_MODULE', 0); //是否开启竞拍
define('PAI_REAL_BTN', 0); //是否开启实物竞拍
define('PAI_VIRTUAL_BTN', 0); //是否开启虚拟竞拍
define('SHOPPING_GOODS', 0); //是否开启购物
define('SHOP_SHOPPING_CART', 0); //是否开启购物车
define('OPEN_PODCAST_GOODS', 0); //是否打开主播小店

define('PAI_MAX_VIOLATIONS', 2); //竞拍 一个月最大违规次数(手动退出竞拍，或者直播掉线)
define('PAI_CLOSE_VIOLATIONS', 15); //多久之后解禁(天)
define('SHOW_USER_ORDER', 0); //是否显示【我的订单】 0否 1是
define('SHOW_USER_PAI', 0); //是否显示【我的竞拍】 0否 1是
define('SHOW_PODCAST_ORDER', 0); //是否显示星店订单(主播) 0否 1是
define('SHOW_PODCAST_PAI', 0); //是否显示竞拍管理(主播) 0否 1是
define('SHOW_PODCAST_GOODS',0); //是否显示 商品管理（主播） 0否 1是
define('MAX_PAI_PAY_TIME', 15 * 60); //竞拍付款倒计时时长，单位秒
define('MAX_USER_CONFIRM_TIME', 3 * 24 * 3600); //用户确认约会完成倒计时时长，单位秒
define('MAX_PODCAST_CONFIRM_TIME', 1 * 24 * 3600); //主播确认约会完成倒计时时长，单位秒
define('MAX_USER_CONFIRM_VIRTUAL_TIME', 10 * 24 * 3600); //用户确认收货倒计时时长，单位秒
define('MAX_PODCAST_CONFIRM_VIRTUAL_TIME', 3 * 24 * 3600); //主播确认发货倒计时时长，单位秒
define("FANWE_APP_ID_YM", ''); //源码用户的app_id；需手动填写
define("FANWE_AES_KEY_YM", ''); //源码用户的aes_key；需手动填写

define('ROBOT_PROP', 0); // 机器人自动送礼

define('PAI_YANCHI_MODULE', 0); //延迟模式：0：添加延迟时长，1：更新延迟时长

//靓号开关配置
define('OPEN_LUCK_NUM', 1); //是否开启靓号

//广告开关配置
define('OPEN_ADS', 1); //是否开启广告

//每日首次登录赠送积分配置
define('OPEN_LOGIN_SEND_SCORE', 1); //是否开启每日首次登录赠送积分

//每次登录时升级提示
define('OPEN_UPGRADE_PROMPT', 1); //是否开启每次登录时升级提示

//是否开启排行榜
define('OPEN_RANKING_LIST', 1); //是否开启排行榜

//是否开启集群组配置
define('OPEN_SLBGROUP', 1); //是否开启集群组配置

//是否开启PC版本
define('OPEN_PC', 1); //是否开启PC版本

// 独立 PC 版本
define('ONLY_PC', 0); //是否独立PC版本 OPEN_PC 为 1 时才生效

// PC是否开启观看历史
define('OPEN_PC_HISTORY', 1); //是否开启PC版本

//是否开启分享加秀票或是秀豆
define('OPEN_SHARE_EXPERIENCE', 1); //是否开启分享加秀票或是秀豆

//是否开启第三方商城
define('OPEN_GOODS', 0); //是否开启分享加经验

//是否开启腾讯云视频
define('TECENT_VIDEO', 1); //是否开启腾讯云视频

//是否付费直播
define('OPEN_LIVE_PAY', 1); //是否付费直播

define('LIVE_PAY_TIME', 1); //是否开启按时付费

define('LIVE_PAY_SCENE', 1); //是否开启按场付费

define('LIVE_END_TO_SCENE', 1); //是否按时付费直播结束后转按场付费

//是否开启游戏
define('OPEN_GAME_MODULE', 1); //是否开启游戏
define('OPEN_BANKER_MODULE', 0); //是否开启上庄模块
define('OPEN_SEND_COINS_MODULE', 0); //是否开启赠送游戏币模块
define('OPEN_SEND_DIAMONDS_MODULE', 0); //是否开启赠送秀豆模块
define('OPEN_DIAMOND_GAME_MODULE', 1); //是否开启秀豆游戏
define('SHOW_IS_GAMING', 1); //首页显示正在游戏中
define('GAME_GAIN_FOR_ALERT', 1); //游戏获胜弹幕
define('GAME_COMMISSION', 1); //平台抽成开关
define('PODCAST_COMMISSION', 1); //主播抽成开关
define('GAME_DISTRIBUTION', 0); //游戏分销开关
define('GAME_DISTRIBUTION_TOP', 0); //游戏分销顶级用户开关
define('GAME_WINNER', 0); //游戏赢家开关
define('GAME_REWARD', 0); //游戏打赏开关
define('GAME_AUTO_START', 1); //自动游戏开关
define('USER_GAME_RATE', 0); //玩家独立游戏干预系数

define('WEIXIN_DISTRIBUTION', 0); //游戏分销开关

define('ENTER_INVITATION_CODE', 0); //邀请码

define('BUY_PLUGIN_ONCE', 0); //一次性购买插件

//游戏分销开关
define('ONE_MOBILE', 0); //0 可重复绑定手机 不可重复绑定

//切换后台
define('MODULE_ADMIN', 0); //切换后台 0 默认后台 1 PC版

//支付宝一键认证
define('OPEN_AUTHENT_ALIPAY', 1); //支付宝一键认证  0 关闭 1 开启

//分销功能
define('OPEN_DISTRIBUTION', 0); //分销功能  0 关闭 1 开启
//分销模块
define('DISTRIBUTION_MODULE', 0); //分销功能  0 关闭 1 开启1类型（个人中心编辑上级，昆明）

//限时开放直播
define('OPEN_LIMINT_TIME', 1); //限时开放直播  0 关闭 1 开启

//VIP会员模块
define('OPEN_VIP', 1); //VIP会员  0 关闭 1 开启

//监控页面发送警告
define('OPEN_WARNING', 1); //监控页面发送警告  0 关闭 1 开启

//房间隐藏
define('OPEN_ROOM_HIDE', 1); //房间隐藏  0 关闭 1 开启

//关于公会的一些配置
define('OPEN_SOCIETY_MODULE', 1); //是否开启公会

//关于家族的一些配置
define('OPEN_FAMILY_MODULE', 0); //是否开启家族

// 千秀直播云开关
if (!defined('OPEN_FWYUN')) {
    define('OPEN_FWYUN', 1);
}

//
define('EXAMINE_TIME', 0); // 审核时间

//教育的直播配置
define('OPEN_EDU_MODULE', 0); //是否开启教育模块开关 0 关闭 1 开启

define('CHANGE_NAV', 'default'); //默认为0，若有填写则为开启不同后台，xr为鲜肉直播

//主播收礼物日志
define('USER_PROP_CLOSED', 1); //是否开启收礼物日志 0 关闭 1 开启

//对主播单独限制 游戏、竞拍、付费
define('OPEN_PLUGIN', 1); //是否开启限制 0 关闭 1 开启

//手动置顶
define('OPEN_STICK', 1); //是否开启收手动置顶 0 关闭 1 开启

//回放可编辑付费直播
define('OPEN_PAY_EDITABLE', 0); // 0 关闭 1 开启

//后台设置多少级才可发言
define('OPEN_SPEAK_LEVEL', 1); // 0 关闭 1 开启

//主播单独设置提现比例
define('OPEN_SCALE', 1); // 0 关闭 1 开启

// 每日任务开关
define('OPEN_MISSION', 0); // 0 关闭 1 开启

//声网开关
define('SOUND_NETWORK', 0); // 0 关闭 1 开启

//是否开启鲜肉
define('OPEN_X', 0); // 0 关闭 1 开启

//是否游客登录
define('VISITORS', 1); // 0 关闭 1 开启

//机器人手动送礼物
define('robot_gifts', 0); // 0 关闭 1 开启
//多支付宝功能
define('MORE_ALIPAY', 0); // 0 关闭 1 开启

//公屏收费开关
define('PUBLIC_PAY', 0); // 0 关闭 1 开启
//IOS审核视频
define('CHECK_VIDEO', 1); // 0 关闭 1 开启

//百魅视频
define('OPEN_BM', 0); // 0 关闭 1 开启

//百魅礼物消耗游戏币
define('PROP_COINS', 0); // 0 关闭 1 开启

// 强制开启手机绑定
define('OPEN_FORCE_MOBILE', 0); // 0 关闭 1 开启

//后台管理更改直播中的视频收费状态
define('OPEN_EDIT_VIDEO_PAY', 1); // 0 关闭 1 开启

// 登录邀请码开关
define('OPEN_INVITE_CODE', 0); // 0 关闭 1 开启

//APP端会所的显示
define('OPEN_SOCIETY_APP', 1); // 0 关闭 1 开启

//二级分销开关
define('DISTRIBUTION_SCAN', 0); // 0 关闭 1 开启

//是否子房间
define("CHILD_ROOM", 0); //0 关闭 1开启

//大型礼物赠送——全服飞屏通告
define('PROP_NOTIFY', 1); // 0 关闭 1 开启

//上传视频到OSS
define('UPLOAD_OSS', 0); // 0 关闭 1 开启

//指定家族给用户 ljz
define('OPEN_FAMILY_JOIN', 0); //0关闭1开启

//西藏青稞
define('QK_TREE', 0); //0关闭 1开启

//Excel文件上传修改m_config配置文件  ljz
define('UPDATE_EXCEL', 0); //0关闭 1开启

// 开启小视屏
define('OPEN_SVIDEO_MODULE', 1); //0关闭 1开启

//礼物中奖
define('OPEN_REWARD_GIFT', 1); //0关闭 1开启

//云片国际短信
define('OPEN_YPSMS', 0); //0关闭 1开启

//520预约众筹定制
define('ORDER_ZC', 0);

//Url分类跳转
define('OPEN_CLASSIFY_URL', 1); //是否开启

//车行定制开关
define('OPEN_CAR_MODULE', 1); //是否开启

//车行定制开关-全省/全服红包
define('FULLSERVER_RED_ENVELOPE', 0); //是否开启

//张先生-秀豆兑换码定制
define('RECHARGE_CODE', 0); //是否开启

//浪花分销
define('SHARE_DISTRIBUTION', 0); //是否开启

// wawa
define('OPEN_WAWA', 0);