2.51;

ALTER TABLE `%DB_PREFIX%prop`
ADD COLUMN `is_heat`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否热度值礼物 0：否，1：是';

ALTER TABLE `%DB_PREFIX%video`
ADD COLUMN `heat_value`  int(11) NOT NULL COMMENT '直播热度值';

ALTER TABLE `%DB_PREFIX%video`
ADD COLUMN `rank_update_time`  int(11) NOT NULL COMMENT '热度排行最后更新时间';

ALTER TABLE `%DB_PREFIX%video`
ADD COLUMN `rank_update_date`  date  NOT NULL COMMENT '热度排行更新日期';

ALTER TABLE `%DB_PREFIX%video`
ADD COLUMN `total_heat_value`  int(11) NOT NULL COMMENT '总热度值';


ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `open_lianmai`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用连麦  0为 不禁用 1 为禁用';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `open_private`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否禁用私密直播  0为 不禁用 1 为禁用';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `allow_start_time`  char(2) DEFAULT NULL COMMENT  '单个主播允许直播的起始时间,空为不限制';
ALTER TABLE `%DB_PREFIX%user`
ADD COLUMN `allow_end_time`  char(2) DEFAULT NULL COMMENT '单个主播允许直播的起始时间,空为不限制';









