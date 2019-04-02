2.21;

CREATE TABLE `%DB_PREFIX%pai_goods`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `podcast_name` varchar(50) NOT NULL COMMENT '主播名称',
  `imgs` longtext NOT NULL COMMENT '图片（JSON数据）',
  `tags` varchar(255) NOT NULL COMMENT '标签 (约会、逛街，以 、 隔开保存)  ',
  `name` varchar(255) NOT NULL COMMENT '拍品名称',
  `description` text NOT NULL COMMENT '竞拍描述[虚拟]',
  `date_time` datetime NOT NULL COMMENT '约会时间[虚拟]',
  `place` varchar(100) NOT NULL COMMENT '约会地点[虚拟]',
  `district` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL COMMENT '联系人[虚拟]',
  `mobile` varchar(50) NOT NULL COMMENT '联系电话[虚拟]',
  `is_true` tinyint(4) NOT NULL COMMENT '0虚拟 1实物  ',
  `goods_id` int(11) NOT NULL COMMENT '商品ID (实物 此项不为空)',
  `bz_diamonds` int(11) NOT NULL COMMENT '竞拍保证金',
  `qp_diamonds` int(11) NOT NULL COMMENT '起拍价',
  `jj_diamonds` int(11) NOT NULL COMMENT '每次加价',
  `pai_time` decimal(4,2) NOT NULL COMMENT '竞拍时长 （单位小时）',
  `pai_yanshi` int(2) NOT NULL COMMENT '每次竞拍延时（单位分）',
  `max_yanshi` int(2) NOT NULL COMMENT '最大延时(次)',
  `pai_less_time` int(2) NOT NULL COMMENT '少于几秒才会触发 更新now_yanshi事件',
  `now_yanshi` int(2) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `create_date` datetime NOT NULL COMMENT '发布时间（格式化）',
  `create_time_ymd` date NOT NULL COMMENT '发布时间（年月日）',
  `create_time_y` int(4) NOT NULL COMMENT '发布时间（年）',
  `create_time_m` int(2) NOT NULL COMMENT '发布时间（月）',
  `create_time_d` int(2) NOT NULL COMMENT '发布时间（日）',
  `pai_nums` int(11) NOT NULL COMMENT '参与竞拍人次',
  `user_id` int(11) NOT NULL COMMENT '拍到的人的ID',
  `user_name` varchar(50) NOT NULL COMMENT '拍到的人',
  `status` tinyint(1) NOT NULL COMMENT '0竞拍中 1竞拍成功（结算中） 2流拍 3失败 4竞拍成功（完成）',
  `order_id` varchar(20) NOT NULL COMMENT '订单ID',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态',
  `order_time` datetime NOT NULL,
  `pay_time` datetime NOT NULL COMMENT '支付时间（格式化）',
  `refund_over_time` datetime NOT NULL COMMENT '退款完成时间（格式化）',
  `last_user_id` int(11) NOT NULL COMMENT '最后竞拍用户id',
  `last_user_name` varchar(50) NOT NULL COMMENT '最后出价竞拍用户名称',
  `last_pai_diamonds` int(11) NOT NULL COMMENT '最后出价金额',
  `is_delete` tinyint(1) NOT NULL COMMENT '删除标识',
  `end_time` int(11) NOT NULL COMMENT '竞拍结束时间（module=1使用）',
  `shop_id` int(11) DEFAULT NULL COMMENT '店铺id[如果为第三方竞拍，此项必选]',
  `shop_name` varchar(50) DEFAULT NULL COMMENT '店铺名称[如果为第三方竞拍，此项必选]',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='竞拍商品表';


