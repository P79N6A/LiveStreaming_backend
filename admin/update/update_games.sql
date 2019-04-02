2.21

CREATE TABLE `%DB_PREFIX%game_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `long_time` int(11) NOT NULL DEFAULT '60' COMMENT '游戏时间',
  `game_id` int(11) NOT NULL COMMENT '游戏id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `create_time_ymd` date NOT NULL COMMENT '发布时间（年月日）',
  `create_time_y` int(4) NOT NULL COMMENT '发布时间（年）',
  `create_time_m` int(2) NOT NULL COMMENT '发布时间（月）',
  `create_time_d` int(2) NOT NULL COMMENT '发布时间（日）',
  `bet` varchar(255) DEFAULT NULL COMMENT 'json格式下注情况,结算时候统计',
  `podcast_income` int(11) DEFAULT NULL COMMENT '主播收益',
  `suit_patterns` varchar(255) DEFAULT NULL COMMENT 'json格式，记录牌型',
  `result` int(11) DEFAULT '0' COMMENT '中奖结果：1/2/3，无结果，退款设置为-1',
  `game_name` varchar(255) DEFAULT NULL COMMENT '游戏名称（冗余字段）',
  `status` int(11) DEFAULT '1' COMMENT '1：进行，2：结束，',
  `income` int(11) NOT NULL DEFAULT '0' COMMENT '平台收入',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10628 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%game_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `long_time` int(11) NOT NULL DEFAULT '60' COMMENT '游戏时间',
  `game_id` int(11) NOT NULL COMMENT '游戏id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `create_time_ymd` date NOT NULL COMMENT '发布时间（年月日）',
  `create_time_y` int(4) NOT NULL COMMENT '发布时间（年）',
  `create_time_m` int(2) NOT NULL COMMENT '发布时间（月）',
  `create_time_d` int(2) NOT NULL COMMENT '发布时间（日）',
  `bet` varchar(255) DEFAULT NULL COMMENT 'json格式下注情况,结算时候统计',
  `podcast_income` int(11) DEFAULT NULL COMMENT '主播收益',
  `suit_patterns` varchar(255) DEFAULT NULL COMMENT 'json格式，记录牌型',
  `result` int(11) DEFAULT '0' COMMENT '中奖结果：1/2/3，无结果，退款设置为-1',
  `game_name` varchar(255) DEFAULT NULL COMMENT '游戏名称（冗余字段）',
  `status` int(11) DEFAULT '1' COMMENT '1：进行，2：结束，',
  `income` int(11) NOT NULL DEFAULT '0' COMMENT '平台收入',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10607 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%user_game_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `game_log_id` int(11) DEFAULT NULL COMMENT '游戏轮数id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `money` int(11) DEFAULT NULL COMMENT '下注金额',
  `bet` int(11) DEFAULT NULL COMMENT '下注选项：1/2/3',
  `podcast_id` int(11) DEFAULT NULL COMMENT '主播id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1、下注，2、收益',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58989 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%user_game_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `game_log_id` int(11) DEFAULT NULL COMMENT '游戏轮数id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `money` int(11) DEFAULT NULL COMMENT '下注金额',
  `bet` int(11) DEFAULT NULL COMMENT '下注选项：1/2/3',
  `podcast_id` int(11) DEFAULT NULL COMMENT '主播id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1、下注，2、收益',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51647 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%games` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `image` text NOT NULL COMMENT '图片',
  `name` varchar(255) NOT NULL COMMENT '标题',
  `principal` int(11) NOT NULL COMMENT '主播开启游戏抵金',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `commission_rate` int(11) NOT NULL DEFAULT '50' COMMENT '主播佣金比例：50表示收益的50%最为佣金；收益金额=总金额-中奖返还；',
  `long_time` int(11) NOT NULL COMMENT '当轮时长，单位s',
  `rate` int(11) NOT NULL DEFAULT '0' COMMENT '干预系数，0-100,0表示无干预，全部随机结果、100表示完全干预，收益最大',
  `option` varchar(255) NOT NULL DEFAULT '{"option1":1,"option2":2,"option3":3}' COMMENT '投注选项',
  `bet_option` varchar(255) NOT NULL DEFAULT '[10,100,1000,10000]' COMMENT '投注金额',
  `description` text NOT NULL COMMENT '游戏描述',
  `class` varchar(255) NOT NULL COMMENT '游戏操作类',
  `player_num` int(4) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `%DB_PREFIX%games` VALUES ('1', './public/images/5859e6513a3c7.png', '炸金花', '5000', '1', '5', '30', '100', '{\"1\":3,\"2\":3,\"3\":3}', '[10,100,1000,10000]', '炸金花的特长描述', 'Poker', '3');
