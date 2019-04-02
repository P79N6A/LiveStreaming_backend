2.21;
CREATE TABLE `%DB_PREFIX%society` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) NOT NULL COMMENT '公会logo',
  `name` varchar(255) NOT NULL COMMENT '公会名称',
  `notice` text NOT NULL COMMENT '公告',
  `manifesto` varchar(255) NOT NULL COMMENT '宣言',
  `user_id` int(11) NOT NULL COMMENT '公会长ID',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `memo` text NOT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL COMMENT '状态，0未审核，1审核通过，2拒绝通过',
  `contribution` int(11) NOT NULL COMMENT '公会成员的贡献',
  `user_count` int(10) NOT NULL COMMENT '公会人数',
  `society_settlement_type` tinyint(1) DEFAULT '0' COMMENT '结算方式；0：对私结算、1：对公结算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='公会表';

ALTER TABLE `%DB_PREFIX%society` ADD COLUMN `refund_rate` decimal(2,2) NOT NULL COMMENT '公会提现比例';

CREATE TABLE `%DB_PREFIX%society_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `society_id` int(11) NOT NULL COMMENT '公会ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `create_time` int(11) NOT NULL COMMENT '申请时间',
  `apply_type` tinyint(1) NOT NULL COMMENT '0 加入申请 1 退出申请',
  `status` tinyint(1) NOT NULL COMMENT '	审核状态；0申请加入待审核、1加入申请通过、2 加入申请被拒绝，3申请退出公会待审核 4退出公会申请通过 5.退出公会申请被拒',
  `memo` text NOT NULL COMMENT '备注',
  `deal_time` int(11) NOT NULL COMMENT '处理申请的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='公会申请表';

CREATE TABLE `%DB_PREFIX%society_earning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `society_id` int(11) NOT NULL COMMENT '公会ID',
  `vote_number` int(11) NOT NULL COMMENT '该长直播收获的秀票数',
  `video_id` int(11) NOT NULL COMMENT '视频ID',
  `begin_time` int(11) NOT NULL COMMENT '创建时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `end_date` date NOT NULL COMMENT '日期字段,按日期归档',
  `end_Y` int(4) NOT NULL COMMENT '年',
  `end_m` tinyint(2) NOT NULL COMMENT '月',
  `end_d` tinyint(2) NOT NULL COMMENT '日',
  `end_w` tinyint(2) NOT NULL COMMENT '周',
  `timelen` int(11) NOT NULL COMMENT '播放时长',
  `society_settlement_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '结算方式；0：对私结算、1：对公结算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('society_private_rate', '对私结算比例', '提现设置', '0.4', 0, 99, NULL, NULL, '对私结算比例，如 0.3');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('society_public_rate', '对公结算比例', '提现设置', '0.5', 0, 99, NULL, NULL, '对公结算比例');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('society_user_private_rate', '主播对私结算比例', '提现设置', '0.5', 0, 99, NULL, NULL, '主播对私结算比例,如 0.3');
INSERT INTO `%DB_PREFIX%m_config` (`code`, `title`, `group_id`, `val`, `type`, `sort`, `value_scope`, `title_scope`, `desc`) VALUES ('society_user_public_rate', '主播对公结算比例', '提现设置', '0.5', 0, 99, NULL, NULL, '主播对公结算比例,如 0.3 ');

INSERT INTO `%DB_PREFIX%role_module` (`module`, `name`, `is_effect`, `is_delete`) VALUES ('Society', '公会列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('index', '列表', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='Society'));
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('edit', '公会详情', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='Society'));
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('view', '成员列表', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='Society'));
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('dissolve', '解散公会', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='Society'));

INSERT INTO `%DB_PREFIX%role_module` (`module`, `name`, `is_effect`, `is_delete`) VALUES ('SocietyIncome', '公会收入列表', '1', '0');
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('index', '列表', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='SocietyIncome'));
INSERT INTO `%DB_PREFIX%role_node` (`action`, `name`, `is_effect`, `is_delete`, `group_id`, `module_id`) VALUES ('submit_refund', '申请提现', '1', '0', '0', (select id from `%DB_PREFIX%role_module` where module='SocietyIncome'));

ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `bank_name`  varchar(255) NOT NULL  COMMENT '银行名称';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `province`  varchar(255) NOT NULL  COMMENT '开户省';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `city`  varchar(255) NOT NULL  COMMENT '开户市';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `area`  varchar(255) NOT NULL  COMMENT '开户区';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `branch_name`  varchar(255) NOT NULL  COMMENT '支行名称';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `open_account_num`  varchar(255) NOT NULL  COMMENT '开户账号';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `open_account_name`  varchar(255) NOT NULL  COMMENT '开户名称';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `contact`  varchar(255) NOT NULL  COMMENT '联系人';

ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `contact_number`  varchar(20) NOT NULL  COMMENT '联系电话';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `legal`  varchar(255) NOT NULL  COMMENT '法人';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `company_name`  varchar(255) NOT NULL COMMENT '公司全称';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `register_site`  varchar(255) NOT NULL  COMMENT '注册地址';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `contact_site`  varchar(255) NOT NULL  COMMENT '联系地址';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `receipt`  varchar(10) NOT NULL  COMMENT '发票税点';
ALTER TABLE `%DB_PREFIX%society`
ADD COLUMN `business_photo`  varchar(255) NOT NULL  COMMENT '营业执照';

ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `opus_site`  varchar(255) NOT NULL COMMENT '作品地址';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `opus_explain`  varchar(255) NOT NULL COMMENT '作品说明';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `remark`  varchar(255) NOT NULL COMMENT '备注说明';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `show_bill`  varchar(255) NOT NULL COMMENT '直播间海报';