CREATE TABLE `%DB_PREFIX%pai_join`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `pai_id` int(11) NOT NULL COMMENT '竞拍ID',
  `user_id` int(11) NOT NULL COMMENT '参与会员',
  `bz_diamonds` int(11) NOT NULL COMMENT '参与保证金(冗余字段)',
  `status` tinyint(1) NOT NULL COMMENT '0未处理 1已返还 2已扣除',
  `create_time` int(11) NOT NULL COMMENT '参与时间',
  `create_date` datetime NOT NULL COMMENT '参与时间',
  `create_time_ymd` date NOT NULL COMMENT '参与时间(年月日)',
  `create_time_y` int(4) NOT NULL COMMENT '参与时间(年)',
  `create_time_m` int(2) NOT NULL COMMENT '参与时间(月)',
  `create_time_d` int(2) NOT NULL COMMENT '参与时间(日)',
  `consignee` varchar(50) NOT NULL COMMENT '收货人姓名',
  `consignee_mobile` varchar(20) NOT NULL COMMENT '收货人手机号',
  `consignee_district` varchar(255) NOT NULL COMMENT '收货人所在地行政地区信息,json格式，主要用于可能的运费计算',
  `consignee_address` varchar(150) NOT NULL COMMENT '收货人详细地址',
  `pai_diamonds` int(11) NOT NULL COMMENT '最终出价',
  `pai_number` int(11) NOT NULL COMMENT '出价几次',
  `pai_status` tinyint(1) NOT NULL COMMENT '0 出局 1待付款 2排队中 3超时出局 4 付款完成',
  `pai_left_start_time` datetime NOT NULL COMMENT '前3名排队付款开始时间,用于倒计时',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态',
  `order_time` int(11) NOT NULL COMMENT '下单时间',
  `pay_time` datetime NOT NULL COMMENT '支付时间（格式化）',
  `refund_over_time` datetime NOT NULL COMMENT '退款完成时间（格式化）',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='参与竞拍表';


CREATE TABLE `%DB_PREFIX%goods_order`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '购物订单ID',
  `order_source` enum('remote','local') NOT NULL DEFAULT 'local' COMMENT '订单来源（local:本地 remote:远程）',
  `order_type` enum('shop','pai_goods','pai') DEFAULT 'shop' COMMENT '订单类型（shop:购物单 pai:竞拍单 pai_goods:实物竞拍）',
  `order_sn` varchar(30) NOT NULL COMMENT '订单编号(本地的系统生成，远程的第三方同步)',
  `order_status` tinyint(2) NOT NULL COMMENT '当前订单状态 1:待付款 2:待发货 3:待收货(主播确认约会)  4:已收货(观众确认约会)5:
退款成功 6未付款 7结单',
  `no_refund` tinyint(1) NOT NULL COMMENT '是否允许退款 0是 1否是否允许退款 0是 1否，实物竞拍（remote+pai）不允许退款退货',
  `refund_buyer_status` tinyint(1) NOT NULL COMMENT '退款买方状态 0：无 1:退款中 2:退货中 3:退款成功 4:主动撤销退款 5:被动关
闭',
  `refund_buyer_delivery` tinyint(1) NOT NULL COMMENT '退款买方配货状态 0：无 1:未发货 2:已发货， 虚拟商品不涉及该项',
  `refund_seller_status` tinyint(1) NOT NULL COMMENT '退款卖方状态 0：无 1:退款成功',
  `refund_platform` tinyint(1) NOT NULL COMMENT '退款平台申诉 0:无 1:申诉中(卖方) 2:申诉完成（结果同步到相关refund状态）',
  `number` int(11) NOT NULL COMMENT '该订单的商品总数量',
  `total_diamonds` decimal(10,2) NOT NULL COMMENT '订单应付总金额(含运费，竞拍单为竞拍价)',
  `remote_total_diamonds` decimal(10,2) NOT NULL COMMENT '第三方购物平台冗余的应付总额，本地订单该项为0',
  `remote_cost_diamonds` decimal(10,2) NOT NULL COMMENT '购物平台的订单成本',
  `goods_diamonds` decimal(10,2) NOT NULL COMMENT '订单中商品的金额（竞拍单直接为竞拍价）',
  `pay_diamonds` decimal(10,2) NOT NULL COMMENT '已付金额',
  `podcast_ticket` decimal(10,2) NOT NULL COMMENT '主播佣金主播佣金，pay_diamonds-cost_diamonds(成本，即结算给购物平台的钱)，
