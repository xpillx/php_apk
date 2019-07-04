<?php
$conf1 = array(
    'VERIFY_CODE' => false, //开启验证码
    'ADMIN_STATUS' => array('关闭' => '0', '正常' => '1', '锁定' => '2'), //锁定是用户连续登陆错误时系统自动锁定
    'ADMIN_LOCK_ERROR' => 5, //连续5此密码错误，自动锁定
    'ADMIN_LOCK_TIME' => 10, //自动解锁时间，设置为0表示永不解锁，只有后台可以解锁

    "HORIZON"=>'|',
    "COMMA" => ',',
    'LOG_TAG' => array('错误' => '0', '登录' => '1', 'Ajax' => '2'),
    'LOG_LOGIN_SUCCESS' => array('<span style="color:red">错误</span>' => '0', '成功' => '1'),
    'LOG_SYSTEM_READ' => array('未读' => '0', '已读' => '1'),
    'ENABLE' => array( '允许' => '1' , '禁止' => '0' ),
    'YESORNO' => array( '是' => '1' , '否' => '0'),
    'PUBLISHING_ENVIRONMENT' => array( '测试环境' => '1' , '正式环境' => '0'),

    'DEFAULT_FIELD_NAME'=>array(
            'id' => 'ID',
            'app_id'=>'游戏ID',
            'uid'=>'玩家ID',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'key'=>'键名',
            'subkey'=>'子键名',
            'value'=>'值',
            'ip' => 'IP',
            'runtime' => '运行时间',
            'info' => '说明',
            '_with'=>'子数据'
    ),

    'NAVIGATION' => array(
        /**
         * 顶部导航 => array(
         *      左侧导航1=> array(
         *          'link'=> 链接地址， 格式：控制器名/方法名?参数1=值&参数2=值
         *          'power'=> 所需权限 ，不设置power则无需任何权限，允许多个权限用|分隔
         *          'func'=>'hasChannelRole',  先检查power ，再检查函数是否返回true
         *      ),
         * ),
         */
        
        'APK包管理'=>array(
            'APK包上传'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_PACKAGE_LIST ),
                'power'=> POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE,
            ),
            '首页展示(1)'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_HOME_SHOW_FIRST ),
                'power'=> POWER2018_PACKAGE_MANAGE_HOME_SHOW_FIRST_MANAGE,
            ),
            '首页展示(2-10)'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_HOME_SHOW ),
                'power'=> POWER2018_PACKAGE_MANAGE_HOME_SHOW_MANAGE,
            ),
            '热门排行榜'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_RANK_HOT ),
                'power'=> POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE,
            ),
            '推荐排行榜'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_RANK_RECOMMEND ),
                'power'=> POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE,
            ),
            '公告设置'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_NOTICE ),
                'power'=> POWER2018_PACKAGE_MANAGE_NOTICE_MANAGE,
            ),
        ),

        '盒子管理'=>array(
            '盒子更新'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_PACKAGE_BOX ),
                'power'=> POWER2018_BOX_MANAGE_UPDATE,
            ),
        ),

        '系统管理' => array(
            '游戏管理'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME ),
                'power'=> POWER2018_SYSTEM_MANAGE_GAME_MANAGE,
            ),
            '类型管理'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_TYPE ),
                'power'=> POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE,
            ),
            '标签管理'=>array(
                'link' => '/Table/index?table='.urlencode( TABLE_GAME_TAG ),
                'power'=> POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE,
            ),
            '用户管理' => array(
                'link' => '/Table/index?table='.urlencode( TABLE_ACCOUNT ),
                'power'=> POWER2018_SYSTEM_MANAGE_USER_MANAGE,
            ),
            '渠道管理' => array(
                'link'=> '/Table/index?table=' .urlencode( TABLE_CHANNEL_MST ),
                'power'=> POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE ,
            ),
            '系统日志'=>array(
                'link'=> '/Table/index?table='.urlencode (TABLE_SYSTEM_LOG ),
                'power'=> POWER2018_SYSTEM_MANAGE_SYSTEM_LOG,
            ),
            '手机号码地址'=>array(
                'link'=>'/Common/phone',
                'power'=>POWER2018_SYSTEM_MANAGE_MOBILE_NUMBER_ADDRESS,
            ),
            '修改密码'=>array(
                'link'=> '/Login/password',
            ),
        ),
    ),

    'USER_POWER_2018' => array(
        '最高权限'=>array(
            '超级管理权限'=> POWER_SUPER,
        ),

        "APK包管理"=>array(
            "APK包上传" => POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE,
            "首页展示1"=>POWER2018_PACKAGE_MANAGE_HOME_SHOW_FIRST_MANAGE,
            "首页展示2-10"=>POWER2018_PACKAGE_MANAGE_HOME_SHOW_MANAGE,
            "热门排行榜"=>POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE,
            "推荐排行榜"=>POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE,
            "公告设置"=>POWER2018_PACKAGE_MANAGE_NOTICE_MANAGE,
        ),

        "盒子管理"=>array(
            "盒子更新" => POWER2018_BOX_MANAGE_UPDATE,
        ),


        "系统管理"=>array(
            "游戏管理"=>POWER2018_SYSTEM_MANAGE_GAME_MANAGE,
            "类型管理"=>POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE,
            "标签管理"=>POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE,
            "用户管理"=>POWER2018_SYSTEM_MANAGE_USER_MANAGE,
            "渠道管理"=>POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE,
            "手机号码地址"=>POWER2018_SYSTEM_MANAGE_MOBILE_NUMBER_ADDRESS,
            "修改密码"=>POWER2018_SYSTEM_MANAGE_CHANGE_PASSWORD,
        ),
    ),
    

);

$conf2 = require('config_table.php');
return array_merge($conf1, $conf2);