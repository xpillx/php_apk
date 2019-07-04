<?php
//数据表配置文件
/**
 *  配置说明：
 *  只允许超级管理员读取的： show=super   ||   data=super ||  datasub=super
 *  允许有某个权限的人读取的： show=viewP  ||    show=viewP || show=viewP
 *  允许所有人读取的： show=all    || data=all  || datasub=all
 */

return array(

    #----------------------------------------------------------------------------#
    #                               `系统管理`模块                                 #
    #----------------------------------------------------------------------------#
    //系统管理 - 用户管理
    TABLE_ACCOUNT => array(
        'name' => '用户表',
        'field' => array(
            'login' => '登录账号',
            'name' => '姓名',
            'password'=>'密码',
            'power'=>'权限',
            'last_time'=>'最后登录',
            'allow_app'=>'允许APP',
            'status'=>'状态'
        ),

        'show='.POWER_SUPER => array(
            'id', 'login', 'name' ,
            'power'=>array('func'=>'getPowerString=###','attr'=>'style="width:65%"'),
            'allow_app'=>array('func'=>'getAppString=###'),
            'last_time'=>array('func'=>'localDatetime=###') ,
            '_control' => array(
                'status' => array('data' => 'C=ADMIN_STATUS'),
            ),
            '_order'=>'id asc',
        ),

        'data='.POWER_SUPER => array(
            'login','name', 'password',
            'power'=>array('func'=>'getPowerCheckbox=power,###'),
            'allow_app'=>array('type'=>'checkbox','data'=>'getAppWithAll'),
        ),

        'datasub='.POWER_SUPER => array(
            'login',
            'name',
            'password'=>array('func'=>'passwd=###'),
            'allow_app'=>array('func'=>'arrayToStr=###','value'=>'maybe'),
            'power'=>array('func'=>'arrayToStr=###','value'=>'maybe'),
            'reset_pwd'=>array('func'=>'time','when'=>'add'),
        ),
        'del' => true,
    ),

    //系统管理 - 系统日志
    TABLE_SYSTEM_LOG => array(
        'name' => '系统日志',
        'field' => array('id' => 'ID', 'tag' => '标签', 'success' => '执行结果', 'module' => '模块', 'action' => '动作', 'info' => '说明', 'runtime' => '运行时间', 'create_time' => '创建时间', 'isread' => '读取状态', 'uid' => '用户', 'ip' => 'IP'),
        'show' => array('id', 'tag',
            'success' => array('func' => 'getKeyByValue=LOG_LOGIN_SUCCESS,###'),
            'module', 'action', 'uid' => array('func' => 'getLoginNameById=###'),
            'ip',
            'info' => array('type' => 'toggle'),
            'runtime' => array('func' => 'friendSecond=###'), 'create_time' => array('func' => 'mdate=###'),
        ),
        'tab' => array(
            '今天' => array('link' => 'table='.TABLE_SYSTEM_LOG.'&where=[create_time]gt[$strtotime=today$]', 'icon' => 'icon_add'),
            '昨天' => array('link' => 'table='.TABLE_SYSTEM_LOG.'&where=[create_time]gt[$strtotime=today -1day$]|[create_time]lt[$strtotime=today$]'),
            '最近一周' => array('link' => 'table='.TABLE_SYSTEM_LOG.'&where=[create_time]gt[$strtotime=today - 7day$]'),
        ),
        'search' => array(
            '搜索' => array(
                'I D' => array('name' => 'id', 'sign' => 'eq|gt|lt', 'sign_def' => 'gt'),
                '标签' => array('name' => 'tag', 'type' => 'select', 'data' => 'Log|getAllTag')
            ),
        ),
    ),

    //系统管理 - 渠道管理
    TABLE_CHANNEL_MST =>array (
        'field'=>array('channel_id'=>'渠道ID' , 'sitename'=>'渠道名' , 'enable'=>'是否显示' ),
        'show='.POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE =>array(
            'id','channel_id','sitename',
        ),
        'tab'=>array(
            '刷新渠道'=>array(
                'link'=>'/Api/freshChannel',
            )
        ),
        'search='.POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE =>array(
            '搜索'=>array(
                '渠道号'=>array('name'=>'channel_id','sign'=>'like'),
                '渠道名'=>array('name'=>'sitename','sign'=>'like'),
            )
        ),
        'del='.POWER2018_SYSTEM_MANAGE_CHANNEL_MANAGE=>true
    ),

    //系统管理 - 游戏管理
    TABLE_GAME=>array(
        'name' => '游戏管理',
        'field'=>array('id'=>'游戏ID','name'=>'游戏名称'),
        'show='.POWER2018_SYSTEM_MANAGE_GAME_MANAGE=>array('id','name'),
        'data='.POWER2018_SYSTEM_MANAGE_GAME_MANAGE=>array(
            'id',
            'name',
        ),
        'datasub='.POWER2018_SYSTEM_MANAGE_GAME_MANAGE=>'Datasub|checkGameSubmit',
        'del='.POWER2018_SYSTEM_MANAGE_GAME_MANAGE=>true,
    ),

    //系统管理 - 类型管理
    TABLE_GAME_TYPE=>array(
        'name' => '类型管理',
        'field'=>array('id'=>'ID','name'=>'类型名称','shortname'=>'简称'),
        'show='.POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE=>array('id','name','shortname'),
        'data='.POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE=>array(
            'name',
            'shortname',
        ),
        'datasub='.POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE=>'Datasub|checkGameTypeSubmit',
        'del='.POWER2018_SYSTEM_MANAGE_GAME_TYPE_MANAGE=>true,
    ),

    //系统管理 - 标签管理
    TABLE_GAME_TAG=>array(
        'name' => '标签管理',
        'field'=>array('id'=>'ID','name'=>'标签名称'),
        'show='.POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE=>array('id','name'),
        'data='.POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE=>array(
            'id',
            'name',
        ),
        'datasub='.POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE=>'Datasub|checkGameTagSubmit',
        'del='.POWER2018_SYSTEM_MANAGE_GAME_TAG_MANAGE=>true,
    ),

    #----------------------------------------------------------------------------#
    #                               `APK包管理`模块                                #
    #----------------------------------------------------------------------------#

    //APK包管理 - APK包上传
    TABLE_PACKAGE_LIST => array(
        'name' => 'APK包上传',
        'field' => array(
            'appId'=>'游戏ID',
            'appPkg'=>'包名',
            'version'=>'版本号',
            'versionName'=>'版本名称',
            'desc'=>'描述',
            'category'=>'分类',
            'tag'=>'标签',
            'star'=>'星级',
            'company'=>'生产商',
            'size'=>'包大小',
            'md5'=>'APK_MD5',
            'downloadTimes'=>'下载次数',
            'searchKey'=>'名称首字母',
            'searchKeyFull'=>'名称全拼',
            'downUrl'=>'下载地址',
            'iconUrl'=>'ICON地址',
            'homeIconUrl'=>'首页ICON地址',
            'categoryIconUrl'=>'分类ICON地址',
            'screenshotUrl'=>'截图下载地址',
            'videoUrl'=>'视频下载地址',
            'createDate'=>'时间',
        ),
        'show='.POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE => array(
            'id',
            'appId'=>array('func'=>'getAppNameById=###'),
            'appPkg',
            'version',
            'versionName',
            'desc',
            'category',
            'tag'=>array('func'=>'getGameTagById=###'),
            'star',
            'company',
            'size',
            'md5',
            'downloadTimes',
            'searchKey',
            'searchKeyFull',
            'downUrl',
            'iconUrl'=>array('func'=>'File|showImg=###'),
            'homeIconUrl'=>array('func'=>'File|showImg=###'),
            'categoryIconUrl'=>array('func'=>'File|showImg=###'),
            'screenshotUrl'=>array('func'=>'File|showImg=###'),
            'videoUrl',
            'createDate',
        ),
        'tab' => array(
            '添加'=>array(
                'link'=>'/PackageManage/packageAdd',
                'icon'=>'icon_add',
            ),
        ),
        'operate='.POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE => array(
            '修改' => array(
                'link'=>'/PackageManage/packageEdit?id=@id@',
                'icon'=>'icon_edit',
            ),
            '删除' => array(
                'link'=>'/PackageManage/packageDel?id=@id@',
                'icon'=>'icon_delete',
                'attr'=>'onclick="javascript:return confirm(\'你确定要删除么？\')"',
            ),
        ),
        'search='.POWER2018_PACKAGE_MANAGE_UPLOAD_PACKAGE_MANAGE=>array(
            '搜索'=>array(
                '游戏'=>array('type'=>'select','name'=>'appId','data'=>'getAllowAppAsData' ),
                '标签'=>array('type'=>'select','name'=>'tag','data'=>'getGameTag'),
            )
        ),
    ),

    //APK包管理 - 首页展示第一款游戏位设置
    TABLE_GAME_HOME_SHOW_FIRST => array(
        'name' => '首页展示1',
        'field' => array(
            'displayIndex'=>'展示位',
            'appId'=>'游戏ID',
            'isPic'=>'是否展示图片',
            'showIconUrl'=>'图片',
        ),
        'show='.POWER2018_PACKAGE_MANAGE_HOME_SHOW_FIRST_MANAGE => array(
            'id',
            'displayIndex',
            'appId'=>array('func'=>'getAppNameById=###'),
            'isPic'=>array('func'=>'getKeyByValue=YESORNO,###'),
            'showIconUrl'=>array('func'=>'File|showImg=###'),
        ),
        'tab' => array(
            '添加'=>array(
                'link'=>'/HomeShowFirst/homeShowFirstAdd',
                'icon'=>'icon_add',
            ),
        ),
        'operate='.POWER2018_PACKAGE_MANAGE_HOME_SHOW_FIRST_MANAGE => array(
            '修改' => array(
                'link'=>'/HomeShowFirst/homeShowFirstEdit?id=@id@',
                'icon'=>'icon_edit',
            ),
            '删除' => array(
                'link'=>'/HomeShowFirst/homeShowFirstDel?id=@id@',
                'icon'=>'icon_delete',
                'attr'=>'onclick="javascript:return confirm(\'你确定要删除么？\')"',
            ),
        ),
    ),

    //APK包管理 - 首页展示9款游戏位设置
    TABLE_GAME_HOME_SHOW => array(
        'name' => '首页展示(2-10)',
        'field' => array(
            'appId'=>'游戏ID',
            'displayIndex'=>'展示位',
            'showIconUrl'=>'图片',

        ),
        'show='.POWER2018_PACKAGE_MANAGE_HOME_SHOW_MANAGE => array(
            'id',
            'appId'=>array('func'=>'getAppNameById=###'),
            'displayIndex',
            'showIconUrl'=>array('func'=>'File|showImg=###'),
            '_order'=>'displayIndex asc'
        ),
        'tab' => array(
            '添加'=>array(
                'link'=>'/HomeShow/homeShowAdd',
                'icon'=>'icon_add',
            ),
        ),
        'operate='.POWER2018_PACKAGE_MANAGE_HOME_SHOW_MANAGE => array(
            '修改' => array(
                'link'=>'/HomeShow/homeShowEdit?id=@id@',
                'icon'=>'icon_edit',
            ),
            '删除' => array(
                'link'=>'/HomeShow/homeShowDel?id=@id@',
                'icon'=>'icon_delete',
                'attr'=>'onclick="javascript:return confirm(\'你确定要删除么？\')"',
            ),
        ),
    ),

    //APK包管理 - 热门排行榜
    TABLE_GAME_RANK_HOT=>array(
        'name' => '热门排行榜',
        'field'=>array('id'=>'排名','appId'=>'游戏ID'),
        'show='.POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE=>array('id','appId'=>array('func'=>'getAppNameById=###'),'_order'=>'id asc'),
        'data='.POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE=>array(
            'id',
            'appId'=>array('type'=>'select','data'=>'getAllowAppAsData'),
        ),
        'datasub='.POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE=>'Datasub|checkRankHotSubmit',
        'del='.POWER2018_PACKAGE_MANAGE_RANK_HOT_MANAGE=>true,
    ),

    //APK包管理 - 推荐排行榜
    TABLE_GAME_RANK_RECOMMEND=>array(
        'name' => '推荐排行榜',
        'field'=>array('id'=>'排名','appId'=>'游戏ID'),
        'show='.POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE=>array('id','appId'=>array('func'=>'getAppNameById=###'),'_order'=>'id asc'),
        'data='.POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE=>array(
            'id',
            'appId'=>array('type'=>'select','data'=>'getAllowAppAsData'),
        ),
        'datasub='.POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE=>'Datasub|checkRankRecommendSubmit',
        'del='.POWER2018_PACKAGE_MANAGE_RANK_RECOMMEND_MANAGE=>true,
    ),

    //APK包管理 - 公告设置
    TABLE_GAME_NOTICE=>array(
        'name' => '公告设置',
        'field'=>array('id'=>'ID','noticeUrl'=>'公告'),
        'show='.POWER2018_PACKAGE_MANAGE_NOTICE_MANAGE=>array(
            'id',
            'noticeUrl'=>array('func'=>'File|showImg=###')
        ),
        'tab' => array(
            '添加'=>array(
                'link'=>'/Notice/noticeAdd',
                'icon'=>'icon_add',
            ),
        ),
        'operate='.POWER2018_PACKAGE_MANAGE_NOTICE_MANAGE => array(
            '修改' => array(
                'link'=>'/Notice/noticeEdit?id=@id@',
                'icon'=>'icon_edit',
            ),
            '删除' => array(
                'link'=>'/Notice/noticeDel?id=@id@',
                'icon'=>'icon_delete',
                'attr'=>'onclick="javascript:return confirm(\'你确定要删除么？\')"',
            ),
        ),
    ),

    #----------------------------------------------------------------------------#
    #                               `TV盒子管理`模块                                #
    #----------------------------------------------------------------------------#

    //TV盒子管理 - 盒子更新
    TABLE_PACKAGE_BOX => array(
        'name' => 'APK包上传',
        'field' => array(
            'version'=>'版本号',
            'versionName'=>'版本名称',
            'desc'=>'描述',
            'downUrl'=>'下载地址',
            'time'=>'时间',
        ),
        'show='.POWER2018_BOX_MANAGE_UPDATE => array(
            'id',
            'version',
            'versionName',
            'desc',
            'downUrl',
            'time',
        ),
        'tab' => array(
            '添加'=>array(
                'link'=>'/BoxUpdate/boxAdd',
                'icon'=>'icon_add',
            ),
        ),
        'operate='.POWER2018_BOX_MANAGE_UPDATE => array(
            '修改' => array(
                'link'=>'/BoxUpdate/boxEdit?id=@id@',
                'icon'=>'icon_edit',
            ),
            '删除' => array(
                'link'=>'/BoxUpdate/boxDel?id=@id@',
                'icon'=>'icon_delete',
                'attr'=>'onclick="javascript:return confirm(\'你确定要删除么？\')"',
            ),
        ),
    ),
);