虚拟竞拍无成本',
  `refund_diamonds` decimal(10,2) NOT NULL COMMENT '退款金额',
  `freight_diamonds` decimal(10,2) NOT NULL COMMENT '运费',
  `memo` text NOT NULL COMMENT '订单备注',
  `consignee` varchar(20) NOT NULL COMMENT '收货人',
  `consignee_mobile` varchar(20) NOT NULL COMMENT '收货人手机号',
  `consignee_district` text NOT NULL COMMENT '收货人所在地行政地区信息,json格式\r\n{\r\n    "province":"福建省",\r\n
"city":"福州市",\r\n    "area":"鼓楼区", //行政区\r\n    "zip":"350001", //邮编\r\n    "lng":"xxxxxxxxxxx" //经度\r\n
"lat":"xxxxxxxxxxx" //纬度\r\n}',
  `consignee_address` text NOT NULL COMMENT '详细地址',
  `create_time` int(11) NOT NULL COMMENT 'GMT+0 时间戳',
  `create_date` datetime NOT NULL COMMENT 'GMT8时间',
  `create_time_ymd` date NOT NULL COMMENT 'GMT8日期',
  `create_time_y` int(4) NOT NULL,
  `create_time_m` int(2) NOT NULL,
  `create_time_d` int(2) NOT NULL,
  `podcast_id` int(11) NOT NULL COMMENT '主播人id',
  `viewer_id` int(11) NOT NULL COMMENT '购买人id',
  `pai_id` int(11) NOT NULL COMMENT '竞拍ID',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `pay_time` datetime NOT NULL COMMENT '付款时间 GMT+8时间',
  `refund_over_time` datetime NOT NULL COMMENT '退款完成时间（格式化）',
  `order_status_time` int(11) NOT NULL COMMENT '状态更新时间戳',
  `delivery_time` datetime DEFAULT NULL COMMENT '发货时间',
  `refund_reason` varchar(255) NOT NULL COMMENT '退款原因',
  `courier_number` varchar(255) DEFAULT NULL COMMENT '物流单号',
  `courier_offic` varchar(255) DEFAULT NULL COMMENT '物流公司',
  `buy_type` int(1) DEFAULT NULL COMMENT '0购买给自己 1购买给主播',
  `is_p` int(1) NOT NULL DEFAULT '0' COMMENT '是否是父订单  1 是  0否',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父订单id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn_unk` (`order_sn`) USING BTREE,
  KEY `order_type` (`order_source`) USING BTREE,
  KEY `order_sn` (`order_sn`) USING BTREE
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单表';


CREATE TABLE `%DB_PREFIX%user_address`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_id` int(11) NOT NULL COMMENT '会员id',
  `consignee` varchar(50) NOT NULL DEFAULT '收货人姓名',
  `consignee_mobile` varchar(20) NOT NULL COMMENT '收货人手机号',
  `consignee_district` varchar(255) NOT NULL COMMENT '收货人所在地行政地区信息，json格式，主要用于可能的运费计算',
  `consignee_address` varchar(150) NOT NULL COMMENT '收货人详细地址',
  `is_default` tinyint(1) NOT NULL COMMENT '是否默认',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='收货地址表';


