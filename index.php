<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用入口文件

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
// 定义应用目录
define('APP_PATH', './Apps/');
define('IS_WINDOWS', strtolower(PHP_OS) == 'linux' ? false : true);
define('__PUBLIC__', 'Public');

//项目定义

//数据库名、 表名 等相关定义开始
define('DB', 'gamebox');
define('TABLE_ACCOUNT', DB . ':account_mst');
define('TABLE_SYSTEM_LOG', DB . ':log_system');
define('TABLE_CHANNEL_MST', DB . ':channel_mst');
define('TABLE_GAME', DB . ':game');
define('TABLE_GAME_TYPE', DB . ':game_type');
define('TABLE_GAME_TAG', DB . ':game_tag');

define('TABLE_PACKAGE_LIST', DB . ':package_list');
define('TABLE_GAME_HOME_SHOW_FIRST', DB . ':game_home_show_first');
define('TABLE_GAME_HOME_SHOW', DB . ':game_home_show');

define('TABLE_GAME_RANK_HOT', DB . ':game_rank_hot');
define('TABLE_GAME_RANK_RECOMMEND', DB . ':game_rank_recommend');
define('TABLE_GAME_NOTICE', DB . ':notice');

define('TABLE_PACKAGE_BOX', DB . ':package_box');

define('MSG_ERROR_POWER', "您没有权限执行此操作!");
//数据库名、 表名 等相关定义结束


#=====================================================================================================
#                                    权限配置2018 - start
#=====================================================================================================
//权限定义开始=====================================
define('POWER_SUPER', 'super');  //超级管理员权限

// 定义所有模块
define('MODEL2018_BOX_MANAGE','acv-2_'); //盒子管理
define('MODEL2018_PACKAGE_MANAGE','acv-1_'); //APK包管理
define('MODEL2018_SYSTEM_MANAGE','acv-0_'); //系统管理

# 定义所有权限 - 加上当前模块名作为前缀，防止重复 -

//`APK包管理`权限
define("POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE",MODEL2018_PACKAGE_MANAGE."upload_package_manage");
define("POWER2018_PACKAGE_MANAGE_HOME_SHOW_FIRST_MANAGE",MODEL2018_PACKAGE_MANAGE."home_show_first_manage");
define("POWER2018_PACKAGE_MANAGE_HOME_SHOW_MANAGE",MODEL2018_PACKAGE_MANAGE."home_show_manage");
define("POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE",MODEL2018_PACKAGE_MANAGE."rank_hot_manage");
define("POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE",MODEL2018_PACKAGE_MANAGE."rank_recommend_manage");
define("POWER2018_PACKAGE_MANAGE_NOTICE_MANAGE",MODEL2018_PACKAGE_MANAGE."notice_manage");

//`盒子管理`权限
define("POWER2018_BOX_MANAGE_UPDATE",MODEL2018_BOX_MANAGE."update");

//`系统管理`权限
define("POWER2018_SYSTEM_MANAGE_GAME_MANAGE",MODEL2018_SYSTEM_MANAGE."game_manage");
define("POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE",MODEL2018_SYSTEM_MANAGE."game_type_manage");
define("POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE",MODEL2018_SYSTEM_MANAGE."game_tag_manage");
define("POWER2018_SYSTEM_MANAGE_USER_MANAGE",MODEL2018_SYSTEM_MANAGE."user_manage");
define("POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE",MODEL2018_SYSTEM_MANAGE."channel_manage");
define("POWER2018_SYSTEM_MANAGE_SYSTEM_LOG",MODEL2018_SYSTEM_MANAGE."system_log");
define("POWER2018_SYSTEM_MANAGE_MOBILE_NUMBER_ADDRESS",MODEL2018_SYSTEM_MANAGE."mobile_number_address");
define("POWER2018_SYSTEM_MANAGE_CHANGE_PASSWORD",MODEL2018_SYSTEM_MANAGE."change_password");

#=====================================================================================================
#                                    权限配置2018 - end
#=====================================================================================================

require './ThinkPHP/ThinkPHP.php';

