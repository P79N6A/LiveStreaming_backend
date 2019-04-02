/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : 000000

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2016-09-01 09:25:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `%DB_PREFIX%admin`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%admin`;
CREATE TABLE `%DB_PREFIX%admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adm_name` varchar(255) NOT NULL,
  `adm_password` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `role_id` int(11) NOT NULL,
  `login_time` int(11) NOT NULL,
  `login_ip` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户类型   0：管理员，1：部门，2：部门成员',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '所属部门编号',
  `referrals_rate` varchar(50) NOT NULL COMMENT '提成系数',
  `referrals_count` int(11) NOT NULL COMMENT '部门成员人数',
  `referrals_money` decimal(11,0) NOT NULL,
  `role_ids` text NOT NULL,
  `real_name` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_adm_name` (`adm_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='//管理员';

-- ----------------------------
-- Records of %DB_PREFIX%admin
-- ----------------------------
INSERT INTO `%DB_PREFIX%admin` VALUES ('1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1', '0', '2', '1472620490', '120.32.126.40', '0', '0', '', '0', '0', '', '', '');
INSERT INTO `%DB_PREFIX%admin` VALUES ('12', 'fanwe', '6714ccb93be0fda4e51f206b91b46358', '1', '0', '11', '1472607215', '120.197.117.134', '0', '0', '', '0', '0', '', '', '');

-- ----------------------------
-- Table structure for `%DB_PREFIX%api_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%api_list`;
CREATE TABLE `%DB_PREFIX%api_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ctl_act` varchar(50) NOT NULL,
  `has_cookie` tinyint(1) DEFAULT '1' COMMENT '1需要传cookie；0不需要',
  `slb_group_id` int(10) DEFAULT '0' COMMENT '从属于那个集群组',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%api_list
-- ----------------------------
INSERT INTO `%DB_PREFIX%api_list` VALUES ('2', '获得礼物列表', 'app_prop', '1', '3');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('3', '举报类型', 'app_tipoff_type', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('4', '弹幕', 'deal_pop_msg', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('5', '发送礼物', 'deal_pop_prop', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('6', '抢红包', 'deal_red_envelope', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('7', '抢红包---》看看大家的手气', 'deal_user_red_envelope', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('8', '获得用户签名', 'user_usersig', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('9', '获得用户信息', 'user_userinfo', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('10', '禁言', 'user_forbid_send_msg', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('11', '关注', 'user_follow', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('12', '设置管理员', 'user_set_admin', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('13', '举报', 'user_tipoff', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('14', '用户管理员列表', 'user_user_admin', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('15', '关注用户', 'user_user_follow', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('16', '分享成功回调', 'user_share', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('17', '友盟推送code', 'user_apns', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('18', '用户主页', 'user_user_home', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('19', '直播回看列表', 'user_user_review', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('20', '用户粉丝', 'user_user_focus', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('21', '设置黑名单', 'user_set_black', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('22', '房间在线用户列表', 'video_viewer', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('23', '直播结束', 'video_end_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('24', '主播心跳监听', 'video_monitor', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('25', '删除视频', 'video_del_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('26', '(随机)获得视频', 'video_get_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('27', '贡献榜', 'video_cont', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('28', '客户端创建房间状态回调', 'video_video_cstatus', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('29', '检查是否有连麦权限', 'video_check_lianmai', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('30', '开始连麦', 'video_start_lianmai', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('31', '停止连麦', 'video_stop_lianmai', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('32', '用户充值界面', 'pay_recharge', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('33', '用户充值支付', 'pay_pay', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('34', '添加音乐', 'music_add_music', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('35', '删除音乐', 'music_del_music', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('36', '用户音乐列表', 'music_user_music', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('37', '音乐搜索', 'music_search', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('38', '获得音乐下载地址', 'music_downurl', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('39', '回看视频', 'video_get_vodset', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('40', '搜索用户', 'user_search', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('41', '添加一直播房间', 'video_add_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('42', '获得音乐歌词', 'music_getlrc', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('43', '检查直播状态', 'video_check_status', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('44', '删除回播视频', 'video_del_video_history', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('45', '我关注的用户', 'user_my_follow', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('46', '好友(相互关注用户)', 'user_friends', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('47', '私密直播加人', 'video_private_push_user', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('48', '私密房间用户列表', 'video_private_room_friends', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('49', '私密直播踢人', 'video_private_drop_user', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('50', '获得用户基本信息', 'user_baseinfo', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('51', '首页-热门', 'index_index', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('52', '首页-最新', 'index_new_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('53', '首页-关注', 'index_focus_video', '1', '0');
INSERT INTO `%DB_PREFIX%api_list` VALUES ('54', '热门(搜索)话题', 'index_search_video_cate', '1', '3');

-- ----------------------------
-- Table structure for `%DB_PREFIX%api_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%api_log`;
CREATE TABLE `%DB_PREFIX%api_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` varchar(32) DEFAULT NULL,
  `ctl_act` varchar(50) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `parma` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%api_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%article`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%article`;
CREATE TABLE `%DB_PREFIX%article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `content` text NOT NULL COMMENT ' 文章内容',
  `cate_id` int(11) NOT NULL COMMENT '文章分类ID',
  `create_time` int(11) NOT NULL COMMENT '发表时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `add_admin_id` int(11) NOT NULL COMMENT '发布人(管理员ID)',
  `is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
  `rel_url` varchar(255) NOT NULL COMMENT '自动跳转的外链',
  `update_admin_id` int(11) NOT NULL COMMENT '更新人(管理员ID)',
  `is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
  `click_count` int(11) NOT NULL COMMENT '点击数',
  `sort` int(11) NOT NULL COMMENT '排序 由大到小',
  `seo_title` text NOT NULL COMMENT '自定义seo页面标题',
  `seo_keyword` text NOT NULL COMMENT '自定义seo页面keyword',
  `seo_description` text NOT NULL COMMENT '自定义seo页面标述',
  `uname` varchar(255) NOT NULL,
  `sub_title` varchar(255) NOT NULL,
  `brief` text NOT NULL,
  `is_week` tinyint(1) NOT NULL,
  `is_hot` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL COMMENT '展示图表',
  `writer` varchar(255) NOT NULL COMMENT '发布者',
  `tags` varchar(255) NOT NULL COMMENT '标签',
  PRIMARY KEY (`id`),
  KEY `cate_id` (`cate_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE,
  KEY `update_time` (`update_time`) USING BTREE,
  KEY `click_count` (`click_count`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%article
-- ----------------------------
INSERT INTO `%DB_PREFIX%article` VALUES ('6', '公告1', '我们提倡绿色直播,封面和直播内容含吸烟、低俗、引诱、暴露等都将会被封停账号，网警24小时在线查巡哦！', '17', '1465690127', '1471025515', '0', '1', '', '0', '0', '0', '4', '', '', '', '', '', '', '1', '1', '', '千秀众筹', '公告');
INSERT INTO `%DB_PREFIX%article` VALUES ('13', '主播协议', '<p>\r\n	<span style=\"line-height:1.5;\"> </span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:center;\" align=\"center\">\r\n	<b><span style=\"font-size:12.0pt;font-family:宋体;\"><span style=\"color:#E53333;\"></span><span style=\"color:#000000;\">千秀互动直播用户协议</span></span></b> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">&nbsp;&nbsp; </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播服务协议是直播用户（下称<span>“</span>用户<span>”</span>）与福建千秀信息科技有限公司（简称<span>“</span>千秀公司<span>”</span>）之间签订的协议。千秀互动直播是由千秀公司演示的多人在线观看视频播放服务为主的计算机软件，千秀公司网站是承载千秀互动直播演示的网站，包括此网站上相关互联网信息及互联网应用。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">1</span><span style=\"font-size:12.0pt;font-family:宋体;\">、重要须知<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">1.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户欲下载安装千秀互动直播或者访问千秀互动直播网站，必须事先认真阅读（未成年人应当在监护人陪同下阅读）、充分理解《千秀互动直播服务协议》及千秀互动直播及千秀互动直播网站发布的使用规则（以下称<span>“</span>本协议<span>”</span>）中各条款，包括免除或者限制千秀互动直播及千秀互动直播网站责任的免责条款及对用户的权利限制条款。如用户不同意本协议条款，用户应不使用或主动取消千秀互动直播及千秀互动直播网站提供的服务。用户的使用行为将被视为用户对本协议条款全部的完全接受。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">1.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">在用户接受本协议之后，本协议可能因国家政策、产品、服务以及履行本协议的环境发生变化而进行不时的修改。修改后的协议发布在千秀互动直播客户端或网站上，若用户对修改后的协议有异议的，请立即停止访问、使用千秀互动直播及千秀互动直播网站，用户的继续访问或使用的行为，视为对修改后的协议予以认可且接受。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2</span><span style=\"font-size:12.0pt;font-family:宋体;\">、千秀互动直播服务<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播由千秀公司演示，以多人在线观看视频播放服务为主，包括个人、多人在线文字服务，以及相关社区、功能的软件组合。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播网站，由千秀公司经营的网址包括（<span>www.fanwe.com</span>）的网站，本网站为千秀互动直播提供下载、演示服务、客服等相关承载服务，同时也提供千秀互动直播形成用户展示、社区交流等与千秀互动直播相配套的服务。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">在用户遵守本协议及相关法律法规的前提下，千秀公司给予用户一项个人的、不可转让及非排他性的许可，以使用千秀互动直播服务。用户仅可为非商业目的使用千秀互动直播服务，包括：<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>1</span>）接收、下载、安装、启动、升级、登录、显示、运行千秀互动直播；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>2</span>）创建角色，设置网名，查阅规则、用户个人资料、在千秀互动直播中充值、购买、使用虚拟代币、道具等，使用聊天功能、社交分享功能；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>3</span>）其他千秀互动直播支持并允许的其他某一项或几项功能。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.4 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户在使用千秀互动直播服务过程中不得未经千秀公司许可以任何方式录制并向他人传播千秀互动直播内容，包括不得利用任何第三方软件进行网络传播等。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.5 </span><span style=\"font-size:12.0pt;font-family:宋体;\">如果千秀公司发现或收到他人举报或投诉用户违反本协议约定的，千秀公司有权不经通知随时对相关内容进行删除，并视行为情节对违规账号处以包括但不限于警告、限制或禁止使用全部或部分功能、账号封禁直至注销的处罚。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.6 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户充分理解并同意，千秀公司有权依合理判断对违反有关法律法规或本协议规定的行为进行处罚，对违法违规的任何用户采取适当的法律行动，并依据法律法规保存有关信息向有关部门报告等，用户应独自承担由此而产生的一切法律责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.7 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户充分理解并同意，因用户违反本协议或相关服务条款的规定，导致或产生第三方主张的任何索赔、要求或损失，用户应当独立承担责任；千秀公司因此遭受损失的，用户也应当一并赔偿。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.8 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户充分理解并同意：为营造公平、健康的网络环境，在用户使用千秀互动直播服务的过程中，千秀公司有权通过技术手段了解用户终端设备的随机存储内存以及与千秀互动直播软件同时运行的相关程序。一经发现有任何未经授权的、危害千秀互动直播服务正常演示的相关程序，千秀公司将收集所有与此有关的信息并采取合理措施予以打击。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">2.9 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户充分理解并同意，千秀公司有权以任何方式直播或录制用户使用千秀互动直播服务时产生的内容和画面，有权对上述内容及画面进行编辑、剪接、加以评论、点评，并通过网络向第三人传播。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3</span><span style=\"font-size:12.0pt;font-family:宋体;\">、用户行为规范<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户在接受本协议后可浏览千秀互动直播软件<span>/</span>网站一般性功能，下载安装千秀互动直播，但若需要登录网站、软件的需要成为注册用户。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户必须清楚使用规则，千秀互动直播提供的服务仅供个人交流、学习欣赏，非商业性质的使用，用户不可对该等服务任何部分的任何信息进行复制、拷贝、出售、或利用本服务进行调查、广告、或用于其他商业目的。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得利用千秀互动直播提供的服务制作、复制、发布、传播、存储含有下列内容的信息：<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（一）反对宪法所确定的基本原则的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（二）危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（三）损害国家荣誉和利益的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（四）煽动民族仇恨、民族歧视，破坏民族团结的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（五）破坏国家宗教政策，宣扬邪教和封建迷信的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（六）散布谣言，扰乱社会秩序，破坏社会稳定的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（七）散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（八）侮辱或者诽谤他人，侵害他人合法权益的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（九）含有法律、行政法规禁止的其他内容的。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.4 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户保证在使用千秀互动直播时发布、传播的信息的真实性、准确性，同时保证该等信息不侵犯任何第三方的知识产权。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.5 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得利用千秀互动直播服务进行任何诸如发布广告、销售商品的商业行为，或者进行任何非法的侵害千秀互动直播利益的行为，如贩卖虚拟货币、礼券、外挂、道具等。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.6 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得为商业演示目的安装、使用、运行千秀互动直播，不得对该软件或者该软件运行过程中释放到任何计算机终端内存中的数据及该软件运行过程中客户端与服务器端的交互数据进行复制、更改、修改、挂接运行或创作任何衍生作品，形式包括但不限于使用插件、外挂或非经授权的第三方工具<span>/</span>服务接入千秀互动直播和相关系统。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.7 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得未经千秀公司许可，将千秀互动直播安装在未经明示许可的其他终端设备上。包括但不限于机顶盒、手持设备、电话、无线上网机、游戏机、电视机、<span>DVD</span>机等。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.8 </span><span style=\"font-size:12.0pt;font-family:宋体;\">保留权利：未明示授权的其他一切权利仍归千秀公司所有，用户使用其他权利时须另外取得千秀公司的书面同意。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.9 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得通过非千秀公司开发、授权或认可的三方兼容软件、系统登录或使用千秀互动直播及服务，用户不得针对千秀互动直播使用非千秀公司开发、授权或认证的插件和外挂；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.10</span><span style=\"font-size:12.0pt;font-family:宋体;\">不得使用任何手段删除、修改本软件展示的信息，不得对千秀互动直播进行反向工程、反向汇编、反向编译等。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.11 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得利用本软件发布、传播违法信息、虚假信息，损害任何第三方的名誉权、隐私权、肖像权、知识产权等合法权益的信息，或者发布垃圾信息，广告信息，骚扰信息。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.12 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得进行任何危害计算机网络安全的行为，包括但不限于：使用未经许可的数据或进入未经许可的服务器<span>/</span>账 号；未经允许进入公众计算机网络或者他人计算机系统并删除、修改、增加存储信息；未经许可，企图探查、扫描、测试本软件系统或网络的弱点或其它实施破坏网\r\n络安全的行为；企图干涉、破坏本软件系统或网站的正常运行，故意传播恶意程序或病毒以及其他破坏干扰正常网络信息服务的行为；伪造<span>TCP/IP</span>数据包名称或部分名称。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.13 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得修改或伪造软件作品运行中的指令、数据、数据包，增加、删减、变动软件的功能或运行效果，不得将用于上述用途的软件通过信息网络向公众传播或者演示。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.14 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得将本软件及提供的服务用于核设施运行、生命维持或其他会使人类及其财产处于危险之中的重大设备。用户理解本软件及千秀公司提供的服务并非为以上目的而设计，如果因为软件和服务的原因导致以上操作失败而带来的人员伤亡、严重的财产损失和环境破坏，千秀互动直播将不承担任何责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.15</span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得以任何不合法的方式、为任何不合法的目的、或以任何与本协议不一致的方式使用本软件和提供的其他服务。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">3.16 </span><span style=\"font-size:12.0pt;font-family:宋体;\">若有发现用户存在上述行为的，所有的责任由用户承担。同时千秀互动直播可在不通知用户的情况下中止全部或部分服务。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4</span><span style=\"font-size:12.0pt;font-family:宋体;\">、免责声明<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户理解并同意，在使用千秀互动直播服 务可能存在来自任何他人的包括威胁性的、诽谤性的、令人反感的或非法的内容或行为或对他人权利的侵犯（包括知识产权）的匿名或冒名的信息的风险，用户须承\r\n担以上风险，千秀公司对服务不作担保，不论是明确的或隐含的，包括所有有关信息真实性、所有权和非侵权性的默示担保和条件，对因此导致任何因用户不正当或 非法使用服务产生的直接、间接、偶然、特殊及后续的损害，不承担任何责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户使用千秀互动直播服务必须遵守国家有关法律和政策等，维护国家利益，保护国家安全，并遵守本条款，对于用户违法或违反本协议的使用<span>(</span>包括但不限于言论发表、传送等<span>)</span>而引起的一切责任，由用户负全部责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播以及千秀互动直播网站的服务同大多数因特网产品一样，易受到各种安全问题的困扰，包括但不限于：<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>1</span>）透露详细个人资料，被不法分子利用，造成现实生活中的骚扰；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>2</span>）哄骗、破译密码；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>3</span>）下载安装的其它软件中含有计算机病毒，威胁到个人计算机上信息和数据的安全，继而威胁对本服务的使用。对于发生上述情况的，用户应当自行承担责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.4 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户须明白，千秀互动直播以及千秀互动直播网站为了服务整体演示的需要，有权在公告通知后修改或中断、中止或终止服务的权利，而无须向第三方负责或承担任何赔偿责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.5 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户理解，互联网技术的不稳定性，可能导致政府政策管制、病毒入侵、黑客攻击、服务器系统崩溃或者其他现有技术无法解决的风险发生可能导致千秀互动直播服务中断或账号、道具损失，对此非人为因素引起的损失，千秀互动直播不承担责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.6 </span><span style=\"font-size:12.0pt;font-family:宋体;\">在任何情况下，千秀公司不对因不可抗力导致的用户在使用千秀互动直播服务过程中遭受的损失承担责任。该等不可抗力事件包括但不限于国家法律、法规、政策及国家机关的命令或者其它的诸如地震、水灾、雪灾、火灾、海啸、台风、罢工、战争等不可预测、不可避免且不可克服的事件。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.7 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播以及千秀互动直播网站不对发布在本网站上的广告的产品效果、宣传、信息准确性负责，用户在接触这些广告时自行判断。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.8 </span><span style=\"font-size:12.0pt;font-family:宋体;\">对加载在千秀互动直播以及千秀互动直播网站上的其他公司的产品，以及使用这些产品所呈现的信息的真实性、准确性、知识产权，千秀互动直播均不承担责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.9 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀公司未授权用户从任何第三方通过购买、接受赠与或者其他的方式获得账号、虚拟代币、装备等，千秀公司不对第三方交易的行为负责，并且不受理因任何第三方交易发生纠纷而带来的申诉。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.10 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播及千秀互动直播网站可能因软件<span>BUG</span>、 版本更新缺陷、第三方病毒攻击或其他任何因素导致用户的账号、虚拟货币、用户数据发生异常。在数据异常的原因未得到查明前，千秀公司有权暂时冻结该账号；\r\n若查明数据异常为非正常行为，千秀公司有权恢复账号数据至异常发生前的原始状态（包括向第三方追回被转移数据），且千秀公司无须向用户承担任何责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">4.11 </span><span style=\"font-size:12.0pt;font-family:宋体;\">不同操作系统之间存在不互通的客观情况，该情况并非千秀互动直播造成，由此可能导致用户在某一操作系统中的充值和数据不能顺利转移到另一操作系统中。由于用户在不同系统进行切换造成的充值损失和数据丢失风险应由用户自行承担，不得要求千秀公司承担任何责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">5</span><span style=\"font-size:12.0pt;font-family:宋体;\">、收费服务<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">5.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播软件及网站的部分应用可能涉及付费，相关的资费政策（包括但不限于收费标准、收费方式、购买方式等）千秀公司将会在相关服务界面说明。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">5.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀公司有权决定所提供服务的资费标准和收费方式。用户若使用千秀互动直播的收费服务项目时，应当按照千秀互动直播的要求支付相应的费用。千秀公司保留变更收费标准、收费的软件功能、收费对象及收费时间等权利。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6</span><span style=\"font-size:12.0pt;font-family:宋体;\">、知识产权<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播及千秀互动直播网站内的文字、图片、视频、音频、软件等元素，千秀公司及千秀互动直播的服务标志、标识、商标以及专利权等知识产权，全部归千秀公司享有。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户不得对千秀互动直播及相关附属组件，千秀互动直播网站相关网页、应用等产品进行反向工程、反向汇编、反向编译等。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户使用千秀互动直播服务只能在本协议以及相应的授权许可协议授权的范围使用千秀互动直播知识产权，未经授权超范围使用的构成对千秀互动直播的侵权。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.4 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户在使用千秀互动直播软件或千秀公司提供的软件拍摄或上传的音频、视频、文字以及表演等内容的知识产权归千秀公司所有。千秀公司可将上述内容在千秀公司旗下的服务平台上使用，可再次编辑后使用，也可以授权给合作方使用。未经千秀公司书面同意，用户不得在千秀互动直播或千秀公司旗下其他平台之外的平台传播。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.5 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户在使用千秀互动直播服务时发布上传的文字、图片、视频、软件以及表演等用户原创的信息，此部分信息的知识产权归用户，但用户的发表、上传行为是对千秀互动直播的授权，用户确认其发表、上传的信息非独占性、永久性、免费用的授权，该授权可转授权。千秀互动直播可将前述信息在千秀公司旗下的服务平台上使用，可再次编辑后使用，也可以由千秀互动直播授权给合作方使用。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">6.6 </span><span style=\"font-size:12.0pt;font-family:宋体;\">若千秀互动直播及千秀互动直播网站内的信息以及其他用户上传、存储、传播的信息有侵犯的用户或第三人的知识产权的，千秀互动直播提供投诉通道。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7</span><span style=\"font-size:12.0pt;font-family:宋体;\">、隐私保护<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">请用户注意勿在使用千秀互动直播服务中透露自己的各类财产账户、银行卡、信用卡、第三方支付账户及对应密码等重要资料，否则由此带来的任何损失由用户自行承担。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户在使用千秀互动直播服务时不可将自认为隐私的信息发表、上传至千秀互动直播及千秀互动直播网站，也不可将该等信息通过千秀互动直播的服务传播给其他人，若用户的行为引起的隐私泄漏，由用户承担责任。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀互动直播在提供服务时可能会搜集用户信息，千秀互动直播会明确告知用户，通常信息仅限于用户姓名、性别、年龄、出生日期、身份证号、家庭住址、教育程度、公司情况、所属行业、兴趣爱好等。千秀互动直播承诺不将搜集的用户信息向第三人泄露。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.4 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户同意并授权千秀公司为履行本协议之目的收集用户的用户信息，这些信息包括用户在实名注册系统中注册的信息、用户账号下的数据以及其他用户在使用千秀互动直播服务的过程中向千秀公司提供或千秀公司基于安全、用户体验优化等考虑而需收集的信息，千秀公司对用户的用户信息的收集将遵循相关法律的规定。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.5 </span><span style=\"font-size:12.0pt;font-family:宋体;\">用户充分理解并同意：为更好地向用户提供千秀互动直播服务，千秀公司可以将用户的用户信息提交给其关联公司，且千秀公司有权自行或通过第三方对用户的用户信息进行整理、统计、分析及利用。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.6 </span><span style=\"font-size:12.0pt;font-family:宋体;\">千秀公司保证不对外公开或向任何第三方提供用户的个人信息，但是存在下列情形之一的除外：<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>1</span>）公开或提供相关信息之前获得用户许可的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>2</span>）根据法律或政策的规定而公开或提供的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>3</span>）只有公开或提供用户的个人信息，才能提供用户需要的千秀互动直播服务的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>4</span>）根据国家权力机关要求公开或提供的；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">（<span>5</span>）根据本协议其他条款约定而公开或提供的。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.7 </span><span style=\"font-size:12.0pt;font-family:宋体;\">由于下述原因导致用户信息泄露，千秀公司不承担责任：<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.7.1</span><span style=\"font-size:12.0pt;font-family:宋体;\">用户将用户密码告知他人或与他人共享注册账户，由此导致的任何个人信息的泄漏，或其他非因千秀互动直播原因导致的个人信息的泄漏；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.7.2</span><span style=\"font-size:12.0pt;font-family:宋体;\">任何由于黑客攻击、电脑病毒侵入造成的信息泄漏；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.7.3</span><span style=\"font-size:12.0pt;font-family:宋体;\">因不可抗力导致的信息泄漏；<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">7.8</span><span style=\"font-size:12.0pt;font-family:宋体;\">更隐私权保护政策，请用户查看《千秀互动直播隐私条款》<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">8</span><span style=\"font-size:12.0pt;font-family:宋体;\">、其他条款<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">8.1 </span><span style=\"font-size:12.0pt;font-family:宋体;\">本协议条款的签订、解释以及争议的解决均适用中华人民共和国法律。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">8.2 </span><span style=\"font-size:12.0pt;font-family:宋体;\">本协议的签署地点为福州，若用户与千秀公司发生法争议的，双方同意将争议提交福州法院诉讼解决。<span></span></span> \r\n</p>\r\n<p class=\"MsoNormal\" style=\"text-align:left;\" align=\"left\">\r\n	<span style=\"font-size:12.0pt;font-family:宋体;\">8.3 </span><span style=\"font-size:12.0pt;font-family:宋体;\">本协议在千秀互动直播登录页面及在千秀互动直播网站（<span>www.fanwe.com</span>）上展示，对用户和千秀公司具有法律约束力，用户在登录页面接受本协议以及访问本网站的行为均视为对本协议的接受，对用户具有法律约束力。<span></span></span> \r\n</p>\r\n<p>\r\n	<br />\r\n</p>\r\n<p>\r\n	<br />\r\n</p>', '8', '1467569785', '1471911311', '0', '1', '', '0', '0', '0', '11', '', '', '', '', '', '主播协议', '1', '1', '', '千秀众筹', '主播协议');
INSERT INTO `%DB_PREFIX%article` VALUES ('20', '用户隐私政策', '<div style=\"text-align:center;\">\r\n	<span style=\"line-height:1.5;font-size:18px;\"><strong>引言</strong></span> \r\n</div>\r\n<span>我们重视您的隐私。您在使用我们的服务（包括我们的网站http://www.fanwe.com）时，我们可能会收集和使用您的信息。我们希望通过本《隐私政策》向您说明在您使用我们的服务时，我们如何收集、使用、储存和分享这些信息，以及我们为您提供的访问、更新、控制和保护这些信息的方式。本《隐私政策》与您所使用的我们服务息息相关，我们也希望您能够仔细阅读，并在需要时，按照本《隐私政策》的指引，作出您认为适当的选择。本《隐私政策》之中涉及的相关技术词汇，我们尽量以简明扼要的表述向您解释，并提供了进一步说明的链接，以便您的理解。</span><br />\r\n<span>您使用或继续使用我们的服务，都表示您同意我们按照本《隐私政策》收集、使用、储存和分享您的信息。</span><br />\r\n<span>如您对本《隐私政策》或与本《隐私政策》相关的事宜有任何问题，请致电400-118-5335&nbsp;与我们联系。</span><br />\r\n<span>我们收集的信息</span><br />\r\n<p>\r\n	我们提供服务时，可能会收集、储存和使用下列与您有关的信息。如果您不提供相关信息，可能无法注册成为我们的用户、享受我们提供的某些服务，或者即便我们可以继续向您提供一些服务，也无法达到该服务拟达到的效果。&nbsp;\r\n</p>\r\n<p>\r\n	您提供的信息<br />\r\n您在注册我们的账户或使用我们的服务时，向我们提供的相关个人信息，例如电话号码、电子邮件等；<br />\r\n您通过我们的服务向其他方提供的共享信息，以及您使用我们的服务时所储存的信息。<br />\r\n其他方分享的您的信息<br />\r\n其他方使用我们的服务时所提供有关您的共享信息。<br />\r\n我们获取的您的信息<br />\r\n您使用我们服务时我们可能收集如下信息：<br />\r\n日志信息指您使用我们服务时，系统可能会通过cookies、web beacon或其他方式自动采集的技术信息，包括：<br />\r\n设备或软件信息，例如您的移动设备、网页浏览器或您用于接入我们的服务的其他程序所提供的配置信息、您的IP地址和您的移动设备所用的版本和设备识别码；<br />\r\n您在使用我们服务时搜索和浏览的信息，例如您使用的网页搜索词语、访问的社交媒体页面url地址，以及您在使用我们服务时浏览或要求提供的其他信息和内容详情；\r\n</p>\r\n<p>\r\n	向您提供我们的服务；<br />\r\n实现“我们如何使用您的信息”部分所述目的；<br />\r\n理解、维护和改善我们的服务。<br />\r\n如我们或我们的关联公司与任何上述第三方分享您的个人信息，我们将努力确保该等第三方在使用您的个人信息时遵守本《隐私政策》及我们 要求其遵守的其他适当的保密和安全措施。<br />\r\n随着我们业务的持续发展，我们以及我们的关联公司有可能进行合并、收购、资产转让或类似的交易，而您的个人信息有可能作为此类交易的一部分而被转移。我们将在您的个人信息转移前通知您。<br />\r\n我们或我们的关联公司还可能为以下需要保留、保存或披露您的个人信息：<br />\r\n遵守适用的法律法规；<br />\r\n遵守法院命令或其他法律程序的规定；<br />\r\n遵守相关政府机关的要求；<br />\r\n我们认为为遵守适用的法律法规、维护社会公共利益、或保护我们或我们的集团公司、我们的客户、其他用户或雇员的人身和财产安全或合法权益所合理必需的。 我们如何保留、储存和保护您的信息<br />\r\n我们仅在本《隐私政策》所述目的所必需期间和法律法规要求的时限内保留您的个人信息。 我们使用各种安全技术和程序，以防信息的丢失、不当使用、未经授权阅览或披露。例如，在某些服务中，我们将利用加密技术（例如SSL）来保护您向我们提供的个人信息。但请您谅解，由于技术的限制以及风险防范的局限，即便我们已经尽量加强安全措施，也无法始终保证信息百分之百的安全。您需要了解，您接入我们的服务所用的系统和通讯网络，有可能因我们可控范围外的情况而发生问题。\r\n</p>\r\n<div>\r\n	<br />\r\n</div>\r\n<br />\r\n<span>有关您曾使用的移动应用（APP）和其他软件的信息，以及您曾经使用该等移动应用和软件的信息；</span><br />\r\n<span>您通过我们的服务进行通讯的信息，例如曾通讯的账号，以及通讯时间、数据和时长；</span><br />\r\n<span>您通过我们的服务分享的内容所包含的信息（元数据），例如拍摄或上传的共享照片或录像的日期、时间或地点等。</span><br />\r\n<span>位置信息指您开启设备定位功能并使用我们基于位置提供的相关服务时，我们收集的有关您位置的信息，包括：</span> \r\n<div>\r\n	&nbsp;您通过具有定位功能的移动设备使用我们的服务时，我们通过GPS或WiFi等方式收集的您的地理位置信息；<br />\r\n您或其他用户提供的包含您所处地理位置的实时信息，例如您提供的账户信息中包含的您所在地区信息，您或其他人上传的显示您当前或曾经所处地理位置的共享信息，例如您或其他人共享的照片包含的地理标记信息；<br />\r\n您可以通过关闭定位功能随时停止我们对您的地理位置信息的收集。<br />\r\n我们如何使用您的信息<br />\r\n我们可能将在向您提供服务的过程之中所收集的信息用作下列用途：<br />\r\n向您提供服务；<br />\r\n在我们提供服务时，用于身份验证、客户服务、安全防范、诈骗监测、存档和备份用途，确保我们向您提供的产品和服务的安全性；<br />\r\n帮助我们设计新服务，改善我们现有服务；<br />\r\n使我们更加了解您如何接入和使用我们的服务，从而针对性地回应您的个性化需求，例如语言设定、位置设定、个性化的帮助服务和指示，或&nbsp;对您和其他使用我们服务的用户作出其他方面的回应；<br />\r\n向您提供与您更加相关的广告以替代普遍投放的广告；<br />\r\n评估我们服务中的广告和其他促销及推广活动的效果，并加以改善；<br />\r\n软件认证或管理软件升级；<br />\r\n让您参与有关我们产品和服务的调查。<br />\r\n</div>\r\n<div>\r\n	&nbsp;同意的其他用途，在符合相关法律法规的前提下，我们可能将通过我们的某一项服务所收集的个人信息，以汇集信息或者个性化的方式，用于我们的其他服务。例如，在您使用我们的一项服务时所收集的您的个人信息，可能在另一服务中用于向您提供特定内容或向您展示与您相关的、而非普遍推送的信息。如我们在相关服务之中提供了相应选项，您也可以主动要求我们将您在该服务所提供和储存的个人信息用于我们的其他服务。<br />\r\n针对某些特定服务的特定隐私政策将更具体地说明我们在该等服务中如何使用您的信息。<br />\r\n如何访问和控制您的信息<br />\r\n我们将尽量采取适当的技术手段，保证您可以访问、更新和更正您的注册信息或使用我们的服务时提供的其他个人信息。在访问、更新、更正和删除您的个人信息时，我们可能会要求您进行身份验证，以保障您的账户安全。<br />\r\n我们如何分享您的信息<br />\r\n除以下情形外，未经您同意，我们以及我们的关联公司不会与任何第三方分享您的个人信息：<br />\r\n我们以及我们的关联公司可能将您的个人信息与我们的关联公司、合作伙伴及第三方服务供应商、承包商及代理（例如代表我们发出电子邮件或推送通知的通讯服务提供商、以及为我们提供位置数据的地图服务供应商）分享（他们可能并非位于您所在法域），用作下列用途：<br />\r\n</div>\r\n<span>向您提供我们的服务；</span><br />\r\n<span>实现“我们如何使用您的信息”部分所述目的；</span><br />\r\n<span>理解、维护和改善我们的服务。</span><br />\r\n<span>如我们或我们的关联公司与任何上述第三方分享您的个人信息，我们将努力确保该等第三方在使用您的个人信息时遵守本《隐私政策》及我们 要求其遵守的其他适当的保密和安全措施。</span><br />\r\n<span>随着我们业务的持续发展，我们以及我们的关联公司有可能进行合并、收购、资产转让或类似的交易，而您的个人信息有可能作为此类交易的一部分而被转移。我们将在您的个人信息转移前通知您。</span><br />\r\n<span>我们或我们的关联公司还可能为以下需要保留、保存或披露您的个人信息：</span><br />\r\n<span>遵守适用的法律法规；</span><br />\r\n<span>遵守法院命令或其他法律程序的规定；</span><br />\r\n<span>遵守相关政府机关的要求；</span><br />\r\n<span>我们认为为遵守适用的法律法规、维护社会公共利益、或保护我们或我们的集团公司、我们的客户、其他用户或雇员的人身和财产安全或合法权益所合理必需的。 我们如何保留、储存和保护您的信息</span><br />\r\n<span>我们仅在本《隐私政策》所述目的所必需期间和法律法规要求的时限内保留您的个人信息。 我们使用各种安全技术和程序，以防信息的丢失、不当使用、未经授权阅览或披露。例如，在某些服务中，我们将利用加密技术（例如SSL）来保护您向我们提供的个人信息。但请您谅解，由于技术的限制以及风险防范的局限，即便我们已经尽量加强安全措施，也无法始终保证信息百分之百的安全。您需要了解，您接入我们的服务所用的系统和通讯网络，有可能因我们可控范围外的情况而发生问题。</span><br />\r\n<div>\r\n	有关共享信息的提示<br />\r\n我们的多项服务可让您不仅与您的社交网络、也与使用该服务的所有用户公开分享您的相关信息，例如，您在我们的服务中所上传或发布的信息（包括您公开的个人信息、您建立的名单）、您对其他人上传或发布的信息作出的回应，以及包括与这些信息有关的位置数据和日志信息。使用我们服务的其他用户也有可能分享与您有关的信息（包括位置数据和日志信息）。特别是我们的社交媒体服务，是专为使您可以与世界各地的用户共享信息而设计，从而使共享信息可实时、广泛的传递。只要您不删除共享信息，有关信息便一直留存在公众领域；即使您删除共享信息，有关信息仍可能由其他用户或不受我们控制的非关联第三方独立地缓存、复制或储存，或由其他用户或该等第三方在公众领域保存。<br />\r\n因此，请您认真考虑您通过我们的服务上传、发布和交流的信息内容。在一些情况下，您可通过我们某些服务的隐私设定来控制有权浏览您的共享信息的用户范围。如您要求从我们的服务中删除您的个人信息，请通过该等特别服务条款提供的方式操作。<br />\r\n</div>', '2', '1470874836', '1471911322', '0', '1', '', '0', '0', '0', '1', '', '', '', '', '', '', '1', '1', '', '麻辣TV', '隐私政策');
INSERT INTO `%DB_PREFIX%article` VALUES ('21', '关于我们', '<span style=\"color:#000000;\"><strong>啊，一个聚集了明星</strong>、网红、校花校草、高颜值帅哥美女，有生活趣事，花边新闻，各类发布会等内容的手机直播社交软件。</span><br />\r\n<span style=\"color:#000000;\"> 数不尽的大咖、小鲜肉、美女妹纸等你来约，全天候服务用户，但这里拒绝色情和低俗，给大家一个舒适安逸的直播环境.</span><br />', '10', '1470874873', '1471912733', '0', '1', '', '0', '1', '0', '2', '麻辣TV', '麻辣TV', '麻辣TV', '', '', '', '1', '1', '', '麻辣TV', '关于我们');

-- ----------------------------
-- Table structure for `%DB_PREFIX%article_cate`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%article_cate`;
CREATE TABLE `%DB_PREFIX%article_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '分类名称',
  `brief` varchar(255) NOT NULL COMMENT '分类简介(备用字段)',
  `pid` int(11) NOT NULL COMMENT '父ID，程序分类可分二级',
  `is_effect` tinyint(4) NOT NULL COMMENT '有效性标识',
  `is_delete` tinyint(4) NOT NULL COMMENT '删除标识',
  `type_id` tinyint(1) NOT NULL COMMENT '型 0:普通文章（可通前台分类列表查找到） 1.帮助文章（用于前台页面底部的站点帮助） 2.公告文章（用于前台页面公告模块的调用） 3.系统文章（自定义的一些文章，需要前台自定义一些入口链接到该文章） 所属该分类的所有文章类型与分类一致',
  `sort` int(11) NOT NULL,
  `seo_title` varchar(255) NOT NULL COMMENT 'SEO标题',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`) USING BTREE,
  KEY `type_id` (`type_id`) USING BTREE,
  KEY `sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%article_cate
-- ----------------------------
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('2', '隐私政策', '放的歌0000', '10', '1', '0', '0', '1', 'yszc');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('8', '主播协议', '主播协议', '1', '1', '0', '0', '5', 'zbxy');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('10', '关于我们', '麻辣TV是一款全民移动直播软件，由广州政和科技有限公司研发。', '0', '1', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%article_cate` VALUES ('17', '公告', '', '0', '1', '0', '2', '5', '公告');

-- ----------------------------
-- Table structure for `%DB_PREFIX%authent_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%authent_list`;
CREATE TABLE `%DB_PREFIX%authent_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '认证名称',
  `icon` varchar(255) NOT NULL COMMENT '认证图标',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='认证名称列表';

-- ----------------------------
-- Records of %DB_PREFIX%authent_list
-- ----------------------------
INSERT INTO `%DB_PREFIX%authent_list` VALUES ('5', '美女', 'http://image.qiankeep.com/public/attachment/201608/10/11/57aa9ad7942cf.png', '5');
INSERT INTO `%DB_PREFIX%authent_list` VALUES ('6', '帅哥', 'http://image.qiankeep.com/public/attachment/201608/14/10/57afd1be32771.png', '6');

-- ----------------------------
-- Table structure for `%DB_PREFIX%black`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%black`;
CREATE TABLE `%DB_PREFIX%black` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '设置黑名单时间',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `black_user_id` int(11) NOT NULL COMMENT '被设置的用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%black
-- ----------------------------
INSERT INTO `%DB_PREFIX%black` VALUES ('252', '1471317573', '100272', '360');
INSERT INTO `%DB_PREFIX%black` VALUES ('254', '1471396248', '100310', '8401');
INSERT INTO `%DB_PREFIX%black` VALUES ('257', '1471479066', '100334', '100339');
INSERT INTO `%DB_PREFIX%black` VALUES ('258', '1471484363', '100308', '100232');
INSERT INTO `%DB_PREFIX%black` VALUES ('259', '1471568545', '100389', '100247');

-- ----------------------------
-- Table structure for `%DB_PREFIX%conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%conf`;
CREATE TABLE `%DB_PREFIX%conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `group_id` int(11) NOT NULL,
  `input_type` tinyint(1) NOT NULL COMMENT '配置输入的类型 0:文本输入 1:下拉框输入 2:图片上传 3:编辑器',
  `value_scope` text NOT NULL COMMENT '取值范围',
  `is_effect` tinyint(1) NOT NULL,
  `is_conf` tinyint(1) NOT NULL COMMENT '是否可配置 0: 可配置  1:不可配置',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%conf
-- ----------------------------
INSERT INTO `%DB_PREFIX%conf` VALUES ('1', 'DEFAULT_ADMIN', 'admin', '1', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('2', 'URL_MODEL', '0', '1', '1', '0,1', '1', '1', '3');
INSERT INTO `%DB_PREFIX%conf` VALUES ('3', 'AUTH_KEY', 'fanwe', '1', '0', '', '1', '0', '4');
INSERT INTO `%DB_PREFIX%conf` VALUES ('4', 'TIME_ZONE', '8', '1', '1', '0,8', '1', '1', '1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('5', 'ADMIN_LOG', '1', '1', '1', '0,1', '0', '1', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('6', 'DB_VERSION', '1.70', '0', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('7', 'DB_VOL_MAXSIZE', '8000000', '1', '0', '', '1', '1', '11');
INSERT INTO `%DB_PREFIX%conf` VALUES ('8', 'WATER_MARK', 'http://liveimage.fanwe.net/public/attachment/201607/22/11/579193a182480.png', '2', '2', '', '0', '1', '48');
INSERT INTO `%DB_PREFIX%conf` VALUES ('10', 'BIG_WIDTH', '500', '2', '0', '', '0', '0', '49');
INSERT INTO `%DB_PREFIX%conf` VALUES ('11', 'BIG_HEIGHT', '500', '2', '0', '', '0', '0', '50');
INSERT INTO `%DB_PREFIX%conf` VALUES ('12', 'SMALL_WIDTH', '200', '2', '0', '', '0', '0', '51');
INSERT INTO `%DB_PREFIX%conf` VALUES ('13', 'SMALL_HEIGHT', '200', '2', '0', '', '0', '0', '52');
INSERT INTO `%DB_PREFIX%conf` VALUES ('14', 'WATER_ALPHA', '75', '2', '0', '', '0', '1', '53');
INSERT INTO `%DB_PREFIX%conf` VALUES ('15', 'WATER_POSITION', '3', '2', '1', '1,2,3,4,5', '0', '1', '54');
INSERT INTO `%DB_PREFIX%conf` VALUES ('16', 'MAX_IMAGE_SIZE', '10000000', '2', '0', '', '0', '1', '55');
INSERT INTO `%DB_PREFIX%conf` VALUES ('17', 'ALLOW_IMAGE_EXT', 'jpg,gif,png', '2', '0', '', '0', '1', '56');
INSERT INTO `%DB_PREFIX%conf` VALUES ('18', 'BG_COLOR', '#ffffff', '2', '0', '', '0', '0', '57');
INSERT INTO `%DB_PREFIX%conf` VALUES ('19', 'IS_WATER_MARK', '1', '2', '1', '0,1', '0', '1', '58');
INSERT INTO `%DB_PREFIX%conf` VALUES ('20', 'TEMPLATE', 'default', '1', '0', '', '0', '1', '17');
INSERT INTO `%DB_PREFIX%conf` VALUES ('24', 'SMS_ON', '1', '5', '1', '0,1', '1', '1', '78');
INSERT INTO `%DB_PREFIX%conf` VALUES ('26', 'PUBLIC_DOMAIN_ROOT', '', '2', '0', '', '0', '1', '59');
INSERT INTO `%DB_PREFIX%conf` VALUES ('27', 'APP_MSG_SENDER_OPEN', '0', '1', '1', '0,1', '1', '0', '9');
INSERT INTO `%DB_PREFIX%conf` VALUES ('28', 'ADMIN_MSG_SENDER_OPEN', '0', '1', '1', '0,1', '1', '0', '10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('29', 'GZIP_ON', '1', '1', '1', '0,1', '1', '1', '2');
INSERT INTO `%DB_PREFIX%conf` VALUES ('30', 'CACHE_ON', '0', '1', '1', '0,1', '0', '1', '7');
INSERT INTO `%DB_PREFIX%conf` VALUES ('31', 'EXPIRED_TIME', '300', '1', '0', '', '1', '1', '5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('32', 'TMPL_DOMAIN_ROOT', '', '2', '0', '0', '0', '0', '62');
INSERT INTO `%DB_PREFIX%conf` VALUES ('33', 'CACHE_TYPE', 'File', '1', '1', 'File,Xcache,Memcached', '0', '1', '7');
INSERT INTO `%DB_PREFIX%conf` VALUES ('34', 'MEMCACHE_HOST', '127.0.0.1:11211', '1', '0', '', '1', '1', '8');
INSERT INTO `%DB_PREFIX%conf` VALUES ('35', 'IMAGE_USERNAME', '', '2', '0', '', '0', '1', '60');
INSERT INTO `%DB_PREFIX%conf` VALUES ('36', 'IMAGE_PASSWORD', '', '2', '4', '', '0', '1', '61');
INSERT INTO `%DB_PREFIX%conf` VALUES ('38', 'SEND_SPAN', '1', '1', '0', '', '1', '0', '85');
INSERT INTO `%DB_PREFIX%conf` VALUES ('39', 'TMPL_CACHE_ON', '0', '1', '1', '0,1', '0', '1', '6');
INSERT INTO `%DB_PREFIX%conf` VALUES ('40', 'DOMAIN_ROOT', '', '1', '0', '', '1', '0', '10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('41', 'COOKIE_PATH', '/', '1', '0', '', '0', '1', '10');
INSERT INTO `%DB_PREFIX%conf` VALUES ('42', 'SITE_NAME', '千秀直播', '1', '0', '', '1', '1', '1');
INSERT INTO `%DB_PREFIX%conf` VALUES ('43', 'INTEGRATE_CFG', '', '0', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('44', 'INTEGRATE_CODE', '', '0', '0', '', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('176', 'SITE_LICENSE', '千秀直播-福建千秀信息科技有限公司版权所有', '1', '0', '', '1', '1', '22');
INSERT INTO `%DB_PREFIX%conf` VALUES ('177', 'PROMOTE_MSG_LOCK', '0', '0', '0', '', '0', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('178', 'PROMOTE_MSG_PAGE', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('180', 'USER_VERIFY', '2', '4', '1', '0,1,2,3,4,5,6', '0', '1', '63');
INSERT INTO `%DB_PREFIX%conf` VALUES ('181', 'INVITE_REFERRALS', '20', '4', '0', '', '0', '1', '67');
INSERT INTO `%DB_PREFIX%conf` VALUES ('182', 'INVITE_REFERRALS_TYPE', '1', '4', '1', '0,1', '0', '1', '68');
INSERT INTO `%DB_PREFIX%conf` VALUES ('183', 'USER_MESSAGE_AUTO_EFFECT', '0', '4', '1', '0,1', '0', '1', '64');
INSERT INTO `%DB_PREFIX%conf` VALUES ('184', 'BUY_INVITE_REFERRALS', '20', '4', '0', '', '0', '1', '67');
INSERT INTO `%DB_PREFIX%conf` VALUES ('185', 'REFERRAL_IP_LIMI', '0', '4', '1', '0,1', '0', '1', '71');
INSERT INTO `%DB_PREFIX%conf` VALUES ('186', 'REFERRAL_LIMIT', '1', '4', '0', '', '0', '1', '69');
INSERT INTO `%DB_PREFIX%conf` VALUES ('190', 'MAIL_SEND_PAYMENT', '1', '5', '1', '0,1', '1', '0', '75');
INSERT INTO `%DB_PREFIX%conf` VALUES ('191', 'REPLY_ADDRESS', 'info@fanwe.com', '5', '0', '', '1', '0', '77');
INSERT INTO `%DB_PREFIX%conf` VALUES ('193', 'MAIL_ON', '1', '5', '1', '0,1', '1', '0', '72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('198', 'EDM_ON', '0', '5', '1', '0,1', '0', '1', '86');
INSERT INTO `%DB_PREFIX%conf` VALUES ('199', 'EDM_USERNAME', '', '5', '0', '', '0', '1', '87');
INSERT INTO `%DB_PREFIX%conf` VALUES ('200', 'EDM_PASSWORD', '', '5', '4', '', '0', '1', '88');
INSERT INTO `%DB_PREFIX%conf` VALUES ('260', 'MOBILE_OPEN', '1', '4', '1', '0,1', '0', '1', '100');
INSERT INTO `%DB_PREFIX%conf` VALUES ('262', 'NETWORK_FOR_RECORD', '闽ICP备10206706号-7', '1', '0', '', '1', '1', '201');
INSERT INTO `%DB_PREFIX%conf` VALUES ('263', 'QR_CODE', './public/attachment/201603/15/15/56e7b8cfeb532.png', '3', '2', '', '0', '1', '202');
INSERT INTO `%DB_PREFIX%conf` VALUES ('265', 'SQL_CHECK', '1', '1', '1', '0,1', '1', '0', '265');
INSERT INTO `%DB_PREFIX%conf` VALUES ('268', 'INVEST_PAY_SEND_STATUS', '0', '5', '1', '0,2', '0', '1', '3');
INSERT INTO `%DB_PREFIX%conf` VALUES ('270', 'INVEST_PAID_SEND_STATUS', '0', '5', '1', '0,2', '0', '1', '5');
INSERT INTO `%DB_PREFIX%conf` VALUES ('275', 'SCORE_TRADE_NUMBER', '1000', '4', '0', '', '0', '1', '72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('276', 'BUY_PRESEND_SCORE_MULTIPLE', '1', '4', '0', '', '0', '1', '72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('277', 'BUY_PRESEND_POINT_MULTIPLE', '1', '4', '0', '', '0', '1', '72');
INSERT INTO `%DB_PREFIX%conf` VALUES ('279', 'KF_PHONE', '400-118-5335', '3', '0', '', '0', '1', '279');
INSERT INTO `%DB_PREFIX%conf` VALUES ('280', 'KF_QQ', '800005515', '3', '0', '', '0', '1', '280');
INSERT INTO `%DB_PREFIX%conf` VALUES ('282', 'WORK_TIME', '09:00-18:00', '3', '0', '', '0', '1', '69');
INSERT INTO `%DB_PREFIX%conf` VALUES ('283', 'IDENTIFY_POSITIVE', '1', '4', '1', '0,1', '0', '1', '283');
INSERT INTO `%DB_PREFIX%conf` VALUES ('284', 'IDENTIFY_NAGATIVE', '1', '4', '1', '0,1', '0', '1', '284');
INSERT INTO `%DB_PREFIX%conf` VALUES ('285', 'BUSINESS_LICENCE', '1', '4', '1', '0,1', '0', '1', '285');
INSERT INTO `%DB_PREFIX%conf` VALUES ('286', 'BUSINESS_CODE', '1', '4', '1', '0,1', '0', '1', '286');
INSERT INTO `%DB_PREFIX%conf` VALUES ('287', 'BUSINESS_TAX', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('288', 'VIRSUAL_NUM', '0', '4', '0', '', '1', '0', '288');
INSERT INTO `%DB_PREFIX%conf` VALUES ('290', 'WX_MSG_LOCK', '1', '0', '0', '', '0', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('291', 'USER_VERIFY_STATUS', '0', '4', '1', '0,1', '1', '1', '291');
INSERT INTO `%DB_PREFIX%conf` VALUES ('293', 'PAYPASS_STATUS', '0', '4', '1', '0,1', '1', '0', '293');
INSERT INTO `%DB_PREFIX%conf` VALUES ('294', 'USER_SEND_VERIFY_TIME', '300', '4', '0', '', '1', '1', '294');
INSERT INTO `%DB_PREFIX%conf` VALUES ('295', 'URL_NAME', 'm.php', '1', '0', '', '1', '1', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('302', 'USER_SUBMIT_TIME', '5', '4', '0', '0', '1', '1', '302');
INSERT INTO `%DB_PREFIX%conf` VALUES ('305', 'BAIDU_MAP_APPKEY', '', '1', '0', '', '0', '1', '265');
INSERT INTO `%DB_PREFIX%conf` VALUES ('306', 'CREDIT_REPORT', '1', '4', '1', '0,1', '0', '1', '306');
INSERT INTO `%DB_PREFIX%conf` VALUES ('307', 'HOUSING_CERTIFICATE', '1', '4', '1', '0,1', '0', '1', '307');
INSERT INTO `%DB_PREFIX%conf` VALUES ('311', 'IS_SMS_DIRECT', '1', '5', '1', '0,1', '1', '0', '0');
INSERT INTO `%DB_PREFIX%conf` VALUES ('313', 'IS_WX_REFUND', '1', '1', '1', '0,1', '1', '1', '500');
INSERT INTO `%DB_PREFIX%conf` VALUES ('314', 'IS_LOCK_DEAL', '0', '1', '1', '0,1', '0', '0', '265');
INSERT INTO `%DB_PREFIX%conf` VALUES ('315', 'ASSET', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('316', 'OFFICE_DOCUMENTS', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('317', 'GUARANTEE', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('318', 'AUTHORIZED_OPERATION_LETTER', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('319', 'AUTHORIZED_OPERATION_CERTIFICATE', '1', '4', '1', '0,1', '0', '1', '287');
INSERT INTO `%DB_PREFIX%conf` VALUES ('322', 'ONLINETIME_TO_EXPERIENCE', '600', '1', '0', '', '1', '1', '500');
INSERT INTO `%DB_PREFIX%conf` VALUES ('323', 'TICKET_CATTY_RATIO', '0.01', '1', '0', '', '1', '1', '500');
INSERT INTO `%DB_PREFIX%conf` VALUES ('325', 'TICKET_CATTY_MIN', '1', '1', '0', '', '1', '1', '500');

-- ----------------------------
-- Table structure for `%DB_PREFIX%deal_msg_list`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%deal_msg_list`;
CREATE TABLE `%DB_PREFIX%deal_msg_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dest` varchar(255) NOT NULL,
  `send_type` tinyint(1) NOT NULL,
  `content` text NOT NULL,
  `send_time` int(11) NOT NULL,
  `is_send` tinyint(1) NOT NULL,
  `create_time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `result` text NOT NULL,
  `is_success` tinyint(1) NOT NULL,
  `is_html` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `is_youhui` tinyint(1) NOT NULL,
  `youhui_id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL COMMENT '发送的验证码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%deal_msg_list
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%exchange_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%exchange_log`;
CREATE TABLE `%DB_PREFIX%exchange_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `rule_id` int(11) NOT NULL COMMENT '兑换规则id',
  `is_success` tinyint(1) DEFAULT '0' COMMENT '是否成功 1-成功 0-未成功',
  `diamonds` int(11) DEFAULT NULL COMMENT '兑换秀豆',
  `ticket` int(11) DEFAULT NULL COMMENT '兑换秀票',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%exchange_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%exchange_rule`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%exchange_rule`;
CREATE TABLE `%DB_PREFIX%exchange_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `diamonds` int(11) NOT NULL DEFAULT '0' COMMENT '可获得秀豆',
  `ticket` int(11) NOT NULL DEFAULT '0' COMMENT '需要的秀票数',
  `is_effect` tinyint(1) DEFAULT '1' COMMENT '是否有效',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%exchange_rule
-- ----------------------------
INSERT INTO `%DB_PREFIX%exchange_rule` VALUES ('1', '100', '258', '1', '0');
INSERT INTO `%DB_PREFIX%exchange_rule` VALUES ('2', '300', '770', '1', '0');
INSERT INTO `%DB_PREFIX%exchange_rule` VALUES ('7', '1000', '2580', '1', '0');
INSERT INTO `%DB_PREFIX%exchange_rule` VALUES ('8', '2000', '5160', '1', '0');
INSERT INTO `%DB_PREFIX%exchange_rule` VALUES ('9', '5000', '12900', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%faq`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%faq`;
CREATE TABLE `%DB_PREFIX%faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort` int(11) NOT NULL,
  `click_count` int(11) DEFAULT '0' COMMENT '点击数',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`) USING BTREE,
  KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%faq
-- ----------------------------
INSERT INTO `%DB_PREFIX%faq` VALUES ('1', '咨询客服', '如何提现', '（1）关注微信服务号“千秀互动直播”\r\n（2）同千秀互动直播账户绑定\r\n（3）通过微信公众号“千秀互动直播”申请提现\r\n（4）审核通过马上到账', '9', '46');
INSERT INTO `%DB_PREFIX%faq` VALUES ('2', '直播问题', '怎么打开美颜？', '进入直播间后打开房间设置功能，打开美颜即可', '10', '24');
INSERT INTO `%DB_PREFIX%faq` VALUES ('3', '充值问题', '充值有上限吗？', '千秀互动直播充值是没有限制的，您充值时提示有限制，您可以查询一下您的银行卡或者是其他您的支付方式是否有限额。', '0', '60');
INSERT INTO `%DB_PREFIX%faq` VALUES ('6', '直播问题', '怎么成为热门？', '热门是系统随机推荐的，您多直播、多和粉丝互动您上热门的几率会高一点，您也可以把您的直播分享到多个平台让您的朋友都来观看您的精彩直播。', '1', '51');
INSERT INTO `%DB_PREFIX%faq` VALUES ('7', '账号问题', '怎么认证？', '打开千秀互动直播—个人主页—千秀互动直播认证—填写相关信息—提交即可。', '32', '33');
INSERT INTO `%DB_PREFIX%faq` VALUES ('8', '账号问题', '为什么总是显示账户信息过期，请重新登录？', '亲爱的千秀互动直播用户，如果收到“账号信息已过期”的弹窗提示，请到千秀互动直播—个人主页—设置—退出登录，并重新登录账号即可。如还不能解决，请您联系在线客服并详细描述您的问题。', '3', '16');
INSERT INTO `%DB_PREFIX%faq` VALUES ('11', '直播问题', '怎么使用后置摄像头？', '进入直播间—点击屏幕右下角有“...”-点击“翻转”', '11', '10');
INSERT INTO `%DB_PREFIX%faq` VALUES ('12', '咨询客服', '咨询客服', '客服QQ：2366666681', '12', '25');
INSERT INTO `%DB_PREFIX%faq` VALUES ('14', '充值问题', '怎么充值秀豆？', '打开千秀互动直播应用，你可以在个人主页—我的秀豆里进行充值或者在观看他人直播时点击礼物—充值。您可以选择不同的档位进行充值。', '17', '7');
INSERT INTO `%DB_PREFIX%faq` VALUES ('15', '账号问题', '怎么设置管理员？', '在您自己的直播间点击粉丝头像，在弹出的页面点左上角管理—设置管理员，就可以把您喜欢的人设置成管理员了。', '18', '4');
INSERT INTO `%DB_PREFIX%faq` VALUES ('16', '账号问题', '账号与安全怎么绑定手机号', '个人主页—设置—账号与安全-手机绑定绑定手机号，验证码是短信形式发送，绑定手机号后可以使用手机号登陆。', '19', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('17', '账号问题', '个人主页是什么？', '点击最下方右边的按钮，就进入到个人主页中心界面，在这里可以查看到您个人信息的信息，还有收益、等级等信息。', '20', '3');
INSERT INTO `%DB_PREFIX%faq` VALUES ('18', '账号问题', '如何更改个人信息？', '点击个人主页左上角的铅笔图标，进入到个人信息编辑界面，在这里可以更改您的昵称、头像、性别、个性签名。', '21', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('19', '账号问题', '如何选择热门地区？', '在首页点击热门，就可以选择您喜欢的地区，观看该地区热门主播的精彩直播。', '22', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('20', '账号问题', '账户可以注销吗？', '千秀互动直播是第三方快捷登陆的，所以账号是不可以注销的。每一种登陆方式都是一个全新的千秀互动直播号，您想要其他账号您可以用其他方式登陆哦。', '23', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('21', '账号问题', '登陆失败怎么办？', '登陆失败建议您关闭app、切换网络试一下，重新登陆即可。', '24', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('22', '直播问题', '为什么直播时脸是反的', '千秀互动直播是在手机上进行的。使用前置摄像头直播的时候就相当于在照镜子一样，所以说才会看到感觉脸是反的。', '25', '1');
INSERT INTO `%DB_PREFIX%faq` VALUES ('23', '直播问题', '提示相机不可用', '安卓用户在手机设置里找到权限管理，找到千秀互动直播打开所有相机权限就可以了；ios用户在审核设置—隐私—相机—打开千秀互动直播相机权限就可以了。', '26', '1');
INSERT INTO `%DB_PREFIX%faq` VALUES ('24', '直播问题', '怎么隐藏公屏和弹幕？', '进入直播间向右滑屏就可以隐藏公屏和弹幕，即可无遮挡的观看您喜欢的主播了哟。', '27', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('25', '直播问题', '直播没有人看怎么办？', '多直播，让自己的直播诙谐有趣，千秀互动直播这么好玩，可以拉朋友一起嗨；多和粉丝互动，培养自己的粉丝群；您也可以和其他主播小伙伴多多交流共享经验。超级大主播指日可待！', '28', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('26', '直播问题', '公屏怎么不显示评论', '建议您切换网络、关闭app重新打开试试，网络不稳定的时候会显示延迟。', '29', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('27', '直播问题', '直播时怎么分享到其他平台？', '在开始直播前，填写直播标题页面点亮相应的平台图标，就可以分享到其他平台邀请朋友观看直播。在直播中是没有办法分享自己的直播给朋友们的哦。', '30', '9');
INSERT INTO `%DB_PREFIX%faq` VALUES ('28', '直播问题', '为什么我直播好友收不到通知？', '安卓用户确认好友的千秀互动直播应用是否关闭好友推送，进入个人主页—设置—推送管理—直播消息提醒；ios用户确认好友是否关闭千秀互动直播的推送，还需在系统设置里允许通知：设置—通知—选择千秀互动直播允许通知就可以了。', '31', '6');
INSERT INTO `%DB_PREFIX%faq` VALUES ('29', '直播问题', '收到骚扰，怎么拉黑？', '点击进入ta的主页，右下方点击拉黑即可。', '32', '9');
INSERT INTO `%DB_PREFIX%faq` VALUES ('30', '直播问题', '怎么关注好友，取消关注？', '进入ta个人主页，页面下方可以选择关注好友，再次点击已关注就可以取消关注了。', '0', '0');
INSERT INTO `%DB_PREFIX%faq` VALUES ('31', '直播问题', '怎么播放音乐？', '在直播页面下方点击音乐图标，进入可以搜索歌曲，在搜索框中搜索您想要演唱的歌曲，点击“下载”图标，即可演唱。', '33', '21');
INSERT INTO `%DB_PREFIX%faq` VALUES ('32', '充值问题', 'ios充值方式？', 'ios版本app充值只支持AppStore充值。', '0', '5');
INSERT INTO `%DB_PREFIX%faq` VALUES ('33', '充值问题', '怎么支付宝和微信充值？', '目前只有安卓版本支持微信和支付宝充值的，在个人主页—我的秀豆里选择您喜欢的支付方式即可。', '0', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('34', '充值问题', '充错账户，点错充值档位怎么办？', '购买秀豆输入密码前，请仔细确认金额后再购买。错充等无法退还，还请您务必小心操作哦。', '0', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('35', '充值问题', '微信充值提示充值超限怎么办？', '千秀互动直播没有限制充值金额，银行卡是有限额的，查看绑定银行卡的限额，建议您提高一下您银行卡的支付限额。', '0', '2');
INSERT INTO `%DB_PREFIX%faq` VALUES ('36', '充值问题', '充值成功秀豆没有到,怎么办？', '充值未到账请您点击设置-帮助与反馈-咨询客服，找到我们在线客服的并提供以下资料，方便我们核实及处理。\r\n1.千秀互动直播号\r\n2.充值日期、充值时间是商务、下午或是晚上，大概是几点\r\n3.千秀互动直播版本号是多少（在个人主页设置内查看）\r\n4.提交您交易账单的截图（苹果app store充值在绑定苹果ID的邮箱里查看账单）\r\n5.请您详细说明具体一共几笔没有到账个，未到账金额各是多少。', '0', '2');

-- ----------------------------
-- Table structure for `%DB_PREFIX%flow_statistics`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%flow_statistics`;
CREATE TABLE `%DB_PREFIX%flow_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `voice_user_num` int(11) DEFAULT '0' COMMENT '在线人数',
  `max_voice_user_num` int(11) DEFAULT NULL COMMENT '峰值在线人数',
  `voice_time` int(11) DEFAULT '0' COMMENT '通话时长（秒）',
  `flux` int(11) DEFAULT NULL COMMENT '下行流量',
  `bandwidth` float(11,2) DEFAULT '0.00' COMMENT '峰值带宽',
  `user_id` int(11) DEFAULT '0' COMMENT '归属系统',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每5分钟统计在线数据：在线人数，峰值在线人数，通话时间,下行流量,峰值带宽';

-- ----------------------------
-- Records of %DB_PREFIX%flow_statistics
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%index_image`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%index_image`;
CREATE TABLE `%DB_PREFIX%index_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 表示首页轮播',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%index_image
-- ----------------------------
INSERT INTO `%DB_PREFIX%index_image` VALUES ('8', 'http://image.qiankeep.com/public/attachment/201608/15/13/57b158edab468.jpg', 'http://www.fanwe.com', '2', '轮播', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%log`;
CREATE TABLE `%DB_PREFIX%log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_info` text NOT NULL,
  `log_time` int(11) NOT NULL,
  `log_admin` int(11) NOT NULL,
  `log_ip` varchar(255) NOT NULL,
  `log_status` tinyint(1) NOT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%mobile_verify_code`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%mobile_verify_code`;
CREATE TABLE `%DB_PREFIX%mobile_verify_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verify_code` varchar(10) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `create_time` int(11) NOT NULL,
  `client_ip` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT '邮件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%mobile_verify_code
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%msg_template`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%msg_template`;
CREATE TABLE `%DB_PREFIX%msg_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) NOT NULL COMMENT '名字',
  `content` text NOT NULL COMMENT '内容',
  `type` tinyint(1) NOT NULL COMMENT '类型',
  `is_html` tinyint(1) NOT NULL COMMENT '是否成功：1表示成功，0表示失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%msg_template
-- ----------------------------
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('20', 'TPL_SMS_USER_VERIFY', '{$success_user_info.user_name},恭喜您,注册验证成功!', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('23', 'TPL_SMS_VERIFY_CODE', '验证码为{$verify.code}', '0', '0');
INSERT INTO `%DB_PREFIX%msg_template` VALUES ('33', 'TPL_SMS_TIPS_STATUS', '互动直播统计分析，在{$tips.time}登录超时！！', '0', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%m_config`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%m_config`;
CREATE TABLE `%DB_PREFIX%m_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `group_id` varchar(50) DEFAULT NULL COMMENT '分组名称',
  `val` text,
  `type` tinyint(1) NOT NULL,
  `sort` int(11) DEFAULT '0',
  `value_scope` varchar(50) DEFAULT NULL,
  `title_scope` varchar(255) DEFAULT NULL COMMENT '对应value_scope的中文解释',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%m_config
-- ----------------------------
INSERT INTO `%DB_PREFIX%m_config` VALUES ('10', 'kf_phone', '客服电话', '基础配置', '400-000-0000', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('11', 'kf_email', '客服邮箱', '基础配置', 'qq@fanwe.com', '0', '2', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('16', 'page_size', '分页大小', '基础配置', '10', '0', '10', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('18', 'program_title', '程序标题名称', '基础配置', '千秀直播', '0', '0', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('22', 'android_version', 'android版本号', 'APP版本管理', '2016082902', '0', '2', null, null, '格式：yyyymmddnn');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('23', 'android_filename', 'android下载包名', 'APP版本管理', 'https://app.fanwe.cn/android/zhibo/%E6%96%B9%E7%BB%B4%E4%BA%92%E5%8A%A8%E7%9B%B4%E6%92%AD_1.8.2_2016082902.apk', '0', '3', null, null, '放程序根目录下');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('24', 'ios_version', 'ios版本号', 'APP版本管理', '2016082901', '0', '6', null, null, '格式：yyyymmddnn');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('25', 'ios_down_url', 'ios下载地址', 'APP版本管理', 'itms-services://?action=download-manifest&url=https://app.fanwe.cn/ios/zhibo/zhibo.plist', '0', '7', null, null, 'appstore连接地址');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('28', 'android_upgrade', 'android版本升级内容', 'APP版本管理', '2016-08-29更新\r\n修复bug', '3', '4', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('29', 'ios_upgrade', 'ios版本升级内容', 'APP版本管理', '2016-08-29更新', '3', '8', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('31', 'android_forced_upgrade', 'android是否强制升级', 'APP版本管理', '1', '4', '1', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('32', 'ios_forced_upgrade', 'ios是否强制升级', 'APP版本管理', '1', '4', '5', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('36', 'wx_appid', '微信开放平台APPID', '第三方帐户', '', '0', '9', null, null, '用于微信登陆');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('37', 'wx_secrit', '微信开放平台SECRIT', '第三方帐户', '', '0', '10', null, null, '用于微信登陆');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('38', 'sina_app_key', '新浪APP_KEY', '第三方帐户', '', '0', '11', null, null, '用于微博登陆');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('39', 'sina_app_secret', '新浪APP_SECRET', '第三方帐户', '', '0', '12', null, null, '用于微博登陆');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('41', 'android_qq_app_id', '安卓端QQ登录APP_ID', '第三方帐户', '', '0', '13', null, null, '安卓端QQ登录');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('42', 'ios_qq_app_id', 'IOS端QQ登录APP_ID', '第三方帐户', '', '0', '14', null, null, 'IOS端QQ登录');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('46', 'ios_check_version', 'ios审核版本号', 'APP版本管理', '1.6.0', '0', '9', '', null, '审核中填写');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('47', 'android_master_secret', '安卓(推送)appSecret', '第三方帐户', '', '0', '15', null, null, '友盟推送AppMasterSecret,供API对接友盟服务器使用(安卓)');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('48', 'android_app_key', '安卓(推送)appkey', '第三方帐户', '', '0', '16', null, null, '友盟推送AppKey(安卓)');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('49', 'ios_master_secret', 'IOS(推送)appSecret', '第三方帐户', '', '0', '17', null, null, '友盟推送AppMasterSecret,供API对接友盟服务器使用(IOS)');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('50', 'ios_app_key', 'IOS(推送)appkey', '第三方帐户', '', '0', '18', null, null, '友盟推送AppKey(IOS)');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('52', 'service_push', '是否开启全服推送', '基础配置', '1', '4', '1', '0,1', '否,是', '0:否 推送给粉丝;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('53', 'beauty_ios', 'IOS默认美颜度', '应用设置', '49', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('54', 'beauty_android', 'ANDROID默认美颜度', '应用设置', '49', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('55', 'beauty_close', '客户端不许自义美颜度', '应用设置', '0', '4', '1', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('56', 'short_name', '千秀直播名称', '基础配置', '千秀直播', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('57', 'ticket_name', '秀票名称', '基础配置', '秀票', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('58', 'app_name', 'app名称', '基础配置', '千秀互动直播', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('59', 'share_title', '分享标题', '分享设置', '你丑你先睡，我美我直播', '0', '1', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('60', 'qcloud_secret_id', '腾讯云API账号', '腾讯直播', '', '0', '37', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('61', 'qcloud_secret_key', '腾讯云API密钥', '腾讯直播', '', '0', '37', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('63', 'has_wx_login', '支持微信登陆', '第三方帐户', '1', '4', '4', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('64', 'has_qq_login', '支持qq登陆', '第三方帐户', '1', '4', '3', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('65', 'has_mobile_login', '支持手机登陆', '第三方帐户', '1', '4', '1', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('66', 'sina_app_api', '支持新浪分享', '分享设置', '1', '4', '2', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('67', 'wx_app_api', '支持微信分享', '分享设置', '1', '4', '3', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('68', 'qq_app_api', '支持QQ分享', '分享设置', '1', '4', '4', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('69', 'has_sina_login', '支持新浪登陆', '第三方帐户', '1', '4', '2', '0,1', '否,是', '0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('70', 'jr_user_level', '用户等级', '应用设置', '2', '0', '1', null, null, '用户等级大于等于该值,有用户加入房间提醒操作');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('71', 'monitor_overtime', '心跳超时时间', '应用设置', '40', '0', '1', null, null, '单位:秒;建议设置30秒至90秒之间; 每5秒收集一次心跳,超过时间则会强制结束');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('72', 'robot_num', '机器人数量', '应用设置', '20', '0', '2', null, null, '创建直播时,随机添加机器人数;可以出现在观众列表中的');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('73', 'diamonds_rate', '充值比例', '基础配置', '20', '0', '0', null, null, '充值金额与秀豆的换算比率如：充值1元，可以获得10个秀豆');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('74', 'tim_sdkappid', 'SdkAppId', '腾讯直播', '', '0', '0', null, null, '腾讯互动直播SdkAppId');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('75', 'tim_identifier', '账号管理员', '腾讯直播', '', '0', '0', null, null, '腾讯互动直播账号管理员');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('76', 'has_save_video', '保存视频', '腾讯直播', '1', '4', '1', '0,1', '否,是', '保存视频（可用于回播）;0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('78', 'region_versions', '地区版本号', '基础配置', '1', '0', '78', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('79', 'vodset_app_id', '视频播放器APP_ID', '腾讯直播', '', '0', '0', null, null, '旁路直播APP_ID');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('80', 'subscription', '微信公众号名称', '基础配置', '千秀科技', '0', '0', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('81', 'iap_recharge', '苹果充值', '应用设置', '1', '4', '1', '0,1', '否,是', '苹果支付价格与普通充值价格相同 0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('82', 'short_video_time', '短视频时间定义(秒)', '应用设置', '50', '0', '0', null, null, '默认不保存短视频');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('83', 'del_short_video', '直接删除短(私密)视频', '应用设置', '1', '4', '0', '0,1', '否,是', '直播结束时,直接删除短(私密)视频;0:否;1:是');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('84', 'qcloud_bizid', '腾讯bizid', '腾讯直播', '2811', '0', '0', null, null, null);
INSERT INTO `%DB_PREFIX%m_config` VALUES ('85', 'virtual_number', '虚拟数量', '应用设置', '50', '0', '2', null, null, '进来一个真实会员,带来几个虚拟会员数量');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('86', 'lucky_num', '吉祥保留号', '应用设置', '888888,666666', '3', '0', null, null, '预留的吉祥保留号已英文半角逗号隔开');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('87', 'wx_gz_appid', '微信公众号APPID', '第三方帐户', '', '0', '5', null, null, '用于提现的公众号的appid');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('88', 'wx_gz_secrit', '微信公众号SECRIT', '第三方帐户', '', '0', '6', null, null, '用于提现的公众号的secrit');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('91', 'num_weight', '观看人数权重', '排序权重', '1', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('92', 'sort_weight', '排序权重', '排序权重', '10000', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('93', 'ticke_weight', '持有映票权重', '排序权重', '10', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('94', 'level_weight', '等级权重', '排序权重', '1', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('95', 'video_ticket_weight', '当前视频获取映票权重', '排序权重', '20', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('96', 'video_focus_weight', '房间内关注数', '排序权重', '20', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('97', 'video_share_weight', '房间内分享数的权重', '排序权重', '20', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('98', 'focus_weight', '当前有的关注数权重', '排序权重', '10', '0', '0', null, null, '');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('99', 'app_logo', '系统LOGO', '基础配置', null, '2', '0', null, null, 'app的logo');

-- ----------------------------
-- Table structure for `%DB_PREFIX%national_telephone_code`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%national_telephone_code`;
CREATE TABLE `%DB_PREFIX%national_telephone_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL COMMENT '国家或地区',
  `area_code` varchar(20) NOT NULL COMMENT '电话区号',
  `continent` varchar(10) NOT NULL COMMENT '所属大陆',
  `is_effect` tinyint(4) NOT NULL COMMENT '是否有效',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=236 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%national_telephone_code
-- ----------------------------
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('1', '中国', '+86', '亚洲', '1', '1');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('2', '香港', '+852', '亚洲', '1', '2');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('3', '马来西亚', '+60', '亚洲', '1', '3');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('4', '菲律宾', '+63', '亚洲', '1', '4');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('5', '泰国', '+66', '亚洲', '1', '5');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('6', '日本', '+81', '亚洲', '1', '6');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('7', '越南', '+84', '亚洲', '1', '7');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('8', '柬埔寨', '+855', '亚洲', '1', '8');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('9', '孟加拉国', '+880', '亚洲', '1', '9');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('10', '印度', '+91', '亚洲', '1', '10');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('11', '阿富汗', '+93', '亚洲', '1', '11');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('12', '缅甸', '+95', '亚洲', '1', '12');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('13', '黎巴嫩', '+961', '亚洲', '1', '13');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('14', '叙利亚', '+963', '亚洲', '1', '14');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('15', '科威特', '+965', '亚洲', '1', '15');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('16', '阿曼', '+968', '亚洲', '1', '16');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('17', '巴林', '+973', '亚洲', '1', '17');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('18', '不丹', '+975', '亚洲', '1', '18');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('19', '尼泊尔', '+977', '亚洲', '1', '19');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('20', '塞浦路斯', '+357', '亚洲', '1', '20');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('21', '阿联酋', '+971', '亚洲', '1', '21');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('22', '印度尼西亚', '+62', '亚洲', '1', '22');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('23', '新加坡', '+65', '亚洲', '1', '23');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('24', '文莱', '+673', '亚洲', '1', '24');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('25', '韩国', '+82', '亚洲', '1', '25');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('26', '朝鲜', '+850', '亚洲', '1', '26');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('27', '澳门', '+853', '亚洲', '1', '27');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('28', '老挝', '+856', '亚洲', '1', '28');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('29', '台湾', '+886', '亚洲', '1', '29');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('30', '土耳其', '+90', '亚洲', '1', '30');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('31', '巴基斯坦', '+92', '亚洲', '1', '31');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('32', '斯里兰卡', '+94', '亚洲', '1', '32');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('33', '马尔代夫', '+960', '亚洲', '1', '33');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('34', '约旦', '+962', '亚洲', '1', '34');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('35', '伊拉克', '+964', '亚洲', '1', '35');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('36', '沙特阿拉伯', '+966', '亚洲', '1', '36');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('37', '以色列', '+972', '亚洲', '1', '37');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('38', '卡塔尔', '+974', '亚洲', '1', '38');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('39', '蒙古', '+976', '亚洲', '1', '39');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('40', '伊朗', '+98', '亚洲', '1', '40');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('41', '巴勒斯坦', '+970', '亚洲', '1', '41');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('42', '也门', '+967', '亚洲', '1', '42');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('43', '俄罗斯联邦', '+7', '欧 洲', '1', '43');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('44', '荷兰', '+31', '欧 洲', '1', '44');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('45', '法国', '+33', '欧 洲', '1', '45');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('46', '直布罗陀', '+350', '欧 洲', '1', '46');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('47', '卢森堡', '+352', '欧 洲', '1', '47');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('48', '冰岛', '+354', '欧 洲', '1', '48');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('49', '马耳他', '+356', '欧 洲', '1', '49');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('50', '芬兰', '+358', '欧 洲', '1', '50');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('51', '匈牙利', '+36', '欧 洲', '1', '51');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('52', '南斯拉夫', '+381', '欧 洲', '1', '52');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('53', '圣马力诺', '+378', '欧 洲', '1', '53');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('54', '罗马尼亚', '+40', '欧 洲', '1', '54');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('55', '列支敦士登', '+423', '欧 洲', '1', '55');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('56', '英国', '+44', '欧 洲', '1', '56');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('57', '瑞典', '+46', '欧 洲', '1', '57');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('58', '波兰', '+48', '欧 洲', '1', '58');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('59', '斯洛伐克', '+421', '欧 洲', '1', '59');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('60', '马其顿', '+389', '欧 洲', '1', '60');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('61', '斯洛文尼亚', '+386', '欧 洲', '1', '61');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('62', '亚美尼亚共和国', '+374', '欧 洲', '1', '62');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('63', '格鲁吉亚共和国', '+995', '欧 洲', '1', '63');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('64', '吉尔吉斯坦共和国', '+996', '欧 洲', '1', '64');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('65', '塔吉克斯坦共和国', '+992', '欧 洲', '1', '65');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('66', '乌克兰', '+380', '欧 洲', '1', '66');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('67', '拉脱维亚', '+371', '欧 洲', '1', '67');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('68', '摩尔多瓦', '+373', '欧 洲', '1', '68');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('69', '希腊', '+30', '欧 洲', '1', '69');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('70', '比利时', '+32', '欧 洲', '1', '70');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('71', '西班牙', '+34', '欧 洲', '1', '71');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('72', '葡萄牙', '+351', '欧 洲', '1', '72');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('73', '爱尔兰', '+353', '欧 洲', '1', '73');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('74', '阿尔巴尼亚', '+355', '欧 洲', '1', '74');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('75', '安道尔', '+376', '欧 洲', '1', '75');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('76', '保加利亚', '+359', '欧 洲', '1', '76');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('77', '德国', '+49', '欧 洲', '1', '77');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('78', '意大利', '+39', '欧 洲', '1', '78');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('79', '梵蒂冈', '+3906698', '欧 洲', '1', '79');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('80', '瑞士', '+41', '欧 洲', '1', '80');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('81', '奥地利', '+43', '欧 洲', '1', '81');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('82', '丹麦', '+45', '欧 洲', '1', '82');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('83', '挪威', '+47', '欧 洲', '1', '83');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('84', '捷克 ', '+00420 ', '欧 洲', '1', '84');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('85', '摩纳哥', '+377', '欧 洲', '1', '85');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('86', '科罗地亚', '+385', '欧 洲', '1', '86');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('87', '波斯尼亚和塞哥维那', '+387', '欧 洲', '1', '87');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('88', '白俄罗斯共和国', '+375', '欧 洲', '1', '88');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('89', '哈萨克斯坦共和国', '+7', '欧 洲', '1', '89');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('90', '乌兹别克斯坦共和国', '+998', '欧 洲', '1', '90');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('91', '土库曼斯坦共和国', '+993', '欧 洲', '1', '91');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('92', '立陶宛', '+370', '欧 洲', '1', '92');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('93', '爱沙尼亚', '+372', '欧 洲', '1', '93');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('94', '阿塞拜疆', '+994', '欧 洲', '1', '94');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('95', '埃及', '+20', '非 洲', '1', '95');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('96', '阿尔及利亚', '+213', '非 洲', '1', '96');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('97', '利比亚', '+218', '非 洲', '1', '97');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('98', '塞内加尔', '+221', '非 洲', '1', '98');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('99', '马里', '+223', '非 洲', '1', '99');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('100', '科特迪瓦', '+225', '非 洲', '1', '100');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('101', '尼日尔', '+227', '非 洲', '1', '101');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('102', '贝宁', '+229', '非 洲', '1', '102');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('103', '利比里亚', '+231', '非 洲', '1', '103');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('104', '加纳', '+233', '非 洲', '1', '104');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('105', '乍得', '+235', '非 洲', '1', '105');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('106', '喀麦隆', '+237', '非 洲', '1', '106');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('107', '圣多美', '+239', '非 洲', '1', '107');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('108', '赤道几内亚', '+240', '非 洲', '1', '108');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('109', '刚果', '+242', '非 洲', '1', '109');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('110', '安哥拉', '+244', '非 洲', '1', '110');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('111', '阿森松', '+247', '非 洲', '1', '111');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('112', '苏丹', '+249', '非 洲', '1', '112');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('113', '埃塞俄比亚', '+251', '非 洲', '1', '113');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('114', '吉布提', '+253', '非 洲', '1', '114');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('115', '坦桑尼亚', '+255', '非 洲', '1', '115');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('116', '布隆迪', '+257', '非 洲', '1', '116');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('117', '赞比亚', '+260', '非 洲', '1', '117');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('118', '留尼旺岛', '+262', '非 洲', '1', '118');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('119', '纳米比亚', '+264', '非 洲', '1', '119');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('120', '莱索托', '+266', '非 洲', '1', '120');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('121', '斯威士兰', '+268', '非 洲', '1', '121');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('122', '南非', '+27', '非 洲', '1', '122');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('123', '阿鲁巴岛', '+297', '非 洲', '1', '123');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('124', '厄里特里亚', '+291', '非 洲', '1', '124');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('125', '加那利群岛(西)(圣克鲁斯)', '+3422', '非 洲', '1', '125');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('126', '桑给巴尔', '+259', '非 洲', '1', '126');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('127', '摩洛哥', '+212', '非 洲', '1', '127');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('128', '突尼斯', '+216', '非 洲', '1', '128');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('129', '冈比亚', '+220', '非 洲', '1', '129');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('130', '毛里塔尼亚', '+222', '非 洲', '1', '130');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('131', '几内亚', '+224', '非 洲', '1', '131');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('132', '布基拉法索', '+226', '非 洲', '1', '132');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('133', '多哥', '+228', '非 洲', '1', '133');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('134', '毛里求斯', '+230', '非 洲', '1', '134');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('135', '塞拉利昂', '+232', '非 洲', '1', '135');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('136', '尼日利亚', '+234', '非 洲', '1', '136');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('137', '中非', '+236', '非 洲', '1', '137');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('138', '佛得角', '+238', '非 洲', '1', '138');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('139', '普林西比', '+239', '非 洲', '1', '139');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('140', '加蓬', '+241', '非 洲', '1', '140');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('141', '扎伊尔', '+243', '非 洲', '1', '141');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('142', '几内亚比绍', '+245', '非 洲', '1', '142');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('143', '塞舌尔', '+248', '非 洲', '1', '143');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('144', '卢旺达', '+250', '非 洲', '1', '144');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('145', '索马里', '+252', '非 洲', '1', '145');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('146', '肯尼亚', '+254', '非 洲', '1', '146');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('147', '乌干达', '+256', '非 洲', '1', '147');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('148', '莫桑比克', '+258', '非 洲', '1', '148');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('149', '马达加斯加', '+261', '非 洲', '1', '149');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('150', '津巴布韦', '+263', '非 洲', '1', '150');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('151', '马拉维', '+265', '非 洲', '1', '151');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('152', '博茨瓦纳', '+267', '非 洲', '1', '152');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('153', '科摩罗', '+269', '非 洲', '1', '153');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('154', '圣赫勒拿', '+290', '非 洲', '1', '154');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('155', '法罗群岛', '+298', '非 洲', '1', '155');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('156', '美国', '+1', '北 美 洲', '1', '156');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('157', '中途岛', '+1808', '北 美 洲', '1', '157');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('158', '威克岛', '+1808', '北 美 洲', '1', '158');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('159', '维尔京群岛', '+1340', '北 美 洲', '1', '159');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('160', '波多黎各', '+1809', '北 美 洲', '1', '160');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('161', '巴哈马', '+1242', '北 美 洲', '1', '161');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('162', '阿拉斯加', '+1907', '北 美 洲', '1', '162');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('163', '安提瓜和巴布达', '+1268', '北 美 洲', '1', '163');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('164', '开曼群岛', '+1345', '北 美 洲', '1', '164');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('165', '多米尼加共和国', '+1809', '北 美 洲', '1', '165');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('166', '蒙特塞拉特岛', '+1664', '北 美 洲', '1', '166');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('167', '波多黎哥', '+1787', '北 美 洲', '1', '167');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('168', '圣克里斯托弗和尼维斯', '+1869', '北 美 洲', '1', '168');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('169', '特立尼达和多巴哥', '+1868', '北 美 洲', '1', '169');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('170', '瓜多罗普', '+590', '北 美 洲', '1', '170');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('171', '加拿大', '+1', '北 美 洲', '1', '171');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('172', '夏威夷', '+1808', '北 美 洲', '1', '172');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('173', '安圭拉岛', '+1264', '北 美 洲', '1', '173');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('174', '圣卢西亚', '+1758', '北 美 洲', '1', '174');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('175', '牙买加', '+1876', '北 美 洲', '1', '175');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('176', '巴巴多斯', '+1246', '北 美 洲', '1', '176');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('177', '格陵兰岛', '+299', '北 美 洲', '1', '177');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('178', '百慕大群岛', '+1441', '北 美 洲', '1', '178');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('179', '多米尼加联邦', '+1767', '北 美 洲', '1', '179');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('180', '格林纳达', '+1473', '北 美 洲', '1', '180');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('181', '荷属安的列斯群岛', '+599', '北 美 洲', '1', '181');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('182', '圣皮埃尔岛密克隆岛（法）', '+508', '北 美 洲', '1', '182');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('183', '圣文森特岛（英）', '+1784', '北 美 洲', '1', '183');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('184', '特克斯和凯科斯群岛', '+1649', '北 美 洲', '1', '184');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('185', '福克兰群岛', '+500', '南 美 洲', '1', '185');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('186', '危地马拉', '+502', '南 美 洲', '1', '186');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('187', '洪都拉斯', '+504', '南 美 洲', '1', '187');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('188', '哥斯达黎加', '+506', '南 美 洲', '1', '188');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('189', '海地', '+509', '南 美 洲', '1', '189');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('190', '墨西哥', '+52', '南 美 洲', '1', '190');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('191', '阿根廷', '+54', '南 美 洲', '1', '191');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('192', '智利', '+56', '南 美 洲', '1', '192');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('193', '委内瑞拉', '+58', '南 美 洲', '1', '193');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('194', '圭亚那', '+592', '南 美 洲', '1', '194');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('195', '法属圭亚那', '+594', '南 美 洲', '1', '195');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('196', '马提尼克', '+596', '南 美 洲', '1', '196');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('197', '乌拉圭', '+598', '南 美 洲', '1', '197');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('198', '伯利兹', '+501', '南 美 洲', '1', '198');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('199', '萨尔瓦多', '+503', '南 美 洲', '1', '199');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('200', '尼加拉瓜', '+505', '南 美 洲', '1', '200');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('201', '巴拿马', '+507', '南 美 洲', '1', '201');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('202', '秘鲁', '+51', '南 美 洲', '1', '202');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('203', '古巴', '+53', '南 美 洲', '1', '203');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('204', '巴西', '+55', '南 美 洲', '1', '204');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('205', '哥伦比亚', '+57', '南 美 洲', '1', '205');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('206', '玻利维亚', '+591', '南 美 洲', '1', '206');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('207', '厄瓜多尔', '+593', '南 美 洲', '1', '207');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('208', '巴拉圭', '+595', '南 美 洲', '1', '208');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('209', '苏里南', '+597', '南 美 洲', '1', '209');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('210', '澳大利亚', '+61', '大 洋 洲', '1', '210');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('211', '关岛', '+1671', '大 洋 洲', '1', '211');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('212', '诺福克岛', '+672', '大 洋 洲', '1', '212');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('213', '瑙鲁', '+674', '大 洋 洲', '1', '213');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('214', '所罗门群岛', '+677', '大 洋 洲', '1', '214');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('215', '斐济', '+679', '大 洋 洲', '1', '215');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('216', '纽埃岛', '+683', '大 洋 洲', '1', '216');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('217', '西萨摩亚', '+685', '大 洋 洲', '1', '217');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('218', '图瓦卢', '+688', '大 洋 洲', '1', '218');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('219', '马里亚纳群岛', '+1670', '大 洋 洲', '1', '219');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('220', '巴布亚新几内亚', '+675', '大 洋 洲', '1', '220');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('221', '托克鲁', '+690', '大 洋 洲', '1', '221');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('222', '马绍尔群岛', '+692', '大 洋 洲', '1', '222');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('223', '新西兰', '+64', '大 洋 洲', '1', '223');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('224', '科科斯岛', '+619162', '大 洋 洲', '1', '224');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('225', '圣诞岛', '+619164', '大 洋 洲', '1', '225');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('226', '汤加', '+676', '大 洋 洲', '1', '226');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('227', '瓦努阿图', '+678', '大 洋 洲', '1', '227');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('228', '科克群岛', '+682', '大 洋 洲', '1', '228');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('229', '东萨摩亚', '+684', '大 洋 洲', '1', '229');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('230', '基里巴斯', '+686', '大 洋 洲', '1', '230');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('231', '法属波里尼西亚、塔希提 ', '+00689 ', '大 洋 洲', '1', '231');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('232', '新咯里多尼亚群岛 ', '+00687 ', '大 洋 洲', '1', '232');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('233', '帕劳 ', '+00680 ', '大 洋 洲', '1', '233');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('234', '密克罗尼西亚 ', '+00691 ', '大 洋 洲', '1', '234');
INSERT INTO `%DB_PREFIX%national_telephone_code` VALUES ('235', '瓦里斯和富士那群岛 ', '+001681 ', '大 洋 洲', '1', '235');


-- ----------------------------
-- Table structure for `%DB_PREFIX%payment`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%payment`;
CREATE TABLE `%DB_PREFIX%payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `online_pay` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `total_amount` decimal(20,2) NOT NULL COMMENT '总金额',
  `config` text NOT NULL,
  `logo` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%payment
-- ----------------------------
INSERT INTO `%DB_PREFIX%payment` VALUES ('2', 'Aliapp', '1', '3', '支付宝支付', '', '12.12', 'a:4:{s:14:\"aliapp_partner\";s:16:\"2088701687941821\";s:16:\"aliapp_seller_id\";s:12:\"cw@fanwe.com\";s:17:\"aliapp_rsa_public\";s:216:\"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB\";s:22:\"aliapp_rsa_private_key\";s:816:\"MIICXgIBAAKBgQDo88CbOMZXHubKIFmWScf+Ozt2IRsPAo8BYb/znVZrHhIgj1vbBWhB1V+SR8OajT/xJ+oQESrlt9lGmx6QXUchRfAjgPhBG4UgDCNaVaR5nZt8hX+199qIU4Zwi8/ZU2QohHzpqiYiy1K3trkLgqzlRS5mDg7VSh4jyuTVjpLQJwIDAQABAoGASwScRSBudwXjiroKP6S4+/01M+CLZzUKuoYxG5HSj6JachPYn9rI7VJ6eZAUxMOyEMYq0UvGBb5EAUHZAOKZEdee+1NzO6xkZ8avLj/xztLI9zhXngVM9RFtGtw9JnfTx3x+GRXQWUX5DDD1mUS1dSOUjhUxkvgGTHu8eUQtRFkCQQD9oPTCM37wHse6mNd3yBRLXrKBgFFCnKBND9qS/ludaHMu70OJ7Z5QMuQYK+b6yP45g0zol6yk/9PVucIPF94jAkEA6yFO6IM+aGM5wBGNqSHBx5u0eCSH0AyLrRzh3TXxE/MzlxuiOJvMkIqtIZ3d8ZGRLZZJm6mkIaoKsinjLU5sLQJBAKKNTTFGNd4JrDKwkLApYLBpkfij1/DcV3Tsa0b4lJkO/3ueR2gYDfYSl9PSF2i19xG/UERmKXVarVb2hiSMRIcCQQDZ/6XDWXuRGP5AH4Yx24RoZuppwaTRtfACbpbSm+KKVp/sZ8h9p6WAFbLzSgSupgHuPDq+wgfU1mzYRpHEPcN5AkEAl98qr3GQkF3bJLDxPS4GjDLu9/1k4laDqHZbkzPqO/2rkjF6xysJuIsW8MJeZ4tV6kJHjlHXkSnN6ZkcRQ1Q2Q==\";}', 'http://liveimage.fanwe.net/public/attachment/201608/16/17/57b2e3bb47c96.png', '1');
INSERT INTO `%DB_PREFIX%payment` VALUES ('6', 'Wwxjspay', '1', '2', '微信提现', '', '0.00', 'a:7:{s:5:\"appid\";s:18:\"wxc09e0b1395d47e7d\";s:9:\"appsecret\";s:32:\"8539c855ad9ab5fbb117bcf3951fc0dd\";s:5:\"mchid\";s:10:\"1334943601\";s:3:\"key\";s:32:\"fanwefanwefanwefanwefanwefanwef1\";s:7:\"sslcert\";s:32:\"public/weixin/apiclient_cert.pem\";s:6:\"sslkey\";s:31:\"public/weixin/apiclient_key.pem\";s:4:\"type\";s:2:\"V3\";}', '', '2');
INSERT INTO `%DB_PREFIX%payment` VALUES ('11', 'Iappay', '1', '3', '苹果支付', '', '6.94', 'N;', 'http://liveimage.fanwe.net/public/attachment/201608/18/11/57b5264df01ee.jpg', '4');
INSERT INTO `%DB_PREFIX%payment` VALUES ('12', 'WxApp', '1', '3', '微信支付', '', '1.07', 'a:4:{s:11:\"wxapp_appid\";s:18:\"wx125bfb1866fd0d4d\";s:15:\"wxapp_partnerid\";s:10:\"1239275002\";s:9:\"wxapp_key\";s:32:\"fanwefanwefanwefanwefanwefanwe11\";s:12:\"wxapp_secret\";s:32:\"e07effc12efc7f72a709ee3d7e354a51\";}', 'http://liveimage.fanwe.net/public/attachment/201608/16/17/57b2dd5688278.jpg', '5');

-- ----------------------------
-- Table structure for `%DB_PREFIX%payment_notice`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%payment_notice`;
CREATE TABLE `%DB_PREFIX%payment_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_sn` varchar(255) NOT NULL,
  `create_time` int(11) NOT NULL,
  `pay_time` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT 'order_id为0时为充值',
  `is_paid` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `bank_id` varchar(255) NOT NULL,
  `memo` text NOT NULL,
  `money` decimal(20,2) NOT NULL COMMENT '金额',
  `outer_notice_sn` varchar(255) NOT NULL,
  `deal_id` int(11) NOT NULL COMMENT '0为充值',
  `deal_name` varchar(255) NOT NULL COMMENT '空为充值',
  `is_has_send_success` tinyint(1) NOT NULL COMMENT '（0表示发送不成功，1表示发送成功）',
  `paid_send` tinyint(1) NOT NULL COMMENT '支付成功后是否发送',
  `pay_date` date DEFAULT NULL COMMENT '收款日期',
  `recharge_id` int(11) NOT NULL COMMENT '购买秀豆的列表ID',
  `recharge_name` varchar(255) DEFAULT NULL COMMENT '购买秀豆名称',
  `product_id` varchar(50) DEFAULT NULL COMMENT '苹果应用内支付项目ID',
  `iap_receipt` varchar(2000) DEFAULT NULL COMMENT '苹果应该内支付返回内容',
  `diamonds` int(10) DEFAULT '0' COMMENT '充值时,获得的秀豆数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY `notice_sn_unk` (`notice_sn`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `payment_id` (`payment_id`) USING BTREE,
  KEY `deal_id` (`deal_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='// 付款单号列表';

-- ----------------------------
-- Records of %DB_PREFIX%payment_notice
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%prop`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%prop`;
CREATE TABLE `%DB_PREFIX%prop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '道具名',
  `score` int(11) NOT NULL COMMENT '积分',
  `diamonds` int(11) NOT NULL COMMENT '消费秀豆',
  `icon` varchar(255) NOT NULL COMMENT '图标',
  `ticket` int(11) NOT NULL COMMENT '秀票或秀豆，当为红包时是秀豆，非红包时是秀票',
  `is_much` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:可以连续发送多个;用于小金额礼物',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序，从大到小;越大越靠前',
  `is_red_envelope` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:红包',
  `is_animated` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0:普通礼物 1:gif礼物 2:大型动画礼物',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:禁用;1:启用;默认启用',
  `anim_type` varchar(255) NOT NULL COMMENT '大型道具类型 如："plane1","plane2","rocket1"',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%prop
-- ----------------------------
INSERT INTO `%DB_PREFIX%prop` VALUES ('1', '香蕉', '1', '1', './public/gift/a7.png', '1', '1', '8', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('2', '黄瓜', '1', '1', './public/gift/a8.png', '1', '1', '14', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('5', '玫瑰花', '10', '10', './public/gift/c.png', '10', '1', '13', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('6', '皮鞭', '3', '3', './public/gift/a1.png', '3', '1', '7', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('7', '粉钻', '88', '88', './public/gift/a2.png', '199', '1', '11', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('8', '钻戒', '199', '199', './public/gift/a6.png', '199', '0', '5', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('9', '香吻', '33', '33', './public/gift/a.png', '33', '1', '12', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('10', '法拉利', '6666', '6666', 'http://image.qiankeep.com/public/attachment/201608/12/20/57adc842ec997.png', '6666', '0', '2', '0', '2', '1', 'ferrari');
INSERT INTO `%DB_PREFIX%prop` VALUES ('11', '花', '1', '1', './public/gift/a4.png', '1', '1', '15', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('12', '红包', '500', '500', './public/gift/a3.png', '200', '0', '10', '1', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('13', '烟花', '999', '999', './public/gift/a4.png', '999', '0', '6', '0', '1', '0', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('19', '表', '1', '1', 'http://image.qiankeep.com/public/attachment/201607/26/12/5796e55e60201.png', '1', '1', '16', '0', '0', '1', '');
INSERT INTO `%DB_PREFIX%prop` VALUES ('25', '轰炸机', '3000', '3000', 'http://image.qiankeep.com/public/attachment/201608/12/20/57adc53bf3726.png', '3000', '0', '4', '0', '2', '1', 'plane1');
INSERT INTO `%DB_PREFIX%prop` VALUES ('26', '客机', '5000', '5000', 'http://image.qiankeep.com/public/attachment/201608/12/20/57adc567284cd.png', '5000', '0', '3', '0', '2', '1', 'plane2');
INSERT INTO `%DB_PREFIX%prop` VALUES ('27', '兰博基尼', '1200', '1200', 'http://image.qiankeep.com/public/attachment/201608/12/20/57adc834ec803.png', '1200', '0', '9', '0', '2', '1', 'lamborghini');
INSERT INTO `%DB_PREFIX%prop` VALUES ('28', '火箭', '9000', '9000', 'http://image.qiankeep.com/public/attachment/201608/12/20/57adc5a2ec77e.png', '9000', '0', '1', '0', '2', '1', 'rocket1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%prop_animated`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%prop_animated`;
CREATE TABLE `%DB_PREFIX%prop_animated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prop_id` int(10) NOT NULL,
  `url` varchar(255) NOT NULL COMMENT 'gif动画地址',
  `play_count` tinyint(1) NOT NULL DEFAULT '1' COMMENT '播放次数;play_count>1时duration无效',
  `delay_time` int(10) NOT NULL DEFAULT '0' COMMENT '延时播放时间;从第delay_time毫秒开始播放',
  `duration` int(10) NOT NULL DEFAULT '0' COMMENT '播放时长（毫秒）play_count>1时duration无效',
  `show_user` tinyint(1) NOT NULL DEFAULT '1' COMMENT '在顶部显示：用户名（曾送者）',
  `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '0：使用path路径；1：屏幕上部；2：屏幕中间；3：屏幕底部',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序，从大到小;越大越靠前',
  `path` text COMMENT '动画播放路径,格式待定;比如：从上到下；从左到右等',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%prop_animated
-- ----------------------------
INSERT INTO `%DB_PREFIX%prop_animated` VALUES ('1', '1', 'http://s1.sinaimg.cn/middle/618a870dt9b1e4dbd2510&690', '1', '0', '0', '1', '3', '0', null);
INSERT INTO `%DB_PREFIX%prop_animated` VALUES ('14', '4', 'http://ilvb.fanwe.net/public/gift/122.gif', '1', '0', '5000', '1', '2', '0', null);
INSERT INTO `%DB_PREFIX%prop_animated` VALUES ('16', '13', 'http://ilvb.fanwe.net/public/gift/fireworks_1.gif', '1', '0', '0', '1', '2', '0', null);
INSERT INTO `%DB_PREFIX%prop_animated` VALUES ('18', '17', './public/attachment/201607/07/16/577e195c7e034.gif', '2', '50', '1000', '0', '0', '0', null);

-- ----------------------------
-- Table structure for `%DB_PREFIX%push_anchor`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%push_anchor`;
CREATE TABLE `%DB_PREFIX%push_anchor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL COMMENT '主播ID',
  `nick_name` varchar(100) NOT NULL COMMENT '主播昵称',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `cate_title` varchar(100) NOT NULL COMMENT '直播主题',
  `room_id` int(10) NOT NULL COMMENT '房间ID',
  `city` varchar(20) NOT NULL COMMENT '直播城市地址',
  `head_image` varchar(255) NOT NULL COMMENT '主播头像',
  `status` tinyint(1) NOT NULL COMMENT '推送状态(0:未推送，1：推送中；2：已推送）',
  `android_file_id` varchar(50) NOT NULL,
  `ios_file_id` varchar(50) NOT NULL COMMENT '上传文件后获取file_id',
  `ret_android_status` varchar(10) NOT NULL COMMENT '返回状态SUCCESS/FAIL',
  `ret_android_data` varchar(2000) NOT NULL COMMENT '安卓返回内容',
  `ret_ios_status` varchar(10) NOT NULL COMMENT '返回状态SUCCESS/FAIL',
  `ret_ios_data` varchar(2000) NOT NULL COMMENT 'IOS返回内容',
  `pust_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推送类型 0：推送粉丝；1：推送全服',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='开始直播时候，推送给粉丝';

-- ----------------------------
-- Records of %DB_PREFIX%push_anchor
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%recharge_rule`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%recharge_rule`;
CREATE TABLE `%DB_PREFIX%recharge_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `iap_money` decimal(20,2) NOT NULL COMMENT '苹果支付价格',
  `diamonds` int(11) NOT NULL DEFAULT '0' COMMENT '金额对应的秀豆数量',
  `gift_diamonds` int(11) NOT NULL DEFAULT '0' COMMENT '秀豆赠送数量',
  `is_effect` tinyint(1) DEFAULT '1' COMMENT '是否有效 1-有效 0-无效',
  `sort` int(1) NOT NULL COMMENT '排序',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '是否删除 0-未删除 1-删除',
  `product_id` varchar(50) NOT NULL COMMENT '苹果应用内支付项目ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%recharge_rule
-- ----------------------------
INSERT INTO `%DB_PREFIX%recharge_rule` VALUES ('1', '秀豆60', '6.00', '6.00', '60', '0', '1', '1', '0', '10001');
INSERT INTO `%DB_PREFIX%recharge_rule` VALUES ('2', '秀豆300', '30.00', '30.00', '300', '0', '1', '2', '0', '10002');
INSERT INTO `%DB_PREFIX%recharge_rule` VALUES ('3', '秀豆980', '98.00', '98.00', '980', '0', '1', '3', '0', '10003');
INSERT INTO `%DB_PREFIX%recharge_rule` VALUES ('4', '秀豆2980', '298.00', '298.00', '2980', '0', '1', '4', '0', '10004');
INSERT INTO `%DB_PREFIX%recharge_rule` VALUES ('6', '购买测试10W秀豆', '0.01', '6.00', '100000', '0', '1', '0', '0', '10005');

-- ----------------------------
-- Table structure for `%DB_PREFIX%region_conf`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%region_conf`;
CREATE TABLE `%DB_PREFIX%region_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '地区名称',
  `region_level` tinyint(4) NOT NULL COMMENT '1:国 2:省 3:市(县) 4:区(镇)',
  `py` varchar(50) NOT NULL,
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为热门地区',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3401 DEFAULT CHARSET=utf8 COMMENT='//地区配置';

-- ----------------------------
-- Records of %DB_PREFIX%region_conf
-- ----------------------------
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('1', '0', '全国', '1', 'quanguo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3', '1', '安徽', '2', 'anhui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('4', '1', '福建', '2', 'fujian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('5', '1', '甘肃', '2', 'gansu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('6', '1', '广东', '2', 'guangdong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('7', '1', '广西', '2', 'guangxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('8', '1', '贵州', '2', 'guizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('9', '1', '海南', '2', 'hainan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('10', '1', '河北', '2', 'hebei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('11', '1', '河南', '2', 'henan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('12', '1', '黑龙江', '2', 'heilongjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('13', '1', '湖北', '2', 'hubei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('14', '1', '湖南', '2', 'hunan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('15', '1', '吉林', '2', 'jilin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('16', '1', '江苏', '2', 'jiangsu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('17', '1', '江西', '2', 'jiangxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('18', '1', '辽宁', '2', 'liaoning', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('19', '1', '内蒙古', '2', 'neimenggu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('20', '1', '宁夏', '2', 'ningxia', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('21', '1', '青海', '2', 'qinghai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('22', '1', '山东', '2', 'shandong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('23', '1', '山西', '2', 'shanxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('24', '1', '陕西', '2', 'shanxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('26', '1', '四川', '2', 'sichuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('28', '1', '西藏', '2', 'xicang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('29', '1', '新疆', '2', 'xinjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('30', '1', '云南', '2', 'yunnan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('31', '1', '浙江', '2', 'zhejiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('36', '3', '安庆', '3', 'anqing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('37', '3', '蚌埠', '3', 'bangbu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('38', '3', '巢湖', '3', 'chaohu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('39', '3', '池州', '3', 'chizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('40', '3', '滁州', '3', 'chuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('41', '3', '阜阳', '3', 'fuyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('42', '3', '淮北', '3', 'huaibei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('43', '3', '淮南', '3', 'huainan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('44', '3', '黄山', '3', 'huangshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('45', '3', '六安', '3', 'liuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('46', '3', '马鞍山', '3', 'maanshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('47', '3', '宿州', '3', 'suzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('48', '3', '铜陵', '3', 'tongling', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('49', '3', '芜湖', '3', 'wuhu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('50', '3', '宣城', '3', 'xuancheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('51', '3', '亳州', '3', 'zhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('52', '2', '北京', '2', 'beijing', '1');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('53', '4', '福州', '3', 'fuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('54', '4', '龙岩', '3', 'longyan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('55', '4', '南平', '3', 'nanping', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('56', '4', '宁德', '3', 'ningde', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('57', '4', '莆田', '3', 'putian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('58', '4', '泉州', '3', 'quanzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('59', '4', '三明', '3', 'sanming', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('60', '4', '厦门', '3', 'xiamen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('61', '4', '漳州', '3', 'zhangzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('62', '5', '兰州', '3', 'lanzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('63', '5', '白银', '3', 'baiyin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('64', '5', '定西', '3', 'dingxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('65', '5', '甘南', '3', 'gannan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('66', '5', '嘉峪关', '3', 'jiayuguan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('67', '5', '金昌', '3', 'jinchang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('68', '5', '酒泉', '3', 'jiuquan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('69', '5', '临夏', '3', 'linxia', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('70', '5', '陇南', '3', 'longnan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('71', '5', '平凉', '3', 'pingliang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('72', '5', '庆阳', '3', 'qingyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('73', '5', '天水', '3', 'tianshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('74', '5', '武威', '3', 'wuwei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('75', '5', '张掖', '3', 'zhangye', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('76', '6', '广州', '3', 'guangzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('77', '6', '深圳', '3', 'shen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('78', '6', '潮州', '3', 'chaozhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('79', '6', '东莞', '3', 'dong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('80', '6', '佛山', '3', 'foshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('81', '6', '河源', '3', 'heyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('82', '6', '惠州', '3', 'huizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('83', '6', '江门', '3', 'jiangmen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('84', '6', '揭阳', '3', 'jieyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('85', '6', '茂名', '3', 'maoming', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('86', '6', '梅州', '3', 'meizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('87', '6', '清远', '3', 'qingyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('88', '6', '汕头', '3', 'shantou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('89', '6', '汕尾', '3', 'shanwei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('90', '6', '韶关', '3', 'shaoguan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('91', '6', '阳江', '3', 'yangjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('92', '6', '云浮', '3', 'yunfu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('93', '6', '湛江', '3', 'zhanjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('94', '6', '肇庆', '3', 'zhaoqing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('95', '6', '中山', '3', 'zhongshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('96', '6', '珠海', '3', 'zhuhai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('97', '7', '南宁', '3', 'nanning', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('98', '7', '桂林', '3', 'guilin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('99', '7', '百色', '3', 'baise', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('100', '7', '北海', '3', 'beihai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('101', '7', '崇左', '3', 'chongzuo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('102', '7', '防城港', '3', 'fangchenggang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('103', '7', '贵港', '3', 'guigang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('104', '7', '河池', '3', 'hechi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('105', '7', '贺州', '3', 'hezhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('106', '7', '来宾', '3', 'laibin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('107', '7', '柳州', '3', 'liuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('108', '7', '钦州', '3', 'qinzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('109', '7', '梧州', '3', 'wuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('110', '7', '玉林', '3', 'yulin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('111', '8', '贵阳', '3', 'guiyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('112', '8', '安顺', '3', 'anshun', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('113', '8', '毕节', '3', 'bijie', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('114', '8', '六盘水', '3', 'liupanshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('115', '8', '黔东南', '3', 'qiandongnan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('116', '8', '黔南', '3', 'qiannan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('117', '8', '黔西南', '3', 'qianxinan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('118', '8', '铜仁', '3', 'tongren', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('119', '8', '遵义', '3', 'zunyi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('120', '9', '海口', '3', 'haikou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('121', '9', '三亚', '3', 'sanya', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('122', '9', '白沙', '3', 'baisha', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('123', '9', '保亭', '3', 'baoting', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('124', '9', '昌江', '3', 'changjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('125', '9', '澄迈县', '3', 'chengmaixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('126', '9', '定安县', '3', 'dinganxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('127', '9', '东方', '3', 'dongfang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('128', '9', '乐东', '3', 'ledong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('129', '9', '临高县', '3', 'lingaoxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('130', '9', '陵水', '3', 'lingshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('131', '9', '琼海', '3', 'qionghai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('132', '9', '琼中', '3', 'qiongzhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('133', '9', '屯昌县', '3', 'tunchangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('134', '9', '万宁', '3', 'wanning', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('135', '9', '文昌', '3', 'wenchang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('136', '9', '五指山', '3', 'wuzhishan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('137', '9', '儋州', '3', 'zhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('138', '10', '石家庄', '3', 'shijiazhuang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('139', '10', '保定', '3', 'baoding', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('140', '10', '沧州', '3', 'cangzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('141', '10', '承德', '3', 'chengde', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('142', '10', '邯郸', '3', 'handan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('143', '10', '衡水', '3', 'hengshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('144', '10', '廊坊', '3', 'langfang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('145', '10', '秦皇岛', '3', 'qinhuangdao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('146', '10', '唐山', '3', 'tangshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('147', '10', '邢台', '3', 'xingtai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('148', '10', '张家口', '3', 'zhangjiakou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('149', '11', '郑州', '3', 'zhengzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('150', '11', '洛阳', '3', 'luoyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('151', '11', '开封', '3', 'kaifeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('152', '11', '安阳', '3', 'anyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('153', '11', '鹤壁', '3', 'hebi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('154', '11', '济源', '3', 'jiyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('155', '11', '焦作', '3', 'jiaozuo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('156', '11', '南阳', '3', 'nanyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('157', '11', '平顶山', '3', 'pingdingshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('158', '11', '三门峡', '3', 'sanmenxia', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('159', '11', '商丘', '3', 'shangqiu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('160', '11', '新乡', '3', 'xinxiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('161', '11', '信阳', '3', 'xinyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('162', '11', '许昌', '3', 'xuchang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('163', '11', '周口', '3', 'zhoukou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('164', '11', '驻马店', '3', 'zhumadian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('165', '11', '漯河', '3', 'he', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('166', '11', '濮阳', '3', 'yang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('167', '12', '哈尔滨', '3', 'haerbin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('168', '12', '大庆', '3', 'daqing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('169', '12', '大兴安岭', '3', 'daxinganling', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('170', '12', '鹤岗', '3', 'hegang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('171', '12', '黑河', '3', 'heihe', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('172', '12', '鸡西', '3', 'jixi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('173', '12', '佳木斯', '3', 'jiamusi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('174', '12', '牡丹江', '3', 'mudanjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('175', '12', '七台河', '3', 'qitaihe', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('176', '12', '齐齐哈尔', '3', 'qiqihaer', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('177', '12', '双鸭山', '3', 'shuangyashan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('178', '12', '绥化', '3', 'suihua', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('179', '12', '伊春', '3', 'yichun', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('180', '13', '武汉', '3', 'wuhan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('181', '13', '仙桃', '3', 'xiantao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('182', '13', '鄂州', '3', 'ezhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('183', '13', '黄冈', '3', 'huanggang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('184', '13', '黄石', '3', 'huangshi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('185', '13', '荆门', '3', 'jingmen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('186', '13', '荆州', '3', 'jingzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('187', '13', '潜江', '3', 'qianjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('188', '13', '神农架林区', '3', 'shennongjialinqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('189', '13', '十堰', '3', 'shiyan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('190', '13', '随州', '3', 'suizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('191', '13', '天门', '3', 'tianmen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('192', '13', '咸宁', '3', 'xianning', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('193', '13', '襄樊', '3', 'xiangfan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('194', '13', '孝感', '3', 'xiaogan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('195', '13', '宜昌', '3', 'yichang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('196', '13', '恩施', '3', 'enshi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('197', '14', '长沙', '3', 'changsha', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('198', '14', '张家界', '3', 'zhangjiajie', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('199', '14', '常德', '3', 'changde', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('200', '14', '郴州', '3', 'chenzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('201', '14', '衡阳', '3', 'hengyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('202', '14', '怀化', '3', 'huaihua', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('203', '14', '娄底', '3', 'loudi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('204', '14', '邵阳', '3', 'shaoyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('205', '14', '湘潭', '3', 'xiangtan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('206', '14', '湘西', '3', 'xiangxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('207', '14', '益阳', '3', 'yiyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('208', '14', '永州', '3', 'yongzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('209', '14', '岳阳', '3', 'yueyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('210', '14', '株洲', '3', 'zhuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('211', '15', '长春', '3', 'changchun', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('212', '15', '吉林', '3', 'jilin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('213', '15', '白城', '3', 'baicheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('214', '15', '白山', '3', 'baishan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('215', '15', '辽源', '3', 'liaoyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('216', '15', '四平', '3', 'siping', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('217', '15', '松原', '3', 'songyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('218', '15', '通化', '3', 'tonghua', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('219', '15', '延边', '3', 'yanbian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('220', '16', '南京', '3', 'nanjing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('221', '16', '苏州', '3', 'suzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('222', '16', '无锡', '3', 'wuxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('223', '16', '常州', '3', 'changzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('224', '16', '淮安', '3', 'huaian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('225', '16', '连云港', '3', 'lianyungang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('226', '16', '南通', '3', 'nantong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('227', '16', '宿迁', '3', 'suqian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('228', '16', '泰州', '3', 'taizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('229', '16', '徐州', '3', 'xuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('230', '16', '盐城', '3', 'yancheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('231', '16', '扬州', '3', 'yangzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('232', '16', '镇江', '3', 'zhenjiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('233', '17', '南昌', '3', 'nanchang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('234', '17', '抚州', '3', 'fuzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('235', '17', '赣州', '3', 'ganzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('236', '17', '吉安', '3', 'jian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('237', '17', '景德镇', '3', 'jingdezhen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('238', '17', '九江', '3', 'jiujiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('239', '17', '萍乡', '3', 'pingxiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('240', '17', '上饶', '3', 'shangrao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('241', '17', '新余', '3', 'xinyu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('242', '17', '宜春', '3', 'yichun', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('243', '17', '鹰潭', '3', 'yingtan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('244', '18', '沈阳', '3', 'shenyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('245', '18', '大连', '3', 'dalian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('246', '18', '鞍山', '3', 'anshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('247', '18', '本溪', '3', 'benxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('248', '18', '朝阳', '3', 'chaoyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('249', '18', '丹东', '3', 'dandong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('250', '18', '抚顺', '3', 'fushun', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('251', '18', '阜新', '3', 'fuxin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('252', '18', '葫芦岛', '3', 'huludao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('253', '18', '锦州', '3', 'jinzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('254', '18', '辽阳', '3', 'liaoyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('255', '18', '盘锦', '3', 'panjin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('256', '18', '铁岭', '3', 'tieling', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('257', '18', '营口', '3', 'yingkou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('258', '19', '呼和浩特', '3', 'huhehaote', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('259', '19', '阿拉善盟', '3', 'alashanmeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('260', '19', '巴彦淖尔盟', '3', 'bayannaoermeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('261', '19', '包头', '3', 'baotou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('262', '19', '赤峰', '3', 'chifeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('263', '19', '鄂尔多斯', '3', 'eerduosi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('264', '19', '呼伦贝尔', '3', 'hulunbeier', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('265', '19', '通辽', '3', 'tongliao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('266', '19', '乌海', '3', 'wuhai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('267', '19', '乌兰察布市', '3', 'wulanchabushi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('268', '19', '锡林郭勒盟', '3', 'xilinguolemeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('269', '19', '兴安盟', '3', 'xinganmeng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('270', '20', '银川', '3', 'yinchuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('271', '20', '固原', '3', 'guyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('272', '20', '石嘴山', '3', 'shizuishan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('273', '20', '吴忠', '3', 'wuzhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('274', '20', '中卫', '3', 'zhongwei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('275', '21', '西宁', '3', 'xining', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('276', '21', '果洛', '3', 'guoluo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('277', '21', '海北', '3', 'haibei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('278', '21', '海东', '3', 'haidong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('279', '21', '海南', '3', 'hainan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('280', '21', '海西', '3', 'haixi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('281', '21', '黄南', '3', 'huangnan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('282', '21', '玉树', '3', 'yushu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('283', '22', '济南', '3', 'jinan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('284', '22', '青岛', '3', 'qingdao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('285', '22', '滨州', '3', 'binzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('286', '22', '德州', '3', 'dezhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('287', '22', '东营', '3', 'dongying', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('288', '22', '菏泽', '3', 'heze', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('289', '22', '济宁', '3', 'jining', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('290', '22', '莱芜', '3', 'laiwu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('291', '22', '聊城', '3', 'liaocheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('292', '22', '临沂', '3', 'linyi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('293', '22', '日照', '3', 'rizhao', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('294', '22', '泰安', '3', 'taian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('295', '22', '威海', '3', 'weihai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('296', '22', '潍坊', '3', 'weifang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('297', '22', '烟台', '3', 'yantai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('298', '22', '枣庄', '3', 'zaozhuang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('299', '22', '淄博', '3', 'zibo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('300', '23', '太原', '3', 'taiyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('301', '23', '长治', '3', 'changzhi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('302', '23', '大同', '3', 'datong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('303', '23', '晋城', '3', 'jincheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('304', '23', '晋中', '3', 'jinzhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('305', '23', '临汾', '3', 'linfen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('306', '23', '吕梁', '3', 'lvliang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('307', '23', '朔州', '3', 'shuozhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('308', '23', '忻州', '3', 'xinzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('309', '23', '阳泉', '3', 'yangquan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('310', '23', '运城', '3', 'yuncheng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('311', '24', '西安', '3', 'xian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('312', '24', '安康', '3', 'ankang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('313', '24', '宝鸡', '3', 'baoji', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('314', '24', '汉中', '3', 'hanzhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('315', '24', '商洛', '3', 'shangluo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('316', '24', '铜川', '3', 'tongchuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('317', '24', '渭南', '3', 'weinan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('318', '24', '咸阳', '3', 'xianyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('319', '24', '延安', '3', 'yanan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('320', '24', '榆林', '3', 'yulin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('321', '25', '上海', '2', 'shanghai', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('322', '26', '成都', '3', 'chengdu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('323', '26', '绵阳', '3', 'mianyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('324', '26', '阿坝', '3', 'aba', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('325', '26', '巴中', '3', 'bazhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('326', '26', '达州', '3', 'dazhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('327', '26', '德阳', '3', 'deyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('328', '26', '甘孜', '3', 'ganzi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('329', '26', '广安', '3', 'guangan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('330', '26', '广元', '3', 'guangyuan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('331', '26', '乐山', '3', 'leshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('332', '26', '凉山', '3', 'liangshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('333', '26', '眉山', '3', 'meishan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('334', '26', '南充', '3', 'nanchong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('335', '26', '内江', '3', 'neijiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('336', '26', '攀枝花', '3', 'panzhihua', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('337', '26', '遂宁', '3', 'suining', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('338', '26', '雅安', '3', 'yaan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('339', '26', '宜宾', '3', 'yibin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('340', '26', '资阳', '3', 'ziyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('341', '26', '自贡', '3', 'zigong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('342', '26', '泸州', '3', 'zhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('343', '27', '天津', '2', 'tianjin', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('344', '28', '拉萨', '3', 'lasa', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('345', '28', '阿里', '3', 'ali', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('346', '28', '昌都', '3', 'changdu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('347', '28', '林芝', '3', 'linzhi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('348', '28', '那曲', '3', 'naqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('349', '28', '日喀则', '3', 'rikaze', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('350', '28', '山南', '3', 'shannan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('351', '29', '乌鲁木齐', '3', 'wulumuqi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('352', '29', '阿克苏', '3', 'akesu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('353', '29', '阿拉尔', '3', 'alaer', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('354', '29', '巴音郭楞', '3', 'bayinguoleng', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('355', '29', '博尔塔拉', '3', 'boertala', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('356', '29', '昌吉', '3', 'changji', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('357', '29', '哈密', '3', 'hami', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('358', '29', '和田', '3', 'hetian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('359', '29', '喀什', '3', 'kashi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('360', '29', '克拉玛依', '3', 'kelamayi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('361', '29', '克孜勒苏', '3', 'kezilesu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('362', '29', '石河子', '3', 'shihezi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('363', '29', '图木舒克', '3', 'tumushuke', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('364', '29', '吐鲁番', '3', 'tulufan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('365', '29', '五家渠', '3', 'wujiaqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('366', '29', '伊犁', '3', 'yili', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('367', '30', '昆明', '3', 'kunming', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('368', '30', '怒江', '3', 'nujiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('369', '30', '普洱', '3', 'puer', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('370', '30', '丽江', '3', 'lijiang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('371', '30', '保山', '3', 'baoshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('372', '30', '楚雄', '3', 'chuxiong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('373', '30', '大理', '3', 'dali', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('374', '30', '德宏', '3', 'dehong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('375', '30', '迪庆', '3', 'diqing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('376', '30', '红河', '3', 'honghe', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('377', '30', '临沧', '3', 'lincang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('378', '30', '曲靖', '3', 'qujing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('379', '30', '文山', '3', 'wenshan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('380', '30', '西双版纳', '3', 'xishuangbanna', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('381', '30', '玉溪', '3', 'yuxi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('382', '30', '昭通', '3', 'zhaotong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('383', '31', '杭州', '3', 'hangzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('384', '31', '湖州', '3', 'huzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('385', '31', '嘉兴', '3', 'jiaxing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('386', '31', '金华', '3', 'jinhua', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('387', '31', '丽水', '3', 'lishui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('388', '31', '宁波', '3', 'ningbo', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('389', '31', '绍兴', '3', 'shaoxing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('390', '31', '台州', '3', 'taizhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('391', '31', '温州', '3', 'wenzhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('392', '31', '舟山', '3', 'zhoushan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('393', '31', '衢州', '3', 'zhou', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('394', '32', '重庆', '2', 'zhongqing', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('395', '33', '香港', '2', 'xianggang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('396', '34', '澳门', '2', 'aomen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('397', '35', '台湾', '2', 'taiwan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('500', '52', '东城区', '3', 'dongchengqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('501', '52', '西城区', '3', 'xichengqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('502', '52', '海淀区', '3', 'haidianqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('503', '52', '朝阳区', '3', 'chaoyangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('504', '52', '崇文区', '3', 'chongwenqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('505', '52', '宣武区', '3', 'xuanwuqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('506', '52', '丰台区', '3', 'fengtaiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('507', '52', '石景山区', '3', 'shijingshanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('508', '52', '房山区', '3', 'fangshanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('509', '52', '门头沟区', '3', 'mentougouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('510', '52', '通州区', '3', 'tongzhouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('511', '52', '顺义区', '3', 'shunyiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('512', '52', '昌平区', '3', 'changpingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('513', '52', '怀柔区', '3', 'huairouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('514', '52', '平谷区', '3', 'pingguqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('515', '52', '大兴区', '3', 'daxingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('516', '52', '密云县', '3', 'miyunxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('517', '52', '延庆县', '3', 'yanqingxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2703', '321', '长宁区', '3', 'changningqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2704', '321', '闸北区', '3', 'zhabeiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2705', '321', '闵行区', '3', 'xingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2706', '321', '徐汇区', '3', 'xuhuiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2707', '321', '浦东新区', '3', 'pudongxinqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2708', '321', '杨浦区', '3', 'yangpuqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2709', '321', '普陀区', '3', 'putuoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2710', '321', '静安区', '3', 'jinganqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2711', '321', '卢湾区', '3', 'luwanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2712', '321', '虹口区', '3', 'hongkouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2713', '321', '黄浦区', '3', 'huangpuqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2714', '321', '南汇区', '3', 'nanhuiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2715', '321', '松江区', '3', 'songjiangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2716', '321', '嘉定区', '3', 'jiadingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2717', '321', '宝山区', '3', 'baoshanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2718', '321', '青浦区', '3', 'qingpuqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2719', '321', '金山区', '3', 'jinshanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2720', '321', '奉贤区', '3', 'fengxianqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2721', '321', '崇明县', '3', 'chongmingxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2912', '343', '和平区', '3', 'hepingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2913', '343', '河西区', '3', 'hexiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2914', '343', '南开区', '3', 'nankaiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2915', '343', '河北区', '3', 'hebeiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2916', '343', '河东区', '3', 'hedongqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2917', '343', '红桥区', '3', 'hongqiaoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2918', '343', '东丽区', '3', 'dongliqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2919', '343', '津南区', '3', 'jinnanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2920', '343', '西青区', '3', 'xiqingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2921', '343', '北辰区', '3', 'beichenqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2922', '343', '塘沽区', '3', 'tangguqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2923', '343', '汉沽区', '3', 'hanguqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2924', '343', '大港区', '3', 'dagangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2925', '343', '武清区', '3', 'wuqingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2926', '343', '宝坻区', '3', 'baoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2927', '343', '经济开发区', '3', 'jingjikaifaqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2928', '343', '宁河县', '3', 'ninghexian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2929', '343', '静海县', '3', 'jinghaixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('2930', '343', '蓟县', '3', 'jixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3325', '394', '合川区', '3', 'hechuanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3326', '394', '江津区', '3', 'jiangjinqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3327', '394', '南川区', '3', 'nanchuanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3328', '394', '永川区', '3', 'yongchuanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3329', '394', '南岸区', '3', 'nananqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3330', '394', '渝北区', '3', 'yubeiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3331', '394', '万盛区', '3', 'wanshengqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3332', '394', '大渡口区', '3', 'dadukouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3333', '394', '万州区', '3', 'wanzhouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3334', '394', '北碚区', '3', 'beiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3335', '394', '沙坪坝区', '3', 'shapingbaqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3336', '394', '巴南区', '3', 'bananqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3337', '394', '涪陵区', '3', 'fulingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3338', '394', '江北区', '3', 'jiangbeiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3339', '394', '九龙坡区', '3', 'jiulongpoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3340', '394', '渝中区', '3', 'yuzhongqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3341', '394', '黔江开发区', '3', 'qianjiangkaifaqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3342', '394', '长寿区', '3', 'changshouqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3343', '394', '双桥区', '3', 'shuangqiaoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3344', '394', '綦江县', '3', 'jiangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3345', '394', '潼南县', '3', 'nanxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3346', '394', '铜梁县', '3', 'tongliangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3347', '394', '大足县', '3', 'dazuxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3348', '394', '荣昌县', '3', 'rongchangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3349', '394', '璧山县', '3', 'shanxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3350', '394', '垫江县', '3', 'dianjiangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3351', '394', '武隆县', '3', 'wulongxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3352', '394', '丰都县', '3', 'fengduxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3353', '394', '城口县', '3', 'chengkouxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3354', '394', '梁平县', '3', 'liangpingxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3355', '394', '开县', '3', 'kaixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3356', '394', '巫溪县', '3', 'wuxixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3357', '394', '巫山县', '3', 'wushanxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3358', '394', '奉节县', '3', 'fengjiexian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3359', '394', '云阳县', '3', 'yunyangxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3360', '394', '忠县', '3', 'zhongxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3361', '394', '石柱', '3', 'shizhu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3362', '394', '彭水', '3', 'pengshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3363', '394', '酉阳', '3', 'youyang', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3364', '394', '秀山', '3', 'xiushan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3365', '395', '沙田区', '3', 'shatianqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3366', '395', '东区', '3', 'dongqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3367', '395', '观塘区', '3', 'guantangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3368', '395', '黄大仙区', '3', 'huangdaxianqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3369', '395', '九龙城区', '3', 'jiulongchengqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3370', '395', '屯门区', '3', 'tunmenqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3371', '395', '葵青区', '3', 'kuiqingqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3372', '395', '元朗区', '3', 'yuanlangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3373', '395', '深水埗区', '3', 'shenshui', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3374', '395', '西贡区', '3', 'xigongqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3375', '395', '大埔区', '3', 'dapuqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3376', '395', '湾仔区', '3', 'wanziqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3377', '395', '油尖旺区', '3', 'youjianwangqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3378', '395', '北区', '3', 'beiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3379', '395', '南区', '3', 'nanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3380', '395', '荃湾区', '3', 'wanqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3381', '395', '中西区', '3', 'zhongxiqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3382', '395', '离岛区', '3', 'lidaoqu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3383', '396', '澳门', '3', 'aomen', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3384', '397', '台北', '3', 'taibei', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3385', '397', '高雄', '3', 'gaoxiong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3386', '397', '基隆', '3', 'jilong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3387', '397', '台中', '3', 'taizhong', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3388', '397', '台南', '3', 'tainan', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3389', '397', '新竹', '3', 'xinzhu', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3390', '397', '嘉义', '3', 'jiayi', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3391', '397', '宜兰县', '3', 'yilanxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3392', '397', '桃园县', '3', 'taoyuanxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3393', '397', '苗栗县', '3', 'miaolixian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3394', '397', '彰化县', '3', 'zhanghuaxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3395', '397', '南投县', '3', 'nantouxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3396', '397', '云林县', '3', 'yunlinxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3397', '397', '屏东县', '3', 'pingdongxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3398', '397', '台东县', '3', 'taidongxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3399', '397', '花莲县', '3', 'hualianxian', '0');
INSERT INTO `%DB_PREFIX%region_conf` VALUES ('3400', '397', '澎湖县', '3', 'penghuxian', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%role`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role`;
CREATE TABLE `%DB_PREFIX%role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%role
-- ----------------------------
INSERT INTO `%DB_PREFIX%role` VALUES ('2', '测试一组', '1', '0');
INSERT INTO `%DB_PREFIX%role` VALUES ('11', '管理员', '1', '0');
INSERT INTO `%DB_PREFIX%role` VALUES ('12', '客服', '1', '0');
INSERT INTO `%DB_PREFIX%role` VALUES ('14', '运营', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%role_access`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role_access`;
CREATE TABLE `%DB_PREFIX%role_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%role_access
-- ----------------------------
INSERT INTO `%DB_PREFIX%role_access` VALUES ('210', '14', '0', '3');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('211', '14', '0', '4');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('212', '12', '0', '2');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('213', '12', '0', '3');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('214', '12', '0', '4');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('215', '12', '0', '5');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('216', '12', '0', '6');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('217', '12', '0', '8');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('218', '12', '0', '9');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('219', '12', '0', '10');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('220', '12', '0', '11');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('221', '12', '0', '12');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('222', '12', '0', '40');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('223', '12', '0', '41');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('224', '12', '0', '42');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('225', '12', '0', '13');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('226', '12', '0', '14');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('227', '12', '0', '15');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('228', '12', '0', '16');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('229', '12', '0', '17');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('230', '12', '0', '18');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('231', '12', '0', '43');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('232', '12', '0', '19');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('233', '12', '0', '22');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('234', '12', '0', '44');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('235', '12', '0', '23');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('236', '12', '0', '45');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('237', '12', '0', '25');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('238', '12', '0', '28');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('240', '12', '0', '21');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('241', '11', '61', '40');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('242', '11', '85', '15');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('243', '2', '0', '2');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('244', '2', '0', '3');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('245', '2', '0', '4');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('246', '2', '0', '0');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('247', '2', '0', '5');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('248', '2', '0', '6');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('249', '2', '0', '8');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('250', '2', '0', '9');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('251', '2', '0', '10');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('252', '2', '0', '11');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('253', '2', '0', '12');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('254', '2', '0', '40');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('255', '2', '0', '41');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('256', '2', '0', '42');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('257', '2', '0', '0');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('258', '2', '0', '13');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('259', '2', '0', '14');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('260', '2', '0', '15');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('261', '2', '0', '16');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('262', '2', '0', '17');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('263', '2', '0', '18');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('264', '2', '0', '43');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('265', '2', '0', '19');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('266', '2', '0', '22');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('267', '2', '0', '44');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('268', '2', '0', '23');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('269', '2', '0', '45');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('270', '2', '0', '33');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('271', '2', '0', '28');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('273', '2', '0', '31');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('274', '2', '0', '34');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('275', '2', '0', '35');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('276', '2', '0', '21');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('277', '2', '0', '36');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('278', '2', '0', '37');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('279', '2', '0', '38');
INSERT INTO `%DB_PREFIX%role_access` VALUES ('280', '2', '0', '39');

-- ----------------------------
-- Table structure for `%DB_PREFIX%role_module`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role_module`;
CREATE TABLE `%DB_PREFIX%role_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%role_module
-- ----------------------------
INSERT INTO `%DB_PREFIX%role_module` VALUES ('1', 'Index', '系统首页', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('2', 'Log', '系统日志', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('3', 'UserGeneral', '主播管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('4', 'User', '认证主播', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('5', 'UserAudit', '主播审核', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('6', 'UserInvestor', '认证管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('7', 'UserBusinessInvestor', '企业待审认证', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('8', 'UserInvestorList', '所有认证', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('9', 'AuthentList', '认证名称列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('10', 'UserLevel', '等级管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('11', 'VideoCate', '话题管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('12', 'Video', '视频管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('13', 'Prop', '道具管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('14', 'Payment', '支付接口', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('15', 'RechargeNotice', '充值管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('16', 'UserRefundList', '提现管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('17', 'UserRefund', '提现审核', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('18', 'UserConfirmRefund', '提现确认', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('19', 'Tipoff', '举报管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('20', 'Nav', '前端管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('21', 'Conf', '移动平台管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('22', 'ArticleCate', '文章分类管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('23', 'Article', '文章管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('24', 'MsgTemplate', '短信邮件管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('25', 'Sms', '短信管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('26', 'PromoteMsgSms', '短信列表管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('27', 'StationMessage', '站内消息管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('28', 'DealMsgList', '业务队列管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('29', 'PromoteMsgList', '推广队列管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('30', 'StationMessageMsgList', '站内消息队列管理', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('31', 'IndexImage', '轮播广告', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('32', 'Help', '帮助列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('33', 'Faq', '常见问题', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('34', 'ExchangeRule', '兑换规则', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('35', 'RechargeRule', '购买规则', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('36', 'Role', '管理员分组', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('37', 'RoleTrash', '管理员分组回收站', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('38', 'Admin', '管理员列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('39', 'AdminTrash', '管理员回收站', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('40', 'VideoMonitor', '视频监控', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('41', 'VideoEnd', '直播结束', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('42', 'VideoPlayback', '回播列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('43', 'TipoffType', '举报类型', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('44', 'ArticleCateTrash', '文章分类回收站', '1', '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('45', 'ArticleTrash', '文章回收站', '1', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%role_node`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%role_node`;
CREATE TABLE `%DB_PREFIX%role_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  `is_delete` tinyint(1) NOT NULL,
  `group_id` int(11) NOT NULL COMMENT '后台分组菜单分组ID',
  `module_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%role_node
-- ----------------------------
INSERT INTO `%DB_PREFIX%role_node` VALUES ('3', 'index', '列表', '1', '0', '0', '2');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('4', 'index', '主播列表', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('5', 'add', '添加', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('6', 'edit', '编辑', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('7', 'delete', '删除', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('8', 'set_effect', '设置状态', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('9', 'focus_list', '关注列表', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('10', 'fans_list', '粉丝列表', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('11', 'del_focus_list', '删除关注', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('12', 'del_fans_list', '删除粉丝', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('13', 'contribution_list', '秀票贡献榜', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('14', 'del_contribution_list', '删除秀票贡献榜', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('15', 'account', '账户管理', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('16', 'account_detail', '帐户日志', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('17', 'foreverdelete_account_detail', '删除帐户日志', '1', '0', '0', '3');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('18', 'index', '列表', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('19', 'edit', '编辑', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('20', 'delete', '删除', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('21', 'update', '更新', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('22', 'set_effect', '设置状态', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('23', 'set_ban', '禁播', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('24', 'focus_list', '关注列表', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('25', 'fans_list', '粉丝列表', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('26', 'contribution_list', '秀票贡献榜', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('27', 'push', '消息推送', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('28', 'account', '账户管理', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('29', 'account_detail', '账户日志', '1', '0', '0', '4');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('30', 'index', '列表', '1', '0', '0', '5');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('31', 'edit', '编辑', '1', '0', '0', '5');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('32', 'delete', '删除', '1', '0', '0', '5');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('33', 'update', '更新', '1', '0', '0', '5');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('34', 'set_effect', '设置状态', '1', '0', '0', '5');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('35', 'index', '列表', '1', '0', '0', '6');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('36', 'show_content', '审核', '1', '0', '0', '6');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('37', 'index', '列表', '1', '0', '0', '8');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('38', 'show_content', '审核', '1', '0', '0', '8');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('39', 'index', '列表', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('40', 'add', '添加', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('41', 'edit', '编辑', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('42', 'delete', '移到回收站', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('43', 'update', '更新', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('44', 'restore', '恢复', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('45', 'set_sort', '排序', '1', '0', '0', '9');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('46', 'index', '列表', '1', '0', '0', '10');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('47', 'add', '添加', '1', '0', '0', '10');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('48', 'edit', '编辑', '1', '0', '0', '10');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('49', 'delete', '删除', '1', '0', '0', '10');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('50', 'index', '列表', '1', '0', '0', '11');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('51', 'add', '添加', '1', '0', '0', '11');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('52', 'edit', '编辑', '1', '0', '0', '11');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('53', 'foreverdelete', '彻底删除', '1', '0', '0', '11');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('54', 'set_sort', '排序', '1', '0', '0', '11');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('55', 'online_index', '直播列表', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('56', 'list_virtual', '计划列表', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('57', 'add_virtual', '添加计划', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('58', 'edit_virtual', '编辑计划', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('59', 'push_anchor', '粉丝推送', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('60', 'push_anchor_all', '全服推送', '1', '0', '0', '12');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('61', 'monitor', '监控列表', '1', '0', '0', '40');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('62', 'close_live', '关闭房间', '1', '0', '0', '40');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('63', 'endline_index', '直播结束', '1', '0', '0', '41');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('64', 'contribution_list', '贡献榜', '1', '0', '0', '41');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('65', 'delete', '删除结束视频', '1', '0', '0', '41');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('66', 'palyback_index', '回播列表', '1', '0', '0', '42');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('67', 'del_video', '删除回播视频', '1', '0', '0', '42');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('68', 'get_vodset', '查看视频', '1', '0', '0', '42');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('69', 'index', '列表', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('70', 'add', '添加', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('71', 'edit', '编辑', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('72', 'delete', '删除', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('73', 'update', '更新', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('74', 'restore', '恢复', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('75', 'set_effect', '设置状态', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('76', 'set_sort', '排序', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('77', 'prop_item', '子动画列表', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('78', 'add_prop_item', '添加子动画', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('79', 'edit_prop_item', '编辑子动画', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('80', 'del_prop_item', '删除子动画', '1', '0', '0', '13');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('81', 'index', '列表', '1', '0', '0', '14');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('82', 'install', '安装', '1', '0', '0', '14');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('83', 'edit', '编辑', '1', '0', '0', '14');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('84', 'uninstall', '卸载', '1', '0', '0', '14');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('85', 'index', '列表', '1', '0', '0', '15');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('86', 'delete', '删除', '1', '0', '0', '15');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('87', 'index', '列表', '1', '0', '0', '16');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('88', 'delete', '删除', '1', '0', '0', '16');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('89', 'refund_allow', '是否允许', '1', '0', '0', '16');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('90', 'refund_go_allow', '确认提现', '1', '0', '0', '16');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('91', 'index', '列表', '1', '0', '0', '18');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('92', 'delete', '删除', '1', '0', '0', '18');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('93', 'refund_confirm', '确认支付', '1', '0', '0', '18');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('94', 'index', '列表', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('95', 'add', '添加', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('96', 'edit', '编辑', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('97', 'foreverdelete', '彻底删除', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('98', 'update', '更新', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('99', 'set_effect', '设置状态', '1', '0', '0', '43');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('100', 'index', '列表', '1', '0', '0', '19');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('101', 'edit', '编辑', '1', '0', '0', '19');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('102', 'foreverdelete', '彻底删除', '1', '0', '0', '19');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('103', 'update', '更新', '1', '0', '0', '19');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('104', 'mobile', '手机端配置', '1', '0', '0', '21');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('105', 'savemobile', '保存手机端配置', '1', '0', '0', '21');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('106', 'index', '列表', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('107', 'add', '添加', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('108', 'edit', '编辑', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('109', 'delete', '删除', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('110', 'set_effect', '设置状态', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('111', 'set_sort', '排序', '1', '0', '0', '22');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('112', 'trash', '列表', '1', '0', '0', '44');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('113', 'foreverdelete', '彻底删除', '1', '0', '0', '44');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('114', 'index', '列表', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('115', 'add', '添加', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('116', 'edit', '编辑', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('117', 'delete', '删除', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('118', 'set_effect', '设置状态', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('119', 'set_sort', '排序', '1', '0', '0', '23');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('120', 'trash', '列表', '1', '0', '0', '45');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('121', 'foreverdelete', '彻底删除', '1', '0', '0', '45');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('122', 'index', '列表', '1', '0', '0', '24');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('123', 'update', '更新', '1', '0', '0', '24');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('124', 'index', '列表', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('125', 'install', '安装', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('126', 'uninstall', '卸载', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('127', 'edit', '编辑', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('128', 'update', '更新', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('129', 'set_effect', '设置状态', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('130', 'send_demo', '发送测试短信', '1', '0', '0', '25');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('131', 'sms_index', '列表', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('132', 'add_sms', '添加', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('133', 'edit_sms', '编辑', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('134', 'update_sms', '更新', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('135', 'foreverdelete', '彻底删除', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('136', 'index', '查看队列', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('137', 'show_content', '查看队列内容', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('138', 'send', '发送', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('139', 'smslist_foreverdelete', '删除队列', '1', '0', '0', '26');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('140', 'index', '列表', '1', '0', '0', '27');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('141', 'add', '添加', '1', '0', '0', '27');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('142', 'edit', '编辑', '1', '0', '0', '27');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('143', 'foreverdelete', '彻底删除', '1', '0', '0', '27');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('144', 'index', '列表', '1', '0', '0', '28');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('145', 'show_content', '查看', '1', '0', '0', '28');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('146', 'send', '发送', '1', '0', '0', '28');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('147', 'foreverdelete', '彻底删除', '1', '0', '0', '28');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('148', 'index', '列表', '1', '0', '0', '21');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('149', 'update', '更新', '1', '0', '0', '21');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('150', 'index', '列表', '1', '0', '0', '31');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('151', 'add', '添加', '1', '0', '0', '31');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('152', 'edit', '编辑', '1', '0', '0', '31');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('153', 'foreverdelete', '彻底删除', '1', '0', '0', '31');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('154', 'set_sort', '排序', '1', '0', '0', '31');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('155', 'index', '列表', '1', '0', '0', '33');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('156', 'add', '添加', '1', '0', '0', '33');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('157', 'edit', '编辑', '1', '0', '0', '33');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('158', 'foreverdelete', '彻底删除', '1', '0', '0', '33');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('159', 'set_sort', '排序', '1', '0', '0', '33');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('160', 'index', '列表', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('161', 'add', '添加', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('162', 'edit', '编辑', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('163', 'foreverdelete', '彻底删除', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('164', 'set_effect', '设置状态', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('165', 'set_sort', '排序', '1', '0', '0', '34');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('166', 'index', '列表', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('167', 'add', '添加', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('168', 'edit', '编辑', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('169', 'foreverdelete', '彻底删除', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('170', 'set_effect', '设置状态', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('171', 'set_sort', '排序', '1', '0', '0', '35');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('172', 'index', '列表', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('173', 'add', '添加', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('174', 'edit', '编辑', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('175', 'delete', '移到回收站', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('176', 'set_effect', '设置状态', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('177', 'set_default', '设置默认', '1', '0', '0', '36');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('178', 'trash', '列表', '1', '0', '0', '37');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('179', 'restore', '恢复', '1', '0', '0', '37');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('180', 'foreverdelete', '彻底删除', '1', '0', '0', '37');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('181', 'index', '列表', '1', '0', '0', '38');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('182', 'add', '添加', '1', '0', '0', '38');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('183', 'edit', '编辑', '1', '0', '0', '38');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('184', 'delete', '移到回收站', '1', '0', '0', '38');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('185', 'set_effect', '设置状态', '1', '0', '0', '38');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('186', 'trash', '列表', '1', '0', '0', '39');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('187', 'restore', '恢复', '1', '0', '0', '39');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('188', 'foreverdelete', '彻底删除', '1', '0', '0', '39');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('189', 'index', '列表', '1', '0', '0', '17');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('190', 'delete', '删除', '1', '0', '0', '17');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('191', 'refund_allow', '是否允许', '1', '0', '0', '17');
INSERT INTO `%DB_PREFIX%role_node` VALUES ('192', 'refund_go_allow', '确认提现', '1', '0', '0', '17');

-- ----------------------------
-- Table structure for `%DB_PREFIX%room_id`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%room_id`;
CREATE TABLE `%DB_PREFIX%room_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '所有用户的房间号ID从这张表中生成;这样就可以产生唯一,不重复的房间号了',
  `sysid` int(10) NOT NULL DEFAULT '0' COMMENT '所分配的系统',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='获取一个新的房间号,同时记录分配给那个系统使用;需要放在总数据库中';

-- ----------------------------
-- Records of %DB_PREFIX%room_id
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%slb_group`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%slb_group`;
CREATE TABLE `%DB_PREFIX%slb_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `api_url` varchar(100) NOT NULL COMMENT '接口服务器',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1启用;0:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='集群组配置';

-- ----------------------------
-- Records of %DB_PREFIX%slb_group
-- ----------------------------
INSERT INTO `%DB_PREFIX%slb_group` VALUES ('1', '红包服务器', 'http://ilvb.fanwe.net/mapi/index.php', '1');
INSERT INTO `%DB_PREFIX%slb_group` VALUES ('3', '礼物服务器', 'http://ilvb.fanwe.net/mapi/index.php', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%sms`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%sms`;
CREATE TABLE `%DB_PREFIX%sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `server_url` text NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `is_effect` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='// 短信';

-- ----------------------------
-- Records of %DB_PREFIX%sms
-- ----------------------------
INSERT INTO `%DB_PREFIX%sms` VALUES ('13', '千秀短信平台', '', 'FW', 'http://sms.fanwe.com/', '%DB_PREFIX%live', '%DB_PREFIX%live1', 'N;', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%tipoff`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%tipoff`;
CREATE TABLE `%DB_PREFIX%tipoff` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(10) NOT NULL COMMENT '举报人',
  `to_user_id` int(10) NOT NULL COMMENT '被举报人',
  `tipoff_type_id` int(10) NOT NULL DEFAULT '0' COMMENT '举报类型%DB_PREFIX%tipoff_type.id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '举报时间',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '被举报的房间ID',
  PRIMARY KEY (`id`),
  KEY `idx_to_001` (`to_user_id`) USING BTREE,
  KEY `idx_to_002` (`tipoff_type_id`) USING BTREE,
  KEY `idx_to_003` (`from_user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报';

-- ----------------------------
-- Records of %DB_PREFIX%tipoff
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%tipoff_type`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%tipoff_type`;
CREATE TABLE `%DB_PREFIX%tipoff_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '备注',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1：有效;0:无效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='举报';

-- ----------------------------
-- Records of %DB_PREFIX%tipoff_type
-- ----------------------------
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('1', '违法', '1');
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('2', '涉黄', '1');
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('3', '广告', '1');
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('4', '拉人', '1');
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('5', '其他', '1');
INSERT INTO `%DB_PREFIX%tipoff_type` VALUES ('13', '大写', '1');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user`;
CREATE TABLE `%DB_PREFIX%user` (
  `id` int(11) NOT NULL,
  `nick_name` varchar(100) NOT NULL COMMENT '用户昵称',
  `user_pwd` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL COMMENT '个性签名',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `is_authentication` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否认证 0指未认证  1指待审核 2指认证 3指审核不通过',
  `login_type` tinyint(1) NOT NULL COMMENT '0：微信；1：QQ；2：手机；3：微博',
  `is_effect` tinyint(1) NOT NULL,
  `money` decimal(20,2) NOT NULL COMMENT '金额',
  `login_ip` varchar(50) NOT NULL,
  `province` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `is_edit_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '能否修改性别 1为可修改 0为不可修改',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0:未知, 1-男，2-女',
  `birthday` int(11) NOT NULL COMMENT '出生日期',
  `is_remind` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接受推送消息 0-不接收，1-接收',
  `focus_count` int(11) NOT NULL DEFAULT '0' COMMENT '关注的人数',
  `intro` text NOT NULL COMMENT '个人简介',
  `code` varchar(255) NOT NULL,
  `sina_id` varchar(255) NOT NULL,
  `sina_token` varchar(255) NOT NULL,
  `sina_secret` varchar(255) NOT NULL,
  `sina_url` varchar(255) NOT NULL,
  `tencent_id` varchar(255) NOT NULL,
  `tencent_token` varchar(255) NOT NULL,
  `tencent_secret` varchar(255) NOT NULL,
  `tencent_url` varchar(255) NOT NULL,
  `verify` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL DEFAULT '1' COMMENT '用户等级;%DB_PREFIX%user_level.level',
  `mobile` varchar(20) NOT NULL,
  `user_type` int(11) NOT NULL DEFAULT '0' COMMENT '用户类型 0指 普通用户 ，1指企业会员',
  `is_has_send_success` tinyint(1) NOT NULL,
  `verify_setting_time` int(11) NOT NULL COMMENT '设置时间',
  `authentication_type` varchar(255) NOT NULL COMMENT '认证类型',
  `authentication_name` varchar(255) NOT NULL COMMENT '认证名称',
  `contact` varchar(255) NOT NULL COMMENT '联系方式',
  `from_platform` varchar(255) NOT NULL COMMENT '来自平台',
  `wiki` varchar(255) NOT NULL COMMENT '百度百科',
  `identify_hold_image` varchar(255) NOT NULL COMMENT '手持身份证照片',
  `identify_positive_image` varchar(255) NOT NULL COMMENT '身份证正面',
  `identify_nagative_image` varchar(255) NOT NULL COMMENT '身份证反面',
  `wx_openid` varchar(255) NOT NULL COMMENT '微信openid',
  `gz_openid` varchar(255) NOT NULL COMMENT '公众号的微信openid',
  `qq_openid` varchar(255) NOT NULL COMMENT 'QQopenid',
  `investor_send_info` varchar(255) NOT NULL COMMENT '审核信息',
  `paypassword` varchar(255) NOT NULL COMMENT '提现和支付密码',
  `source_url` varchar(255) NOT NULL COMMENT '来源url',
  `pid` int(11) NOT NULL COMMENT '推荐人id',
  `score` int(11) NOT NULL COMMENT '积分',
  `point` int(11) NOT NULL COMMENT '信用值',
  `emotional_state` varchar(20) NOT NULL DEFAULT '保密' COMMENT '情感状态',
  `job` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '主播' COMMENT '职位',
  `head_image` varchar(255) NOT NULL COMMENT '用户头像',
  `thumb_head_image` varchar(255) NOT NULL,
  `qq_id` varchar(255) NOT NULL,
  `qq_token` varchar(255) NOT NULL,
  `v_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '认证类型:0: 未认证;1:普通认证;2:企业认证;',
  `v_explain` varchar(255) DEFAULT NULL COMMENT '加v认证说明',
  `v_icon` varchar(255) DEFAULT NULL COMMENT '认证图标',
  `fans_count` int(11) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `ticket` int(11) NOT NULL DEFAULT '0' COMMENT '秀票数',
  `refund_ticket` int(11) NOT NULL COMMENT '已提现的秀票',
  `diamonds` int(11) NOT NULL DEFAULT '0' COMMENT '当前还剩秀豆数',
  `use_diamonds` int(10) NOT NULL DEFAULT '0' COMMENT '累计消费的秀豆数',
  `usersig` varchar(2000) NOT NULL COMMENT '用户签名',
  `expiry_after` int(10) NOT NULL COMMENT '用户签名过期时间',
  `is_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:用户在线;0:不在线;通过服务端监听更新',
  `wx_unionid` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL COMMENT '微信昵称',
  `synchronize` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否同步成功 : 0指同步失败 ， 1指同步成功',
  `login_time` datetime NOT NULL COMMENT '最近上线时间',
  `logout_time` datetime NOT NULL COMMENT '最近下线时间',
  `is_agree` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否同意直播协议 0 表示不同意 1表示同意',
  `online_time` int(11) NOT NULL COMMENT '用户总的观看时间，单位为秒',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注公众号 0未关注 1已关注',
  `is_robot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:机器人',
  `apns_code` varchar(64) NOT NULL COMMENT '友盟消息推送服务对设备的唯一标识。Android的device_token是44位字符串, iOS的device-token是64位。',
  `device_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:android; 2:ios',
  `video_count` int(10) NOT NULL DEFAULT '0' COMMENT '可回看的直播数量',
  `is_ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT '禁播状态 0-正常；1-禁播',
  `ban_time` int(11) NOT NULL COMMENT '禁播结束时间',
  `identify_number` varchar(32) NOT NULL COMMENT '身份证号码',
  `authent_list_id` int(10) NOT NULL DEFAULT '0' COMMENT '%DB_PREFIX%authent_list.id 认证ID',
  PRIMARY KEY (`id`),
  KEY `idx_u_001` (`is_effect`) USING BTREE,
  KEY `idx_u_002` (`is_robot`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//用户信息';

-- ----------------------------
-- Records of %DB_PREFIX%user
-- ----------------------------
INSERT INTO `%DB_PREFIX%user` VALUES ('1', 'Tim tina', 'asdqwieoqwe123132', '', '1464409434', '0', '0', '0', '1', '0.00', '117.25.60.29', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/EJwvpUdE23z6HicIC1umXJ2oZK1kGhOBM1PSj22O7YLS63xEKwIdQaO586ggBGllzhdgz6ovv3AbW4oEO3H2eIyWibp1NbAgJ9/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PgzAYRu-5FU1vNa4tTK13CEaXbIuLLIbeNEALFPkSyhga-7sTNTbx*pw3z3nfLQAADNZPF1GSNEOtuZ5aCcENgAie-8G2VYJHmtud*AflsVWd5FGqZTdDsqQEIVNRQtZapepXoNiAvXjh88DMsHM6RRQTaioqm*HmLvRWO4*VuzyxmSjogIOWUVzup9w90liy6cy34-t1XWx1FsTDuMrdR6f0q*y26sfRKxaHRfM8MIbD6W37ukk1GUX*4Po*q0LHnNSqkj9Bl9foiti22XyQXa*a*vsbhJcYY-rVDa0P6xPw51zf', '1468129931', '0', '', '', '1', '2016-07-12 19:35:20', '2016-07-12 19:36:37', '0', '287291', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('2', '潘潘哒', 'asdqwieoqwe123132', '', '1464733255', '0', '0', '0', '1', '0.00', '117.25.58.157', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/icMk2pDm9MCrKg2q1AT7pFt4t3UVshGzjHT8wHxxbGib3bCquX4164QD1V5349qaEZiabrbFxo9OfcOnG1Jgmm96KOAMibMJhg3R/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-Ypmrxhtq63WxIeCQ2RCNpQs*LJUWmaFbU1XZMz435GpcYnP37k55354AAD-6eHxVCyX5bZwqdsb5YNr4EP-5A*N0TIVLj238h*q2mirUrFyyraICcMQdiNaqsLplf4NMNrBSq7TtqA1dPF1ChnCrBvRWYuTYD647**SLEwSdDcZm3poJONqsekFcLafxrPeGo3iyEwrKXZxw3XAG33rwpwOLsUmGGb5OJqPsivyRvrsOazxomT8LNra5oW-8ptOpdO5*hlEKYEUM9LRd2UrXRbf30BEEELsuNv3Pr0DQC9bQA__', '1466563895', '0', '', '', '1', '2016-07-12 14:32:17', '2016-07-12 14:48:57', '1', '316048', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('3', 'tino', 'asdqwieoqwe123132', '', '1464742845', '0', '0', '0', '1', '0.00', '120.39.51.244', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCbIyQbfQzGAZgO1AibDBibjpxuUmCCicNb98yVwgic6VN4depmRmeAxBIlUiaeNDtziagpjAAaMbwZELURGibIh4exj9jK2lywV0tAUo/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19LwzAUR9-7KUJfK*6m-0YFH7I6bNC5iRnoXkq3pCWztjVNh9nwu6tVMeDzOZffuScHIeSy24fzYrdrh0bn2nTCRRfIBffsD3ad5Hmh80Dxf1C8dVKJvCi1UCP0o8QHsBXJRaNlKX*FZGrBnj-n48DIcPh5Cgn2E1uR1QgX83VKr6hX1o9RVsF*zW5K0S7N8pixLdtsnnw4tPWQeZIpL51SIucEr1hN7rz7bWHiyqQLQ2bDaqZUcE0nrwo6wzMfyORI9-2lNanli-gJiuMQwjiwgw5C9bJtvr8BHGGMk69u13l3PgAtI1sd', '1466462239', '0', '', '', '1', '2016-07-11 10:05:54', '2016-07-11 10:09:37', '1', '342889', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('4', '千秀服务雯子', 'asdqwieoqwe123132', '', '1464743587', '1465341064', '0', '0', '1', '0.00', '121.204.96.238', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '萝莉', '丫丫', '113110520670', 'Female', 'baidu.com', './public/attachment/201606/07/14/5756690fa06e8.png', './public/attachment/201606/07/14/575668ec0199b.png', './public/attachment/201606/07/14/5756690a6a847.png', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/icMk2pDm9MCoYAibYrc2pGn7lwrqZZM1tAx36gUicxB2YWgsqgGPicBLbe2arfCaNDEN1pyX30gOfQewMys6bmhP3ItAfticsS5qG/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXD64xtEXA18QEadCRbMkUW99R0UMwtjH-r5ojxu6uokcTn37k5575ZCCH7aZlcyixrjrURZmiVjW6QTeyLP2xbyIU04qrP-6E6t9ArIQuj*hEdjzmETCOQq9pAAb8BNp-gIS-FWDAadT9PCaMOm0bgZcRVlPKY45gX-V2CF3JFqQ71PX6s3JmXtRKHPDsOz1GwuN6e0-0QQBR0VfmQ8AHWAYON1vF2fdrFs7DA1a7pGr0xKStfO3e*NOntpNLAXv0M8n2Xej6hEz2p-gBN-f0NoR6llH3ttq136wMc-ltq', '1466473201', '1', '', '', '1', '2016-07-12 18:05:36', '2016-07-12 18:05:35', '1', '256362', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('5', 'star', 'asdqwieoqwe123132', '', '1465351424', '0', '0', '0', '1', '0.00', '121.204.96.222', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/icMk2pDm9MCrKg2q1AT7pFr9SaPMGsosOHP9ZiclgUHHe6OJVRZjj7Ue6ZpC6BjY60Obkyngl2L4T9396NXnnzVicD8PZtFnRVH/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXprca1hRq6xIsBJutw4sQZd9XUUUizyGop7Cv*d5UtsYnXz3lzznvyfN8HLw-FjVivt11juT1oCfyxDyC4-kOtVcmF5YEp-6Hca2UkF5WVZkBMKIbQjahSNlZV6hIIYOBgW274UDAYCn9OIUWYuhFVDzi-XyZskVCc5WS2a9*7CanmS1aFgtJR96SzI2RCH1eLXPTZTGxYzeo8mcJyEn6S2KJppLPVKH6Ez8lbU8T7q0PaGZOKIn1lfby7cyqt*pCXQbckiCDGkaO9NK3aNud3ISIIIfq7G3hf3jfp7Vq3', '1465437828', '0', '', '', '1', '2016-06-08 18:03:49', '2016-06-08 18:07:34', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('6', '木木岚', 'asdqwieoqwe123132', '', '1465354212', '0', '0', '0', '1', '0.00', '120.32.126.49', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/EWo3hwIVSD34Iegx56Kx4liadyWU2rbjicjW7LTUugG2icCeYiaN8q2Xp2QrNlmFxBUyD5CcFdic3e8vNwFm2qfn40mBvzBYKKrjB/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FaSvGtfSdYJvZHZC7DoJW9x8aRh02OmA0Iosxv-uhkts4vM5N9*5X47rumDJ0pssz*uPyghzbCRw71wAwfUfbBpViMwI3Bb-oOwb1UqR7YxsB*iRwIPQVlQhK6N26iJgOLagLt7EMDAwND6dwgB5ga2ocoBzupnGyTRc*GsqzS1Moi2dvd-znu31qusm8rmvFtSsTP0we8r1Rn-GryHft92hnMeRTtI1e2RpR3g*utqmJeU6RBxHJPRHLCcvS2vSqIO8BE0I9jFEdnMnW63q6vddiAhCKDh3A*fb*QFpe1ti', '1465440614', '0', '', '', '1', '2016-06-08 18:51:47', '2016-06-08 19:11:43', '1', '2282', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('7', 'SUN', 'asdqwieoqwe123132', '', '1465364156', '0', '0', '0', '1', '0.00', '101.90.252.225', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/EJwvpUdE23y9G2DsEzibrEkPSBOIJApclBkwxBLLE7RvxibCMWzZ7eEibeWBkMM0ly9Sic8AfQqQ1OzmpKOQD83qaA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0FPg0AQBeA7v4Jw1dhdEBATD0hcqcFEqZDCZUNgSidNYVkWpW387yo2kcTz9ybvzUnTdd14i1ZXRVm2Q6O4Oggw9FvdIMblHwqBFS8Ut2T1D2EUKIEXGwVyQtP2TELmEaygUbjBc8Ai9gz7asengsno9fcp8ajpzSNYT-j8kATL16Be5G2eLuQ4yPjIyohh467StO1e3JF1H34HBECkvhnWy5o9Dft*vV0jbRn6VnSfOVvcNXEexJCFyfHi0clCPzrkQXI3q1S4h-Mgx72xPdd2ZvoOsse2*X2XUJtS6v3sNrRP7QvJf1xP', '1467917356', '0', '', '', '1', '2016-07-09 18:26:34', '2016-07-09 18:34:23', '1', '837', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('8', '祁红英', 'asdqwieoqwe123132', '', '1465404978', '0', '0', '0', '1', '0.00', '222.76.72.241', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/icMk2pDm9MCrKg2q1AT7pFsAJFgOH298Oq0QRdn1W7cczFofdq17zVBKILUNa3SxAv4fQTXotoc9DjIGFok3mReNDu23MsW46/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXD7Yz2Awr1blnQTUcWMhIzbxqkBSpSEDocLP53FZdI4vVz3pzzni0AgB1v99dJmtZHbbgZGmmDW2BD**oPm0YJnhhOWvEP5alRreRJZmQ7IXYZhnAeUUJqozJ1CRDoz7ATJZ8KJkPO9ylkCLN5ROUThsFhtYlWI2OErr3FoOM8qOmRxZ07*qcSvSZZVURPL3fbBX7QH0UebYrlTi6rkVH2iG9KKOLG29MoCA-vu3WR9KKXg2ZO-Zzfp*HbrNKoSl4GUdchxPPRTHvZdqrWv*9C5CKE2M9u2-q0vgA2gluA', '1465491381', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 00:40:35', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('9', 'Mr、大少爷 ', 'asdqwieoqwe123132', '', '1465409431', '0', '0', '0', '1', '0.00', '220.115.213.241', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/IUPesY0ZY3kIEBTtPibe8ibEJJaSuQE27YnhCU5K32NnMfX4vicDUsIAXaz2ibR0q58Qqu6Hfcib8LzHFDAPOffkMmlcOx7wbu25k/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDbY3rB8zhHakfQaebGULcTUPW4upcV0pZGMb-rsMlYrx*zptz3g8PAOCn08V5sVrtGu24Oxjpg0vgI--sF41RgheOUyv*oWyNspIXpZO2RxJGBKFhRAmpnSrVKUBRNMBabHhf0BsOvk9RhMmfiHrt8eH6hSVPLK-DUSl0MKqwTDLE6nUzsybrrto5qyB*S6nontsuuINxso5nW*xgfL9M35t0mmc5GusOMmknhxtINoTtpVi2F5W9fcSDSqe28jRoHAZ0Qigd6F7aWu30z7sIhxjj6Ljb9z69LzBsWys_', '1465495833', '0', '', '', '1', '2016-06-09 10:10:34', '2016-06-09 10:11:13', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('10', '超卡的我都快', 'asdqwieoqwe123132', '', '1465615638', '0', '0', '0', '1', '0.00', '222.76.123.202', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/lhchyTqMYQ1icyC1SvORpwQbH7oPmDthHNyf1NSPGcsKh39JHcFaibPjrv9ibqbt1cGXK28vQmLEjlXibwwDs82t4dtPtvZa7u1k/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj8tOwzAQRff5CivbImQ7TyOxgLSF0gfQkkZ0Y0W1E40gbhI7tBXi3ymhEpaY7blX98yngxByX2ary3y73XXKcHOspYuukIvdiz9Y1yB4brjXin9QHmpoJc8LI9se0oBRjO0ICKkMFHAOeMSGWrzxfqBnxD9VMSOU2REoezgfpcnkOama9bvUm*YpjmYevh-o7HaapGOzaIYH8RpV5eiYqsd8EG32k3K1NIoN44dofic68Mdh02UxLQugazatsuUN6JT5ch8y-9qaNFDJs1AYhP7pbOcP2WrYqd93MQkIIezH23W*nG8eOFrb', '1465702040', '0', '', '', '1', '2016-06-16 16:08:48', '2016-06-16 16:10:41', '1', '127', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('11', '雷芳', 'asdqwieoqwe123132', '', '1465619149', '0', '0', '0', '1', '0.00', '120.32.127.233', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/EJwvpUdE23yfEAWwKtJPNrwmXKGmkoYe5SdyrC3icgVZP4QiccE4pCUtPiaeTXzAh2MrES2Zck1m7Gs1FcgpeYJEia9HxUUMTheL/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0FPgzAAhe-8CtKz0bYDlpp42HSLS9wyBJTt0uBaZqe0rLQVNP53FZdI4vn7Xt57H57v*yC9S86L3U5Zaajpag78Sx9AcPYH61owWhg60uwf5G0tNKdFabjuIQ4JhnCoCMalEaU4CSOEBrBhL7Qv6BkKvqOQIEyGitj3cDnLrhc398eMV20ePa7c*mm1HcebJm2KNlBITznPyyJ1Zp5kNnifiNnkUC6l3T-Mn9n2rcL2Qh1LpzYyd6*JxYlqb-NpvI67Q5curgaVRlT8NCgKo2BMouEhx3UjlPy9C1GIECI-u4H36X0BeKleKw__', '1465705560', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 17:51:48', '1', '31', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('12', '云管家 微信文章广告植入神器', 'asdqwieoqwe123132', '', '1465664493', '0', '0', '0', '1', '0.00', '221.178.182.58', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDfUlkGqbDWV0mXzRePVOsj6aHFeiabo1NibfL1CI8qluu7seiafC0DtGDgibKbdlgQhmCNzB2X6h5z0Q/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FLwzAUBeD3-oqQ14kmqZmL4EPdpkTWSWmn1JdQm6SksrZLs6oT-7taBwZ8-s7lnPsRAABgtkpPi7Js940T7r1TEFwCiODJH3adkaJwIrTyH6q3zlglCu2UHZFQRhDyI0aqxhltjoEQEw97*SLGgtHw*fcpYpgwP2KqEePlZs6TudnkB97nE15lryXvztRj1uJc79KVfrrDdcSierggciJZwqvl-UynN3Ff79D6mRgi1-vFYK*pVbdDuZCU1rNDUrHtQxRfeZXObNVx0JROWUgY9XRQtjdt8-suwhRjzH52w*Az*AJGo1tV', '1465750895', '0', '', '', '1', '2016-06-12 09:01:35', '2016-06-12 09:11:10', '1', '122', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('13', '试错不是错', 'asdqwieoqwe123132', '', '1465666380', '0', '0', '0', '1', '0.00', '120.32.210.230', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEL9OichoVL7icAGDSREYmWiaJ8a5xuXdBt5BmicECoNMU2XTQSaVMKemjA4140HLlM7nGxqzlyicl6Tzag/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXhdcbQ8mfWZA9ohxg7tjGc*tQg7chVxyqrjM343VW2xCY*-87NOffTsm3bydnivCjLzUetud4r6diXtuM6Z3*oFAheaO414h-KTkEjebHSsukRBwS7rhkBIWsNKzgFPOQbuBWvvC-oDfk-py5BmJgRqHqcjOfXt0nKEhR38TsQqCgdsgl*EvQlfcweqreiXN5ng*hAZiHr2giu6HrgBWqWL3OfPtN0L1pEm3iMp7syz7qdig4sqW-w3RDPRyOjUsNangaFQUgCdIENbWWzhU19fNdFAUKI-O52rC-rG90BWnw_', '1465752782', '0', '', '', '1', '2016-06-14 19:43:54', '2016-06-14 19:43:57', '1', '317', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('14', '李杭泽', 'asdqwieoqwe123132', '', '1465666635', '0', '0', '0', '1', '0.00', '117.87.3.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/icMk2pDm9MCrKg2q1AT7pFo7OEUswF8OtX3Bbf0P0oxXyBat5PVqN1ibOUCD8XznRIdIBBCGf9Woqib2ZT7tF6vBvrdX7IsuLSE/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAUBuB7fkXTa2P6QRk18WJTwCnMTNmcu2mQdqRZYNh27sP43524RBIvztXznrznfHoAAJinz5dFWW62jRPu0CoIrgBE8OIP21ZLUThBjfyHat9qo0Sxcsp0SBgnCPUjWqrG6ZU*ByhmPbRyLbqCzrB-WkUcE96P6KrDLJrdjKe36DBbFOXDYEvvGCF2mDpnKz96n6Tz3XSUGjM45lkdBcbsxlUyz*kQh0s7iu5lUi98*UhfeMDf4uOShU9Zbic1fY3jJllf9yqdrtX5oCDA4WloTz*UsXrT-L6LMMMY85*7offlfQP11VqY', '1466244213', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-18 02:04:10', '1', '772', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('15', '龙', 'asdqwieoqwe123132', '', '1465668959', '0', '0', '0', '1', '0.00', '123.164.109.76', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/EWo3hwIVSD34Iegx56Kx4gWwichjmea2LEtCTlQd79nrVAU0OmCsO5FdYeGAJfvOhD40lFd73GrvIfhBbOVbM0I7urqiaor93R/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTZ*PazkJqsgeGE*fAZU6jPjUNFNYxoZQ6BON-V3GJJD5-5*ac**EAAOBDtD0XSVK9lZbbTksILgFE8OwPtVYpF5ZPTfoP5btWRnKRWWkGJJQRhMYRlcrSqkydAlPsjbBJCz4UDIYvvk8Rw4SNIyofMF5sguW86NftfSQOzV2A*maZVYsr-7Ct13uxn3gyyKPMhuI2zkzuK1*2tl4VYUgrd94*JaJe6eOG1mTXSfIS3zx2*nlXXk*E6vPZbFRp1as8DXKpyzzPJSM9StOoqvx9F2GKMWY-u6Hz6XwBKbRdWw__', '1465755362', '0', '', '', '1', '2016-06-12 15:39:53', '2016-06-12 15:56:33', '0', '67', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('16', '娱乐无穷', 'asdqwieoqwe123132', '', '1465672642', '0', '0', '0', '1', '0.00', '110.231.133.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/IUPesY0ZY3lH3be7ibCXTkKkXBf4oiat2eVBibqgJ2X7O8dtBHwz7c4KXMurqQxRHMhoJT9DNGwHYmJTnNP9TvpbFJ3ROsQFwK3/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXhdUZbCltqsgfGXO0mOjKG6EvT0W5ryFiFSkaM-32KS8T4-J2bc*6HZdu2kzysrnmeH99Lw0yrpWPf2g5wrn5RayUYNwxV4h-Kk1aVZHxrZNWh62MXgH5ECVkatVWXAIK4h7UoWFfQGfS*TgGG7p*I2nUY3cUhJcNQp4tVvh5UOiOzCdmT7JQ803a3GSwg5WFBKX99LIJJEqjgBWWbKUlvpqOoRuBpeW-m**gwz9ZNg0Sax29NottypjCKx*NepVEHeRk09EcAep7X00ZWtTqWP*8C6EMI8fdux-q0zvlIWtM_', '1465759044', '0', '', '', '1', '2016-06-12 14:58:15', '2016-06-12 14:58:54', '1', '7265', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('17', '娱乐无极限', 'asdqwieoqwe123132', '', '1465686407', '0', '0', '0', '1', '0.00', '110.231.133.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA0icg47bjVgIg4BbtyYmicVjIcCyKWCT7D3jce9qyQr4AAjy9gAtPpAAXawnIssXD0mfTZgc0fLpFR/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FU1fNaYtMsTEB4Z1EJmJmQP3RFhbtCisKWVsGP*7ikts4vM5N9*5Hw4AAD6lq4uSsV3fmsIclYDgGkAEz-*gUpIXpSlczf9BcVBSi6KsjNATJF5AELIVyUVrZCVPgkuwBTv*VkwDE8OX36cowCSwFfkywSVdR8miyRLe9j7P45HW2yTN6vkQng163bFqY5JFhMvl-L6OSRNKGsoryvJNwNz98EirSHRZPr4*xOOqT5*pH6rDXT6j1TZ8v2U31qSRjTgFzTwfewTZQXuhO7lrf99F2MMYBz-d0Pl0vgCh2lwX', '1465772809', '0', '', '', '1', '2016-06-12 15:06:50', '2016-06-12 16:04:19', '1', '1578', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('18', '诫', 'asdqwieoqwe123132', '', '1465689875', '0', '0', '0', '1', '0.00', '115.60.139.154', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLC1usXw2nWmLR8VSZ0YqNiblT8FibHdWhiclSBiacsc6wJl9duP1IfknodicNry5lI0qGGftmmribYfxBrQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXD64y2xYLXN2UsTpkbw4g*EQIFqoGVrkPA*N91uEQSn79zc879NBBC5pMfnidpujvUOta95Ca6RiY2z-5QSpHFiY4tlf1D3kmheJzkmqsRKQOK8TQiMl5rkYtTwKJ0gvvsPR4LRiOXP6cYCIVpRBQjrrxXdxm4jlVsZtWihKIPAyr1y1vPF8-h48VKQtTSBrrmau3et34ULMsbf*aASudDx1Q-RMMHbNdDDk7RzDfE81PnoScHz767LZtqUqlFxU*DbJsxBsSaaMvVXuzq33cxYYQQOO42jS-jG1a-W6E_', '1466613513', '0', '', '', '1', '2016-06-22 08:38:34', '2016-06-22 08:57:05', '0', '58', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('19', '舍我其谁', 'asdqwieoqwe123132', '', '1465744852', '0', '0', '0', '1', '0.00', '140.237.24.241', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIV510VBkREN2rudHZeqeqaSUaUAicFsIL4okWRBmO7DpyDD7XfK33SrIv4UTPgYKVlpnS94icQyibyic/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FaSvGm2L4OqbI8qQbXFIlulLA7TIjWtHaBlT439X2RJJfD7n5jv303FdF2Xzp4u8LHedtty*NxK5Ny7C6PwPNg0InlvuteIflIcGWsnzysp2gNRnFOOxAkJqCxWcBI-6I2jEGx8GBkaufk4xI5SNFXgd4OLuOYxXYabJx1QHYfWwTe-LwuuixEagVJtgo4p6zS5nut6Yx5dZH9e382XWdJXd04OIV1RAOrGb6XKhlDlLg7roZU6TyDM9bOPRpAUlT0GBz64nzB8H7WVrYKeP72LiE0LYbzdyvpxv4mRc4A__', '1466036559', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-15 17:02:24', '0', '172', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('20', '独自远行灬刘欢', 'asdqwieoqwe123132', '', '1465757511', '0', '0', '0', '1', '0.00', '113.90.119.12', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM73Nuic8zC46cf5lPkBibiauk9FR9Su2sc558PqWteIwbQld8L9YNsw1h3myDejlovO519Hd4HriaLdiaQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PgzAARd-5FaSvGtcCBeobw6lkskDED3xpGJTZLRRSOrbO*N9VXCKJz*fc3Hs-DNM0QfbweFWUZbsXiirdMWBemwCCyz-YdbyihaK2rP5Bduy4ZLSoFZMjtDCxIJwqvGJC8ZqfBdvyJrCvdnQsGBlyvqOQIItMFb4ZYbzIwygN4V0cZJkXa5Iks5StDy-CCRS*3W9v*u3Tcq6TxiflEM6Ph*g9WK7yXGMb6*d7IoY3ka7SOEcz5XunqNmE69aFuwtZvi6GelKpeMPOg1zs*a7tTA8NTPa8Fb93IcIIIfKzGxifxhcme1tJ', '1465843940', '0', '', '', '1', '2016-06-13 17:20:18', '2016-06-13 17:20:37', '1', '1513', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('21', '戴金', 'asdqwieoqwe123132', '', '1465772118', '0', '0', '0', '1', '0.00', '113.46.99.1', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLARUgqHOdlkcx8lhsrBbclydHgNTcQuCtYsVIja3hPNArcA0FXmNsPxLBNet3NIYHgb8XJ3MmgBiaA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PwjAYhe-3K5bdYqTtHFLvwKGbMOVjIFw1De3Yi9LVURBm-O-OhsQlXj-PyTnny3Fd10tHs2u*XhcHZZg5a*m5d66HvKs-qDUIxg3zS-EPypOGUjKeGVlaSAJKEGoqIKQykMFF8H3cgHvxxmyBZfimjiKKCW0qsLEwGazu40mY6kWVVFFS6nwYPg1FH5FVBmSa5Tvlx8s5hXE6Kto4Cidx3kvGH4-dA551*ot3zeH5ITi1Nol-7kXp66BSS-5yu-2cqm27UI1KAzt5GdQJuvUi0jx0lOUeat-eRTjAGNPf3Z7z7fwARCxbcA__', '1465858520', '0', '', '', '1', '2016-06-20 17:55:58', '2016-06-20 18:01:11', '1', '4112', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('22', 'ECGO', 'asdqwieoqwe123132', '', '1465775101', '0', '0', '0', '1', '0.00', '124.244.185.224', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzndr81WCPvBXhHFFhPJxBceEVAXzwtAv89AEBnuwX6VuvdJvHHMkH1q0TROtOy81jMKeCVX6ecibegHb6yDEcFlK/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FQ2vGNMWyoaJD0aGMJmwCXHhhTBauoYIDesWnPG-63CJTXw*5*Y799MAAJhZ-Hpb1XV-7FSpPiQzwR0woXnzB6UUtKxUaQ-0H2SjFAMrq0axYYKYeBhCXRGUdUo04irYNtbggbblNDAx5PycQg9hT1cEn*BqkT9Gaz9dhllRF0uWvoh9X*DExfnC3xBZsxadrcRqdzYNA-QW8Iivti0untJ4s8-dbB5sYU-J0a-GaNeEJ8osHhMnSeUzHx-utUkl3tk1yCXefObM9OYTGw6i737fhYgghLxLt2l8Gd-VdFqI', '1466045072', '0', '', '', '1', '2016-06-15 18:44:33', '2016-06-15 18:45:38', '0', '83', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('23', '郎道', 'asdqwieoqwe123132', '', '1465778571', '0', '0', '0', '1', '0.00', '42.243.60.225', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeeh5rUqAeTt2Oaibr8AIQRv1KXFIP8C9RKJk3R0vFxvalklHtdTIF3upp2ChHuiaHVwk0woNOlg0kliaBugESceI7Wn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXT2xlp6UAx8YIhGJSZRZgZVw2DgmUMuq77NP53FZfYxOvnvDnn-TAAADCNk*u8KPpdp6g6CQbBHYAIXv2hELykuaJElv*QHQWXjOaVYnJAy3YthPQIL1mneMUvAUKIhttyRYeCwfD4*xS52HL1CK8HnAZzP-LPpAnWy8wjVdO-vs0OJNq9RO-hJCZJ4tf7ZXE6M4yPfIM8HnijR9U*W-biKU2zldm2QT8x5w9TFcaLVoVKzkZxvUml2YjsXqtUfM0ugxzHGbvWza2meya3vO9*30XYxhi7P7uh8Wl8AWpQW8M_', '1466706878', '0', '', '', '1', '2016-06-23 20:13:25', '2016-06-23 20:18:53', '1', '3351', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('24', 'Adixim Shop', 'asdqwieoqwe123132', '', '1465781368', '0', '0', '0', '1', '0.00', '36.186.0.57', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS01nuUQiaQJCsSuc5AKUtq88ibDyeCXx0Kq8NWt33iaS6e5OdHssAPo7plD3jw6ice7iawRH9iaUcO6cBPF/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1tPwjAYBuD7-YqmtxrtYQdqwgUeYBicugHhrplb6yowurYO1Pjf1UniEq*f98v7fh8eAADOZ9lZXhS719px96YFBBcAInj6h1qrkueOU1P*Q3HQygieSydMhyRgBKF*RJWidkqqY4BSv4e2XPOuoDPsf58ihgnrR9Rzh3c3j1fTiSlsSBs1ILeTjf-i4mT8rlcomO3vqVteLg5N0z5VcZudo5EabdaVLOLFiUhta7fLMI5MnqGEpuMmuY706mE*ZTatZSX3w2Gv0qmtOA4KgwFGOCI9bYWxalf-votwgDFmP7uh9*l9AYk8W-g_', '1465867772', '0', '', '', '1', '2016-06-13 17:29:34', '2016-06-13 17:52:10', '1', '64', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('25', 'lara13粉丝团长', 'asdqwieoqwe123132', '', '1465844306', '0', '0', '0', '1', '0.00', '125.119.82.224', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKrGZl6ia3PlWF4rqc6NCZ7CiavCc8XfHH6yqnEHZj4icopgy7b0ibHicqykmjVWvECuSTNCzpOicsV17MA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTV4y21CL4tnTEsZWoYTr1pcFRWJ20UKrbYvzvKi6RxOfv3JxzPzwAAFzy-LRYr82bdsIdWgnBJYAInvxh26pSFE4QW-5DuW*VlaKonLQDBjQOEBpHVCm1U5U6BgihI*zLrRgKBsPn36coxkE8jqh6wCx5ZOntlE2TlXENDeh9PptfT3qno*61O1uFqjL1E-UXi63P8wzvd*lmwnU9T-Emu3qpeIfwHeM232XPS*58f8bCB31ITMPZTUy6UaVTjTwOCml0QTCKRvouba*M-n0XYYoxjn92Q*-T*wIBwlrK', '1465930708', '0', '', '', '1', '2016-06-14 12:01:38', '2016-06-14 12:04:15', '1', '338', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('26', '贵州土豆', 'asdqwieoqwe123132', '', '1465844501', '0', '0', '0', '1', '0.00', '111.123.227.47', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxziarAR38zdGMs1QsRLCKmibHRY0Lz7f1UK343z8bjiaQpSGvibPOWqIiaibpVxeZA24NukAr9EPlDYntiaosBwPGZu48e/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTayMtlWq9qw5Dp24ZLgt60xAoo-uAWjrdZvzvKi6xidfPeXPO*xEAAOD84em8KMtu1zrpDkZBcA0ggmd-aIyuZOEksdU-VHujrZJF7ZQdMIpZhJAf0ZVqna71KUAI9bCv1nIoGAxffJ8ihiPmR-RywMfk*VbMRsLtk5s7zbJdkxs*2izoJMlMPp*lipfO8HBMXJ*amot30fB7JbaT6XKdHw98tTpmtKavnGain75wotJm0cWbcRlaHCZepdNbdRpE46tLQhDx9E3ZXnft77sIxxhj9rMbBp-BF4BYW8E_', '1465930903', '0', '', '', '1', '2016-06-14 11:01:44', '2016-06-14 11:06:25', '1', '250', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('27', 'William', 'asdqwieoqwe123132', '', '1465845313', '0', '0', '0', '1', '0.00', '125.120.228.9', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxzPkicnXU6KgsLB19k3BdhL6EzRdmnpgT1v4AFF6vjTlVkl0uR0nocVOibUv94SVibgekyNGyOREfTkg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDtXEtjEFNvGC48KGQzLmaeNOw0ZnqykfpxprF-67DJTbx*jlvznnPFgDAfnla3ZbbbXOoFVW6ZTa4Aza0b-6wbXlFS0VdWf1Ddmq5ZLTcKSZHdDzsQGhGeMVqxXf8GnBd38C**qRjwWho*nMKMXKwGeHvI*aLdZTGUu83JCsEngtfnjzyoEin3kQ2ZJPeeY2Pm49EJKLrkjjki1CkTff4vA8mrBj6YY2WOfF0WM2V1kUU9YHI1GFJYBrl8N6oVFyw66CZF-hT5MwMPTLZ86b*fRciDyGEL7tt68v6Bu2QXJM_', '1465931726', '0', '', '', '1', '2016-06-14 13:28:40', '2016-06-14 14:03:52', '1', '5278', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('28', '梦里花落知多少', 'asdqwieoqwe123132', '', '1465846861', '0', '0', '0', '1', '0.00', '27.199.14.87', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxzSF6tUf0K1eS0aqoyP3z9FSzggBaf0SiaRNtroz5ecF6fib9FtpakL5kub0VpTx3gyopDMicUgM3zRic6yBLd87uby/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj81OwkAYRfd9ikm3GJ2vPyNjwgILRrFFSyGkq6a0g340DG0Zflrju6uVxElcn3Nz7-0wCCHm3I*u0yzbHaRKVFMKk9wRk5pXf7AsMU9Sldh1-g*Kc4m1SNK1EnUHLZdblOoK5kIqXONFsO2*Bvd5kXQFHQPnO0o5WFxX8K2DwTj0nka*LG5Gt*FCCvkYvbib9HkVYxQ0E59tFw68ZuDRJsNq1hvi-TTwDm2-N2*r5ftDjKuZy6wllypuKwWTEztuxkVwmjphMBwMtEqFW3EZxBhltg36oaOo97iTv3cpuADAf3abxqfxBShyWyE_', '1466120910', '0', '', '', '1', '2016-06-30 16:27:54', '2016-06-30 19:26:47', '1', '210', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('29', 'Ericwang[王宝太]', 'asdqwieoqwe123132', '', '1465858089', '0', '0', '0', '1', '0.00', '101.81.141.0', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCwutjiciaax7oOQ5rnrFkS5MrOjpQJKbhEcG8KDaYjLNd5UGlwO02j2NQw4FmVFIzjPG0tibMeUaXnQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXTV422dCzUZA8LAzczx7*5V4K0HTdjwFjnGMbvruISMT7-zs05991ACOH1Mr5Ls6w6lTrRl1pi9IAwwbe-WNcgklQnrBH-ULY1NDJJlZZNj6bFTUKGERCy1KDgGmCMD-Aodklf0BsdfZ0STs0-Edj2*OyGzsLr5OtTHvgrlc1F553rYukUi-3h-mamQk9BuYtdH4rHuNtMYboSFzVaH*IqgqBlUbgpWLWFwA1fInBOeiazdm7mfq7i82QyqNSwl9dBY8u2xzZnA32TzRGq8uddQi1KKf-ejY0P4xMkRV1H', '1465944493', '0', '', '', '1', '2016-06-14 14:48:14', '2016-06-14 15:18:11', '1', '549', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('30', '陆钧', 'asdqwieoqwe123132', '', '1465858489', '0', '0', '0', '1', '0.00', '101.81.141.0', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCJIqfMXSbakTM5uibjWnYHiboNcGu0ibfZibUia34mNmvx0GDJiciaQxgnHv3QibO1t6sfPdFFliat3eKr5Jw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXhFqMtg9GaeEFwRnBLtmxs6E2D0GE3xmehDON-1*ESm3j9nDfnvJ*KqqraZr6*jeK4aHNO*LmkmnqvakC7*cOyZAmJOJnUyT*kfclqSqI9p-WIhoUNAOQIS2jO2Z5dAxNTxiY5krFgNGj*nAIMDSxHWDriYha43sqN27wsXBGyynfCYRbY891T1WYYBW48nNBWiI6-9o2j68JLd2g4hs70za*9TlRZ3z4enPe7l429-DCX9LB*9rN8oespRasHqZKzE70OmloI2QY2Je1o3bAi-30XQAtCiC*7NeVL*QYLi1zQ', '1465944894', '0', '', '', '1', '2016-06-14 14:54:55', '2016-06-14 15:00:40', '1', '172', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('31', '好名字可以讓你的朋友更容易記住你', 'asdqwieoqwe123132', '', '1465860945', '0', '0', '0', '1', '0.00', '106.57.94.38', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxwCjMnyrcBc3S805pzRq7YQZkPx4ouIBH1YwWAsArcxP2htvgXeh8JFHHxCaicpe9jpyn49ibQnIGwpwvr8w1VsK4/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTW41pO77qHYMZYS5rs6Fy1ZDRQd0GBDoFjf9dxSU28fo5b855PywAANw*bG7y3a4511rosZUQ3AKI4PUftq0qRK7FrCv*oRxa1UmR77XsJiQOJQiZEVXIWqu9ugRmNjawLw5iKpgM29*niGJCzYgqJ1wtsjDm4eMhiDhvUndOhjFNz**MHXmbr5ZltnYp95-01Yn4x5cqDuIqSNibrOhaR9jOkiFUaEBRf8eeHTby7UJuMu9*Hi9Z0pdmpVYneRnkOr5PPdsz9FV2vWrq33cRdjDG9Gc3tD6tLxu1Wu0_', '1465947346', '0', '', '', '1', '2016-06-18 18:47:01', '2016-06-18 18:57:16', '0', '97', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('32', '王剑龙', 'asdqwieoqwe123132', '', '1465924996', '0', '0', '0', '1', '0.00', '121.204.96.219', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS05K7e9Ik22cZrGN7XpHjdUCVw5IwCic5xOSibylaBd0dmL29ic4aqTeBZE0fQtsWL3FkjXDicwU64Nbq/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXps9EWCg7fKi4ZYYubzCFPDdLCKlshpdN1xv*usiU28fk7N*fcT8d1XbCeZ9dlVXUHqak2PQfunQsguPrDvheMlpr6iv1DfuyF4rSsNVcjekHkQWhHBONSi1pcAj7GFg6spWPBaAj-nMIIeZEdEc2Ii2kRJ6vYyEnB8*0r9tgT6YJ0r4q0maazxQ1ca5GZLgkkI-WzwSTZksdCbQrjv7TDDIf3lTqtPojcxbmpHg7yLV9mSbtbTuZVI45WpRZ7fhkUhhDfYs*39J2rQXTy-C5EAUIo*t0NnC-nG7ksXKU_', '1466105023', '0', '', '', '1', '2016-06-16 11:24:27', '2016-06-16 11:41:07', '1', '3246', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('33', '焦傲', 'asdqwieoqwe123132', '', '1465933744', '0', '0', '0', '1', '0.00', '123.147.244.92', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7Mm9Wp8hJ5eqtGf9pFsMyk5tscHKQdKML7hqKAOPsCHbXAmkZInDXXic9vB02LH4I6tpNicxNialrAujqiaWQYTx8vJ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19PwjAUR9-3KZq9zph2ZYyZ*LAMpkRhGsR-L01hZVwXutrWwmL87uokccbnc25*5757CCH-7npxytfr5k1aZlslfHSGfOyf-EKloGTcMqrLf1AcFGjB*MYK3cEwSkKM*wqUQlrYwFGgg7gHTVmzbqBjZPB1ihMSJn0Fqg7OJstsepvl5j6bj7b2YbyULqgvTfq0k6*L5wI7u6L4BVKq8-koLUw1rfIoTrYK*MyFjy5tb66a9iKG-WpyqAtHjQtE4DjPKBlX571JCztxDBoOCQ3Jn2YntIFG-ryLSUQISb67fe-D*wSMcVvy', '1466189747', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-17 16:35:48', '1', '69', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('34', '扎西卓玛', 'asdqwieoqwe123132', '', '1465945047', '0', '0', '0', '1', '0.00', '103.2.108.18', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS04l9ibYO7ZpcHfLoAoeYRHx4cVHFormuia57ibzxibF7rvClxJljfO8exbklrn7NrxicUDRzeicvSlR8Xl/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXps3EtUKEmPiiaDAeSzeEfXpqOFnLBAYNuzhm-uxOXSOLz79yccz8N0zTRMnw8F1nWbGvN9UerkHlpIozO-rBtQXKhud3Jf6j2LXSKi1yrbkCLMgvjcQSkqjXkcArYjjfCXlZ8KBiMOMdTzIjFxhEoBozuEj*Y3y68NHtK5y5Tz2F-U79JFU6EnpJY5ZEgZbNyaZ44L6WYXQeFoGySPkBRxbCx7uMQEronr9GsOrz75WFXrxbTzdbHa8aCq1GlhrU6DbqgzLU9Ska6U10PTf37LiaUEMJ*diPjy-gGTFRbjw__', '1466031451', '0', '', '', '1', '2016-06-24 23:39:25', '2016-06-25 00:24:41', '0', '1205', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('35', '我是十七', 'asdqwieoqwe123132', '', '1465946092', '0', '0', '0', '1', '0.00', '119.123.166.248', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5ic1MELS6XnANUGAzI4EwKzsiaYyZk5WNQn9SESPOXnicibC6BFmkBmKEDN7mrUFg4dCQxf7zzKKeGzju33YajiazxVwgGMn53MwE0/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDs3EttJ31jaDZRtgMYZvZU8OgYDNtEQrYGf*7ikvE*Pydm3PuuwMAcLdxep3lue6U4cbWwgW3wIXu1S-WtSx4ZrjfFP9QvNWyETwrjWhG9AjzIJxGZCGUkaW8BHzMJtgWJz4WjIbw1ylkyPsTkdWI6-tDuEpCW*-Spl2ce3iMqH6IImQpEQGhqu9e72Q8LB-T2WIZq32wego2sqqSzkNhjDfVYUYFe9YmSYZ8beel8rdpmGH-fDwNVk8qjXwRl0GUsDm*YXSivWhaqdXPuxARhBD73u06H84nhjFcDA__', '1466032496', '0', '', '', '1', '2016-06-16 16:14:41', '2016-06-16 16:18:43', '1', '6610', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('36', '尼古拉斯、三胖', 'asdqwieoqwe123132', '', '1465946904', '0', '0', '0', '1', '0.00', '182.50.120.87', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS03OQvHZ0g4aJ3PvTNia1rcT0ia85k7EMNYWuE5Jl1vPK0zI52sff1JUSUQpySh9PvlYHWKDDvYvYhn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXprUZbugI18QI2NIgjIzBNdtPUtdsaFGrXfcX431VcYhOvn-PmnPfD830fNI-1FV8u*11nmT1pCfwbH0Bw*YdaK8G4ZdiIfyiPWhnJ*MpKM2BAaAChG1FCdlat1DmAiYtb0bKhYDA0*j6FFAXUjaj1gNNsPs6rCcd9LEyShOP0fafEnciaKs1mfX-IuLbdgZbt6ySMXqrnKl8vinpzca8e4qOoUczzYoP303aOZRLxRfk0i7vmeqQLWqbtrVNp1Zs8DwoJjUgEQ0f30mxV3-2*CxFBCNGf3cD79L4AJANa5Q__', '1466033306', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-16 14:59:11', '1', '210', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('37', '小爷们', 'asdqwieoqwe123132', '', '1465955252', '0', '0', '0', '1', '0.00', '182.50.120.87', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRznRh97Fq72j6NX0FbTuTIUkGIffKx03HpoDUvjg9yiaNA654ljEpibXQXibTf943SQOsaGBvpY8LLgrKSdkCK4rlst/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0tPg0AUBeA9v4KwrTEzMNMw7lp88KipQGkCG0JgkAF5dJjWovG-a7GJk7j*zs0591NRVVXbbcLbLM-7YydSMQ1UU*9UDWg3fzgMrEgzkRq8*If0PDBO06wUlM*oY6IDIEdYQTvBSnYNGBhKOBZNOhfMBtHPKSBQJ3KEvc74-BBbjn*PYLZY0W3lnrd1FLQvPA6c3K3j6qPfWPXRNg*47PdJQwzfqVbu2zJI9DicFl7YPLURTKIOPLbvXmmv0aE4jaZn7X17StalVClYS6*DlpiYCGAk6YnykfXd77sAYgghuezWlC-lG11ZW6Q_', '1466041654', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-16 11:17:34', '1', '444', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('38', '龙*5', 'asdqwieoqwe123132', '', '1465955258', '0', '0', '0', '1', '0.00', '222.79.161.8', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIf7SFtdRZmfdhGbPWxqBibaGujXibZwT0Pu72uKG27MbtMWxFwOgkc2zAJNehz5kuLq8NIE45iakrXu/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXhVWN6gaL4pm5ZCIWtGwo8NYR2o3MClrI-MX73TVwiic*-c3PO-TJM07QSsroryrLpa830qRWW*WhayLr9w7aVnBWaOYr-Q3FspRKsWGuhBrSxbyM0jkguai3X8hpwsD3Cjr*zoWAwcC*nyAfbH0fkZsBomr8EdOI9r7jt9dVc74Pt7HM326hs6kU3xNUQJ-e7JXAS4hOJSxpUT2H15pBMU0nnTpPGGT3IRXfo09f8GNGc4iWkiXJhsQ0no0otP8R1kIf9Bxd5MNK9UJ1s6t93EWAA8H92W8a3cQYhzVsl', '1466041661', '0', '', '', '1', '2016-06-18 17:50:44', '2016-06-18 19:13:17', '1', '114', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('39', '沈府二少', 'asdqwieoqwe123132', '', '1466000857', '0', '0', '0', '1', '0.00', '117.136.79.128', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS035Xoq9n1ic7vD8ibkZlvaL6WRViaQ9m8vUMicVylSDzvH7N1krjAzm1OkDfJItRk0xibkCyK6ItpUxV3/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0tPg0AUBeA9v4KwNnZmKCO4awpVHtI0rc-NhDJDuRKmZJhiS*N-V7GJJK6-c3POPRumaVqbZH2d5fn*IDXTp0ZY5q1pIevqD5sGOMs0sxX-h*LYgBIsK7RQAxLHIwiNI8CF1FDAJWA79ghbXrGhYDA8-T5FHibeOAK7AR*C13m48m-u5CzqHH-bQlkH7TKvtyqJg*rUx5GMDo*L5-fFhONVG*zCcpbwlKJ03lchxJvph1sWLxN8XPZ1pwiBgK7fXP*ppjKV96NKDbW4DKKUYAdhd6SdUC3s5e*7CDsYY*9nt2V8Gl*KR1vQ', '1466272618', '0', '', '', '1', '2016-06-18 09:56:59', '2016-06-18 10:02:02', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('40', '小四班', 'asdqwieoqwe123132', '', '1466029889', '0', '0', '0', '1', '0.00', '117.136.75.182', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeegUG7Q6UiceedXc0d7R8rdC7b35g0L43LuiaZfwyEW3Clty5J81zAU9GTZ9Wic2dibWiaL6KCpibkG5GiaVA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FvgjAUBeB3fgXhlWVrKxVZ4sNgiExNSjAz46UhtmjZhKYUnVn233XMZE32-J2bc*6XZdu2s17m9*V22-aNpvosuWM-2g5w7v5QSsFoqelIsX-IP6VQnJaV5mpAhAMEgBkRjDdaVOIWGGHPwI6906FgMOhdT0EAUWBGxG7AVZxFaVK6cBZDV64fwlOfrfYqP7UAe8vnZDObFxP4smCbOkd1kz2JMFRvBUFJRaqMkBBHrz2JG5ftu0OUpl2N0aKY*0IfP-LddGpUanHgt0FjP-D88QQbeuSqE23z*y6AGEIY-Ox2rG-rAqXGWlI_', '1468005285', '0', '', '', '1', '2016-07-08 11:15:40', '2016-07-08 11:20:11', '0', '2032', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('41', 'A person journey', 'asdqwieoqwe123132', '', '1466031266', '0', '0', '0', '1', '0.00', '111.3.145.130', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIdJrO0Iokfc1TtPjJC7jKOfTNkCwnChGFNMJ3ULbJ6oX0seqsIyrd3EQZtANzU2S3Lb9zCflq565/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz09PgzAYBvA7n4L0ipGWCgNvCxuITuYCB*VCsH-WagZN6cBl8buruEQSz7-nzfO8Z8u2bVBuiuuGkO7YmtqcFAP2rQ0guPpDpSStG1NjTf8h*1BSs7rhhukJPT-yIJxHJGWtkVxeAtj3Z9jT93oqmAzdfJ-CCHnRPCL3Ez6uX*JsF5uKr8nbgqsVlk8su0t3TlKdeEHSfLtfhUMlh5Hh0nPNmIllHi*fs1C4uUiPW698GLtN95qIMCbaof3QJQdHuPeFw-FiVmnkgV0GBQEMIAzCmQ5M97Jrf9*FyEcIRT*7gfVpfQFFKVs3', '1466117668', '0', '', '', '1', '2016-06-16 14:56:21', '2016-06-16 14:57:08', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('42', '宋征', 'asdqwieoqwe123132', '', '1466034667', '0', '0', '0', '1', '0.00', '117.136.38.195', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIWHrowJeaKCSg4hvS5aFGNLfPQN2fn7wAr1RXJeiaHvO6E8dkftZmIAELMKjJvMrSDHXDl1DmAeXX/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz81OhDAUBeA9T0FYG9MC7VgTFzhhdBCYqDhh1xBo4Q4OFCj4F99dxUkkcf2dm3Puh2GappWEj*dZnrdjo7l*U8IyL00LWWd-qBQUPNPc6Yt-KF4V9IJnUot*RpswG6FlBArRaJBwCjiELnAoaj4XzIbd71PEsM2WEShnjPyn9fY6CPd1Ut7Xw910ETP63lY7HdhBlK5eds*tF6uBjXHXTeTBA9*j*6gqfUjHeDMcZHdI3Zv1mEmQubSJ2yrq3FbHMMmTzfZqUanhKE6DKEXUcVd4oZPoB2ib33cRJhhj9rPbMj6NL7XcXD8_', '1466121071', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-16 17:28:26', '1', '2094', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('43', '张文斌', 'asdqwieoqwe123132', '', '1466037631', '0', '0', '0', '1', '0.00', '113.200.85.123', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxzk4nErTA2xZn7Jp0x0FyvmicWhUrtnEW3ZHRmPNGeruursMUFqoCYc4pJNlHQCxuhfvCzPKu0XwrgHgD9oqSZey/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDs9EWShETHxQ3wsS4xW3Op4aVwspCi6VjG4v-fYpLJPH5Ozfn3JMFALDnydt1ypjaSUPNseY2uAM2tK-*sK5FRlNDXZ39Q36oheY0zQ3XPTpe4EA4jIiMSyNycQm4nj-AJtvSvqA3hL9PYYCcYBgRRY8vo0UYz0K1ktxPxH4b1Q-TNfuALh7L5aZUa2fTtVXx5ON3eMPYa7SPi3A6ueWHmRyVx0lJeJSPk7DoHL*RHVo9f1byUcdo0e7wXN0PKo2o*GUQIZAQ7OKBtlw3QsnfdyHyEELBz27b*rLOsQZcUA__', '1466124034', '0', '', '', '1', '2016-07-05 06:59:46', '2016-07-05 07:00:28', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('44', '亦然', 'asdqwieoqwe123132', '', '1466040882', '0', '0', '0', '1', '0.00', '59.41.93.154', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEIUEVUwsDUjkBlguk1iblkOTbicJg7xGjYVic99kZz6PWiayyicIJmBicCSlSS7B7wib1nWJ3TwsTib8jkXicw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXh2WhLbV1NfNhgKsvUiO2S8dJUKKxToOvqNl3231VcIsbn79ycc-ee7-sBmz6dyjxv3xon3LtRgX-pByA4*UVjdCGkE8gW-1DtjLZKyNIp22GIaQhAP6IL1Thd6mMAYdrDdfEiuoLO4PnXKaAw-BPRVYd3Yx4lj-FiubwlY1lvEStGeSnTOFMfzeQ*4q0ZZlP2zEYWZWm6abdJFclZybEdzFf19dmAMZ1eZNFDovQrrvJ4uACr5GbG64nh86tepdO1Og4iBBBKKOrpRtm1bpufdwHEEEL6vTvwDt4np6dcGA__', '1466127293', '0', '', '', '1', '2016-06-16 17:34:55', '2016-06-16 17:36:09', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('45', '站起来(>_<)', 'asdqwieoqwe123132', '', '1466096400', '0', '0', '0', '1', '0.00', '117.25.59.131', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA7364yibcLSkhQsVL7kGeG9Lk8tP8IRWNYwn9rksrNoX0lFVmge1Y2fiaRpiaYZLZ0Fibl5TA1TsveJs/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTa*Pa0uLYHR8ax6q4zYzNmwZHgUpkhFUEjf9dh0ts4vVz3pzzfloAAPjI15fpfn94q7XQQyMhmAGI4MUfNo3KRKqF3Wb-UPaNaqVIcy3bEQlzCUJmRGWy1ipX54DtmHjMKjEWjIbpzylyMXHNiCpGvLveBfNl0CT9im4fJjjX6832nUmvYzdXZcSHoYwXXD9Niwn9GPgmLOalF3m168c8Dvqk8p-vuyis5OLWltELXe0SVk7DqlxmqR8FnVGp1as8D3IcTBhB1NBOtkd1qH-fRZhhjN3Tbmh9Wd85SVs7', '1466182804', '0', '', '', '1', '2016-06-17 09:00:05', '2016-06-17 09:16:45', '1', '18', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('46', '孙迎军', 'asdqwieoqwe123132', '', '1466096641', '0', '0', '0', '1', '0.00', '61.155.234.34', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLAW7lCp1p62Vj82LlTlWKwWiag0ZtwU56aSxCdeOkx4zYIickJojeictWqxa2IAFyiclE52ZibfSq69DlQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTa6MttDXdXRGUTl2yyNR5Q5CWrWF8rKts0-jfVVwiidfPeXPO**EBAGB693CeF0X71rjMHTsNwQRABM-*sOuMynKXBVb9Q33ojNVZXjptB-Qp9xEaR4zSjTOlOQUChke4U1U2FAyGyfcp4tjn44hZDXgfL6-kPBKFWFdyq4TQNr7kvXvu4ps*tDSYuhcVzohKQxa9*7jey7WY1gtGaSuv53u5CZ5WG4WqpYy25Dauy8coaZE4JBc0SWevo0pnan0axBj2KSFkpL22O9M2v*8iTDHG-Gc39D69LwFpWqI_', '1466183044', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-17 13:37:52', '0', '115', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('47', '断肠酒', 'asdqwieoqwe123132', '', '1466100363', '0', '0', '0', '1', '0.00', '183.69.209.141', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeejbkr87q77vbsEIuZzb9TKydABVY9QpUzZEdGUN4cdHyv8pib8UztosOIDuFVkUC05Bo8xAxafwicOf2UYHK8c1oc/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXptZG2fCxd4oUycCRDRdzmuGmwLViHgKVsM4v-3YlLJPH6OW-OeY*GaZrgaZFe5ow1fa2p-mwFMKcmgODiD9tWcppraiv*D8WhlUrQvNBCDYhdgiEcRyQXtZaFPAdszx5hx7d0KBgMOadTSBAm44gsB4yDpR8lfhJ2*GO1mdW7yUEhJuLr9G7dw9u1jq0*DGYvi8DJnbnDqiQqH5rJyqr2skT8Nasy8lY*2hkr5PN95Po3XepY81AJvNTbzdWoUst3cR7keac1yPNGuhOqk039*y5ELkKI-OwGxpfxDSztWyM_', '1466186766', '0', '', '', '1', '2016-06-17 10:06:07', '2016-06-17 10:06:55', '1', '15', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('48', '猫哥', 'asdqwieoqwe123132', '', '1466101142', '0', '0', '0', '1', '0.00', '122.79.104.121', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJw4OpJ16DoZ0cwCOVc265mfKDAtcqXXyVX1loxYyL1ibDnF2XC0drnLRmCBHX7fVib2zfdykeic35Mg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj81ugkAURvc8xYStTTsDSKSJC39qStpGBCTihiBzsbdanIxDi5i*ey016SRdn3PznXs2CCFm-Bzd5kVxqCuVqZMAk9wTk5o3f1AI5FmuMlvyfxAagRKyvFQgO2j1PYtSXUEOlcISr4LtOho88l3WDXSMOZdT6jHL0xXcdvDlYTHxR1jOxOdry8NgsOGstccRryf0rkiDCOTaXzltLx1Y82Q2H*E4bFZF0iR7sVCPaSxrOFlPsAv30bTBaBq8uWKz9pfLNO5th0NtUuE7XINc91LjOXrzB8gjHqrfdynrM8a8n27T*DK*AaqYXD0_', '1466187544', '0', '', '', '1', '2016-07-09 16:31:21', '2016-07-09 16:47:33', '1', '248', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('49', '李小成', 'asdqwieoqwe123132', '', '1466114570', '0', '0', '0', '1', '0.00', '223.73.6.205', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzlb1CfbDn303moQVPeF3NWNqsdziafaiclm8ML9nXIDuvf0Q2sfesFhgeVgPW6PP9Sm1Pic0kdTibJMg07SKqenNnSg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXptdG2QKVLvECC*IESZbLJDWFQTOfoWNuxLcb-rsMlNvH6OW-OeT8t27bBNMnOq7peb4Uu9aFnwJ7YAIKzP*x73pSVLh3Z-EO277lkZdVqJkfEHsUQmhHeMKF5y08BhxADVfNRjgWjIffnFFKEqRnh7yM*Rq-h3a2O-I28d4m*DATJL4qH*XzaLtUTVnTRyFUapkrMfJINXcCjwKtWYbrHsX7Oin5R5ORlc5jFuyQX128d1CK62cbtQJNlXF8ZlZp37DSIEJe4voMNHZhUfC1*34XIQwjR425gfVnffiFcCA__', '1466522432', '0', '', '', '1', '2016-07-12 13:23:16', '2016-07-12 13:24:19', '1', '4728', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('50', '就这样吧', 'asdqwieoqwe123132', '', '1466117195', '0', '0', '0', '1', '0.00', '120.192.185.17', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDt6iab7pebB7cHjXBVyAT4ngY4SJ0SCX2icvJ6scu5qDOgGXpcZjMRdx64USH5BIUkN67CPjsuQh9Q/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTV41pgRUw8YEwosxt2Rwm*tQwWkYzVmpXNmDxv6u4RBKfv3Nzzr1YAACYzjd3WZ7XjTTUdIpDcA8ggrd-qJRgNDPU0ewf8lYJzWlWGK4HtCeBjdA4IhiXRhTiGnCIN8Ij29OhYDDsfp*iANvBOCJ2Ay7i1yhZR1vnMNu06ctqH5-DhT9HRa8*bPREevk8LR997zSrZZXpVbNOdkl104RTu*vjZZgz7ab52cfpsisr2W7fI1KQri29-i1S7sOo0ogDvw4ixPMJ8slIT1wfRS1-30V4gjEOfnZD69P6ApswXEQ_', '1466843686', '0', '', '', '1', '2016-06-25 15:11:52', '2016-06-25 15:15:32', '1', '81', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('51', '王超同学', 'asdqwieoqwe123132', '', '1466121935', '0', '0', '0', '1', '0.00', '221.200.114.110', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDAOWE3picIXiaGjNlWhTwqKfic9ppoKxoHQ7RADHbCBKunLFzo7nyTLqW5nDBrh3ib3hxpY1j5XjtTBw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDrca1hYLsbhIiOJzOuWXupuGjzE4LXVcWhvG-q7jEJl4-580574cFALCf08VVVhRNW2uqT5LZYAxsaF-*oZS8pJmmjir-IeskV4xmlWZqQEwCDKEZ4SWrNa-4OeB41wYeyjc6FAyG3O9TGCAcmBG*HfA*egmTeTjV7e2jv1yzDi-6i*3pKRK7VPZitCkgaWf*aIonwZ6pfTpPXicJ1Ku4y*O8P9418QNJ4yLaJCLcYelWeTpDoq1XcE3c9xujUnPBzoM8DxHoO76hR6YOvKl-34WIIISCn9229Wl9AWHeW3g_', '1466208337', '0', '', '', '1', '2016-06-17 16:28:28', '2016-06-17 16:29:46', '0', '54', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('52', 'hey', 'asdqwieoqwe123132', '', '1466122416', '0', '0', '0', '1', '0.00', '218.85.135.104', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0x24xZplRSqnNLm5SyX0uuUCibWqBiaSjF2YLSEsd5BdtGSnnfGv5UarYFNQicTqIm5WQ6yMqeFdJicS/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXT2xlty4dg4sWEmuFWog7FcNM0bcFmG2OsU6fxvztxiRivn-PmnPfDAQDAfDY-FVKud43ldt9qCC4ARPDkF9vWKC4sdzv1D-VbazrNRWV11yPxI4LQMGKUbqypzDHgBtEAt2rB*4LesHc4RREmfyKm7pHRhzhN2HM*3Z*XqaS31JtNAxJfJ53KcxaO2GOto3lG7**KZbLDY0PH7WYlztxR8V7V6mrTSO9Gpo2osvhVLcN0Uj4lfrEI80lWsstBpTUrfRwUBNjHBA8Hvehua9bNz7vowBhH37uh8*l8AcixWms_', '1466208819', '0', '', '', '1', '2016-06-20 08:52:54', '2016-06-20 18:07:27', '1', '29', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('53', '成为一名伟大的吃货', 'asdqwieoqwe123132', '', '1466124763', '0', '0', '0', '1', '0.00', '183.252.16.23', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBBJURW1nQV8TWD9bCE3BlnFwYrxwu8vLoHeiaCVsvz5RiaTCQAZj0zpMeST8vrEZtkOQkO03iaoH8mv/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTV4xpgbLVNyfoCJtk6iB9aiotrJoB6aobW-zvTlwiic-fuTnnnhwAAHxZPF*Lsmw-Gstt3ykIbgBE8OoPu05LLiz3jfyH6tBpo7iorDIDeoR6CI0jWqrG6kpfAv5kjDv5zoeCwXBwPkUUe3Qc0fWAy5jdJauoISEm6bGIsdyrmhGTBEUfiAIz5WbztcJPLHfLDcvrfbK5XS7y9KCPxb2s1tFUJP0seHzN2DbO3h6M26azVSzofJKSaTSqtHqrLoPCc5tPQjLST2V2um1*30WYYIzpz27ofDnf2axaYA__', '1466211165', '0', '', '', '1', '2016-06-17 16:52:45', '2016-06-17 16:54:07', '1', '14', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('54', '好孩子', 'asdqwieoqwe123132', '', '1466188759', '0', '0', '0', '1', '0.00', '121.204.96.238', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5I4wJAHaQdtN8S0cIj45ichb1yrg5CX3QDBKxh8yA6eOoQcjpPKA6aDqA7ZrRC3Byzatd9xOa6sUQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FQ2vGNcWgWDiw0RwIJuIGyF7adjoSqMw0pUNZ-zvm2yJTXw*5*Y791sDAOjz*P22WK*3XSOJ-GqpDu6BDvWbP9i2vCSFJKYo-0Hat1xQUmwkFQPEloshVBVe0kbyDb8KyHSwQnflBxkWLvDufAtdhF1V4WyAU3-hhW-eTB4ceQyZlx2fWD37jJdsMUdGOorMNK5E1uQo2rd9-roah6x86cZL08rxY59EK2NyGBkOzKssSHg68ZOuC2r-OXBkNfUflEnJa3oNsm2MHMtWm-dU7Pi2ufwLkYUQcn*7de1HOwF941sm', '1466275162', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 11:56:09', '1', '86', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('55', '千秀', 'asdqwieoqwe123132', '', '1466189373', '0', '0', '0', '1', '0.00', '120.32.126.21', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6qdEiaVHzWPYJbGLicyYHTALVnWCRZo8nRaYsYfFgCiazlOneXBHN7yINb3t2LxFdAzbz2uibQ3AFoAvK8vaT2XpfUn9zkpx4c8o0/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXT1xnTFmHr3tiGSuYfdCiRF4Klm9cOqKVOFuN3V9kSSXz*nZtz7qeDEMLJ1eq0EKJ5r21u91piNEWY4JM-1BrKvLC5a8p-KDsNRubF2krTI-M4I2QYgVLWFtZwDFB37A60LVXeNxzw7OeWcMr4MAKbHq-Dp3l0t9iZ*7lKJGNmdCFo*hguzsVtqtsQRs9BfLn3xJvadK8hT4PoJYi8TqhuFhOVcFVNeEa36fJhW1ZZES2rVIQqvgni7KOZwaDSQiWPg3yf0Qkd*wPdSdNCUx-*JdSjlPLf3dj5cr4Brbtb-g__', '1466275776', '0', '', '', '1', '2016-06-19 00:48:03', '2016-06-19 22:51:09', '1', '2070', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('56', '晓志', 'asdqwieoqwe123132', '', '1466190057', '0', '0', '0', '1', '0.00', '192.173.153.26', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehRFiceaSnBibiaibfia2RdYWrWzN2KseewAvkGnFa7ZzC9SyGQicicohlpyzicCmh2SRarQ6wuYibYszXqlHg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11vgjAYRu-5FU2vl6UFEWuyG8s*cDWLG2aZN6SDQuoqkrYWnNl-n6LJSHZ9zpvnvEcPAABT9nbL83y3r21mD42AYAoggjd-sGlkkXGbBbr4B0XXSC0yXlqhe*iHxEdoqMhC1FaW8irgIBoNqCm*sn7hAkenW0SwT4aKrHq4uF-RZBkv*EZFn3NF3St-L*lYM2omBLeH51mXu9Q8vqB9SrZRta6SSqTtB*NP36TxO7Ze6kjE84Ipa1YulrNQ1UlQqs2Do6q9G0xauRXXoPHkVBMGw2YntJG7*vIvwiHGmJy7offj-QIlj10A', '1468187134', '0', '', '', '1', '2016-07-12 14:03:44', '2016-07-12 14:04:34', '0', '299', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('57', '中和庸', 'asdqwieoqwe123132', '', '1466208089', '0', '0', '0', '1', '0.00', '203.81.27.32', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIaEJibibnRIhEhbMOpGOGYeToHegwPrkA2fbVd5ibCN8QbVJjQVA0QUOC8UiaJ7fUJlTyoFq4GLibbrZB/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq0ZaWNH6RpAtneg2NxN9auraSXVAbYuDGf*7ji2RxOfv3JxzvzwAgL-Klxd8va6byjHXaemDa*BD--wPtVaCccciI-6hbLUykvGNk6bHEJMQwmFECVk5tVGnAIou8UCteGd9wxFHv7eQoJAMI*q1x7vsOaWL1EZpntnpbFGUwUtZzGCnR9t7ud89xOnEasHj4GP8NsbBPqFFcusaQ2vzSJPgxp0FHcf1MqfTbAeJINuJaNqqxfMwap*Gm50q5WlQHIdRfEXQQD*lsaqujv9ChBFC5LDb9769H6YBW60_', '1466294491', '0', '', '', '1', '2016-06-18 16:01:31', '2016-06-18 16:02:51', '0', '198', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('58', '李兴彥', 'asdqwieoqwe123132', '', '1466213864', '0', '0', '0', '1', '0.00', '120.192.101.58', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRznlCDCe7MArBy9icef8no7Csec6Aicib7ia8j5C2S8vZ2GL9Bg310j2tbQrVR6CztJp8s0icDhnCiazg4QolQQRx6HC3Y/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('59', 'Jacklin', 'asdqwieoqwe123132', '', '1466216165', '0', '0', '0', '1', '0.00', '117.136.75.228', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxyqO22djvU5XNhkWebhZ0iaS8kbZdL6StVZibl2U8PJeiaXWjGIuV3l8SMVZ2yO3zO6Y0bHlNAV94lPmDsaDrcvjsK/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PwjAYRu-3K5beYrQd26AmXmAF3MaY7kPUm6Wwgp1h1K5jA*N-VweJS7w*581z3k9N13UQz6JLulrtqkKl6iAY0K91AMHFHxSCZylVaV9m-yBrBJcspWvFZAsNCxsQdhWesULxNT8LqD8YdGiZvaftwgmaP7cQIwN3Fb5poT9OiPNItr0lCmt030yPY-vu9kPIPOBViYbzo9fzk1dPhc8sah6St9rZ0KCCcW7tl1NskgmZvFh0dBV6o8Rd1Ac-n5Vu7A4D8hQtnJvOpOJbdg6ybcM0sd1t3jNZ8l1x*hciCyGEf7uB9qV9A4TbW04_', '1466302567', '0', '', '', '1', '2016-06-19 22:21:35', '2016-06-19 22:23:01', '0', '85', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('60', 'Terry', 'asdqwieoqwe123132', '', '1466315257', '0', '0', '0', '1', '0.00', '24.114.75.58', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeegCNbevvzAN8WnGXYvhfg9ebUHweLLkYzGcfib1niaHuicthxH6wpbmiaOLeUkSzfDZTyic8gCOwEgXoqUBg0N5STVjb/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz8Fqg0AUBdC9XzG4Ls2MGo2FLIzV2tKkNDaGuBFxRnmRqOhYx4T*e1sT6EDX5z7ufRcFIaR*vIb3aZbVfcUTPjZMRQ9IxerdHzYN0CTlid7Sf8hEAy1L0pyzdkJtbmsYyxGgrOKQwy1AdGshaUfLZGq4ovFzi22i2XIEignX3rv77Lmhm4El2qgsnJfgaBz27lYEAmbWdsF9Vjz5URSfNfLWO7AaSuh2M96vzMcxGE4WOzuj0OL1cfA3Ye1tDnZHd3ka76NiuZQqOZzYbZBp6oaBTUvST9Z2UFfXfzGZE0Ls392q8qV8A-J5XIg_', '1466401667', '0', '', '', '1', '2016-06-19 21:49:13', '2016-06-19 21:49:18', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('61', 'Queen.', 'asdqwieoqwe123132', '', '1466360520', '0', '0', '0', '1', '0.00', '123.117.168.161', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/NdiccZl8Jk96INUdefHdoKx5BR15PejZ0WGGeDJ4iabD35NdOR5atBCVS79cCrVCe3PjOiabzDlfOQofDG0sibDSmf5lo2TvKRGQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXDs9GWPwv1DYQ55hzOTY2*EEaLu2GjWAqsMX53lS2RxOffuTnnfhoIIXOzWF9meS7aSqVK19xE18jE5sUf1jWwNFOpLdk-5McaJE*zQnE5oOVSC*NxBBivFBRwDhDbG2vDynRoOKHzc4spseg4Au8D3kevN-Eq1Inugx1h-cJ5i2bJvktkC-OP7VNbBhAJ3u*XMfP6Fyfy450-F*Dn-mp2d-twZJs2yaZZ3D3rcCp0EeCls5ahYI-ltjlcjSoVHPh50GRie9S23JF2XDYgqtO-mLiEEPq72zS*jG9fb11N', '1466446925', '0', '', '', '1', '2016-06-20 10:22:06', '2016-06-20 10:22:51', '0', '20', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('62', 'Gothic', 'asdqwieoqwe123132', '', '1466364873', '1467671497', '0', '0', '1', '0.00', '120.40.6.188', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6v5YAeNvGtfibOYS0zIibGWPBzHTQLSK1IZvichEVytwFlV7HicjST2pVux4jb4ca6H83rTvAxxWwaHw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19PgzAUR9-5FA2vM9rCEDHxQdlqiLDsDxh5IrBeWJnraqkwY-zum2yJJD6fc-M799tACJlxuLrO1*v9p9CZ-pJgontkYvPqD0rJWZbrzFbsH4SD5AqyvNSgemg5noXxUOEMhOYlvwjEviMD2rBt1i*c4fh0iz1ieUOFVz2MpokfLPxlt2qJ-z55lLQ4zGIR0XnhNFDHsgzFs7*VmOqndDp-WXRBFYg0denuI3A6CmkHXdVao9GsqMflTTipl9Fmw17pm0pE8jCY1HwHl6BbF9u2i4dBLaiG78X5X0wcQoj3220aP8YR9mBcbg__', '1467091309', '0', '', '', '1', '2016-07-08 23:48:11', '2016-07-08 23:48:25', '1', '502', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('63', '黑夜', 'asdqwieoqwe123132', '', '1466380898', '0', '0', '0', '1', '0.00', '183.33.187.143', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS036Tiashh98IO5Cxia7LKibudFBoRCqoh6V0sIA071o8Gou6rxPUeoGUyl4CwiaTrj8a1TFc546UbTtT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-Yqmrxptt3VQEx82HMuMLDLowKdmoQWr0M2uKMbw39VBYhOfv3Nzzv3yAABw-jC7qlerZq8tt5*thOAGQAQv-7BtleC15YER-1AeWmUkr9dWmh59Qn2E3IgSUlu1VucADoaho5145X3DCcOfW0SxT92I2vQ4SdkoT5hWF-l4U*wPZU2myfKuiO0Ty-B2lJmwCoVmyWxaFdUAxSqNn7tyh036sUCPL6wIqL*MWxNtbWnf9H2WT5rF9TzA45QIdOtUWrWT50FRFCI6IJGj79J0qtGnfxEmGGP6uxt6R*8bH2lacA__', '1466467356', '0', '', '', '1', '2016-06-20 16:09:38', '2016-06-20 16:11:48', '1', '91', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('64', '王小贱', 'asdqwieoqwe123132', '', '1466381081', '0', '0', '0', '1', '0.00', '49.223.56.190', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIVv9WtcXC9VKGl3stc4fGsQh8qmcfqcwfXo23bYp2dgibANRPULhriaOibu9dTQ2hD06ibCfx9E7utC0/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXhdUZbOrA18UHZjEWWMDfE*UKQtli3AaOdOpb9d5UtsYnP37k55*4t27adeTQ7z4ui3lY607uGO-aV7QDn7A*bRrIs1xlq2T-kX41seZYLzdseXY*4AJgRyXilpZCnAETYM1SxZdY3HHH4cwsIdIkZkWWPk3ES0OmIBY9P0Z0-ofdvs25Bu3kNQkbzcpx0qRgMSHLznsKX5zjcTGmZeCO9UiRdXPDCl6tdjFVwifDn7WtYrjcVfyiEilC8LAW*Niq1XPPTIN8fAoIxMvSDt0rW1fFfAD0IIfnd7VgH6xuEllum', '1466467483', '0', '', '', '1', '2016-06-20 16:04:45', '2016-06-20 16:35:09', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('65', '(心)残缺才完美', 'asdqwieoqwe123132', '', '1466381174', '0', '0', '0', '1', '0.00', '183.63.139.129', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRznykNvut7K3ia4ysIqKhb3MTTLlgWuelM30qBnObHJdBeCAC3oN5XbOoPE131DkeLzvsbd3lO4UqaYrgAYbGXYGd/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXhVmNaOuhq4sVAohMQyVaXeNMQWlzlY1jKHDP*d5UtkcTr57w55-00TNO01tHqKsvzXd9opodWWOa1aQHr8g-bVnKWaYYU-4fi0EolWFZooUa0HWIDMI1ILhotC3kOQDR3J9rxko0NJ5z93AICbTKNyNcR44D6y1vMCyd-QR6dezEGul8*bNZPsX8ISTXUXUKz-A0lKEpSbyGDRXUsK--j8T3oytWmQfdVuKW4vEtDrqKB9vuujrcXznOZ2sHNpFLLWpwHue4MEILxRPdCdXLXnP4F0IEQkt-dlvFlfAObnVvq', '1466467577', '0', '', '', '1', '2016-06-20 16:06:18', '2016-06-20 16:07:08', '0', '14', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('66', '阿牛', 'asdqwieoqwe123132', '', '1466382769', '0', '0', '0', '1', '0.00', '221.15.241.106', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKOXhPv449fKe1TfTdrWbw8HUM3IF4SOwlebzQxBlLeJXbADvjSIj6icIESkwst7teia2uBb7icicnDQA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11rgzAYBeB7f0XweswkrdoMdrPaD5kVimutuwlSo2ZWG5I4Zsf**zZbWGDXz3k55-20AAD2S5Tc58fjue801YNgNngANrTv-lAIXtBc04ks-iH7EFwympeayRGxSzCEZoQXrNO85LcAmhBTVdHQseGK059bSBAmZoRXI24Wu3m4DZbIdy5oVwdPiape*7dMOPvYCTMZ96dIzZqLx0Sw9AMZVmGV6XaTt*6zivEJpzIaDms9lMlapbOMqO2hTvfxajWvo0XzaFRq3rLbIM*bIuT7rqHvTCp*7q7-QuQihMjvbtv6sr4BwMRcHA__', '1466469375', '0', '', '', '1', '2016-06-20 16:36:16', '2016-06-20 16:39:03', '1', '316', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('67', '余振国，众悦影视', 'asdqwieoqwe123132', '', '1466555859', '0', '0', '0', '1', '0.00', '112.12.48.143', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0ic0quESEI86tAiaB7LASWEcwB760mc0DalneXRr6lt2OFrpEaqTlibV9AcyJFcNtrUfDImkRmYILiac/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXhVqO0hbGa7GIfzGLQlcBmvCKwdqRxfJXCnGb-fYokknj9nDfnvF*arutG5Id3yX5ftoWK1bnihv6gG6Zx*4dVJVicqBhJ9g-5RyUkj5OD4rJHaGNomuOIYLxQ4iCGALBsNNKGvcd9w4DftyYGEI8jIuvx2Q2W3oJuHEYIfKRJjY73S7-spnW2Ss9BnsGTSL12E7U*p1v6NBdz4q3zsF50NnJe8t1nsyJrl6hdnUbu6*nteDOhtJRbREIczGajSiVyPgyaTAFEluWMtOOyEWXx*68JbAAA-tltaBftCoZBW20_', '1468181047', '1', '', '', '1', '2016-07-12 21:14:28', '2016-07-12 21:08:35', '1', '8180', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('68', '梦飞', 'asdqwieoqwe123132', '', '1466561082', '0', '0', '0', '1', '0.00', '27.154.155.150', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLD4XcpeWe85iccamiaicibX3WNaVo3n4icfH6N7IKnsnQlRcJBQITcI5ICTjtFDusy19RGpZK3Q9Z8qyvg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FU1fNdICZdRkDwSJzriRBjPnU0PWsnUoVOjqNuN-dzISSXw*5*Z898sBAMDnp-ymWK*bfW24OWoJwS2ACF7-Qa2V4IXhfiv*QXnQqpW8KI1se*gR6iE0VpSQtVGlGgQckGhEO1HxvjDA8y2i2KNjRW16OE9ZMksqNLenTOzZSh8znObVw*LVLsrMjdHE9RlZpmz3csCbuotVnNIkD3aRexU*NtXbBxU**Swr4p3yeGvY-fZuZYydnXuWTaejpFHvchgUhpMARXg8yMq2U019*RdhgjGmv7uh8*38AD8FWsk_', '1466798419', '1', '', '', '1', '2016-07-12 21:12:22', '2016-07-12 21:04:39', '1', '141', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('69', '天下第一', 'asdqwieoqwe123132', '', '1466581320', '0', '0', '0', '1', '0.00', '106.114.6.196', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEK7CzC1OeAMYcsRx7O0ohym6OJ97K2ZhBeniccic39RDXCcReW9CxTibLZEghk3UibDk2EaJjj50ROt2w/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDrca0ZSXWu22SiFYdzi*uGkaL6yZtYWXgjP9dRRIxXj-nzTnvuwcA8O-Z8iTLc9Nox92blT44Az70j3-RWiV45nhQi38oO6tqybPCybpHTCiGcBxRQmqnCjUE0ITQke7ElvcNA37dQorwn4h66fE6SudxMi9nWTytxGNiL9nDSkzYbRstyOxw9HxDcbqh6Z1tEa4MW7bxerq4YKpjm645aHP6FOhua-erorERTcqwOG*r11iI3FyZNRlVOlXKYVAYhggiHIx0L*udMvrnX4gIQoh*7-a9D*8TJ4RdPA__', '1466667723', '0', '', '', '1', '2016-07-01 09:30:30', '2016-07-01 09:30:35', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('70', '飞羽科技', 'asdqwieoqwe123132', '', '1466623602', '0', '0', '0', '1', '0.00', '223.95.85.242', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA2diaic2BjCvib1kAlcA7CpjhFnqg9Ec6fpylp9iaAdo4f4LHopeA3DOQDyHstZlgexe5RvVaFia4RibSK/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXD64xrgdZh4kOZPLg5NyaL0ZcGaYErGXSlui3G-67ijCQ*f*fmnPvuIITc9Pb*PMvz9rWxwh61ctElcrF79odagxSZFb6R-1AdNBglssIq06NHQw-jYQSkaiwUcAqQgPkD7WQt*oYTft3ikHjhMAJlj4t4M72Z1tU4Ac6a-fb6In6KTMLoODdJpFfBaM8jPEl8vSl3z4RziHl4lGmdLh-iik-ylumOQ7eQy5d1fZjlfF6t78rZ3BajXfF4Nai0sFW-axmjXoDpQN*U6aBtfv7FhBJCwu-drvPhfAJXY1tb', '1466710005', '0', '', '', '1', '2016-07-12 10:16:59', '2016-07-12 10:53:38', '1', '654', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('71', '微商', 'asdqwieoqwe123132', '', '1466733705', '0', '0', '0', '1', '0.00', '60.166.201.6', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS02NLFZic7POb6TCvbgUiabQCCXJqic3RFV47zQp3A9MUiaBemS1snIfpkkVEDuYa3sTCCMnlG9pTyAPV/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXp64y2nYXVt*o2IEIWgkzxhZDRjescq6WYEeN-V3GJTXz*zs0598NxXRc9xNlltdkc*9aUZlASuTcuwujiD5WCuqxMOdX1P5QnBVqW1dZIPSJlnGJsR6CWrYEtnAP*jDJLu3pfjg0jkuvvW8wJ5XYEdiMmi*IuSufhsOdXqr2P*kU7z96IiIaXZwxBkwd*oyZJlSfTW96zUy6iRsQryNe0C3PFAcwBh6tsGXuPZqnWhU4DUviTp-R11wsxsyoNHOR5kOf5HmXYt-Rd6g6O7e*-mDBCCP-ZjZxP5wtn7Fsb', '1466820107', '0', '', '', '1', '2016-06-24 19:42:44', '2016-06-24 22:10:30', '1', '387', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('72', '蓝色妖姬', 'asdqwieoqwe123132', '', '1466752763', '0', '0', '0', '1', '0.00', '27.149.133.75', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIZLCRx3nDoId90elfP2nYWsVW0PdTfSHNKPR9ibr0lSib4lvbHeFl7berOEh74dAjAVL7tPJVXYHXF/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PwjAUBuD7-YpltxjTlmGtCRdjoGwKiHwI3DSDduZg3EdX2Kbxv6uThCZcP*-J*54vy7ZtZ-40u452u-SQaK7rTDr2ne0g5*qMWQaCR5q3lbhAWWWgJI9iLVWDpMMIQmYEhEw0xHAK0FtCDS3EO28aGsTu7y1imDAzAm8NjgZTP*g9L4gfrSqx3i4Paj1CZdma56IYBLM9c2NU5jDcqNeHcbTxwAvT-T3*nHisyn1Sh6xV9vsv3jGoVjRZLtwwq8V22JvI8HHa7RqVGj7kadANJRS3GTH0KFUBafL-L8IdjDH72*1Y39YPy69b4Q__', '1467328992', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-30 15:29:37', '1', '384', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('73', 'Homin', 'asdqwieoqwe123132', '', '1466791171', '0', '0', '0', '1', '0.00', '223.210.178.186', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS07fBSic8ZfKLsuxzusUZBRmG9icTjFia7PEJ8xJ4mlMCibz7bqEdbkNX5clUqIHW2d6QINXwGA7rqdiaM/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq0ZbGIOa7GGgZCRjODYz50uDa4FC6FipyjT*dzdcYhOfv3Nzzv0yAADmer66yXa7-ZtQRB1bZoI7YELz*g-bllOSKWJL*g9Z33LJSJYrJge0HGxBqEc4ZULxnF8Crmfr2tGaDA0DotHpFmJkYT3CiwHjh2UQTdOr46qL0GxWFkH9*bhpKjxfJ1UqXuEWvsjy8FTjJnecyp5yv3QWTZgcojjcBreLZHPf*9L3w1GWeM9pETPqllzEH33ULScTrVLxhl0Gjccewti1NX1nsuN78fsvRA5CCJ93m8a38QOLQluM', '1466877573', '0', '', '', '1', '2016-06-25 16:38:32', '2016-06-25 16:38:34', '0', '117', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('74', '＿奥特更曼❗', 'asdqwieoqwe123132', '', '1466793042', '1470881031', '0', '0', '1', '0.00', '117.25.59.178', '', '', '1', '1', '0', '0', '0', 'sdf', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '直播大神', '11111', 'Female', '', './public/attachment/201606/25/23/576ea268998e0.jpg', './public/attachment/201606/25/23/576ea23edd388.jpg', './public/attachment/201606/25/23/576ea25fd21ae.jpg', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLA9L7NXQ70hHF1ibC2fqNuETGBHtHry909oUuyIRIEwicq6fz3gJmoz5NiahM0iboOWt6951b3lNkyfpQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXD64zrLRboEh8YE4PTwJzOyAtBaLFRgbBuwBb-u4pLbOLzd27OuUcDIWQ*3K7Pszyvd5VK1dBwE82Qic2zP2waWaSZSq22*Ie8b2TL00wo3o5IKCMY6xFZ8EpJIU8Bx7VA023xlo4NI8LF9y1mQJgekeWId1ePfrhatN36nsmn3XROMu57r-tA2H1Oe1pN5jfvXr8i043lLRMQZVh6cSn4IQn4UNbiupsM3XO47DdO3NHEf4kimlgL5wBRHNSXWqWSH-w0yHaBMcd2Nd3zdivr6vdfDBQA2M9u0-g0vgCN*1uC', '1468257368', '0', '', '', '1', '2016-07-12 14:20:43', '2016-07-12 14:22:33', '1', '5817', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('75', '韩伟', 'asdqwieoqwe123132', '', '1466813643', '0', '0', '0', '1', '0.00', '183.39.152.93', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/n7rh6ibhGlba5VxiavCI0jfXDdwaxvEqeiccW0LibzLvaK9Mv8AWBA4CdZkiaz2BSqFuia1Mp6o21etkKwu5KdzvBibFGAoTslibd15s/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FLwzAURt-7K0JeK5K0TdsIPoQRdVRB7OpwLyFrsy3I0izNZJ343zfrwMKez7l8534HAAA4ey5vZV23e*OF762C4A5ABG-*obW6EdKL2DVXUB2sdkrIlVdugBGhEUJjRTfKeL3SFyHL42REu*ZTDAsDxMn5FlEc0bGi1wN84dVk*mDCA9-sSVykUk-64*ZpKddzU08X7Suxs8elSYq89POUL5jmjBS797BKLJcZYv1bdzTM4wwRVYSqC0vWbpFRu9x9VPx*NOn1Vl2C0ixHJKXj5i-lOt2av38RJhhj*tsNg5-gBH66W0E_', '1467863294', '0', '', '', '1', '2016-07-12 20:44:18', '2016-07-12 20:50:37', '1', '5646', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('76', '姚森 ‍♛起源天下', 'asdqwieoqwe123132', '', '1466815290', '0', '0', '0', '1', '0.00', '183.37.239.245', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeeiabVErBLUJMQxa4AHttE8MGamFf4LFGoLfpKxADVibk7AgYibOCkCcN3iaxzicZv3CzKL8eO9INQE3sOw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAUBuB7fgXptdGWFga7m4MQjF-70s0bQtYD6wYdluLYjP9dh0ts4vXznrzv*bRs20bz*9l1tl7vW6lTfawB2UMbYXT1h3UteJrplCr*D6GrhYI0yzWoHh03cDA2I4KD1CIXl8DAp56hDd*lfUOPhP3c4oA4gRkRRY8P0WqcTMLdHFgQYrd026zdFuEhjqZPQj46x7uuo1H8OhN4SVmDi0OyGSWNksttvhhNXrwS04re3MrIe2vjcbWC8tkHPn1Xi-q0yZVRqUUFl0HeAPuMuczQD1CN2MvffzFxCSHBeTeyvqxvvm1cOg__', '1467142054', '0', '', '', '1', '2016-06-29 16:08:55', '2016-06-29 16:08:59', '1', '380', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('77', '叶思敏', 'asdqwieoqwe123132', '', '1466817308', '0', '0', '0', '1', '0.00', '120.39.50.179', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4Vd8SicObpibXLqtJtf7rAYUVOBmHgRp1CIgIWylG4ucN6wlazO3BHGF5GOL5qdqhC7114q6ibM5aDA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11LwzAYhe-7K0qvxSRt2i7CLqZsWFjB0g*3q5Cu7-RlrClZus2J-12tAyteP8-hnPPuuK7rFcv8Vm02um*ttG8deO6d61Hv5hd2HTZSWRmY5h*Ec4cGpNpaMAP0Q*FTOlawgdbiFq9CPAnEiB6anRwaBsj4V5YK5v9R8GWA6Tx7SBZPIaqMZ5dYqJTq2l8fqxIW8WNZnFSR5Lnmq74M9mmVzHCGXV0Bkh2PiSCrgAfYhuua379qsjxlPSPnC8BzauZ5Np2OKi3u4TooiiY8Ymz86AjmgLr9*UtZyBgT37s958P5BC9uWrA_', '1466903710', '0', '', '', '1', '2016-06-28 08:57:15', '2016-06-28 09:03:40', '0', '106', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('78', 'Ali上帝的狮子', 'asdqwieoqwe123132', '', '1466884050', '0', '0', '0', '1', '0.00', '203.81.27.32', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIcWRIfCofVjUrmvoibp38VdZJPE3fxZG6AbibZOibbADCWgxJiaNYXiaNIuY7VG2Pp4wOXkFpjqXCM0BP/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FYRXjWkp4Lo3IAsQYQNnlulL07RlNihUVgjM*N9FXCKJz*fcfOd*GqZpWk-p-o4y1nS1JnpUwjLXpgWs2z*olOSEaoJa-g*KQclWEFpq0c7QdrENwFKRXNRalvIq3K8cuKBnXpF5YYbQmW4BhjZeKvI0w2zzHCZF*FGwLu5qRP2x8o7JNrjY3n542D5mRZajmu3SQ3VzKce34ZS8*rlkeRPQPkU8OmCtAHaS2H8JmROxyCsDvEFq10fxJC0mtXwX1yDPm3JWrr2gvWjPsql--wXQhRDin27L*DK*AaYXW3Y_', '1466970452', '0', '', '', '1', '2016-06-26 21:25:14', '2016-06-26 21:27:15', '1', '469', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('79', '病猫', 'asdqwieoqwe123132', '', '1466884155', '0', '0', '0', '1', '0.00', '203.81.27.32', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKSHkstTxbR34ibyqnVSmbulP6QxUfVdaUG7upw3uZC4FicWFZ2HsRZNayia0IyfZwswnchnGzEpIalw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXps9G2-NGa7AGnM7rNsAnZxkvDoODNNmho2SDG767DJTbx*Xduzrmflm3bKJq9X6dZVreV5rqXAtn3NsLo6g*lhJynmjtN-g9FJ6ERPC20aAakHqMYmxHIRaWhgEvg9s6lhqp8x4eGAYn7c4sZocyMQDng-GkxfhlPlodT7LsEHtYhUf0GVqWcxH23VzRh7dapPorysV3tFmkAQZi07rRb98dSvSWsYyzI25vpNpuHcVZv9rMoeoViqZ3n-jQaGZUaDuIyyPfPczzf0KNoFNTV77*YeIQQdt6NrC-rG0ZhXS0_', '1466970556', '0', '', '', '1', '2016-06-26 20:45:04', '2016-06-26 20:46:49', '1', '310', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('80', '馒头小强PM', 'asdqwieoqwe123132', '', '1466886930', '0', '0', '0', '1', '0.00', '203.81.27.32', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA3OTSblZtTsvGaQXy5eMFtQGFiaQuvuk5moNGWMPFFhnKtybA5ssKdMavSiaFgczcqaJKzibPhibV4iaS/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXp64y20I7VxAcGaJZoMhxY99QgdKRhhQJ12WL231W2xCY*f*fmnPvluK4LsufNbVGW3WdruDlpAdx7F0Bw84day4oXhvtD9Q-FUctB8GJnxDChR6gHoR2RlWiN3MlrIFhg39KxavjUMCHCP7eQIo-aEVlP*JLk0SpapSZoinj-lmeqX7eYiPjjsG4Qi7okylTdoPSd9XoGYSiTcCT4dTk7bfK4No9*2Rsc*uxOwSRO**0TU*W*TrdsWR2D8cGqNFKJ66D5nCIS*J6lBzGMsmsv-0JEEEL0dzdwzs43c-JbRg__', '1466973332', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-26 20:51:19', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('81', '微笑向暖', 'asdqwieoqwe123132', '', '1466910472', '0', '0', '0', '1', '0.00', '36.62.192.25', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEIKwzDWveWRRGWQJdBqrmdReveL7vnc9cFrvyLDeKA1Dqtm2gxeXVxp4MAWCg9gVdlqWQ2asic1tow/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0FPg0AQRu-8ig1Xje5SYF0TD1VqBFttiwY8bQgMuG2ABUZBjf9dxSaSeH5v8r35MAgh5sMyPEnStH6pUOKbBpOcE5Oax39Qa5XJBOWszf5BGLRqQSY5QjtCyxEWpVNFZVChytVB4Ge2PaFdtpfjwgiZ-X1LBbPEVFHFCFeLxyt-4*l4hZ1-79WYlkNQLiH2n6vEvd1HjVdCeYnh7nRoQhE7G79Y6KdmzgvYbQN*k2s8ivh1BHwdBO22h7733tFZ34X23KMXk0lUJRyCXFfMhMWnza-Qdqqufv*lzGGMiZ9u0-g0vgBCb1y*', '1466996874', '0', '', '', '1', '2016-06-26 19:09:26', '2016-06-26 19:09:33', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('82', '侯玮江', 'asdqwieoqwe123132', '', '1466917505', '0', '0', '0', '1', '0.00', '203.81.27.32', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/n7rh6ibhGlba5VxiavCI0jfTPkFIVxFZBajrHIHbO9LicQJmx7B2tyqFd2bHJmQQUicW0f8nu4HqT18HTvYmmkwVkhClrdxiaQs2X/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDtZGWUT5MdgFkcdONbEOyrTcESyFl2tVS9qHxv6u4RBKvn-PmnPfDAACYT-P0tqD00Amd64tkJrgDJjRv-lBKXuaFzkeq-IfsLLlieVFppnq0cWBDOIzwkgnNK34NeL6DB9qW*7xv6BE537cwQHYwjPC6x8VkFc*iJU6iEMcPhNBi1qx3XkJoWNUv82w9IeJkpXRKpRUfF3bIQ7lV9eb98rzt-N1pIyyy75qmijN-memEpPeotaq3qcDR42o8HlRq-squg1w3cNwR9AZ6ZKrlB-H7L0QYIRT87DaNT*MLqt1b5Q__', '1467003907', '0', '', '', '1', '2016-06-26 21:05:08', '2016-06-26 21:05:47', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('83', '浪子（360网络推广）13267882111', 'asdqwieoqwe123132', '', '1466964564', '0', '0', '0', '1', '0.00', '223.74.12.228', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxy3BicbQ5qOuicT5ENJ4Lx4ibwnibiberyUIwPy07icD4s39hDTjdqcn7ygffshwlDoRDa0o8mLoFS4AHyn6AFkv5Y51y/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz81Og0AUBeA9T0HYanSG0jpj4gIpqPzZilrthpDOMF6KMNJpCxrfXcUmkrj*zs0590PTdd24D5OTbLWqt5VKVSe5oZ-rBjKO-1BKYGmm0lHD-iFvJTQ8zXLFmx7NMTURGkaA8UpBDofAGbHIQDdsnfYNPWLr*xZRbNJhBESPkfvg3DhP3WUZ13ZAy2efB3ErYHYFe1eQOLnuHmkRePt8OvVeSGKDax*Jtnlf03Lp*besYHQuRS7forso8hcmWW4Xbbg7DZywEORiUKnglR8GTSaUjiyEB7rjzQbq6vdfhMcYY-qz29A*tS-yvlxM', '1467051001', '0', '', '', '1', '2016-07-08 20:02:46', '2016-07-08 21:07:16', '1', '214', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('84', '刘正鹏-云豹直播', 'asdqwieoqwe123132', '', '1466969616', '0', '0', '0', '1', '0.00', '39.87.147.48', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBDeaySk0xtoliamK3FLprQYJItD0zFtW6DBjXgjib5JGauCeNj8x8DOzibdMJTaLTssrnt2St908cAI/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXp7YxpO77q3SRESZjd18XwpsFSWNmAjlUmGv*7ji2xidfPeXPO*2XZtg02yfo*47x9bzTTgxLAfrABBHd-qJTMWabZtMv-ofhQshMsK7ToRsQuwRCaEZmLRstC3gJ*4CJDT-mejQ0jIuf3FhKEiRmR5YjzKA3jZZg-LbB37lbFmzsk5DxZxslh-aLSQ5zwjaiiTz*oti2FtSrj3YxG0VFNXgeKp2XWN-S5D*luO*znldunR76ANZfceZyFK9*o1LIWt0GeR0jgoMDQXnQn2TbXfyFyEULkshtY39YP36RcTA__', '1467056018', '0', '', '', '1', '2016-06-27 11:38:55', '2016-06-27 11:41:51', '1', '24', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('85', '张钦明-视频直播', 'asdqwieoqwe123132', '', '1466969636', '0', '0', '0', '1', '0.00', '27.200.16.89', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6pH4F8phRtWkM39ZDowib5ylSOIRLPm6YFoOphBY0PeAYm6Q9sWqWPuqlfsoILQfibz6wxj747bl9g/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-Yqlrxhtx1ZW38ZELG6Y4TKcL8ukRQpamlKhYvzv6iSxic-fuTnnfni*74Myuz9vF4vtmzSNeVcc*Jc*gODsD5USrGlN09fsH3KrhOZNuzRcdxhEJIDQjQjGpRFLcQoM4ihwdMc2TdfQIQq-byFBAXEj4rnDfFSntLiKD*s7tcnki5mmw-TmIq*z2wo-YFSPC7QaWEt68xGZzI6ThK6SKS0VSjJ5HJNKw5nlQ2nXrA72j-VTiOZ5W133MOFFSalTacQrPw3CmJA47MeO7rneia38-ReiCCFEfnYD79P7AiM2WpQ_', '1467056037', '0', '', '', '1', '2016-07-12 11:10:17', '2016-07-12 11:10:45', '1', '222', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('86', '暖阳', 'asdqwieoqwe123132', '', '1466970945', '0', '0', '0', '1', '0.00', '42.226.115.19', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0ic5LchvKpDZQsbXlPZNibSibcDSw3xZvpGWCRvfGs97SP61cVcrw9TkqHBc9t63xgFvHibA1bWCJF1n/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0tPg0AUBeA9v4KwrbEzw6tj4oLWoTTpQwETZTOhZUpvDQPC0EdM-3sVm0ji*js359wvTdd1I55H9*lmU7ZScXWuhKE-6AYy7v6wqiDjqeJmnf1DcaqgFjzdKlF3SGxKEOpHIBNSwRZuAXdk2z1tsg-eNXSIre9bRDGh-QjkHS7Y62QWqH3gJ7OQlOTdCndD5q6j8-Jl9AljNpjGik327Jg0HgkWHjBv6Of2lCz9dv38ZiZFMJfHXS7i8aBSRRg-rVaH1nHNSGLaPPYqFRTiNshxKKWu5fT0IOoGSvn7L8I2xpj*7Da0i3YFGsRapw__', '1467057346', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-27 12:05:29', '0', '362', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('87', 'Uncle.Zhao', 'asdqwieoqwe123132', '', '1466972462', '0', '0', '0', '1', '0.00', '182.43.14.160', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/xVMWE8W0baibOVEIMTExvrgCt1Az4vGyWliaENvEdDhyvTz6WBdsCxpMWiaIyAvOWAUSoS3nLa6OkCfnywhAicEbCGMSdGofSvHX/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz9FOgzAUBuB7nqLprUbbStth4gUui4BbMuc2HDcNWYsrItTSOYzx3Z24RBIvT77-5D-n0wMAwOX08SLfbpt97YT7MAqCawARPP9DY7QUuRNXVv5D1RltlcgLp2yPhAYEoWFES1U7XehTgI8oG2grX0Tf0CP2j7sowCQYRvRzj7PJahzfva1ydqn0cQiJJHI3rrl5qubJw6bxS5JEyf2cspGqdlWoJ6Fd*Ol6sc7asgxImmfplIdFQ7MNCtWsu*3ifXsWR26ZRYebQaXTr*p0EOMIYcL4QN*VbXVT--6LMMUYBz93Q*-L*wZQYlsW', '1467058867', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 20:20:59', '1', '2766', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('88', '飞天猪', 'asdqwieoqwe123132', '', '1466973353', '0', '0', '0', '1', '0.00', '124.226.50.225', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS03ICQY8S3FYFTnmovcZMmGKNjaiaAs2UsAMgcScRXYTWPEOuGPn8TxWaHtaYXu326FtwPuicxpExqg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXh2bi2Wv6Y*ABmE7YxdbglPhGgxV0dtJaOzZh99ykusYnPv3Nzzv2ybNt2nufZZVFVYtfqXH9K7tg3toOciz*UElhe6PxKsX-IDxIUz4taczUgoQFByIwA462GGs4Bz6eeoR17z4eGAfH19y0KMAnMCLwOmI5Xd0m0ahYFFrIKSSdLWMz8lE0e1rz9YBuOxb2OU7J7aRSM1iGMQ8HLkhz6KKPu45OfbOtoOtkm03YZx2LfL-dvm-koC-tAzcStUamh4edBrocQwdQ1tOeqA9H*-oswxRgHP7sd62idAAtOXIg_', '1467059756', '0', '', '', '1', '2016-06-28 11:58:55', '2016-06-28 12:01:16', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('89', 'Boom shakalaka', 'asdqwieoqwe123132', '', '1466980431', '0', '0', '0', '1', '0.00', '218.240.50.73', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehV1ib8DtrIeIoEcY200Mrr51CgBFzHqXt6XMeIvTFZicdOHt54BP9SSgnGLD08hjHqdZJ9Iogbh4xA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq8a1RTow2cOQoZhJYpxo9tLUtmCHsloqgxn-uxOXSOLzd27OuZ8OAMBdLe-PGOfbj9pS22vpggvgQvf0D7VWgjJLPSP*oey0MpKywkozIPZDDOE4ooSsrSrUMTAN-GCkjajo0DAgOj-cwhDhcBxR5YC3i7vLNE4EXrQxz7p0VS55Kp-nUpTXeP3Qwqx63*l*HWz4zb6y*VxFYYInsCYveXby2KVJpO0VyaM2mzCCN03-RBiLxaspkmI3m40qrXqTx0FkehiEPW*krTSN2ta--0LkI4TCn92u8*V8A9JFXA8_', '1467066833', '0', '', '', '1', '2016-06-27 14:35:04', '2016-06-27 14:35:30', '0', '76', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('90', '攀爬___蜗牛', 'asdqwieoqwe123132', '', '1466981811', '0', '0', '0', '1', '0.00', '124.65.174.78', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehYcO65qd9honm8Anhkkzc07gmNQcnh8OLibDZR9NmdTMxe2Qkec8h6KKRKEYybicyMXfBTmEtxiaXnA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDrca03aDWZBeFzsUJm1O2GW4I0EI6so6VuvgR-7uKSyTx*jlvznk-HACAm0RPV3lZHl60zexbK11wA1zoXv5h2yqR5TYbGfEP5WurjMzyykrTI-YohnAYUUJqqyp1DpBrf6idaLK*oUc0-r6FFGE6jKi6x3i6Cu*CIqZFlHvs*bFAQbr23tms1UxPHy4STHaEbfms5Gx*WnZMBQkbb5Y8uuVhinl93OnjvKnSFdzek-1iE9g4DRtfLNaxqSeTQaVVe3ke5BOIoI9GAz1J06mD-v0XIg8hRH92u86n8wXjd1o2', '1467068213', '0', '', '', '1', '2016-06-27 15:14:51', '2016-06-27 15:17:09', '0', '539', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('91', '顾毅', 'asdqwieoqwe123132', '', '1466984543', '0', '0', '0', '1', '0.00', '221.177.252.28', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5HUAzGK9MLg2VVC8CDapDGCsaq59TbtmjzzpRYV7X5jcp6ibYwXMrKXvpwHoZwbMJX8QicsXicyCib1Q/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXTV422-LUme2AEEw0zgarxjcBayJ2sJaWDTeN3V3GJJD7-zs0598NBCOGnjF9V260*KFvaUy8xukWY4Ms-7HsQZWVLz4h-KI89GFlWjZVmRjdgLiHLCAipLDRwDkQ3IV3oIN7KuWFG6n-fEkZdtoxAO*MmzZP7pMu03Xe1qYNpzFJfPGwGrW07SV*p64K-8pFdNDkUhRtDzDt5EHWUVI9x2Psvhh9Dt4a2S7vdLr0z7aT5*vl9WLNTvlotKi3s5XlQGBHqeX600FGaAbT6-ZfQgFLKfnZj59P5AmsGXUQ_', '1467070947', '0', '', '', '1', '2016-06-27 15:42:28', '2016-06-27 15:45:19', '1', '24', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('92', '清风冥剑', 'asdqwieoqwe123132', '', '1466984625', '0', '0', '0', '1', '0.00', '1.202.42.85', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEIbtmP1HUPzuZ8s4OiaeiaDIJVZjOAuXer1TDAPhzOAc9fJSROlkak0JLcoR3MMYfNCZvLPm4z5PHEg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FU2fjbRFwPq24JRujNCNTHlqcBTtjLSBDtmM-13FJZL4fM7Nd*6HAwCAebK5LHc7fWissEcjIbgBEMGLP2iMqkRphddW-6AcjGqlKGsr2xESnxKEpoqqZGNVrc5CeB2QCe2qVzEujBBffd8iigmdKup5hKt5ETEeFfXixPpi-Z5tF4-bgC7rzgzKlVXCPdZtXLbvHpY65knB2cuMxTyNdZZz97598s1sWJdudLzDgzykvQ4idEtTMs-2*cqdTFr1Js9BQRgQL0TToF62ndLN778I*xhj*tMNnU-nC56BW18_', '1467681309', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-05 15:03:59', '1', '411', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('93', '偏爱白菜的菜菜', 'asdqwieoqwe123132', '', '1466986747', '0', '0', '0', '1', '0.00', '220.160.193.250', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS08PLGLUSu1lOQ0EPLy5pqgydK2Lb7vQnXHDYp42G0TjicGcLqOkTlcibXaicwuKdaskS81nvVFlRcVJ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXp7Yy2HQVZ4gVSFsGPuQwCXjUVCtZFqNB9xfjfnbhkTbx*zptz3i-Ltm2QPqwueVl2m1YzfVAC2DMbQHBxRqVkxbhm0776h2KvZC8Yr7XoR8TExxCaEVmJVstangLetUsMHao1GxtGRM7xFvoI*2ZENiM*RlkYL0OaY3jv4XTzxEO*SrOOH8j7Z9ZEVaDu4jmkE9Q3Cwrf8iBuFs*vEfWulnP8UqbUHXBx60yjfL3bqqEOikQ3hZokONl3zo1RqeWHOA1yPYgIccxBW9EPsmv--j0qQsj-3Q2sb*sH7sVaRA__', '1467073149', '0', '', '', '1', '2016-06-27 17:05:12', '2016-06-27 17:09:13', '1', '58', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('94', '文刀三木', 'asdqwieoqwe123132', '', '1466989133', '0', '0', '0', '1', '0.00', '112.17.236.35', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeeiaVNMkkyqwibP2icTXaXmv16hcQ5Px76sShDibCCu8komZtBAb15UcEhy10643wJ5iasBeYFmibFxqAT9A/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTW4y2ILAu8WJuZKlxjYNtyhXho9C6AR2rG9P431VcIonXz3lzzvthAADg6jG8TrKseat1rM*KQzAGEMGrP1RK5nGiY7vN-yHvlGx5nBSatz1aDrEQGkZkzmstC3kJeCPXHegh38Z9Q4-49vsWEWyRYUSWPS789ZQuZw*m7zAvXJ2iYzqiC5btTOWV4hwEVTrdUZzk27UtVPMUlbS8Z9V*9tJm75NAzgl9TrUQhTCjruKb8ObE6GuddWyO9xP-blCpZcUvg1wPYY-YzkCPvD3Ipv79F2EHY0x*dkPj0-gC8YBclA__', '1467075534', '0', '', '', '1', '2016-06-27 16:58:55', '2016-06-27 17:03:52', '1', '128', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('95', 'ＯT', 'asdqwieoqwe123132', '', '1466991031', '0', '0', '0', '1', '0.00', '123.139.23.98', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7PMrzl2ia59PpZpn4qmbiaVSBzCqbgxWs9BM3CBurdBicIicqibqficZCtrxzOKLIuZrqjl3sAzqmyrroy8ymJXaK3tj5/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-YplrxrTdmOjvhFAWUZFZGDGS1PWbtZhKV2ZguG-q5PEJT5-5*ac**m4ruul08UNy-PdQVlqj1p47q3rAe-6D7WWnDJLfcP-ofjQ0gjKCitMi6iHEQDdiORCWVnISyDqh1FHa17RtqFFGHzfAgwR7kZk2SIZZ8N4Ptoctpu6QXwwe4L81SQ*WuKA3cVgdEon9XC-aoglc7BV5j1*GTyeZseSp1UR*Eiv8mS6RvtxxmMSEoYqPUnU87LED-fZ*qpTaeWbuAwKIwBx3-c72ghTy536-RfAHoQQ-*z2nLPzBct2W*Y_', '1467077433', '0', '', '', '1', '2016-06-30 23:58:33', '2016-07-01 00:02:42', '1', '458', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('96', '麦客', 'asdqwieoqwe123132', '', '1466991397', '0', '0', '0', '1', '0.00', '223.104.11.245', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLBza9xf0EXq4qdgZ3oiafkGzVSEqPiaD7WhQsTVokZRKOoO0Kb8r62pzHKouKnrvnzvDhAU0FpN8c91MlwWsTqFruBw0pibdhulUc/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj81Og0AURvc8BWGrMTNjgY5JF7UQQMViWiV1M6HMQK6Uv2FKSozvrmITMa7PufnOfdd0XTe2D5urJE3rY6WYGhph6De6gYzLX9g0wFmi2LXk-6A4NSAFSzIl5AiJSQlCUwW4qBRkcBbsuUUntOMFGxdGiGdft4hi8keBfISh*7QKHDPy09jrd2tngP22De325WjfB9bF-rDclG9tMfPcFaexJEu4dW3wW9M5uXHnhbs4qHPTeU7uskjO1fB48PuyeC0x6YooXywmkwpKcQ6ybEQQptOgXsgO6urnX4RNjDH97ja0D*0Tqqtb4g__', '1467077799', '0', '', '', '1', '2016-07-04 03:55:56', '2016-07-04 03:56:04', '1', '919', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('97', '春', 'asdqwieoqwe123132', '', '1466994143', '0', '0', '0', '1', '0.00', '183.38.5.251', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBK3hvkEY0B1xrcghvXDXuHYJ0SdtHt2ibZ5lKVoGiacZDwgjB8Mzho40K2sFpHUHGmkp9icUDgNMYBI/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXh2Zi2tGOY7AEI809U3GAGnxqkLbuCDFlxa4zfXcUlkvj8Ozfn3A-Ltm0nu03Pi7LcDa3m2nTSsS9sBzlnf9h1IHihuduLfyiPHfSSF0rLfkTCfILQNAJCthoUnALe3MMT3Yuajw0jYvp9i3xM-GkEqhHv4lV0vXxaDcljY9bhQ6WS*7A3S5ohhGsIyBVtWRYctoZC9DJsAgib5*1lSrM8Lhtdz5q5OG5ywWLjIgapG6pD3kUqGd7ITbVYTCo1vMrToJmHCPEpm*i77Pewa3--RZhhjP2f3Y71aX0BoDVbqQ__', '1467080545', '0', '', '', '1', '2016-07-01 11:03:16', '2016-07-01 11:03:46', '1', '786', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('98', 'Delia', 'asdqwieoqwe123132', '', '1466994296', '0', '0', '0', '1', '0.00', '183.39.154.12', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxyedFYlTGib5rqYqbEvPYdiaYdTWvHan3w33dcRATic0qKr8eAZJ8AnBc128VYibPCkEuDKU2YllEX2COqfjxP3koFd/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXDszEt-7ou8YFt4BxiUKxhTw0b3XJdhA7qAln87jpcYhOff*fmnHu2EEL262N*W263zWethR6UtNEU2di**UOloBKlFm5b-UPZK2ilKHdatiM6PnMwNiNQyVrDDq4BOqGOoV11EGPDiMT7ucWMOMyMwH7ENOLzh2VxHOKnLCSLnBf0qGm*OriJtwCVsTiIlnEd*KTou-f8OYQo7P31etjMVvyl5GQPLm1C5iZZE5*84S1hs3lU0k16zxlN74xKDR-yOiig2HExmxh6km0HTf37LyY*IYRddtvWl-UN4LVZ2w__', '1467080698', '0', '', '', '1', '2016-07-12 16:17:53', '2016-07-12 16:34:33', '1', '420', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('99', 'C', 'asdqwieoqwe123132', '', '1466994859', '0', '0', '0', '1', '0.00', '183.37.235.31', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLBo81Ve5rXa53oBVrC4WxU6SJChkEA6iaujHkvJwOVM6aINFhyTj5gPz9u1Ebr0ibJ6ibUBebRJgahGgwHbSUGxQljwA2cN7S2WcA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq0bbIiAmPqAgLjrnBJQ30tCCLRk0bbcwjf9dh0ts4vN3bs65nw4AwC0e8zPSNON2MLXZS*aCK*BC9-QPpeS0Jqb2FP2HbJJcsZq0hqkZsR9hCO0Ip2wwvOXHQHgZepZq2tdzw4zo4ucWRghHdoR3My7T8naR9dMoFvSJFqs0Rv4NZA-N*JEPEqPdxMRJqe98oXSSLUXM0-h9nQRVQvptW-UST1S*pXRfPId59to1q-L8RWxUrAmu7rtrq9LwDTsOCkKIvSBAlu6Y0nwcfv*FyEcIRYfdrvPlfAMK-1yW', '1467081261', '0', '', '', '1', '2016-07-05 14:58:30', '2016-07-05 15:10:06', '1', '2830', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('100', '68', 'asdqwieoqwe123132', '', '1467003606', '0', '0', '0', '1', '0.00', '211.100.51.77', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeeiaZTgPzTYdicFFQpq6elSVnXRONC7aPRIw8fJkh4aqFqAhTuMaNicTicfMInFvOYFMd1gGHb4etHkiaQg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXp7Yy23RjDu2aajKXEfaCRq6bSDioBaulkm-G-b*ISm3j9nDfnvF*e7-sgpdtbnuftvrHMHrUE-r0PILj5Q62VYNyysRH-UB60MpLxnZVmQBxEGEI3ooRsrNqpayCchYGjnajY0DAgmlxuYYRw5EZUMWDymM3j9YOx1RRWeFmu8WlR6KcRkqnevHwko-07JSnNJXlu3*q6y-q4JAnpC5JAQQlchLEldGXu2r7clNvjKz5wYU7zJW*yVcALp9KqWl4HTUM4xhM4c-RTmk61ze*-EAUIoehnN-C*vTPxAFyC', '1467090008', '0', '', '', '1', '2016-06-29 18:47:12', '2016-06-29 18:47:15', '1', '45', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('101', '时空恋旅人', 'asdqwieoqwe123132', '', '1467015831', '0', '0', '0', '1', '0.00', '1.81.204.221', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/n7rh6ibhGlba5VxiavCI0jfUFAs407aI4yEicIIhxIwiagea3ibib7u6pdXK7PTJ39iau6RtBnpyVeibrYolONrDDcFamUNKicTHv7HtE/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXptdGWrw6TXVRFkaxkbEYXbggfBctcqaWbQ*N-V3GJJF4-580574dhmiZ4WKzP87Ls9kJnepAMmJcmgODsD6XkVZbrzFbVP2RHyRXL8lozNaLl*haE0wivmNC85qcAnmFvon21zcaGEZHzfQt9ZPnTCG9GpEFyfX-b4rsibTer1SENrvItfC-dnXhaLkXyrC4CWhTJi91gO8aIcAIX*yYkxz5iXSRxGIc0ofARkpuU5sNrGAzruH2r6lm0IfP5pFLzHTsN8jB0HM*2J3pgqued*P0XIhch5P-sBsan8QWDSltJ', '1467102233', '0', '', '', '1', '2016-07-03 19:10:51', '2016-07-03 19:11:21', '0', '64', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('102', '胡图+', 'asdqwieoqwe123132', '', '1467044921', '0', '0', '0', '1', '0.00', '113.76.31.254', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeejZZJe0QbDnicycibFsEMZmLyLUDrkLzu7C015Tebk8kwS8PDhZibjE7XM4iaXQqibgph5M6ic7tNbHalqJiaHLAgRoibwn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXh2bi2iB0mewDiTCcEFtyUJ8KgLHfGUtrq-hi-u8qWSOLz79yccz8t27adpzi-ruq6exemNEfJHfvOdpBz9YdSQlNWpnRV8w-5QYLiZdUargYknk8QGkeg4cJAC5cAnVI6Ut28lkPDgPjm5xb5mPjjCGwHTO6XEYvUPlm1xt1XuaZumr2wuA-73VTPH9YLgYSKF7saZJNuugCCZfvMiyzzcnaYrDdVER-nXAeEi7gImQ6jx0mS9sBOGd7OZqNKA2-8MuiWIupSQkb6wZWGTpz-RdjDGPu-ux3ry-oGALtcRg__', '1467131322', '0', '', '', '1', '2016-06-29 10:45:15', '2016-06-29 10:49:20', '1', '195', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('103', '武', 'asdqwieoqwe123132', '', '1467045303', '0', '0', '0', '1', '0.00', '117.136.12.118', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxyUZ97kv0pANia2TOzfU8jGC1OQpeAHdCOSuicg19FrNvAsCfcdXmhe2zJAibojzpM48UIBq6IElJ68vQ4QpSgKm01/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXhdca1OISa7IF1TNm-OJlo9kJwbc21jLFSJpvxu2-iEkl8-p2bc*6XYZqmtZxG1*l6va1ynehDwS3zzrSQdfWHRQEsSXVyo9g-5HUBiiep0Fw1aDvERqgdAcZzDQIuAddzvZaWTCZNQ4O4d75FBNukHYH3BmfBgoZU0D0LwjlZLjZH6Q34CuRg8uRmhInRPQRxLMc7LMvHMvPBH6uD6kSziE1fHz4o7e687ktnWPvVSjzXcZgdR5M8e6uG9vyz329Vatjwy6BbF7k9jJyW7rkqYZv--ouwgzEmP7st49s4Ady0XCw_', '1467131705', '0', '', '', '1', '2016-06-29 10:48:26', '2016-06-29 11:05:07', '1', '720', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('104', '洛。', 'asdqwieoqwe123132', '', '1467047394', '1467047868', '0', '0', '1', '0.00', '120.194.238.66', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaELIuPfIeEflLr1LUZY1qmicWiaX0scmhRmgRzuIXoQPaLJ4gSh33RRiaWxlEG9RUlPSRAvpcsfibdrUww/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz09PgzAYBvA7n6LhqtGWwWpNPCBhybTz4EQMF8KfsnSMtraFDI3fXcUlYjz-njfP8747AAD3iW4viqqSvbC5HRVzwTVwoXv*i0rxOi9svtD1P2RHxTXLi8YyPaEXEA-CeYTXTFje8FMAX2EyU1O3*dQwIfK-biFB3p8I3024iZNoHbd9l*35bfGmJSrTKrpLfZnBYJcWNGz93tz32bF8HkgiQx6Hl1TUkb9WjQhG87LfCLnoHs5eV9sMDoL6j2NyWIkyPdDW3MwqLe-YadASQ7xEBM90YNpwKX7*hShACJHv3a7z4XwCXj1dRg__', '1467133797', '0', '', '', '1', '2016-06-28 22:48:22', '2016-06-28 22:48:27', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('105', 'Tino', 'asdqwieoqwe123132', '', '1467067171', '0', '0', '0', '1', '0.00', '117.136.75.235', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLA812fCR2pQkjxFsvTOhWKyHjP2aOpj8WjvQVqCP8UAQ4KB2yicfIbad1HxicrCpvibHMJ3PSWCVzEkw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19PgzAUR9-5FKSvGmn5u5rsYVO2LEg2dST41DBatGNSZBe2YfzuKi5ZE5-Pufmd*2mYponWD883WZ6rtgIGp1og89ZEGF1fYF1LzjJgTsP-QXGsZSNYVoBoBmh71MZYVyQXFchCnoVgNHI0uuclGxYGSNyfW0yJTXVFvg4wDh-vFhPSxXBUCgBm1jLCL*3mPnCTLozsZZrMQy74Rz*7aulcTeQ09Tb*bpXWO--t6SB72JYF7pK0b91FfKoCKLaWlUzzqHQO47E2CfJdnIP8AFOPBnpzJ5q9VNXfv5h4hBD6242ML*MbETlcqg__', '1467153573', '0', '', '', '1', '2016-07-11 17:16:43', '2016-07-11 17:18:37', '1', '11480', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('106', '天涯浪子', 'asdqwieoqwe123132', '', '1467074496', '0', '0', '0', '1', '0.00', '1.194.23.37', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/gw2JLn6TNiaDCYf5nBUluy9EKoVABoIricOT4iaKLAkuutWbicu3fgV8g6TM2wScAebictYdeiamYAqnhqpIrF1buoBbiaFfdnQVvUf/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FvgjAURt-5FQ2vLksLou0SH8jGkqEMUDHxqSlQ8GqEplbHsvjftzETSfZ8zs137peFELLXi9WjKIr23BhuPpW00ROysf1wh0pByYXhri7-Qdkp0JKLykjdQ8djDsZDBUrZGKjgJkwppQN6Kg*8X*ghGf-cYkYcNlSg7mEUpM9vvskCD7eXPUwgzV-cfUWTnGXh6KNr1VwuA0EizcYHGq588Eex6Og2h67YJVn4HvkbeRY4AbXZ1mQpix1*jdP5Me4W9Ww2mDRwlLegyZRg12HDoIvUJ2ibv38x8Qgh7Lfbtq7WN6BdW7M_', '1467160899', '1', '', '', '1', '2016-07-12 19:16:16', '2016-07-12 19:16:13', '1', '178', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('107', '攀', 'asdqwieoqwe123132', '', '1467077175', '0', '0', '0', '1', '0.00', '223.104.19.20', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzkXPMN3XOKgruh5wUl42MQH1ULu5Qh84GTaefvStMxqNtiaq7DicjTibY8Udnop4KISA3JOviaACzwhkjuQM2hq8GkB/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-Ypmrxhp58aoCQ8EdcwqCaMP42np1rJcCOvoCg6N-12ZJM74-J2bc*6HgxBy*cvqVhSFPlY2s*dauegeudi9*cW6BpkJm90Z*Q9VW4NRmdhYZTr0Auph3I*AVJWFDVwD4XhMe9rIXdY1dEj871tMifcnAmWHr4-LWRw18yct6EglD1s4nJQu07a1e5YUc70i*MyHft4uJDQzOYVpzgZsEB25jd9jq3dvcktY6vMiP-iRED7jyeJ5bddDk5aTSa-Swl5dB43CgAYkCHt6UqYBXf38i0lACKGX3a7z6XwBPFFczA__', '1467652757', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-04 12:40:49', '1', '2438', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('108', '沃德', 'asdqwieoqwe123132', '', '1467077598', '0', '0', '0', '1', '0.00', '1.194.16.52', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmmPMhsXtRVA8rgBeEg2IQHBRictPvYW7IK9keJjWBIIhec77n0tkNX3rX4BMj760I9ZvuTaPRYxeJ5Q7fUIHfWp/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXp64xpywBr4sPGJhK3wBgu6kvT0A7rVuigW9YY-7uKSyTx*Ts359wPx3VdUCzW16wsm2NtqLFaAPfWBRBc-aHWklNmqNfyfyjOWraCsq0RbY-YJxjCYURyURu5lZdAeEPQQDu*o31Dj2j8fQsJwmQYkVWPy-lTlKxmUYlxNL1XMx0u7a7bB-OFsadHy-DrOg199bLPhd3ErbKTpJrkMU8UgbAYlZ7M6vQ9S4twE2cj9sDVW7M6HlAu0PTwXN0NKo1U4jIoCBEMxtAb6Em0nWzq338h8hFC5Gc3cD6dL5iIW5w_', '1467164003', '0', '', '', '1', '2016-06-28 17:33:25', '2016-06-28 17:38:10', '1', '147', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('109', '蚊子', 'asdqwieoqwe123132', '', '1467102817', '0', '0', '0', '1', '0.00', '106.117.180.128', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxxf1icEQTjL8qWxu5vu3lPlIRCGpAAfTHXZHqCmzyGsRHOtEZxbK1qQ874w3l66NO2CVt5nGREGNSA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FU2fjfZSR4fJHhAmWTY2O4lTXho2ylInhUAxGON-V3GJTXw*5*Y798NBCOF09XCZHw51r40w743E6AZhgi-*YNOoQuRG0Lb4B*XQqFaKvDSyHaE78V1CbEUVUhtVqrPApr5r0a44iXFhhHD9fUt8cH1bUccRJnMeLqJhF1GaPcXLqe561t8Rvek6db9ZDnWcxACMv9DtIqz2YaBuox1L*Ckn*5TxigQrzsw6o-PnNDCvxi3lNvXWj0OZXdXH2cyaNKqS5yCPAQUP7KA32Xaq1r--EpgAgP-TjZ1P5wszV1q7', '1467189219', '0', '', '', '1', '2016-07-06 08:25:35', '2016-07-06 08:27:47', '1', '157', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('110', '隔壁老臧', 'asdqwieoqwe123132', '', '1467140860', '0', '0', '0', '1', '0.00', '42.102.230.137', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJHh7X2vL13PBmjiapJxaGA5EexEQ10Fz7kV24szm3W6jj5YjBvicoVTibKaBBqzibwvEXlZjb87Qkiagg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTW41rKV818aIhzAxhcTL8uCKEllm3QVO6wTT*dxWXSOL1c96c835YAAC4TrKrsqraQ2MKc1ICgmsAEbz8Q6UkL0pTEM3-oRiU1KIoayP0iLZLbYSmEclFY2QtzwE-oM5EO74txoYRsfN9iyi26TQiNyOmUR4ubmebulvliuXMnQVPRDz0W71aLkiSJf7*kb06zjEj4TwNCZMRG6qOBfFL9RYntVa9PK0TuruLnnmcXiyzMh7e6U7peXu4728mlUbuxXmQ52OPep490aPQnWyb338RdjHG9Gc3tD6tL3I0W6k_', '1467227261', '0', '', '', '1', '2016-06-29 20:01:12', '2016-06-29 20:02:48', '1', '20', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('111', 'K', 'asdqwieoqwe123132', '', '1467156534', '0', '0', '0', '1', '0.00', '183.194.77.98', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCKcibOmjVCKSsl2tXugrEeYaJ9RCCnznC2mQqdlZMSuXNCiaghiay0M6894bh5oZsOiakjrK3OuqoN0w/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0tPg0AUBeA9v2IyW43OgDzGxAVaSLRI5FVTNhMC0zJSKI8pD43-XcUmkrj*zs0590MCAMDQCa6SND2eKkHFVDMIbgFE8PIP65pnNBFUabN-yMaat4wmO8HaGWWVyAgtIzxjleA7fg7oBtEW2mUFnRtmxDfft4hgmSwjfD-jsxU9PNpuHHNns8o1u7b9izy0*uGpyafuumeRbzpGqE*HIPLcKjW5ZXaFOrjrcTxtc--tfZD3pfdaFHl-aO9L31s1sbbevgSbJmXD3aJS8JKdB2k6NlRFURfas7bjx*r3X4RVjDH52Q2lT*kLd61dxQ__', '1467242935', '0', '', '', '1', '2016-06-30 19:53:24', '2016-06-30 19:56:24', '0', '977', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('112', 'vma', 'asdqwieoqwe123132', '', '1467161684', '0', '0', '0', '1', '0.00', '211.97.164.78', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6iaxUFKoRajYY3yvdKrwtOyhgS1ibeTHvic0J5kvlDtXZfqDszpgBko7ZXDMzSQLNFD9DOASCbSEwGQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDszEtg0GX7KE41ClM6UwUXxpCW9PMdawUwrL4391wiRifv3Nz7jk6AAD3JV1fl1W1a7Vl9lALF8yAC92rX6xrxVlp2cTwfyj6WhnBSmmFGdALsAfhOKK40FZJdQmE*I82fMOGhgGRf7qFGHl4HFEfA2ZJfrO8lQ*tn2aiWPYe-STFCt2HRm4k1dukxtGTRvuYkpbY1YQokr413SteSI4e13y6r3qZhTGk5rmgseGLJM-e74JIp4cun89HlVZtxeWhaYgw9KNgpJ0wjdrpn70QBQidIufRzpfzDXO0W0Y_', '1467248085', '0', '', '', '1', '2016-07-09 17:52:20', '2016-07-09 17:52:52', '0', '34', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('113', 'Mr_Zhan', 'asdqwieoqwe123132', '个性签名', '1467163292', '1468291860', '0', '0', '1', '0.00', '125.77.238.22', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '单身', '教师', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5CrmIaRn7AOTV3HrFZrCSxb8kaia2GkQvN8t19oZgdiaJoCz1jrS6V5k2GGFxaVEVg3QucSXHialhJA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq8a0bGXUt0XUYdjiAi7gS9PRbtzYQQPVsBj-*yYuscmev3Nzzv32EEJ*nmZ3oqraz8ZyezTKR-fIx-7tPxoDkgvLJ528QjUY6BQXO6u6EQPKAozdCEjVWNjBJTBjeOJoLz-42DAimZ5vMSMBcyOwH3H5WD4k63gguT7kg5GbJnk3OqRUvhRlEYdZ-CSzI9uso1pQpUHsk3qeFgtdRUm72JIlyCgtbl7F82qbS0NWZVuD7EH0xuiKvTmVFg7qMiickfNHbOrol*p6aJu-fzGhhBD2u9v3frwT*s5cpA__', '1467249694', '0', '', '', '1', '2016-07-12 18:57:55', '2016-07-12 19:04:23', '1', '6245', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('114', '中国梦', 'asdqwieoqwe123132', '', '1467163356', '0', '0', '0', '1', '0.00', '117.136.79.80', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBDmMjtzASKA0782iaUQUNjNicRUZfITKXrGaTDXjeUkmy96ZlIKvvEwW6f7xmKeVqYPGLiamJib665JI/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FaTPRttaJDXxQe02wHXZnJOMF4JQmo4NaqkDY-zvKi6RxOdzbr5zPxzXdcHTfH2e5XnzVtvUvmsB3GsXQHD2B7VWRZrZ9NIU-6DotTIizUorzACxRzGEY0UVoraqVCfBp5CMaFtU6bAwQES*byFFmI4VJQfIJ5v7cMUif5WFt3e6mnbxbs5ZP5Gv7PkhmB7yXX*MtlEkLzh9TGZLGcowQIbMeIyTZrOOvXyfBKxsO74QhLxsq4Vsu6BQhJE9vxlNWnUQp6ArH1GMvHHQUZhWNfXvvxB5CCH60w2cT*cLlU1bnw__', '1467249759', '0', '', '', '1', '2016-06-29 17:22:39', '2016-06-29 17:26:31', '1', '57', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('115', '朝岩', 'asdqwieoqwe123132', '', '1467165839', '0', '0', '0', '1', '0.00', '124.72.46.163', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIcG5DLC52XK6RT2HOHzVTcXkZkfxfFAglw0weSiaKNFXusAjCSsnI59lJDYpvmyrVyD8lgRE3KPxU/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAUBuB7fkXTayMtMrou8YKsmKBsS*Rj8YogbeeZsSB0*LHsv6u4RBKvn-fkfc-RQQjhLEkvq7puDsaW9qNVGC0QJvjiD9sWZFnZ8qqT-1C9t9CpstJWdSN6M*4RMo2AVMaChnOAccIm2svncmwYkfrft4RTj08jsBtxFeXLWHRMp0*9*-m6nN*Fh7VIb*9zJViUJvuBF8EQJXvqyQKMDiEK3SIPYIjjVSYflTKbbS3CN2GyG*jX2*bB1TwjOql2bDO-nlRaeFHnQQGj3A98OtFBdT005vdfQmeUUv6zGzsn5wuBwltg', '1467252240', '0', '', '', '1', '2016-07-10 16:12:28', '2016-07-10 16:14:34', '1', '101', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('116', '江', 'asdqwieoqwe123132', '', '1467168105', '0', '0', '0', '1', '0.00', '58.248.16.36', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBCLtSFzdVWicMe31bhs5qERicKSCNQo1tKPcAbL5xFk6VOXIiaribhK9cMmA93XGlKdicVZumsibO6sVg7/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXp7Yy0yMdq4gXahmEwkWyCu2oK7WYzVwjrcGj87yousYnXz3lzzvvhuK4LVvnykjdNe9SGmbGTwL12AQQXf9h1SjBu2FUv-qE8daqXjG*M7Cf0Q*xDaEeUkNqojToHYgznlh7Ejk0NE6Lg*xZi5GM7orYTPtCnuyxt83k74pIkdYhkTLJB3Bc7mM6edZlFPK3GVa*HRVA9viSKJh5d0rdxW8wW9Z4eX4d6Tcqq1gVRhEe*563z92C85SeSNTdWpVF7eR4UxQhHGMaWDrI-qFb--gtRiBDCP7uB8*l8AWTJW0I_', '1467254507', '0', '', '', '1', '2016-06-29 18:41:48', '2016-06-29 18:43:11', '1', '19', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('117', 'kermit', 'asdqwieoqwe123132', '', '1467168125', '1467168824', '0', '0', '1', '0.00', '180.164.137.41', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7PMrzl2ia59Ppd9gNTkpek8sFk9Nzpp8k7D7AMCRDvicDMJmrjDku7nOKjTjAjRFSicGOzGZVCXMF15vJdwvKnGzSx/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXTayMtA5qa7ALJHIq4OY1GbxpGS1P5qtAJ0-jfnbhEjNfPeXPO*2EBAOD99d1pmmXNrjbM7LWA4AxABE9*UWvFWWrYrOX-UAxatYKluRHtiI5HHYSmEcVFbVSujgFCEZ1oxws2NoyI3cMtotj5E1FyxGRxG15eZMR9jLOXRvvrKnhvbFWjUHqeu-Xs56hIZKpneFXYHUoCdb7j5boqg4coGkojl8TkZXxDeNwPoQw3-dXr4qlfmu2K7Pv5fFJpVCWOg3yCqU8df6Jvou1UU--8i7CHMabfu6H1aX0BtkNcKw__', '1467254526', '0', '', '', '1', '2016-07-06 15:42:04', '2016-07-06 15:43:13', '1', '1471', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('118', '缘来如此(林工)', 'asdqwieoqwe123132', '', '1467171897', '0', '0', '0', '1', '0.00', '125.78.140.240', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDPR51xnHoWZia0sKrT0Gtqj9PhrHicY2hZ38VHn6MOZCAzcKsHbCZI8xLziaq6XSZqfSyiaM7wwatwCQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXDq0Za-gxrsgeCGyswddPEhBeC0JEbI5DSdhvG7*5kS0bi8*-cnHO-DYSQ*Za*3hVl2apG5vLYcRM9IBObt1fsOqjyQuaOqP4hP3QgeF7sJBcj2h61MZ5GoOKNhB1cAj4lU*2rz3xsGJG4p1tMiU2nEahHXC82IQvZRsVMRVmcLUTk6245uHVvbdNVnLXwvh2E5STDUma2DiBIdVZbhfOkkoNf7Vno1S-s8fnmyBP9EYmSuyok3hpW-T6YzyeVEr74ZdDMP-0zo-cT1Vz00DbnfzHxCCH0b7dp-Bi-t8tb1A__', '1467258298', '0', '', '', '1', '2016-06-29 19:44:59', '2016-06-29 20:01:40', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('119', '杨小焰', 'asdqwieoqwe123132', '', '1467172996', '0', '0', '0', '1', '0.00', '120.39.51.47', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGISl9XYj5FKjrggtDlXG1Xj9nZf6cLS1OUCecic25DdhRK6iciaDdEHib7Ye7hicicHakTnmwvcSnkUNCKu/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0FPg0AQBeA7v4JwrdEZ2oJr4qXQNlA0EKGJXgiFXdygdLO7RcH431VsIonn703emw-DNE0rjR4ui7I8nlqd615Qy7wxLbAu-lAIXuWFzuey*of0XXBJ84JpKke0l8QGmEZ4RVvNGT8HXII4UVU1*dgwIi6*b4GgTaYRXo94t868IPHvBeC8O9QqgefNxs-ChnnJqZkF6rHon8Ik7UnobK-pS1oH9X69ktkufoNhdhi8VcyiWGrWsdLbL5xotw0hxqHy-SutbieVmr-S8yDHtQFd4k60o1LxY-v7L*ASEcnPbsv4NL4AhONbbg__', '1467259397', '0', '', '', '1', '2016-07-12 14:09:39', '2016-07-12 14:10:57', '1', '6782', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('120', '脚步', 'asdqwieoqwe123132', '', '1467177925', '0', '0', '0', '1', '0.00', '120.36.86.59', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/n7rh6ibhGlba5VxiavCI0jffPALUDX3icFI8loEG15gS7yp7fGd4AuiaEQQiaIglOSqEY62kjT69OOj6pagM3HntQZUCSlUHofz6t/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj8tOwzAURPf5CitbENjpIxiJRbHaJiW0tAlCYmO5ju26kEcdUyVF-DshVCISi7s6ZzRzPx0AgJtE8RXjvPjILbVNKVxwC1zoXv7BstQpZZYOTPoPirrURlAmrTAd9EbYg7Cv6FTkVkt9FnyMvB6t0jfaNXQQDdssbA3cV7Tq4OP0mYRrwlnC9nLhSz6ttqdNGYfxDKvJw7wm96Jhu0l8nRq*2e0vVKjW*J2sno7zV7WUL-wwyLb2gIOmyWckSk72Jo6SLAgWq6Ie3vUqrc7EedDY92B74x49ClPpIv-9F6IRQgj-7HadL*cbKx1c4A__', '1467264326', '0', '', '', '1', '2016-07-10 13:51:27', '2016-07-10 13:53:57', '1', '434', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('121', 'JLD', 'asdqwieoqwe123132', '', '1467188388', '0', '0', '0', '1', '0.00', '210.22.107.98', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJkzDOdkAtk2Tw2kxQEd1tMx4mq7SRUiacWUHfkeJ7bSU1BvSCgobys7cPiacibFkqxEFRPn9ctG4LrA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0tPg0AURvf8iglbjXB5TcfERduUFmOxaKmNmwmFwd5oZ3gMVWP87yo2kcT1OTffuR8GIcRc39xfZHmuOqm5fq*ESS6JaZvnf7CqsOCZ5m5T-IPircJG8KzUoumh4zPHtocKFkJqLPEkUAbugLbFM*8Xegje963NwGFDBZ96uJyl0yhUNPWwHq2Zu5e3h*1iM5Er2G5ehUjC68ySkfeQ7ubK93GMszEEZ3GaxxOxj7q7KA8fV9OXaF7KxOkUVUG9tOqc7hZJbLVXg0mNB3EKCqgDFEbDoKNoWlTy918bfABgP92m8Wl8AS5ZWrc_', '1467274789', '0', '', '', '1', '0000-00-00 00:00:00', '2016-06-30 01:34:27', '1', '0', '1', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('122', '小小的太阳☀️、', 'asdqwieoqwe123132', '', '1467209307', '0', '0', '0', '1', '0.00', '223.104.255.107', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzlb1CfbDn303nqm9XT433qwUofZY3fEla4Kj8s7YL6Hia3ujTkBjnnx3vAlF1ILZiavHNSpKic6zahbsheCkpvLDX7/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPwjAUBeD3-YqlrxrbjrFREx4mEViCCA6V*dIsbdd1wFhKxyDE-65OEpv4-J2bc*7FcV0XrGbJXcbYvqkMNedaAPfeBQjc-mFdK04zQ3ua-0NxqpUWNMuN0B16feIhZEcUF5VRuboGQoJ9Sw98Q7uGDrH-fYsI9ogdUbLDp8flKB4zuE1KDUN5incyXzy8yXI9hWaeBlHTHOE7ROdCzlZKFJGK0hfGSl6MSBC0ME5b6fvr1yR-njeTBfq4GZfTiBXLCdxu2uHQqjRqJ66DgtDrDTAaWHoU*qD21e*-CPcxxuRnN3A*nS*tUluc', '1467295708', '0', '', '', '1', '2016-07-09 08:22:02', '2016-07-09 08:38:42', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('123', '蔚腾飞·David@WiserUNION', 'asdqwieoqwe123132', '', '1467228710', '1467589805', '0', '0', '1', '0.00', '111.206.73.181', '', '', '1', '0', '0', '0', '0', 'fd', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '型男', 'fg', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5XkRI8RTR3SIDldwFNZhjelc591iajAHRQeHCQSYhRlE5pic09nich6iabq2X2fF0TpcNOKSQMYfTAZw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXD64xpy2qpiQ*TjI0oOt0ww5cGaSF1GSulMtD431VcIonP37k55344AAB3c7s*z-L88FZZbnstXXAJXOie-aHWSvDMcs*Ifyg7rYzkWWGlGRAThiEcR5SQlVWFOgUoQ3SkjdjxoWFANP2*hQxhNo6ocsB4ngTRQ7DtE3acmUln4vRV72atfc9hGYr7m*e*Dsk0ffGDdBLTel1G5WKzvOuio9W1EFkc*nK1KDwdzpdelWxp-WT8*hHKfdtfN1ejSqv28jTogmJCCcIjbaVp1KH6-RcighBiP7td59P5AiM*XL4_', '1467315112', '0', '', '', '1', '2016-07-02 13:05:02', '2016-07-02 13:05:39', '1', '270', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('124', '远方༄ེ', 'asdqwieoqwe123132', '', '1467236782', '0', '0', '0', '1', '0.00', '111.206.73.181', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKEXDKQHJHQnP7ZPjoxJFaocHibse3Mvwy8wAchf3GYoYgkKNTkDoYiajmUO61j01XHTib3bDh8bO8jQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11LwzAYBeD7-oqQW0WTuK6L4EXmWqm0itqp9CZ0TaqZ-bKNYUH87846MOD1c17OeT89AADMkoeToiy7j1ZzbXsJwTmACB7-Yd8rwQvNzwbxD*WuV4PkRaXlMCHxKUHIjSghW60qdQgEe3d0FG98apgQz-a3iGJC3Yh6mTAN15fxldiwLMI0ZxbFhkSnRK7j*naFyGNqzGbcbm9YQk3Q6JypkI0hYiJKX3HC6vfM7pZEi7J9XlH7tOzsUd5k1eK*Zkhe3104lVo18jBoHpC57y9mjho5jKprf-9F2McY05-d0PvyvgFJHlso', '1467323184', '0', '', '', '1', '2016-06-30 15:34:48', '2016-06-30 15:39:12', '1', '86', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('125', 'abs', 'asdqwieoqwe123132', '', '1467237954', '0', '0', '0', '1', '0.00', '115.35.130.174', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgAwG8B7MVbwYZKQTkyOLdN735Rno2iaobq7stHFiaaLGuk2nb1W4zaYWBh1bRud1GXodlnoFeYQkRfy/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PwjAYBeD7-Yqltxhti91WEy8GghlTYJmQ4E0z2rI17CttXVDjf1cmiUu8fs6bc95Px3Vd8PKUXmecN2*1Zfa9lcC9cwEEV3-YtkqwzLKxFv9QnlqlJcsOVuoeMaEYwmFECVlbdVCXgE8xGqgRR9Y39Ihuf24hRZgOIyrv8Xm2mUbJw2oCy3SXdEXerCY42W*n8TwbBxI-1nZfxiFcE2NSHdJtEuXxzXG5POFFzJtKd9GGmzIcFa0sdqIKiMr5DC5e58Hoo*T3g0qrKnkZ5PnY83xCBtpJbVRT--4LEUEI0fNu4Hw534p4W6Q_', '1467324355', '0', '', '', '1', '2016-06-30 14:05:55', '2016-06-30 14:07:19', '0', '40', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('126', '范哥•••', 'asdqwieoqwe123132', '', '1467244979', '0', '0', '0', '1', '0.00', '211.157.183.190', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIXRk0ESmzUPuAChd94uMVLMWtv1E0OxQC0tFkuZDVMKf2CTSTYSib7dywRvgXfviaicHVAGQMAYB8Lr/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz9FOwjAUBuD7PUXTayNtt1HqHQyVJgNCYBG8aeZaoF3YZld0QHx3cZK4xOvvP*c-5*IBAOAqXt6nWVYeCyfcqVIQPACI4N0fVpWWInXCt-IfqqbSVol065RtkYSMINSNaKkKp7f6FqCMkI7WMhdtQ4s4uM4ihgnrRvSuxenjJuKLKDjO14aymAc8r8160st6SYzeNuPJfpGYlXHngA7IeZePh3w-5A19evGflzOjeUQGqnl-PSVZyfJaHuiMfNppf864PypGYafS6YO6HdSnhPrXhR39ULbWZfH7L8Ihxpj93A29L*8bN1ha1w__', '1467331382', '0', '', '', '1', '2016-06-30 18:07:44', '2016-06-30 18:08:12', '1', '170', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('127', '安之若素', 'asdqwieoqwe123132', '', '1467253011', '0', '0', '0', '1', '0.00', '59.56.186.248', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS01z0mchz8P2X8P93vt5AjOz23bLJ6pRibFpIUBYFAPlibE5F8HQSOCibfdfgYLibWA4HtkibhMaMTSEKl/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXD7YyhdYzVO8AxIRDDPjL1piFtYRUKWCqOGf*7iksk8fo5b855PwwAgLmLt9cZpc1brYkeWm6CW2Ba5tUftq1gJNPkRrF-yE*tUJxkueZqRGRjZFnTiGC81iIXl4CD0XyiHSvJ2DAinH-fWhgiPI2IYsRk9eSHqR9vZ357z4YVjvJyX4R9nAczrzo11WNHo-Pa0VKyPrUr4YZH90ElZUo33SFcH3fDAj7DTfRS46DYv1L5Lg8yYL7r2Unn3U0qtZD8MmjhoCVcQjTRnqtONPXvvxa0IYT4Z7dpfBpfFFNcpQ__', '1467339412', '0', '', '', '1', '2016-07-06 10:25:28', '2016-07-06 10:42:08', '1', '57', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('128', 'Jinnee', 'asdqwieoqwe123132', '', '1467253939', '1467315788', '0', '0', '1', '0.00', '123.139.23.98', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEIK0mic7Ub2GbUae3qeq3thMMddGIAsXze4yxknZ78g9EDJ5HspMxhN03TQHVqGrcD92ceZNZf19Mg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0FPgzAAhe-8iobrjGs7Cqu3CcwRRcMUIyeC0LFuAhW6Kpj99ykusYnn73t5730ZAADz6e7xMsvz5lDLVPaCmeAKmNC8*INC8CLNZDpri3*QfQresjTbSNaOEBOKIdQVXrBa8g0-Cw7FRKNdsU-HhhEi6zsLKcJUV3g5wtBP3CDy7FU1hHn-PCUTlFg8iq7Xw-tQekvpB25i3ZZsHe-u1a4*LILt4qGSgbv8wF0*n8Lt6yr2VdsUL3Eo3D3pLTJRypY3s857a7RKySt2HmQ7eI4dS3*kWNvxpv79CxFBCNGf3aZxNE636FwE', '1467340340', '0', '', '', '1', '2016-07-01 14:43:47', '2016-07-01 14:46:12', '0', '11', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('129', '天天情人节', 'asdqwieoqwe123132', '', '1467307907', '0', '0', '0', '1', '0.00', '106.119.55.110', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA1Q2WA48MlicBaLERAEt6VcV5bg4P2uIl3kMibq14B8yAiaoncuz5Bv3VIBeUSuIcM3NVxVKQVqdibK5/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0FPgzAAhe-8iqZXjWmBgV3iAcysTMoSV4OeSB2FVRg0UBc3439X2RKbeP6*l-fepwMAgDxdX4nNpn-vTGEOWkIwBxDByz*otSoLYQpvKP9B*aHVIAtRGTlM0J0RFyFbUaXsjKrUWQiJG1p0LJtiapgg9n*yiGCX2IqqJ8gWT7dJVMcsD57zNO9IuosPwf1qzeJrTC94xr24Yi8ujTg6Ph7LSC2i4a3lUcBELdpgxJSGotdUr5JsebdtxWvS8v2DbJpttkxurEqjdvI8KAh9z-Ox-Wgvh1H13ekvwjOMMfndDZ0v5xslNlrz', '1467491010', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-02 12:24:39', '1', '67', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('130', 'Eshan', 'asdqwieoqwe123132', '', '1467316972', '0', '0', '0', '1', '0.00', '113.66.109.114', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7PMrzl2ia59Ppa5PCh1UGdnB367OeClKzOXHVGPnBRafdiaY3oWfaKrod5ZzClUCeu1rWKCB2SyepHFPjAQ18xib1E/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq8a0BYT6tg1ENjaZYuKemgplVGbpWOcgxv*u4hKb*Pydm3PuhwUAsPP08YoVRXuUmupBcRvcABval3*olCgp09Tpyn-IeyU6TlmleTci9giG0IyIkkstKnEO*AQHhh7Kho4NIyL3*xYShIkZEdsRl9FmlqzDIoQPO3mhljHxwmGVsTxxn063YZDsI0Z6mVWbYXq3ixZsm9ST*9e*nuR41UapUzSzen6asuNLjuZpLAOFYlnvF3XWrJ97s1KLN34edO07ruf7jqHvvDuIVv7*C5GHECI-u23r0-oCkPNbxA__', '1467403373', '0', '', '', '1', '2016-07-04 15:39:27', '2016-07-04 15:42:08', '1', '437', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('131', 'MESSIC', 'asdqwieoqwe123132', '', '1467329681', '0', '0', '0', '1', '0.00', '123.135.8.33', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIcrODyMht0rv1m6Q0HB9iaIYC3icMZhMUdUEdrxMLCpNQFbPlVleajiblb6joO1Y1jCgpNEndhLjFux/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDtTEtX6O7gw2XiZjhyJJ5Q4CWrRho0xbdNPvvKltiE6*f8*ac98sCANjF0-a*aho*DrrUZ0FtMAc2tO-*UAhGykqXriT-kJ4Ek7SsWk3lhI6PHQjNCCN00Kxlt8AMe6Yq8lZODRMi7*cWYuRgM8IOE2bJfrHOl5-p8jGtV8*RjArR0W0XnKhOXvp6k6mQ8CjpnfYjF5l8iNbHKEO1VruKek6qYtiex2C1aTDPg7jj*y6GbNeNB1y8wsXRqNSsp7dBwcz1Qy90DX2nUjE*XP*FyEcI4d-dtnWxvgHtjFxJ', '1467416083', '0', '', '', '1', '2016-07-02 15:38:36', '2016-07-02 15:38:52', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('132', '何大双', 'asdqwieoqwe123132', '', '1467330918', '0', '0', '0', '1', '0.00', '222.209.11.42', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmdUwJMhSvc7uV5icRfBfwDCYycqib5urqAXBbU1RPbPPrDHZ8Sqcx4bFoEaqC75UwvNnib9Hz9eZ2wSM721EzX6eC/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0tPwkAURvf9FU23Ndo7fWXcQUMCvqZYQOumGTpTmGIfdAYsMf53pZI4ietzbr5zPw3TNK3FQ3JN87w51CpTp5Zb5q1pOdbVH2xbwTKqMrdj-yDvW9HxjBaKdwNEPkaOoyuC8VqJQlyEEHtIo5LtsmFhgOD93DoYENYVsRng4ySNZvOocPMxJbSFw6s89nFVPn-Qm0XqTScrRF6at3y0jHdBlZT1ZrYdxS4QPt7KE0QN3qf9fMWUTaS9v2PvyRNa30-l2i7DQHKiTSpR8UtQELo*DkEPOvJOiqb*-dcBHwDwudsyvoxv8VZcUA__', '1467417319', '0', '', '', '1', '2016-07-01 15:55:20', '2016-07-01 16:22:20', '1', '141', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('133', '老米丨古典书城', 'asdqwieoqwe123132', '', '1467336435', '0', '0', '0', '1', '0.00', '223.104.38.150', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM64l0MyKOqbeJ1FoicgbiapUz3ynUBSme9CQt4NZo8Bfj7N4D13LGKicbdGDnVxGRaQJBJBUgCa7OPww/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FYTXGdcyOlYTH4gSBtJtkWGYLw1Zy3ZFoZbqthj-u4pLJPH5nJvv3A-Ltm1nnWaX5XbbvjWGm5OSjn1lO8i5*INKgeCl4RMt-kF5VKAlLysjdQ9dQl2EhgoI2Rio4Cz41CMD2oma9ws9xN73LaLYpUMFdj1kYX4Th10COB8fiJjt46zAWjD1kNz5RUniWZGuNr6ck3AZ7b0ygDBAC8o24xqelrpbsyiv8tW8zU5qMaqTw*POC56j4*v9LRul7fVg0sCLPAdN-cmUuP6w*V3qDtrm91*ECcaY-nQ71qf1BSeiWtA_', '1467422874', '0', '', '', '1', '2016-07-01 21:31:37', '2016-07-01 21:39:53', '1', '34', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('134', '王炎生', 'asdqwieoqwe123132', '', '1467345941', '0', '0', '0', '1', '0.00', '223.215.62.188', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NQT5Zua3S9GdOJCmOjxcOu2NNKFb9ICu56jMxEvvhv9NzFvuJvIhmY9Crw2KlWHLHqHuKv93EIkflUqPdvdDXH/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0FPgzAYBuA7v4JwndG2o3Y12WFdQDtYlgke2KVB2pluWBjUyWb87youkcTz83553*-DcV3XS*PkOi*K6s1YYU*18tw71wPe1R-WtZYit2LcyH*oulo3SuRbq5oeEaYIgGFES2Ws3upLgFBMBtrKvegbeoT*9y2gENFhRL-0uAzWc86esmjRpbsHzicMH028ADfpCB-2aH7m4QoXWZyZKCjbUTnTjPH72WENkk2yWdadRGEU754Zo5QHk-IsYRhV*PRYJGH*Pp0OKq1*VZdBt2RMfOKjgR5V0*rK-P4LIIYQ0p-dnvPpfAH7LFpZ', '1467432342', '0', '', '', '1', '2016-07-04 14:27:38', '2016-07-04 14:27:42', '1', '30', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('135', '오빠', 'asdqwieoqwe123132', '', '1467346069', '1468000235', '0', '0', '1', '0.00', '117.92.68.186', '', '', '1', '0', '-28800', '0', '0', 'errrrrrrrrrr4', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxyaYNsZDvI63dmjPYor5rl9GcAR7kf0U9arCtUBrZAD8Fj99BNL7ekfVEIc1ZGD3iceltIPdCoia2icA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTZ*Pa0tJhsgecc2IQwySb2wsBWrZuAZrS6Rbjf1dxiSQ*f*fmnPvhAABgGr1c52XZHhub2bOWENwAiODVH2qtRJbbzDXiH8qTVkZmeWWl6ZEwnyA0jCghG6sqdQlwn40H2olD1jf0iOn3LfIx8YcRte3xaZZMw*ljoVf8UJi4XWp3V5uupOEySqLbZEUZ3VRi9KDP7jElIlBB1YSv9yPvjqXePK5puFhUakaeyX4foNN6G843iRcXebDevU8mg0qrankZ5HGX0zF3B-omTafa5vdfhBnG2P-ZDZ1P5wtYLFrZ', '1467432473', '0', '', '', '1', '2016-07-12 20:17:42', '2016-07-12 20:24:57', '1', '4163', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('136', '勇者无惧', 'asdqwieoqwe123132', '', '1467394633', '0', '0', '0', '1', '0.00', '218.17.34.50', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7PibWZOAOAQKL5VQgNbRmQeghTLx9zIRMSiaPW6q2ZSFq0o7xFK1hcANmSIf6ibnWpTCxLTQRuACQ2EZf2UQfbVCYT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PgXhVWNaKH9qsgc3N0WYATeU7KUh0OLNHGCp29D43VVcYhOff*fmnPthmKZprePVRVGW7VujmBo6bpmXpoWs8z-sOqhYoZgjq3-Ijx1IzgqhuBzRdqmNkB6BijcKBJwCPvV07astGxtGxOT7FlFsUz0C9YjLeToLZy8E6lc3Pm6rZAP7XSlkCsEQ9JsMx3cCnvto4fliGT7Nr2D68H7Iorx4jEAgO0nWC8mv09Chtzf301zlw1mQtaskIU1XTyZapYIdPw3yfGI7xHE13XPZQ9v8-ouwizGmP7st49P4AuO4W*w_', '1467481035', '0', '', '', '1', '2016-07-07 15:46:31', '2016-07-07 15:47:15', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('137', '陈冰', 'asdqwieoqwe123132', '', '1467398955', '1467752711', '0', '0', '1', '0.00', '112.224.20.129', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDcLHAjp7CfwichZct2CEzxIViamwp8q8vicDzVrgbA90BeJHVQbiaTy3LRHwCW7RcdPVsb4wHCBoHefA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz81Og0AUBeA9T0HYauxc-mRMXBjEhlhqlBKKGwLMhU5rKZ1O7TTGd2-FJpK4-s7NOfdL03XdmE3im6KqNvtW5vLYoaHf6QYxrv*w6zjLC5lbgv1DVB0XmBe1RNGj6VCTkGGEM2wlr-klcEtdGOiOrfK*oUewz7eEgkmHEd70GAWJH776b2HljAm*PL6rMZtNFbqLZbkFwp09ZHEarJ5Ci6JSPjmEDdLREmqRxtE88csHL40CZS*SbJ41z9PSmhw*tuujtK9GLLgfVEq*xssg1wOLOJ430E8UO75pf-8l4AAA-dltaN-aCWYfWz8_', '1468188188', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 11:41:22', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('138', 'Franz', 'asdqwieoqwe123132', '', '1467413795', '0', '0', '0', '1', '0.00', '101.128.78.211', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxzZdn4LZZ2swuOicIaY6RCKJP1ibSX3d4ADgnMZT2vDgLCMyXib8NtSqtDjhz5IuN6zlucSPvZZAjicG4KfCF2u7gqD/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj81ugkAYRfc8BWFL08zwI45JN1RSobaWYHU5oc4An1Igw2AV03cvRZNO0vU5N-fei6brurFeJvfpbld3laTy3HBDn*kGMu7*YNMAo6mktmD-ID81IDhNM8nFCC2XWAipCjBeScjgJnhkYiu0ZQc6NowQO0MWEWwRVYF8hC-B*2MYzxsZO2*lmQA3c2jjJ1*Q*sBL4lt2GO0XfdIXH1N-23fHrzDny42JNimzV9VrEYDIPXEugudocdqLFrLVugu25TxzI2f6oFRK*OS3QRPPcYZTnkKPfMjW1fUvwi7GmPzuNrRv7QcQc1yr', '1467500197', '0', '', '', '1', '2016-07-05 14:44:44', '2016-07-05 14:45:23', '1', '18', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('139', '阿诚|占罗成', 'asdqwieoqwe123132', '', '1467571056', '0', '0', '0', '1', '0.00', '211.162.222.193', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5dsE1Jk0KkJLJPwqpahicyia1WfU7uE4qqicic9x7VDHhL0A2UCBjbgGNJC839fygVLahGJwZIrQgq8wQFVmuVBrkY3EISZCmuZRg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj0FPgzAAhe-8ioYrRmlZqTXxgnioTqMbc7JLw2jpukWopSrT*N9VXGITz9-38t77CAAAYTGdH1d13b20jru9kSE4A2EcHv1BY7TgleOJFf*gHIy2kleNk3aECFMUx76ihWydbvRBIDTFHu3Fjo8NI4ST72xMIaK*otUIby4XF*w*N1lucH*SvrcZvCIP22i1nJgkZ4-PZZ1t9ltr1m-yuqTDUjEltG1uawXbTDlE3I6iQs2mlsyiTVSuO0RWdxVLCtYPi3Ov0ukneRiUEkzpaeo-epW21137*zeGGEJIf3aHwWfwBbpeW*k_', '1467657460', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-04 21:45:40', '1', '131', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('140', '启明子', 'asdqwieoqwe123132', '', '1467584402', '0', '0', '0', '1', '0.00', '117.29.46.212', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0wicIicr2qhicibSribzNUrWmDuvgIpVvJIqA2Uj0xYJclO1WWAIPmhz9vhz9FoDoiaVLMr2PdysTqNvok/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTV420lBXr27LhxmSJAk7ZCyFrwYpS0nZzYvzvKi6RxOfv3JxzPxwAAMzi9KLc7dS*tYV97wQEVwAieP6HXSd5UdqCaP4PxbGTWhRlZYUe0JswD6FxRHLRWlnJUyBg9HKkhjfF0DAg9r9vEcMeG0dkPeA6zGfR3Tx9fM4O*0gfAzs1bp6EHhHlfdpXm-WqboKZND2N*yzO6Vv0NI0ewg0nLxW-Wah0K9z5UvnM1Eloz7DbLW8XjbpeaUOS7bjSyldxGkQDiomH-JEehDZStb--IjzBGLOf3dD5dL4A0kBb2A__', '1467670804', '0', '', '', '1', '2016-07-11 17:18:34', '2016-07-11 17:19:09', '1', '25', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('141', '٩( ᐛ )و', 'asdqwieoqwe123132', '', '1467591659', '0', '0', '0', '1', '0.00', '180.130.10.48', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/NdiccZl8Jk96INUdefHdoK5OAYSHxgRWAe93SymufKKxdOjT8lvJ3WkrtFypjCYwibB3K9a7fO4TiaIKosZmpVwjaUF6ibsuom3T/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PwjAYRu-3K5ZdG2nHvuqdAcbQGWkGQ3fT1LWbzWAtWxGM8b8Lk8QmXp-z5jnvl2XbtrNKs1talvLQaqI-FXfsO9sBzs0fVEowQjUZd*wf5CclOk5opXk3QNdHLgCmIhhvtajEVQhRaNKeNWRYGCD0zrcAQReZiqgH*DR7nSzwlGb1YTT1Z1Hf42O6lfK0ZnFTbbCbRDimj1IU40CirFR48X7--DbK2T7ZzqPmmOyilzZtHqpNVfihqierHK7n*dKL*2WxT41JLXb8GhSEgQu8wGz*4F0vZPv7L4A*hBBduh3r2-oBy35b8g__', '1467678060', '1', '', '', '1', '2016-07-07 20:49:41', '2016-07-04 16:22:08', '0', '22', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('142', 'Hy', 'asdqwieoqwe123132', '', '1467593975', '0', '0', '0', '1', '0.00', '223.21.37.210', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgAick9og4gc5u6kVmNvWeTrmQaxEicrbDNdsnKYjKrShCVNPnR1ibS0pqPmJTh6PWQnibXHFXFdCW5y3G/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXh2QjtoLV7c4MliwVlc06fmoYWVkUgtCqd8b*rbIkkPn-n5pz76biu693T7SUvivatMczYTnru3PUC7*IPu04Jxg2b9eIfyqFTvWS8NLIfEUYEBsE0ooRsjCrVOYAJjiaqxQsbG0YE4c9tQAAk04iqRkyTp*U6j31*mxO-ONAoLKtVDxC9g0mshxpVmSV7M4A2var3z5x*rA-XabYrhb6xGZ3lNT-GbajzHaL*w2aZbK1uHlcbnmnr28ViUmnUqzwPQhhBiDGe6LvstWqb078BiAAA5He353w5340OW8c_', '1467680377', '0', '', '', '1', '2016-07-04 16:59:38', '2016-07-04 17:16:18', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('143', 'mike', 'asdqwieoqwe123132', '', '1467653447', '0', '0', '0', '1', '0.00', '114.242.249.127', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLAt9iaUxic6jmunGIl0vJDjGyD0I0aeduRZuEaibTGaP0lwJMXuAUyryFOc32dgeibkySmJPYicHCuWFdQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19PgzAUR9-5FE1fZ7Qtf0pN9oAG4oiymanRJ4K0Y3fTgqU6pvG7q7jEJj6fc-M798NDCOGby*VxVdftq7al3XcKo1OECT76g10Hsqxs6Rv5D6qhA6PKamWVGSELBSPEVUAqbWEFB4ELHjm0l9tyXBghDb5viaBMuAo0I7xKr89nZyF-SndDlolNGtaT96rg2UbD1rAXSPaPsWlvC6H7vKh3CSTzk5g*3NMizrNhgLwhdxJ4rNbLRaf1bOGvg8nc7y9IkDTTqTNp4VkdgiIexYwFbtCbMj20*vdfQkNKqfjpxt6n9wVpp1r3', '1467739849', '0', '', '', '1', '2016-07-05 16:34:55', '2016-07-05 16:51:35', '1', '6796', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('144', '梦醒', 'asdqwieoqwe123132', '', '1467676431', '0', '0', '0', '1', '0.00', '183.69.210.39', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0icliaA5rkcduouHGko4gUSvqLicNUOZll8JJo23syzJDMHibOfeRMYexy3eiccDXxjOJl2IRrovY6iaHO/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj19PgzAUR9-5FIRXjGvLv9bEhzJ5mKnTDZ28EQJFbsxoB4U5jd9dxSWS*HzOze-cD8u2bedRpJdFWaqhNbk5aenYV7aDnIs-qDVUeWFyr6v*QfmmoZN5URvZTZAEjCA0V6CSrYEazkLEKJnRvnrNp4UJYv-7FjFM2FyBlwneJU-LVTJkAeMpb1x-vUT3xy01N*XI3cgTgduvI-G*i7MdMCU3HBLuUjglBzlmgxdv9SjakHZKKLg9LGDTPDzvm1Wdxt4RLfzr2aSBvTwHhRGlKCTz5lF2Paj291*EA4wx**l2rE-rCwLBWpA_', '1467938222', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 16:57:23', '1', '127', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('145', 'jiedon', 'asdqwieoqwe123132', '', '1467676866', '0', '0', '0', '1', '0.00', '222.76.175.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIYT40ou3Kft4V3ticSaqamaz0yn57aJf29XNC0qFqA1tYghYWvH1a9k49RjZllRgZ7kia3dUhq2GA8/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTZ6Mts3Q12YMMZTCcIy46fWmAdqNZgAplbDP*dxWXSOLzd27OuR8WAACuoqfLJMuqtjTcHLWE4AZABC-*UGsleGL4qBb-UB60qiVPNkbWPdqE2QgNI0rI0qiNOgcoG48G2ogd7xt6xNfft4hhmw0jatvjw108DaaLKkoOafhOvU6g*HUcPr8FDfY6vy2uZvPYW5Kl8IMQ5fWtcts2m*NCEBSKdbeOXHe-SvVjKsj9yXs5MXxkdu7nNJvttpPJoNKoQp4HOZQi4jh0oHtZN6oqf-9FmGCM2c9uaH1aX35YW1s_', '1467763267', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-09 16:39:04', '1', '201', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('146', '牛猫', 'asdqwieoqwe123132', '', '1467676955', '0', '0', '0', '1', '0.00', '117.136.11.166', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeejvuwnlWDtFjGtRrFXa5dLqZAlT0mwxxNpSzjfevhEhphlUibNNwQbiaRlnVACuZ3juqUlBRQS4QqYw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDrUZbpNaaeMGcy9gXEMBFbwijBeq2Flk3h8b-ruISm3j9nDfnvB8WAMBOZvFFXhRqL3Wmu4bb4BbY0D7-w6YRLMt1dtWyf8iPjWh5lpeatz06mDoQmhHBuNSiFKcAoTeuoTu2zvqGHpH7fQspcqgZEVWP84f03o*G7r4bTqIwd4tUQSToyzRRkxFLynCwSuH741Yu5CGYP-vxm19FarMedMEiGq*84EzJ2TTwIlyPlpfhEvnjY528bjw3rpr66c6o1GLLT4OuCYGYYGLogbc7oeTvvxBhhBD92W1bn9YXvCdb7Q__', '1467763357', '0', '', '', '1', '2016-07-09 13:10:44', '2016-07-09 13:11:05', '1', '3508', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('147', '翼show', 'asdqwieoqwe123132', '', '1467677058', '0', '0', '0', '1', '0.00', '222.76.174.6', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM44c5UyYL8kfuWQWgMjrLA2uAm25wKxE7TvdWOIxdWGcxDicuXbicPmficI1dtj7icPTOI6VO6EYzibVHQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj81OhDAURvc8RdO10ZaxMpjMomMGFZyoDBhYEQoFq-yWQpgY313FSWzi*pyb79wPAwAAg4fDeZpl7dioRB07DsE1gAie-cGuE3mSqmQl83*Qz52QPEkLxeUCTWKbCOmKyHmjRCFOgmWviUaH-D1ZFhaIL79vkY1NW1dEucD97vnm3hnjac7C9s4f4qgs3VUl3xqG2XyhCv9QM4fN-RDT-faFUEH5KKk3pS5-9Sfv1gl3ymPHx3kbiWjogz5MKzd-qtw6oHSz0SaVqPkp6MqyEFkTPWjichBt8-svwgRjbP90Q*PT*ALGKl4G', '1467763459', '0', '', '', '1', '2016-07-05 16:25:34', '2016-07-05 16:26:25', '1', '66', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('148', 'jhombo', 'asdqwieoqwe123132', '', '1467677134', '0', '0', '0', '1', '0.00', '222.76.175.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDwx9ZvmOBzZUIhGuE6kupntWsEv9u8cNKibsDOmPpMJz2M1IZJa7vQurbVicFSpxD3RCPfIv7pkKkw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fgXpLca0MD5q4g1MlwXIUFxEb0iFUiuxlNIRpvG-q2yJJF4-580576dhmiZ4SPJLUlXdQehSHyUF5pUJILj4Qyl5XRJdOqr*h3SSXNGSNJqqGW0X2xAuI7ymQvOGnwM*DryFDnVbzg0zotXPLcTIxssIZzOmN-toexcV4rHSNO1XqJnWSeFbT4jEnZTWugiIK54PmfeqjuJW2GzL4k02qrzqQ*sl3k0qCclOjk7TDs59yJjs081HnsXRG2yD60Wl5u-0PMjzfehix13oSNXAO3H6FyIXIYR-dwPjy-gG*cRceg__', '1467763535', '0', '', '', '1', '2016-07-07 08:53:50', '2016-07-07 08:55:02', '0', '658', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('149', '陈超', 'asdqwieoqwe123132', '', '1467680543', '0', '0', '0', '1', '0.00', '1.95.221.230', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgAicjeD0HaoEzLIOhTtsd54BWWTpPBfX4jcolbQib7555MbuS7CwVYCGWmzIOH9Sa0POjrB4q2iaew5ia/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz81Og0AUBeA9T0HYYnQuf*O4K7VNa4pToAvbDaEzgx1pYYSplhrfXcUmkrj*zs0598MwTdNaLdLrnLH6WOlMd0pY5p1pIevqD5WSPMt15jb8H4qTko3I8kKLpkfHJw5Cw4jkotKykJcAJrd4oC0vs76hR-C*bxEBhwwj8rnHaLIez*NxOF1s8Sh0WXRe8Ul507J3eLU3-pOOuySZyofyHrrZjNplPN*NoqRlL8c8DLxljmiy29ppSCE9w4EHalmvvQ3sKWXF-nG4SsuDuAwKMEbE9fyBvommlXX1*y8CHwDIz27L*DS*AEzcWv8_', '1467766945', '0', '', '', '1', '2016-07-05 17:08:30', '2016-07-05 17:08:33', '0', '90', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('150', 'NULL', 'asdqwieoqwe123132', '', '1467681675', '0', '0', '0', '1', '0.00', '182.148.59.75', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxxVaP8gq7JN97Iwe1kZNskd3EQVZpF0HK8TLlTWVvwxRiaF2NjaEeg8aS5On3KS2U8Gx8U3KzMQQL9iaOiaZ7m8Y6b/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11rgzAYBeB7f4V4uzESZ4wWeqFTpmMd84NRvQm2SdsoRtFYK2P-fZsrVNj1c17OeT8VVVW19DV5KPb7ZhCSyKllmrpSNaDd37BtOSWFJI8d-Yfs0vKOkeIgWTejjmwdgGWEUyYkP-BrANuWtdCeVmRumBEaP7fAhrq9jPDjjBs-egrdcMzKdHfxMgef6Ht83oDtR90aCIkcvcTM6nMZTG4TuM8Od8dyN9Sm4F4e4iS*2x7B5Ptx9oajqnLweBJDkJR96pUoWq8XlZLX7DrIxBgCA5sLPbOu5434*xdABCG0f3drypfyDcQtW*g_', '1467768076', '0', '', '', '1', '2016-07-05 18:06:58', '2016-07-05 18:08:03', '1', '506', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('151', 'Candy', 'asdqwieoqwe123132', '', '1467758720', '0', '0', '0', '1', '0.00', '117.25.62.58', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDyxxMV7IbCYDVK9M70Y5ZDm47YfmR1oLChuZ7jfaxzk8OVeT2cyqibOMLprpY9sO3EcuyOnrgY4lw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTZyNtEVhNfNgIi0w3ZcLMfGkQutohrJYOp8b-ruISm-j8nZtz7ocDAIDZ9d1pUZa7fWuYeVMcgnMAETz5Q6VkxQrDPF39Q35QUnNWbAzXAxKfEoTsiKx4a*RGHgMhpZ6lXVWzoWFAfPZ9iygm1I5IMeA8zqMkjSYyLh-l4tAoTuJguV0lur8P3N5k792zq-A6F330UpJxOk7EJM6mCK1fp6utEZfIc2fzZXMr6sXVnkbpQ3Az456v8idUjy6sSiMbfhwUhOEo9AmxtOe6k7v291*EfYwx-dkNnU-nC5xIW08_', '1467845122', '0', '', '', '1', '2016-07-07 15:47:23', '2016-07-07 16:04:03', '1', '212', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('152', 'L=_=W=_=J', 'asdqwieoqwe123132', '', '1467761358', '0', '0', '0', '1', '0.00', '110.180.26.138', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgAibxVia8EUGLaf36OCqJictPiaXKcHvNbebqPZ8eAEkwKyLNGNN0OJvbS3mT73nhTaorgTSkSiaAfX6Rn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PwjAUBfD3fYqlrxppJ6yUxAccCvNfNkcVn5ZlvZ0dYZ2lDMH43cVJwhKff*fmnPvluK6L5g-JRZbnelPZ1O5qQO7IRRidn7CulUgzm14a8Q-hs1YG0kxaMC16A*Zh3I0oAZVVUh0DlLF*R9dimbYNLZL*4RYz4rFuRBUtPt7wIIwnURLei2mPxEGPv0Qw3j5R6Yvn4Tzk-kIEtzK-i-bvO96cFWEBExNvtZazxb4e8xIInhG6*mgagDgul9cbPXxNonLK8rerTqVVKzgO8illmAy6gxowa6Wrv38PRghhv7uR8*38AJ0qXAA_', '1467847759', '0', '', '', '1', '2016-07-10 21:48:10', '2016-07-10 22:04:50', '1', '93', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('153', 'VS', 'asdqwieoqwe123132', '', '1467780428', '0', '0', '0', '1', '0.00', '222.35.224.208', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6RxyQ4Jc4pNNhz12wbWlmnwibrn5VHccIJapicUOgtkx4uuRNuUPzwaGOXwbemCHu7Y0LBR6u3icegicZUp8ezAiak7jqS/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj8tOg0AARfd8BZmtxs6jLYyJizK2FIuGWjGkmwmPoZmqgNOhSE3-vYpNxLg*5*be*2mYpgme-NVVnKZlXWiu20oA89oEEFz*wqqSGY81Jyr7B8VHJZXgca6F6iAeUQxhX5GZKLTM5VmwKKU9usteeNfQQTT8ykKK8B9Fbjp4Pw2Zt2SJncqg8e1lm-k42LrjhtXJagbRILzwSDmrcRsS97B*YBNvc7sgcxwN7rYsCsImeR*q18O0mecLx52wR8Qqy3le1zF1IvumV6nlmzgPGls2pJj0H*2F2smy*PkL0QghRL93A*NonAAGQFo6', '1467866830', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 20:46:59', '0', '276', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('154', '雅丽', 'asdqwieoqwe123132', '', '1467824690', '0', '0', '0', '1', '0.00', '222.76.175.81', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBAKJ5uWCxDRK72fibnyxm1tJibZc1aBId189ZibLyvibnicGYzHJTFz53Bk3kFjfsicPb4djkIsJbCeJMe/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz19PgzAUBfB3PkXDq0ZuKZ3UtwWWZQIqzmHmC*lo0WaxYOn*afzum7hEEp9-5*ac**UghNyndH7Fq6rZaFvaQytddINccC--sG2VKLktiRH-UO5bZWTJaytNjz5lPsAwooTUVtXqHAgByEA7sS77hh5xcLoFhn02jKjXHrPJMprl8SNvbL1RJH**mJv4NsmKdZgED7vJbtp5bVRsdSryjC5WMJ69je8J8Mh6nng5AFtUPFVeEq9g9LEPLVtOm887WQjKItB8UGnVuzwPGl2HlAQMD3QrTaca-fsvYIoxZj*7XefbOQJRFFrp', '1467911091', '0', '', '', '1', '2016-07-07 20:11:40', '2016-07-07 20:11:52', '0', '806', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('155', '方舟智联', 'asdqwieoqwe123132', '', '1467829290', '0', '0', '0', '1', '0.00', '111.196.83.94', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIV4g7YdBic7FnrA4iczaouhskFIVAPhxVpQRtUdSWnXiaRRicV1bCRqU3ouHJVPZX50Pr9XmicYqIcJxo/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXpa422jA5q4gMgUTNJxDpjfGmQFlLdOgaFDZb9dycukcTn79yccw*Wbdvg5ZFdZnm*abXhpq8ksK9tgMDFH1aVEjwzfFaLfyj3laolzwoj6xEdQh2EphElpDaqUOeAjxCZaCO**NgwInZPt4hih04jqhwxiZfRw233Afv11W5epeY5HcI6yHuahOIODrMVTU356r*5An7ClgQqDrYwg3qRLHbv97p7Ik7UM88t9NBu5SpkQWTiPWEMN54qbyaVRq3ledDc84l-mjTRTtaN2ujffxEmGGP6sxtYR*sbmrBbow__', '1467915691', '0', '', '', '1', '2016-07-07 20:05:48', '2016-07-07 20:09:29', '1', '5', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('156', '森飞科技韩亚斌', 'asdqwieoqwe123132', '', '1467847562', '0', '0', '0', '1', '0.00', '60.221.133.179', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzkgWCs0ZayXZ7cm4WFqH8OOyKp5ANsvkY9pmnqsxzWVLicJ89tMBZSic42I9rSzQw4gcIlzcb4ZwcWoLLUtpiaBlJl/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz8FOhDAUBdA9X9F0bbQtMLQmLnTESCIqCBl0QxgopENoC3QmovHfVZxEjOtzX*597xYAACZ3T6dFWaq9NLmZNIfgHEAET35Ra1HlhcntofqH-FWLgedFbfgwI3EZQWgZERWXRtTiGKAIsYWOVZvPDTNi5*sWMUz*REQzY*in6yC69mIaJnSayv7xJcriGych62TzHN6vcOr6abyL5E45bDNmTdDEwfbK9-ssVN1Wt61feNFbf6g73BF5WfBbO0Bi-9CehSq4WFQa0fHjoJXHqEeRvdADH0ah5M*-CLsYY-a9G1of1id6bVua', '1468045403', '0', '', '', '1', '2016-07-09 22:45:47', '2016-07-09 22:55:01', '0', '12', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('157', '困倦', 'asdqwieoqwe123132', '', '1467847568', '0', '0', '0', '1', '0.00', '183.37.226.119', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0ickqvtC7lNxicsiawfGVKnWWq9evdesCLI2DdHTZUbHKjc4x9gkyt1ddLCd5RY21uASoY0FJg7a8I9/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('158', '夜烨', 'asdqwieoqwe123132', '', '1467850318', '0', '0', '0', '1', '0.00', '124.160.217.224', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzkXPMN3XOKgrhN6qdWSxniaMlQ0n9hlPhDfNiaF3Nh3nFCslG3CsicArRACJ2pt6W9HnKSHhmauwAJrG3tLYGOY8XK/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11vgjAARd-5FQ2vLqPtdICJD8ywuQ8JinPBl4bRMiobraUOddl-nzKTNdnzOTf33i8LAGAvnpLLLM-FttZE7yWzwRDY0L74g1JySjJNrhT9B9lOcsVIVmimOogHPobQVDhlteYFPwseRMigDa1I19BB1D9moY*wbyr8rYPTcDa*D*cPi*rwTh9f035epIKGMmKJdsbL3uR2Mp9Gy7p08M4J4k3AA69d4Vb35N368BKtC3f-rHhZuXHZiE3YpqJMk1WcVDN1E4xGRqXmH*w86Nr13OMg89EnUw0X9e9fiAYIIf*027a*rR8CeVy0', '1467936720', '0', '', '', '1', '2016-07-11 10:11:53', '2016-07-11 10:11:53', '1', '16', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('159', '伧子', 'asdqwieoqwe123132', '', '1467853211', '0', '0', '0', '1', '0.00', '116.25.239.77', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxzk4nErTA2xZvynkSPgPlk0RZickb6AVJYbFNzYS6ibBbggBkrtaTlkaozZzPa90DNAsSnBf7h5UCDsNEKzicANTgv/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FvgjAUBeB3fgXp65atLeKoyZ4QM*aIMDXBvjTMVmxwhZQqU7P-PsdM1sTn79ycc8*O67pg8TZ-KNbreq8MM8dGAHfkAgju-7FpJGeFYZ7mNyi*GqkFKzZG6B6xTzCEdkRyoYzcyGsggAhb2vKK9Q09osHlFhKEiR2RZY9JtAzjLNymKlTq9TQ*zjF9yXAYDRezNp3wldDdY7FLKrQrl5lPvC4ueUEG4zjff2x9Wt5Rmr570zCLpp08VJNclHSVpHl9mmUxfLYqjfwU10HDpyDAEPmWHoRuZa3*-r0QQoj87gbOt-MDl9pboQ__', '1467939615', '0', '', '', '1', '2016-07-07 17:03:31', '2016-07-07 17:04:57', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('160', '冬天', 'asdqwieoqwe123132', '', '1467875691', '0', '0', '0', '1', '0.00', '115.25.208.53', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA2WgITkWE4vXNRyCahwul7gcnicPQSa3fBrW3Snw1m6sBXOx8F5h7u8vATzw2rHSXaTFpG2ibQEMyF/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0trg0AUBeC9v2Jwm1JmfNUJZCE2D6lJUSOk3QwmjnJTo6OOTST0vze1gQpdf*dyzr0qCCF160ePyeFQdaVkshdcRVOkYvXhD4WAlCWS6U36D-lFQMNZkkneDKiZVMN4HIGUlxIyuAdsTMyRtukHGxoGJMbtFlOi0XEE8gHX88D1XL4OXs*Tpe3Pn*PlUXcLy-HLuG9ObxcIbZ84he7Xk3LfrxxwLDOqg6worJ0nYBHX7xH3upeNCKtw1xX7qF3lG5If6aI*z2ajSgknfh9kPVFsGFQf6SdvWqjK339v7xBC6M9uVflSvgGezFwC', '1467962093', '0', '', '', '1', '2016-07-09 07:07:05', '2016-07-09 07:12:15', '1', '50', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('161', '丁凯', 'asdqwieoqwe123132', '', '1467914321', '0', '0', '1', '1', '0.00', '117.25.59.178', '福建', '南平', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/CBA231810675A5C63E8CA9EA7EBD323E/100', 'http://q.qlogo.cn/qqapp/1105454078/CBA231810675A5C63E8CA9EA7EBD323E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq0bbDjZq4ouEuYVVM5Rp9tIglK4RSi1lmzH*dxWX2MTn79yccz88AID-uHq4KMqyG5Rl9l1zH1wBH-rnf6i1rFhh2cRU-5AftTScFbXlZkQcEgyhG5EVV1bW8hSIIIoc7atXNjaMiILvW0gQJm5EihFpksfLdZxvL9fzrXgOy9ssbeSieYqy2bFdxQWnSbfL7weRdsmuhsFhKRbzQeebjEZvhG7abMD07kwmvUJ5R8P*5pAqbfRUvOyb5NqptLLlp0HTGQkmCGNH99z0slO--0IUIoTIz27f*-S*AM4oXEw_', '1468000722', '1', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 21:13:16', '1', '14504', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('162', '科林诗尔李祥利TEL18063396287', 'asdqwieoqwe123132', '', '1467918500', '0', '0', '0', '1', '0.00', '58.58.183.144', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzkXPMN3XOKgrmPCj7pRSwPr4tyZhFmxxW7MvTfRF1T9KquOO28MFLET0p3LOJm7OetW3KrxgCGABT50qdkppUY7/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PwjAYBeD7-Yqmtxhpx2CrdwaYmWPqEAPzptnasnWGfZQOGMb-rk4SZ7x*zptz3ncDAABXi*frmLGyKTTVbSUguAEQwatfrCrJaazpSPF-KE6VVILGWy1Uh*aYmAj1I5KLQsutvAQchElP9-yNdg0dYuvrFhFs-onItMNgHk29cJZZYd2sau4lT1Z*VsPMNdfpYJmf8lfHX9gbLw3b6STL7zehl90G2q9d3ur1*aV0ooglbHD3WCc75g*Tw3LeoMBtH3h7nDHZq9RyJy6DJjax7BHCPT0ItZdl8fMvwmOMMfneDY0P4xMnHF0F', '1468004901', '0', '', '', '1', '2016-07-08 11:08:22', '2016-07-08 11:11:16', '1', '105', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('163', '千秀安卓技术-zj', 'asdqwieoqwe123132', '', '1467921654', '0', '0', '1', '1', '0.00', '218.5.47.167', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/5D326327E50C5827DE7FA00B88723267/100', 'http://q.qlogo.cn/qqapp/1105454078/5D326327E50C5827DE7FA00B88723267/40', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FQ2vGm0LSGuyB8YadUEXxKnbCymlsGYZ67pCWIz-XcUlkvh8zs137ocDAHBfkuyKC7FvG5vbk5YuuAUudC--oNaqzLnNPVP*g7LXysicV1aaAeKAYgjHiiplY1WlzgKBGI3osdzmw8IAkf99CynCdKyoeoCPLI0fWPKOqtOOd5GMCY2nSyFm4Wsci9WGba9xkbFOc1bT*V0RqSlGbXGwazF-C*7XT2nV*qnpo8PyoidsVi8SEj5vMr0yxIsmk9GkVTt5DrohkIS*N27upDmqffP7L0QBQoj*dLvOp-MFPtxazA__', '1468145031', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-10 08:52:45', '1', '1781', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('164', '王星', 'asdqwieoqwe123132', '', '1467929049', '0', '0', '0', '1', '0.00', '61.183.89.46', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIUUQex29Us83NAIibE20X3BwptrrkmQDOurLH3LGHMCocEVX7ysHtcQArQnxU2cBHia08zia23cB410/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PgzAYhe-5FYRro-0AR028KEgmbmZzYzHxhuBa5pu5UrsiG8b-ruISm3j9PCfnnA-P9-2gmC7Pq-W6aZUt7VHLwL-yAxSc-UGtQZSVLakR-6A8aDCyrGorzQBJxAhCrgJCKgs1nIQYEerQvdiWQ8MAcfidRQwT5iqwGeB9tkrzRDzrOqnost3Mp203Jt0hTyf5bFXJO6OaXEvDsQhv0jbkkHE6FinbLsTDhYLj0wtDu57TRX-LJ9nb7DGkfUEhVV2RvMbXTqWFnTwNuhyxaBRH7qN3afbQqN*-CEcYY-azO-A*vS*5M1u6', '1468015450', '0', '', '', '1', '2016-07-09 14:36:49', '2016-07-09 14:38:12', '1', '8', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('165', '愤怒的小唧唧', 'asdqwieoqwe123132', '', '1467929863', '0', '0', '1', '1', '0.00', '117.25.59.178', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/014E7A137AC927E40CE7964E8F79A798/100', 'http://q.qlogo.cn/qqapp/1105454078/014E7A137AC927E40CE7964E8F79A798/40', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PgzAYhe-5FU1vZ8zbMiY12QXiCGMsy9hHojeEQVc7sXTQ6dD431VcIonXz3NyzvmwEEJ4Ha*uszyvTsqkptUco1uEAV-9Qa1lkWYmteviH*RnLWueZnvD6w5Sh1GAviILrozcy4vgAh32aFM8p11DB8nwOwuMUNZXpOjgfLLxp0v-pjVidyjzkMIqCoLSjv1isPVYWL-dvSeDKGjmBI7Z7kl4U5FsQlX6obeI4uaxgrxK7HOoT5OovY9nYu0u1aEcLY4PMwfGvUojX-hl0MilYFPWf-TK60ZW6vcvEIcQwn52Y*vT*gJjGVsv', '1468260890', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 20:20:02', '1', '10591', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('166', 'fly', 'asdqwieoqwe123132', '', '1467931356', '0', '0', '0', '1', '0.00', '122.156.219.195', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS0wX5R6ypiblyC3YuzPzyCXwXialvrLbW98TibxHeKIbEn8ZnyM4wAJdwWNooEs7lHx6A3Z5yIpuR0Ty/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fgXhdUZbNhg18UXZyObmZKMj8YUQWqABSi2d2zD*dycusYnP37k5534apmla0Wp3m2ZZe*AqUWdBLfPetIB184dCMJKkKhlL8g-pSTBJkzRXVA5oO8gGQI8wQrliObsGPGB7mnakSoaGAeHkcgsQtJEeYcWA6xl*WoS*C1-BrkKPhxrjZjnn5Sr2*-1ohN-OGZ7UZR4G6K4-bl5UuCiWdXysTvN6*971ovWnAUByJrko*zGJtp3t7J*bmAVeEa0ftErFGnod5E6RC6CjD-qgsmMt--33YhBC9LPbMr6Mb9tvXGQ_', '1468017759', '0', '', '', '1', '2016-07-08 15:00:02', '2016-07-08 15:11:24', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('167', '命运', 'asdqwieoqwe123132', '', '1467935204', '0', '0', '0', '1', '0.00', '218.205.22.6', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeejNgy5wU1aibiahmu4t3iad3d2fXKdvac595thxOL3mTGxWN0OsBoew7v4xIfwthgRVzBFf6PicACUTeg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11PgzAYBeB7fkXDrUZbvgQTL4iCMAdu0xn1hpC2mw0ZbdqKReN-d7IlNvH6OW-Oeb8cAID7OH84azHm771u9CioCy6BC93TPxSCkabVjS-JP6RGMEmbdqOpnNALEw9CO8II7TXbsGMghr6tinTN1DAhCva3MEFeYkfYdsIqW1*X6QKt6xeKn86HbRoXyxNSoC5QGeVv2OC7j9UzicuqviW4T1mW8kgpzxerVH*O*U1Q0VxUhdlh9drdj-nMLOpZabiYyyG*sio129HjoOgiifarQksHKhXj-eFfiEKEUPK723W*nR-VvVyC', '1468021605', '0', '', '', '1', '2016-07-08 23:20:13', '2016-07-08 23:20:32', '1', '79', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('168', 'happy@girl', 'asdqwieoqwe123132', '', '1467938615', '0', '0', '0', '1', '0.00', '121.204.96.238', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIfugnnMwlkCG5SQE3q7pRibVqush76vuYsLfeHZr5GQOQSaluwXq460snfnqsnibwxpBC0iaclZftvY/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz0FPgzAYBuA7v4L0OiMtFLDeNoaOKAnodpiXBkaRqmtrKTpc-O8qLrGJ5*f98r7f0XFdF6xv78*r3U4OwlAzKgbcSxdAcPaHSvGGVoYGuvmH7KC4ZrRqDdMT*iHxIbQjvGHC8JafAhcQI0v75plODRMi-H0LCfKJHeGPE*bpNsnKpH5abUKxHiFCgbxO9D7y3s3rKl92BZEzwsySHxZeWcf1POvmxaJP8z4ORpUOOJkV7d3LUKflVS6zB6-70B7mItvehAIKq9LwPTsNimISxRhFlr4x3XMpfv*FKEQIkZ-dwPl0vgBJHlsQ', '1468025016', '0', '', '', '1', '2016-07-11 20:52:54', '2016-07-11 21:09:34', '1', '3409', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('169', '别问我是谁', 'asdqwieoqwe123132', '', '1468006628', '1468006787', '0', '0', '1', '0.00', '117.92.68.186', '', '', '1', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBO10dLPqgWQLRHFaNqkqmAyEPBVQ8HRJpRDYluFd7ib7GqpEFkQ1CnxShcvkfVBjJDnAGDWu6nia4P/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FQ2vGm1hZWCyhzndQCdkA*bcC2G0nQ3Cuq4Dddl-V3GJTXw*5*Y792gAAMxkGl-lRbE91CpTH4Ka4AaY0Lz8g0JwkuUqsyX5B*m74JJmOVNUdtDCngWhrnBCa8UZPwsuxDrdkzLrFjqIet*30EOWpyt808Gn*9komHwuVT9-4NNwfHHtwyQKZ3ePmyiRwme7xduEsXXkqnkysoIhH7qHeRGVa6dtnvE4TldNuapUWqa0ei1uX8iydXZx3w8DuWgHA21S8YqegxwX2rhn680NlXu*rX--hQgjhLyfbtM4GV-DIFw4', '1468093030', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 18:52:34', '1', '22790', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('170', '甘小川', 'asdqwieoqwe123132', '', '1468029387', '0', '0', '0', '1', '0.00', '211.97.131.197', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJwhic0YuA2QMf9RibT2cicOsKiaEksf153hc20B6wG3JSBC8wicHTw8sGt7BAXgkicvJ1pk7v2VhQKHFyg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj11PgzAYRu-5FaTXxrVMNuqdwYUPB5nDmMwb0rVlq9iCtGNM43934hKbeH3Om*e8n47ruuBpWVwTSpuDMqU5tRy4ty6A4OoPtq1gJTHltGP-IB9a0fGSVIZ3I-R87EFoK4JxZUQlLkIA-alFNavLcWGE6OZ8CzHysK2I3QizxSZMHsPDUq6PH8lmWE1iH6eVoiycRetk6KMg3w96OzeNQXSbZ7tkf5emRx0vHibRq4xO92*rnsxlUWOsYkHUe1hEvdQ1zZ9f*syaNELyS9DsHBygwA7qeadFo37-hchHCOGfbuB8Od87z109', '1468115789', '0', '', '', '1', '2016-07-11 17:11:02', '2016-07-11 17:16:56', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('171', '凌杏水', 'asdqwieoqwe123132', '', '1468033429', '0', '0', '0', '1', '0.00', '218.240.5.108', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS03zjPJNcSPrWyty5Ix90VibQx91eBayUGGvI0NGaUMYH9VUf8ccrvBL1bExl1tko1z8HOqbAx2duI/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq8bc4orUxIe54QIbCOqYPDVktKNBgZQ6x4z-XcUlNvH5Ozfn3A8LIWQ-rR4viu22fWs000PHbXSNbLDP-7DrZMkKzS5V*Q-5oZOKs0JorkZ0CHUAzIgseaOlkKeAB2RiaF-WbGwYEU**b4Fih5oRuRsx8tez4PZIvaqN0jl2g6qO0jCfH-aii3gWpPW6OiPxIguGbLhP8qn0p*1ztNn1arNYJldLCOlL6ghfg4DkIdY0PCb*Kpmpuzj33m*MSi1f*WmQ62EXsEsM3XPVy7b5-RcwwRjTn9229Wl9AWqaWxw_', '1468217765', '1', '', '', '1', '2016-07-12 21:14:48', '2016-07-12 20:50:01', '1', '627', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('172', '林训源', 'asdqwieoqwe123132', '', '1468034816', '0', '0', '0', '1', '0.00', '120.86.59.83', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxzk4nErTA2xZnQfnia9gdCpVfmghPsibt2tdLuFL9AgePA6kDILIK4G42RRvjsX6xMDXLDQqBub9GpGLcI4qrZND6/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXTZ*NaBqU18QEWkgmYRYfK9kIq7UhD7Bpa5qbZf1dxiSQ*f*fmnPvpAQBgWayvedPsB*1qdzISghsAEbz6Q2OUqLmr5734h-JoVC9rvnOyH9EPmY-QNKKE1E7t1CVAURhO1IquHhtGxMH3LWLYZ9OIake8T58WdzGNTRpWRDTbIesWOsjKTTJ0a*G7oKqKqG0Dw-WhSaMgVmn8-rHkS9aax5w*uKjMX8npORO2SVhhtrMVT1Yz*0KPFuWb20mlU2-yMohQROYE04keZG-VXv-*i3CIMWY-u6F39r4AbBhbPQ__', '1468121218', '0', '', '', '1', '2016-07-09 19:33:00', '2016-07-09 19:40:41', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('173', '灵儿', 'asdqwieoqwe123132', '', '1468037186', '0', '0', '0', '1', '0.00', '114.245.155.16', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6qdEiaVHzWPYJbGLicyYHTALQAPMN6SZqBMzUFVWQPgurlhEeMzLPKmlzAe2K7XwKUoXkbm4UwY1L8sOTraL7gr6d4d1wvia0qUA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXD64xpwTbUt42QwRyJBNDMF4K0K3eLUFjFEeN-d*ISSXz*zsk999NCCNnZNr0tq6p9b0xhRi1tdI9sbN-8odYgitIUbi-*oTxr6GVR7o3sJ3QodzCeR0DIxsAergEPUzbTkzgW04UJyd2lizlx*DwCasI42PlR4oNZURYsBk5UF8aszv2DGlVVw1abMX1Oh4aHZZD3T4OK6uUmdtcM3iR*dY2IFslD2x1fsnr9kXS73DlsxtDNVuc2eVzOV5lL4zqIeZhR7nkzHWR-grb5-RcTSgjhP7tt68v6Bue-XFo_', '1468123588', '0', '', '', '1', '2016-07-12 13:23:39', '2016-07-12 13:38:23', '1', '1724', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('174', '哈哈', 'asdqwieoqwe123132', '', '1468095356', '0', '0', '0', '1', '0.00', '110.84.170.38', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJAVG0e3ZRoVia4bMts2nr7ugkLTicm3tWFQXzfQ4pfyJWG1pTCp9w21tr0A0aypRDxlrQ4MkjrmvqA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlj1FPgzAURt-5FaSvGm1xTDDxgeHinCzDwYr60iAt5tYMsHRMZvzvOlxiE5-PufnO-bRs20ZplJzlRVFvK8103whkX9kIo9M-2DTAWa7ZheL-oPhoQAmWl1qoATqu72BsKsBFpaGEo*DhsWfQlr*xYWGAZPRzi33i*KYCrwNcTNfhXRCeyG5Wk7jrX4LHySpKt9G7kz24ezpf6H1N6eRepRKKMAtgGrTqiePsZr2MeS-mfjC7TKRMnunSy2OZbaAvznF5SwVOdtfGpIaNOAaNPeKMiGs2d0K1UFe--2LiEkL8Qzeyvqxv3XVcLA__', '1468181758', '0', '', '', '1', '2016-07-10 22:26:42', '2016-07-10 22:43:22', '1', '34', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('175', '锋', 'asdqwieoqwe123132', '', '1468130972', '0', '0', '0', '1', '0.00', '36.249.111.142', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIWTvhVXWfcNXWayZ9uvZnUxOOicbs96J6nk6C8hOJVRrLe3FBzfPibtjZ6rcVKe8BL4gdh9eoFibvom/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz11vgjAYBeB7fgXh1mVpKwy6ZBcEhwEksdPq7hqE4rrJR7AoYPbfN9FkTXb9nDfnvBdN13VjvVg9JmlataVksq*5oT-rBjAe-rCuRcYSyaZN9g95V4uGsySXvBkRWRgBoEZExkspcnEPOMBR9Zh9sbFhRGj*3gIMEVYjYj9i-Eq9gHjpAW3yeFfRlT3ZbhaTfiBzN-N2JpoR1K3xZ3EAvPeJfXaDvb8N-DD9CO0ZPdfv3rLFTv7WkaI1KV2ehrCM3HiInGjuui9KpRQFvw96cqCFbXuq6Ik3R1GVt38BtCCE*Lrb0L61H1yqWy0_', '1468217373', '0', '', '', '1', '2016-07-12 17:49:43', '2016-07-12 17:51:50', '1', '582', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('176', '晓萍妈妈', 'asdqwieoqwe123132', '', '1468132554', '0', '0', '0', '1', '0.00', '111.199.88.246', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmOicdHe0xM8ibbChmVBLwKVVUFH2hYaNSibBUDFccictXiciaQ5p4bjyicicH1NTiagp0uhf7LO7ILRdcgUAy767LtkNClX/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz81Og0AUBeA9T0FYGzMXBMHExYitAaktKqFlQ0gZ4JYwjDCKP-HdVWziJK6-c3PO-dB0XTceo4fTYr-vn7nM5Ztghn6hG8Q4*UMhsMwLmVtD*Q-Zq8CB5UUl2TCjaXsmIWoES8YlVngMuMQFRceyzeeGGeHs*5Z4YHpqBOsZV4vED5a1y*oiuw8BJb8Vm6egitIw26YH32frcHe9zGh81fo3fKK4oNO0jceOHmyrYe26ayIrjWl-nuB0l7XvUWKToFltmjgpd5dKpcSOHQc5Ljhg2Y6iL2wYsee--xKwAcD72W1on9oX6OBcRg__', '1468218956', '0', '', '', '1', '2016-07-12 18:03:46', '2016-07-12 19:57:38', '0', '1830', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('177', 'Sunday', 'asdqwieoqwe123132', '', '1468140723', '0', '0', '1', '1', '0.00', '121.204.96.238', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/EBC21D2A66EAD91328901F3860E2D7A0/100', 'http://q.qlogo.cn/qqapp/1105454078/EBC21D2A66EAD91328901F3860E2D7A0/40', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FPgzAUBeB3fkXDq0ZbaJGa*KBjRuYgQcURX5qOdqTiCnadqzP*dxWXSOLzd27OuR8eAMB-mN*f8Lruttoy*95LH5wDH-rHf9j3SjBuWWjEP5SuV0YyvrLSDBgQGkA4jightVUrdQjEMA5HuhEtGxoGRPj7FlIU0HFENQNm03KSFpOuSnZIlpfru8Vyh5u6utoiSmxevrp2eRbptkrzp-QlWxw1aVPYHN84k*vTrJrV8*dE4Nt94eLyOjRu1tvHBLd6z3lDphejSqvW8jAoilFESYBH*ibNRnX691*ICEKI-uz2vU-vC*XrXFY_', '1468227124', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 10:58:07', '1', '1265', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('178', 'RIKI', 'asdqwieoqwe123132', '', '1468151876', '0', '0', '0', '1', '0.00', '117.92.68.186', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgAwW0VGR1BVh0NXwgvDDpsBssGAFicC0kP6UKXMvGicVck4ic9UlSONtotGOECtm2Gu3vbJvicb3AGqLT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz1FvgjAUBeB3fkXT52VpK2BZsgeHxOBAUWSBJ9LQFusiNlhxavbftzGTkez5Ozfn3JsFAICbKH1kVXU4NaY0Fy0geAIQwYc-1Frxkply1PJ-KD60akXJpBFtj8TxCELDiOKiMUqqe4Aiag-0yN-LvqFHbH-fIg8TbxhRdY9xkPnhys*T65Lv8kCS1Mneqq0zjRQd*fK6aXN96fbnOlvOU1YkcR3Ws-V6EREizvNghvhiymzMokK-SncyOSHqhUW82nYqednFz4NKo-biPsilmCJ3PB5oJ9qjOjS--yLsYIy9n93Q*rS*AIxLW1I_', '1468238277', '0', '', '', '1', '2016-07-12 20:24:23', '2016-07-12 20:24:48', '1', '906', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('179', '千秀o2o-小蒋', 'asdqwieoqwe123132', '', '1468172768', '0', '0', '0', '1', '0.00', '120.32.127.233', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIXjZQOSdia6ibryPRY92wgCYoDIzicb1IHbYU1zUzyKYRcY96dQYJ9fySkMWM8t4193tboiaCuRic6wm2/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', 'eJxlz09vgjAYBvA7n4L06rK0-FFqsgOCW0x0ZgN38NIAbbGildXaKMu**xwzscnOv*fN87xfjuu6IJ9nj0VVHU5SE31pGXDHLoDg4Y5tKygpNPEV-Yfs3ArFSME1Uz16IfYgtCOCMqkFF7dABKPQ0iNtSN-QIwqutxAjD9sRUfe4mK6SWTIbepdys21UmWex6T4nz2tYU7gb4M38nL0Fk66JsO-L4CMW0zisF7wc7Og2WS7TnJdJuupUlbwHRq3ZPjWnFyPSUS5fefRkVWqxZ7dBw8iDKBzZHxmmjuIg--69IkII-*4GzrfzA6ZcW80_', '1468259170', '0', '', '', '1', '2016-07-11 16:26:21', '2016-07-11 16:54:12', '1', '54', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('180', '欧了个美 ヽ', 'asdqwieoqwe123132', '', '1468175295', '0', '0', '1', '1', '0.00', '117.25.62.95', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/559F19A6AADBE55D8902D50516D538CA/100', 'http://q.qlogo.cn/qqapp/1105454078/559F19A6AADBE55D8902D50516D538CA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 12:23:16', '0', '396', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('181', '过客', 'asdqwieoqwe123132', '', '1468180494', '0', '0', '1', '1', '0.00', '117.25.62.95', '福建', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/358AAD556102E9B26B77253F420A778C/100', 'http://q.qlogo.cn/qqapp/1105454078/358AAD556102E9B26B77253F420A778C/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 14:54:46', '1', '52', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('182', 'シ坏小孩', 'asdqwieoqwe123132', '', '1468180707', '0', '0', '1', '1', '0.00', '117.25.62.95', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/1ECF02BC2B11AF84EBC5E276770E3E2E/100', 'http://q.qlogo.cn/qqapp/1105454078/1ECF02BC2B11AF84EBC5E276770E3E2E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 16:19:26', '1', '21', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('183', 'ヤ＇L＇♀', 'asdqwieoqwe123132', '', '1468180797', '0', '0', '1', '1', '0.00', '117.25.59.178', '福建', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/1642DDD34588DB949F939D722587820F/100', 'http://q.qlogo.cn/qqapp/1105454078/1642DDD34588DB949F939D722587820F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 11:48:46', '1', '-15', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('184', 'vian♑️', 'asdqwieoqwe123132', '', '1468184066', '0', '0', '0', '1', '0.00', '112.96.100.187', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzkXPMN3XOKgriaA1JpiaTDrEWpLfKYvFCnicunj6djc7HuZj3DE6DnWt75M2MiaZLicojEicNgUSAnu544q3LaRpffxv4/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-11 12:54:29', '2016-07-11 13:08:57', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('185', '夜游神', 'asdqwieoqwe123132', '', '1468194381', '0', '0', '1', '1', '0.00', '125.118.105.101', '湖北', '黄冈', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/6E44B5D5D522EA72AAEE120CEE0EC90B/100', 'http://q.qlogo.cn/qqapp/1105454078/6E44B5D5D522EA72AAEE120CEE0EC90B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 16:12:45', '1', '447', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('186', '分割线------', 'asdqwieoqwe123132', '', '1468194586', '0', '0', '1', '1', '0.00', '110.87.40.70', '福建', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/36A73D27E3B73A37F16B753638BBA46F/100', 'http://q.qlogo.cn/qqapp/1105454078/36A73D27E3B73A37F16B753638BBA46F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 19:12:05', '1', '943', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('187', '~踏-空\"', 'asdqwieoqwe123132', '', '1468194621', '0', '0', '1', '1', '0.00', '110.91.202.44', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/1697BB2480F992BED2F204E018AC9666/100', 'http://q.qlogo.cn/qqapp/1105454078/1697BB2480F992BED2F204E018AC9666/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 19:34:45', '0', '1125', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('188', 's', 'asdqwieoqwe123132', '', '1468194706', '0', '0', '0', '1', '0.00', '120.32.127.233', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS00x04AD6XpRnFXBomZzrN7TpayVhGjbhzcvxe7JtYxct9pz9cUJt5qKeDIP5VI3iazXRyk83LYiaOP/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-11 15:51:48', '2016-07-11 15:52:25', '0', '25', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('189', '东京不太热', 'asdqwieoqwe123132', '', '1468194756', '0', '0', '1', '1', '0.00', '117.136.75.150', '福建', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/FF5B6DFC13DF3D27A692979141861130/100', 'http://q.qlogo.cn/qqapp/1105454078/FF5B6DFC13DF3D27A692979141861130/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 19:30:36', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('190', '長情', 'asdqwieoqwe123132', '', '1468194961', '0', '0', '1', '1', '0.00', '183.69.210.39', '四川', '达州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/3222A69117736554DAC7F28FEC839058/100', 'http://q.qlogo.cn/qqapp/1105454078/3222A69117736554DAC7F28FEC839058/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 15:57:14', '0', '20', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('191', '伊文思', 'asdqwieoqwe123132', '', '1468195929', '0', '0', '1', '1', '0.00', '121.204.96.238', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/4ACBE0F0C831D1912F863E7C04B3261D/100', 'http://q.qlogo.cn/qqapp/1105454078/4ACBE0F0C831D1912F863E7C04B3261D/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '1', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 18:28:08', '0', '10', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('192', '刘兴金', 'asdqwieoqwe123132', '', '1468195942', '0', '0', '0', '1', '0.00', '120.236.0.98', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7NtWMc194iahBP2pNQUiaE4uicIKYUyMsU1Cor9uiaM8cwOkKH9AwsmepXInBn9glP0FfZnzOrEDGZOWvnvukibgZcac/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 16:17:41', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('193', '境界的彼方', 'asdqwieoqwe123132', '', '1468199023', '0', '0', '1', '1', '0.00', '117.136.75.157', '福建', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/E7A232E9E81E1CB0E8DDA6B79315C64C/100', 'http://q.qlogo.cn/qqapp/1105454078/E7A232E9E81E1CB0E8DDA6B79315C64C/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 00:26:54', '0', '9', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('194', '范伟斌', 'asdqwieoqwe123132', '', '1468199603', '0', '0', '0', '1', '0.00', '117.25.62.95', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLC5icUMM4x1fFpQOrLwvYW5ouXK3dagAQia72sUdxVh09hnnxiawqjPoTCicTnAtFGVfxxW418ibRCSCYw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-11 17:13:41', '2016-07-11 17:17:04', '1', '775', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('195', '和尚用清扬心飞扬', 'asdqwieoqwe123132', '', '1468199661', '0', '0', '0', '1', '0.00', '117.25.59.178', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeejwk5eKBp2TZuydALc33r2HFqaCHvK0gSoJdalVRPGmFphB2p6eDrCXgAlu4dkzxlkEBISRyjxmZA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 13:08:52', '1', '3812', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('196', '豕', 'asdqwieoqwe123132', '', '1468199795', '0', '0', '1', '1', '0.00', '117.136.75.222', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/09D39BE89E855ACA05FE030448019DB0/100', 'http://q.qlogo.cn/qqapp/1105454078/09D39BE89E855ACA05FE030448019DB0/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '1', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 19:25:17', '0', '17', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('197', '壹个人的深＆夜', 'asdqwieoqwe123132', '', '1468200099', '0', '0', '1', '1', '0.00', '101.130.216.167', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/B8F450D4E185E73AEEB30647C07948F5/100', 'http://q.qlogo.cn/qqapp/1105454078/B8F450D4E185E73AEEB30647C07948F5/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 21:04:20', '1', '6', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('198', '北极星', 'asdqwieoqwe123132', '', '1468200178', '0', '0', '0', '1', '0.00', '120.32.127.233', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/NdiccZl8Jk96INUdefHdoKxicWOpDNU8iaGXA8BWcjuef9U5Eia37DMEbPuEkUVT5rw30ApYQsvvDxiaG2KhibuPyOmVcQkVMuuwaB/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 17:24:40', '0', '8', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('199', 'JUAN', 'asdqwieoqwe123132', '', '1468200291', '0', '0', '1', '1', '0.00', '120.32.127.233', '福建', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/FC01F1646F956377C91751ACB90D59D9/100', 'http://q.qlogo.cn/qqapp/1105454078/FC01F1646F956377C91751ACB90D59D9/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 11:46:43', '0', '48', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('200', '白开水', 'asdqwieoqwe123132', '', '1468200700', '0', '0', '0', '1', '0.00', '121.204.96.238', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/0T8yO33zeehsnl2ibT9lgA6uVFn6X3cDQZibSYDUibefEn6klAmGdeqDRbfevn3f2Kic7hNkDXbXxWE1In8kNYXIOx8xREjUlKY1/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 17:51:02', '0', '1137', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('201', '小小蜗壳', 'asdqwieoqwe123132', '', '1468207497', '0', '0', '0', '1', '0.00', '27.149.208.42', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLANKc47NLcztkjgLQqsc5B3XN5p2libLXxavtKwENTJ9gPbPdxDfhFy61uDTZzEMzeEFlzibe1WU1kw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-12 08:41:44', '2016-07-12 08:41:54', '1', '147', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('202', 'start_yhz', 'asdqwieoqwe123132', '', '1468207910', '0', '0', '3', '1', '0.00', '120.32.127.233', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://tva1.sinaimg.cn/crop.0.0.1080.1080.1024/662fd5e9jw8epeg5de714j20u00u0dhv.jpg', 'http://tva1.sinaimg.cn/crop.0.0.1080.1080.180/662fd5e9jw8epeg5de714j20u00u0dhv.jpg', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-11 19:59:55', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('203', '刘荔群', 'asdqwieoqwe123132', '', '1468219379', '0', '0', '0', '1', '0.00', '125.77.238.22', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJxyITRuCj40j3Yt9KgZAJymQE3cs0hsfmicu9VVmRwbJXGD1o5DcW1ZhVfGiceokn0kHgico3pzh6Fw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '1', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 19:55:36', '0', '1816', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('204', 'nsobject', 'asdqwieoqwe123132', '', '1468219789', '0', '0', '0', '1', '0.00', '61.241.221.78', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/mIFppCz6Rxz3FMmYZyLS023yibibgK6XYnBhYsn5Kh9K8Hrk6turl5tibZdfRIVjiaDcdrvFufVsV4sC5ic3gDPq55ax4JPI1aAu4/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('205', '有些事情我们无法左右', 'asdqwieoqwe123132', '', '1468261044', '0', '0', '3', '1', '0.00', '120.39.51.119', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://tva4.sinaimg.cn/crop.0.0.996.996.1024/b4d46bddjw8f4kxefxs7mj20ro0rptck.jpg', 'http://tva4.sinaimg.cn/crop.0.0.996.996.180/b4d46bddjw8f4kxefxs7mj20ro0rptck.jpg', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 10:19:44', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('206', '安然', 'asdqwieoqwe123132', '', '1468263909', '0', '0', '0', '1', '0.00', '123.7.39.207', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/tsia3doObj7PMrzl2ia59PpcricjTNFMJDJHuc0foSO7dQAuElWSIDBUAyqawEgOAMGyHHXx1lsTdZ2jXzzCweon6vgvJSjibnxK/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-12 11:08:28', '2016-07-12 11:11:23', '1', '162', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('207', 'Nothing', 'asdqwieoqwe123132', '', '1468267864', '0', '0', '0', '1', '0.00', '223.104.13.86', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/CkXW38kSRzmSnzH54wqGIUrhWXg5exwklBeScWgnk28AtMXxIQVQzkWr4uNZSqeiapqesAN0piaibkficbPF4SPTd4GHk9M2chs1/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 12:40:16', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('208', '選擇哯實', 'asdqwieoqwe123132', '', '1468278011', '0', '0', '0', '1', '0.00', '112.26.235.54', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLBxHYFnJwnMKPsb6kHkxLsRicbYxTw3aibSXDvIDDXicNsOdmGm4n0jrEAcbP1CJqaia5SYSHYjPFG5kA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-12 15:01:14', '2016-07-12 15:02:50', '0', '73', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('209', 'yhz', 'asdqwieoqwe123132', '', '1468280726', '0', '0', '1', '1', '0.00', '121.204.96.231', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/D4739CA1BC3447CF8E58BF1833127044/100', 'http://q.qlogo.cn/qqapp/1105519446/D4739CA1BC3447CF8E58BF1833127044/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 16:25:10', '1', '43539', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('210', '倫仔', 'asdqwieoqwe123132', '', '1468282106', '0', '0', '1', '1', '0.00', '124.244.185.224', '香港', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105454078/9A10EBCB527991AE5F7783194727416C/100', 'http://q.qlogo.cn/qqapp/1105454078/9A10EBCB527991AE5F7783194727416C/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '2016-07-12 16:12:52', '1', '91', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('211', 'pro', 'asdqwieoqwe123132', '', '1468390524', '1468824595', '0', '0', '1', '0.00', '59.56.187.92', '福建', '福州', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSaDSOKoxzbZTTPPr6H4VaEclzOQlIhmx61ib7YfVBt1s5IRMvNbBxsbm5xjYhfM7hKcf72K1hXUyYw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-13 18:30:00', '0000-00-00 00:00:00', '1', '137949', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('212', '千秀科技ZF', 'asdqwieoqwe123132', '', '1468430310', '0', '0', '0', '1', '0.00', '59.56.186.29', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBMAt1iaYvRfESKbSftppiaQHpYcWhL1ljTjBTHP7vPL4F3p12WdU0WsaJy9XWibz1icS1bMZp7G4uf2sg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '158915', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('213', 'A.', 'asdqwieoqwe123132', '', '1468450840', '0', '0', '0', '1', '0.00', '222.56.128.217', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3ctQmkwbRicYlGoaiapuSLBbusRxbO65OOwn931AZ5iaFtkBeCKBib5dRPlMD0foflAZkUPZUA8AooIs/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2217', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('214', '甘味人生123', 'asdqwieoqwe123132', '', '1468450890', '0', '0', '1', '1', '0.00', '59.56.186.29', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/7373407699BA06B73923BAABD879CDDA/100', 'http://q.qlogo.cn/qqapp/1105519446/7373407699BA06B73923BAABD879CDDA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-08 07:49:09', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('215', '@姑娘耍大刀', 'asdqwieoqwe123132', '', '1468453120', '0', '0', '1', '1', '0.00', '183.39.152.136', '上海', '黄浦', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/6FD89919F7892C725C0D71D31E44B885/100', 'http://q.qlogo.cn/qqapp/1105519446/6FD89919F7892C725C0D71D31E44B885/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '8885', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('216', 'Kimi', 'asdqwieoqwe123132', '', '1468456362', '0', '0', '1', '1', '0.00', '183.39.153.211', '广东', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/1E3E4E80BCBA4D5C25062AF85060B78D/100', 'http://q.qlogo.cn/qqapp/1105519446/1E3E4E80BCBA4D5C25062AF85060B78D/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '245', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('217', '路人', 'asdqwieoqwe123132', '', '1468456565', '0', '0', '1', '1', '0.00', '183.39.153.126', '广西', '南宁', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/DFF370A252CC93B36318FDDB9CDA3AB0/100', 'http://q.qlogo.cn/qqapp/1105519446/DFF370A252CC93B36318FDDB9CDA3AB0/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('218', '（❤ Vian）', 'asdqwieoqwe123132', '', '1468456984', '0', '0', '1', '1', '0.00', '183.39.153.126', '广东', '广州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/2E3C2074B11D9B1E0141C38A7C37F6F4/100', 'http://q.qlogo.cn/qqapp/1105519446/2E3C2074B11D9B1E0141C38A7C37F6F4/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '290', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('219', 'Aimly莎莎', 'asdqwieoqwe123132', '', '1468458053', '0', '0', '0', '1', '0.00', '113.214.218.195', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM6DA0zkL6tdwzViaITBeyjAb8z9t6mo9GQfMicEgibDxdJAc4hmlw9tBMpviba9a8OhbDVgmuaDIDuDpg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('220', '雲淡風輕', 'asdqwieoqwe123132', '', '1468458336', '0', '0', '1', '1', '0.00', '183.39.152.181', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/444CBB2D4C174F131AC9ED648F123A2D/100', 'http://q.qlogo.cn/qqapp/1105519446/444CBB2D4C174F131AC9ED648F123A2D/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '3372', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('221', 'Rob没脾气', 'asdqwieoqwe123132', '', '1468458767', '0', '0', '1', '1', '0.00', '183.39.152.46', '广东', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/7C617ADE1B81AB737B4E77148C685444/100', 'http://q.qlogo.cn/qqapp/1105519446/7C617ADE1B81AB737B4E77148C685444/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '8505', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('222', '羽蒙', 'asdqwieoqwe123132', '', '1468460856', '0', '0', '1', '1', '0.00', '183.39.153.54', '广东', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/68E91BC4D6893159FF4094F48FAA103E/100', 'http://q.qlogo.cn/qqapp/1105519446/68E91BC4D6893159FF4094F48FAA103E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1678', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('223', '天生', 'asdqwieoqwe123132', '', '1468465286', '0', '0', '0', '1', '0.00', '59.56.187.92', '', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPzPXCKUxA4cujajKJhupXC4vjvia5lag6B01BSjCXvsvpxicHA244Br29lh721tN8DiamDvAnzMVCPZcibh88pic2mN/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '6406', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('224', '上善若水', 'asdqwieoqwe123132', '', '1468489600', '0', '0', '0', '1', '0.00', '101.41.215.244', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUNeEk9EUNEvDlGiaVcnRo53MfX5bgT2MQE0ticV0WaNdtYHB8P63S6P6N1LW9q2hvsEKs6pVzib0Bhy/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('225', 'OA交流', 'asdqwieoqwe123132', '', '1468524680', '0', '0', '0', '1', '0.00', '1.62.206.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://interface.qiankeep.com/theme/images/defaulthead.png', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-15 01:11:18', '0000-00-00 00:00:00', '1', '6655', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('226', '东京不太热', 'asdqwieoqwe123132', '', '1468524874', '0', '0', '1', '1', '0.00', '211.97.122.11', '火星', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/6849DC7F658D3FECB252AE49742B013F/100', 'http://q.qlogo.cn/qqapp/1105519446/6849DC7F658D3FECB252AE49742B013F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '6009', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('227', '~踏-空\"', 'asdqwieoqwe123132', '', '1468524960', '0', '0', '1', '1', '0.00', '125.77.81.201', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/70BD33781BC331FD1B4D90DC797490EB/100', 'http://q.qlogo.cn/qqapp/1105519446/70BD33781BC331FD1B4D90DC797490EB/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '899', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('228', '祁红英', 'asdqwieoqwe123132', '', '1468525016', '0', '0', '0', '1', '0.00', '117.136.75.171', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbuGdLIndBUWFxC9tr0aia8TpZvaIulgqUlLlfYPDtbmicEVRzb1iaygG7epAFfsltsibkBSRqMTu1jex/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '273', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('229', '林铃', 'asdqwieoqwe123132', '', '1468525155', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYdibkOKV3dVXspjMSPichJa75qViaEzxf0vNbsM9ibgw5ZnqWuxV6jbBlahf2z1R3kyicg1hOdQEA9kTNaIBhGyZJL5/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('230', '准、男人', 'asdqwieoqwe123132', '', '1468525168', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUPYickF3LZxbzY2COO4R2SncYibRia5FOslBs9Xkwpd5XpjJWddspa5red1aFgU2gjaaFMP08m11o3ia/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '4432', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('231', 'urgirl', 'asdqwieoqwe123132', '', '1468531433', '0', '0', '0', '1', '0.00', '114.61.4.100', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSZtrl923iaVc3QcID4KKZURK5hzmbcuuFsUGVcUbiccASGlfPClvJTRPw3JG16Wb05u8RTecHwbvSW22Byen5Yabib/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '18', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('232', '倩多多', 'asdqwieoqwe123132', '', '1468532060', '0', '0', '0', '1', '0.00', '110.177.73.109', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM61m6xqCMmbM2OuSAm3W55GWdJm4ZLDnzQ9yBuwj0IjXUPClBHpEwOvzjukGjHRVicNfvic3P8yribMQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('233', 'AAA~灵魂Angel', 'asdqwieoqwe123132', '', '1468532311', '0', '0', '0', '1', '0.00', '219.134.53.45', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbVoxAe00JR3R8d0UkW19dicFckRmsDf3oib4icUjO5ITt2cSDCs6mibLiawStcYniabZ691wJ32kr3qYJag0o1EZp85O/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '4562', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('234', '呆萌→寻梦(︶︹︺)', 'asdqwieoqwe123132', '', '1468532454', '0', '0', '0', '1', '0.00', '113.99.5.187', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbVoxAe00JR3eIHeGldibGvaOz9N9tHNTlle8s2NaySibK7iakOFdplQ16lMg4T3oiceuibeequewh3bnSdbMFddG5Yk/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2795', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('235', 'G.K', 'asdqwieoqwe123132', '', '1468532633', '0', '0', '0', '1', '0.00', '117.136.64.89', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbUqIFN0qgZxEpscw2pibdibNuzZCdTDauDfCNSDGfpasIBMiawcPvcbyrhd9W2MztmhFXFB2wTrMYvoNTu90UVDiaW/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '8', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('236', '莫纯纯', 'asdqwieoqwe123132', '', '1468532770', '0', '0', '0', '1', '0.00', '121.31.250.14', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPqNWSNN3af3vtCBAcRkh6cGicia1RonAQBlzCyibGOUOQa86rNPljcIibYu2TNLoaVWvAqU242m2XSibcbaZJ1U6iaXz/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1345', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('237', 'cc小诗', 'asdqwieoqwe123132', '', '1468532784', '0', '0', '0', '1', '0.00', '112.96.100.128', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/DxzdP6HkgoORXJqeiaSmzicFQ5MgmtO3hnQNjKkAia8kbp7Lv3HEnwQywqpO8szZxOE5Ycw19nPaxBB8stJiaPCYXBzOtXUZTdyH/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '21', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('238', 'Cyan', 'asdqwieoqwe123132', '', '1468532927', '0', '0', '0', '1', '0.00', '27.41.143.88', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSY3QjkgfmavpP7qh7YDt8KougM6MdPicfn2GKuQibsndxtMk52nicQSOyvsQD3SrqBQS6s62r1p2fNrWXUrbj7jQre/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '53', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('239', 'Gene', 'asdqwieoqwe123132', '', '1468532940', '0', '0', '0', '1', '0.00', '223.88.119.141', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPyNJiaW8JlHrowbHClgmuZXCRJIpUia7BEKQS2VHl59Q5y1lJm7ibDVkiarMFKGbcxcIkkyy9MoDy2aw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '337', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('240', 'Ellent', 'asdqwieoqwe123132', '', '1468533097', '0', '0', '1', '1', '0.00', '113.66.244.97', '火星', '威克洛', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/0067986F8FB86331BC751EB6BE2480E1/100', 'http://q.qlogo.cn/qqapp/1105519446/0067986F8FB86331BC751EB6BE2480E1/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '620', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('241', 'A╭ 賢敏', 'asdqwieoqwe123132', '', '1468533530', '0', '0', '0', '1', '0.00', '183.43.237.254', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbtyzK1eAjvqz1gyctLmjsrJ5VlncFWflZK31gtGcicAeKwGP2PGajPibBy5oWYt65Uxo2f9c4cIvxF/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '43', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('242', '潇潇疏柳', 'asdqwieoqwe123132', '', '1468533841', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '郑州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/AF70A8E7612D6853294039E407B02A72/100', 'http://q.qlogo.cn/qqapp/1105519446/AF70A8E7612D6853294039E407B02A72/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '191', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('243', 'Angel', 'asdqwieoqwe123132', '', '1468533947', '0', '0', '0', '1', '0.00', '219.137.139.212', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3SfcYOpAOZgroEiawBZgta40vRsPxXEyAl9to0uLEn1CHJjzfcxqURmT4R0vUbhrB2CDTRI8xfuKB/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('244', '乐乐果', 'asdqwieoqwe123132', '', '1468534089', '0', '0', '0', '1', '0.00', '27.156.4.162', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBOGibrtqBOibsS7Uc1nDFVicduIxQ74A45d8e64ictaj1mNpicyXHMt8hDAo3vwcWRiazleib9mkyTEb38icw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '24', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('245', 'happy@Girl', 'asdqwieoqwe123132', '', '1468534174', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/0D33C747AFEA2211EA42F91648CE6141/100', 'http://q.qlogo.cn/qqapp/1105519446/0D33C747AFEA2211EA42F91648CE6141/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '598', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('246', '明星外模经纪小甜甜15013059047', 'asdqwieoqwe123132', '', '1468534599', '0', '0', '0', '1', '0.00', '113.68.188.10', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeZjrf4Oo2UIcI8YudL8FVHKEvfia3yW3oNEuE99hB4Jy3LkLrOMrncMg5XtVzHSlRmVPEJklJAfMS52jdFVoiaVQA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1821', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('247', 'Amy', 'asdqwieoqwe123132', '', '1468534653', '0', '0', '1', '1', '0.00', '183.39.153.126', '火星', '莫斯科', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/5AADC9D95E25579CACAC624FC22694D1/100', 'http://q.qlogo.cn/qqapp/1105519446/5AADC9D95E25579CACAC624FC22694D1/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2836', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('248', '兜空空', 'asdqwieoqwe123132', '', '1468534793', '0', '0', '1', '1', '0.00', '183.39.154.94', '火星', '抚州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/C510FCA23A8467252B1234C48DEC543E/100', 'http://q.qlogo.cn/qqapp/1105519446/C510FCA23A8467252B1234C48DEC543E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '3336', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('249', 'Aries。', 'asdqwieoqwe123132', '', '1468534837', '0', '0', '1', '1', '0.00', '183.5.238.173', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/F69A5A5988B5D93AD54557C04AC877C2/100', 'http://q.qlogo.cn/qqapp/1105519446/F69A5A5988B5D93AD54557C04AC877C2/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2932', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('250', '照頭乎', 'asdqwieoqwe123132', '', '1468534947', '0', '0', '1', '1', '0.00', '183.39.153.126', '火星', '巴斯', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/3C2AC05D9D8B8999A8C46691CA66C068/100', 'http://q.qlogo.cn/qqapp/1105519446/3C2AC05D9D8B8999A8C46691CA66C068/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2830', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('251', '  火', 'asdqwieoqwe123132', '', '1468535531', '0', '0', '0', '1', '0.00', '117.136.45.165', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBOs6ibBtQpEQUbPNgALqauLuLe7rib8TQggAu8iakwWWxI9YGkNW0noFbJtvVD97uJTzKyjqInQU9tLA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '1470', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('252', '阿俊模特演员工作室', 'asdqwieoqwe123132', '', '1468535882', '0', '0', '1', '1', '0.00', '14.23.233.121', '火星', '广州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/8DF34B033E1C41D3D30B3B3969E2CD64/100', 'http://q.qlogo.cn/qqapp/1105519446/8DF34B033E1C41D3D30B3B3969E2CD64/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2728', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('253', '分割线------', 'asdqwieoqwe123132', '', '1468535889', '0', '0', '1', '1', '0.00', '110.87.40.70', '火星', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/9FF90C364A08B3626BCAA2933CF71192/100', 'http://q.qlogo.cn/qqapp/1105519446/9FF90C364A08B3626BCAA2933CF71192/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '200', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('254', '大漠孤风', 'asdqwieoqwe123132', '', '1468535900', '0', '0', '1', '1', '0.00', '183.39.152.136', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/0292F10D7390063815834BAB690BB925/100', 'http://q.qlogo.cn/qqapp/1105519446/0292F10D7390063815834BAB690BB925/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '13308', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('255', '杨晓国', 'asdqwieoqwe123132', '', '1468535922', '0', '0', '0', '1', '0.00', '111.172.235.6', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3UbCQQw646hicsweaDMD4H3T8aCHAqu7gavMnIo5TAbWv7C6pkFKYg5jYYaicTITRyL7qibjGr672AQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '113', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('256', '范伟斌', 'asdqwieoqwe123132', '', '1468535951', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '三明', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/74D8EE43442C907E47A5C8A7609F320D/100', 'http://q.qlogo.cn/qqapp/1105519446/74D8EE43442C907E47A5C8A7609F320D/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1159', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('257', '我小时候可胖了', 'asdqwieoqwe123132', '', '1468536073', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/780236B56830D9585C0ABDC27EA0DE95/100', 'http://q.qlogo.cn/qqapp/1105519446/780236B56830D9585C0ABDC27EA0DE95/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1163', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('258', '擦拉黑有', 'asdqwieoqwe123132', '', '1468536086', '0', '0', '1', '1', '0.00', '58.61.175.32', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/37A95BF0902AA8B466668A861739C9B4/100', 'http://q.qlogo.cn/qqapp/1105519446/37A95BF0902AA8B466668A861739C9B4/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1013', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('259', 'Tony', 'asdqwieoqwe123132', '', '1468536107', '0', '0', '0', '1', '0.00', '14.154.159.61', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCgSnzUEUIeF57JHv1V7Ke2sGHDYa2hIr1dkMaich4povXoDozib59rOXqFRLJxG1GibsI91Gl0XwHpg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2810', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('260', '李子', 'asdqwieoqwe123132', '', '1468536112', '0', '0', '1', '1', '0.00', '183.39.152.136', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/1E72191C109536DD699DC2D225C77D6B/100', 'http://q.qlogo.cn/qqapp/1105519446/1E72191C109536DD699DC2D225C77D6B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '6224', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('261', '傅奕豪', 'asdqwieoqwe123132', '', '1468536214', '0', '0', '0', '1', '0.00', '113.102.169.73', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RosN85ibA0zKt3NwJoQDOf9gAHQibAQqmZq8Yib38Pnpou7kQdO0D3icRdeOymm9picxwibIHwIR3PkTnT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '448', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('262', '天人', 'asdqwieoqwe123132', '', '1468536351', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://interface.qiankeep.com/theme/images/defaulthead.png', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '77', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('263', '张政', 'asdqwieoqwe123132', '', '1468536410', '0', '0', '0', '1', '0.00', '113.118.244.183', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4pEqNCbCQPtwFySDPLhhWUfJBib37wVFWJCwNrxoT8jffEt247uDv6tibAqFQywI4cajBCs3ocVWOw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '55', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('264', '潘潘哒', 'asdqwieoqwe123132', '', '1468536517', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbncbsAbniadeQA0A5wpR4bxD7MIh6If1qeF2eCCFUmibsXFeRRBC4EvGtj0YUoicRn4X3vS91yhD2zn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2279', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('265', '呐喏', 'asdqwieoqwe123132', '', '1468536658', '0', '0', '1', '1', '0.00', '121.204.96.231', '火星', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/B5191FEC8CACDB328CB0AC624AB69695/100', 'http://q.qlogo.cn/qqapp/1105519446/B5191FEC8CACDB328CB0AC624AB69695/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '4710', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('266', 'D、W', 'asdqwieoqwe123132', '', '1468536695', '0', '0', '1', '1', '0.00', '59.56.187.92', '火星', '梅州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/CB2CD55CEEA680769A15B01922038229/100', 'http://q.qlogo.cn/qqapp/1105519446/CB2CD55CEEA680769A15B01922038229/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '986', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('267', '困倦', 'asdqwieoqwe123132', '', '1468536814', '0', '0', '1', '1', '0.00', '183.39.153.126', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/F66DBB8512977E91DC025B8655AA2A47/100', 'http://q.qlogo.cn/qqapp/1105519446/F66DBB8512977E91DC025B8655AA2A47/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '10854', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('268', '落落餘輝任你採摘', 'asdqwieoqwe123132', '', '1468536841', '0', '0', '1', '1', '0.00', '117.136.75.175', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/1FCC203B115BE03806C42D13632E4965/100', 'http://q.qlogo.cn/qqapp/1105519446/1FCC203B115BE03806C42D13632E4965/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '652', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('269', 'Nustar_Seeing', 'asdqwieoqwe123132', '', '1468536857', '0', '0', '0', '1', '0.00', '219.130.242.157', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/u4sPFDPjlLAHIsiaSSREcdqKD796cMv10l2T5GCpHGQ1MIr9oYkbjmPsZNxwdpvMXbvW00bpJQKl9hHanyMTnaAj09johbxlR/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '10', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('270', '。。。', 'asdqwieoqwe123132', '', '1468536897', '0', '0', '1', '1', '0.00', '183.39.155.177', '火星', '南昌', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/60CA552FB4880B319676342DF852E7CA/100', 'http://q.qlogo.cn/qqapp/1105519446/60CA552FB4880B319676342DF852E7CA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1174', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('271', '荣', 'asdqwieoqwe123132', '', '1468536898', '0', '0', '1', '1', '0.00', '123.98.79.211', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/8247445F55222164A3005E7EDE12EF76/100', 'http://q.qlogo.cn/qqapp/1105519446/8247445F55222164A3005E7EDE12EF76/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '276', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('272', '游侠', 'asdqwieoqwe123132', '', '1468536931', '0', '0', '0', '1', '0.00', '183.39.152.93', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbVoxAe00JR3Wmvc6kcsjFLmwCY9JV0TDQSunVytuhmW41ch7znSfTrnrYxJqVKCf92G4dlHReEiaqQo1wt0EkNt/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2404', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('273', '诺大牙', 'asdqwieoqwe123132', '', '1468537095', '0', '0', '0', '1', '0.00', '183.39.152.108', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1Rry3iaNUsbibuG9QFAqkibmibszLyOibR58e47ib9skratBHdgeW3LekUlOGPJs6oLlZEePRVUV8SiaUC36/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '8321', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('274', 'A大头静', 'asdqwieoqwe123132', '', '1468537123', '0', '0', '0', '1', '0.00', '27.46.224.189', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLCticoK0gjFibASCJAZ1Td7hTvXibSqVW065qwGNeoy7ia0Zpxzeqic18VHe51PgzYxvfe6rsZvOUorD6g/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('275', '境界的彼方', 'asdqwieoqwe123132', '', '1468537189', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '宁德', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/01AD2746058AC80A743EE0359AE8D59F/100', 'http://q.qlogo.cn/qqapp/1105519446/01AD2746058AC80A743EE0359AE8D59F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('276', '李逍遥', 'asdqwieoqwe123132', '', '1468537233', '0', '0', '0', '1', '0.00', '183.39.153.192', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBNJfo3DBwaVs5T5niajcjF88Vdn5VyIE6usWC91QGNBnrJQ4aTQYsEBNjacGV7VHv3MrgRrqMZuIiaA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-15 03:08:00', '0000-00-00 00:00:00', '1', '3394', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('277', '有胆你就来', 'asdqwieoqwe123132', '', '1468537256', '0', '0', '1', '1', '0.00', '211.97.161.145', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/9F368FAEA096627711CF0687DE06D99B/100', 'http://q.qlogo.cn/qqapp/1105519446/9F368FAEA096627711CF0687DE06D99B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '4111', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('278', '好孩子', 'asdqwieoqwe123132', '', '1468537338', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM4yZd88HyibwHekRIhn6lzdial1vVFFSiaNLWDVIVJEesychXWBzT8QMafAIIH7gRPqD3F3PYzriblicGQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2702', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('279', '阿静', 'asdqwieoqwe123132', '', '1468537352', '0', '0', '0', '1', '0.00', '117.136.41.72', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RrPYQlYmPuW1SjsNqe2lsQJR2cfxibfTYyotUTWiaCxejib3SJB4Q3YgtRZ9vkutdRmbuYKeibgHEZCo/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-14 11:08:39', '0000-00-00 00:00:00', '1', '555', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('280', ' Yonnie', 'asdqwieoqwe123132', '', '1468537378', '0', '0', '0', '1', '0.00', '59.42.231.178', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBOBrWA0SA5yianzRia06TnY6KDwW37c8VDVsJ9ibu0diaiaKou33QVdaJHc9AebfG3vovDZibIM9C1apkKQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '29', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('281', '°', 'asdqwieoqwe123132', '', '1468537382', '0', '0', '1', '1', '0.00', '183.10.88.184', '火星', '梅州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/C8ABEE18ECA666244BF7437440FF081A/100', 'http://q.qlogo.cn/qqapp/1105519446/C8ABEE18ECA666244BF7437440FF081A/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2632', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('282', '御守', 'asdqwieoqwe123132', '', '1468537581', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '湘西', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/E9517C1395706BA091450D36D4D94C5B/100', 'http://q.qlogo.cn/qqapp/1105519446/E9517C1395706BA091450D36D4D94C5B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2102', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('283', '壹个人的深＆夜', 'asdqwieoqwe123132', '', '1468537686', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/DBF7485F1CD3F912EF037FA40C685363/100', 'http://q.qlogo.cn/qqapp/1105519446/DBF7485F1CD3F912EF037FA40C685363/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '413', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('284', '超卡', 'asdqwieoqwe123132', '', '1468537731', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/D8963D1DBC6A9144DA7DD8876776241A/100', 'http://q.qlogo.cn/qqapp/1105519446/D8963D1DBC6A9144DA7DD8876776241A/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('285', '大鑫鑫', 'asdqwieoqwe123132', '', '1468537782', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/FF019EEF4CE2284B26F5786C8F3E9C52/100', 'http://q.qlogo.cn/qqapp/1105519446/FF019EEF4CE2284B26F5786C8F3E9C52/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '6185', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('286', '猫在键盘踩啊踩', 'asdqwieoqwe123132', '', '1468537918', '0', '0', '1', '1', '0.00', '120.32.127.58', '火星', '东城', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/5B6C381B655B10D3DF1BAF5A163ABBB9/100', 'http://q.qlogo.cn/qqapp/1105519446/5B6C381B655B10D3DF1BAF5A163ABBB9/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '711', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('287', 'Delia', 'asdqwieoqwe123132', '', '1468538095', '0', '0', '0', '1', '0.00', '183.39.153.126', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSZlvyrfJGXyaBpgpfFJXzqKQqmiaR3XqlczgLn5IUYf6JQa4rL7p7m4uuYR6eAicLxg7O38rmPSAmSQT0JWFSUdwA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '7444', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('288', '天堂鸟', 'asdqwieoqwe123132', '', '1468538410', '0', '0', '0', '1', '0.00', '59.56.186.29', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PdaKw4qMyFhXkPPPSX1YEibA2y0AKiaIZ2ed3fP9tNibOaSjTLplROLy9zXIoORFk2DTzczKsTYbQQ4pZupBWsQoVq/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '5690', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('289', '姚森', 'asdqwieoqwe123132', '', '1468539054', '0', '0', '1', '1', '0.00', '183.39.155.120', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/2B518AF2B649390C15FE114301E1F298/100', 'http://q.qlogo.cn/qqapp/1105519446/2B518AF2B649390C15FE114301E1F298/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '871', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('290', '欧了个美 ヽ', 'asdqwieoqwe123132', '', '1468539085', '0', '0', '1', '1', '0.00', '59.56.186.29', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/01B255084EBFCC03447FE88D43DCABBA/100', 'http://q.qlogo.cn/qqapp/1105519446/01B255084EBFCC03447FE88D43DCABBA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-09 08:10:49', '0000-00-00 00:00:00', '1', '311', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('291', '，。？！～', 'asdqwieoqwe123132', '', '1468539215', '0', '0', '0', '1', '0.00', '101.246.246.25', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM613XkkmGB5xiaJI350WF51AZdSn7DHan0CvTb4UiaJC09Gn9njO4RIib06cjMfZEFJYD0IawPMAH4wA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2055', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('292', 'James', 'asdqwieoqwe123132', '', '1468539339', '0', '0', '1', '1', '0.00', '183.39.153.126', '火星', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/0ED5A08FDBC74C0455B2C4023FE9F5FA/100', 'http://q.qlogo.cn/qqapp/1105519446/0ED5A08FDBC74C0455B2C4023FE9F5FA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '722', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('293', '我吃小龙虾', 'asdqwieoqwe123132', '', '1468539541', '1470368043', '0', '0', '1', '0.00', '211.148.85.203', '火星', '', '0', '0', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUBOKicwgwE22CYY36Zzvs6hCbJnWV642cTtJibzPZGmmuMkNWwV9IQITxSticAaZPNibtKgMGXl6Mdmb/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '4418', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('294', 'Richard Lee', 'asdqwieoqwe123132', '', '1468540021', '0', '0', '0', '1', '0.00', '123.66.35.97', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKZPY54k6oAvrCJBybCMp1qHV0wamNoxQypLeXRJwpqW5icqhUMzxgEuIt3Ocrt7j6kwyb5Cch56Fw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '193', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('295', 's', 'asdqwieoqwe123132', '', '1468540100', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbuTabItVCsVDBOJpgOhA9hgXichHicliadiaGOTUhsgCC52JREDUYt5UsQHAIamY0lyVYjVhhqdE6Fnr/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '86', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('296', '龙云', 'asdqwieoqwe123132', '', '1468540302', '0', '0', '0', '1', '0.00', '183.39.154.94', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeY6sEuBFqHZYEK2uG1rNjFjpB0SapM980CB97e5r7ZuISeHGK7jjQdB0BAQibZO6WOptHxYHWX6fS6k9jx0Q4sfk/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2067', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('297', '邹灵旖', 'asdqwieoqwe123132', '', '1468541046', '0', '0', '0', '1', '0.00', '183.52.209.132', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3f6icZiaPsVPQCquUxn9xRVmABpuj7c7RlotESRWSOwfggFyefjmefvvWA3jZEKcChDHzXCDibB7piaN/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('298', 'NAtt-_-O0o', 'asdqwieoqwe123132', '', '1468541772', '0', '0', '1', '1', '0.00', '223.73.155.224', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/9901880E89108E1BC466B2A8FBE009C9/100', 'http://q.qlogo.cn/qqapp/1105519446/9901880E89108E1BC466B2A8FBE009C9/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '2320', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('299', '丶许先森ㄨ', 'asdqwieoqwe123132', '', '1468542062', '0', '0', '1', '1', '0.00', '59.56.186.7', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/C6B74F07CFD257C5A66AF0B1C1E4127E/100', 'http://q.qlogo.cn/qqapp/1105519446/C6B74F07CFD257C5A66AF0B1C1E4127E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '13334', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('300', '百事可乐。', 'asdqwieoqwe123132', '', '1468542148', '0', '0', '1', '1', '0.00', '101.126.192.220', '火星', '武汉', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/A0AB26846E618FAA242883144A405C26/100', 'http://q.qlogo.cn/qqapp/1105519446/A0AB26846E618FAA242883144A405C26/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '2347', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('301', '怪怪愛獸獸', 'asdqwieoqwe123132', '', '1468542201', '0', '0', '0', '1', '0.00', '58.67.147.109', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86Pdte7lCqMCuKOpaHGQHOYqJXt0oHzib79EIQ2nCjnG6GWicMBm0kzgZ1UsKOXicdajVv5d1iameng5BamOJnb42tNRs/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '104', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('302', '别问我是谁', 'asdqwieoqwe123132', '', '1468542323', '0', '0', '0', '1', '0.00', '180.127.220.97', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUDhotpwwK4mZb8Zb79N5TMMZj2Wpfz41hibgRqQ9kribumqnc4ibdibNQ58elkRS4lhGd9VMp4xxGtw5/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '137', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('303', '余振国，众悦影视', 'asdqwieoqwe123132', '', '1468542330', '0', '0', '0', '1', '0.00', '122.243.46.6', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdboObJpibcHja8bYssORxE6aDtArr2F1FbH1arnZNRsa3YJBlsu71hcwH4w0wEtMgcibVoE5GJWZcWS/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '14676', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('304', 'Mr.黑白', 'asdqwieoqwe123132', '', '1468542664', '0', '0', '0', '1', '0.00', '115.230.115.57', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSa1RVXSqrKDYU6ibkwQP72Kv6GuQLwvP0CUCicIy4MtNQ70ibhpCOvHIX8dTcLyY1vUBEJlqWlMQAzYQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '91', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('305', '＊不念则忘', 'asdqwieoqwe123132', '', '1468542758', '0', '0', '1', '1', '0.00', '49.220.36.70', '火星', '广州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/8F988F2E5F917D7A48ECEFB18AE4FC9E/100', 'http://q.qlogo.cn/qqapp/1105519446/8F988F2E5F917D7A48ECEFB18AE4FC9E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '811', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('306', '游狐', 'asdqwieoqwe123132', '', '1468543512', '0', '0', '1', '1', '0.00', '36.5.145.173', '火星', '合肥', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/457ED12C653996AD8642224DB2C8BC02/100', 'http://q.qlogo.cn/qqapp/1105519446/457ED12C653996AD8642224DB2C8BC02/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1006', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('307', 'Fred', 'asdqwieoqwe123132', '', '1468543528', '0', '0', '0', '1', '0.00', '183.39.152.181', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5WcbMk26wBpqkb8lIcaiah4sqjXG5j5wQ5c31xaz0IYwguibSZswickKDGmtn162CibDp5icz97mQtHWtic9qniaMTRXpnDJaDmO3ibl0/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-04 06:59:49', '0000-00-00 00:00:00', '1', '6597', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('308', '   風不愿停息﹎', 'asdqwieoqwe123132', '', '1468543850', '0', '0', '1', '1', '0.00', '101.126.134.198', '火星', '弗里堡', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/EC46C8525FD5054328F76D648AF744E1/100', 'http://q.qlogo.cn/qqapp/1105519446/EC46C8525FD5054328F76D648AF744E1/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1150', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('309', '准、男人', 'asdqwieoqwe123132', '', '1468544449', '0', '0', '1', '1', '0.00', '59.56.186.7', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/6450E74BD2C67CB39EE4ED25B4D27109/100', 'http://q.qlogo.cn/qqapp/1105519446/6450E74BD2C67CB39EE4ED25B4D27109/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '12393', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('310', '靳宇', 'asdqwieoqwe123132', '', '1468544802', '0', '0', '0', '1', '0.00', '58.132.171.126', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3eTlK4XgdY6jqG4MgnHENt1az8TGSzdvfu5wTz51M3u2jbeyARel78u0p56Xfa44kpNAwxdml6XJ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '191', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('311', 'Sunny莹加油', 'asdqwieoqwe123132', '', '1468547180', '0', '0', '0', '1', '0.00', '42.199.57.157', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYxeibflNkMdBUp9Nm70o72wr5xG0icBShjia3ic9c8PTekd4VEgwmyCYDL7CzYhedE2G9aU8YicToWmVoAkUjhPnvzT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('312', 'Alaways小琳', 'asdqwieoqwe123132', '', '1468547461', '0', '0', '0', '1', '0.00', '116.5.31.188', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYwF5IFynnzmIRuekaruj9L7Xjsu7Jj8Q9BcPxlCYnYO48kC7h9OSOXcwaJic55RPHv7k6cJvTSQ40moAW73KcgE/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1115', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('313', '大萍子哈哈', 'asdqwieoqwe123132', '', '1468548987', '0', '0', '0', '1', '0.00', '117.136.0.185', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ib92NG88ro8ZQoia2Ow37hDlmYYkBUvGDO3089hLuJVtHx0Oxz3ibFERCRZCEkMGzKGOxrmicBiaoCh5H9VGojVtCL5ibunic3TuGay/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '218', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('314', 'Vivien', 'asdqwieoqwe123132', '', '1468554843', '0', '0', '0', '1', '0.00', '113.109.212.143', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86Pe3wkraphRupicDk4KcXvVGiaiaUml3srVOW4KiavX60AgIL3Lz4hfqU3V9dzgicwkibFfIh0ySgslaw0u2Iiboz6nB0hm/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '1237', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('315', '云淡风轻', 'asdqwieoqwe123132', '', '1468567934', '0', '0', '0', '1', '0.00', '110.84.147.111', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3farABFpxyYeUccgb6x5YokqiacK8gBjtzibQu5ibZjRicJMrbgWXs6LazicXRUHOQ9iaUIRjnEWaAIb6d/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '649', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('316', 'jim', 'asdqwieoqwe123132', '', '1468570095', '0', '0', '0', '1', '0.00', '27.38.49.176', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbic9ibRSh41OJqN6tnxGXF2QfN8cDyibnD5AUXF73dkRGNrSl46NR4X7sWH0QDnw83zkEg4fibYWbqHwqO50EL05hP/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('317', '自由', 'asdqwieoqwe123132', '', '1468573393', '0', '0', '1', '1', '0.00', '106.42.68.44', '火星', '郑州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/403DB5F680CB6C57E2A837602619B92B/100', 'http://q.qlogo.cn/qqapp/1105519446/403DB5F680CB6C57E2A837602619B92B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('318', 'lala', 'asdqwieoqwe123132', '', '1468600463', '0', '0', '0', '1', '0.00', '112.96.161.186', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RgRRvMPFyE575gwkyt6Aprnz9NMAicy4LQoDPQsuJFlTdw9icsfHZWj8Hk24XY5mTbSTyXMdUkP8YY/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('319', '乌弟夜宵', 'asdqwieoqwe123132', '', '1468649801', '0', '0', '0', '1', '0.00', '117.136.31.129', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBP1ibdibibfx0icWp3iaia1dRJfjRHUR4LXic0U4ez3q9yTiaDnK0qibV4aVRgo6f24nBFrqQwuOz5ne2aiaCPFsBtUJc0U7S/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('320', 'kaman', 'asdqwieoqwe123132', '', '1468726615', '0', '0', '0', '1', '0.00', '223.73.154.185', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbVoxAe00JR3R59bNIZsP51Enzeym3Az7DicJiaU3Q2Db3sKzYQAtGczXekYaaAGpuDvibQIQDAvb3ibzS9ebBGCy1O/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('321', '千秀安卓技术-zj', 'asdqwieoqwe123132', '', '1468821285', '0', '0', '1', '1', '0.00', '59.56.186.29', '火星', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/12976DA77DC51739CE500727586C22A0/100', 'http://q.qlogo.cn/qqapp/1105519446/12976DA77DC51739CE500727586C22A0/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('322', 'Wtfg', 'asdqwieoqwe123132', '', '1468901375', '0', '0', '0', '1', '0.00', '59.56.186.29', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3ZvEavyvcuFaJCZdn970rT8ovxFbw3GwykahfUmCiap4TxDY6icibQHqBe92KR3VxEJgBmLeUhOKcGb/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('323', '林俊杰', 'asdqwieoqwe123132', '', '1468906840', '0', '0', '1', '1', '0.00', '61.158.148.36', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/69BB6F72F3D85A47609B8DF7E070042F/100', 'http://q.qlogo.cn/qqapp/1105519446/69BB6F72F3D85A47609B8DF7E070042F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('324', 'Steve', 'asdqwieoqwe123132', '', '1468912534', '0', '0', '0', '1', '0.00', '119.137.21.147', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPzPXCKUxA4cs8jEon7JLzRNQ1j8AnMeuBwiaNtp7xibyPvcUzQiaGsnicU8T9kiaVQic7ianllfofKIvwCxNgv4KdzFCE/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-14 08:39:24', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('325', '오빠', 'asdqwieoqwe123132', '', '1468935971', '0', '0', '0', '1', '0.00', '180.127.220.97', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PfyiaNXkRoibL4OcWwbA9cU9Cdj0tOicIWXIIUzicFqgECOCNOeK5PNlDJN2dGE1EzxUENaRT0DwnsKyA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('326', 'wmiss', 'asdqwieoqwe123132', '', '1468970158', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbsiawhnafE3hm2oVDa4lvLM0o6LcJQicNLre8Dhw7ACnS15eJDhqw6NtEic15iaOTqRPMctUcL7YXjEV/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-21 00:57:19', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('327', 'physicszw', 'asdqwieoqwe123132', '', '1468971257', '0', '0', '0', '1', '0.00', '183.69.213.223', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSZFoDrnRhyK25F29GXvTM6zDk5ibBFP7L5XiaEoiaHo0RxwJicIumsf3jyAM3iaTFmb8KzNCgToUTucxmAtPz41h1mW2/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-20 08:28:04', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('328', '有些事情无法左右', 'asdqwieoqwe123132', '', '1468984377', '0', '0', '0', '1', '0.00', '59.56.186.172', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/6sjcgYCR1qyPSrf0ynCklblAxiaRC6xp5xs7KrxMzlNQbe33AjlfIKg4d8ptCVl5ILHnRwGK9HngGyBgoXcG3Vv9L0U6MsEicw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('329', '和尚用清扬心飞扬', 'asdqwieoqwe123132', '', '1469040680', '0', '0', '0', '1', '0.00', '120.32.127.58', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPPYyHyMKCC4LpFHn6M64umyTYDgyYQRUj3icibPaRtxKibbq6s8ua9shsFIAPGldHkagibnXX1BYxQ9w/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '1', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('330', '再见孙悟空', 'asdqwieoqwe123132', '', '1469164377', '0', '0', '0', '1', '0.00', '59.56.187.92', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbnfGibMW3ZCwKOwUJqAJ2bSGCvNgX3YNzAhBYqSrsguBxyvh61CxibglIXaUPuqnU7oquj7Iym8Wicq/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('331', '夏天音乐祭', 'asdqwieoqwe123132', '', '1469166115', '0', '0', '0', '1', '0.00', '121.204.96.231', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKjJiciaNoTpxe5drQRYcZNHaVU1egBPbD55M3iaw2ibYIRoOkTsU6g5pJb0XLWFoibia3cCVzVdT0WymHw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-07-26 09:17:55', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('332', 'Joe Wu', 'asdqwieoqwe123132', '', '1469170303', '0', '0', '0', '1', '0.00', '117.136.79.16', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/DxzdP6HkgoORXJqeiaSmzicLD8iakgyjKkFd2CstMiazmTnK2icxwdicBK5NBzDuR0MgZicWHEOjuWIw16RvvHs9pPYTZib9ickAf1v3N/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('333', '依赖', 'asdqwieoqwe123132', '', '1469185134', '0', '0', '0', '1', '0.00', '183.22.209.22', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5eEepfLFMptAdZhVmT1p2iaOOUHeEiaicdYM9IeeqpmvCBpNBks85hUEibN8gqWkXR7YBFvwZ3vcicaBH3lw0icC0Zomb3Vlkgu6qXI/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('334', '北斗齐心文化传媒', 'asdqwieoqwe123132', '', '1469213781', '0', '0', '0', '1', '0.00', '220.197.208.143', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUBtliaL1bqic4vLia0CsnXSgXdGQTlmcjOO35ibZUibdwYrWNpL1Oq5nvJ4Tw7US2scxnY6maLewqiajR0/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('335', '娇龙', 'asdqwieoqwe123132', '', '1469214590', '0', '0', '0', '1', '0.00', '117.136.79.142', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBOI8SBia2s0YLQzbsJAiayOlL3DoYicTkIojoLAIChfC0Xia4ibiby02k8v3vOW4yPAoibNZudCwHuthvPwTMJmmVRqgHl/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('336', '羽', 'asdqwieoqwe123132', '', '1469389857', '1469494160', '0', '0', '1', '0.00', '120.32.126.155', '', '', '0', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3dyJvSpoic3QupJJGxuwU2jUeZ6ibUzBRy7Pn3Yr7yDkNMgjllgtHsejCJpU5KPnxr2HJO3bIeO3o6/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('337', '酒', 'asdqwieoqwe123132', '', '1469401197', '1470279234', '0', '0', '1', '0.00', '14.122.107.228', '', '', '1', '1', '0', '0', '0', '著名老司机', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '著名老司机', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/D863ED3A66D3AB2C64FE25BB985509A2/100', 'http://q.qlogo.cn/qqapp/1105519446/D863ED3A66D3AB2C64FE25BB985509A2/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-14 06:15:33', '0000-00-00 00:00:00', '1', '0', '1', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('338', '墨', 'asdqwieoqwe123132', '', '1469403652', '0', '0', '0', '1', '0.00', '117.136.79.33', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/L39TGOpB5LKViaFfrbnSWCFtHXnia2pGRYAZTHdm0dRsn5YuzTsv42GntCY9J9dNCYvH4ICcjXXxzeX4oCUwGysz0HhFibpXlEU/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('339', '迷糊的兢', 'asdqwieoqwe123132', '', '1469421605', '0', '0', '0', '1', '0.00', '175.42.236.238', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3Wd5eD8aw2qCv7KID7619ibpPvrX0cRUhzLLnOM2xaGaV82jgZZSGvtB6Dq70kayHFkKFTmKAav3A/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('340', 'linyk', 'asdqwieoqwe123132', '', '1469425934', '0', '0', '0', '1', '0.00', '121.204.96.231', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PfPnVSAZZfxG1ouJics6NB0uI7vsNXast43rHsibt3XeZ34tjpeYUwRDQgC4j01hSO3e05NUZjYp0pQ/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('341', 'Tino', 'asdqwieoqwe123132', '', '1469463807', '0', '0', '0', '1', '0.00', '121.204.97.112', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLAEnRzyrByLkb3agiaWzh8TxB5n3LwTMyY1C8kw3tDQAndpC3fFWcuHjISJ8QhFic8uicdOePHGVylvw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('342', '何大双', 'asdqwieoqwe123132', '', '1469465053', '0', '0', '0', '1', '0.00', '110.184.30.117', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSZmibljic48gv0IIKbKA9ibjA4vNQqkbZmqPyzgqRNqjLqRQOdeMstxAInmBbicOtmJAG1NULmr9mscWGZw8nIUKzMp/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('343', '信恒刚 ‍♛起源天下', 'asdqwieoqwe123132', '', '1469470621', '0', '0', '0', '1', '0.00', '112.97.63.96', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBMG96p2Cibr79ZHqSs6wA4aT1eiaCpb25gy7icXRvy2ciaPk9iaKmwf2Waica6jfZ8sibzUV7GQKh9QCP9MBArIbqreCXV/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('344', '杨小焰', 'asdqwieoqwe123132', '', '1469473702', '0', '0', '0', '1', '0.00', '27.155.138.26', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3ZDF07vuicYy3kJ9yFdf7lFtB16Exy2AVVpAGken2q65eUEXNBIkEp6j8QDsGTYcgjzSZKZCd6rcz/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('345', '然', 'asdqwieoqwe123132', '', '1469475952', '0', '0', '0', '1', '0.00', '117.136.40.148', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RkiaXmdMnXkk4dJD2EIeiamHGVHLktk4bCyuhPlib05Xw5VvaicvKvuDyJbGIR1ic8Ceg8JeavmChulRv/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('346', '刘小玥', 'asdqwieoqwe123132', '', '1469477483', '0', '0', '0', '1', '0.00', '183.39.152.18', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeb5a8EsJM735acA7Njibn3ZYFFYLkiaJ4iatZa5WonJlc57MWzECqDluWc25FUt7b45G6iarOQmojd1n6SkaFrB5pYh/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('347', '罗力波', 'asdqwieoqwe123132', '', '1469483691', '0', '0', '0', '1', '0.00', '183.37.109.159', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEKnWw6aryNFibmibbO79IVzQhicQJ8AcVaXo94PbFWqeYeZIrrqwBdhZWAMqAGAeonQpMctrbwWRCVCw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('348', '彭Da魚☔', 'asdqwieoqwe123132', '', '1469488179', '0', '0', '0', '1', '0.00', '14.153.239.226', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbSjlyMzicAiaBB6DBfiakStgUCaxW0KOAScITjtpnPFCNxYOAf3NsVa6btFF9zAyrx0jtyPxGgBamXoXFm4kCKenia/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('349', '李子', 'asdqwieoqwe123132', '', '1469488396', '0', '0', '0', '1', '0.00', '183.39.152.181', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5mK8mClbTHdicT8hNjCfpd74pp3oMmLxt1ibjRnXGtMcmETQezUzsiaULawFKuQibmOvrxBm9l3GJTOAAyZicEFO35zw67f1OrhOMM/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-03 07:33:56', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('350', '包紫Katie', 'asdqwieoqwe123132', '', '1469492999', '0', '0', '0', '1', '0.00', '117.136.75.150', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLDV5nWMGIpyWDOZbPbgPht4UE3OEEfPTst1k44HTgIk5sbDbRGHTQ5iaGxcgX5lxsD74BAWg2gfJtg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('351', '佚名', 'asdqwieoqwe123132', '', '1469493679', '0', '0', '0', '1', '0.00', '59.60.140.180', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/ajNVdqHZLLC6OGSLyy2n7faLT2n8aLzraUXuTticticchPBibFMfibKPQHq3C4BWpAS0CwbLMrKibkIWgQmialtuJUbg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('352', '王·政是在下', 'asdqwieoqwe123132', '', '1469516834', '0', '0', '0', '1', '0.00', '183.39.152.75', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RiaEouXL1FQY08URVWw7caHys8wX5RFJE7w2pnxq8YmwlibwOK4hRQjHwEWibicdn3qJsgFSDFoCmswT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('353', '疯子', 'asdqwieoqwe123132', '', '1469560628', '0', '0', '0', '1', '0.00', '58.100.81.90', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaELcXQWIL0FxwMKicYFC6wRBqC0riaouoTvhb7SW2A3UACZQFRqHqKrGLfYBvoyhO73pjiaEUia6Iqpc0A/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('354', '姜学强', 'asdqwieoqwe123132', '', '1469998799', '0', '0', '0', '1', '0.00', '123.233.114.178', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdblEUzwZDdGEbQp3hpx8rAm7Yh2Jd7IsPcIcfMiapUfQmgNrYIqyHahoUlnIRsS43CRwXE7Unt9sCc/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('355', 'Sunday111222888', 'asdqwieoqwe123132', '', '1470030775', '1471080668', '0', '1', '1', '0.00', '59.56.187.92', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/71C6CAE8EACE4DF6614A82D841E4155D/100', 'http://q.qlogo.cn/qqapp/1105519446/71C6CAE8EACE4DF6614A82D841E4155D/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-15 02:33:43', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('356', 'Jacklin', 'asdqwieoqwe123132', '', '1470031502', '0', '0', '0', '1', '0.00', '140.243.87.181', '火星', '', '1', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PehplZ90wSXgbqLicP9bS53gMAo8qibd7bEnEMI1gCzWnMicnr37WiaWGupcptsGm4enspf0WSC22rnia19ficRibc06nb/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('357', 'vian♑️', 'asdqwieoqwe123132', '', '1470036172', '0', '0', '0', '1', '0.00', '59.42.86.6', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSbVoxAe00JR3fLZnGCmSRqhTnAcRUg8ZOSg7LM9iaKGmjibmibXxiaFxib8uvItHW3oXa3M4wBYnyr8rDGgYLumRgibict/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('358', '陈斯楠', 'asdqwieoqwe123132', '', '1470037030', '0', '0', '0', '1', '0.00', '117.136.40.181', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUAXa6nH1D4qlajecP1Mg42Dx9MsDNlicW7M6qGRP1lHtYA7R2z2icLETkODDiau7mBsiciaVXSIPIV6Jz/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('359', '莉莉', 'asdqwieoqwe123132', '', '1470041721', '0', '0', '0', '1', '0.00', '106.114.252.184', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUFnslicsloCg9WdzkAsvNEFezHlo3giadAdE7RvgQ5ECuwjAwLP8mPOhHfYEAXEhgINyJ7qFtdLofR/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('360', '无名', 'asdqwieoqwe123132', '', '1470081087', '0', '0', '1', '1', '0.00', '59.56.186.29', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/66D1497664C886EC6BE02D51C862ADC8/100', 'http://q.qlogo.cn/qqapp/1105519446/66D1497664C886EC6BE02D51C862ADC8/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('361', '闻聪', 'asdqwieoqwe123132', '', '1470163662', '0', '0', '0', '1', '0.00', '119.167.80.194', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBPzPXCKUxA4cuJ1OqS2nefSFdNslk5O178ZkY3ZpOKE3JRIVX1RJ9fcIPaEBorqIE5HeFZyT1eCj8lmKVbZZ4R4/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('362', 'Gothic', 'asdqwieoqwe123132', '', '1470190431', '0', '0', '0', '1', '0.00', '120.32.127.154', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5Vs81elMg7WBQ4mAibrcXvaV6lPia0A5tUas2LD8LU9FsB5uf1KooekVlQiaoEDRGIVQqWZqZicGO7Qw/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('363', '媛媛', 'asdqwieoqwe123132', '', '1470352761', '0', '0', '0', '1', '0.00', '223.104.20.183', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1Rq5LJxvZaszAFRicWB4FvVA4T8kH33QJqTicjYTA89ZFMasFyNMenxuOyIrBHTwia9ibgOxUcjyE7J83/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('364', 'Ayccon', 'asdqwieoqwe123132', '', '1470353105', '0', '0', '0', '1', '0.00', '116.7.100.219', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYyKCpOCQOYUOmicSWLvEmr2qho8CQKuXMdjIicfrxN50to7iaZOb4Lxkox9DOek0Ruk73VnP94TiaJOOpicTF57KJMf/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('365', '土豆哪里去挖', 'asdqwieoqwe123132', '', '1470353214', '0', '0', '0', '1', '0.00', '111.122.201.53', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86Pfn7o7p9CwTiamcpgl3NaaOm57eDg689icfZBC1ogbiay6GNFFcGibeKgFxEAltEcTqCXYx333shoKZ0rbibiaqaTZicV7/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('366', '威森', 'asdqwieoqwe123132', '', '1470353822', '0', '0', '0', '1', '0.00', '112.96.100.71', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbiak6aSzKl5gTKJrlcf1IbOClBTUUdiaO1fZoia056kMYaOwiaGxbiaaIttGa88yQKlicK7jia8NH39RJ1N/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('367', '•貓', 'asdqwieoqwe123132', '', '1470354504', '0', '0', '0', '1', '0.00', '124.226.40.183', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86Pe3wkraphRupic0dNs9MApTtNFK0cyNJ0ZunicGRibqH7DWc7oibU8cDT58Tukvupt4BmnCM5t1YNlFWLvGUbWdicibqP/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('368', 'T-mac', 'asdqwieoqwe123132', '', '1470354675', '0', '0', '0', '1', '0.00', '223.73.53.98', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcoNrLK0HQZicBcorRkTKick9rq53DYlq5lp8BiaHrE6VOesubOvrGBO2ZZlukYtfrpu2kqFmh9LarBB5Xhpav82ek/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('369', '南风', 'asdqwieoqwe123132', '', '1470355071', '0', '0', '0', '1', '0.00', '223.73.119.12', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeZ2iaickUcDiaIVshY17KfpRCc2sEIObTh2Qft5daTpDfiaPRibVfADib0iaRHJu4nZ8WvC296h44lgcbp3sJfW6Om2WCC/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('370', 'Ann-Kylie', 'asdqwieoqwe123132', '民航学院 2016新生', '1470356318', '1470376874', '0', '0', '1', '0.00', '14.120.225.55', '火星', '', '0', '2', '-28800', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/X6Ucic5kYIBNeGojatMpwAaebYHUurq5icvY4kJia4TDpONB4NJZoLCUkkTHx0GKibGaiceMQcjCvoO55J8ficHdIe2uZYvpnJatFn/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('371', 'Camila', 'asdqwieoqwe123132', '', '1470357901', '0', '0', '0', '1', '0.00', '125.34.216.111', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/Q3auHgzwzM5eEepfLFMptAdZhVmT1p2iagvRNY7LbbbXXdZUy4DKDppRvrfQE15j0NibSQ0e1ErVO3VEpBIjwQhfGqArcWEB15SOl1WBjJmMM/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('372', '貓王妃', 'asdqwieoqwe123132', '', '1470362644', '1470367919', '0', '0', '1', '0.00', '112.96.100.188', '广东', '广州', '0', '2', '652374000', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PcO2Us7uoKdbrGgVaqu3LgzetWCBxXkZ44vMHrBgZYKXfNGJP4IVEKg2eufQxSAvSjP0icwt92fqTQSWBlNuP9wj/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('373', '广通-马成', 'asdqwieoqwe123132', '', '1470364649', '0', '0', '0', '1', '0.00', '27.22.79.36', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3ZAkZjXiaK6GORQlgJPgat20NzclMRz4pHdVAG24CY1q7ucPEHkY3OGA3YD21smFrtnAaNmpD4AVg/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('374', '小太阳☀️', 'asdqwieoqwe123132', '爱我你就抱抱我', '1470364963', '1470445927', '0', '0', '1', '0.00', '123.88.232.98', '澳门', '澳门', '0', '2', '884102400', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeaVDkys6KRYY9OvIOTm538khmd68OXBqDjiapvZuJyNU6VbygJzt3DNfZ85E0Mo1Siaj05wRAFPOb5iaPPjzR3wGOE/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('375', '甘味人生', 'asdqwieoqwe123132', '', '1470613830', '0', '0', '1', '1', '0.00', '59.56.186.29', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/7373407699BA06B73923BAABD879CDDA/100', 'http://q.qlogo.cn/qqapp/1105519446/7373407699BA06B73923BAABD879CDDA/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('376', ' Super Langa', 'asdqwieoqwe123132', '', '1470701624', '0', '0', '0', '1', '0.00', '101.81.74.27', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/DxzdP6HkgoORXJqeiaSmzicGNxf4M2ErkCjsAGt64tj6KzXc7icCeqZCqyEicuhjpbNictb0cjQyg0WN9hbl9ia3Vic2yeWQDfBAjko/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('377', '罗力波', 'asdqwieoqwe123132', '', '1470773068', '1470790474', '0', '1', '1', '0.00', '183.39.155.120', '广东', '深圳', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/2C400726ABAF1BA393F89557A5D77B0F/100', 'http://q.qlogo.cn/qqapp/1105519446/2C400726ABAF1BA393F89557A5D77B0F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('378', '玖/kl', 'asdqwieoqwe123132', '', '1470773825', '0', '0', '1', '1', '0.00', '183.39.155.138', '浙江', '杭州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/733595EBEDEF032F4B4EFC6FEDEFDBFB/100', 'http://q.qlogo.cn/qqapp/1105519446/733595EBEDEF032F4B4EFC6FEDEFDBFB/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('379', '生活', 'asdqwieoqwe123132', '', '1470811378', '1471080931', '0', '0', '1', '0.00', '59.56.187.92', '广东', '深圳', '1', '1', '155059200', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/7945E59F22D1FBF8F2214C181B3BA24F/100', 'http://q.qlogo.cn/qqapp/1105519446/7945E59F22D1FBF8F2214C181B3BA24F/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('380', '斑の猫', 'asdqwieoqwe123132', '', '1470861523', '0', '0', '0', '1', '0.00', '59.41.93.177', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYXvcfeAcicbUJWfcgoSeqyNmVoIcFj1N9EO99hJx9jVZCrnmBWricDCkoJFPichn4iarffJfOg2gIGG7tyOzYcHjML/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('381', '石河子在线', 'asdqwieoqwe123132', '', '1470864672', '0', '0', '0', '1', '0.00', '110.155.171.87', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/PiajxSqBRaEJGjiaWtLboJ9S4QBeXh8xYRouiakORs8qIYibIhBXphENYcrD6n57qXdN7nr4cl8u4ra312k4KXvDXA/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('382', '行走在地平线', 'asdqwieoqwe123132', '', '1470885618', '0', '0', '1', '1', '0.00', '183.39.155.138', '吉林', '长春', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/C7FAF11FBC30859E633DB3472044462B/100', 'http://q.qlogo.cn/qqapp/1105519446/C7FAF11FBC30859E633DB3472044462B/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('383', 'cocoa', 'asdqwieoqwe123132', '', '1470938223', '0', '0', '1', '1', '0.00', '117.136.75.239', '福建', '福州', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/415A56FDDA248102D23510E04C558257/100', 'http://q.qlogo.cn/qqapp/1105519446/415A56FDDA248102D23510E04C558257/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-12 09:37:17', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('384', '曹曹曹、', 'asdqwieoqwe123132', '', '1470983757', '0', '0', '0', '1', '0.00', '110.53.132.38', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1Rg5zObKPra7PVv4FMyPODDmOXgGOdKbX72C5R12rLIN6spsqplzt9WEXa7UCk5x76zdgnlK9xIIT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('385', '开心', 'asdqwieoqwe123132', '', '1471137971', '1471138667', '0', '0', '1', '0.00', '61.154.15.150', '', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '帅哥', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/SSnmE8B86PeDiaE13rOlcEicbq4152ua5duuz6rQ6hmKCibe0piaOesAKskjrvxiaiaPaSqDjrbfEQCT8eOicHx8ia9zgh29R3rBO8Lic/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '6');
INSERT INTO `%DB_PREFIX%user` VALUES ('386', 'AlexSo', 'asdqwieoqwe123132', '', '1471141672', '0', '0', '0', '1', '0.00', '119.33.114.143', '火星', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeagjicLyKicW1RqrCr0YgQJ7RvKmXmuJ9CUKjg39cU6R9yXduN1csdOfFht1QnlohvxCHUbRLicgq0Cw51kE0VjMx5/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('387', '＆雲飞水月＆', 'asdqwieoqwe123132', '', '1471154927', '0', '0', '1', '1', '0.00', '101.130.200.215', '福建', '福州', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://q.qlogo.cn/qqapp/1105519446/6BC2DD29063439DA3729F8B69DFB135E/100', 'http://q.qlogo.cn/qqapp/1105519446/6BC2DD29063439DA3729F8B69DFB135E/40', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('388', '羽1', 'asdqwieoqwe123132', '', '1471184788', '1471214795', '0', '0', '1', '0.00', '101.130.221.147', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3dyJvSpoic3QupJJGxuwU2jUeZ6ibUzBRy7Pn3Yr7yDkNMgjllgtHsejCJpU5KPnxr2HJO3bIeO3o6/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('389', '逗逗麻麻つ杂货店', 'asdqwieoqwe123132', '', '1471201508', '0', '0', '0', '1', '0.00', '183.39.152.27', '火星', '', '1', '2', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '1', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/7aHoiacrWTeYF9HuuAQYm0IqshC0xcVvyWTPK9aQia7PnoSiblJE9WzzXTbW0u3iaPRmm0ClYArVloibXZdo5gbqPTChjQK7EfEBT/0', '', '', '', '0', '', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1', '0', '0', '1', '', '0', '0', '0', '0', '', '0');
INSERT INTO `%DB_PREFIX%user` VALUES ('390', 'ViVi', 'asdqwieoqwe123132', '', '1471202464', '1471475246', '0', '0', '1', '0.00', '117.136.41.55', '', '', '1', '1', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '3', '', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '160', '0', '保密', '主播', 'http://wx.qlogo.cn/mmopen/o9aDQpnysSYl0Z7aKFMj3SXiao9ialunDKPdFZAicQviblqCIjl3ia2qXXssRibXxuHrT6KLolh05RRuOpxYnxwzea7qz188dGR20d/0', '', '', '', '0', '0', '', '0', '0', '0', '0', '0', '', '0', '0', '', '', '1', '2016-08-15 03:22:24', '0000-00-00 00:00:00', '0', '0', '0', '1', '', '0', '0', '0', '0', '', '0');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_admin`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_admin`;
CREATE TABLE `%DB_PREFIX%user_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `podcast_id` int(10) NOT NULL COMMENT '主播ID; %DB_PREFIX%user.id',
  `user_id` int(10) NOT NULL COMMENT '主播下面的管理员id',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播管理员';

-- ----------------------------
-- Records of %DB_PREFIX%user_admin
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_id`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_id`;
CREATE TABLE `%DB_PREFIX%user_id` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '所有用户的ID从这张表中生成;这样就可以产生唯一,不重复的用户id',
  `sysid` int(10) NOT NULL DEFAULT '0' COMMENT '所分配的系统',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=391 DEFAULT CHARSET=utf8 COMMENT='获取一个新的用户号,同时记录分配给那个系统使用;需要放在总数据库中';

-- ----------------------------
-- Records of %DB_PREFIX%user_id
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_level`
-- ----------------------------

DROP TABLE IF EXISTS `%DB_PREFIX%user_level`;
CREATE TABLE `%DB_PREFIX%user_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL COMMENT '等级名',
  `level` int(11) DEFAULT NULL COMMENT '等级大小   大->小',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '等级所需积分',
  `point` int(11) NOT NULL COMMENT '所需信用值',
  `icon` varchar(255) NOT NULL COMMENT '等级图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT= 1 DEFAULT CHARSET=utf8 COMMENT='//用户等级';

-- ----------------------------
-- Records of %DB_PREFIX%user_level
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv1', '1', '10', '10', './public/attachment/201605/20/08/573e5ee3ce14a.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv2', '2', '60', '60', './public/attachment/201605/20/08/573e5ef612269.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv3', '3', '160', '160', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv4', '4', '261', '261', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv5', '5', '360', '360', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv6', '6', '510', '510', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv7', '7', '660', '660', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv8', '8', '810', '810', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv9', '9', '990', '990', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv10', '10', '1240', '1240', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv11', '11', '1540', '1540', './public/attachment/201605/20/08/573e5ee3ce14a.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv12', '12', '1890', '1890', './public/attachment/201605/20/08/573e5ef612269.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv13', '13', '2270', '2270', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv14', '14', '2680', '2680', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv15', '15', '3120', '3120', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv16', '16', '3580', '3580', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv17', '17', '4070', '4070', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv18', '18', '4580', '4580', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv19', '19', '5120', '5120', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv20', '20', '5700', '5700', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv21', '21', '6310', '6310', './public/attachment/201605/20/08/573e5ee3ce14a.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv22', '22', '6970', '6970', './public/attachment/201605/20/08/573e5ef612269.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv23', '23', '7690', '7690', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv24', '24', '8480', '8480', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv25', '25', '9350', '9350', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv26', '26', '10320', '10320', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv27', '27', '11420', '11420', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv28', '28', '12620', '12620', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv29', '29', '13980', '13980', './public/attachment/201605/20/08/573e5f12381fb.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv30', '30', '15510', '15510', './public/attachment/201606/21/16/5768fec847204.png');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv31', '31', '17250', '17250', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv32', '32', '19250', '19250', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv33', '33', '21450', '21450', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv34', '34', '23950', '23950', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv35', '35', '26750', '26750', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv36', '36', '29880', '29880', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv37', '37', '33380', '33380', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv38', '38', '37280', '37280', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv39', '39', '41630', '41630', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv40', '40', '46460', '46460', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv41', '41', '51820', '51820', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv42', '42', '57750', '57750', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv43', '43', '64290', '64290', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv44', '44', '71490', '71490', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv45', '45', '79390', '79390', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv46', '46', '88030', '88030', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv47', '47', '97530', '97530', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv48', '48', '107830', '107830', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv49', '49', '119030', '119030', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv50', '50', '131230', '131230', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv51', '51', '144430', '144430', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv52', '52', '158680', '158680', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv53', '53', '174080', '174080', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv54', '54', '190680', '190680', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv55', '55', '208480', '208480', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv56', '56', '227680', '227680', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv57', '57', '248280', '248280', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv58', '58', '270380', '270380', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv59', '59', '293980', '293980', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv60', '60', '319190', '319190', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv61', '61', '346190', '346190', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv62', '62', '374850', '374850', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv63', '63', '405350', '405350', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv64', '64', '437750', '437750', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv65', '65', '472150', '472150', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv66', '66', '508650', '508650', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv67', '67', '547550', '547550', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv68', '68', '588450', '588450', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv69', '69', '631750', '631750', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv70', '70', '677350', '677350', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv71', '71', '725550', '725550', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv72', '72', '776350', '776350', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv73', '73', '829880', '829880', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv74', '74', '886220', '886220', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv75', '75', '945460', '945460', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv76', '76', '1007710', '1007710', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv77', '77', '1073060', '1073060', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv78', '78', '1141660', '1141660', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv79', '79', '1213530', '1213530', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'Lv80', '80', '1288832', '1288832', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv81', '81', '1300000', '1300000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv82', '82', '1330000', '1330000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv83', '83', '1360000', '1360000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv84', '84', '1390000', '1390000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv85', '85', '1420000', '1420000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv86', '86', '1460000', '1460000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv87', '87', '1500000', '1500000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv88', '88', '1800000', '1800000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv89', '89', '2000000', '2000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv90', '90', '2500000', '2500000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv91', '91', '3000000', '3000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv92', '92', '3500001', '3500001', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv93', '93', '4000000', '4000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv94', '94', '4500000', '4500000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv95', '95', '5000000', '5000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv96', '96', '5500000', '5500000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv97', '97', '6000000', '6000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv98', '98', '6500000', '6500000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv99', '99', '7000000', '7000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv100', '100', '8000000', '8000000', '');
INSERT INTO `%DB_PREFIX%user_level` (name,`level`,score,point,icon) VALUES ( 'lv101', '101', '10000000', '10000000', '');


-- ----------------------------
-- Table structure for `%DB_PREFIX%user_log`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_log`;
CREATE TABLE `%DB_PREFIX%user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_info` text NOT NULL,
  `log_time` int(11) NOT NULL,
  `log_admin_id` int(11) NOT NULL,
  `money` double(20,4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型 0表示充值 1表示提现 2赠送道具',
  `prop_id` int(11) NOT NULL COMMENT '道具ID号',
  `score` int(11) NOT NULL COMMENT '积分',
  `point` int(11) NOT NULL COMMENT '信用值',
  `podcast_id` int(11) NOT NULL COMMENT '主播ID',
  `diamonds` int(11) NOT NULL,
  `ticket` int(11) DEFAULT '0' COMMENT '票数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//帐户资金变动日志';

-- ----------------------------
-- Records of %DB_PREFIX%user_log
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_music`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_music`;
CREATE TABLE `%DB_PREFIX%user_music` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '观众id',
  `audio_id` varchar(255) NOT NULL COMMENT '音乐标识',
  `audio_link` varchar(255) NOT NULL COMMENT '音乐下载地址',
  `lrc_link` varchar(255) NOT NULL COMMENT '歌词下载地址',
  `audio_name` varchar(255) NOT NULL COMMENT '歌曲名',
  `artist_name` varchar(255) DEFAULT NULL COMMENT '演唱者',
  `create_time` int(10) NOT NULL,
  `time_len` int(10) NOT NULL DEFAULT '0' COMMENT '时长（秒）',
  `api_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'api类型;1:tingapi.ting.baidu.com',
  `lrc_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '歌词类型',
  `lrc_content` longtext COMMENT '歌词',
  PRIMARY KEY (`id`),
  KEY `idx_um_001` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2147 DEFAULT CHARSET=utf8 COMMENT='用户下载的音乐';

-- ----------------------------
-- Records of %DB_PREFIX%user_music
-- ----------------------------
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2046', '100236', '73987461', 'http://yinyueshiting.baidu.com/data2/music/239124483/239124483.mp3?xcode=a9b1bc40b6d7ccae5990d376263109e0', '', '乌克丽丽', '刘瑞琦', '1471309618', '200', '0', '0', '[00:00.03]乌克丽丽\n[00:00.11]\n[00:00.18]演唱：刘瑞琦\n[00:00.20]\n[00:01.60]你就是海滩下的那乌克丽丽\n[00:07.29]寻找着逆光让暧昧变成剪影\n[00:14.66]浪漫不一定要在那夏威夷\n[00:20.14]沙滩上有你的脚印 是一辈子美景\n[00:34.49]\n[00:37.0]穿花衬衫要配上一把乌克丽丽\n[00:40.34]不然看起来会像流氓兄弟\n[00:43.32]穿西装要记得带个带个女伴\n[00:45.24]不然看起来会像泊车小弟\n[00:48.28]一般我是不会随便就 载着花瓶兜风\n[00:50.60]我不是说你是个 花瓶 你你你别发疯\n[00:53.72]你说你很容易碎 叮咚叮咚\n[00:55.85]让我来保护你 你懂我懂\n[00:58.27]你要我小心你的坏脾气\n[01:00.6]我说我刚好有一点耐心\n[01:02.88]你始终改变不了的任性\n[01:05.44]我当它是一种可爱的个性\n[01:07.57]你就是海滩下的那乌克丽丽\n[01:12.13]寻找着逆光让暧昧变成剪影\n[01:18.23]浪漫不一定要在那夏威夷\n[01:23.18]沙滩上有你的脚印 是一辈子美景\n[01:32.94]\n[01:38.6]别剪短你的发让它飘逸\n[01:41.47]刚好配上他的八块肌\n[01:43.34]别再不吃东西 瘦到不行\n[01:46.5]乌克丽丽在你身上\n[01:47.14]就像一把放大的Guitar\n[01:49.91]迷人不一定要比基尼\n[01:51.22]你的笑容已经非常卡哇伊\n[01:53.87]想要度假不用去到Hawaii\n[01:56.0]椰子树刚好种在我家院子里\n[01:59.13]想要买醉 买醉 买醉 买醉 \n[02:01.63]不一定要喝酒 \n[02:04.64]一起干杯 干杯 干杯 干杯 \n[02:06.23]用音乐来灌醉你 \n[02:08.35]你就是海滩下的那乌克丽丽\n[02:13.62]寻找着逆光让暧昧变成剪影\n[02:18.92]浪漫不一定要在那夏威夷\n[02:23.16]沙滩上有你的脚印 是一辈子美景\n[02:28.97]你就是海滩下的那乌克丽丽\n[02:33.55]寻找着逆光让暧昧变成剪影\n[02:39.92]浪漫不一定要在那夏威夷\n[02:43.40]沙滩上有你的脚印\n[02:46.83]是一辈子美景 \n[02:49.79]你就是海滩下的那乌克丽丽\n[02:53.81]寻找着逆光让暧昧变成剪影\n[02:59.59]浪漫不一定要在那夏威夷\n[03:04.27]沙滩上有你的脚印 是一辈子美景\n[03:09.30]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2047', '100237', '85065229', 'http://yinyueshiting.baidu.com/data2/music/123027717/123027717.mp3?xcode=174808df6182a0b729676627b54cb918', '', '国歌', '彭丽媛', '1471365775', '294', '0', '0', '[ti:]\n[ar:]\n[al:]\n[offset:0]\n\n[00:02.40]国歌\n[00:05.58]演唱：彭丽媛\n[00:09.31]\n[00:40.57]这一首歌 用我们的热血谱曲\n[00:48.73]这一首歌 用我们的挚爱唱起\n[00:57.02]这一首歌 在我们的心里燃烧\n[01:05.21]这一首歌 激励我们走过风雨\n[01:13.84]国歌响起 国歌响起\n[01:21.76]你是我们生命的旋律\n[01:30.23]国歌响起 国歌响起\n[01:38.48]为了中华民族的伟大复兴\n[01:42.53]前进 前进 前进\n[01:50.80]我们奋斗到底\n[01:58.05]\n[02:28.25]这一首歌 是我们理想的火炬\n[02:36.44]这一首歌 让我们努力奋起\n[02:44.76]这一首歌 给我们光荣与梦想\n[02:52.87]这一首歌 让我们留下难忘的记忆\n[03:01.28]国歌响起 国歌响起\n[03:09.50]你是我们生命的旋律\n[03:17.91]国歌响起 国歌响起\n[03:26.04]为了中华民族的伟大复兴\n[03:30.13]前进 前进 前进\n[03:38.69]我们奋斗到底\n[03:42.75]国歌响起 国歌响起\n[03:50.92]你是我们生命的旋律\n[03:59.23]国歌响起 国歌响起\n[04:07.38]为了中华民族的伟大复兴\n[04:11.50]前进 前进 前进\n[04:20.04]我们奋斗到底\n[04:24.08]我们奋斗到底\n[04:39.47]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2048', '100228', '73987461', 'http://yinyueshiting.baidu.com/data2/music/239124483/239124483.mp3?xcode=6a67bfb9bb67063131189f16455400eb', '', '乌克丽丽', '刘瑞琦', '1471366192', '200', '0', '0', '[00:00.03]乌克丽丽\n[00:00.11]\n[00:00.18]演唱：刘瑞琦\n[00:00.20]\n[00:01.60]你就是海滩下的那乌克丽丽\n[00:07.29]寻找着逆光让暧昧变成剪影\n[00:14.66]浪漫不一定要在那夏威夷\n[00:20.14]沙滩上有你的脚印 是一辈子美景\n[00:34.49]\n[00:37.0]穿花衬衫要配上一把乌克丽丽\n[00:40.34]不然看起来会像流氓兄弟\n[00:43.32]穿西装要记得带个带个女伴\n[00:45.24]不然看起来会像泊车小弟\n[00:48.28]一般我是不会随便就 载着花瓶兜风\n[00:50.60]我不是说你是个 花瓶 你你你别发疯\n[00:53.72]你说你很容易碎 叮咚叮咚\n[00:55.85]让我来保护你 你懂我懂\n[00:58.27]你要我小心你的坏脾气\n[01:00.6]我说我刚好有一点耐心\n[01:02.88]你始终改变不了的任性\n[01:05.44]我当它是一种可爱的个性\n[01:07.57]你就是海滩下的那乌克丽丽\n[01:12.13]寻找着逆光让暧昧变成剪影\n[01:18.23]浪漫不一定要在那夏威夷\n[01:23.18]沙滩上有你的脚印 是一辈子美景\n[01:32.94]\n[01:38.6]别剪短你的发让它飘逸\n[01:41.47]刚好配上他的八块肌\n[01:43.34]别再不吃东西 瘦到不行\n[01:46.5]乌克丽丽在你身上\n[01:47.14]就像一把放大的Guitar\n[01:49.91]迷人不一定要比基尼\n[01:51.22]你的笑容已经非常卡哇伊\n[01:53.87]想要度假不用去到Hawaii\n[01:56.0]椰子树刚好种在我家院子里\n[01:59.13]想要买醉 买醉 买醉 买醉 \n[02:01.63]不一定要喝酒 \n[02:04.64]一起干杯 干杯 干杯 干杯 \n[02:06.23]用音乐来灌醉你 \n[02:08.35]你就是海滩下的那乌克丽丽\n[02:13.62]寻找着逆光让暧昧变成剪影\n[02:18.92]浪漫不一定要在那夏威夷\n[02:23.16]沙滩上有你的脚印 是一辈子美景\n[02:28.97]你就是海滩下的那乌克丽丽\n[02:33.55]寻找着逆光让暧昧变成剪影\n[02:39.92]浪漫不一定要在那夏威夷\n[02:43.40]沙滩上有你的脚印\n[02:46.83]是一辈子美景 \n[02:49.79]你就是海滩下的那乌克丽丽\n[02:53.81]寻找着逆光让暧昧变成剪影\n[02:59.59]浪漫不一定要在那夏威夷\n[03:04.27]沙滩上有你的脚印 是一辈子美景\n[03:09.30]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2052', '100256', '73977596', 'http://yinyueshiting.baidu.com/data2/music/127519082/127519082.mp3?xcode=548ec5c190433cfcbd5cc648288db746', '', '哪吒伴奏', '墨明棋妙', '1471388599', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2053', '100278', '23469909', 'http://yinyueshiting.baidu.com/data2/music/137735411/137735411.mp3?xcode=46ed95fc9b02bd4884da5601d869449c', '', '十年（live）', '刘德华', '1471388707', '400', '0', '0', '[ti:十年（live）]\n[ar:刘德华]\n[al:0]\n[offset:0]\n\n[00:00.00]十年（live）\n[00:03.00]演唱：刘德华\n[00:06.00]\n[00:10.23]曾经爸爸给我望远镜\n[00:15.12]他带我到这段路上\n[00:20.09]去望望地球是那个样\n[00:25.20]幻想就像西瓜波儿胀\n[00:30.34]如果可跳出天上看\n[00:35.34]想见到每人快乐健康\n[00:43.26]年青的风不爱屏与障\n[00:47.98]它带我去反叛浪荡\n[00:53.14]我造造梦儿又说说谎\n[00:57.97]日子慢慢一点一点点流放\n[01:03.32]狂想总教这心头痒\n[01:08.29]当我有翅膀会尽力闯\n[01:16.13]我每一个十年\n[01:20.56]许多难忘的片段\n[01:25.68]当转眼回头望一遍\n[01:31.04]已经很远\n[01:36.28]记那一个十年\n[01:40.17]彷佛无时不见面\n[01:45.88]今天你人儿但不见\n[01:51.29]记忆仍是暖\n[01:56.64]如今心中长了望远镜\n[02:01.50]拉近四野真象幻象\n[02:06.62]我踏实路前面每寸方\n[02:11.43]便一步一步攀上\n[02:16.77]人生可当首歌谣唱\n[02:21.65]总唱到醉人美丽地方\n[02:29.77]我每一个十年\n[02:33.94]许多难忘的片段\n[02:38.76]当转眼回头望一遍\n[02:43.93]已经很远\n[02:49.24]记那一个十年\n[02:53.48]彷佛无时不见面\n[02:58.35]今天你人儿但不见\n[03:03.51]记忆仍是暖\n[03:08.67]我每一个十年\n[03:13.23]许多难忘的片段\n[03:18.23]当转眼回头望一遍\n[03:22.96]已经很远\n[03:28.06]记那一个十年\n[03:32.52]彷佛无时不见面\n[03:37.33]今天你人儿但不见\n[03:42.50]记忆仍是和暖\n[03:50.75]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2054', '100256', '73980777', 'http://yinyueshiting.baidu.com/data2/music/239822798/239822798.mp3?xcode=46ed95fc9b02bd482a1779a2a81a9c08', '', '入戏太深', '李诺欣', '1471388740', '0', '0', '0', '[ti:入戏太深]\n[ar:李诺欣]\n[al:GU-1]\n\n[00:00.00]入戏太深\n[00:02.00]演唱：李诺欣\n[00:05.50]词曲：马旭东\n[00:08.00]\n[00:18.26]是我入戏太深 结局却一个人\n[00:22.71]原地傻傻的等 换不回那温存\n[00:27.02]怪我入戏太深 已变心的灵魂\n[00:31.44]这首歌越唱越觉得残忍\n[00:35.72]是我入戏太深 结局却一个人\n[00:40.03]原地傻傻的等 换不回那温存\n[00:44.43]怪我入戏太深 已变心的灵魂\n[00:48.81]谁能懂那些誓言多伤人\n[00:53.52]\n[00:54.56]你的笑总是装作很天真\n[00:58.94]说我们永远都不会离分\n[01:03.17]空气中弥漫浪漫的气氛\n[01:06.80]随着心跳在升温\n[01:11.94]粉红色长发迷人的嘴唇\n[01:16.23]浅蓝色眼影迷离的眼神\n[01:20.48]现实的你和他路边拥吻\n[01:24.04]看街道上的落英缤纷\n[01:27.65]是我入戏太深 结局却一个人\n[01:32.00]原地傻傻的等 换不回那温存\n[01:36.31]怪我入戏太深 已变心的灵魂\n[01:40.66]这首歌越唱越觉得残忍\n[01:44.92]是我入戏太深 结局却一个人\n[01:49.28]原地傻傻的等 换不回那温存\n[01:53.58]怪我入戏太深 已变心的灵魂\n[01:57.93]谁能懂那些誓言多伤人\n[02:02.62]\n[02:29.74]你的笑总是装作很天真\n[02:34.00]说我们永远都不会离分\n[02:38.35]空气中弥漫浪漫的气氛\n[02:41.92]随着心跳在升温\n[02:46.98]粉红色长发迷人的嘴唇\n[02:51.27]浅蓝色眼影迷离的眼神\n[02:55.58]现实的你和他路边拥吻\n[02:59.17]看街道上的落英缤纷\n[03:02.70]是我入戏太深 结局却一个人\n[03:07.11]原地傻傻的等 换不回那温存\n[03:11.42]怪我入戏太深 已变心的灵魂\n[03:15.74]这首歌越唱越觉得残忍\n[03:20.08]是我入戏太深 结局却一个人\n[03:24.27]原地傻傻的等 换不回那温存\n[03:28.68]怪我入戏太深 已变心的灵魂\n[03:33.03]谁能懂那些誓言多伤人\n[03:37.30]是我入戏太深 结局却一个人\n[03:41.68]原地傻傻的等 换不回那温存\n[03:45.96]怪我入戏太深 已变心的灵魂\n[03:50.34]这首歌越唱越觉得残忍\n[03:54.64]是我入戏太深 结局却一个人\n[03:58.96]原地傻傻的等 换不回那温存\n[04:03.30]怪我入戏太深 已变心的灵魂\n[04:07.64]谁能懂那些誓言多伤人\n[04:12.28]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2055', '100256', '268107689', 'http://yinyueshiting.baidu.com/data2/music/7d9270a65b7428676591d7215156fb25/268107801/268107801.mp3?xcode=2a3a7b04a4049ef63d7cab37c0a62272', '', '我想找个女朋友(Extended Mix)', 'Dj小鱼儿', '1471389060', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2056', '100227', '6418469', 'http://yinyueshiting.baidu.com/data2/music/6418460/6418460.mp3?xcode=28138e3523a9d34c1e0a87f01dbb9fb5', '', '红玫瑰与白玫瑰', '冷漠', '1471389736', '296', '0', '0', '[00:01.50]红玫瑰与白玫瑰\n[00:04.00]作词：玉镯儿 作曲：陈伟\n[00:06.00]演唱：冷漠\n[00:08.00]\n[00:32.52]想着白玫瑰的纯\n[00:36.30]想着她的高贵\n[00:40.30]却又迷恋红玫瑰的娇美\n[00:44.01]摘下那朵白玫瑰\n[00:47.63]红玫瑰的娇艳开在我的心扉\n[00:54.63]让人怎能不想入非非\n[01:00.47]\n[01:01.96]恋着红玫瑰的美\n[01:05.38]恋着她的妩媚\n[01:09.49]却又想着白玫瑰的清纯\n[01:13.08]摘下那朵红玫瑰\n[01:16.68]白玫瑰的皎洁印在我的脑海\n[01:23.54]辗转反侧让人难以入睡\n[01:30.06]\n[01:31.25]红玫瑰白玫瑰\n[01:34.79]都是爱的滋味\n[01:38.39]红玫瑰白玫瑰\n[01:42.10]你心里最爱谁\n[01:45.78]别等到那天花都已经枯萎\n[01:52.95]玫瑰园飘散残留的香味\n[01:58.66]\n[02:00.16]红玫瑰白玫瑰\n[02:03.96]都是爱的滋味\n[02:07.62]红玫瑰白玫瑰\n[02:11.18]你心里最爱谁\n[02:14.77]别等到那天花都已经枯萎\n[02:21.92]最后只剩下眼泪的后悔\n[02:28.44]\n[02:57.96]恋着红玫瑰的美\n[03:01.81]恋着她的妩媚\n[03:05.62]却又想着白玫瑰的清纯\n[03:09.59]摘下那朵红玫瑰\n[03:13.02]白玫瑰的皎洁印在我的脑海\n[03:20.01]辗转反侧让人难以入睡\n[03:25.94]\n[03:27.53]红玫瑰白玫瑰\n[03:31.07]都是爱的滋味\n[03:34.75]红玫瑰白玫瑰\n[03:38.39]你心里最爱谁\n[03:41.98]别等到那天花都已经枯萎\n[03:49.18]玫瑰园飘散残留的香味\n[03:55.00]\n[03:56.51]红玫瑰白玫瑰\n[04:00.26]都是爱的滋味\n[04:03.90]红玫瑰白玫瑰\n[04:07.51]你心里最爱谁\n[04:11.10]别等到那天花都已经枯萎\n[04:18.22]最后只剩下眼泪的后悔\n[04:25.68]最后只剩下眼泪的后悔\n[04:36.04]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2057', '100227', '13949885', 'http://yinyueshiting.baidu.com/data2/music/136340877/136340877.mp3?xcode=a38d330bdb07cd5921ec55f2ef944ac8', '', '可惜不是你', '曹轩宾', '1471392129', '310', '0', '0', '[00:00.00]可惜不是你\n[00:00.96]作词：李焯雄 作曲：曹轩宾 \n[00:04.56]演唱：曹轩宾\n[00:08.73]\n[00:08.75]这一刻 突然觉得好熟悉\n[00:14.67]像昨天 今天同时在放映\n[00:24.16]我这句语气 原来好像你\n[00:30.36]不就是我们爱过的证据\n[00:38.39]\n[00:39.92]差一点 骗了自己骗了你\n[00:46.15]爱与被爱不一定成正比\n[00:53.89]我知道被疼是一种运气\n[01:01.45]但我无法完全交出自己\n[01:08.80]\n[01:10.65]努力为你改变 却变不了\n[01:14.73]预留的伏线\n[01:17.65]以为在你身边 那也算永远\n[01:24.01]仿佛还是昨天\n[01:27.88]可是昨天 已非常遥远\n[01:33.01]但闭上双眼 我还看得见\n[01:40.17]可惜不是你 陪我到最後\n[01:48.83]曾一起走却走失那路口\n[01:56.90]感谢那是你 牵过我的手\n[02:04.15]还能感受那温柔\n[02:11.16]\n[02:39.74]那一段 我们曾心贴着心\n[02:45.74]我想我更有权力关心你\n[02:53.66]可能你 已走进别人风景\n[03:01.17]多希望 也有 星光的投影\n[03:08.30]努力为你改变 却变不了\n[03:13.94]预留的伏线\n[03:16.37]以为在你身边 那也算永远\n[03:23.15]仿佛还是昨天\n[03:26.74]可是昨天 已非常遥远\n[03:31.43]但闭上双眼 我还看得见\n[03:40.03]可惜不是你 陪我到最後\n[03:47.43]曾一起走却走失那路口\n[03:55.11]感谢那是你 牵过我的手\n[04:02.93]还能感受那温柔 哦哦 走失那路口\n[04:25.74]感谢那是你 牵过我的手\n[04:33.01]还能感受那温柔\n[04:40.32]感谢那是你\n[04:45.07]牵过我的手\n[04:48.38]还能温暖我胸口\n[05:02.03]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2058', '100256', '268168628', 'http://yinyueshiting.baidu.com/data2/music/9d0630535ed08bd1470454ea741e3718/268168644/268168644.mp3?xcode=134d063e0102106643e6e1d8249fc792', '', '我想找个小对象', 'MC天佑', '1471395319', '0', '0', '0', '我想找个小对象\n\n演唱：mc天佑\n\n我想找个小对象\n长滴必须要有样还要会喊那说唱\n体格不要那么胖要给我冲QQ币\n还要陪我打游戏不难过也不生气\n和我说点小秘密还要会写那另类\n让我坐上这王位\n会捶腿会敲背\n呐喊天佑万万岁\n挣到钱我给你花\n一起孝敬咱爸\n妈你的眼泪帮你擦\n我们天天过家家\n爱我你就马上火\n会冰会火缩了裹\n走哪都要跟着我\n要给我唱小苹果儿\n陪我一起裸着睡\n啪啪都要喊另类\n说一个道一个\n感觉自己萌萌哒\n还要会写那情书\n必须把我给看哭\n看着我打LOL\n你要给我买皮肤\n勇敢勇敢的执着\n永远都是我老婆\n你把天佑当成佛\n这是你要的生活\n爱过你你从没有在意\n我又想起你身上香水刺鼻\n又怎能忘记那段曾经\n已在你脑海结冰\n小伙长得很优秀\n但我男神李天佑\n爱我你就大声说天佑天佑啵啵啵\n别着急我给你脱\n都说天佑流氓汉\n我是YY流串犯爱我\n一把不泛滥\n为你处女这一战');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2061', '100305', '246825917', 'http://yinyueshiting.baidu.com/data2/music/78dc37485b00547c1e1f66e2f7175482/264552645/264552645.mp3?xcode=8856a86431435b4f25903e2d9e216d08', '', '贝加尔湖畔', '李健', '1471417714', '245', '0', '0', '贝加尔湖畔-李健\n在我的怀里 在你的眼里\n那里春风沉醉 那里绿草如茵\n月光把爱恋 洒满了湖面\n两个人的篝火 照亮整个夜晚\n多少年以后 如云般游走\n那变换的脚步 让我们难牵手\n这一生一世 有多少你我\n被吞没在月光如水的夜里\n多想某一天 往日又重现\n我们流连忘返 在贝加尔湖畔\n多少年以后 往事随云走\n那纷飞的冰雪容不下那温柔\n这一生一世 这时间太少\n不够证明融化冰雪的深情\n就在某一天 你忽然出现\n你清澈又神秘 在贝加尔湖畔\n你清澈又神秘 像贝加尔湖畔');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2062', '100337', '12324218', 'http://yinyueshiting.baidu.com/data2/music/124495175/124495175.mp3?xcode=e0d2c4828d2188c6b50a78e8b32aeef1', '', '笑着难过', '庞龙', '1471419122', '279', '0', '0', '[ti:笑着难过]\n[ar:庞龙]\n[al:摇]\n[by:薰风习习]\n[00:01.00]笑着难过\n[00:05.00]作词：二水 作曲：韩雷\n[00:10.00]演唱：庞龙\n[00:14.00]\n[00:28.07]爱 是对还是错 别计较结果\n[00:36.89]终于走到分手这段路\n[00:42.05]我 已无话可说 藏起了难过\n[00:50.73]低头告别 让我们好过\n[00:56.00]爱 像急流穿越我心头\n[01:02.72]这一刻 冲刷着所有\n[01:09.86]爱 失去 也是种解脱\n[01:16.45]有太多的感动 太多曲折的痛\n[01:23.96]\n[01:25.39]爱过恨过 笑着难过\n[01:32.42]是与非掠过 潮起潮落 我难过也要过\n[01:39.38]伤过 痛过 笑着难过\n[01:46.28]风雨都经过 忘记比你多 才好过\n[01:54.86]\n[01:58.44]爱 没人能看破 泛滥的寂寞\n[02:07.17]眼泪不知 在哪里停泊\n[02:12.45]你 不能再爱我 至少忘记我\n[02:21.09]逃离旋涡 你会更好的\n[02:26.20]爱 像急流穿越我心头\n[02:32.98]这一刻 冲刷着所有\n[02:40.26]爱 失去 也是种解脱\n[02:47.10]有太多的感动 太多曲折的痛\n[02:54.07]爱过恨过 笑着难过\n[03:00.80]是与非掠过 潮起潮落 我难过也要过\n[03:08.12]伤过 痛过 笑着难过\n[03:14.88]风雨都经过 忘记比你多 才好过\n[03:23.60]\n[03:37.57]爱过恨过 笑着难过\n[03:44.40]是与非掠过 潮起潮落 我难过也要过\n[03:51.50]伤过 痛过 笑着难过\n[03:58.32]风雨都经过 忘记比你多 才好过\n[04:07.55]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2063', '100235', '85065229', 'http://yinyueshiting.baidu.com/data2/music/123027717/123027717.mp3?xcode=8c987ae38a296b4eec0a60cf41a4a187', '', '国歌', '彭丽媛', '1471452799', '294', '0', '0', '[ti:]\n[ar:]\n[al:]\n[offset:0]\n\n[00:02.40]国歌\n[00:05.58]演唱：彭丽媛\n[00:09.31]\n[00:40.57]这一首歌 用我们的热血谱曲\n[00:48.73]这一首歌 用我们的挚爱唱起\n[00:57.02]这一首歌 在我们的心里燃烧\n[01:05.21]这一首歌 激励我们走过风雨\n[01:13.84]国歌响起 国歌响起\n[01:21.76]你是我们生命的旋律\n[01:30.23]国歌响起 国歌响起\n[01:38.48]为了中华民族的伟大复兴\n[01:42.53]前进 前进 前进\n[01:50.80]我们奋斗到底\n[01:58.05]\n[02:28.25]这一首歌 是我们理想的火炬\n[02:36.44]这一首歌 让我们努力奋起\n[02:44.76]这一首歌 给我们光荣与梦想\n[02:52.87]这一首歌 让我们留下难忘的记忆\n[03:01.28]国歌响起 国歌响起\n[03:09.50]你是我们生命的旋律\n[03:17.91]国歌响起 国歌响起\n[03:26.04]为了中华民族的伟大复兴\n[03:30.13]前进 前进 前进\n[03:38.69]我们奋斗到底\n[03:42.75]国歌响起 国歌响起\n[03:50.92]你是我们生命的旋律\n[03:59.23]国歌响起 国歌响起\n[04:07.38]为了中华民族的伟大复兴\n[04:11.50]前进 前进 前进\n[04:20.04]我们奋斗到底\n[04:24.08]我们奋斗到底\n[04:39.47]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2064', '100352', '33984572', 'http://yinyueshiting.baidu.com/data2/music/134370493/134370493.mp3?xcode=93e7b588593a878be977ca24e3730051', '', '稳稳的幸福', '陈奕迅', '1471462603', '0', '0', '0', '[00:00.02]稳稳的幸福\n[00:00.61]词曲：小柯\n[00:01.33]演唱：陈奕迅\n[00:01.70]\n[00:03.80]有一天 我发现自怜资格都已没有\n[00:11.34]只剩下不知疲倦的肩膀\n[00:15.17]担负着简单的满足\n[00:19.31]有一天 开始从平淡日子感受快乐\n[00:27.12]看到了明明白白的远方\n[00:31.56]我要的幸福\n[00:34.90]我要稳稳的幸福\n[00:38.70]能抵挡末日的残酷\n[00:43.57]在不安的深夜\n[00:46.64]能有个归宿\n[00:50.86]我要稳稳的幸福\n[00:54.87]能用双手去碰触\n[00:59.30]每次伸手入怀中\n[01:02.70]有你的温度\n[01:08.39]\n[01:39.71]有一天 我发现自怜资格都已没有\n[01:47.18]只剩下不知疲倦的肩膀\n[01:51.13]担负着简单的满足\n[01:55.45]有一天 开始从平淡日子感受快乐\n[02:03.14]看到了明明白白的远方\n[02:07.35]我要的幸福\n[02:10.94]我要稳稳的幸福\n[02:14.69]能抵挡末日的残酷\n[02:19.61]在不安的深夜\n[02:22.64]能有个归宿\n[02:26.86]我要稳稳的幸福\n[02:30.85]能用双手去碰触\n[02:35.28]每次伸手入怀中\n[02:38.64]有你的温度\n[02:42.88]我要稳稳的幸福\n[02:46.65]能抵挡失落的痛楚\n[02:51.67]一个人的路途\n[02:54.63]也不会孤独\n[02:58.90]我要稳稳的幸福\n[03:02.79]能用生命做长度\n[03:07.38]无论我身在何处\n[03:10.60]都不会迷途\n[03:14.84]我要稳稳的幸福\n[03:23.60]这是我想要的幸福\n[03:33.16]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2065', '100352', '369536', 'http://yinyueshiting.baidu.com/data2/music/125221958/125221958.mp3?xcode=19e0584ab5b9d377539f0e82e788ec87', '', '你的背包', '陈奕迅', '1471462616', '0', '0', '0', '[00:02.50]你的背包\n[00:04.50]作词：林夕 作曲：蔡政\n[00:06.50]演唱：陈奕迅\n[00:08.50]\n[00:23.71]一九九五年 我们在机场的车站\n[00:30.76]你借我 而我不想归还\n[00:37.71]那个背包载满纪念品和患难\n[00:45.74]还有摩擦留下的图案\n[00:51.83]\n[00:53.17]你的背包 背到现在还没烂\n[01:01.02]却成为我身体另一半\n[01:08.11]千金不换 它已熟悉我的汗\n[01:16.29]它是我肩膀上的指环\n[01:21.02]\n[01:28.69]背了六年半 我每一天陪它上班\n[01:35.23]你借我 我就为你保管\n[01:42.15]我的朋友都说它旧得很好看\n[01:50.27]遗憾是它已与你无关\n[01:56.14]\n[01:57.23]你的背包 让我走得好缓慢\n[02:05.09]总有一天陪着我腐烂\n[02:12.54]你的背包 对我沉重的审判\n[02:20.89]借了东西为什么不还\n[02:25.63]\n[02:50.76]你的背包让我走得好缓慢\n[02:58.64]总有一天陪着我腐烂\n[03:05.61]你的背包对我沉重的审判\n[03:14.57]借了东西为什么不还\n[03:19.28]\n[03:22.09]借了东西为什么 不还\n[03:30.91]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2066', '100353', '74037210', 'http://yinyueshiting.baidu.com/data2/music/242092599/242092599.mp3?xcode=1eb5eceb0c8b89a843e6e1d8249fc792', '', '蒋蒋', 'MC法老', '1471465466', '204', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2067', '100331', '268168540', 'http://yinyueshiting.baidu.com/data2/music/f349c1c074e7b355087118c280d96217/268168556/268168556.mp3?xcode=56167f65e7cb90984394f800f7f2550e', '', '十年戎马心孤单', 'MC天佑', '1471479833', '0', '0', '0', '十年戎马心孤单\n\n演唱：mc天佑\n\n十年戎马的心孤单\n隐退了江湖归深山\n如果有天你难堪\n挂帅出征再扬帆\n既然那疆场你已输\n我怎还能继续哭\n召集三千那勇者夫\n那么血洗金銮捣皇都\n北斗七星八卦阵\n忘却了红尘爱或恨\n爱或恨被情困\n只故心中太苦闷\n风云变幻天地搬\n嗜血那魔剑破天翻\n名与利有何干\n早已归隐深山\n一天两类套套词\n不管对错值不值\n为了在我辉煌时\n那么犹如骏马在奔驰\n骏马奔驰多豪迈\n另类的喊歌多痛快\n霸主君王这一代\n那么巅峰另类从未败\n另类破腔踏五关\n直抵那巅峰凌云山\n麒麟咆哮猛虎吹\n那么喊破三界的深渊\n凤凰展翅龙摇摆\n凤凰另类荡大海\n一生戎马把命改\n那么凤展翅我摇尾\n神雀烈煞斗厉鬼\n仙人指路向西北\n出征挂念那家人美\n那么久别战场向仙问');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2068', '100331', '10494295', 'http://yinyueshiting.baidu.com/data2/music/f7588ac96189b952efabaf19a1822bb3/10495650/10495650.mp3?xcode=56167f65e7cb90983cbb1604e4436b22', '', '两只老虎', '小蓓蕾组合', '1471479881', '0', '0', '0', '[00:00.50]两只老虎\n[00:02.07]作词：王建平 作曲：佚名\n[00:04.77]演唱：小蓓蕾组合\n[00:06.89]\n[00:08.00]两只老虎两只老虎 跑得快跑得快\n[00:14.76]一只没有耳朵 一只没有尾巴\n[00:18.35]真奇怪 真奇怪\n[00:21.92]两只老虎两只老虎 跑得快跑得快\n[00:28.96]一只没有耳朵 一只没有尾巴\n[00:32.54]真奇怪 真奇怪\n[00:35.48]\n[00:43.42]两只老虎两只老虎 跑得快跑得快\n[00:50.20]一只没有耳朵 一只没有尾巴\n[00:53.87]真奇怪 真奇怪\n[00:57.35]两只老虎两只老虎 跑得快跑得快\n[01:04.43]一只没有耳朵 一只没有尾巴\n[01:08.02]真奇怪 真奇怪\n[01:10.98]\n[01:18.82]两只老虎两只老虎 跑得快跑得快\n[01:25.76]一只没有耳朵 一只没有尾巴\n[01:29.26]真奇怪 真奇怪\n[01:32.78]两只老虎两只老虎 跑得快跑得快\n[01:39.91]一只没有耳朵 一只没有尾巴\n[01:43.48]真奇怪 真奇怪\n[01:46.24]\n[01:54.46]两只老虎两只老虎 跑得快跑得快\n[02:01.21]一只没有耳朵 一只没有尾巴\n[02:05.08]真奇怪 真奇怪\n[02:08.58]两只老虎两只老虎 跑得快跑得快\n[02:15.42]一只没有耳朵 一只没有尾巴\n[02:18.98]真奇怪 真奇怪\n[02:22.28]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2069', '100368', '38240140', 'http://yinyueshiting.baidu.com/data2/music/239844937/239844937.mp3?xcode=de11bacc13adebac48ae5c20176c0886', '', 'Mascara', 'G.E.M.邓紫棋', '1471492793', '0', '0', '0', '[ti:Mascara (烟燻妆)]\n[ar:邓紫棋]\n[al:]\n\n[00:00.22]邓紫棋 - Mascara (烟燻妆)\n[00:08.09]\n[00:09.57]作曲：G.E.M  填词：G.E.M.  Aniefann\n[00:10.87]\n[00:15.72]被欺骗算什麽 早已习惯难过\n[00:23.55]眼神空 眼眶红 但记得别过执着\n[00:33.29]寂静无声的我 还能够说什麽\n[00:41.55]眼神憔悴脆弱 用烟燻妆来盖过\n[00:50.15]\n[00:51.57]玫瑰都在淌血 它沾污了白雪\n[00:59.46]有谁想要了解 心如刀割的感觉\n[01:07.45]你等着我 解释为何 微笑中带泪\n[01:15.28]卸了妆 却忘了我是谁\n[01:22.19]\n[01:22.91]我用尽了力气 想要留住你 你却没会意\n[01:28.97]你的坚决让我最後不得不放弃\n[01:33.10]看进我眼里 黑色的眼泪流着不停\n[01:38.92]你说你从不信 从来不在意 假装的生气\n[01:45.02]我恨这样才能抓住你的注意力\n[01:48.83]女生的哭泣 它是常被误会的心机\n[01:54.84]对不起 其实你对我不熟悉\n[02:02.93]答应你 自由我从此给你\n[02:10.31]\n[02:11.38]躲进你的生活 想占据某角落\n[02:19.55]但愈小的异国 愈容易遭到封锁\n[02:28.27]\n[02:29.44]玫瑰都在淌血 它沾污了白雪\n[02:37.48]有谁想要了解 心如刀割的感觉\n[02:45.29]你等着我 解释为何微笑中带泪\n[02:53.36]卸了妆 却忘了我是谁\n[03:00.45]\n[03:01.02]我用尽了力气 想要留住你 你却没会意\n[03:06.99]你的坚决 让我最後不得不放弃\n[03:10.86]看进我眼里 黑色的眼泪流着不停\n[03:16.88]你说你从不信 从来不在意 假装的生气\n[03:22.95]我恨这样才能抓住你的注意力\n[03:27.06]女生的哭泣 它是常被误会的心机\n[03:32.96]对不起 其实你对我不熟悉\n[03:40.72]答应你 自由我从此给你\n[03:48.61]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2070', '100288', '38240140', 'http://yinyueshiting.baidu.com/data2/music/239844937/239844937.mp3?xcode=ad1a2898b258afca84b505fb6b038464', '', 'Mascara', 'G.E.M.邓紫棋', '1471493258', '0', '0', '0', '[ti:Mascara (烟燻妆)]\n[ar:邓紫棋]\n[al:]\n\n[00:00.22]邓紫棋 - Mascara (烟燻妆)\n[00:08.09]\n[00:09.57]作曲：G.E.M  填词：G.E.M.  Aniefann\n[00:10.87]\n[00:15.72]被欺骗算什麽 早已习惯难过\n[00:23.55]眼神空 眼眶红 但记得别过执着\n[00:33.29]寂静无声的我 还能够说什麽\n[00:41.55]眼神憔悴脆弱 用烟燻妆来盖过\n[00:50.15]\n[00:51.57]玫瑰都在淌血 它沾污了白雪\n[00:59.46]有谁想要了解 心如刀割的感觉\n[01:07.45]你等着我 解释为何 微笑中带泪\n[01:15.28]卸了妆 却忘了我是谁\n[01:22.19]\n[01:22.91]我用尽了力气 想要留住你 你却没会意\n[01:28.97]你的坚决让我最後不得不放弃\n[01:33.10]看进我眼里 黑色的眼泪流着不停\n[01:38.92]你说你从不信 从来不在意 假装的生气\n[01:45.02]我恨这样才能抓住你的注意力\n[01:48.83]女生的哭泣 它是常被误会的心机\n[01:54.84]对不起 其实你对我不熟悉\n[02:02.93]答应你 自由我从此给你\n[02:10.31]\n[02:11.38]躲进你的生活 想占据某角落\n[02:19.55]但愈小的异国 愈容易遭到封锁\n[02:28.27]\n[02:29.44]玫瑰都在淌血 它沾污了白雪\n[02:37.48]有谁想要了解 心如刀割的感觉\n[02:45.29]你等着我 解释为何微笑中带泪\n[02:53.36]卸了妆 却忘了我是谁\n[03:00.45]\n[03:01.02]我用尽了力气 想要留住你 你却没会意\n[03:06.99]你的坚决 让我最後不得不放弃\n[03:10.86]看进我眼里 黑色的眼泪流着不停\n[03:16.88]你说你从不信 从来不在意 假装的生气\n[03:22.95]我恨这样才能抓住你的注意力\n[03:27.06]女生的哭泣 它是常被误会的心机\n[03:32.96]对不起 其实你对我不熟悉\n[03:40.72]答应你 自由我从此给你\n[03:48.61]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2071', '100288', '243155881', 'http://yinyueshiting.baidu.com/data2/music/243155950/243155950.mp3?xcode=ec087db9239ee76b2bb21879f3eb947d', '', '小幸运', '田馥甄', '1471498218', '0', '0', '0', '[00:03.00]小幸运\n[00:06.01]\n[00:09.41]作词：徐世珍，吴辉福 作曲：JerryC\n[00:12.55]演唱：田馥甄\n[00:14.78]\n[00:16.00]我听见雨滴落在青青草地\n[00:21.87]我听见远方下课钟声响起\n[00:27.87]可是我没有听见你的声音\n[00:32.98]认真 呼唤我姓名\n[00:40.09]爱上你的时候还不懂感情\n[00:46.15]离别了才觉得刻骨 铭心\n[00:52.19]为什么没有发现遇见了你\n[00:56.70]是生命最好的事情\n[01:02.52]也许当时忙着微笑和哭泣\n[01:08.50]忙着追逐天空中的流星\n[01:14.26]人理所当然的忘记\n[01:18.79]是谁风里雨里一直默默守护在原地\n[01:26.52]原来你是我最想留住的幸运\n[01:31.73]原来我们和爱情曾经靠得那么近\n[01:37.74]那为我对抗世界的决定\n[01:42.30]那陪我淋的雨\n[01:45.53]一幕幕都是你 一尘不染的真心\n[01:52.79]与你相遇 好幸运\n[01:56.41]可我已失去为你泪流满面的权利\n[02:02.00]但愿在我看不到的天际\n[02:06.61]你张开了双翼\n[02:09.68]遇见你的注定 (oh--)\n[02:14.57]她会有多幸运\n[02:20.54]\n[02:29.54]青春是段跌跌撞撞的旅行\n[02:35.47]拥有着后知后觉的美丽\n[02:41.58]来不及感谢是你给我勇气\n[02:46.15]让我能做回我自己\n[02:51.93]也许当时忙着微笑和哭泣\n[02:57.81]忙着追逐天空中的流星\n[03:03.52]人理所当然的忘记\n[03:08.14]是谁风里雨里一直默默守护在原地\n[03:16.00]原来你是我最想留住的幸运\n[03:21.04]原来我们和爱情曾经靠得那么近\n[03:27.10]那为我对抗世界的决定\n[03:31.87]那陪我淋的雨\n[03:34.64]一幕幕都是你 一尘不染的真心\n[03:42.24]与你相遇 好幸运\n[03:45.27]可我已失去为你泪流满面的权利\n[03:51.42]但愿在我看不到的天际\n[03:55.94]你张开了双翼\n[03:59.21]遇见你的注定 (oh--)\n[04:06.83]她会有多幸运\n[04:11.26]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2072', '100227', '85028268', 'http://yinyueshiting.baidu.com/data2/music/123263874/123263874.mp3?xcode=21e3d72703b0c390f5d44a3a1acfff98', '', '可惜不是你', '杨宗纬', '1471539674', '283', '0', '0', '[ti:0]\n[ar:0]\n[al:0]\n[offset:0]\n\n[00:00.33]可惜不是你\n[00:00.85]作词：李焯雄 作曲：曹轩宾\n[00:01.41]演唱：杨宗纬\n[00:02.00]\n[00:37.68]这一刻突然觉得好熟悉\n[00:45.08]像昨天今天同时在放映\n[00:51.73]我这句语气原来好像你\n[00:58.35]不就是我们爱过的证据\n[01:04.66]差一点骗了自己骗了你\n[01:11.01]爱与被爱不一定成正比\n[01:17.66]我知道被疼是一种运气\n[01:24.05]但我无法完全交出自己\n[01:30.55]努力为你改变却变不了\n[01:33.82]预留的伏线\n[01:36.68]以为在你身边那也算永远\n[01:43.38]仿佛还是昨天\n[01:45.13]可是昨天已非常遥远\n[01:49.84]但闭上双眼我还看得见\n[01:56.79]可惜不是你陪我到最後\n[02:03.43]曾一起走却走失那路口\n[02:09.90]感谢那是你牵过我的手\n[02:16.48]还能感受那温柔\n[02:22.85]那一段我们曾心贴着心\n[02:27.61]曾一起走却走失那路口\n[02:29.30]我想我更有权力关心你\n[02:35.87]可能你已走进别人风景\n[02:42.33]多希望也有星光的投影\n[02:48.64]努力为你改变却变不了\n[02:51.95]预留的伏线\n[02:54.92]以为在你身边那也算永远\n[03:01.36]仿佛还是昨天\n[03:03.20]可是昨天已非常遥远\n[03:08.05]但闭上双眼我还看得见\n[03:14.82]可惜不是你陪我到最後\n[03:21.39]曾一起走却走失那路口\n[03:27.88]感谢那是你牵过我的手\n[03:34.42]还能感受那温柔\n[03:41.00]可惜不是你陪我到最後\n[03:47.38]曾一起走却走失那路口\n[03:53.85]感谢那是你牵过我的手\n[04:00.39]还能感受那温柔\n[04:06.95]感谢那是你牵过我的手\n[04:13.56]还能温暖我胸口\n[04:24.25]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2073', '100227', '243155881', 'http://yinyueshiting.baidu.com/data2/music/243155950/243155950.mp3?xcode=24853cd896fc01a85990d376263109e0', '', '小幸运', '田馥甄', '1471539720', '265', '0', '0', '[00:03.00]小幸运\n[00:06.01]\n[00:09.41]作词：徐世珍，吴辉福 作曲：JerryC\n[00:12.55]演唱：田馥甄\n[00:14.78]\n[00:16.00]我听见雨滴落在青青草地\n[00:21.87]我听见远方下课钟声响起\n[00:27.87]可是我没有听见你的声音\n[00:32.98]认真 呼唤我姓名\n[00:40.09]爱上你的时候还不懂感情\n[00:46.15]离别了才觉得刻骨 铭心\n[00:52.19]为什么没有发现遇见了你\n[00:56.70]是生命最好的事情\n[01:02.52]也许当时忙着微笑和哭泣\n[01:08.50]忙着追逐天空中的流星\n[01:14.26]人理所当然的忘记\n[01:18.79]是谁风里雨里一直默默守护在原地\n[01:26.52]原来你是我最想留住的幸运\n[01:31.73]原来我们和爱情曾经靠得那么近\n[01:37.74]那为我对抗世界的决定\n[01:42.30]那陪我淋的雨\n[01:45.53]一幕幕都是你 一尘不染的真心\n[01:52.79]与你相遇 好幸运\n[01:56.41]可我已失去为你泪流满面的权利\n[02:02.00]但愿在我看不到的天际\n[02:06.61]你张开了双翼\n[02:09.68]遇见你的注定 (oh--)\n[02:14.57]她会有多幸运\n[02:20.54]\n[02:29.54]青春是段跌跌撞撞的旅行\n[02:35.47]拥有着后知后觉的美丽\n[02:41.58]来不及感谢是你给我勇气\n[02:46.15]让我能做回我自己\n[02:51.93]也许当时忙着微笑和哭泣\n[02:57.81]忙着追逐天空中的流星\n[03:03.52]人理所当然的忘记\n[03:08.14]是谁风里雨里一直默默守护在原地\n[03:16.00]原来你是我最想留住的幸运\n[03:21.04]原来我们和爱情曾经靠得那么近\n[03:27.10]那为我对抗世界的决定\n[03:31.87]那陪我淋的雨\n[03:34.64]一幕幕都是你 一尘不染的真心\n[03:42.24]与你相遇 好幸运\n[03:45.27]可我已失去为你泪流满面的权利\n[03:51.42]但愿在我看不到的天际\n[03:55.94]你张开了双翼\n[03:59.21]遇见你的注定 (oh--)\n[04:06.83]她会有多幸运\n[04:11.26]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2074', '100373', '247215689', 'http://yinyueshiting.baidu.com/data2/music/247218159/247218159.mp3?xcode=989b90b0bc4b377bc7d4e558c0a93524', '', '有多少爱可以重来', '张靓颖,韩庚', '1471547593', '0', '0', '0', '[00:00.05]有多少爱可以重来\n[00:00.23]\n[00:00.49]演唱者:张靓颖,韩庚(影视原声)\n[00:00.69]\n[00:00.81]男：常常责怪自己\n[00:02.99]\n[00:03.76]当初不应该\n[00:06.29]\n[00:09.40]常常后悔没能\n[00:11.84]\n[00:12.59]把你留下来\n[00:15.60]\n[00:16.25]女：为什么明明相爱\n[00:21.09]\n[00:21.96]到最后还是会分开\n[00:25.83]\n[00:26.46]是否我们总是\n[00:28.65]\n[00:29.32]徘徊在幸福之外\n[00:32.48]\n[00:33.92]男：谁知道又和你\n[00:39.42]相遇在人海\n[00:42.41]\n[00:45.72]命运如此安排\n[00:48.44]总有它精彩\n[00:51.74]\n[00:52.40]女：这些年过得不好不坏\n[00:57.45]\n[00:58.16]只是知道少了一个人存在\n[01:02.13]\n[01:02.73]而我渐渐明白\n[01:05.23]你仍然是我不变的关怀\n[01:09.61]\n[01:11.42]男：有多少爱可以重来\n[01:17.70]\n[01:18.23]有多少人愿意等待\n[01:22.17]女：当懂得珍惜以后归来\n[01:25.95]却不知那份爱\n[01:28.15]\n[01:28.73]会不会还在\n[01:31.37]男：有多少爱可以重来\n[01:35.65]\n[01:36.36]有多少人值得等待\n[01:40.45]女：当世界已经桑田沧海\n[01:44.04]是否还有勇气去爱\n[01:48.16]\n[01:53.67]谁知道又和你\n[01:55.92]\n[01:56.48]相遇在人海\n[01:59.12]\n[02:02.69]命运如此安排\n[02:05.52]总有它精彩\n[02:08.21]\n[02:08.97]男：这些年过得不好不坏\n[02:14.35]\n[02:15.11]只是知道少了一个人存在\n[02:19.03]\n[02:19.67]而我渐渐明白\n[02:22.27]你仍然是我不变的关怀\n[02:26.89]\n[02:28.51]有多少爱可以重来\n[02:32.42]\n[02:33.03]有多少人愿意等待\n[02:36.84]女：当懂得珍惜以后归来\n[02:40.61]却不知那份爱\n[02:43.43]会不会还在\n[02:45.49]男：有多少爱可以重来\n[02:50.37]\n[02:51.13]有多少人值得等待\n[02:55.10]女：当世界已经桑田沧海\n[02:58.71]是否还有勇气去爱\n[03:03.03]合：当世界已经桑田沧海\n[03:08.44]是否还有勇气去爱\n[03:15.69]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2075', '100376', '402244', 'http://yinyueshiting.baidu.com/data2/music/124333041/124333041.mp3?xcode=bd7a455e5390502ebdfb5657fcf2438e', '', '十七岁的雨季', '林志颖', '1471547752', '0', '0', '0', '[00:01.07]十七岁的雨季\n[00:02.95]演唱：林志颖\n[00:04.94]\n[00:36.52]当我还是小孩子\n[00:39.68]门前有许多的茉莉花\n[00:43.19]散发着淡淡的清香\n[00:47.44]\n[00:50.68]当我渐渐地长大\n[00:53.68]门前的那些茉莉花\n[00:57.18]已经慢慢地枯萎不再萌芽\n[01:04.63]什么样的心情\n[01:09.58]什么样的年纪\n[01:12.91]什么样的欢愉\n[01:16.39]什么样的哭泣\n[01:20.99]\n[01:24.09]十七岁那年的雨季\n[01:27.03]我们有共同的期许\n[01:30.49]也曾经紧紧拥抱在一起\n[01:36.69]\n[01:38.03]十七岁那年的雨季\n[01:41.14]回忆起童年的点点滴滴\n[01:44.65]却发现成长已慢慢接近\n[01:50.09]\n[01:52.19]十七岁那年的雨季\n[01:55.12]我们有共同的期许\n[01:58.58]也曾经紧紧拥抱在一起\n[02:06.12]十七岁那年的雨季\n[02:09.25]回忆起童年的点点滴滴\n[02:12.88]却发现成长已慢慢接近\n[02:18.97]\n[02:48.99]当我还是小孩子\n[02:53.26]门前有许多的茉莉花\n[02:56.87]散发着淡淡的清香\n[03:01.18]\n[03:04.38]当我渐渐地长大\n[03:07.50]门前的那些茉莉花\n[03:10.97]已经慢慢地枯萎不再萌芽\n[03:18.37]什么样的心情\n[03:23.21]什么样的年纪\n[03:26.67]什么样的欢愉\n[03:30.18]什么样的哭泣\n[03:34.61]\n[03:37.77]十七岁那年的雨季\n[03:40.76]我们有共同的期许\n[03:44.30]也曾经紧紧拥抱在一起\n[03:48.22]\n[03:51.81]十七岁那年的雨季\n[03:54.91]回忆起童年的点点滴滴\n[03:58.41]却发现成长已慢慢接近\n[04:03.07]\n[04:05.93]十七岁那年的雨季\n[04:08.86]我们有共同的期许\n[04:12.43]也曾经紧紧拥抱在一起\n[04:16.51]\n[04:19.92]十七岁那年的雨季\n[04:22.99]回忆起童年的点点滴滴\n[04:26.52]却发现成长已慢慢接近\n[04:32.92]\n[04:35.77]十七岁那年的雨季\n[04:38.84]我们有共同的期许\n[04:42.31]也曾经紧紧拥抱在一起\n[04:46.47]\n[04:49.88]十七岁那年的雨季\n[04:52.83]回忆起童年的点点滴滴\n[04:56.57]却发现成长已慢慢接近\n[05:04.93]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2076', '100334', '23219346', 'http://yinyueshiting.baidu.com/data2/music/123656052/123656052.mp3?xcode=288312258c1cb78923407a79428c3f0f', '', '美丽的神话', '韩红,孙楠', '1471561164', '292', '0', '0', '[00:04.70]美丽的神话\n[00:06.90]演唱：孙楠、韩红\n[00:09.10]\n[00:18.80]梦中人 熟悉的脸孔\n[00:25.90]你是我守候的温柔\n[00:33.30]就算泪水淹没天地\n[00:40.70]我不会放手\n[00:48.30]每一刻孤独的承受\n[00:55.30]只因我曾许下承诺\n[01:03.00]你我之间熟悉的感动\n[01:10.15]爱就要苏醒\n[01:16.90]万世沧桑唯有爱是永远的神话\n[01:24.05]潮起潮落始终不悔真爱的相约\n[01:31.15]几番苦痛的纠缠多少黑夜挣扎\n[01:38.95]紧握双手让我和你再也不离分\n[01:47.72]\n[02:02.00]枕上雪 冰封的爱恋\n[02:09.30]真心相拥才能融解\n[02:16.80]风中摇曳炉上的火\n[02:24.05]不灭亦不休\n[02:31.75]等待花开春去春又来\n[02:39.05]无情岁月笑我痴狂\n[02:46.15]心如钢铁任世界荒芜\n[02:53.65]思念永相随\n[03:00.25]万世沧桑唯有爱是永远的神话\n[03:07.15]潮起潮落始终不悔真爱的相约\n[03:14.80]几番苦痛的纠缠多少黑夜挣扎\n[03:22.00]紧握双手让我和你再也不离分\n[03:29.60]悲欢岁月唯有爱是永远的神话\n[03:37.00]谁都没有遗忘古老古老的誓言\n[03:44.50]你的泪水化为漫天飞舞的彩蝶\n[03:51.85]爱是翼下之风两心相随自在飞\n[03:59.05]悲欢岁月唯有爱是永远的神话\n[04:06.60]谁都没有遗忘古老古老的誓言\n[04:14.00]你的泪水化为漫天飞舞的彩蝶\n[04:21.35]爱是翼下之风两心相随自在飞\n[04:31.60]你是我心中唯一美丽的神话\n[04:44.67]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2077', '100385', '131037346', 'http://yinyueshiting.baidu.com/data2/music/131037523/131037523.mp3?xcode=2ab72983892beba67ed60eb536e08c87', '', '焚情', '张信哲', '1471562564', '247', '0', '0', '[00:02.27]焚情\n[00:05.18]演唱：张信哲\n[00:09.53]\n[00:19.83]不争爱得有结果\n[00:24.6]被你 夺走永远也不留\n[00:31.30]感情的包袱 复杂又沉重\n[00:36.53]好不了的伤 隐隐作痛\n[00:43.61]赢了什么算拥有\n[00:48.19]坦白 总会输给了沉默\n[00:56.12]一遍遍 情绪汹涌的挣脱\n[01:01.64]追逐 和错过 谁能看透\n[01:11.44]月圆月缺 看尽 谁的愁\n[01:17.13]我在承受 你不懂 谁都没赢过\n[01:24.11]潮起潮落 涌进了谁的忧\n[01:29.45]你不放手 我逗留 争到什么\n[01:36.90]\n[01:48.59]赢了什么算拥有\n[01:53.56]坦白 总会输给了沉默\n[02:01.37]一遍遍 情绪汹涌的挣脱\n[02:05.97]追逐 和错过 谁能看透\n[02:12.72]\n[02:16.40]月圆月缺 看尽 谁的愁\n[02:21.46]我在承受 你不懂 谁都没赢过\n[02:28.19]潮起潮落 涌进了谁的忧\n[02:33.90]你不放手 我逗留 争到什么\n[02:41.74]\n[03:07.59]月圆月缺 看尽 谁的愁\n[03:11.20]我在承受 你不懂 谁都没赢过\n[03:19.86]潮起潮落 涌进了谁的忧\n[03:23.54]你不放手 我逗留 争到什么\n[03:31.31]月圆月缺 爱恨几时罢休\n[03:36.6]岁月穿梭 我的梦 没有尽头\n[03:45.82]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2078', '100392', '14470743', 'http://yinyueshiting.baidu.com/data2/music/92ed7e5647121fd1bdc1adb5195be99b/266525259/266525259.mp3?xcode=b1694a42e8ff17995fa4084c7da1aa86', '', '好日子', '宋祖英', '1471566580', '172', '0', '0', '[00:02.59]好日子\n[00:04.99]作词：车行 作曲：李昕\n[00:06.27]演唱：宋祖英\n[00:08.93]\n[00:18.29]开心的锣鼓敲出年年的喜庆\n[00:24.98]好看的舞蹈送来天一原欢腾\n[00:31.72]阳光的油彩涂红了今天的日子哟\n[00:38.38]生活的花朵是我们的笑容\n[00:44.01]\n[00:48.60]今天是个好日子\n[00:51.83]心想的事儿都能成\n[00:55.26]明天是个好日子\n[00:58.26]打开了家门咱迎春风\n[01:08.76]\n[01:25.53]门外的灯笼露出红红的光景\n[01:32.22]好听的歌儿传达浓浓的深情\n[01:38.99]月光的水彩涂亮明天的日子哟\n[01:45.50]美好的世界在我们的心中\n[01:51.32]\n[01:55.66]今天都是好日子\n[01:58.86]千金的光阴不能等\n[02:02.43]明天又是好日子\n[02:05.28]赶上了盛世咱享太平\n[02:15.78]今天是个好日子\n[02:19.05]心想的事儿都能成\n[02:22.47]明天又是好日子\n[02:25.82]千金的光阴不能等\n[02:29.17]今天明天都是好日子\n[02:32.21]赶上了盛世咱享太平\n[02:44.77]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2079', '100399', '299465', 'http://yinyueshiting.baidu.com/data2/music/123348877/123348877.mp3?xcode=9ce770aca1769e59bd5cc648288db746', '', '会有那么一天', '林俊杰', '1471596343', '248', '0', '0', '[ti:会有那么一天]\n[ar:林俊杰]\n[al:乐行者]\n\n[00:01.55]会有那么一天\n[00:05.55]作词：张思尔 作曲：林俊杰\n[00:10.55]演唱：林俊杰\n[00:15.55]\n[00:35.48]一九四三 世界大战\n[00:39.11]阿嬷年轻的时候\n[00:42.72]爷爷爱他那么多\n[00:46.36]他们感情很深\n[00:49.10]\n[00:49.80]当时爷爷 身负重任\n[00:53.50]就在离乡的那夜\n[00:57.04]给了阿嬷一个吻\n[01:00.55]轻声说到\n[01:03.79]\n[01:06.32]我要离去 别再哭泣\n[01:09.59]不要伤心 请你相信我\n[01:14.23]要等待 我的爱\n[01:17.79]陪你永不离开\n[01:20.54]因为会有那么一天\n[01:24.09]我们牵着手在草原\n[01:27.83]听 鸟儿歌唱的声音\n[01:32.88]听我说声 我爱你\n[01:35.63]\n[02:05.94]夕阳西下 鸟儿回家\n[02:10.18]阿嬷躺在病床上\n[02:13.75]呼吸有一点散漫\n[02:17.54]眼神却很温柔\n[02:19.72]\n[02:20.79]看著爷爷 湿透的眼\n[02:24.70]握着他粗糙的手\n[02:28.42]阿嬷泪水开始流\n[02:31.86]轻声说道\n[02:35.22]\n[02:36.13]我要离去 别再哭泣\n[02:39.87]不要伤心 请你相信我\n[02:44.30]要等待 我的爱\n[02:48.00]陪你永不离开\n[02:50.83]因为会有那么一天\n[02:54.34]我们牵着手在草原\n[02:58.03]听 鸟儿歌唱的声音\n[03:02.97]听我说声 我爱你\n[03:06.86]\n[03:09.98]我要离去 别再哭泣\n[03:13.38]不要伤心 请你相信我\n[03:17.81]要等待 我的爱\n[03:21.38]陪你永不离开\n[03:24.17]因为会有那么一天\n[03:27.91]我们牵着手在草原\n[03:31.25]听 鸟儿歌唱的声音\n[03:36.51]听我说声 我爱你\n[03:41.02]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2080', '100401', '73892466', 'http://yinyueshiting.baidu.com/data2/music/245933273/245933273.mp3?xcode=f7f8664f442e0e7e48ae5c20176c0886', '', '速度与激情', '塔兔(DJTattoo)', '1471603194', '485', '0', '0', '好久没有起得这么的早了 为了要骑上我的摩托车出去溜达一圈\n久违了早上初升的太阳 久违了晨风拂面的那种感觉\n我的爱车发出的轰鸣 叫醒还在睡觉的城市街道\n轮胎抚摸着一道道斑马线 要唤起她内在的狂躁\n监控探头目送着我的背影 信号灯眨眼看着我的体型\n我操 对面突然看见了一个警察 我马上掉头驶向了逆行\n从反光镜看见警察追我的表情 我的心里真的无法平静\n天哪 你还真追我呀 （快跑吧）\n别他妈追我 你越追我我就越跑 （给油啊）\n你他妈越追我我就越跑 （给油啊）\n你越他妈追我我就越跑 （给油啊）（快跑啊）\n不是有规定吗 警察不许在马路上追摩托车呀\n我操 快跑吧 快跑 我就不让你丫给逮着了\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度 速度 激情 激情\n速度 速度 激情 激情\n躲过了警察的追击 我骑着车来到了熟张儿的摩托车俱乐部\n在这儿一般都是正经卖车的但从中骑驴拼缝儿的也不少\n看那个胖子笑得多么狡诈 谁从他那儿买车都得被他宰上几刀\n满口仁义道德 假么惺惺的各种称兄道弟\n（兄弟，不挣你钱，都是哥们儿介绍的，谁挣你钱谁妈是男的）\n我操 再看那小胡子 上次他把一辆事故车重新喷漆卖了挣了不少\n真他妈孙子\n离开有些纷乱的摩托车俱乐部 再去看看各种牛逼的骑行服饰\n玩车就得有个玩车的样吧 衣服都他妈往身上招呼着吧\n有个大哥们弄了一身哈雷的行头 出门骑上了个艾玛电动车\n悄无声息地走了 牛逼\n什么真的假的便宜的贵的A的原单的DIY泡妞吹牛逼的\n什么冬天穿的夏天穿的春秋穿的赛车穿的哈雷穿的宝马穿的金翼穿的 胡逼穿吧\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度 速度 激情 激情\n速度 速度 激情 激情\n晚上约了一大帮骑车的朋友们吃饭 就是我们常去的那个农家院儿\n所有的车一字排开 亮起大灯真他妈的气派\n所有人吃着 喝着 聊着 唱着 吹着牛逼 真是痛快\n离开的时候几十辆车发出轰鸣声 打破了夜晚道路中的宁静\n速度有它独特的魅力 唤起内心深处的激情\n速度有它独特的魅力 吸引明星也吸引老百姓\n不要再犹豫 想玩就要这样真正的享受生活\n不要再犹豫 玩车其实就是生与死那一瞬间的距离\n让你和我一起来体验速度与激情 拼份跟骑驴 好人与坏人\n生与死之间那一点点的秘密\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度与激情 只有玩车才能体会这样的心情\n速度与激情 看见我的尾灯真的就算你赢\n速度与激情 这么好的车不带姑娘实在不行\n速度与激情 其实就是他妈散德行\n速度 速度 激情 激情\n速度 速度 激情 激情');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2081', '100401', '74073374', 'http://yinyueshiting.baidu.com/data2/music/247020810/247020810.mp3?xcode=a88a9bea52ab7dce60e85653b43f0cf4', '', 'See You Again Remix', 'YoungMinz', '1471603267', '138', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2082', '100375', '246747045', 'http://yinyueshiting.baidu.com/data2/music/246762892/246762892.mp3?xcode=297b7552b9b35816c85f964b2abbb8a2', '', '十年', '赵丽颖', '1471623709', '201', '0', '0', '[00:02.00]十年\n[00:04.70]\n[00:06.18]作词：林夕  作曲：陈小霞\n[00:08.00]演唱：赵丽颖\n[00:10.00]\n[00:16.47]如果那两个字没有颤抖\n[00:19.93]我不会发现我难受\n[00:23.63]怎么说出口也不过是分手\n[00:31.76]如果对于明天没有要求\n[00:35.62]牵牵手就像旅游\n[00:38.71]成千上万个门口\n[00:42.47]总有一个人要先走\n[00:48.51]怀抱既然不能逗留\n[00:51.77]何不在离开的时候\n[00:54.86]一边享受一边泪流\n[01:01.82]十年之前\n[01:03.60]我不认识你你不属于我\n[01:07.36]我们还是一样陪在一个陌生人左右\n[01:13.91]走过渐渐熟悉的街头\n[01:17.31]十年之后\n[01:19.34]我们是朋友还可以问候\n[01:23.10]只是那种温柔\n[01:25.33]再也找不到拥抱的理由\n[01:29.39]情人最后难免沦为朋友\n[01:58.25]怀抱既然不能逗留\n[02:01.50]何不在离开的时候\n[02:04.60]一边享受一边泪流\n[02:11.41]十年之前\n[02:13.34]我不认识你你不属于我\n[02:17.19]我们还是一样陪在一个陌生人左右\n[02:23.49]走过渐渐熟悉的街头\n[02:26.89]十年之后\n[02:28.72]我们是朋友还可以问候\n[02:32.84]只是那种温柔\n[02:35.22]再也找不到拥抱的理由\n[02:39.13]情人最后难免沦为朋友\n[02:47.47]直到和你做了多年朋友\n[02:51.18]才明白我的眼泪\n[02:54.13]不是为你而流\n[02:58.40]也为别人而流\n[03:00.42]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2083', '100402', '74140877', 'http://yinyueshiting.baidu.com/data2/music/a6b17916c3bc9ef52440a381249b81dc/265937745/265937745.mp3?xcode=5af386a579b2b17c0e36f2067d58dcad', '', '逆流成河', '魏佳艺', '1471624204', '139', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2084', '100402', '23344282', 'http://yinyueshiting.baidu.com/data2/music/23344212/23344212.mp3?xcode=5af386a579b2b17c48ae5c20176c0886', '', '逆流而上', '赵雷', '1471624222', '80', '0', '0', '[ti:逆流而上]\n[ar:赵雷]\n[al:]\n\n[00:00.00]逆流而上\n[00:06.00]演唱：赵雷\n[00:12.00]\n[00:41.66]天上挂着一个不落的太阳\n[00:47.99]我问云彩这是真的吗\n[00:55.34]云彩已奔向太阳\n[00:58.27]却伤心的哭了\n[01:01.67]她的泪珠透出七彩光\n[01:07.55]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2085', '100396', '17780102', 'http://yinyueshiting.baidu.com/data2/music/17781151/17781151.mp3?xcode=01b8b4071027aa43c85f964b2abbb8a2', '', '爱在西元前', '周杰伦', '1471644200', '0', '0', '0', '[ti:爱在西元前]\n[ar:周杰伦]\n[al:范特西]\n\n[00:00.08]  爱在西元前\n[00:10.02]作词：方文山 作曲：周杰伦\n[00:12.58]演唱：周杰伦\n[00:14.43]\n[00:30.93]古巴比伦王颁布了汉摩拉比法典\n[00:35.14]刻在黑色的玄武岩 距今已经三千七百多年\n[00:39.58]你在橱窗前 凝视碑文的字眼\n[00:43.03]我却在旁静静欣赏你那张我深爱的脸\n[00:46.75]\n[01:51.09][00:47.32]祭司 神殿 征战 弓箭 是谁的从前\n[01:55.34][00:51.02]喜欢在人潮中你只属於我的那画面\n[01:59.08][00:54.68]经过苏美女神身边 我以女神之名许愿\n[02:03.45][00:59.03]思念像底格里斯河般的漫延\n[02:07.18][01:02.57]当古文明只剩下难解的语言\n[02:13.53][01:09.18]传说就成了永垂不朽的诗篇\n[02:19.80][01:15.48]\n[02:22.16][01:17.96]我给你的爱写在西元前\n[02:24.79][01:20.65]深埋在美索不达米亚平原\n[02:29.85][01:25.54]几十个世纪後出土发现\n[02:32.54][01:28.17]泥板上的字迹依然清晰可见\n[02:35.86][01:31.25]\n[02:37.50][01:33.13]我给你的爱写在西元前\n[02:40.02][01:35.72]深埋在美索不达米亚平原\n[02:45.01][01:40.69]用楔形文字刻下了永远\n[02:47.71][01:43.24]那已风化千年的誓言 一切又重演\n[02:51.94][01:49.06]\n[02:52.57]我感到很疲倦 离家乡还是很远\n[02:58.93]害怕再也不能回到你身边\n[03:04.86]\n[03:07.82]我给你的爱写在西元前\n[03:10.33]深埋在美索不达米亚平原\n[03:15.40]几十个世纪後出土发现\n[03:18.05]泥板上的字迹依然清晰可见\n[03:21.06]\n[03:22.95]我给你的爱写在西元前\n[03:25.47]深埋在美索不达米亚平原\n[03:30.54]用楔形文字刻下了永远\n[03:33.05]那已风化千年的誓言 一切又重演\n[03:37.26]爱在西元前 爱在西元前\n[03:50.54]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2086', '100334', '2070237', 'http://yinyueshiting.baidu.com/data2/music/123937797/123937797.mp3?xcode=72ffdb768ea8b32990032d54ecb07301', '', 'I Miss You', '罗百吉', '1471659087', '248', '0', '0', '[ti:0]\n[ar:0]\n[al:0]\n[by:0]\n[offset:0]\n[00:01.00]I Miss You\n[00:01.11]演唱：罗百吉\n[00:04.00]\n[00:23.55]这一刻是我给你最后的机会\n[00:28.68]用不着对我又吼又乱叫\n[00:33.33]我一定对你是真心真意\n[00:39.89]这你不用来质疑\n[00:43.75]\n[00:45.14]你的背叛已经伤了我太深\n[00:50.52]不知是否应不应该太认真\n[00:54.71]也许你以后会改变自己\n[01:01.49]但我已决定必须离开你\n[01:05.80]I Miss You I Miss You\n[01:09.00]I Miss You everyday\n[01:12.75]只想看看你的脸\n[01:16.61]想念你 想念你\n[01:19.78]想念你的欢笑\n[01:22.63]整颗心已属于你\n[01:28.41]\n[01:39.77]你的背叛已经伤了我太深\n[01:45.01]不知是否应不应该太认真\n[01:49.41]也许你以后会改变自己\n[01:55.92]但我已决定必须离开你\n[02:00.43]I Miss You I Miss You\n[02:03.73]I Miss You everyday\n[02:07.28]只想看看你的脸\n[02:11.24]想念你 想念你\n[02:14.40]想念你的欢笑\n[02:17.14]整颗心已属于你\n[02:22.71]I Miss You I Miss You\n[02:25.36]I Miss You everyday\n[02:29.02]只想看看你的脸\n[02:33.06]想念你 想念你\n[02:36.17]想念你的欢笑\n[02:38.96]整颗心已属于你\n[02:44.66]\n[02:45.10]罗百吉：I Miss You I Miss You\n[02:47.08]I Miss You everyday\n[02:50.92]只想看看你的脸\n[02:55.02]想念你 想念你\n[02:57.93]想念你的欢笑\n[03:00.64]整颗心已属于你\n[03:06.50]\n[03:07.91]让时间随着音乐流走\n[03:12.43]我轻轻对你唱着这首歌\n[03:16.71]I Miss You I Miss You\n[03:19.74]I Miss You everyday\n[03:23.66]只想看看你的脸\n[03:27.65]想念你 想念你\n[03:30.72]想念你的欢笑\n[03:33.48]整颗心已属于你\n[03:38.87]I Miss You 鸣……\n[03:49.39]想念你 咿……\n[03:58.02]已属于你……\n[04:01.11]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2087', '100334', '64399206', 'http://yinyueshiting.baidu.com/data2/music/124484652/124484652.mp3?xcode=0bdb78a4bf490cd29d88bb873c3ec0e5', '', 'U You', 'Apink', '1471659152', '205', '0', '0', '[ti:U You]\n[ar:Apink]\n[al:Secret Garden]\n[offset:0]\n\n[00:00.67]U You - Apink\n[00:04.29]\n[00:15.12]내 사랑은 U U\n[00:16.64]너만 보면 자꾸 떨려와\n[00:19.89]내 전부는 U U\n[00:21.58]내 눈엔 너밖에 안보여\n[00:24.99]꿈속에도 U U U (BABY MY LOVE)\n[00:27.37]매일매일 U U U (I JUST WANNA)\n[00:30.34]조금씩 더 천천히 니 사랑에 빠질래\n[00:34.70]\n[00:36.08]유난히 햇살 좋은 아침 오늘은 그댈 만나는 날\n[00:45.23]어제부터 골라 놓은 옷들은 참 많은데 뭘 입어야 더 예뻐 보일까\n[00:53.49]\n[00:55.09]Shining Shining Shining Star 그댄 나의 Super Star\n[00:59.96]누가 뭐라 해도 뭐라 말을 해도 내 눈엔 다 멋진걸\n[01:04.25]\n[01:04.45]내 사랑은 U U\n[01:06.11]너만 보면 자꾸 떨려와\n[01:09.27]내 전부는 U U\n[01:11.06]내 눈엔 너밖에 안보여\n[01:14.39]꿈속에도 U U U (BABY MY LOVE)\n[01:16.96]매일매일 U U U (I JUST WANNA)\n[01:19.88]조금씩 더 천천히 니 사랑에 빠질래\n[01:24.08]\n[01:26.86]OH MY BOY OH MY LUV\n[01:31.89]이제 너를 만나러 가는 길\n[01:37.21]예쁜 구두 신고 예쁜 치마도 입고\n[01:42.03]너를 만나면 환하게 웃어 줄거야\n[01:46.42]\n[01:47.15]Shining Shining Shining Star 그댄 나의 Super Star\n[01:51.86]누가 뭐라 해도 뭐라 말을 해도\n[01:54.50]내 눈엔 내 눈엔 내 눈엔 완벽한걸\n[01:58.61]\n[01:58.99]내 사랑은 U U\n[02:00.71]너만 보면 자꾸 떨려와\n[02:03.66]내 전부는 U U\n[02:05.85]내 눈엔 너밖에 안보여\n[02:08.66]꿈속에도 U U U (BABY MY LOVE)\n[02:11.36]매일매일 U U U (I JUST WANNA)\n[02:14.10]조금씩 더 천천히 니 사랑에 빠질래\n[02:18.48]\n[02:19.31]Chu Chu Chu 니 입술이 다가와\n[02:24.22]Chu Pop Chu Pop 난 어떡해야 돼\n[02:29.32]내 사랑은 진짜 너야 내 전부는 진짜 너야\n[02:33.90]너도 나와 같은지 내 맘과 똑같은지\n[02:39.14]\n[02:42.18]내 사랑은 U U\n[02:43.79]너만 보면 자꾸 떨려와\n[02:47.15]내 전부는 U U\n[02:48.69]내 눈엔 너 밖에 안보여\n[02:52.09]꿈속에도 U U U (BABY MY LOVE)\n[02:54.61]매일매일 U U U (I JUST WANNA)\n[02:57.61]조금씩 더 천천히 니 사랑에 빠질래\n[03:01.71]\n[03:01.97]나 너만 보면 어떡해\n[03:03.60]내 가슴이 자꾸 떨려와\n[03:06.77]나 너만 보면 어떡해\n[03:08.58]니 사랑이 내게 다가와\n[03:11.84]하루 종일 너너너 생각할게\n[03:14.48]봐도 봐도 너너너 보고싶어\n[03:17.33]조금씩 더 천천히 니 사랑에 빠질래\n[03:21.95]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2088', '100334', '266728213', 'http://yinyueshiting.baidu.com/data2/music/7b1e08f8f3debeaa5248a0f3794d0df9/266874334/266874334.mp3?xcode=951ab3ed6d4cbd25b74cd3784244547c', '', 'Everytime', 'Chen', '1471659284', '189', '0', '0', '[00:00.00]OH EVERY TIME I SEE YOU\n[00:06.11]그대 눈을 볼 때면 자꾸 가슴이 또 설레여와\n[00:12.15]내 운명이죠 세상 끝이라도 지켜주고 싶은 단 한 사람\n[00:24.32]BABY OHOHOHOH\n[00:27.86]OHOHOHOH\n[00:30.42]BABY OHOHOHOH\n[00:35.40]OH EVERY TIME I SEE YOU\n[00:40.90]그대 눈을 볼 때면 자꾸 가슴이 또 설레여와\n[00:47.34]내 운명이죠 세상 끝이라도 지켜주고 싶은 단 한 사람\n[00:57.67]\n[01:00.62]그대 나를 바라볼 때 나를 보며 미소 질 때 난 심장이 멈출 것 같아요 난.\n[01:11.74]그댄 어떤가요. 난 정말 감당하기 힘든걸\n[01:18.42]온종일 그대 생각해\n[01:22.17]조금 멀리 우리 돌아왔지만\n[01:26.55]지금이라도 난 괜찮아\n[01:31.66]OH EVERY TIME I SEE YOU\n[01:36.76]그대 눈을 볼 때면 자꾸 가슴이 또 설레여와\n[01:43.37]내 운명이죠 세상 끝이라도 지켜주고 싶은 단 한 사람\n[01:54.44]날 떠나지 말아요\n[01:57.37]가끔은 알 수 없는 미래라 해도\n[02:03.10]날 믿고 기다려줄래요\n[02:14.15]워 나만의 그대여\n[02:19.49]내겐 전부라는 말\n[02:22.48]고백한 적이 있었나요\n[02:26.05]내 운명이죠 세상 끝이라도 지켜주고 싶은 너\n[02:36.00]BABY OHOHOHOH\n[02:37.99]사랑할래요\n[02:38.90]OHOHOHOH\n[02:41.21]니 눈빛과 니 미소와 그 향기까지도\n[02:46.08]BABY OHOHOHOH\n[02:48.55]기억해줘요\n[02:49.50]OHOHOHOH\n[02:51.88]언제나 우리 함께 있음을..i love u\n[03:02.67]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2089', '100379', '498547', 'http://yinyueshiting.baidu.com/data2/music/123655398/123655398.mp3?xcode=96a65a24b6415443c6826868de970894', '', '格桑花开', '韩红', '1471685687', '286', '0', '0', '[00:01.65]格桑花开\n[00:03.32]演唱;韩红\n[00:04.21]\n[00:51.41]啦......\n[01:07.28]骑上“雪公主”还有五里路\n[01:11.26]或许会有人等我\n[01:15.16]格桑花开过\n[01:17.23]就在云深处\n[01:19.51]我不怀疑有路\n[01:23.00]又是我\n[01:24.09]哦又一次入梦\n[01:29.30]天路中身往何处\n[01:39.39]被思念捉住\n[01:41.91]马背上停留\n[01:43.50]你古铜色的笑容\n[01:47.51]格桑花开过\n[01:49.67]雪在云深处\n[01:51.72]再不会有人等我\n[01:55.21]又是我哦\n[01:57.67]又一夜好梦\n[02:03.06]又是我与你想逢\n[03:45.59][02:11.84]点不破玄机中深藏的佛陀\n[03:52.85][02:19.54]难道迷惑解脱是错\n[04:01.16][02:27.73]我不怕藏北的风吹过我荒凉的面孔\n[04:09.74][02:35.16]背负尘世的火\n[04:12.08][02:37.99]穿越所有的梦\n[02:56.58]啦......\n[03:12.55]无所谓心恸\n[03:14.61]不在乎相守\n[03:16.53]我只想要归路\n[03:20.56]格桑花开过\n[03:22.61]翻云覆雨中\n[03:24.45]物是人非难懂\n[03:28.16]还好我哦\n[03:30.56]会一夜好梦\n[03:35.91]你不用再次相送');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2090', '100388', '74124355', 'http://yinyueshiting.baidu.com/data2/music/e158408f148a263168b4591b071847bb/263948886/263948886.mp3?xcode=132f0d201729347aaca186416cbdae39', '', '太阳的后裔（马健南、Shirley Jiang）', '弦音唱片', '1471808417', '0', '0', '0', '[00:00.33]太阳的后裔\n[00:07.00]词：Shirley Jiang\n[00:11.56]曲：马健南\n[00:15.73]唱：马健南、Shirley Jiang\n[00:19.65]\n[00:21.20]男:\n[00:26.57]Would you know my love\n[00:32.84]Miss you wherever I go\n[00:38.55]\n[00:39.25]女:\n[00:40.76]Would you know my love\n[00:46.29]Forever hold you in my heart\n[00:51.80]\n[00:52.55]合唱:\n[00:52.90]You light up my sky\n[00:55.95]Never say goodbye\n[00:59.26]Whenever dreams come true\n[01:02.92]We\'re descendants of the sun\n[01:06.98]\n[01:07.93]男:\n[01:08.83]Would you know my love（Would you know my love）\n[01:15.00]Miss you wherever I go\n[01:21.52]\n[01:22.33]女:\n[01:23.08]Would you know my love（Would you know my love）\n[01:28.64]Forever hold you in my heart\n[01:33.96]\n[01:34.61]合唱:\n[01:35.06]You light up my sky\n[01:38.11]Never say goodbye\n[01:41.32]Whenever dreams come true\n[01:44.92]We\'re descendants of the sun\n[01:49.67]\n[01:50.60]男:\n[01:52.09]Would you know my love\n[01:56.91]Miss you wherever I go\n[02:03.18]\n[02:03.68]女:\n[02:04.68]Would you know my love\n[02:09.94]Forever hold you in my heart\n[02:15.80]\n[02:18.68]We\'re descendants of the sun\n[02:27.35]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2091', '100388', '74127326', 'http://yinyueshiting.baidu.com/data2/music/513acc05773dadf872962b632a6a66f1/264555661/264555661.mp3?xcode=132f0d201729347aafe7b73b41f866fe', '', '太阳的后裔OST中文版《说吧爱我》说干什么呢-口哨歌', '戴凤鑫', '1471808425', '0', '0', '0', '[00:00.05]韩剧《太阳的后裔》OST \n[00:02.06]Part.6<Talk Love>\n[00:04.53](说干什么呢)\n[00:06.52][口哨歌] -K.Will\n[00:08.05]中文填词版《说吧爱我》\n[00:11.36]填词：皋阜率（戴凤鑫）\n[00:14.65]演唱：大欧正（戴凤鑫）\n[00:18.45]\n[00:19.92]思念是无边烈火\n[00:21.80]燃烧了你和我\n[00:24.21]两个人掏了心交流多快乐\n[00:28.78]强烈地来不及闪躲\n[00:31.42]陷进 甜蜜的漩涡\n[00:34.73]慢慢的沉没\n[00:37.07]可是你的\n[00:37.66]爱 变成了一种 折磨\n[00:42.08]想要看得见的结果\n[00:44.48]太在乎会着了魔\n[00:47.50]我 温热了你的 冷漠\n[00:51.32]（都不管了）\n[00:54.73]不需要想太多\n[00:56.87]（说吧爱我 说吧爱我） \n[01:01.49]你还纠结 犹豫什么\n[01:03.79]其实我们会很适合\n[01:06.22]（说吧爱我 说吧爱我）\n[01:10.64]你一直藏在 我心窝\n[01:13.47]You are my only one \n[01:16.31]\n[01:36.80]看你伤心流泪不说\n[01:39.19]我比你更难过\n[01:41.54]请告诉我什么地方出了错\n[01:46.25]是不是越爱越寂寞\n[01:48.63]这种 感觉我懂得\n[01:52.17]相信我\n[01:53.39]坚持我们这份\n[01:55.40]爱 变成了一句 承诺\n[01:59.20]想要摸得着的结果\n[02:01.76]冲破所有的枷锁\n[02:04.52]我 温热了你的 冷漠\n[02:08.60]（还在想什么）\n[02:12.07]不需要想太多\n[02:13.63]\n[02:14.13]（说吧爱我 说吧爱我） \n[02:18.79]你还纠结 犹豫什么\n[02:21.12]我们两个 会很适合\n[02:23.51]（说吧爱我 说吧爱我）  \n[02:27.78]你一直藏在 我心窝\n[02:30.77]You are my only one \n[02:32.96]当 夜幕和心情同时在沉默\n[02:37.15]要记得 远方有一个在想你的我\n[02:42.20]不要失落  也不要难过\n[02:46.36]不要 不要  轻易地忘了我\n[02:53.95]（说吧爱我 说吧爱我） \n[02:59.13]别再犹豫 多想什么\n[03:01.50]付出一切 会很值得\n[03:03.78]（说出来吧 说出来吧）\n[03:08.36]你一直就在 我心窝\n[03:11.11]You are my only one \n[03:14.21]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2092', '100372', '438173', 'http://yinyueshiting.baidu.com/data2/music/64097047/64097047.mp3?xcode=02db0245f628f7f8d1fcd5f6c50b1ab0', '', '我很好', '刘若英', '1471827699', '272', '0', '0', '[ti:0]\n\n[ar:0]\n\n[al:0]\n\n[by:0]\n\n[offset:0]\n\n[00:01.02]我很好\n\n[00:01.77]演唱;刘若英\n\n[00:02.67]\n\n[00:15.81]沙发上睡着　\n\n[00:19.04]孤单冷醒的破晓\n\n[00:23.35]冷的面条　\n\n[00:25.42]热的泪痕　\n\n[00:27.12]啤酒在苦笑\n\n[00:30.96]当时的煎熬　\n\n[00:34.85]当时的心痛如绞\n\n[00:38.58]天终于亮了　\n\n[00:42.39]遗憾终于退潮\n\n[00:45.00]\n\n[00:45.32]终于能够恨不再疯　\n\n[00:48.23]泪不再掉　\n\n[00:50.13]心不跑\n\n[00:52.95]一定会有一个人　\n\n[00:55.44]一段新的美好\n\n[00:59.34]\n\n[01:01.48]谁让我拥抱　\n\n[01:04.42]谁让我再一次心跳\n\n[01:08.27]就算爱情让我再次的跌倒　\n\n[01:13.01]伤痕也要是一种骄傲\n\n[01:16.96]谁让我拥抱　\n\n[01:19.43]谁让我疯狂的心跳\n\n[01:23.46]就算明天整个城市要倾倒　\n\n[01:28.10]也让我爱到最后一秒\n\n[01:33.39]\n\n[01:47.57]丢掉电影票　\n\n[01:50.52]删掉信件跟合照\n\n[01:55.28]洗了床单　\n\n[01:56.79]剪了头发　\n\n[01:58.73]清空了烦恼\n\n[02:02.59]恨可以很小　\n\n[02:05.82]小到眼泪能冲掉\n\n[02:10.64]我现在很好　\n\n[02:13.14]可以重新起跑\n\n[02:16.37]\n\n[02:16.84]终于能够恨不再疯　\n\n[02:19.68]泪不再掉　心不跑\n\n[02:24.46]一定会有一个人　\n\n[02:27.06]一段新的美好\n\n[02:31.13]\n\n[02:33.04]谁让我拥抱　\n\n[02:35.59]谁让我再一次心跳\n\n[02:39.58]就算爱情让我再次的跌倒　\n\n[02:44.44]伤痕也要是一种骄傲\n\n[02:48.21]谁让我拥抱　\n\n[02:50.88]谁让我疯狂的心跳\n\n[02:55.39]就算明天整个城市要倾倒　\n\n[02:59.57]也让我爱到最后一秒\n\n[03:04.82]\n\n[03:05.64]地铁涌出了人潮　\n\n[03:09.13]幸福涌出了预兆\n\n[03:12.15]我会找回当初对爱天真的霸道\n\n[03:18.85]\n\n[03:22.53]谁让我拥抱　\n\n[03:25.17]谁让我再一次心跳\n\n[03:29.12]就算爱情让我再次的跌倒　\n\n[03:33.88]伤痕也要是一种骄傲\n\n[03:37.78]谁让我拥抱　\n\n[03:40.20]谁让我疯狂的心跳\n\n[03:44.49]就算明天整个城市要倾倒　\n\n[03:49.24]也让我爱到最后一秒\n\n[03:53.98]谁让我拥抱　\n\n[03:55.63]谁让我疯狂的心跳\n\n[03:59.72]就算明天整个城市要倾倒　\n\n[04:04.53]也让我爱到最后一秒\n\n[04:13.98]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2093', '100266', '960385', 'http://yinyueshiting.baidu.com/data2/music/242218173/242218173.mp3?xcode=1a6b71fdc77d97950c486914f0acd7b6', '', '我的国歌', '赵传', '1471831697', '266', '0', '0', '[ti:0]\n[ar:0]\n[al:0]\n[by:0]\n[offset:0]\n[00:01.33]我的国歌\n[00:02.69]赵传\n[00:04.80]\n[00:31.83]在我内心的深处\n[00:38.87]为你升起一面国旗\n[00:45.87]那是我平凡的爱情\n[00:52.69]和忠贞不灭的灵魂\n[00:58.19]\n[00:59.73]站在没风的草原\n[01:06.58]为你插上一面国旗\n[01:13.61]那是我脆弱的勇气\n[01:20.59]和男人最后的宣誓\n[01:29.01]\n[01:30.65]这是我为你唱的歌\n[01:38.15]像烙在胸口的红心\n[01:44.66]这是我为你唱的歌\n[01:52.01]只想大声唱给你听\n[01:59.33]\n[02:26.92]在我内心的深处\n[02:33.91]为你升起一面国旗\n[02:41.00]那是我平凡的爱情\n[02:47.84]和忠贞不灭的灵魂\n[02:53.37]\n[02:54.79]站在没风的草原\n[03:01.62]为你插上一面国旗\n[03:08.44]那是我脆弱的勇气\n[03:15.56]和男人最后的宣誓\n[03:22.68]\n[03:25.88]这是我为你唱的歌\n[03:32.84]像烙在胸口的红心\n[03:39.34]这是我为你唱的歌\n[03:46.88]只想大声唱给你听\n[03:53.48]只想大声唱给你听\n[04:03.54]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2094', '100429', '16071398', 'http://yinyueshiting.baidu.com/data2/music/16072132/16072132.mp3?xcode=97f452d950d47680e9c5304b21fec782', '', '后来', '原声大碟', '1471903183', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2095', '100429', '74125621', 'http://yinyueshiting.baidu.com/data2/music/3dc19ebd9be7eb4c92b458d85b7ae480/264222195/264222195.mp3?xcode=1236124a5e8464797ed60eb536e08c87', '', '后来', '魏佳艺', '1471903263', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2096', '100429', '235725', 'http://yinyueshiting.baidu.com/data2/music/123328664/123328664.mp3?xcode=d527292faaa922fcb59adc403ebeee44', '', '清明雨上', '许嵩', '1471903375', '0', '0', '0', '[ti:清明雨上]\n[ar:许嵩]\n[al:]\n\n[00:06.65]清明雨上\n[00:07.04]作词：许嵩、安琪 作曲：许嵩\n[00:11.05]演唱：许嵩\n[00:16.17]\n[00:29.81]窗透初晓 日照西桥 云自摇\n[00:35.71]想你当年荷风微摆的衣角\n[00:43.06]木雕流金 岁月涟漪 七年前封笔\n[00:49.34]因为我今生挥毫只为你\n[00:56.41]雨打湿了眼眶 年年倚井盼归堂\n[01:02.90]最怕不觉泪已拆两行\n[01:06.91]\n[01:08.61]我在人间彷徨 寻不到你的天堂\n[01:15.35]东瓶西镜放 恨不能遗忘\n[01:21.95]又是清明雨上 折菊寄到你身旁\n[01:28.90]把你最爱的歌来轻轻唱\n[01:36.13]\n[01:49.15]远方有琴 愀然空灵 声声催天雨\n[01:55.89]涓涓心事说给自己听\n[02:02.55]月影憧憧 烟火几重 烛花红\n[02:09.20]红尘旧梦 梦断都成空\n[02:15.84]雨打湿了眼眶 年年倚井盼归堂\n[02:22.35]最怕不觉泪已拆两行\n[02:27.64]\n[02:28.34]我在人间彷徨 寻不到你的天堂\n[02:34.77]东瓶西镜放 恨不能遗忘\n[02:41.38]又是清明雨上 折菊寄到你身旁\n[02:48.40]把你最爱的歌来轻轻唱\n[02:53.19]\n[02:54.16]我在人间彷徨 寻不到你的天堂\n[03:01.22]东瓶西镜放 恨不能遗忘\n[03:07.83]又是清明雨上 折菊寄到你身旁\n[03:14.87]把你最爱的歌来轻轻唱\n[03:22.31]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2097', '100253', '74107715', 'http://yinyueshiting.baidu.com/data2/music/995e76bae5208c289b6205377b647dad/261618379/261618379.mp3?xcode=e76152a3e0d319cb7ddf74b1a6ef6955', '', '周杰伦 - 东风破', '公主殿下', '1471917506', '315', '0', '0', '[ver:v1.0]\r\n[ar:周杰伦]\r\n[ti:东风破]\r\n[00:01.35]东风破-周杰伦\r\n[00:13.61]一盏离愁孤灯伫立在窗口\r\n[00:20.50]我在门后假装你人还没走\r\n[00:27.01]旧地如重游月圆更寂寞\r\n[00:33.60]夜半清醒的烛火不忍苛责我\r\n[00:40.23]一壶漂泊浪迹天涯难入喉\r\n[00:46.73]你走之后酒暖回忆思念瘦\r\n[00:53.23]水向东流时间怎么偷\r\n[00:59.92]花开就一次成熟我却错过\r\n[01:09.83]谁在用琵琶弹奏一曲东风破\r\n[01:16.38]岁月在墙上剥落看见小时候\r\n[01:22.93]犹记得那年我们都还很年幼\r\n[01:29.42]而如今琴声幽幽我的等候你没听过\r\n[01:36.03]谁在用琵琶弹奏一曲东风破\r\n[01:42.65]枫叶将故事染色结局我看透\r\n[01:49.20]篱笆外的古道我牵着你走过\r\n[01:55.75]荒烟漫草的年头就连分手都很沉默\r\n[02:28.64]一壶漂泊浪迹天涯难入喉\r\n[02:35.25]你走之后酒暖回忆思念瘦\r\n[02:41.85]水向东流时间怎么偷\r\n[02:48.36]花开就一次成熟我却错过\r\n[02:58.01]谁在用琵琶弹奏一曲东风破\r\n[03:04.80]岁月在墙上剥落看见小时候\r\n[03:11.36]犹记得那年我们都还很年幼\r\n[03:17.96]而如今琴声幽幽我的等候你没听过\r\n[03:24.51]谁在用琵琶弹奏一曲东风破\r\n[03:31.11]枫叶将故事染色结局我看透\r\n[03:37.72]篱笆外的古道我牵着你走过\r\n[03:44.28]荒烟漫草的年头就连分手都\r\n[03:50.91]谁在用琵琶弹奏一曲东风破\r\n[03:57.37]岁月在墙上剥落看见小时候\r\n[04:03.95]犹记得那年我们都还很年幼\r\n[04:10.63]而如今琴声幽幽我的等候你没听过\r\n[04:17.13]谁在用琵琶弹奏一曲东风破\r\n[04:23.76]枫叶将故事染色结局我看透\r\n[04:30.16]篱笆外的古道我牵着你走过\r\n[04:36.76]荒烟漫草的年头就连分手都很沉默');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2098', '100374', '268168540', 'http://yinyueshiting.baidu.com/data2/music/f349c1c074e7b355087118c280d96217/268168556/268168556.mp3?xcode=e0a8c7cfc0f72bf40d9b8f0c677c76d2', '', '十年戎马心孤单', 'MC天佑', '1471923468', '73', '0', '0', '十年戎马心孤单\n\n演唱：mc天佑\n\n十年戎马的心孤单\n隐退了江湖归深山\n如果有天你难堪\n挂帅出征再扬帆\n既然那疆场你已输\n我怎还能继续哭\n召集三千那勇者夫\n那么血洗金銮捣皇都\n北斗七星八卦阵\n忘却了红尘爱或恨\n爱或恨被情困\n只故心中太苦闷\n风云变幻天地搬\n嗜血那魔剑破天翻\n名与利有何干\n早已归隐深山\n一天两类套套词\n不管对错值不值\n为了在我辉煌时\n那么犹如骏马在奔驰\n骏马奔驰多豪迈\n另类的喊歌多痛快\n霸主君王这一代\n那么巅峰另类从未败\n另类破腔踏五关\n直抵那巅峰凌云山\n麒麟咆哮猛虎吹\n那么喊破三界的深渊\n凤凰展翅龙摇摆\n凤凰另类荡大海\n一生戎马把命改\n那么凤展翅我摇尾\n神雀烈煞斗厉鬼\n仙人指路向西北\n出征挂念那家人美\n那么久别战场向仙问');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2101', '100250', '19188868', 'http://yinyueshiting.baidu.com/data2/music/123944798/123944798.mp3?xcode=dc72911b15311fb196a2d3f13bf86a03', '', '生命树', '郑钧', '1472003234', '0', '0', '0', '[ti:]\n[ar:]\n[al:]\n[offset:0]\n\n[00:00.87]生命树\n[00:01.47]演唱：郑钧\n[00:02.12]\n[00:17.01]命运\n[00:20.86]在旷野中呻吟\n[00:25.46]在悼念\n[00:29.17]她痛失的昨天\n[00:33.76]昨天已消逝得无限远\n[00:38.01]谁也走不到她面前\n[00:46.56]纯真\n[00:50.22]在月光下裸奔\n[00:54.68]想找寻\n[00:58.34]一个喜欢她的人\n[01:02.06]可是那人总也不出现\n[01:07.12]谁也见不到他的面\n[01:10.80]嗯嗯\n[01:12.97]\n[01:30.29]当激情从生命的树上凋零\n[01:37.84]我们将在极乐中苏醒\n[01:46.52]\n[02:05.73]我们\n[02:09.79]在欢乐中呻吟\n[02:14.07]纪念\n[02:17.77]正痛失的今天\n[02:22.48]今天将消逝得无限远\n[02:26.54]谁也走不到她面前\n[02:31.22]\n[02:49.54]当激情从生命的树上凋零\n[02:57.39]我们将在极乐中苏醒\n[03:06.27]\n[03:16.81]阳光真刺眼\n[03:20.90]令我很难堪\n[03:24.88]我亲着你的脸\n[03:28.89]却尝到了孤单\n[03:33.39]幸福真新鲜\n[03:37.60]但摘下来太难\n[03:41.74]亲爱的姑娘\n[03:45.74]我拿你怎么办\n[03:49.81]阳光真刺眼\n[03:53.74]令我很难堪\n[03:58.40]我亲着你的脸\n[04:00.62]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2103', '100466', '555947', 'http://yinyueshiting.baidu.com/data2/music/124203728/124203728.mp3?xcode=9923dd806a3e1a6ef5d44a3a1acfff98', '', '月半小夜曲', '梁咏琪,李克勤', '1472150372', '0', '0', '0', '[ti:月半小夜曲]\n[ar:梁咏琪]\n[al:903拉阔音乐会2002]\n[offset:500]\n\n[00:01.79]月半小夜曲_梁咏琪 李克勤\n[00:11.45]曲：河合奈保子  词：向雪怀\n[00:12.30]\n[02:14.49][00:22.58]仍然倚在失眠夜望天边星宿\n[02:21.02][00:29.33]仍然听见小提琴如泣似诉再挑逗\n[02:26.49][00:34.81]为何只剩一弯月留在我的天空\n[02:32.73][00:40.79]这晚以后音讯隔绝\n[02:38.27][00:45.83]人如天上的明月是不可拥有\n[02:44.15][00:52.04]情如曲过只遗留无可挽救再分别\n[02:49.79][00:58.00]为何只是失望填密我的空虚\n[02:55.86][01:03.84]这晚夜没有吻别\n[03:39.00][03:01.70][01:08.61]仍在说永久想不到是借口\n[03:44.74][03:07.35][01:15.21]从未意会要分手\n[03:53.13][03:15.88][01:23.46]但我的心每分每刻仍然被她占有\n[03:59.41][03:22.09][01:29.68]她似这月儿仍然是不开口\n[04:04.71][03:27.62][01:34.99]提琴独奏独奏着明月半倚深秋\n[04:10.78][03:33.29][01:41.02]我的牵挂我的渴望  直至以后\n[04:20.14]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2104', '100238', '45494019', 'http://yinyueshiting.baidu.com/data2/music/51192974/51192974.mp3?xcode=dab2ab44c3df79b348ae5c20176c0886', '', '背叛', '陈一玲', '1472150598', '0', '0', '0', '[00:01.00]背叛\n[00:05.00]作词：阿丹,邬裕康 作曲：曹格\n[00:07.00]演唱：陈一玲\n[00:09.00]\n[00:12.50]雨 不停落下来\n[00:20.08]花 怎么都不开\n[00:26.64]尽管我细心灌溉\n[00:30.59]你说不爱就不爱\n[00:33.79]我一个人  欣赏悲哀\n[00:42.00]爱 只剩下无奈\n[00:49.27]我 一直不愿再去猜\n[00:55.69]钢琴上黑键之间\n[00:59.36]永远都夹着空白\n[01:03.53]缺了一块  就不精彩\n[01:10.67]\n[01:11.88]紧紧相依的心如何 say goodbye\n[01:18.98]你比我清楚还要我说明白\n[01:25.27]爱太深会让人疯狂地勇敢\n[01:32.03]我用背叛自己  完成你的期盼\n[01:39.69]把手放开不问一句 say goodbye\n[01:46.48]当作最后一次对你的溺爱\n[01:53.00]冷冷清清淡淡今后都不管\n[02:00.68]只要你能愉快\n[02:06.04]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2105', '100223', '530072', 'http://yinyueshiting.baidu.com/data2/music/40232654/40232654.mp3?xcode=4c08f16d4f01023c84b505fb6b038464', '', '十年', '陈奕迅', '1472151984', '205', '0', '0', '[00:02.03]十年\n[00:04.77]演唱：陈奕迅\n[00:06.18]\n[00:15.42]如果那两个字没有颤抖\n[00:19.68]我不会发现 我难受\n[00:23.09]怎么说出口\n[00:26.58]也不过是分手\n[00:31.18]如果对于明天没有要求\n[00:35.24]牵牵手就像旅游\n[00:38.30]成千上万个门口\n[00:42.22]总有一个人要先走\n[00:47.81]怀抱既然不能逗留\n[00:51.23]何不在离开的时候\n[00:54.11]一边享受 一边泪流\n[01:01.34]十年之前\n[01:03.35]我不认识你 你不属于我\n[01:07.01]我们还是一样\n[01:09.54]陪在一个陌生人左右\n[01:13.48]走过渐渐熟悉的街头\n[01:16.81]十年之后\n[01:18.82]我们是朋友 还可以问候\n[01:22.54]只是那种温柔\n[01:25.08]再也找不到拥抱的理由\n[01:28.89]情人最后难免沦为朋友\n[01:35.50]\n[01:57.73]怀抱既然不能逗留\n[02:00.87]何不在离开的时候\n[02:03.81]一边享受 一边泪流\n[02:11.03]十年之前\n[02:12.91]我不认识你 你不属于我\n[02:16.73]我们还是一样\n[02:19.30]陪在一个陌生人左右\n[02:23.08]走过渐渐熟悉的街头\n[02:26.39]十年之后\n[02:28.50]我们是朋友 还可以问候\n[02:32.13]只是那种温柔\n[02:34.67]再也找不到拥抱的理由\n[02:38.51]情人最后难免沦为朋友\n[02:48.59]直到和你做了多年朋友\n[02:52.80]才明白我的眼泪\n[02:55.65]不是为你而流\n[02:59.46]也为别人而流\n[03:03.39]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2106', '100475', '268168741', 'http://yinyueshiting.baidu.com/data2/music/e2a8c35dd27150a22b6776d672a65c19/268168761/268168761.mp3?xcode=328abff5513c38c83cbb1604e4436b22', '', '一人饮酒醉', 'MC天佑', '1472170741', '79', '0', '0', '一人饮酒醉\n\n演唱：mc天佑\n\n一人 我饮酒醉\n醉把佳人成双对\n两眼 是独相随\n只求他日能双归\n娇女我轻扶琴\n燕嬉她紫竹林\n痴情红颜心甘情愿\n千里把君寻\n说红颜痴情笑\n曲动琴声太奇妙\n我轻狂太高傲我懵懂无知\n太年少\n弃江山\n忘天下\n斩断了情丝无牵挂\n千古留名传佳话\n我两年征战已白发\n一生征战何人陪\n谁是谁非谁相随\n戎马一生为了谁\n我能爱几回恨几回\n败帝王\n斗苍天\n夺得了皇位已成仙\n豪情万丈天地间\n我续写了另类帝王篇\n红尘事我已斩断\n久经战场人心乱\n当年扬名又立万\n我为这一战无遗憾\n相思\n我愁断肠\n眼中我泪两行\n多年为君一统天下\n为的是戎马把名扬');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2107', '100475', '73991606', 'http://yinyueshiting.baidu.com/data2/music/cf2ca8019d13e4f4e2fe72c74057fdca/261660677/261660677.mp3?xcode=f9583434a1b0b0e66f333bb99c6701d9', '', '（7妹提供）经典完整版', 'DJ  Fat', '1472170814', '299', '0', '0', '[00:00.00]Sorry，该歌曲暂无歌词。');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2108', '100475', '268168540', 'http://yinyueshiting.baidu.com/data2/music/f349c1c074e7b355087118c280d96217/268168556/268168556.mp3?xcode=ee7f2d56bb60d4d3aca186416cbdae39', '', '十年戎马心孤单', 'MC天佑', '1472170917', '73', '0', '0', '十年戎马心孤单\n\n演唱：mc天佑\n\n十年戎马的心孤单\n隐退了江湖归深山\n如果有天你难堪\n挂帅出征再扬帆\n既然那疆场你已输\n我怎还能继续哭\n召集三千那勇者夫\n那么血洗金銮捣皇都\n北斗七星八卦阵\n忘却了红尘爱或恨\n爱或恨被情困\n只故心中太苦闷\n风云变幻天地搬\n嗜血那魔剑破天翻\n名与利有何干\n早已归隐深山\n一天两类套套词\n不管对错值不值\n为了在我辉煌时\n那么犹如骏马在奔驰\n骏马奔驰多豪迈\n另类的喊歌多痛快\n霸主君王这一代\n那么巅峰另类从未败\n另类破腔踏五关\n直抵那巅峰凌云山\n麒麟咆哮猛虎吹\n那么喊破三界的深渊\n凤凰展翅龙摇摆\n凤凰另类荡大海\n一生戎马把命改\n那么凤展翅我摇尾\n神雀烈煞斗厉鬼\n仙人指路向西北\n出征挂念那家人美\n那么久别战场向仙问');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2109', '100476', '268316624', 'http://yinyueshiting.baidu.com/data2/music/7692b06874d06d8492dbf264a1531f13/268613130/268613130.mp3?xcode=0483bf57fa382bcf19a583210f1a2e09', '', '爱在星空下', '齐秦', '1472171722', '0', '0', '0', '[00:00.67]爱在星空下\n[00:10.15]\n[00:17.35]作词: 汪子琦 晓柏\n[00:20.34]作曲: 晓柏 李全\n[00:23.90]演唱：齐秦\n[00:28.10]\n[00:35.40]寒夜冷风 肆意吹散了美梦\n[00:42.93]唤醒了浮浮沉沉的我\n[00:50.11]午夜星空 除了寂寞 还有感动\n[00:57.66]细数每次奔波 好像繁星在闪烁\n[01:04.79]\n[01:05.96]远方的家 牵着我的心跳动 \n[01:13.53]提醒我梦想不会陨落\n[01:20.92]遥远的她 是否会给我一个拥抱\n[01:28.11]当我从你的世界里路过\n[01:33.36]\n[01:35.22]一样的声影 一样的旋律 \n[01:42.54]一样的太年轻 划出青涩的痕迹\n[01:50.18]也许是不愿凝望    \n[01:54.99]渐渐消失的背影\n[01:58.87]回首才发现 你已不在这里 \n[02:06.80]\n[02:10.64]远方的家 牵着我的心跳动 \n[02:18.32]提醒我梦想不会陨落\n[02:25.79]遥远的她 是否会给我一个拥抱\n[02:32.97]当我从你的世界里路过\n[02:38.90]\n[02:39.42]一样的声影 一样的旋律 \n[02:47.24]一样的太年轻 划出青涩的痕迹\n[02:54.86]也许是不愿凝望    \n[02:59.59]渐渐消失的背影\n[03:03.68]回首才发现 你已不在这里 \n[03:09.77]\n[03:10.32]一样的星星  一样的回忆\n[03:17.69]一样的旧电影 靠着你温暖如昔\n[03:25.25]也许错过了风景\n[03:30.02]渐渐模糊的足迹\n[03:34.03]请你别怀疑 我一直在这里\n[03:39.14]\n[03:53.57]爱的星空下 以幸福的名义\n[03:56.00]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2110', '100295', '74094208', 'http://yinyueshiting.baidu.com/data2/music/bac354d73e58c1834d2f27c4474bed18/258578575/258578575.mp3?xcode=be29f369f6827f6a9614b4474386abae', '', '嗨曲【3D环绕嗨曲】戴上耳机，巴西佬口水旋律，', '北京龙天音乐基地', '1472178291', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2113', '100491', '242078437', 'http://yinyueshiting.baidu.com/data2/music/c63861e9b77a9bc200d9178434e17229/242078536/242078536.mp3?xcode=272aa801cab677a46b73a0f684021956', '', '演员', '薛之谦', '1472233071', '261', '0', '0', '[00:00.32]演员\n[00:01.00]\n[00:01.61]作词：薛之谦\n[00:02.64]作曲：薛之谦\n[00:03.00]演唱：薛之谦\n[00:04.20]\n[00:21.12]简单点说话的方式简单点\n[00:30.20]递进的情绪请省略\n[00:33.64]你又不是个演员\n[00:36.38]别设计那些情节\n[00:39.36]\n[00:41.93]没意见我只想看看你怎么圆\n[00:51.54]你难过的太表面 像没天赋的演员\n[00:57.15]观众一眼能看见\n[01:00.19]\n[01:02.22]该配合你演出的我演视而不见\n[01:07.68]在逼一个最爱你的人即兴表演\n[01:12.90]什么时候我们开始收起了底线\n[01:18.02]顺应时代的改变看那些拙劣的表演\n[01:23.42]可你曾经那么爱我干嘛演出细节\n[01:28.63]我该变成什么样子才能延缓厌倦\n[01:33.87]原来当爱放下防备后的这些那些\n[01:39.37]才是考验\n[01:41.97]\n[01:44.60]没意见你想怎样我都随便\n[01:54.53]你演技也有限\n[01:57.58]又不用说感言\n[02:00.15]分开就平淡些\n[02:02.99]\n[02:05.00]该配合你演出的我演视而不见\n[02:10.53]别逼一个最爱你的人即兴表演\n[02:15.81]什么时候我们开始没有了底线\n[02:21.01]顺着别人的谎言被动就不显得可怜\n[02:26.43]可你曾经那么爱我干嘛演出细节\n[02:31.52]我该变成什么样子才能配合出演\n[02:36.72]原来当爱放下防备后的这些那些\n[02:41.86]都有个期限\n[02:44.60]\n[02:47.56]其实台下的观众就我一个\n[02:53.04]其实我也看出你有点不舍\n[02:58.34]场景也习惯我们来回拉扯\n[03:02.93]还计较着什么\n[03:07.39]\n[03:08.71]其实说分不开的也不见得\n[03:14.04]其实感情最怕的就是拖着\n[03:19.21]越演到重场戏越哭不出了\n[03:24.07]是否还值得\n[03:28.12]\n[03:29.07]该配合你演出的我尽力在表演\n[03:34.39]像情感节目里的嘉宾任人挑选\n[03:39.68]如果还能看出我有爱你的那面\n[03:44.82]请剪掉那些情节让我看上去体面\n[03:50.04]可你曾经那么爱我干嘛演出细节\n[03:55.31]不在意的样子是我最后的表演\n[04:01.05]是因为爱你我才选择表演 这种成全');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2115', '100269', '23219362', 'http://yinyueshiting.baidu.com/data2/music/134369535/134369535.mp3?xcode=a31b780b43550a143cbb1604e4436b22', '', '神话', '韩红,孙楠', '1472244449', '0', '0', '0', '[00:04.70]美丽的神话\n[00:06.90]演唱：孙楠、韩红\n[00:09.10]\n[00:18.80]梦中人 熟悉的脸孔\n[00:25.90]你是我守候的温柔\n[00:33.30]就算泪水淹没天地\n[00:40.70]我不会放手\n[00:48.30]每一刻孤独的承受\n[00:55.30]只因我曾许下承诺\n[01:03.00]你我之间熟悉的感动\n[01:10.15]爱就要苏醒\n[01:16.90]万世沧桑唯有爱是永远的神话\n[01:24.05]潮起潮落始终不悔真爱的相约\n[01:31.15]几番苦痛的纠缠多少黑夜挣扎\n[01:38.95]紧握双手让我和你再也不离分\n[01:47.72]\n[02:02.00]枕上雪 冰封的爱恋\n[02:09.30]真心相拥才能融解\n[02:16.80]风中摇曳炉上的火\n[02:24.05]不灭亦不休\n[02:31.75]等待花开春去春又来\n[02:39.05]无情岁月笑我痴狂\n[02:46.15]心如钢铁任世界荒芜\n[02:53.65]思念永相随\n[03:00.25]万世沧桑唯有爱是永远的神话\n[03:07.15]潮起潮落始终不悔真爱的相约\n[03:14.80]几番苦痛的纠缠多少黑夜挣扎\n[03:22.00]紧握双手让我和你再也不离分\n[03:29.60]悲欢岁月唯有爱是永远的神话\n[03:37.00]谁都没有遗忘古老古老的誓言\n[03:44.50]你的泪水化为漫天飞舞的彩蝶\n[03:51.85]爱是翼下之风两心相随自在飞\n[03:59.05]悲欢岁月唯有爱是永远的神话\n[04:06.60]谁都没有遗忘古老古老的誓言\n[04:14.00]你的泪水化为漫天飞舞的彩蝶\n[04:21.35]爱是翼下之风两心相随自在飞\n[04:31.60]你是我心中唯一美丽的神话\n[04:44.67]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2116', '100226', '243155881', 'http://yinyueshiting.baidu.com/data2/music/243155950/243155950.mp3?xcode=06e263cbe33a2f4a2e999d51bc758b81', '', '小幸运', '田馥甄', '1472257930', '265', '0', '0', '[00:03.00]小幸运\n[00:06.01]\n[00:09.41]作词：徐世珍，吴辉福 作曲：JerryC\n[00:12.55]演唱：田馥甄\n[00:14.78]\n[00:16.00]我听见雨滴落在青青草地\n[00:21.87]我听见远方下课钟声响起\n[00:27.87]可是我没有听见你的声音\n[00:32.98]认真 呼唤我姓名\n[00:40.09]爱上你的时候还不懂感情\n[00:46.15]离别了才觉得刻骨 铭心\n[00:52.19]为什么没有发现遇见了你\n[00:56.70]是生命最好的事情\n[01:02.52]也许当时忙着微笑和哭泣\n[01:08.50]忙着追逐天空中的流星\n[01:14.26]人理所当然的忘记\n[01:18.79]是谁风里雨里一直默默守护在原地\n[01:26.52]原来你是我最想留住的幸运\n[01:31.73]原来我们和爱情曾经靠得那么近\n[01:37.74]那为我对抗世界的决定\n[01:42.30]那陪我淋的雨\n[01:45.53]一幕幕都是你 一尘不染的真心\n[01:52.79]与你相遇 好幸运\n[01:56.41]可我已失去为你泪流满面的权利\n[02:02.00]但愿在我看不到的天际\n[02:06.61]你张开了双翼\n[02:09.68]遇见你的注定 (oh--)\n[02:14.57]她会有多幸运\n[02:20.54]\n[02:29.54]青春是段跌跌撞撞的旅行\n[02:35.47]拥有着后知后觉的美丽\n[02:41.58]来不及感谢是你给我勇气\n[02:46.15]让我能做回我自己\n[02:51.93]也许当时忙着微笑和哭泣\n[02:57.81]忙着追逐天空中的流星\n[03:03.52]人理所当然的忘记\n[03:08.14]是谁风里雨里一直默默守护在原地\n[03:16.00]原来你是我最想留住的幸运\n[03:21.04]原来我们和爱情曾经靠得那么近\n[03:27.10]那为我对抗世界的决定\n[03:31.87]那陪我淋的雨\n[03:34.64]一幕幕都是你 一尘不染的真心\n[03:42.24]与你相遇 好幸运\n[03:45.27]可我已失去为你泪流满面的权利\n[03:51.42]但愿在我看不到的天际\n[03:55.94]你张开了双翼\n[03:59.21]遇见你的注定 (oh--)\n[04:06.83]她会有多幸运\n[04:11.26]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2117', '100288', '14699366', 'http://yinyueshiting.baidu.com/data2/music/455f54227975aa9803df78ac3a216ccd/262775660/262775660.mp3?xcode=0e13715fb0fa7debd1fcd5f6c50b1ab0', '', '我的秘密', 'G.E.M.邓紫棋', '1472258307', '0', '0', '0', '[ti:]\n[ar:]\n[al:]\n[by:]\n\n[00:00.37]我的秘密 MySecret\n[00:02.37]词曲：邓紫棋\n[00:04.37]演唱：邓紫棋\n[00:06.01]\n[00:10.41]最近一直很好心情 不知道什么原因\n[00:19.66]我现在这一种心情 我想要唱给你听\n[00:26.98]\n[00:38.18]看着窗外的小星星 心里想着我的秘密\n[00:47.33]算不算爱我不太确定 我只知道我在想你\n[00:55.13]\n[01:05.26]我们之间的距离 好像忽远又忽近\n[01:09.88]你明明不在我身边 我却觉得很亲\n[01:14.27]Ha~ 有一种感觉我想说明\n[01:18.81]我心里的秘密 是你给的甜蜜\n[01:23.53]我们之间的距离好像一点点靠近\n[01:28.30]是不是你对我也有一种特殊感情\n[01:32.56]Ha~ 我犹豫要不要告诉你\n[01:37.13]我心里的秘密是我好像喜欢了你\n[01:43.86]\n[01:51.96]夜里陪着我的声音就算沙了也动听\n[02:01.22]这一种累了的声音是最温柔的证明\n[02:08.48]\n[02:10.71]（你是我你是我的秘密）\n[02:12.76]（我一直都在想着你）\n[02:15.16]（你是我心里的秘密）\n[02:18.48]\n[02:19.28]我们之间的距离好像忽远又忽近\n[02:23.68]你明明不在我身边我却觉得很亲\n[02:28.08]Ha~ 有一种感觉我想说明\n[02:32.59]我心里的秘密是你给的甜蜜\n[02:37.33]我们之间的距离好像一点点靠近\n[02:42.14]是不是你对我也有一种特殊感情\n[02:46.39]Ha~ 我犹豫要不要告诉你\n[02:50.95]我心里的秘密是我好像喜欢了你\n[02:57.62]\n[03:04.65]这模糊的关系是莫名的美丽\n[03:11.48]\n[03:14.44]我们之间的距离好像忽远又忽近\n[03:19.01]你明明不在我身边我却觉得很亲\n[03:23.35]Ha~ 这一刻我真的想说明\n[03:27.92]我心里的秘密是你给的甜蜜\n[03:32.81]我们之间的距离每天一点点靠近\n[03:37.55]这是种别人无法理解的特殊感情\n[03:41.77]Ha~ 我要让全世界都清晰\n[03:46.38]我心里的秘密是我会一直深爱着你\n[03:55.30]深爱着你\n[03:58.12]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2118', '100497', '73994843', 'http://yinyueshiting.baidu.com/data2/music/239833517/239833517.mp3?xcode=230302e57804eda5a0cc505dc0621a48', '', '悟空(live)', '戴荃▪悟空', '1472263616', '200', '0', '0', '[00:03.01]悟空\r\n[00:04.58]Live版\r\n[00:06.06]\r\n[00:07.45]作词：戴荃\r\n[00:08.92]作曲：戴荃\r\n[00:10.54]演唱：戴荃▪悟空\r\n[00:12.19]\r\n[00:23.67]月溅星河，长路漫漫。\r\n[00:29.67]风烟残尽，独影阑珊。\r\n[00:36.30]谁叫我身手不凡，谁让我爱恨两难。\r\n[00:43.43]到后来，肝肠寸断。\r\n[00:49.27]\r\n[00:50.79]幻世当空，恩怨休怀，\r\n[00:57.14]舍悟离迷，六尘不改\r\n[01:03.94]且怒且悲且狂哉，是人是鬼是妖怪，\r\n[01:11.61]不过是，心有魔债。\r\n[01:17.58]\r\n[01:18.37]叫一声佛祖，回头无岸。\r\n[01:25.07]跪一人为师，生死无关。\r\n[01:32.24]善恶浮世真假界,尘缘散聚不分明，难断！\r\n[01:41.67]\r\n[01:43.31]我要这铁棒有何用，\r\n[01:49.84]我有这变化又如何，\r\n[01:56.33]还是不安，还是氐惆\r\n[02:03.73]金箍当头，欲说还休\r\n[02:09.51]\r\n[02:26.75]我要这铁棒醉舞魔\r\n[02:33.72]我有这变化乱迷浊\r\n[02:40.65]踏碎灵霄，放肆桀骜\r\n[02:47.19]世恶道险，终究难逃\r\n[02:53.99]\r\n[02:55.23]这一棒，叫你灰飞烟灭\r\n[03:16.19]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2119', '100295', '1596043', 'http://yinyueshiting.baidu.com/data2/music/134273114/134273114.mp3?xcode=4c23e0a54c1270b2bd5cc648288db746', '', '你快回来', '孙楠', '1472404301', '0', '0', '0', '[ti:你快回来]\n[ar:孙楠]\n[al:]\n\n[00:02.29]你快回来\n[00:05.70]演唱：孙楠\n[00:08.92]\n[00:27.09]没有你 世界寸步难行\n[00:34.02]我困在原地 任回忆凝集\n[00:41.16]黑夜里 祈求黎明快来临\n[00:48.23]只有你 给我温暖晨曦\n[00:54.68]\n[00:55.74]走到思念的尽头我终于相信\n[01:01.92]没有你的世界 爱都无法给予\n[01:09.86]忧伤反复纠缠 我无法躲闪\n[01:16.04]心中有个声音 总在呼喊\n[01:21.62]\n[01:22.35]你快回来 我一人承受不来\n[01:29.46]你快回来 生命因你而精彩\n[01:36.52]你快回来 把我的思念带回来\n[01:43.99]别让我的心空如大海\n[01:50.14]\n[02:05.76]没有你 世界寸步难行\n[02:12.66]我困在原地 任回忆凝集\n[02:19.81]黑夜里 祈求黎明快来临\n[02:26.80]只有你 给我温暖晨曦\n[02:33.36]\n[02:34.29]走到思念的尽头我终于相信\n[02:40.56]没有你的世界 爱都无法给予\n[02:48.45]忧伤反复纠缠 我无法躲闪\n[02:54.66]心中有个声音 总在呼喊\n[03:00.30]\n[03:00.94]你快回来 我一人承受不来\n[03:07.87]你快回来 生命因你而精彩\n[03:15.14]你快回来 把我的思念带回来\n[03:22.66]别让我的心空如大海\n[03:28.51]\n[03:28.98]你快回来 我一人承受不来\n[03:36.09]你快回来 生命因你而精彩\n[03:43.35]你快回来 把我的思念带回来\n[03:50.89]别让我的心空如大海\n[03:58.14]别让我的心空如大海\n[04:07.92]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2120', '100528', '124688661', 'http://yinyueshiting.baidu.com/data2/music/241763594/241763594.mp3?xcode=06dc381f8b7fbfeeafe7b73b41f866fe', '', '分开那天', '程响', '1472406228', '269', '0', '0', '《分开那天》\n词曲：王羽泽\n演唱：程响\n终于你不在我身边\n总算让我离开视线\n我们不可能再续缘\n对那些回忆不留恋\n如果他对你好一点\n就安心留在他身边\n我和你真的没感觉\n也许和你就没有缘\n当我和你分开那一天\n眼里才会有泪水出现\n是我不懂珍惜你的爱\n还是我不在你心里面\n当我和你分开那一天\n眼里才会有泪水出现\n是你不懂珍惜我的爱\n还是我把爱情看太远');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2121', '100529', '12946574', 'http://yinyueshiting.baidu.com/data2/music/123312917/123312917.mp3?xcode=30dcc7fc46494520b6f28ebc1a3b5868', '', '贝加尔湖畔', '李健', '1472408970', '0', '0', '0', '[00:20.00]贝加尔湖畔\n[00:22.06]词曲：李健\n[00:23.80]演唱：李健\n[00:28.09]\n[00:48.76]在我的怀里 在你的眼里\n[00:55.71]那里春风沉醉 那里绿草如茵\n[01:04.06]月光把爱恋 洒满了湖面\n[01:11.90]两个人的篝火 照亮整个夜晚\n[01:18.90]多少年以后 如云般游走\n[01:25.39]那变换的脚步 让我们难牵手\n[01:33.89]这一生一世 有多少你我\n[01:41.53]被吞没在月光如水的夜里\n[01:49.51]\n[01:54.64]多想某一天 往日又重现\n[02:01.53]我们流连忘返 在贝加尔湖畔\n[02:09.79]\n[02:42.70]多少年以后 往事随云走\n[02:50.39]那纷飞的冰雪容不下那温柔\n[02:57.82]这一生一世 这时间太少\n[03:06.14]不够证明融化冰雪的深情\n[03:13.49]\n[03:18.39]就在某一天 你忽然出现\n[03:25.83]你清澈又神秘 在贝加尔湖畔\n[03:34.08]你清澈又神秘 像贝加尔湖畔\n[03:43.98]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2122', '100293', '9138435', 'http://yinyueshiting.baidu.com/data2/music/44066015/44066015.mp3?xcode=a8b08351aaa65af0e23725bcf113dbcd', '', 'We Are The World', 'Michael Jackson', '1472409377', '0', '0', '0', '[ti:]\n[ar:]\n[al:]\n\n[00:05.47]We Are The World\n[00:08.01]Michael Jackson\n[00:11.33]\n[00:14.06]There is a time when we should heed a certain call\n[00:20.43]Cause the world it seems it’s right in this line\n[00:27.14]Cause there’s a chance for taking in needing our own lives\n[00:34.38]It seems we need nothing at all\n[00:41.46]I used to feel that I should give away my heart\n[00:48.12]And it shows that fear of needing them\n[00:54.76]Then I read the headlines and it said they’re dying there\n[01:01.92]And it shows that we must heed instead\n[01:08.25]We are the world\n[01:11.68]We are the children\n[01:15.12]We are the ones who make a brighter day\n[01:18.40]So let’s start giving\n[01:22.58]But there’s a chance we’re taking\n[01:26.10]We’re taking our own lives\n[01:29.55]It’s true we’ll make a brighter day just you and me\n[01:38.95]\n[01:40.46]Give in your heart and you will see that someone cares\n[01:46.81]Cause you know that they can feed them all\n[01:53.83]Than I read the paper and it said that you’ve been denied\n[02:00.76]And it shows the second we will call\n[02:07.03]We are the world\n[02:10.37]We are the children\n[02:13.83]We are the ones who make a brighter day\n[02:16.95]So let’s start giving\n[02:21.02]But there’s a chance we’re taking\n[02:24.86]We’re taking our own lives\n[02:28.30]It’s true we’ll make a brighter day just you and me\n[02:35.42]No there’s a time when we must love them all\n[02:42.26]And it seems that life, it don’t make love at all\n[02:48.67]But if you’d be there, and I’ll love you more and more\n[02:55.97]It seems in life, i didn’t do that\n[03:02.29]We are the world\n[03:05.64]We are the children\n[03:09.09]We are the ones who make a brighter day\n[03:12.19]So let’s start giving\n[03:16.27]But there’s a chance we’re taking\n[03:20.17]We’re taking our own lives\n[03:23.61]It’s true we’ll make a brighter day just you and me\n[03:29.83]We are the world\n[03:33.31]We are the children\n[03:36.75]We are the ones who make a brighter day\n[03:40.19]So let’s start giving\n[03:44.05]But there’s a chance we’re taking\n[03:47.76]We’re taking our own lives\n[03:51.19]It’s true we’ll make a brighter day just you and me\n[03:57.56]We are the world\n[04:00.93]We are the children\n[04:04.37]We are the ones who make a brighter day\n[04:07.52]So let’s start giving\n[04:11.80]But there’s a chance we’re taking\n[04:15.40]We’re taking our own lives\n[04:18.85]It’s true we’ll make a brighter day just you and me\n[04:25.25]We are the world\n[04:28.61]We are the children\n[04:32.06]We are the ones who make a brighter day\n[04:35.29]So let’s start giving\n[04:39.36]But there’s a chance we’re taking\n[04:43.13]We’re taking our own lives\n[04:46.50]It’s true we’ll make a brighter day just you and me\n[04:52.02]We are the world\n[04:56.20]We are the children\n[04:59.69]We are the ones who make a brighter day\n[05:02.79]So let’s start giving\n[05:06.98]But there’s a chance we’re taking\n[05:10.80]We’re taking our own lives\n[05:14.26]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2123', '100295', '230637', 'http://yinyueshiting.baidu.com/data2/music/122511231/122511231.mp3?xcode=d4d15fbaae1d69d184b505fb6b038464', '', '天空', '王菲', '1472411359', '0', '0', '0', '[ti:天空(Unplugged)]\n[ar:王菲]\n[al:天空]\n\n[00:01.00]天空\n[00:05.00]演唱：王菲\n[00:09.00]\n[00:29.99]我的天空 为何挂满湿的泪\n[00:38.61]\n[00:43.50]我的天空 为何总灰着脸\n[00:52.45]\n[00:58.39]飘流在世界的另一边\n[01:02.07]\n[01:04.76]任寂寞侵犯\n[01:07.91]一遍一遍 天空\n[01:12.41]划著长长的思念\n[01:18.51]\n[01:23.73]你的天空 可有悬着想的云\n[01:32.03]\n[01:37.00]你的天空 可会有冷的月\n[01:45.58]\n[01:51.97]放逐在世界的另一边\n[01:55.60]\n[01:58.21]任寂寞占据\n[02:00.50]一夜一夜 天空\n[02:06.35]藏著深深的思念\n[02:12.08]\n[02:45.63]等待在世界的各一边\n[02:50.00]任寂寞嬉笑\n[02:54.15]一年一年 天空\n[02:59.71]叠著层层的思念\n[03:09.31]天空\n[03:13.33]叠著层层的思念\n[03:21.55]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2124', '100295', '369536', 'http://yinyueshiting.baidu.com/data2/music/125221958/125221958.mp3?xcode=2534f9db5b808f550e36f2067d58dcad', '', '你的背包', '陈奕迅', '1472411763', '0', '0', '0', '[00:02.50]你的背包\n[00:04.50]作词：林夕 作曲：蔡政\n[00:06.50]演唱：陈奕迅\n[00:08.50]\n[00:23.71]一九九五年 我们在机场的车站\n[00:30.76]你借我 而我不想归还\n[00:37.71]那个背包载满纪念品和患难\n[00:45.74]还有摩擦留下的图案\n[00:51.83]\n[00:53.17]你的背包 背到现在还没烂\n[01:01.02]却成为我身体另一半\n[01:08.11]千金不换 它已熟悉我的汗\n[01:16.29]它是我肩膀上的指环\n[01:21.02]\n[01:28.69]背了六年半 我每一天陪它上班\n[01:35.23]你借我 我就为你保管\n[01:42.15]我的朋友都说它旧得很好看\n[01:50.27]遗憾是它已与你无关\n[01:56.14]\n[01:57.23]你的背包 让我走得好缓慢\n[02:05.09]总有一天陪着我腐烂\n[02:12.54]你的背包 对我沉重的审判\n[02:20.89]借了东西为什么不还\n[02:25.63]\n[02:50.76]你的背包让我走得好缓慢\n[02:58.64]总有一天陪着我腐烂\n[03:05.61]你的背包对我沉重的审判\n[03:14.57]借了东西为什么不还\n[03:19.28]\n[03:22.09]借了东西为什么 不还\n[03:30.91]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2125', '100295', '2477376', 'http://yinyueshiting.baidu.com/data2/music/fef6c390bc4964f6f792921050290f99/39483709/39483709.mp3?xcode=d4411b33cc397e1a29676627b54cb918', '', '卡农钢琴曲（Variations on the Canon）', '我的野蛮女友', '1472412378', '0', '0', '0', '[00:00.00]该歌曲为无歌词的纯音乐，请您欣赏。');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2126', '100295', '277161', 'http://yinyueshiting.baidu.com/data2/music/241199311/241199311.mp3?xcode=6afc536484e69e35c85f964b2abbb8a2', '', '背对背拥抱', '林俊杰', '1472413001', '0', '0', '0', '[00:02.18]背对背拥抱\n[00:04.83]作词：林怡凤 作曲：林俊杰\n[00:06.79]演唱：林俊杰\n[00:08.47]\n[00:15.23]话总说不清楚 该怎么明了\n[00:22.70]一字一句像圈套\n[00:29.90]旧帐总翻不完 谁无理取闹\n[00:36.64]你的双手甩开刚好的微妙\n[00:42.21]然后战火再燃烧\n[00:46.49]\n[00:48.74]我们背对背拥抱\n[00:52.40]滥用沉默在咆哮\n[00:56.06]爱情来不及变老\n[00:59.07]葬送在烽火的玩笑\n[01:02.86]\n[01:03.51]我们背对背拥抱\n[01:07.15]真话兜着圈子乱乱绕\n[01:11.06]只是想让我知道\n[01:14.94]只是想让你知道 爱的警告\n[01:21.00]\n[01:23.74]话总说不清楚 该怎么明了\n[01:30.68]一字一句像圈套\n[01:38.22]旧帐总翻不完 谁无理取闹\n[01:45.12]你的双手甩开刚好的微妙\n[01:50.61]然后战火再燃烧\n[01:54.53]\n[01:55.24]我们背对背拥抱\n[01:58.82]滥用沉默在咆哮\n[02:02.56]爱情来不及变老\n[02:05.53]葬送在烽火的玩笑\n[02:09.00]\n[02:10.71]我们背对背拥抱\n[02:13.65]真话兜着圈子乱乱绕\n[02:17.46]只是想让我知道\n[02:21.23]只是想让你知道 爱的警告\n[02:28.13]\n[02:29.35]我不要一直到 形同陌路变成自找\n[02:36.47]既然可以拥抱  就不要轻易放掉\n[02:43.67]\n[02:45.31]我们背对背拥抱\n[02:48.74]滥用沉默在咆哮\n[02:52.29]爱情来不及变老\n[02:55.35]葬送在烽火的玩笑\n[02:59.20]\n[02:59.62]我们背对背拥抱\n[03:03.43]真话兜着圈子乱乱绕\n[03:07.55]只是想让我知道\n[03:11.21]只是想让你知道 这警告\n[03:18.56]只是想让我知道\n[03:22.14]只是想让你知道 爱的警告\n[03:31.21]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2127', '100529', '2477376', 'http://yinyueshiting.baidu.com/data2/music/fef6c390bc4964f6f792921050290f99/39483709/39483709.mp3?xcode=976ffea94ceb4c449d88bb873c3ec0e5', '', '卡农钢琴曲（Variations on the Canon）', '我的野蛮女友', '1472413054', '0', '0', '0', '[00:00.00]该歌曲为无歌词的纯音乐，请您欣赏。');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2128', '100295', '2496519', 'http://yinyueshiting.baidu.com/data2/music/134369719/134369719.mp3?xcode=ff57775e7f16de400c0d8753917afb85', '', '当你', '林俊杰', '1472413184', '0', '0', '0', '[ti:当你]\n[ar:林俊杰]\n[al:她说]\n[t_time:(04:09)]\n\n[00:00.71]当你\n[00:02.37]作词：张思尔 作曲：林俊杰\n[00:04.47]演唱：林俊杰\n[00:06.43]\n[00:14.70]如果有一天 我回到从前\n[00:20.53]回到最原始的我\n[00:24.91]你是否 会觉得我不错\n[00:29.36]如果有一天 我离你遥远\n[00:34.09]不能再和你相约\n[00:39.22]你是否会发觉 我已经说再见\n[02:52.16][01:52.79][00:45.12]\n[03:21.07][02:52.37][01:54.79][00:46.78]当你的眼睛 瞇着笑 当你喝可乐 当你吵\n[03:27.87][02:59.37][02:01.96][00:53.90]我想对你好 你从来不知道 想你 想你 也能成为嗜好\n[03:06.48][02:09.14][01:01.13]当你说今天的烦恼 当你说夜深 你睡不着\n[03:13.66][02:16.38][01:08.28]我想对你说 却害怕都说错 好喜欢你 知不知道\n[02:26.03][01:17.32]\n[01:23.31]如果有一天 梦想都实现\n[01:28.68]回忆都成了永远 你是否还会 记得今天\n[01:37.43]如果有一天 我们都发觉\n[01:42.28]原来什么都可以 我们是否还会 停留在这里\n[02:29.58]\n[02:38.44]也许空虚 让我想得太多 也许该回到被窝\n[02:45.06]梦里会相遇 就毫不犹豫 大声的说我要说\n[03:34.86]\n[03:35.06]啦 ~ ~ ~ 啦 ~ ~ ~\n[03:42.40]我想对你说 却害怕都说错 好喜欢你 知不知道\n[03:52.21]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2129', '100537', '119837101', 'http://yinyueshiting.baidu.com/data2/music/119837362/119837362.mp3?xcode=bf2bc22f987289326a37d80a2c02fc5b', '', '撸啊撸', '单小源,马娳颖', '1472418340', '0', '0', '0', '[ti:撸啊撸]\n[ar:单小源]\n[al:撸啊撸]\n[offset:0]\n\n[00:01.60]撸啊撸 - 单小源\n[00:03.08]词：单小源\n[00:04.36]曲：单小源\n[00:05.65]编曲：舒文轩\n[00:06.85]\n[00:39.23]剑圣偷塔的速度\n[00:40.47]会让你感到无助\n[00:41.89]炼金和提姆的毒\n[00:43.26]会让你感到痛苦\n[00:44.67]暗黑的冰霜女巫\n[00:46.06]让寒冷带来陵墓\n[00:47.50]打野的阿木木\n[00:49.02]偷偷把你缠住\n[00:50.32]诺克萨斯之手\n[00:51.83]一提斧你断头\n[00:53.25]披甲龙龟好肉\n[00:54.63]嘲讽你太瘦\n[00:56.05]英勇的德玛\n[00:57.51]拼命在扛塔\n[00:58.92]蛮王一开大\n[01:00.33]是真男人欧巴\n[01:01.88]不断恶化的政治\n[01:03.00]越来越多的争执\n[01:04.66]瓦罗然的法师\n[01:06.05]达成了共识\n[01:07.30]建立了一个组织\n[01:08.71]让战斗来解决争执\n[01:10.20]这就是故事的开始\n[01:11.69]是英雄联盟的历史\n[01:13.00]在正义的土地 他们厮杀竞技\n[01:15.81]为了取得胜利 要齐心协力\n[01:18.58]打更多的金币 买更好的武器\n[01:21.56]摧毁对方的水晶 是你们的目的\n[01:24.75]撸啊撸 从日落战斗到日出\n[01:29.03]\n[01:30.10]撸啊撸 拜托跟紧我的脚步\n[01:34.31]\n[01:35.72]撸啊撸 不要把你队友辜负\n[01:40.12]\n[01:41.33]让我们分出个胜负\n[01:45.13]\n[01:47.09]撸啊撸 我从来就不会服输\n[01:51.87]\n[01:52.63]撸啊撸 拜托看清我的态度\n[01:57.70]\n[01:58.22]撸啊撸 请叫我超神小公主\n[02:02.66]\n[02:03.94]谁知道爬坑的路途\n[02:07.41]有多么的孤独\n[02:10.48]\n[02:20.98]不断恶化的政治\n[02:22.15]越来越多的争执\n[02:23.58]瓦罗然的法师 达成了共识\n[02:26.30]建立了一个组织\n[02:27.73]让战斗来解决争执\n[02:29.17]这就是故事的开始\n[02:30.59]是英雄联盟的历史\n[02:31.99]在正义的土地 他们厮杀竞技\n[02:34.76]为了取得胜利 要齐心协力\n[02:37.58]打更多的金币 买更好的武器\n[02:40.60]摧毁对方的水晶\n[02:41.87]是你们的目的\n[02:43.64]撸啊撸 从日落战斗到日出\n[02:47.99]\n[02:49.15]撸啊撸 拜托跟紧我的脚步\n[02:54.13]\n[02:54.71]撸啊撸 不要把你队友辜负\n[02:59.07]\n[03:00.40]让我们分出个胜负\n[03:04.37]\n[03:06.08]熔岩巨兽的锤击\n[03:07.35]会让你感到无力\n[03:08.87]潜行的阿卡丽\n[03:10.12]穿过暗影突袭\n[03:11.40]可爱的小安妮\n[03:12.93]会召唤出泰迪\n[03:14.37]剑姬好美丽\n[03:15.81]却是杀人机器\n[03:17.30]薇恩太暴力\n[03:18.57]追杀你毫不费力\n[03:20.01]魅惑的阿狸欺诈你敌意\n[03:22.83]眼瞎的李青 战场上飘逸\n[03:25.64]爱射的艾希 会让你哭泣');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2130', '100295', '1329365', 'http://yinyueshiting.baidu.com/data2/music/241193992/241193992.mp3?xcode=53c1a43cef891dba5990d376263109e0', '', 'Always Online', '林俊杰', '1472422833', '0', '0', '0', '[00:02.45]Always Online\n[00:04.35]作词：林怡凤 作曲：林俊杰\n[00:06.03]演唱：林俊杰\n[00:08.49]\n[00:17.10]变色的生活 任性的挑拨\n[00:21.21]疯狂的冒出了头\n[00:25.36]单方的守侯 试探的温柔\n[00:29.24]还是少了点什么\n[00:31.80]\n[00:33.39]遥远两端 爱挂在天空飞\n[00:37.30]风停了也无所谓 只因为你总说\n[00:43.35]Everthing will be okay\n[00:46.55]\n[00:48.41]准备好了three two one\n[00:51.03]I\'m always online\n[00:52.82]和你one to one 爱开始扩散\n[00:56.78]我们连结了 穿越\n[00:59.60]天空 银河 oh\n[01:04.24]开始倒数three two one \n[01:06.58]删除我的孤单\n[01:08.75]more and more尽是深刻\n[01:13.66]爱亮了 爱笑了 \n[01:15.53]I\'m always online\n[01:18.23]\n[01:21.46]变色的生活 任性的挑拨\n[01:25.19]疯狂的冒出了头\n[01:29.38]单方的守侯 试探的温柔\n[01:33.00]却还是少了点什么\n[01:36.08]\n[01:37.37]遥远两端 爱挂在天空飞\n[01:41.35]风停了也无所谓 只因为你总说\n[01:47.25]Everthing Will Be okay\n[01:50.90]\n[01:52.12]我准备好了three two one, \n[01:54.97]I\'m always online\n[01:56.80]和你one to one 爱开始扩散\n[02:00.76]我们连结了 穿越 \n[02:03.58]天空 银河 oh\n[02:08.26]开始倒数three two one \n[02:10.58]删除我的孤单\n[02:12.78]more and more尽是深刻\n[02:17.58]爱亮了 爱笑了\n[02:19.52]I\'m always online\n[02:22.72]\n[02:42.21]准备好了three two one, \n[02:44.91]I\'m always online\n[02:46.78]和你one to one 爱开始扩散\n[02:50.77]我们连结了 穿越 \n[02:53.84]天空 银河\n[02:58.16]开始倒数hree two one \n[03:00.59]删除我的孤单\n[03:02.65]more and more尽是深刻\n[03:07.56]爱亮了 爱笑了 \n[03:09.46]I\'m always online\n[03:15.71]爱亮了 爱笑了 \n[03:17.74]I\'m always online\n[03:23.44]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2131', '100295', '2113203', 'http://yinyueshiting.baidu.com/data2/music/124475927/124475927.mp3?xcode=9b671a25b7393b10e23725bcf113dbcd', '', '屋顶', '温岚,周杰伦', '1472423278', '0', '0', '0', '[00:02.21]屋顶 \n[00:04.62]演唱：周杰伦、温岚\n[00:06.01]\n[00:24.16]半夜睡不着觉 \n[00:26.82]把心情哼成歌\n[00:29.52]只好到屋顶找\n[00:31.44]另一个梦境\n[00:37.16]\n[00:40.43]睡梦中被敲醒 \n[00:43.11]我还是不确定\n[00:45.83]怎会有动人 \n[00:47.44]弦律在对面的屋顶\n[00:51.39]我悄悄关上门 \n[00:54.04]带着希望上去\n[00:56.74]原来是我梦里\n[00:58.84]常出现的那个人\n[01:01.06]那个人不就是我梦里 \n[01:04.30]那模糊的人\n[01:06.38]我们有同样的默契\n[01:11.79]用天线（用天线） \n[01:14.49]排成爱你的形状Ho Ho\n[01:21.31]在屋顶唱着你的歌 \n[01:24.23]在屋顶和我爱的人\n[01:26.86]让星星点缀成\n[01:28.67]最浪漫的夜晚\n[01:33.00]拥抱这时刻\n[01:34.95]这一分一秒全都停止\n[01:41.14]爱开始纠结\n[01:43.21]在屋顶唱着你的歌 \n[01:45.86]在屋顶和我爱的人\n[01:48.55]将泛黄的的夜献给\n[01:50.86]最孤独的月\n[01:54.74]拥抱这时刻\n[01:56.73]这一分一秒全都停止\n[02:02.84]爱开始纠结 \n[02:05.03]梦有你而美\n[02:09.74]\n[02:43.26]半夜睡不着觉 \n[02:45.98]把心情哼成歌\n[02:48.63]只好到屋顶找另一个梦境\n[02:55.59]\n[02:59.62]睡梦中被敲醒 \n[03:02.32]我还是不确定\n[03:05.02]怎会有动人 \n[03:06.71]弦律在对面的屋顶\n[03:10.51]我悄悄关上门 \n[03:13.18]带着希望上去\n[03:15.93]原来是我梦里\n[03:17.94]常出现的那个人\n[03:20.36]那个人不就是我梦里 \n[03:23.41]那模糊的人\n[03:25.46]我们有同样的默契\n[03:30.62]用天线(用天线 )\n[03:33.67]排成爱你的形状Ho Ho\n[03:40.45]在屋顶唱着你的歌 \n[03:43.29]在屋顶和我爱的人\n[03:46.07]让星星点缀成\n[03:47.90]最浪漫的夜晚\n[03:52.13]拥抱这时刻\n[03:54.37]这一分一秒全都停止\n[04:00.57]爱开始纠结\n[04:02.41]在屋顶唱着你的歌 \n[04:05.06]在屋顶和我爱的人\n[04:07.83]将泛黄的的夜\n[04:09.32]献给最孤独的月\n[04:13.97]拥抱这时刻\n[04:15.92]这一分一秒全都停止\n[04:22.09]爱开始纠结 \n[04:24.81]梦有你而美\n[04:28.63]\n[04:38.06]让我爱你是谁 (是我)\n[04:40.66]让你爱我是谁 (是你)\n[04:43.39]怎会有 \n[04:44.22]动人弦律环绕在我俩的身边\n[04:48.82]让我爱你是谁 (是我)\n[04:51.57]让你爱我是谁 (是你)\n[04:54.55]原来是这屋顶有美丽的邂逅\n[05:01.88]\n[05:04.18]在屋顶唱着你的歌 \n[05:07.26]在屋顶和我爱的人\n[05:13.65]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2132', '100295', '255982860', 'http://yinyueshiting.baidu.com/data2/music/256005178/256005178.mp3?xcode=f50cbb56a30729bf5990d376263109e0', '', '一半', '薛之谦', '1472423838', '0', '0', '0', '[ti:一半]\n[ar:薛之谦]\n[al:一半]\n[by:珍妮]\n匹配时间为: 04 分 46 秒 的歌曲\n[00:00.00] \n[00:01.01]一半  \n[00:03.01]\n[00:07.38]作词：薛之谦\n[00:10.56]作曲：李荣浩\n[00:13.27]演唱：薛之谦\n[00:15.01]\n[00:17.77]多平淡 所以自己刻意为难\n[00:25.49]多遗憾 被抛弃的人没喜感\n[00:33.61]像被人围起来 就特别放不开\n[00:41.67]都在期待 角色要坏 别委屈了人才\n[00:50.10]别期待 伤人的话变得柔软\n[00:57.63]也别揭穿 剧透的电影不好看\n[01:05.65]隔墙有只耳朵 嘲笑你多难过\n[01:13.84]你越反驳 越像示弱\n[01:17.83]请别再招惹我\n[01:22.66]我可以 为我们的散 承担一半\n[01:27.45]可我偏要摧毁所有的好感\n[01:31.52]看上去能孤独的很圆满\n[01:38.73]我做作的表情让自己很难堪\n[01:43.75]可感情这玩意儿怎么计算\n[01:47.44]别两难 hey晚安\n[01:54.84]少了有点不甘 但多了太烦\n[02:01.06]\n[02:15.95]多困难 狠话有几句新鲜感\n[02:23.73]又有多难 掩饰掉全程的伤感\n[02:31.58]我毁了艘小船 逼我们隔着岸\n[02:39.75]冷眼旁观 最后一段 对白还有点烂\n[02:48.67]你可以 为我们的散 不用承担\n[02:53.41]是我 投入到一半 感到不安\n[02:57.51]好过未来一点一点纠缠\n[03:04.61]我帮你 摘下的那颗廉价指环\n[03:09.67]像赠品附送完 人群涣散\n[03:13.36]心很酸 烟很淡\n[03:20.80]难过若写不完 用情歌刁难\n[03:28.59]我非要 锈了的皇冠 还不肯摘\n[03:33.64]在悲伤明显前 举杯离散\n[03:37.41]为何亏欠的人 特别勇敢\n[03:44.70]我撑到 你的恨 开始无限扩散\n[03:49.64]该流的泪才刚刚流一半\n[03:53.34]别有关 就两断\n[04:00.83]故事已经说完 懒得圆满\n[04:07.51]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2133', '100295', '242078437', 'http://yinyueshiting.baidu.com/data2/music/c63861e9b77a9bc200d9178434e17229/242078536/242078536.mp3?xcode=f50cbb56a30729bf2e999d51bc758b81', '', '演员', '薛之谦', '1472423852', '0', '0', '0', '[00:00.32]演员\n[00:01.00]\n[00:01.61]作词：薛之谦\n[00:02.64]作曲：薛之谦\n[00:03.00]演唱：薛之谦\n[00:04.20]\n[00:21.12]简单点说话的方式简单点\n[00:30.20]递进的情绪请省略\n[00:33.64]你又不是个演员\n[00:36.38]别设计那些情节\n[00:39.36]\n[00:41.93]没意见我只想看看你怎么圆\n[00:51.54]你难过的太表面 像没天赋的演员\n[00:57.15]观众一眼能看见\n[01:00.19]\n[01:02.22]该配合你演出的我演视而不见\n[01:07.68]在逼一个最爱你的人即兴表演\n[01:12.90]什么时候我们开始收起了底线\n[01:18.02]顺应时代的改变看那些拙劣的表演\n[01:23.42]可你曾经那么爱我干嘛演出细节\n[01:28.63]我该变成什么样子才能延缓厌倦\n[01:33.87]原来当爱放下防备后的这些那些\n[01:39.37]才是考验\n[01:41.97]\n[01:44.60]没意见你想怎样我都随便\n[01:54.53]你演技也有限\n[01:57.58]又不用说感言\n[02:00.15]分开就平淡些\n[02:02.99]\n[02:05.00]该配合你演出的我演视而不见\n[02:10.53]别逼一个最爱你的人即兴表演\n[02:15.81]什么时候我们开始没有了底线\n[02:21.01]顺着别人的谎言被动就不显得可怜\n[02:26.43]可你曾经那么爱我干嘛演出细节\n[02:31.52]我该变成什么样子才能配合出演\n[02:36.72]原来当爱放下防备后的这些那些\n[02:41.86]都有个期限\n[02:44.60]\n[02:47.56]其实台下的观众就我一个\n[02:53.04]其实我也看出你有点不舍\n[02:58.34]场景也习惯我们来回拉扯\n[03:02.93]还计较着什么\n[03:07.39]\n[03:08.71]其实说分不开的也不见得\n[03:14.04]其实感情最怕的就是拖着\n[03:19.21]越演到重场戏越哭不出了\n[03:24.07]是否还值得\n[03:28.12]\n[03:29.07]该配合你演出的我尽力在表演\n[03:34.39]像情感节目里的嘉宾任人挑选\n[03:39.68]如果还能看出我有爱你的那面\n[03:44.82]请剪掉那些情节让我看上去体面\n[03:50.04]可你曾经那么爱我干嘛演出细节\n[03:55.31]不在意的样子是我最后的表演\n[04:01.05]是因为爱你我才选择表演 这种成全');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2134', '100543', '69135510', 'http://yinyueshiting.baidu.com/data2/music/246422156/246422156.mp3?xcode=7b9106c709b4418c0e36f2067d58dcad', '', '回忆的沙漏', '邓紫棋', '1472425861', '233', '0', '0', '[ti:回忆的沙漏]\n[ar:邓紫棋]\n[al:G.E.M EP]\n[offset:500]\n\n[00:01.66]邓紫棋 - 回忆的沙漏\n[00:03.66]作词：庭竹\n[00:05.66]作曲：G.E.M.\n[00:12.66]\n[00:14.66]拼图一片片失落　像枫叶的冷漠\n[00:21.68]墙上的钟　默默数着寂寞\n[00:28.94]咖啡飘散过香味　剩苦涩陪着我\n[00:36.29]想念的心　埋葬我在深夜的脆弱\n[00:43.67]无尽的苍穹　满天的星座\n[00:47.69]你的光亮一闪而过\n[00:51.15]只想要记住这永恒的瞬间\n[00:56.46]\n[00:57.17]像流星的坠落　灿烂夺去了轮廓\n[01:05.62]刹那过後　世界只是　回忆的沙漏\n[01:11.72]像流星的坠落　绚丽地点亮了整个星空\n[01:19.82]像你故事在我生命留下　不褪色的伤口\n[01:26.83]\n[01:29.24]湖水守候着沈默　等待天边的月\n[01:36.33]孤独的水面　却漆黑整夜\n[01:43.57]夜雾凝结的泪光　被蒸发在角落\n[01:51.11]他无情地　遗忘我在追忆的漩涡\n[01:58.05]无尽的苍穹　满天的星座\n[02:02.10]你的光亮　一闪而过\n[02:05.70]只想要记住这永恒的瞬间\n[02:11.40]\n[02:11.81]像流星的坠落　灿烂夺去了轮廓\n[02:20.22]这刹那过後　世界只是　回忆的沙漏\n[02:26.17]像流星的坠落　绚丽地点亮了整个星空\n[02:34.37]像你故事在我生命留下　不褪色的伤口\n[02:40.56]\n[02:41.91]在黑夜的尽头　是你的捉弄\n[02:45.61]和无声的伤痛\n[02:48.95]燃烧过後 　只剩静默\n[02:56.69]\n[03:00.77]像流星的坠落　灿烂夺去了轮廓\n[03:09.21]这刹那过後　世界只是　回忆的沙漏\n[03:16.91]像流星的坠落　绚丽地点亮了整个星空\n[03:23.71]像你故事在我生命留下　不褪色的伤口\n[03:31.71]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2135', '100295', '438173', 'http://yinyueshiting.baidu.com/data2/music/64097047/64097047.mp3?xcode=84c06a3e8baf54996a37d80a2c02fc5b', '', '我很好', '刘若英', '1472429650', '0', '0', '0', '[ti:0]\n\n[ar:0]\n\n[al:0]\n\n[by:0]\n\n[offset:0]\n\n[00:01.02]我很好\n\n[00:01.77]演唱;刘若英\n\n[00:02.67]\n\n[00:15.81]沙发上睡着　\n\n[00:19.04]孤单冷醒的破晓\n\n[00:23.35]冷的面条　\n\n[00:25.42]热的泪痕　\n\n[00:27.12]啤酒在苦笑\n\n[00:30.96]当时的煎熬　\n\n[00:34.85]当时的心痛如绞\n\n[00:38.58]天终于亮了　\n\n[00:42.39]遗憾终于退潮\n\n[00:45.00]\n\n[00:45.32]终于能够恨不再疯　\n\n[00:48.23]泪不再掉　\n\n[00:50.13]心不跑\n\n[00:52.95]一定会有一个人　\n\n[00:55.44]一段新的美好\n\n[00:59.34]\n\n[01:01.48]谁让我拥抱　\n\n[01:04.42]谁让我再一次心跳\n\n[01:08.27]就算爱情让我再次的跌倒　\n\n[01:13.01]伤痕也要是一种骄傲\n\n[01:16.96]谁让我拥抱　\n\n[01:19.43]谁让我疯狂的心跳\n\n[01:23.46]就算明天整个城市要倾倒　\n\n[01:28.10]也让我爱到最后一秒\n\n[01:33.39]\n\n[01:47.57]丢掉电影票　\n\n[01:50.52]删掉信件跟合照\n\n[01:55.28]洗了床单　\n\n[01:56.79]剪了头发　\n\n[01:58.73]清空了烦恼\n\n[02:02.59]恨可以很小　\n\n[02:05.82]小到眼泪能冲掉\n\n[02:10.64]我现在很好　\n\n[02:13.14]可以重新起跑\n\n[02:16.37]\n\n[02:16.84]终于能够恨不再疯　\n\n[02:19.68]泪不再掉　心不跑\n\n[02:24.46]一定会有一个人　\n\n[02:27.06]一段新的美好\n\n[02:31.13]\n\n[02:33.04]谁让我拥抱　\n\n[02:35.59]谁让我再一次心跳\n\n[02:39.58]就算爱情让我再次的跌倒　\n\n[02:44.44]伤痕也要是一种骄傲\n\n[02:48.21]谁让我拥抱　\n\n[02:50.88]谁让我疯狂的心跳\n\n[02:55.39]就算明天整个城市要倾倒　\n\n[02:59.57]也让我爱到最后一秒\n\n[03:04.82]\n\n[03:05.64]地铁涌出了人潮　\n\n[03:09.13]幸福涌出了预兆\n\n[03:12.15]我会找回当初对爱天真的霸道\n\n[03:18.85]\n\n[03:22.53]谁让我拥抱　\n\n[03:25.17]谁让我再一次心跳\n\n[03:29.12]就算爱情让我再次的跌倒　\n\n[03:33.88]伤痕也要是一种骄傲\n\n[03:37.78]谁让我拥抱　\n\n[03:40.20]谁让我疯狂的心跳\n\n[03:44.49]就算明天整个城市要倾倒　\n\n[03:49.24]也让我爱到最后一秒\n\n[03:53.98]谁让我拥抱　\n\n[03:55.63]谁让我疯狂的心跳\n\n[03:59.72]就算明天整个城市要倾倒　\n\n[04:04.53]也让我爱到最后一秒\n\n[04:13.98]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2136', '100561', '74015268', 'http://yinyueshiting.baidu.com/data2/music/240108123/240108123.mp3?xcode=a63a11116c86fb4c48ae5c20176c0886', '', '泼墨人间', '风定初', '1472511891', '246', '0', '0', '我有闲情只自遣，挥毫泼墨向人间。\n流云绢三尺才剪\n抬手挥出杀伐决断\n簪花笺半幅空悬\n横笔宕开色相缠绵\n调浓淡 潇潇雨落江南\n一勾一点繁花十万\n试深浅 我好趁烟波下钓船\n一折一顿雪漫千山\n洇一痕水墨入流年\n惊鸿飞去春华枝满\n染一湾江湖烈酒洗剑\n转身拂袖天心月圆\n借丹青 描个生世长安\n一哭一笑红尘疯癫\n遣风雨 造化在指掌任变幻\n一天一地我主江山\n颠狂二三两 并风月四五钱\n挥毫泼墨向人间\n欲唤来山川佐酒 煮沸江河一盏\n我尚未至怎开筵\n岁华留白未落款\n闲情自将风流挽\n谁共我走马浪荡尘寰\n借丹青 描个生世长安\n一哭一笑红尘疯癫\n遣风雨 造化在指掌任变幻\n一天一地我主江山\n颠狂二三两 并风月四五钱\n挥毫泼墨向人间\n欲唤来山川佐酒 煮沸江河一盏\n我尚未至怎开筵\n瑶台枕风抱月眠\n醉呼青鸟天外伴\n可共我提笔泼墨人间');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2137', '100229', '12946574', 'http://yinyueshiting.baidu.com/data2/music/123312917/123312917.mp3?xcode=144a45917d58847160e85653b43f0cf4', '', '贝加尔湖畔', '李健', '1472517379', '247', '0', '0', '[00:20.00]贝加尔湖畔\n[00:22.06]词曲：李健\n[00:23.80]演唱：李健\n[00:28.09]\n[00:48.76]在我的怀里 在你的眼里\n[00:55.71]那里春风沉醉 那里绿草如茵\n[01:04.06]月光把爱恋 洒满了湖面\n[01:11.90]两个人的篝火 照亮整个夜晚\n[01:18.90]多少年以后 如云般游走\n[01:25.39]那变换的脚步 让我们难牵手\n[01:33.89]这一生一世 有多少你我\n[01:41.53]被吞没在月光如水的夜里\n[01:49.51]\n[01:54.64]多想某一天 往日又重现\n[02:01.53]我们流连忘返 在贝加尔湖畔\n[02:09.79]\n[02:42.70]多少年以后 往事随云走\n[02:50.39]那纷飞的冰雪容不下那温柔\n[02:57.82]这一生一世 这时间太少\n[03:06.14]不够证明融化冰雪的深情\n[03:13.49]\n[03:18.39]就在某一天 你忽然出现\n[03:25.83]你清澈又神秘 在贝加尔湖畔\n[03:34.08]你清澈又神秘 像贝加尔湖畔\n[03:43.98]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2138', '100232', '2117515', 'http://yinyueshiting.baidu.com/data2/music/116794465/116794465.mp3?xcode=e4eb72282de8988931189f16455400eb', '', '冷血动物', '谢天笑', '1472519312', '0', '0', '0', '[00:01.0]冷血动物\n[00:03.98]演唱;谢天笑\n[00:04.46]\n[00:38.63]我在水里 也在陆上 阳光照射着我没有意义 \n[00:47.45]我在梦里 在你怀里 我在草里非常隐蔽 \n[02:29.15][00:56.66]飘在水上 一切正常 咀嚼着泥 我很忧伤 \n[02:37.98][01:05.74]趴在树上 并不惊慌 \n[02:42.54][01:10.38]很长很长时间才会死亡 才会死亡 \n[02:55.58][01:23.61]我一步一步走向明天 我一夜一夜的睡眠\n[03:05.20][01:32.81]我一句一句把话说完 永远失去了昨天 \n[03:14.35][01:42.02]总有一天都化作云烟 不可能再有人世间 \n[03:23.66][01:51.43]蹬大这双眼看看月亮 仍然高挂在云上\n[03:31.74][01:59.26]也飘在水上 飘在水上');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2139', '100232', '2117519', 'http://yinyueshiting.baidu.com/data2/music/65555314/65555314.mp3?xcode=b2875e95fa1a4be0d1fcd5f6c50b1ab0', '', '向阳花', '谢天笑', '1472519614', '0', '0', '0', '[ti:向阳花]\n[ar:谢天笑与冷血动物]\n[al:谢天笑X.T.X]\n\n[00:00.50]向阳花\n[00:59.26]作词：谢天笑 作曲：谢天笑\n[01:03.26]演唱：谢天笑与冷血动物\n[01:05.26]\n[01:07.19]那美丽的天，总是一望无边\n[01:15.47]有粒种子埋在云下面\n[01:19.20]\n[01:23.59]营养来自这满地污泥\n[01:31.79]生根发芽，仍然顺从天意\n[01:35.65]\n[01:39.94]无数个雨点，在我面前洒满大地\n[01:48.59]站在这里，只有一个问题\n[01:54.00]\n[01:54.94]向阳花！如果你生长在黑暗下\n[02:02.75]向阳花，你会不会害怕\n[02:09.79]\n[02:28.58]那美丽的天，总是一望无边\n[02:36.83]有粒种子埋在云下面\n[02:40.74]\n[02:44.93]营养来自这满地污泥\n[02:53.01]生根发芽，仍然顺从天意\n[02:56.81]\n[03:01.27]无数个雨点，在我面前洒满大地\n[03:09.88]站在这里，只有一个问题\n[03:14.98]\n[03:15.94]向阳花！如果你生长在黑暗下\n[03:24.06]向阳花，你会不会再继续开花\n[03:30.72]\n[03:36.51]会不会害怕，会不会害怕\n[03:40.37]向阳花，你会不会再继续开花\n[03:48.81]\n[03:52.52]你会不会害怕，会不会害怕\n[03:56.64]向阳花，你会不会再继续开花\n[04:05.26]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2140', '100561', '33341939', 'http://yinyueshiting.baidu.com/data2/music/241199554/241199554.mp3?xcode=39f2091cf336125f37ff48ca31f559c2', '', '方圆几里', '薛之谦', '1472523452', '263', '0', '0', '[00:01.82]方圆几里\n[00:04.87]词：薛之谦 曲：薛之谦\n[00:07.56]演唱：薛之谦\n[00:11.24]\n[00:18.35]感觉很诚恳 是好事\n[00:23.85]\n[00:26.41]不需要发誓 那么幼稚\n[00:32.47]\n[00:34.71]本以为可以 就这样随你\n[00:42.58]反正我也无处可去\n[00:49.38]\n[00:50.69]我怕太负责任的人\n[00:58.74]因为他随时会牺牲\n[01:04.87]\n[01:06.94]爱不爱都可以 我怎样都依你\n[01:13.67]连借口 我都帮你寻\n[01:20.60]\n[01:21.22]与其在你不要的世界里\n[01:25.21]不如痛快把你忘记\n[01:29.52]这道理谁都懂 说容易 爱透了还要嘴硬\n[01:37.70]我宁愿 留在你方圆几里\n[01:41.50]我的心 要不回就送你\n[01:45.86]因为我爱你 和你没关系\n[01:52.61]\n[02:12.24]感觉会压抑 的样子\n[02:16.86]\n[02:20.23]勉强 也没什么意思\n[02:25.04]\n[02:28.16]我不算很自私 也越来越懂事\n[02:34.78]\n[02:35.84]爱你只是我的事\n[02:41.95]\n[02:42.65]与其在你不要的世界里\n[02:46.51]不如痛快把你忘记\n[02:51.00]这道理谁都懂 说容易 爱透了还要嘴硬\n[02:57.55]\n[02:58.99]我宁愿 留在你方圆几里\n[03:02.93]至少能感受你的悲喜\n[03:07.36]在你需要我的时候 就能陪你\n[03:14.10]\n[03:15.34]我在你 不要的世界里\n[03:19.53]何苦不找个人来代替\n[03:23.39]可惜我 谁劝都不停\n[03:30.79]\n[03:31.80]我宁愿 留在你方圆几里\n[03:35.41]我的心 要不回就送你\n[03:41.41]爱不爱都可以 我怎样都依你\n[03:48.20]因为我爱你 和你没关系\n[03:55.19]\n[03:55.88]我的爱 扩散在方圆几里\n[03:59.82]近的能 听见你的呼吸\n[04:04.37]只要你转身 我就在这里');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2141', '100561', '100575177', 'http://yinyueshiting.baidu.com/data2/music/100613720/100613720.mp3?xcode=72a3e76e135069b73d7cab37c0a62272', '', '你还要我怎样', '薛之谦', '1472523863', '310', '0', '0', '[00:00.00]你还要我怎样\n[00:06.00]词：薛之谦 曲：薛之谦\n[00:09.00]演唱：薛之谦\n[00:22.00]\n[00:26.25]你停在了这条我们熟悉的街\n[00:36.18]把你准备好的台词全念一遍\n[00:44.02]我还在逞强 说着谎\n[00:48.37]也没能力遮挡 你去的方向\n[00:53.83]至少分开的时候我落落大方\n[01:01.73]\n[01:05.72]我后来都会选择绕过那条街\n[01:14.92]又多希望在另一条街能遇见\n[01:23.50]思念在逞强 不肯忘\n[01:28.19]怪我没能力跟随 你去的方向\n[01:34.00]若越爱越被动 越要落落大方\n[01:41.65]\n[01:43.65]你还要我怎样 要怎样\n[01:48.30]你突然来的短信就够我悲伤\n[01:53.30]我没能力遗忘 你不用提醒我\n[01:58.65]哪怕结局就这样\n[02:03.24]我还能怎样 能怎样\n[02:08.20]最后还不是落得情人的立场\n[02:13.15]你从来不会想 我何必这样\n[02:21.60]\n[02:45.64]我慢慢的回到自己的生活圈\n[02:53.89]也开始可以接触新的人选\n[03:03.35]爱你到最后 不痛不痒\n[03:08.70]留言在计较 谁爱过一场\n[03:13.35]我剩下一张 没后悔的模样\n[03:21.35]\n[03:22.95]你还要我怎样 要怎样\n[03:28.20]你千万不要在我婚礼的现场\n[03:33.20]我听完你爱的歌 就上了车\n[03:39.10]爱过你很值得\n[03:43.30]我不要你怎样 没怎样\n[03:48.20]我陪你走的路你不能忘\n[03:53.50]因为那是我 最快乐的时光\n[04:03.75]\n[04:05.80]后来我的生活还算理想\n[04:16.05]没为你落到孤单的下场\n[04:24.13]有一天晚上 梦一场\n[04:28.56]你白发苍苍 说带我流浪\n[04:34.41]我还是没犹豫 就随你去天堂\n[04:44.10]不管能怎样 我能陪你到天亮\n[04:57.17]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2142', '100288', '121031569', 'http://yinyueshiting.baidu.com/data2/music/121075365/121075365.mp3?xcode=5e7f34e33a8adfd3bd5cc648288db746', '', '幻想即兴曲', '肖邦', '1472525708', '0', '0', '0', '');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2143', '100295', '5738289', 'http://yinyueshiting.baidu.com/data2/music/134373013/134373013.mp3?xcode=a6adcbef31788595c9bf3d409e407d1c', '', '她说', '林俊杰', '1472527838', '0', '0', '0', '[ti:她说]\n[ar:林俊杰]\n[al:她说]\n\n[00:00.03]她说\n[00:07.31]作词：孙燕姿 作曲：林俊杰\n[00:09.80]演唱：林俊杰\n[00:12.88]\n[00:25.63]他静悄悄地来过\n[00:30.81]他慢慢带走沉默\n[00:36.69]只是最后的承诺\n[00:42.59]还是没有带走了寂寞\n[00:47.02]\n[00:48.33]我们爱的没有错\n[00:53.72]只是美丽的独秀太折磨\n[01:00.33]她说无所谓\n[01:05.17]只要能在夜里 翻来覆去的时候有寄托\n[01:11.05]\n[01:11.71]等不到天黑 烟火不会太完美\n[01:17.10]回忆烧成灰 还是等不到结尾\n[01:23.45]她曾说的无所谓 我怕一天一天被摧毁\n[01:31.64]\n[01:34.13]等不到天黑 不敢凋谢的花蕾\n[01:39.70]绿叶在跟随 放开刺痛的滋味\n[01:44.75]今后不再怕天明 我想只是害怕清醒\n[01:56.99]\n[02:23.69]他静悄悄地来过\n[02:29.92]他慢慢带走沉默\n[02:35.49]只是最后的承诺\n[02:40.90]还是没有带走了寂寞\n[02:45.94]\n[02:46.84]我们爱的没有错\n[02:52.32]只是美丽的独秀太折磨\n[02:58.80]她说无所谓\n[03:03.55]只要能在夜里 翻来覆去的时候有寄托\n[03:09.53]\n[03:10.26]等不到天黑 烟火不会太完美\n[03:15.77]回忆烧成灰 还是等不到结尾\n[03:21.97]她曾说的无所谓 我怕一天一天被摧毁\n[03:30.06]\n[03:32.62]等不到天黑 不敢凋谢的花蕾\n[03:38.39]绿叶在跟随 放开刺痛的滋味\n[03:43.39]今后不再怕天明 我想只是害怕清醒\n[03:53.85]\n[03:55.25]等不到天黑 烟火不会太完美\n[04:00.89]回忆烧成灰 还是等不到结尾\n[04:07.14]她曾说的无所谓 我怕一天一天被摧毁\n[04:15.43]\n[04:17.88]等不到天黑 不敢凋谢的花蕾\n[04:23.59]绿叶在跟随 放开刺痛的滋味\n[04:28.49]今后不再怕天明 我想只是害怕清醒\n[04:40.52]不怕天明 我想只是害怕清醒\n[04:52.95]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2144', '100577', '258351172', 'http://yinyueshiting.baidu.com/data2/music/b3045edf8f40c37ac06dd3964dea4730/258351242/258351242.mp3?xcode=16ea2ca541fc847ed1fcd5f6c50b1ab0', '', '好想你', '朱主爱', '1472585796', '0', '0', '0', '[00:00.18]好想你 \n[00:00.38]\n[00:01.2]演唱：Joyce Chu\n[00:01.93]\n[00:02.18]想要传送一封简讯给你\n[00:05.26]我好想好想你\n[00:07.63]想要立刻打通电话给你\n[00:10.27]我好想好想你\n[00:12.77]每天起床的第一件事情\n[00:15.21]就是好想好想你\n[00:17.88]无论晴天还是下雨\n[00:20.37]都好想好想你\n[00:22.62]每次当我一说我好想你\n[00:25.27]你都不相信\n[00:27.44]但却总爱问我有没有想你\n[00:32.43]我不懂的甜言蜜语\n[00:34.59]所以只说好想你\n[00:37.38]反正说来说去\n[00:38.90]都只想让你开心\n[00:41.95]好想你 好想你 好想你 好想你\n[00:47.33]是真的真的好想你\n[00:49.47]不是假的假的好想你\n[00:51.95]好想你 好想你 好想你 好想你\n[00:57.18]是够力够力好想你\n[00:59.34]真的西北西北好想你\n[01:01.84]好想你\n[01:22.20]每次当我一说我好想你\n[01:24.68]你都不相信\n[01:26.97]但却总爱问我有没有想你\n[01:31.83]我不懂的甜言蜜语\n[01:34.01]所以只说好想你\n[01:36.69]反正说来说去\n[01:38.26]都只想让你开心\n[01:41.44]好想你 好想你 好想你 好想你\n[01:46.69]是真的真的好想你\n[01:48.78]不是假的假的好想你\n[01:51.24]好想你 好想你 好想你 好想你\n[01:56.59]是够力够力好想你\n[01:58.76]真的西北西北好想你\n[02:01.19]好想你 好想你 好想你 好想你\n[02:06.45]是真的真的好想你\n[02:08.51]不是假的假的好想你\n[02:11.02]好想你 好想你 好想你 好想你\n[02:16.28]是够力够力好想你\n[02:18.46]真的西北西北好想你\n[02:20.92]好想你\n[02:23.71]好想你\n[02:26.85]好想你\n[02:30.64]');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2145', '100368', '74018498', 'http://yinyueshiting.baidu.com/data2/music/240397991/240397991.mp3?xcode=542c391e7a690da6bdfb5657fcf2438e', '', '摩羯座女孩', '晓夏', '1472586728', '0', '0', '0', '魔羯座女孩\n演唱：晓夏    詞：秀天下-黃中原    曲：秀天下-潘攀\n也许是那件 蓝色衬衫\n也许那一眼 太过温暖\n心中的你 喧嚣在夜晚\n我以为可以 平平淡淡\n规律的日子 被你打乱\n天天想你 我该怎么办\n不要不要再发呆\n不要不要再等待\n要鼓起勇气 把爱你都说出来\nLa La La\nI Love you 坚定不移\n魔羯座的女孩 不会懒散\nLa La La\nI Love you 好好珍惜\n我的爱为你存在\nLa La La\nI Love you 傻傻爱你\n魔羯座的女孩 爱得简单\nLa La La\nI Love you 全心全意\n我的世界就是你\n编曲：近藤昭雄\n和声：付三土\n缩混：家守久雄');
INSERT INTO `%DB_PREFIX%user_music` VALUES ('2146', '100295', '100575177', 'http://yinyueshiting.baidu.com/data2/music/100613720/100613720.mp3?xcode=f2b12accd46a556838b319dd5c3c046e', '', '你还要我怎样', '薛之谦', '1472619549', '0', '0', '0', '[00:00.00]你还要我怎样\n[00:06.00]词：薛之谦 曲：薛之谦\n[00:09.00]演唱：薛之谦\n[00:22.00]\n[00:26.25]你停在了这条我们熟悉的街\n[00:36.18]把你准备好的台词全念一遍\n[00:44.02]我还在逞强 说着谎\n[00:48.37]也没能力遮挡 你去的方向\n[00:53.83]至少分开的时候我落落大方\n[01:01.73]\n[01:05.72]我后来都会选择绕过那条街\n[01:14.92]又多希望在另一条街能遇见\n[01:23.50]思念在逞强 不肯忘\n[01:28.19]怪我没能力跟随 你去的方向\n[01:34.00]若越爱越被动 越要落落大方\n[01:41.65]\n[01:43.65]你还要我怎样 要怎样\n[01:48.30]你突然来的短信就够我悲伤\n[01:53.30]我没能力遗忘 你不用提醒我\n[01:58.65]哪怕结局就这样\n[02:03.24]我还能怎样 能怎样\n[02:08.20]最后还不是落得情人的立场\n[02:13.15]你从来不会想 我何必这样\n[02:21.60]\n[02:45.64]我慢慢的回到自己的生活圈\n[02:53.89]也开始可以接触新的人选\n[03:03.35]爱你到最后 不痛不痒\n[03:08.70]留言在计较 谁爱过一场\n[03:13.35]我剩下一张 没后悔的模样\n[03:21.35]\n[03:22.95]你还要我怎样 要怎样\n[03:28.20]你千万不要在我婚礼的现场\n[03:33.20]我听完你爱的歌 就上了车\n[03:39.10]爱过你很值得\n[03:43.30]我不要你怎样 没怎样\n[03:48.20]我陪你走的路你不能忘\n[03:53.50]因为那是我 最快乐的时光\n[04:03.75]\n[04:05.80]后来我的生活还算理想\n[04:16.05]没为你落到孤单的下场\n[04:24.13]有一天晚上 梦一场\n[04:28.56]你白发苍苍 说带我流浪\n[04:34.41]我还是没犹豫 就随你去天堂\n[04:44.10]不管能怎样 我能陪你到天亮\n[04:57.17]');

-- ----------------------------
-- Table structure for `%DB_PREFIX%user_refund`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%user_refund`;
CREATE TABLE `%DB_PREFIX%user_refund` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(20) NOT NULL,
  `money` double(20,4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '提现申请时间',
  `reply` text NOT NULL COMMENT '提现审核回复',
  `is_pay` tinyint(1) NOT NULL COMMENT '0 表示未审核;1 表示 允许操作成功；2 表示 未允许操作成功;3 表示 提现确认成功',
  `pay_time` int(11) NOT NULL,
  `memo` text NOT NULL COMMENT '提现的备注',
  `pay_log` text NOT NULL COMMENT '支付说明',
  `user_bank_id` int(11) NOT NULL COMMENT '银行ID',
  `ybdrawflowid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%user_refund
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video`;
CREATE TABLE `%DB_PREFIX%video` (
  `id` int(11) NOT NULL COMMENT 'id,也是房间room_id',
  `title` varchar(255) NOT NULL COMMENT '直播标题',
  `user_id` int(11) NOT NULL COMMENT '项目id',
  `live_in` tinyint(1) DEFAULT '1' COMMENT '是否直播中 1-直播中 0-已停止;2:正在创建直播;3:正在迁移数据',
  `watch_number` int(11) DEFAULT '0' COMMENT '当前实时观看人数（实际,不含虚拟人数,不包含机器人)',
  `virtual_watch_number` int(10) NOT NULL DEFAULT '0' COMMENT '当前虚拟观看人数',
  `vote_number` int(11) DEFAULT '0' COMMENT '获得票数',
  `cate_id` int(11) DEFAULT NULL COMMENT '话题id',
  `province` varchar(20) DEFAULT NULL COMMENT '省份',
  `city` varchar(20) DEFAULT NULL COMMENT '城市',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `begin_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `end_date` date NOT NULL COMMENT '结束日期',
  `group_id` varchar(50) NOT NULL COMMENT '群组ID,通过create_group后返回的值;直播结束后解散群',
  `destroy_group_status` int(10) NOT NULL DEFAULT '1' COMMENT '1：未解散;0:已解散;其它为ErrorCode错码',
  `long_polling_key` varchar(255) NOT NULL COMMENT '通过create_group后返回的LongPollingKey值',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1热门; 0:非热门',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1新的; 0:非新的,直播结束时把它标识为：0？',
  `max_watch_number` int(10) NOT NULL DEFAULT '0' COMMENT '最大观看人数(每进来一人次加1）实际,不含虚拟人数,不包含机器人',
  `room_type` tinyint(1) NOT NULL COMMENT '房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）',
  `is_playback` tinyint(1) DEFAULT '0' COMMENT '是否可回放 0-否 ；1-是',
  `video_vid` varchar(255) NOT NULL COMMENT '视频地址',
  `monitor_time` datetime NOT NULL COMMENT '最后心跳监听时间；如果超过监听时间，则说明主播已经掉线了',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:删除;0:未删除;私有聊天或小于5分钟的视频，不保存',
  `robot_num` int(10) NOT NULL DEFAULT '0' COMMENT '聊天群中机器人数量',
  `robot_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加机器人时间（每隔20秒左右加几个人）',
  `channelid` varchar(50) NOT NULL COMMENT '旁路直播,频道ID',
  `is_aborted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:被服务器异常终止结束(主要是心跳超时)',
  `is_del_vod` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:表示已经清空了,录制视频;0:未做清空操作',
  `online_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '主播在线状态;1:在线(默认); 0:离开',
  `tipoff_count` int(10) NOT NULL DEFAULT '0' COMMENT '举报次数',
  `private_key` varchar(32) NOT NULL COMMENT '私密直播key',
  `share_type` varchar(30) NOT NULL COMMENT '分享类型WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,SINA',
  `sort` int(11) NOT NULL COMMENT '热门排序',
  `pai_id` int(11) NOT NULL COMMENT '竞拍id',
  PRIMARY KEY (`id`),
  KEY `idx_v_001` (`user_id`) USING BTREE,
  KEY `idx_v_003` (`live_in`) USING BTREE,
  KEY `idx_v_002` (`group_id`) USING BTREE,
  KEY `idx_v_004` (`private_key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%video
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_cate`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_cate`;
CREATE TABLE `%DB_PREFIX%video_cate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '话题名称',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效 1-有效 0-无效',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0-未删除 1-删除',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '从大到小排',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `image` varchar(255) NOT NULL COMMENT '广告图片',
  `url` varchar(255) NOT NULL COMMENT '广告连接地址',
  `desc` varchar(2000) NOT NULL COMMENT '描述',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '可关联一个用户ID，点击进去后，查看应该用户的相关的直播',
  `num` int(10) NOT NULL DEFAULT '0' COMMENT '当前直播数量,上下线时自动更新',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=741 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%video_cate
-- ----------------------------
INSERT INTO `%DB_PREFIX%video_cate` VALUES ('740', '新人直播', '1', '0', '0', '1471244925', '', '', '', '0', '1');


-- ----------------------------
-- Table structure for `%DB_PREFIX%video_history`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_history`;
CREATE TABLE `%DB_PREFIX%video_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id,也是房间room_id',
  `title` varchar(255) NOT NULL COMMENT '直播标题',
  `user_id` int(11) NOT NULL COMMENT '项目id',
  `live_in` tinyint(1) DEFAULT '1' COMMENT '是否直播中 1-直播中 0-已停止;2:正在创建直播;',
  `watch_number` int(11) DEFAULT '0' COMMENT '当前实时观看人数（实际,不含虚拟人数,不包含机器人)',
  `virtual_watch_number` int(10) NOT NULL DEFAULT '0' COMMENT '当前虚拟观看人数',
  `vote_number` int(11) DEFAULT '0' COMMENT '获得票数',
  `cate_id` int(11) DEFAULT '1' COMMENT '话题id',
  `province` varchar(20) DEFAULT NULL COMMENT '省份',
  `city` varchar(20) DEFAULT NULL COMMENT '城市',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `begin_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `end_date` date NOT NULL COMMENT '结束日期',
  `group_id` varchar(50) NOT NULL COMMENT '群组ID,通过create_group后返回的值;直播结束后解散群',
  `destroy_group_status` int(10) NOT NULL DEFAULT '1' COMMENT '1：未解散;0:已解散;其它为ErrorCode错码',
  `long_polling_key` varchar(255) NOT NULL COMMENT '通过create_group后返回的LongPollingKey值',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1热门; 0:非热门',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1新的; 0:非新的,直播结束时把它标识为：0？',
  `max_watch_number` int(10) NOT NULL DEFAULT '0' COMMENT '最大观看人数(每进来一人次加1）',
  `room_type` tinyint(1) NOT NULL COMMENT '房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）',
  `is_playback` tinyint(1) DEFAULT '0' COMMENT '是否可回放 0-否 ；1-是',
  `video_vid` varchar(255) NOT NULL COMMENT '视频地址',
  `monitor_time` datetime NOT NULL COMMENT '最后心跳监听时间；如果超过监听时间，则说明主播已经掉线了',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:删除;0:未删除;私有聊天或小于5分钟的视频，不保存',
  `robot_num` int(10) NOT NULL DEFAULT '0' COMMENT '聊天群中机器人数量',
  `robot_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加机器人时间（每隔20秒左右加几个人）',
  `channelid` varchar(50) NOT NULL COMMENT '旁路直播,频道ID',
  `is_aborted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:被服务器异常终止结束(主要是心跳超时)',
  `is_del_vod` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:表示已经清空了,录制视频;0:未做清空操作',
  `group2_id` varchar(50) NOT NULL COMMENT '回播时的聊天组ID',
  `group2_status` tinyint(1) NOT NULL DEFAULT '-2' COMMENT '-2;还未创建群;-1:正在创建；1：已创建;0:已解散;其它为ErrorCode错码',
  `tipoff_count` int(10) NOT NULL DEFAULT '0' COMMENT '举报次数',
  `private_key` varchar(32) NOT NULL COMMENT '私密直播key',
  `share_type` varchar(30) NOT NULL COMMENT '分享类型WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,SINA',
  `demand_video_status` tinyint(1) NOT NULL COMMENT '视频在热门中显示  1指上线，0指下线',
  PRIMARY KEY (`id`),
  KEY `idx_v_001` (`user_id`) USING BTREE,
  KEY `idx_v_003` (`live_in`) USING BTREE,
  KEY `idx_v_002` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of %DB_PREFIX%video_history
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_lianmai`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_lianmai`;
CREATE TABLE `%DB_PREFIX%video_lianmai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '连麦用户ID',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '连麦开始时间',
  `stop_time` int(11) NOT NULL DEFAULT '0' COMMENT '连麦结束时间',
  PRIMARY KEY (`id`),
  KEY `idx_vm_001` (`video_id`) USING BTREE,
  KEY `idx_vm_002` (`video_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播连麦记录';

-- ----------------------------
-- Records of %DB_PREFIX%video_lianmai
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_lianmai_history`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_lianmai_history`;
CREATE TABLE `%DB_PREFIX%video_lianmai_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '连麦用户ID',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '连麦开始时间',
  `stop_time` int(11) NOT NULL DEFAULT '0' COMMENT '连麦结束时间',
  PRIMARY KEY (`id`),
  KEY `idx_vm_001` (`video_id`) USING BTREE,
  KEY `idx_vm_002` (`video_id`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播连麦记录';

-- ----------------------------
-- Records of %DB_PREFIX%video_lianmai_history
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_monitor`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_monitor`;
CREATE TABLE `%DB_PREFIX%video_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `vote_number` int(10) NOT NULL DEFAULT '0' COMMENT '当前秀票数',
  `watch_number` int(11) NOT NULL DEFAULT '0' COMMENT '当前实际观看（占带宽流量的）人数',
  `lianmai_num` int(10) NOT NULL DEFAULT '0' COMMENT '当前连麦数量',
  `monitor_time` datetime NOT NULL COMMENT '时间采集点',
  `statistic_time` datetime NOT NULL COMMENT '所在统计时间点',
  PRIMARY KEY (`id`),
  KEY `idx_vm_001` (`video_id`) USING BTREE,
  KEY `idx_vm_002` (`video_id`,`user_id`) USING BTREE,
  KEY `idx_vm_003` (`video_id`,`statistic_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播心跳监听，每180秒监听一次;监听数据：时间点，秀票数，房间人数';

-- ----------------------------
-- Records of %DB_PREFIX%video_monitor
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_monitor_history`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_monitor_history`;
CREATE TABLE `%DB_PREFIX%video_monitor_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `vote_number` int(10) NOT NULL DEFAULT '0' COMMENT '当前秀票数',
  `watch_number` int(11) NOT NULL DEFAULT '0' COMMENT '当前实际观看（占带宽流量的）人数',
  `lianmai_num` int(10) NOT NULL DEFAULT '0' COMMENT '当前连麦数量',
  `monitor_time` datetime NOT NULL COMMENT '时间采集点',
  `statistic_time` datetime NOT NULL COMMENT '所在统计时间点',
  PRIMARY KEY (`id`),
  KEY `idx_vm_001` (`video_id`) USING BTREE,
  KEY `idx_vm_002` (`video_id`,`user_id`) USING BTREE,
  KEY `idx_vm_003` (`video_id`,`statistic_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播心跳监听，每180秒监听一次;监听数据：时间点，秀票数，房间人数';

-- ----------------------------
-- Records of %DB_PREFIX%video_monitor_history
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_private`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_private`;
CREATE TABLE `%DB_PREFIX%video_private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '私聊，被邀请的好友ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:已邀请;0:被踢出',
  `ActionStatus` varchar(10) NOT NULL,
  `ErrorCode` int(10) NOT NULL,
  `ErrorInfo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vs_001` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='私聊，被邀请的好友';

-- ----------------------------
-- Records of %DB_PREFIX%video_private
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_private_history`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_private_history`;
CREATE TABLE `%DB_PREFIX%video_private_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '私聊，被邀请的好友ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:已邀请;0:被踢出',
  `ActionStatus` varchar(10) NOT NULL,
  `ErrorCode` int(10) NOT NULL,
  `ErrorInfo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vs_001` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='私聊，被邀请的好友';

-- ----------------------------
-- Records of %DB_PREFIX%video_private_history
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_prop`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_prop`;
CREATE TABLE `%DB_PREFIX%video_prop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT '礼物id',
  `prop_name` varchar(255) NOT NULL COMMENT '道具名',
  `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
  `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
  `use_diamonds` int(10) NOT NULL DEFAULT '0' COMMENT '当红包时有效;记录已经被抢了多少',
  `total_ticket` int(11) NOT NULL DEFAULT '0' COMMENT '秀票(to_user_id增加的秀票）合计',
  `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '送',
  `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '收',
  `create_time` int(10) NOT NULL COMMENT '时间',
  `num` int(10) NOT NULL COMMENT '送的数量',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
  `group_id` varchar(20) NOT NULL COMMENT '群组ID',
  `is_red_envelope` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:红包',
  `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
  `ActionStatus` varchar(10) NOT NULL COMMENT '消息发送，请求处理的结果，OK表示处理成功，FAIL表示失败。',
  `ErrorInfo` varchar(255) DEFAULT NULL COMMENT '消息发送，错误信息',
  `ErrorCode` int(10) NOT NULL COMMENT '消息发送，错误码',
  PRIMARY KEY (`id`),
  KEY `idx_vp_001` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='送礼物表';

-- ----------------------------
-- Records of %DB_PREFIX%video_prop
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_share`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_share`;
CREATE TABLE `%DB_PREFIX%video_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` varchar(50) DEFAULT '' COMMENT '分享类型WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,EMAIL,SMS,SINA\r\n微信，微信朋友圈，qq，QQ空间，email，短信，新浪微博',
  `create_time` int(10) NOT NULL COMMENT '分享时间',
  PRIMARY KEY (`id`),
  KEY `idx_vs_001` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播会员分享记录';

-- ----------------------------
-- Records of %DB_PREFIX%video_share
-- ----------------------------

-- ----------------------------
-- Table structure for `%DB_PREFIX%video_share_history`
-- ----------------------------
DROP TABLE IF EXISTS `%DB_PREFIX%video_share_history`;
CREATE TABLE `%DB_PREFIX%video_share_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) NOT NULL COMMENT '直播ID 也是room_id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` varchar(50) DEFAULT '' COMMENT '分享类型WEIXIN,WEIXIN_CIRCLE,QQ,QZONE,EMAIL,SMS,SINA\r\n微信，微信朋友圈，qq，QQ空间，email，短信，新浪微博',
  `create_time` int(10) NOT NULL COMMENT '分享时间',
  PRIMARY KEY (`id`),
  KEY `idx_vs_001` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播会员分享记录';

-- ----------------------------
-- Records of %DB_PREFIX%video_share_history
-- ----------------------------

