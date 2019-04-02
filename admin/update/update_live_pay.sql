2.21;

INSERT INTO `fanwe_plugin` (`child_id`, `image`, `name`, `is_effect`, `class`, `type`) VALUES (0, './public/images/5851f15e9d962.png', '按时收费', 1, 'live_pay', 1);
INSERT INTO `fanwe_plugin` (`child_id`, `image`, `name`, `is_effect`, `class`, `type`) VALUES (0, './public/images/5851f15e9d962.png', '按场收费', 1, 'live_pay_scene', 1);

CREATE TABLE `%DB_PREFIX%live_pay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_time` int(11) NOT NULL COMMENT '观看时间（from_user_id累计观看时间）合计',
  `total_ticket` decimal(13,2) NOT NULL COMMENT '主播获得的秀票',
  `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
  `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '观众',
  `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '主播',
  `create_time` int(10) NOT NULL COMMENT '时间',
  `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
  `create_ym` varchar(12) NOT NULL COMMENT '年月 如:201610',
  `create_d` tinyint(2) NOT NULL COMMENT '日',
  `create_w` tinyint(2) NOT NULL COMMENT '周',
  `live_fee` int(10) NOT NULL COMMENT '收取费用（秀豆/分钟）',
  `live_pay_time` int(10) NOT NULL COMMENT '直播间开始收费时间',
  `live_pay_date` date NOT NULL COMMENT '直播间开始收费 日期字段',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
  `group_id` varchar(20) NOT NULL COMMENT '群组ID',
  `pay_time_end` int(11) NOT NULL COMMENT '最后一次扣款时间',
  `pay_time_next` int(11) NOT NULL COMMENT '下次扣款时间',
  `live_is_mention_time` int(11) NOT NULL COMMENT '提档后开始收费时间',
  `live_is_mention_pay` int(11) NOT NULL COMMENT '提档前扣费合计',
  `live_pay_type` tinyint(1) NOT NULL COMMENT '直播类型 0 按时收费 1按场收费',
  `new_room_id` int(11) NOT NULL COMMENT '新付费直播的ID , 用于异常终止直播间付费，主播新开的主播ID ',
  `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
  `uesddiamonds_to_score` float(11,2) NOT NULL COMMENT '观众（from_user_id）获得积分的转换比例',
  `ticket_to_rate` float(11,2) NOT NULL COMMENT '主播（to_user_id）获得的秀票转换比例',
  PRIMARY KEY (`id`),
  KEY `idx_vp_002` (`from_user_id`,`to_user_id`,`video_id`) USING BTREE,
  KEY `idx_vp_003` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='//付费直播记录';


CREATE TABLE `%DB_PREFIX%live_pay_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_time` int(11) NOT NULL COMMENT '观看时间（from_user_id累计观看时间）合计',
  `total_diamonds` int(11) NOT NULL COMMENT '秀豆（from_user_id减少的秀豆）合计',
  `from_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '观众',
  `to_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '主播',
  `create_time` int(10) NOT NULL COMMENT '时间',
  `create_date` date NOT NULL COMMENT '日期字段,按日期归档；要不然数据量太大了；不好维护',
  `create_ym` varchar(12) NOT NULL COMMENT '年月 如:201610',
  `create_d` tinyint(2) NOT NULL COMMENT '日',
  `create_w` tinyint(2) NOT NULL COMMENT '周',
  `live_fee` int(10) NOT NULL COMMENT '收取费用（秀豆/分钟）',
  `live_pay_time` int(10) NOT NULL COMMENT '直播间开始收费时间',
  `live_pay_date` date NOT NULL COMMENT '直播间开始收费 日期字段',
  `video_id` int(10) NOT NULL DEFAULT '0' COMMENT '直播ID',
  `group_id` varchar(20) NOT NULL COMMENT '群组ID',
  `pay_time_end` int(11) NOT NULL COMMENT '最后一次扣款时间',
  `pay_time_next` int(11) NOT NULL COMMENT '下次扣款时间',
  `live_is_mention_time` int(11) NOT NULL COMMENT '提档后开始收费时间',
  `live_is_mention_pay` int(11) NOT NULL COMMENT '提档前扣费合计',
  `live_pay_type` tinyint(1) NOT NULL COMMENT '直播类型 0 按时收费 1按场收费',
  `new_room_id` int(11) NOT NULL COMMENT '新付费直播的ID , 用于异常终止直播间付费，主播新开的主播ID ',
  `total_ticket` decimal(13,2) NOT NULL COMMENT '主播获得的秀票',
  `total_score` int(11) NOT NULL COMMENT '积分（from_user_id可获得的积分）合计',
  `uesddiamonds_to_score` float(11,2) NOT NULL COMMENT '观众（from_user_id）获得积分的转换比例',
  `ticket_to_rate` float(11,2) NOT NULL COMMENT '主播（to_user_id）获得的秀票转换比例',
  PRIMARY KEY (`id`),
  KEY `idx_vp_002` (`from_user_id`,`to_user_id`,`video_id`) USING BTREE,
  KEY `idx_vp_003` (`video_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='//付费直播历史记录';

INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_max', '按时付费收费最高', '付费直播配置', '100', 0, 97, NULL, NULL, '（秀豆）付费直播,主播填写最高的收费,0为不限制');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_min', '按时付费收费最低', '付费直播配置', '1', 0, 97, NULL, NULL, '（秀豆）付费直播,主播填写最低的收费 默认1');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_scene_max', '按场付费收费最高', '付费直播配置', '100', 0, 98, NULL, NULL, '（秀豆）付费直播,主播填写最高的收费,0为不限制');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_scene_min', '按场付费收费最低', '付费直播配置', '1', 0, 98, NULL, NULL, '（秀豆）付费直播,主播填写最低的收费 默认1');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('countdown', '预览倒计时', '付费直播配置', '10', 0, 0, NULL, NULL, '(秒) 付费直播间预览倒计时，默认为10，0为关闭倒计时预览');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('is_only_play_video', '是否预览画面', '付费直播配置', '1', 4, 0, '0,1', '否,是', '付费直播间是否预览画面 1是 0否');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_num', '付费开始最低人数', '付费直播配置', '0', 0, 88, NULL, NULL, '（人）允许切换付费模式的最低人数，含机器人');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_rule', '提档要求分钟', '付费直播配置', '1', 0, 90, NULL, NULL, '（分钟）几分钟后出现提档按钮,输入整数');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_pay_fee', '提档扣费设置', '付费直播配置', '2', 0, 90, NULL, NULL, '（秀豆）提档累加的费用,输入整数');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('live_count_down', '倒计时时间', '付费直播配置', '30', 0, 89, NULL, NULL, '（秒）主播切换收费模式，多少时间后开始计费');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('ticket_to_rate', '秀豆转秀票比例', '付费直播配置', '1', 0, 91, NULL, NULL, '（秀票）付费直播主播收到的秀豆转秀票比例，如1钻转多少秀票');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('uesddiamonds_to_score', '秀豆转积分比例', '付费直播配置', '2', 0, 91, NULL, NULL, '（积分）付费直播观众送出的秀豆转积分比例，如1秀豆转多少积分');

