2.21;
CREATE TABLE `%DB_PREFIX%vip_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号id',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `day_num` int(11) NOT NULL DEFAULT '0' COMMENT '天数',
  `money` decimal(20,2) NOT NULL COMMENT '价格',
  `iap_money` decimal(20,2) NOT NULL COMMENT '苹果支付价格',
  `product_id` varchar(50) NOT NULL COMMENT '苹果应用内支付项目ID',
  `is_effect` tinyint(1) DEFAULT '1' COMMENT '是否有效 1-有效 0-无效',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购买VIP会员规则表';

INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'open_vip', '是否开启VIP', '应用设置', '0', '4', '93', '0,1', '否,是', '模块开关 是否开启VIP功能 1开启，0关闭');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'open_room_hide', '是否开启房间隐藏', '应用设置', '0', '4', '93', '0,1', '否,是', '模块开关 是否开启房间隐藏 1开启，0关闭');
INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'open_vip', '是否开启VIP', '应用设置', '0', '4', '93', '0,1', '否,是', '模块开关 是否开启VIP功能 1开启，0关闭');

insert into `%DB_PREFIX%role_module` values('','VipRule','VIP购买规则',1,0);
insert into `%DB_PREFIX%role_node` values('','index','列表',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));
insert into `%DB_PREFIX%role_node` values('','add','新增',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));
insert into `%DB_PREFIX%role_node` values('','edit','编辑',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));
insert into `%DB_PREFIX%role_node` values('','foreverdelete','删除',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));
insert into `%DB_PREFIX%role_node` values('','set_effect','设置状态',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));
insert into `%DB_PREFIX%role_node` values('','set_sort','排序',1,0,0,(select id from `%DB_PREFIX%role_module` where module='VipRule'));