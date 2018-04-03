/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : demo

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-04-02 16:44:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for city_area
-- ----------------------------
DROP TABLE IF EXISTS `city_area`;
CREATE TABLE `city_area` (
  `region_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `region_pid` int(11) unsigned DEFAULT '0' COMMENT '父id,本表中的region_id字段,0没有父级',
  `region_path` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT '路径',
  `region_level` int(11) unsigned DEFAULT NULL COMMENT '层级',
  `region_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `region_code` varchar(64) DEFAULT NULL COMMENT '地区代码',
  `region_hot` int(11) DEFAULT '0' COMMENT '热门地区',
  `region_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='多国家城市区域';

-- ----------------------------
-- Records of city_area
-- ----------------------------

-- ----------------------------
-- Table structure for city_area_translation
-- ----------------------------
DROP TABLE IF EXISTS `city_area_translation`;
CREATE TABLE `city_area_translation` (
  `lang_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实体id',
  `lang_name` varchar(255) DEFAULT NULL COMMENT '翻译',
  `spell` varchar(255) DEFAULT '' COMMENT '拼写: 例如中文是拼音',
  `lang_code` varchar(12) NOT NULL DEFAULT '0' COMMENT '评论人',
  `letter_sort` varchar(64) DEFAULT '' COMMENT '字母排序',
  `lang_sort` int(11) DEFAULT '0' COMMENT '排序',
  `lang_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `unique` (`region_id`,`lang_code`) USING BTREE,
  KEY `region_id` (`region_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='多国家城市区域翻译';

-- ----------------------------
-- Records of city_area_translation
-- ----------------------------

-- ----------------------------
-- Table structure for region
-- ----------------------------
DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `region_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `region_pid` int(11) unsigned DEFAULT '0' COMMENT '父id,本表中的region_id字段,0没有父级',
  `region_path` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT '路径',
  `region_level` int(11) unsigned DEFAULT NULL COMMENT '层级',
  `region_name` varchar(255) DEFAULT NULL COMMENT '名称',
  `region_code` varchar(64) DEFAULT NULL COMMENT '地区代码',
  `region_hot` int(11) DEFAULT '0' COMMENT '热门地区',
  `region_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='地区';

-- ----------------------------
-- Records of region
-- ----------------------------

-- ----------------------------
-- Table structure for region_translation
-- ----------------------------
DROP TABLE IF EXISTS `region_translation`;
CREATE TABLE `region_translation` (
  `lang_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '实体id',
  `lang_name` varchar(255) DEFAULT NULL COMMENT '翻译',
  `spell` varchar(255) DEFAULT '' COMMENT '拼写: 例如中文是拼音',
  `lang_code` varchar(12) NOT NULL DEFAULT '0' COMMENT '评论人',
  `letter_sort` varchar(64) DEFAULT '' COMMENT '字母排序',
  `lang_sort` int(11) DEFAULT '0' COMMENT '排序',
  `lang_changed` int(11) DEFAULT '0' COMMENT '操作时间: 防止更新失败情况',
  PRIMARY KEY (`lang_id`),
  UNIQUE KEY `unique` (`region_id`,`lang_code`) USING BTREE,
  KEY `region_id` (`region_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区翻译';

-- ----------------------------
-- Records of region_translation
-- ----------------------------
