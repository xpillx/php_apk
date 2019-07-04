<?php
return array(
    //'配置项'=>'配置值'
    'URL_MODEL' => 0,
    'URL_HTML_SUFFIX' => false,
    'URL_CASE_INSENSITIVE' => true,
    'SESSION_AUTO_START' => true,


    /**
     * 此配置文件写前台和后台公用的配置文件部分，
     * 前台页面会加载此配置文件
     */
    //DB的配置选项只能在本地环境运行时有效，sae模式下DB的选项无效，但也不影响
    'DB_TYPE' => 'mysql',
//    'DB_' . DB => IS_WINDOWS ? 'mysqli://root:654321@localhost:3306/' . DB : 'mysql://top:top@2015@127.0.0.1:3306/' . DB,
    'DB_' . DB => IS_WINDOWS ? 'mysqli://root:654321@localhost:3306/' . DB : 'mysql://root:654321@localhost:3306/' . DB,


    # 作弊用户功能限制 - 作弊用户类型
    "CHEAT_USER_TYPE" => array("指定用户"=>1,"黑名单用户"=>2),


    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    'TMPL_ACTION_ERROR' => 'Public/jump', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public/jump', // 默认成功跳转对应的模板文件

    'SESSION_PREFIX' => 'hijoyadmin',
    'SESSION_OPTIONS' => array('expire' => 0),
    'COOKIE_PREFIX' => 'hijoyadmin',
    'SHOW_PAGE_TRACE' => IS_WINDOWS ? true : true, //当此项开启时 日志是不会被记录的·
    'LOG_RECORD' => true, // 开启日志记录  ,调试模式开启的情况下：所有的日志都会被记录
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN',  //'EMERG,ALERT,CRIT,ERR,WARN,NOTICE,INFO,DEBUG,SQL',

    'DIR_MANAGER' => '/data/www/center/u3d/',
    'DIR_MANAGER_URL' => 'http://hijoyres.joymeng.com/',
    'BASE_URL' => IS_WINDOWS ? 'http://www.hijoyadmin.com/upload/' : 'http://image.hiwechats.com/images/',
    'BASE_DIR' => IS_WINDOWS ? 'D:/www/hijoyadmin-new/upload/' : '/data/web/gamebox/html/images/',

    'IMG' => array(
        'verify_code' => 'bearrun-exchange/',
        'data2excel' => 'data2excel/',
    ),

);