INSERT INTO `%DB_PREFIX%games` VALUES ('2', './public/images/5859e636cc9f4.png', '斗牛', '5000', '1', '1', '20', '100', '{\"1\":3,\"2\":3,\"3\":3}', '[10,100,1000,10000]', '斗牛描述', 'NiuNiu', '3');

ALTER TABLE `%DB_PREFIX%user` ADD COLUMN `coin` INT (11) NOT NULL DEFAULT 0 COMMENT '游戏币';

CREATE TABLE `%DB_PREFIX%plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `child_id` int(11) NOT NULL COMMENT '插件子编号（对应类别内的自增id）',
  `image` text NOT NULL COMMENT '图片',
  `name` varchar(255) NOT NULL COMMENT '标题',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `class` varchar(255) NOT NULL COMMENT '游戏操作类',
  `type` int(11) NOT NULL COMMENT '类别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `%DB_PREFIX%plugin` VALUES ('1', '1', './public/images/5859e6513a3c7.png', '炸金花', '1', 'game', '2');
INSERT INTO `%DB_PREFIX%plugin` VALUES ('2', '2', './public/images/5859e636cc9f4.png', '斗牛', '1', 'game', '2');

CREATE TABLE `%DB_PREFIX%coin_log` (
    `id` INT (11) NOT NULL AUTO_INCREMENT COMMENT '游戏币id',
    `user_id` INT (11) NOT NULL COMMENT '用户id',
    `game_log_id` INT (11) NOT NULL COMMENT '游戏记录id',
    `create_time` INT (11) NOT NULL COMMENT '创建日期',
    `diamonds` INT (11) NOT NULL COMMENT '记录金额',
    `account_diamonds` INT (11) NOT NULL COMMENT '用户余额',
    `memo` VARCHAR (255) DEFAULT NULL COMMENT '备注',
    PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '游戏币记录';

ALTER TABLE `%DB_PREFIX%recharge_rule`
ADD COLUMN `gift_coins`  int(11) NOT NULL DEFAULT 0 COMMENT '游戏币赠送数量';

INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'coin_exchange_rate', '游戏币兑换比例', '基础配置', '1', '0', '0', null, null, '秀豆与游戏币的兑换比例 如：100秀豆，可以获得50个游戏币，则填写0.5');

insert into `%DB_PREFIX%role_module` values('','Games','游戏配置',1,0);
insert into `%DB_PREFIX%role_node` values('','index','列表',1,0,0,(select id from `%DB_PREFIX%role_module` where module='Games'));
insert into `%DB_PREFIX%role_node` values('','edit','编辑',1,0,0,(select id from `%DB_PREFIX%role_module` where module='Games'));
insert into `%DB_PREFIX%role_node` values('','addCoin','游戏币管理',1,0,0,(select id from `%DB_PREFIX%role_module` where module='Games'));

insert into `%DB_PREFIX%role_module` values('','GameLog','游戏记录',1,0);
insert into `%DB_PREFIX%role_node` values('','index','列表',1,0,0,(select id from `%DB_PREFIX%role_module` where module='GameLog'));
insert into `%DB_PREFIX%role_node` values('','edit','编辑',1,0,0,(select id from `%DB_PREFIX%role_module` where module='GameLog'));

INSERT INTO `%DB_PREFIX%role_module` (`module`, `name`, `is_effect`, `is_delete`) VALUES ('GameLogHistory', '游戏历史记录', '1', '0');
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('index', '列表', '1', '0', '0', (select id from `fanwe_role_module` where module='GameLogHistory'));
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('edit', '编辑', '1', '0', '0', (select id from `fanwe_role_module` where module='GameLogHistory'));

INSERT INTO `%DB_PREFIX%plugin` VALUES ('1', '1', './public/images/5859e6513a3c7.png', '炸金花', '1', 'game', '2');
INSERT INTO `%DB_PREFIX%plugin` VALUES ('2', '2', './public/images/5859e636cc9f4.png', '斗牛', '1', 'game', '2');

CREATE TABLE `%DB_PREFIX%game_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `long_time` int(11) NOT NULL DEFAULT '60' COMMENT '游戏时间',
  `game_id` int(11) NOT NULL COMMENT '游戏id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `create_time_ymd` date NOT NULL COMMENT '发布时间（年月日）',
  `create_time_y` int(4) NOT NULL COMMENT '发布时间（年）',
  `create_time_m` int(2) NOT NULL COMMENT '发布时间（月）',
  `create_time_d` int(2) NOT NULL COMMENT '发布时间（日）',
  `bet` varchar(255) DEFAULT NULL COMMENT 'json格式下注情况,结算时候统计',
  `podcast_income` int(11) DEFAULT NULL COMMENT '主播收益',
  `suit_patterns` varchar(255) DEFAULT NULL COMMENT 'json格式，记录牌型',
  `result` int(11) DEFAULT '0' COMMENT '中奖结果：1/2/3，无结果，退款设置为-1',
  `game_name` varchar(255) DEFAULT NULL COMMENT '游戏名称（冗余字段）',
  `status` int(11) DEFAULT '1' COMMENT '1：进行，2：结束，',
  `income` int(11) NOT NULL DEFAULT '0' COMMENT '平台收入',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10628 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%game_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `long_time` int(11) NOT NULL DEFAULT '60' COMMENT '游戏时间',
  `game_id` int(11) NOT NULL COMMENT '游戏id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `create_time_ymd` date NOT NULL COMMENT '发布时间（年月日）',
  `create_time_y` int(4) NOT NULL COMMENT '发布时间（年）',
  `create_time_m` int(2) NOT NULL COMMENT '发布时间（月）',
  `create_time_d` int(2) NOT NULL COMMENT '发布时间（日）',
  `bet` varchar(255) DEFAULT NULL COMMENT 'json格式下注情况,结算时候统计',
  `podcast_income` int(11) DEFAULT NULL COMMENT '主播收益',
  `suit_patterns` varchar(255) DEFAULT NULL COMMENT 'json格式，记录牌型',
  `result` int(11) DEFAULT '0' COMMENT '中奖结果：1/2/3，无结果，退款设置为-1',
  `game_name` varchar(255) DEFAULT NULL COMMENT '游戏名称（冗余字段）',
  `status` int(11) DEFAULT '1' COMMENT '1：进行，2：结束，',
  `income` int(11) NOT NULL DEFAULT '0' COMMENT '平台收入',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10607 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%user_game_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `game_log_id` int(11) DEFAULT NULL COMMENT '游戏轮数id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `money` int(11) DEFAULT NULL COMMENT '下注金额',
  `bet` int(11) DEFAULT NULL COMMENT '下注选项：1/2/3',
  `podcast_id` int(11) DEFAULT NULL COMMENT '主播id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1、下注，2、收益',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58989 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%user_game_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `game_log_id` int(11) DEFAULT NULL COMMENT '游戏轮数id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `money` int(11) DEFAULT NULL COMMENT '下注金额',
  `bet` int(11) DEFAULT NULL COMMENT '下注选项：1/2/3',
  `podcast_id` int(11) DEFAULT NULL COMMENT '主播id',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1、下注，2、收益',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51647 DEFAULT CHARSET=utf8;

