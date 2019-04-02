
ALTER TABLE fanwe_prop ADD COLUMN is_award tinyint(1) NOT NULL COMMENT '是否为可中奖礼物 1为 是、0为否';


CREATE TABLE `fanwe_award_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(255) DEFAULT NULL COMMENT '配置名称',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `group_id` varchar(50) DEFAULT NULL COMMENT '分组名称',
  `val` text COMMENT '配置值',
  `type` tinyint(1) NOT NULL COMMENT '类型',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `value_scope` varchar(50) DEFAULT NULL COMMENT '值的范围',
  `title_scope` varchar(255) DEFAULT NULL COMMENT '对应value_scope的中文解释',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='中奖礼物配置信息表';

INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('is_open_award', '是否开启礼物中奖', '礼物中奖', '0', 4, 1, '0,1', '否,是', '是否开启礼物中奖机制，0：否；1：是');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_pool_ratio', '投入资金池比例', '礼物中奖', '10', 0, 2, NULL, NULL, '（%）设置中奖礼物进入资金池比例');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_ratio', '中奖金额', '礼物中奖', '50', 0, 3, NULL, NULL, '（%）设置多少比例的资金作为中奖金额');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_condition', '中奖条件', '礼物中奖', '50', 0, 4, NULL, NULL, '（倍）设置多少的倍数为中奖条件,数值不能大于999');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('big_prize_limit', '大奖范围', '礼物中奖', '500', 0, 5, NULL, NULL, '（秀豆）大于多少倍为大奖');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_platform_fee', '手续费', '礼物中奖', '20', 0, 6, NULL, NULL, '（%）设置多少比例为平台手续费';
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_pool', '资金池总数', '礼物中奖', '830', 6, 7, NULL, NULL, '（秀豆）资金池总数');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('amount', '可用金额 (秀豆)', '礼物中奖', '415', 6, 8, NULL, NULL, '（秀豆）可用金额 ');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('used_amount', '已用金额', '礼物中奖', '0', 6, 9, NULL, NULL, '（秀豆）已用金额 ');
INSERT INTO fanwe_award_config (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('award_updatetime', '最后更新时间', '礼物中奖', '1502648483', 6, 10, NULL, NULL, '最后更新资金池等信息时间');

CREATE TABLE `fanwe_award_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '中奖用户ID',
  `prop_id` int(11) NOT NULL COMMENT '礼物ID',
  `video_id` int(11) NOT NULL COMMENT '直播间ID',
  `group_id` int(11) NOT NULL COMMENT '聊天组ID',
  `award_pool` int(11) NOT NULL COMMENT '当前资金池资金总数',
  `award_ratio` int(11) NOT NULL COMMENT '转换可用资金比例',
  `usable_amount` int(11) NOT NULL COMMENT '实际可用奖金 = 理论可用金额-已用金额',
  `commission_charge_ratio` tinyint(2) NOT NULL COMMENT '平台手续比例',
  `bonus` int(11) NOT NULL COMMENT '中奖金额 ',
  `commission_charge` int(5) NOT NULL COMMENT '平台手续 (中奖金额*平台手续费)',
  `receive_bonus` int(11) NOT NULL COMMENT '实际到账奖金 = 中奖金额-手续费 ',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `create_date` date NOT NULL COMMENT '日期字段',
  `create_ym` varchar(12) NOT NULL COMMENT '月',
  `create_d` tinyint(2) NOT NULL COMMENT '日',
  `create_w` tinyint(2) NOT NULL COMMENT '周',
  `from_ip` varchar(255) NOT NULL COMMENT '送礼物人IP',
  `ActionStatus` varchar(10) NOT NULL COMMENT '消息发送，请求处理的结果，OK表示处理成功，FAIL表示失败。',
  `ErrorInfo` varchar(250) NOT NULL COMMENT '消息发送，错误信息',
  `ErrorCode` int(10) NOT NULL COMMENT '错误码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='中奖记录表';

CREATE TABLE `fanwe_award_multiple` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT '名称',
  `multiple` int(200) NOT NULL COMMENT '中奖的倍数',
  `probability` tinyint(3) NOT NULL COMMENT '中奖概率 (所有概率总数不能大于100%)',
  `is_effect` tinyint(1) NOT NULL COMMENT '是否有效 1有效 0无效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='中奖的倍数设置表';

CREATE TABLE `fanwe_video_prop_all` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prop_id` int(10) NOT NULL DEFAULT '0' COMMENT '礼物id',
  `prop_name` varchar(255) NOT NULL COMMENT '道具名',
  `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
  `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
  `total_ticket` int(11) NOT NULL DEFAULT '0' COMMENT '秀票(to_user_id增加的秀票）合计;is_red_envelope=1时,为主播获得的：秀豆 数量',
  `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '送',
  `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '收',
  `create_time` int(10) NOT NULL COMMENT '时间',
  `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
  `create_d` tinyint(2) NOT NULL COMMENT '日',
  `create_w` tinyint(2) NOT NULL COMMENT '周',
  `num` int(10) NOT NULL COMMENT '送的数量',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
  `group_id` varchar(20) NOT NULL COMMENT '群组ID',
  `is_red_envelope` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:红包',
  `msg` varchar(255) NOT NULL COMMENT '弹幕内容',
  `ActionStatus` varchar(10) NOT NULL COMMENT '消息发送，请求处理的结果，OK表示处理成功，FAIL表示失败。',
  `ErrorInfo` varchar(255) NOT NULL COMMENT '消息发送，错误信息',
  `ErrorCode` int(10) NOT NULL COMMENT '错误码',
  `create_ym` varchar(12) NOT NULL COMMENT '年月 如:201610',
  `from_ip` varchar(255) NOT NULL COMMENT '送礼物人IP',
  `is_award` tinyint(1) NOT NULL COMMENT '是否为可中奖礼物 1为 是、0为否',
  `is_coin` varchar(255) NOT NULL COMMENT '双币礼物，0是秀豆，1是游戏币',
  `is_private` int(4) DEFAULT '0' COMMENT '判断是否为私信送礼 1表示私信 2表示不是私信',
  PRIMARY KEY (`id`),
  KEY `idx_ecs_video_prop_cc_1` (`create_ym`,`create_d`,`from_user_id`,`total_diamonds`) USING BTREE,
  KEY `from_user_id` (`from_user_id`,`total_diamonds`) USING BTREE,
  KEY `idx_ecs_video_prop_cc_2` (`create_ym`,`from_user_id`,`total_diamonds`) USING BTREE,
  KEY `to_user_id` (`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
  KEY `idx_ecs_video_prop_cc_3` (`create_ym`,`create_d`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE,
  KEY `idx_ecs_video_prop_cc_4` (`create_ym`,`is_red_envelope`,`to_user_id`,`total_ticket`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `fanwe_user_log`
MODIFY COLUMN `type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型 0表示充值 1表示提现 2赠送道具 3兑换秀票 4分享获得秀票 5登录赠送积分 6观看付费直播 7游戏收益 8公会收益 9分销收益 10公会长获取 11平台收益 12中奖纪录';