CREATE TABLE `%DB_PREFIX%user_notice`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_id` int(11) NOT NULL COMMENT '发生人， 0 为官方发送',
  `send_user_name` varchar(30) DEFAULT NULL COMMENT '发送人昵称',
  `user_id` int(11) NOT NULL COMMENT '接收会员id',
  `content` text NOT NULL COMMENT '内容',
  `type` varchar(40) NOT NULL COMMENT '类型',
  `create_time` int(11) NOT NULL COMMENT '发生时间',
  `create_date` datetime NOT NULL COMMENT '发生时间',
  `create_time_ymd` date DEFAULT NULL COMMENT '发生年月日',
  `create_time_y` int(4) NOT NULL COMMENT '发生年 yyyy',
  `create_time_m` int(2) NOT NULL COMMENT '发生月 mm',
  `create_time_d` int(2) NOT NULL COMMENT '发生天 dd',
  `is_read` int(11) NOT NULL COMMENT '0未读， 1已读',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户消息列表';


CREATE TABLE `%DB_PREFIX%pai_tags`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(100) NOT NULL COMMENT '标签名称',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='标签列表';


CREATE TABLE `%DB_PREFIX%user_diamonds_log`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `pai_id` int(11) NOT NULL DEFAULT '0' COMMENT '竞拍id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `diamonds` int(11) NOT NULL COMMENT '变更数额',
  `account_diamonds` int(11) NOT NULL COMMENT '账户余额',
  `memo` text NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '发生时间 GMT+0',
  `create_date` datetime NOT NULL COMMENT '发生时间 年月日 GMT+8',
  `create_time_ymd` varchar(50) NOT NULL COMMENT '创建年月日',
  `create_time_y` int(4) NOT NULL COMMENT '发生时间 年 GMT+8',
  `create_time_m` int(2) NOT NULL COMMENT '发生时间 月 GMT+8',
  `create_time_d` int(2) NOT NULL COMMENT '发生时间 日 GMT+8',
  `type` varchar(50) NOT NULL COMMENT '变更类型',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户秀豆日志表';


CREATE TABLE `%DB_PREFIX%pai_log`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `podcast_id` int(11) NOT NULL COMMENT '主播ID',
  `user_id` int(11) NOT NULL COMMENT '竞拍人id',
  `user_name` varchar(50) NOT NULL COMMENT '竞拍人',
  `pai_id` int(11) NOT NULL COMMENT '竞拍商品ID',
  `bz_diamonds` int(11) NOT NULL COMMENT '竞拍保证金(冗余字段)',
  `qp_diamonds` int(11) NOT NULL COMMENT '起拍价(冗余字段)',
  `jj_diamonds` int(11) NOT NULL COMMENT '每次加价(冗余字段)',
  `pai_diamonds` int(11) NOT NULL COMMENT '当前出价',
  `pai_sort` int(1) NOT NULL COMMENT '当前价的第n次出价',
  `pai_time_ms` decimal(14,0) NOT NULL COMMENT '竞拍时间（毫秒）',
  `pai_time` int(11) NOT NULL COMMENT '竞拍时间',
  `pai_date` datetime NOT NULL COMMENT '竞拍时间（格式化）',
  `pai_time_ymd` date NOT NULL COMMENT '竞拍时间（年月日）',
  `pai_time_y` int(4) NOT NULL COMMENT '竞拍时间（年）',
  `pai_time_m` int(2) NOT NULL COMMENT '竞拍时间（月）',
  `pai_time_d` int(2) NOT NULL COMMENT '竞拍时间（日）',
  `status` tinyint(1) NOT NULL COMMENT '0待支付  1支付成功 2已流拍',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk` (`pai_id`,`pai_diamonds`,`pai_sort`) USING BTREE,
  KEY `pai_date` (`pai_date`),
  KEY `pai_time_ymd` (`pai_time_ymd`),
  KEY `pai_time_y` (`pai_time_y`),
  KEY `pai_time_m` (`pai_time_m`),
  KEY `pai_time_d` (`pai_time_d`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='竞拍记录表';


CREATE TABLE `%DB_PREFIX%pai_violations`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `podcast_id` int(11) NOT NULL COMMENT '主播id',
  `create_time` int(11) NOT NULL COMMENT '违规事件 时间戳   GMT+0',
  `create_date` date NOT NULL COMMENT '违规事件 年月日   GMT+8',
  `create_time_y` int(4) NOT NULL COMMENT '违规事件 年   GMT+8',
  `create_time_m` int(2) NOT NULL COMMENT '违规事件 月   GMT+8',
  `create_time_d` int(2) NOT NULL COMMENT '违规事件 日   GMT+8',
  `create_time_ym` varchar(50) NOT NULL COMMENT '违规事件 年月   GMT+8',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主播拍卖违规记录表';


CREATE TABLE `%DB_PREFIX%goods`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增字段',
  `user_id` int(11) NOT NULL COMMENT '主播ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `imgs` text NOT NULL COMMENT '图片（JSON数据）',
  `imgs_details` text NOT NULL COMMENT '商品详情图片',
  `price` decimal(20,2) NOT NULL COMMENT '商品价钱',
  `pai_diamonds` decimal(20,2) NOT NULL COMMENT '商品直播价格（秀豆）',
  `url` varchar(255) NOT NULL COMMENT '商品详情URL地址',
  `description` text COMMENT '商品描述',
  `is_delete` tinyint(1) NOT NULL COMMENT '商品状态 0为正常,1为删除（值为1时不在前端展示）',
  `kd_cost` decimal(20,2) NOT NULL COMMENT '快递费用',
  `score` int(255) NOT NULL COMMENT '经验',
  `inventory` int(255) NOT NULL COMMENT '库存',
  `is_effect` int(1) NOT NULL COMMENT '商品状态 1为正常,0为删除（值为0时不在前端展示）',
  `sales` int(11) NOT NULL COMMENT '初始销售量',
  `number` int(11) NOT NULL COMMENT '初始售卖人数',
  `bz_diamonds` int(11) NOT NULL COMMENT '竞拍保证金',
  `jj_diamonds` int(11) NOT NULL COMMENT '竞拍加价幅度',
  `pai_time` float(11,1) NOT NULL COMMENT '竞拍时间',
  `cate_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品分类',
  `podcast_ticket` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '主播商品抽佣',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品表';


CREATE TABLE `%DB_PREFIX%user_goods`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '主播ID',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `name` varchar(50) NOT NULL COMMENT '商品名称',
  `imgs` text NOT NULL COMMENT '图片（JSON数据）',
  `imgs_details` text NOT NULL COMMENT '商品详情图片',
  `price` decimal(20,2) NOT NULL COMMENT '商品价钱',
  `description` text COMMENT '商品描述',
  `kd_cost` decimal(20,2) NOT NULL COMMENT '快递费用',
  `score` int(255) NOT NULL COMMENT '经验',
  `inventory` int(255) NOT NULL COMMENT '库存',
  `is_effect` tinyint(1) NOT NULL COMMENT '商品状态 1为正常,0为删除（值为0时不在前端展示）',
  `pai_diamonds` decimal(20,2) NOT NULL COMMENT '商品直播价格（秀豆）',
  `sales` int(11) NOT NULL COMMENT '初始销售量',
  `number` int(11) NOT NULL COMMENT '初始售卖人数',
  `bz_diamonds` int(11) NOT NULL COMMENT '竞拍保证金',
  `jj_diamonds` int(11) NOT NULL COMMENT '竞拍加价幅度',
  `pai_time` int(11) NOT NULL COMMENT '竞拍时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='主播商品表';


CREATE TABLE `%DB_PREFIX%courier` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) NOT NULL COMMENT '订单编号',
  `courier_number` varchar(255) NOT NULL COMMENT '物流单号',
  `courier_offic` varchar(255) DEFAULT NULL COMMENT '物流公司',
  `courier_details` text COMMENT '物流信息详情',
  `view_time` varchar(255) DEFAULT NULL COMMENT '上次查看时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='物流信息表';


CREATE TABLE `%DB_PREFIX%shopping_cart`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增字段',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `name` text NOT NULL COMMENT '购买的产品显示名称(包含购买的规格)',
  `imgs` text NOT NULL COMMENT '商品图片（JSON数据）',
  `attr` varchar(255) NOT NULL COMMENT '购买的相关属性的ID，用半角逗号分隔',
  `unit_price` decimal(20,2) NOT NULL COMMENT '单价',
  `number` int(11) NOT NULL COMMENT '数量',
  `total_price` decimal(20,2) NOT NULL COMMENT '总价',
  `verify_code` varchar(255) NOT NULL COMMENT '验证唯一的标识码（由商品ID与属性ID组合加密生成）',
  `create_time` datetime NOT NULL COMMENT '加入购物车的时间',
  `update_time` datetime NOT NULL COMMENT '更新的时间',
  `return_money` decimal(20,2) NOT NULL COMMENT '返现金的单价',
  `return_total_money` decimal(20,2) NOT NULL COMMENT '返现金的总价',
  `return_score` int(11) NOT NULL COMMENT '返积分的单价',
  `return_total_score` int(11) NOT NULL COMMENT '返积分的总价',
  `cate_id` int(11) NOT NULL COMMENT '商品分类',
  `sub_name` varchar(255) NOT NULL COMMENT '简短名称',
  `podcast_id` int(11) NOT NULL COMMENT '商品所属的主播ID',
  `attr_str` text NOT NULL COMMENT '属性组合的显示名称',
  `is_effect` int(1) NOT NULL DEFAULT '1' COMMENT '购物车中商品的失效状态，1有效，0失效，该字段用于统计商户或后台更改价格或者
属性导致购物车中的商品与原商品不符而失效',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`),
  CONSTRAINT `goods_id` FOREIGN KEY (`goods_id`) REFERENCES `fanwe_goods` (`id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `fanwe_user` (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='购物车表';


CREATE TABLE `%DB_PREFIX%goods_cate`(
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id自增长，分类ID',
  `name` varchar(255) NOT NULL COMMENT '分类名称',
  `is_effect` int(1) NOT NULL DEFAULT '1' COMMENT '分类是否有效  1有效 0无效',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品分类表';


CREATE TABLE `%DB_PREFIX%podcast_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '主播ID',
  `name` varchar(50) NOT NULL COMMENT '商品名称',
  `imgs` text NOT NULL COMMENT '图片（JSON数据）',
  `price` decimal(20,2) NOT NULL COMMENT '商品价钱',
  `url` text NOT NULL COMMENT '商品URL地址',
  `description` text NOT NULL COMMENT '商品描述',
  `is_effect` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品状态 1为正常,0为删除（值为0时不在前端展示）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主播个人商品表';

INSERT INTO `%DB_PREFIX%m_config` VALUES ('', 'platform_on_commission','主播抽取佣金比例','基础配置','50','0','0','','','用于购物主播抽成。商品价钱(人民币)*主播抽取佣金比例。单位：%');

INSERT INTO `%DB_PREFIX%plugin` VALUES ('5', '4', './public/attachment/201701/24/16/58870cc1be91e.png', '竞拍', '1', 'pai', '1');
INSERT INTO `%DB_PREFIX%plugin` VALUES ('6', '5', '.public/attachment/201701/24/16/58870ca97c72a.png', '购物', '1', 'shop', '1');
INSERT INTO `%DB_PREFIX%plugin` VALUES ('7', '6', '.public/attachment/201701/24/16/58870ca97c72a.png', '小店', '1', 'podcast_goods', '1');

INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'AddGoods', '添加商品', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'Goods', '商品管理', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'GoodsCate', '分类列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'User_Goods', '主播平台商品', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'PodcastGoods', '主播小店商品', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'PodcastOrder', '购物订单列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'PaiGoods', '商品列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'GoodsOrder', '竞拍订单列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'UserAddr', '用户地址', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'UserNotice', '消息列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'PaiJoin', '竞拍列表', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'UserDiamondsLog', '保证金记录', '1',  '0');
INSERT INTO `%DB_PREFIX%role_module` VALUES ('', 'Refund', '申诉订单', '1',  '0');

UPDATE `%DB_PREFIX%conf` SET `value`='1.0' WHERE (`name`='DB_VERSION');