CREATE TABLE `%DB_PREFIX%games` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `image` text NOT NULL COMMENT '图片',
  `name` varchar(255) NOT NULL COMMENT '标题',
  `principal` int(11) NOT NULL COMMENT '主播开启游戏抵金',
  `is_effect` tinyint(1) NOT NULL COMMENT '有效性标识',
  `commission_rate` int(11) NOT NULL DEFAULT '50' COMMENT '主播佣金比例：50表示收益的50%最为佣金；收益金额=总金额-中奖返还；',
  `long_time` int(11) NOT NULL COMMENT '当轮时长，单位s',
  `rate` int(11) NOT NULL DEFAULT '0' COMMENT '干预系数，0-100,0表示无干预，全部随机结果、100表示完全干预，收益最大',
  `option` varchar(255) NOT NULL DEFAULT '{"option1":1,"option2":2,"option3":3}' COMMENT '投注选项',
  `bet_option` varchar(255) NOT NULL DEFAULT '[10,100,1000,10000]' COMMENT '投注金额',
  `description` text NOT NULL COMMENT '游戏描述',
  `class` varchar(255) NOT NULL COMMENT '游戏操作类',
  `player_num` int(4) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `%DB_PREFIX%games` VALUES ('1', './public/images/5859e6513a3c7.png', '炸金花', '5000', '1', '5', '30', '100', '{\"1\":3,\"2\":3,\"3\":3}', '[10,100,1000,10000]', '炸金花的特长描述', 'Poker', '3');
