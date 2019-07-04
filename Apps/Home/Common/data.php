<?php
//获取数据相关的函数

use Home\Controller\ComponentController;

function getDataFromRedis($redisConfig)
{
    $config = C('redis_key.' . $redisConfig);
    if (!$config)
        error("未定义配置文件redis_key." . $redisConfig);

    $redisKey = empty($config['key']) ? $redisConfig : $config['key'];
    $redisKey = parseIString($redisKey);

    debug($redisKey, "Redis Key");
    switch ($config['type']) {
        case REDIS_TYPE_HSET :

            $temp = getRedis()->hGet($redisKey);
            $i = 0;
            foreach ($temp as $k => $v) {
                $data[$i]['key'] = $redisKey;
                $data[$i]['subkey'] = $k;
                $data[$i++]['value'] = $v;//simpleUl(  $v  , true );
            }
            return $data;
        default:  //默认redis的类型为set
            $redis = getRedis();
            $temp = $redis->get($redisKey);
            $i = 0;
            foreach ($temp as $k => $v) {
                $data[$i]['key'] = $redisKey;
                $data[$i]['subkey'] = $k;
                $data[$i]['value'] = $v;
                $i++;
            }
            return $data;
    }
}

function uidIn($uids)
{
    if (isSuperAdmin())
        return true;
    $uids = strToArray($uids, '-');
    return in_array(getAdminId(), $uids);

}


function getDataFromUrl($urlConfig, $resultIsJSON = true)
{
    $u = getInterfaceUrl($urlConfig);
    debug($u, __FUNCTION__);
    if (!$resultIsJSON)
        return file_get_contents($u);
    $data = json_decode(file_get_contents($u), true);

    debug($data, $u);
    if (isset($data['data']['rank']))
        return $data['data']['rank'];
    else
        return $data['data'];
}

function getInterfaceUrl($urlConfig, $data = null)
{
    $base = C('interface_base');
    $u = C('interface_url.' . $urlConfig);
    if (!$u)
        error("未找到相应的接口URL配置文件【{$urlConfig}】");

    $u = parseIString($u);
    return $base . $u;
}

function getToken()
{
    $base = C('interface_base');
    $token_url = C("INTERFACE_URL.token");
    $token = json_decode(file_get_contents($base . $token_url), true);
    if ($token['status'] !== 1) {
        error("请联系系统管理员 ， 获取token错误，{$token_url}");
    }
    return $token['token'];
}

function getChannelIdSelect()
{

}

/**
 * @tip 余小号特殊限制：只能看到8000010 / 9999999
 * @return null
 */
function getChannelId()
{
    static $data;
    if ($data === null) {
        $db = getDb(TABLE_CHANNEL_MST);
        if(getAdminName()=="yuxiaohao") {
            $_where['channel_id'] = array('IN',array(8000010,9999999));
            $arr = $db->field("sitename,channel_id")->where($_where)->order("channel_id")->select();
            foreach ($arr as $item){
                $data[intval($item['channel_id']) . ' - ' . $item['sitename']] = $item['channel_id'];
            }
            return $data;
        }
        $arr = $db->field("sitename,channel_id")->order("channel_id")->select();
        foreach ($arr as $k => $v)
            $data[intval($v['channel_id']) . ' - ' . $v['sitename']] = $v['channel_id'];
    }
    return $data;
}

function getAllChannelId()
{
    static $data;
    if ($data === null) {
        $data["全渠道"] = "全渠道";
        $db = getDb(TABLE_CHANNEL_MST);
        $arr = $db->field("sitename,channel_id")->order("channel_id")->select();
        foreach ($arr as $k => $v)
            $data[intval($v['channel_id']) . ' - ' . $v['sitename']] = $v['channel_id'];
    }
    return $data;
}

function parseChannelId($channelId)
{
    $model = '';
    if (isChannelIdAllSelectedModel($channelId))        return "<span style=\"color:green\">全部渠道</span>";
    if (isChannelIdExceptModel($channelId)) {
        $model = '<span style="color:red">排除模式:</span>';
        $channelId = substr($channelId, 3);

    }
    $delimiter = '|';
    if(strpos($channelId,',') !== false) $delimiter = ',';

    $channelIdArr = explode($delimiter, $channelId);
    $channelIds = array_flip(getChannelId());

    $data = "";
    foreach ($channelIdArr as $v) {
        if (strlen($v) != 7)
            $v = str_pad($v, 7, '0', STR_PAD_LEFT);
        if (isset($channelIds[$v]))
            $data .= "【" . $channelIds[$v] . "】,";
        else
            $data .= "【" . "<span style=\"color:red\">渠道未启用[$v]</span>" . "】,";
    }
    if ($data)      $data = substr($data, 0, -1);

    return $model . $data;
}


