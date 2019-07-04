/*
Navicat MySQL Data Transfer

Source Server         : guoweiOS3
Source Server Version : 50173
Source Host           : 127.0.0.1:3306
Source Database       : gamebox

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2019-07-04 10:23:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account_mst`
-- ----------------------------
DROP TABLE IF EXISTS `account_mst`;
CREATE TABLE `account_mst` (
  `id` smallint(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `login` varchar(20) NOT NULL,
  `password` char(32) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `company` int(11) NOT NULL DEFAULT '0',
  `power` text NOT NULL COMMENT '拥有的权限',
  `allow_app` varchar(1000) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `login_error` tinyint(4) NOT NULL DEFAULT '0',
  `last_time` int(11) NOT NULL DEFAULT '0',
  `reset_pwd` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COMMENT='账号';

-- ----------------------------
-- Records of account_mst
-- ----------------------------
INSERT INTO `account_mst` VALUES ('5', 'admin', 'admin', 'c33367701511b4f6020ec61ded352059', '4', '1', ',super,', ',*,', '1', '0', '1562206009', '1562205995');
INSERT INTO `account_mst` VALUES ('55', 'gw', 'gw', 'c33367701511b4f6020ec61ded352059', '0', '0', ',acv-1_upload_package_manage,acv-1_home_show_first_manage,acv-1_home_show_manage,acv-0_game_manage,acv-0_game_type_manage,acv-0_game_tag_manage,', ',*,', '1', '0', '1562206803', '1562205378');

-- ----------------------------
-- Table structure for `game`
-- ----------------------------
DROP TABLE IF EXISTS `game`;
CREATE TABLE `game` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏列表';

-- ----------------------------
-- Records of game
-- ----------------------------

-- ----------------------------
-- Table structure for `game_home_show`
-- ----------------------------
DROP TABLE IF EXISTS `game_home_show`;
CREATE TABLE `game_home_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` int(11) NOT NULL COMMENT '推荐游戏',
  `displayIndex` int(2) DEFAULT NULL COMMENT '首页展示位',
  `showIconName` varchar(50) DEFAULT NULL COMMENT '图片',
  `showIconUrl` varchar(100) DEFAULT NULL COMMENT '图片链接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='首页展示9款';

-- ----------------------------
-- Table structure for `game_home_show_first`
-- ----------------------------
DROP TABLE IF EXISTS `game_home_show_first`;
CREATE TABLE `game_home_show_first` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayIndex` int(2) NOT NULL COMMENT '首页展示位',
  `appId` int(11) DEFAULT NULL COMMENT '推荐游戏',
  `isPic` bigint(1) DEFAULT NULL,
  `showIconName` varchar(50) DEFAULT NULL COMMENT '图片',
  `showIconUrl` varchar(100) DEFAULT NULL COMMENT '图片链接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='首页展示第一款（可配置多个游戏及视频）';

-- ----------------------------
-- Table structure for `game_rank_hot`
-- ----------------------------
DROP TABLE IF EXISTS `game_rank_hot`;
CREATE TABLE `game_rank_hot` (
  `id` int(10) NOT NULL COMMENT '排序',
  `appId` int(10) NOT NULL COMMENT '游戏id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='热门排行榜';

-- ----------------------------
-- Table structure for `game_rank_recommend`
-- ----------------------------
DROP TABLE IF EXISTS `game_rank_recommend`;
CREATE TABLE `game_rank_recommend` (
  `id` int(10) NOT NULL COMMENT '排序',
  `appId` int(10) NOT NULL COMMENT '游戏id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐排行榜';


-- ----------------------------
-- Table structure for `game_tag`
-- ----------------------------
DROP TABLE IF EXISTS `game_tag`;
CREATE TABLE `game_tag` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='游戏标签';


-- ----------------------------
-- Table structure for `game_type`
-- ----------------------------
DROP TABLE IF EXISTS `game_type`;
CREATE TABLE `game_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL COMMENT '类型名',
  `shortname` varchar(10) NOT NULL COMMENT '简称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='游戏类型';


-- ----------------------------
-- Table structure for `log_system`
-- ----------------------------
DROP TABLE IF EXISTS `log_system`;
CREATE TABLE `log_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `success` tinyint(4) NOT NULL DEFAULT '0',
  `runtime` int(11) NOT NULL DEFAULT '0',
  `info` varchar(1000) NOT NULL,
  `uid` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `isread` tinyint(4) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='系统日志';


-- ----------------------------
-- Table structure for `notice`
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noticeName` varchar(50) DEFAULT NULL COMMENT '公告图片名称',
  `noticeUrl` varchar(150) DEFAULT NULL COMMENT '公告图片路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='公告';

-- ----------------------------
-- Records of notice
-- ----------------------------

-- ----------------------------
-- Table structure for `package_box`
-- ----------------------------
DROP TABLE IF EXISTS `package_box`;
CREATE TABLE `package_box` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `version` varchar(20) DEFAULT NULL COMMENT '版本号',
  `versionName` varchar(50) DEFAULT NULL COMMENT '版本名称',
  `desc` varchar(500) DEFAULT NULL COMMENT '描述',
  `downName` varchar(50) DEFAULT NULL,
  `downUrl` varchar(100) DEFAULT NULL COMMENT '下载地址',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='tv盒子更新表';

-- ----------------------------
-- Records of package_box
-- ----------------------------

-- ----------------------------
-- Table structure for `package_list`
-- ----------------------------
DROP TABLE IF EXISTS `package_list`;
CREATE TABLE `package_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` int(15) NOT NULL COMMENT '游戏ID',
  `appPkg` varchar(100) NOT NULL COMMENT '游戏包名',
  `version` varchar(100) NOT NULL COMMENT '游戏版本号',
  `versionName` varchar(100) DEFAULT NULL COMMENT '游戏版本名称',
  `desc` varchar(1000) DEFAULT NULL COMMENT '游戏描述',
  `category` varchar(255) DEFAULT NULL COMMENT '游戏分类(可有多个类型，可能涉及到游戏类型管理)',
  `tag` varchar(10) DEFAULT NULL COMMENT '标签',
  `star` smallint(2) DEFAULT NULL COMMENT '游戏星级',
  `company` varchar(100) DEFAULT '0' COMMENT '游戏生产商',
  `size` varchar(100) DEFAULT NULL COMMENT '游戏包大小',
  `md5` char(32) DEFAULT NULL COMMENT '游戏apk MD5',
  `downloadTimes` int(11) DEFAULT NULL COMMENT '游戏的下载次数',
  `searchKey` varchar(20) DEFAULT NULL COMMENT '游戏名称首字母搜索（熊大快跑 >>  XDKP）',
  `searchKeyFull` varchar(100) DEFAULT NULL COMMENT '游戏名称汉语拼音全（熊大快跑 >>  XIONGDAKUAIPAO）',
  `packageName` varchar(50) DEFAULT NULL,
  `downUrl` varchar(100) DEFAULT NULL COMMENT '下载地址',
  `iconName` varchar(50) DEFAULT NULL,
  `iconUrl` varchar(100) DEFAULT NULL COMMENT 'ICON下载地址',
  `homeIconName` varchar(50) DEFAULT NULL,
  `homeIconUrl` varchar(100) DEFAULT NULL COMMENT '首页图片下载地址',
  `categoryIconName` varchar(50) DEFAULT NULL,
  `categoryIconUrl` varchar(100) DEFAULT NULL COMMENT '分类图片下载地址',
  `screenshotName` varchar(150) DEFAULT NULL,
  `screenshotUrl` varchar(300) DEFAULT NULL COMMENT '截图下载地址',
  `videoName` varchar(50) DEFAULT NULL,
  `videoUrl` varchar(100) DEFAULT NULL COMMENT '视频下载地址',
  `createDate` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='APK包上传信息';

-- ----------------------------
-- Records of package_list
-- ----------------------------