INSERT INTO `%DB_PREFIX%games` VALUES ('2', './public/images/5859e636cc9f4.png', '斗牛', '5000', '1', '1', '20', '100', '{\"1\":3,\"2\":3,\"3\":3}', '[10,100,1000,10000]', '斗牛描述', 'NiuNiu', '3');


CREATE TABLE `%DB_PREFIX%coin_log` (
    `id` INT (11) NOT NULL AUTO_INCREMENT COMMENT '游戏币id',
    `user_id` INT (11) NOT NULL COMMENT '用户id',
    `game_log_id` INT (11) NOT NULL COMMENT '游戏记录id',
    `create_time` INT (11) NOT NULL COMMENT '创建日期',
    `diamonds` INT (11) NOT NULL COMMENT '记录金额',
    `account_diamonds` INT (11) NOT NULL COMMENT '用户余额',
    `memo` VARCHAR (255) DEFAULT NULL COMMENT '备注',
    PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COMMENT = '游戏币记录';


INSERT INTO `%DB_PREFIX%api_list` (`name`, `ctl_act`, `has_cookie`, `slb_group_id`) VALUES ('图片上传', 'avatar_uploadImage', 1, 2);


CREATE INDEX idx_ecs_user_cc_7 ON %DB_PREFIX%user (id ,is_robot);

CREATE INDEX idx_ecs_user_cc_8 ON %DB_PREFIX%user (luck_num  ,is_robot);

INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'coin_exchange_rate', '游戏币兑换比例', '基础配置', '1', '0', '0', null, null, '秀豆与游戏币的兑换比例 如：100秀豆，可以获得50个游戏币，则填写0.5');


INSERT INTO `%DB_PREFIX%m_config` VALUES (null, 'pc_logo', 'PC端Logo', 'PC端设置', '', '2', '12', null, null, '用于PClogo:[大小：162px*28px]');



INSERT INTO `%DB_PREFIX%m_config` VALUES (null, 'pc_download_slogan', '下载页标语', 'PC端设置', 'null', '0', '12', null, null, 'PC端下载页标语');




INSERT INTO `%DB_PREFIX%m_config` VALUES (null, 'pc_default_headimg', '默认注册头像', 'PC端设置', 'null', '2', '12', null, null, 'PC端默认注册头像');

ALTER TABLE `%DB_PREFIX%game_log`
ADD COLUMN `banker_id`  int NOT NULL DEFAULT 0 COMMENT '上庄玩家ID';
ALTER TABLE `%DB_PREFIX%game_log_history`
ADD COLUMN `banker_id`  int NOT NULL DEFAULT 0 COMMENT '上庄玩家ID';

INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'register_gift','注册赠送开关','基础配置', '0', '4', '0', '0,1', '否,是',  '开启后注册用户将增加秀豆、游戏币');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'register_gift_diamonds','注册赠送秀豆','基础配置', '0', '0', '0', '', '否,',  '开启后注册用户将增加秀豆数');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'register_gift_coins','注册赠送游戏币','基础配置', '0', '0', '0', '', '否,',  '开启后注册用户将增加游戏币数');
CREATE TABLE `%DB_PREFIX%banker_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '上庄ID',
  `video_id` int(11) NOT NULL COMMENT '直播间ID',
  `user_id` int(11) NOT NULL COMMENT '上庄用户ID',
  `coin` int(11) NOT NULL COMMENT '上庄金额',
  `status` int(11) NOT NULL COMMENT '上庄状态，1：申请上庄，2：取消上庄，3：正在上庄，4：下庄',
  `create_time` int(11) NOT NULL COMMENT '记录时间戳',
  PRIMARY KEY (`id`),
  KEY `video_id_status` (`video_id`,`status`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `%DB_PREFIX%banker_log_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '上庄ID',
  `video_id` int(11) NOT NULL COMMENT '直播间ID',
  `user_id` int(11) NOT NULL COMMENT '上庄用户ID',
  `coin` int(11) NOT NULL COMMENT '上庄金额',
  `status` int(11) NOT NULL COMMENT '上庄状态，1：申请上庄，2：取消上庄，3：正在上庄，4：下庄',
  `create_time` int(11) NOT NULL COMMENT '记录时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `%DB_PREFIX%m_config` VALUES (null, 'seo_title', '网站标题', 'PC端设置', NULL, '0', '12', NULL, NULL, 'SEO优化网页标题');

ALTER TABLE `%DB_PREFIX%banker_log`
ADD COLUMN `apply_coin`  int(11) NOT NULL;
ALTER TABLE `%DB_PREFIX%banker_log_history`
ADD COLUMN `apply_coin`  int(11) NOT NULL;
ALTER TABLE `%DB_PREFIX%game_log`
ADD COLUMN `banker_log_id`  int(11) NOT NULL;
ALTER TABLE `%DB_PREFIX%game_log_history`
ADD COLUMN `banker_log_id`  int(11) NOT NULL;

INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `desc`) VALUES ('game_gain_for_alert', '弹幕提示赢取游戏币', '基础配置', '0', '设置提示玩家赢取游戏币的赢取值，如1000表示玩家赢得1000或以上游戏币时直播间弹幕提示，0则表示不发送弹幕');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `desc`) VALUES ('game_commission', '平台游戏抽成', '基础配置', '0', '设置玩家或主播游戏抽成百分比，如10表示玩家赢得游戏币10%将被平台抽取');
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `rate`  int(11) NOT NULL DEFAULT 0 COMMENT '干预系数，0-100,0表示无干预，全部随机结果、100表示完全干预，收益最大';

INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `desc`) VALUES ('game_distribution1', '游戏一级分销抽成比例', '基础配置', '0', '设置游戏分销抽成百分比，如10表示主播赢得游戏币10%将被上级抽取');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `desc`) VALUES ('game_distribution2', '游戏二级分销抽成比例', '基础配置', '0', '设置游戏分销抽成百分比，如10表示主播赢得游戏币10%将被上级抽取');
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `game_distribution_id`  int(11) NOT NULL DEFAULT 0 COMMENT '上级分销者ID';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `game_distribution1`  int(11) NOT NULL DEFAULT 0 COMMENT '游戏一级分销抽成比例';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `game_distribution2`  int(11) NOT NULL DEFAULT 0 COMMENT '游戏二级分销抽成比例';
CREATE TABLE `%DB_PREFIX%game_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分销记录id',
  `room_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `game_log_id` int(11) NOT NULL DEFAULT '0' COMMENT '游戏id',
  `money` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '总金额',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '分销人',
  `distreibution_money` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '分销金额',
  `first_distreibution_id` int(11) DEFAULT '0' COMMENT '一级分销人',
  `first_distreibution_money` int(11) DEFAULT '0' COMMENT '一级分销金额',
  `second_distreibution_id` int(11) DEFAULT '0' COMMENT '二级分销人',
  `second_distreibution_money` int(11) DEFAULT '0' COMMENT '二级分销金额',
  `dec` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `%DB_PREFIX%user_log`
MODIFY COLUMN `type`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '//类型 0表示充值 1表示提现 2赠送道具 3 兑换秀票  4 分享获得秀票 5 登录赠送积分 6 观看付费直播 7 游戏';

ALTER TABLE `%DB_PREFIX%games`
ADD COLUMN `ticket_rate`  int(11) NOT NULL DEFAULT 100 COMMENT '主播收益秀票转化率';