function parseAppId($appId,$split=null)
{
    $model = '';
    if (isAppIdAllSelectedModel($appId))
        return "<span style=\"color:green\">全部游戏</span>";
    if (isAppIdExceptModel($appId)) {
        $model = '<span style="color:red">排除模式:</span><br />';
        $appId = substr($appId, 3);

    }
    $appIdArr = explode('|', $appId);
    if($split == "COMMA") $appIdArr =  explode(C($split), $appId);
    if($split == "GMG_LT_AD") $appIdArr = explode(',',$appId);
    $appIds = array_flip(getAllowAppAsData());

    $data = "";
    foreach ($appIdArr as $v) {

        if (isset($appIds[$v]))
            $data .= "【" . $appIds[$v] . "】,<br />";
        else
            $data .= "【" . "<span style=\"color:red\">游戏未启用[$v]</span>" . "】,";
    }
    if ($data)
        $data = substr($data, 0, -1);
    return $model . $data;
}


function newParseChannelId($channelId)
{
    $channelIds = array_flip(getChannelId());
    $ids = trim($channelId, ',');
    $keys = explode(',', $ids);
    for ($i = 0; $i < count($keys); $i++) {
        $data[] = '【' . $channelIds[$keys[$i]] . '】';
    }
    $data = implode(',', $data);
    return $data;
}

//是否渠道是排除模式
function isChannelIdExceptModel($channelId)
{
    return strpos($channelId, 'no|') === 0;
}

//是否渠道是全选模式
function isChannelIdAllSelectedModel($channelId)
{
    return strpos($channelId, 'all') === 0;
}

//是否游戏是排除模式
function isAppIdExceptModel($appId)
{
    return strpos($appId, 'no|') === 0;
}

//是否游戏是全选模式
function isAppIdAllSelectedModel($appId)
{
    return strpos($appId, 'all') === 0;
}

function jsonEncode($data)
{
    if (is_array($data))
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    return $data;
}

function jsonDecode($str)
{
    $json = json_decode($str, true);
    return json_last_error() == JSON_ERROR_NONE ? $json : $str;
}


function parseIString($str, $data = array())
{
    if (!$_POST['date'])
        $_REQUEST['date'] = 'all';
    else
        $_REQUEST['date'] = parseToSimpleDate($_REQUEST['date']);

    if (preg_match_all('/\$(.+)\$/sU', $str, $match)) {
        foreach ($match[1] as $v) {
            $str = str_replace('$' . $v . '$', $v(), $str);
        }
    }

    if (preg_match_all('/\@(.+)\@/sU', $str, $matched)) {
        foreach ($matched[1] as $v) {
            $str = str_replace('@' . $v . '@', !empty($data[$v]) ? $data[$v] : $_REQUEST[$v], $str);
        }
    }
    return $str;
}

function arrayToStrTrimShuhao($data)
{
    return arrayToStrTrim($data, '|');
}

function simpleUl($data, $withKey = false)
{
    if (is_string($data))
        return $data;

    $re = '<ul>';
    $i = 0;
    $temp = end($data);
    if (!is_array($temp)) //1维数组
    {
        $re = '<li>';
        foreach ($data as $k => $v) {
            $re .= "{$k}=>{$v},";
        }
        $re .= '</li>';
        return $re . '</ul>';
    }
    foreach ($data as $k => $v) //2维及以上
    {
        if ($withKey && $i !== $k) {
            if (!is_array($v))
                $re .= "<li>{$k}=>{$v}</li>";
            else
                $re .= "<li>{$k}=>" . json_encode($v) . "</li>";
        } else {
            if (!is_array($v))
                $re .= "<li>{$v}</li>";
            else
                $re .= "<li>" . json_encode($v) . "</li>";
        }
        $i++;
    }
    return $re . "</ul>";
}

/**
 * 讲绝对路径转换为url地址
 */
function pathToUrl($path)
{
    $baseDir = C('BASE_DIR');
    $baseUrl = C('BASE_URL');
    return str_replace($baseDir, $baseUrl, $path);
}


function getAllowActivity()
{
    $db = getDb(TABLE_GAME_ACTIVITY);
    $activitys = $db->field('id,title')->where('app_id in (' . getAllowAppAsSql() . ')')->order('id desc')->select();
    foreach ($activitys as $k => $v) {
        $re[$v['id'] . "-" . $v['title']] = $v['id'];
    }
    return $re;
}


function getAllowActivityInType()
{
    $args = func_get_args();
    $db = getDb(TABLE_GAME_ACTIVITY);
    $activitys = $db->field('id,type,title')->where('app_id in (' . getAllowAppAsSql() . ') and type in(' . implode(',', $args) . ')')->order('id desc')->select();
    foreach ($activitys as $k => $v) {
        $re[$v['id'] . '-' . $v['title']] = $v['id'];
    }
    return $re;
}

function getAllowActivityHasJoinLog()
{
    $db = getDb(TABLE_GAME_ACTIVITY);
    $allow = C('GAME_ACTIVITY_TYPE_JOIN_LOG');
    $activitys = $db->field('id,type,title')->where('app_id in (' . getAllowAppAsSql() . ')')->order('id desc')->select();
    foreach ($activitys as $k => $v) {
        if (in_array($v['type'], $allow))
            $re[$v['id'] . ' - ' . $v['title']] = $v['id'];
    }
    return $re;
}

/**
 * @return array
 */
function getAllowActivityHasRewardLog()
{
    $db = getDb(TABLE_GAME_ACTIVITY);
    $allow = C('GAME_ACTIVITY_TYPE_REWARD_LOG');
    $activitys = $db->field('id,type,title')->where('app_id in (' . getAllowAppAsSql() . ')')->order('id desc')->select();

    $result = array();
    foreach ($activitys as $k => $v) {
        if (in_array($v['type'], $allow))
            $result[$v['id'] . ' - ' . $v['title']] = $v['id'];
    }
    return $result;
}

function getAllowActivityHasRank()
{
    $db = getDb(TABLE_GAME_ACTIVITY);
    $allow = C('GAME_ACTIVITY_TYPE_RANK_LOG');
    $activitys = $db->field('id,type,title')->where('app_id in (' . getAllowAppAsSql() . ')')->order('id desc')->select();
    foreach ($activitys as $k => $v) {
        if (in_array($v['type'], $allow))
            $re[$v['id'] . ' - ' . $v['title']] = $v['id'];
    }
    return $re;
}

function parseToSimpleDate($str)
{
    return date('Ymd', strtotime($str));
}

function getRankActivityKey($id = null)
{
    if ($id)
        $activityId = $id;
    else
        $activityId = $_REQUEST['activity_id'];
    $data = getActivityById($activityId);
    $ext = jsonDecode($data['ext']);
    $freshByDay = false;
    if (isset($ext['rankType']) && $ext['rankType'] && $ext['rankType'] == '2') //每日刷新
        $freshByDay = true;
    if ($freshByDay) {
        if (empty($_REQUEST['date']))
            error("该活动榜单类型为“每日榜单”，请选择榜单日期");
        $date = str_replace("-", "", $_REQUEST['date']);
        return "GameScoreCpRankList_data{$activityId}_{$date}";
    }

    return "GameScoreCpRankList_data{$activityId}";
}

function getActivityById($id)
{
    $db = getDb(TABLE_GAME_ACTIVITY);
    $data = $db->find($id);
    return $data;
}

function getActivityNameById($id)
{
    if (intval($id))
        return getActivityById($id)['title'];
    return getActivityNameById($_REQUEST[$id]);
}

function parseVerifyCodeByData($data, $all, $appId = null)
{
    if (empty($appId)) {
        $appId = $all['app_id'];
        if (empty($appId))
            return red("解析错误");
    }

    $string = arrayToStrWrapper(ComponentController::parseVerifyCodeDataToString($appId, $data));
    return "{$string}<br />原始编码： {$data}";
}

function getFieldFromJson($data, $field, $getField)
{
    $data = jsonDecode($data[$field]);
    return $data[$getField];
}

function getHourSelect()
{
    $re = array();
    for ($i = 0; $i < 3600 * 24; $i += 3600) {
        $temp = date("H:i", $i);
        $re[$temp] = $temp;
    }
    return $re;
}

function autoButton($id, $type)
{
    $btn = "<input type='button' value='一键发奖' onclick='location.href=\"index.php?m=Home&c=GameInitExt&a=autorewardpre&id=$id\"'>";
    if ($type == GAME_ACTIVITY_TYPE_RANK || $type == GAME_ACTIVITY_TYPE_DAJIANGSAI)
        return $id . $btn;
    else
        return $id;
}

function parseResourceId($resourceId, $app_id)
{
    $resourceIdArr = explode('|', $resourceId);
    $db = getDb(TABLE_RESOURCE_NAME);
    $arr = $db->field("resource_id,name")->where("app_id='{$app_id}'")->select();
    foreach ($arr as $k => $v)
        $resourceIds[intval($v['resource_id'])] = intval($v['resource_id']) . ' - ' . $v['name'];

    $data = "";
    foreach ($resourceIdArr as $v) {
        if (isset($resourceIds[$v]))
            $data .= "【" . $resourceIds[$v] . "】,";
    }
    if ($data)
        $data = substr($data, 0, -1);
    return $data;
}