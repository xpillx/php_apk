<?php
/**
 *  函数库
 */
require_once('data.php');

function getDb($table)
{
    list($db, $tableName) = explode(':', $table);
    $connect_db = C('db_' . $db);
    if ($connect_db) {
        $db = M($tableName, "", $connect_db);
    } else
        $db = M($table, "", C('DB_' . DB));
    return $db;
}

function getAdminName()
{
    return session(\Home\Controller\LoginController::ADMIN_NAME);
}


function getAdminId()
{
    return session(\Home\Controller\LoginController::ADMIN_ID);
}

function getAdminCname()
{
    return session(\Home\Controller\LoginController::ADMIN_CNAME);
}


function getAdminRole()
{
    return session(\Home\Controller\LoginController::ADMIN_POWER);
}

function getAdminAllowApp()
{
    if (isSuperAdmin())
        return array_values(getApp());
    $allow = session(\Home\Controller\LoginController::ADMIN_ALLOW_APP);
    if (in_array('*', $allow))
        return array_values(getApp());
    return session(\Home\Controller\LoginController::ADMIN_ALLOW_APP);
}

function hasRole($role)
{
    if (isSuperAdmin())       return true;
    $role = strToArray($role);
    foreach ($role as $v) {
        if (in_array($v, getAdminRole()))
            return true;
    }
    return false;
}

function hasAppRole($app)
{
    if (isSuperAdmin())
        return true;
    return in_array($app, getAdminAllowApp());
}

//除去传入的app，还有管理其他app的权限
function hasAppRoleExcept($app)
{
    if (isSuperAdmin())
        return true;
    $roles = getAdminAllowApp();
    foreach ($roles as $k => $v)
        if ($v == $app)
            unset($roles[$k]);

    return count($roles) > 0;
}


function isSuperAdmin()
{
    return in_array(POWER_SUPER, getAdminRole());
}

function getAllowAppAsSql()
{
    return implode(',', getAdminAllowApp());
}

function getAllowAppAsData()
{
    $allow = getAdminAllowApp();
    $apps = getApp();
    foreach ($apps as $k => $v) {
        if (!in_array($v, $allow))
            unset($apps[$k]);
    }
    return $apps;
}
function getGameTypeAsData()
{
    $allow = getAdminAllowApp();
    $apps = getApp();
    foreach ($apps as $k => $v) {
        if (!in_array($v, $allow))
            unset($apps[$k]);
    }
    return $apps;
}


function arrayToStrWrapper($arr, $left = '【', $right = '】', $split = ',')
{
    $re = '';
    foreach ($arr as $v) {
        $re .= $left . $v . $right . $split;
    }
    return substr($re, 0, -1);
}


function red($str)
{
    return "<span style=\"color:red\">{$str}</span>";
}


/**
 * 读取 USER_POWER_2018 ， 合并为 一维数组
 */
function getAllPowers()
{
    static $re;
    if ($re === null) {
        $powers = C('USER_POWER_2018');
        foreach ($powers as $v) {
            foreach ($v as $k2 => $v2) {
                $re[$k2] = $v2;
            }
        }
        debug($re, "所有权限列表");
    }
    return $re;
}

/**
 * 通过值获取配置文件的键
 * @param string $conf ：配置文件名称
 * @param string $value ：配置文件的值
 */
function getKeyByValue($conf, $value)
{
    $c = C($conf);
    foreach ($c as $k => $v) {
        if ($v == $value)
            return $k;
    }
    return '';
}


function localDatetime($time)
{
    return date('Y-m-d H:i', $time);
}



function friendSecond($second = 0)
{
    if (!$second)
        return '';

    if ($second < 1000)
        return "{$second} 毫秒";

    $s = round($second / 1000, 2);
    if ($s > 60) {
        $s = intval($s);
        $m = intval($s / 60);
        return $m . "分" . ($s % 60) . "秒";
    }
    return $s . "秒";

}

function getWeekIndex()
{
    return date('Y-W');
}

function getWeekIndex2($date)
{
    return date('Y-W', strtotime($date));
}


function getWeekStart($yearweek)
{
    list($year, $week) = explode("-", $yearweek);
    $last_year = strtotime(($year - 1) . '-12-31');
    $last_date_lase_year_in_week = date('N', $last_year);
    $days = ($week - 1) * 7 + 1 - $last_date_lase_year_in_week;
    return strtotime("+$days days", $last_year);
}

function getWeekDate($yearweek, $date = true)
{
    list($year, $week) = explode("-", $yearweek);
    $last_year = strtotime(($year - 1) . '-12-31');
    if (!$date)
        return "{$year}年第{$week}周";
    $last_date_lase_year_in_week = date('N', $last_year);
    $days = ($week - 1) * 7 + 1 - $last_date_lase_year_in_week;
    $the_day = strtotime("+$days days", $last_year);
    $s = date('m/d', $the_day);

    $e = date('m/d', strtotime("+6 days", $the_day));

    return "{$year}年第{$week}周 (" . $s . "—" . $e . ")";
}

function getWeekDate2($yearweek)
{
    list($year, $week) = explode("-", $yearweek);
    $last_year = strtotime(($year - 1) . '-12-31');
    $last_date_lase_year_in_week = date('N', $last_year);
    $days = ($week - 1) * 7 + 1 - $last_date_lase_year_in_week;
    $the_day = strtotime("+$days days", $last_year);
    $s = date('m/d', $the_day);
    return "第{$week}周 (" . $s . ")";
}


function getLastWeekIndex()
{
    $time = time() - 7 * 24 * 3600;
    return date('Y-W', $time);
}


function mySubstr($str, $len)
{
    for ($i = 0; $i < $len; $i++) {
        $temp_str = substr($str, 0, 1);
        if (ord($temp_str) > 127) {
            $i++;
            if ($i < $len) {
                $new_str[] = substr($str, 0, 3);
                $str = substr($str, 3);
            }
        } else {
            $new_str[] = substr($str, 0, 1);
            $str = substr($str, 1);
        }
    }
    if (strlen($str) > 0)
        return implode($new_str) . "...";

    return implode($new_str);
}

function unhtml($text)
{
    $text = strip_tags($text);
    return $text;
}

function debug($data, $str = null)
{
    if (APP_DEBUG === true && isset($_GET['debug'])) {
        $info = debug_backtrace();
        $file = $info[0]['file'];
        $line = $info[0]['line'];
        //echo $file . " : ".$line;
        if ($str === null)
            dump($data);
        else {
            echo("<div style=\"background-color:#eee;border:solid 1px #ddd;border-radius:5px;margin-bottom:5px\"><h1>" . $str . "</h1>");
            dump($data);
            echo("</div>");
        }
    }
}

/**
 * 多维数组转一位数组
 * @param $arr
 * @param null $split
 * @return string
 */
function mutilArrayToArray($arr, $key)
{
    $re = array();
    foreach ($arr as $v) {
        $re[] = $v[$key];
    }
    return $re;

}

function mutilSort(&$array, $sortKey, $asc = true)
{
    foreach ($array as $k => $v) {
        $sort[$k] = $v[$sortKey];
    }
    array_multisort($sort, SORT_NUMERIC, $asc ? SORT_ASC : SORT_DESC, $array);
    return $array;
}

function arrayToStr($arr, $split = null)
{

    if ($split === null)
        $split = C('DB_SPLIT') === null ? ',' : C('DB_SPLIT');
    if (!is_array($arr)) {
        return '';
    }

    foreach ($arr as $k => $v) {
        $arr[$k] = trim($v);
    }
    $arr = array_unique($arr);
    $str = implode($split, $arr);
    if ($str !== '')
        $str = $split . $str . $split;
    return $str;
}

function arrayToStrTrim($arr, $split = null)
{
    if ($split === null)
        $split = C('DB_SPLIT') === null ? ',' : C('DB_SPLIT');
    return trim(arrayToStr($arr, $split), $split);
}

function mdate($time = NULL)
{
    $text = '';
    $date = date('Y-m-d H:i', $time);
    $text = friendDate($time);
    return "<span title=\"{$date}\">$text</span>";
}


function friendDate($time = null)
{
    $text = '';
    $date = date('Y-m-d H:i', $time);
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $t2 = strtotime('today') - $time;
    if ($t == 0)
        $text = '1秒前';
    elseif ($t < 60)
        $text = $t . '秒前'; // 一分钟内
    elseif ($t < 60 * 60)
        $text = floor($t / 60) . '分钟前'; //一小时内
    elseif ($t < 60 * 60 * 24)
        $text = floor($t / (60 * 60)) . '小时前'; // 一天内
    elseif ($t < 60 * 60 * 24 * 3)
        $text = floor($t2 / (60 * 60 * 24)) == 0 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
    elseif ($t < 60 * 60 * 24 * 30)
        $text = date('m月d日 H:i', $time); //一个月内
    else {
        $this_year = strtotime(date('Y') . "-01-01");
        if ($time >= $this_year)
            $text = date('m月d日', $time); //一年内
        else
            $text = date('Y年m月d日', $time); //一年以前
    }
    return $text;
}


/**
 * @Tip：将字符串处理成数组，如：',super,' 、 '|super|'...
 * @param string $str
 * @param null $split
 * @return array
 */
function strToArray($str, $split = null)
{
    $str = (string)$str;
    if (!is_string($str))
        return array();
    if ($split === null)    $split = C('DB_SPLIT') === null ? ',' : C('DB_SPLIT');
    $str = trim($str, $split);
    $arr = explode($split, $str);

    $re = array();
    foreach ($arr as $k => $v) {
        if (trim($v) !== '')
            $re[] = trim($v);
    }
    return $re;
}


function latelyView($name = null)
{
    $session_key = C('LATELY_VIEW_KEY') ? C('LATELY_VIEW_KEY') : 'lastly_view';
    $lately = session($session_key);
    if ($lately) $lately = unserialize($lately);
    if (!$name)
        return $lately;

    $max = 21;
// 	$max = 5;
    $url = $_SERVER['REQUEST_URI'];
    $exist = -1;
    foreach ($lately as $k => $v) {
        if ($v['link'] === $url) {
            $exist = $k;
            break;
        }
    }
    if ($exist !== -1)
        unset($lately[$exist]);

    $item = array('name' => $name, 'link' => $url, 'time' => time());
    if ($lately)
        array_unshift($lately, $item);
    else
        $lately[] = $item;

    if (count($lately) > $max)
        array_pop($lately);

    session($session_key, serialize($lately));
}


/**
 * 从数据库读取所有数据，作为select、checkbox、radio 的 data
 */
function getDataFromDb($table, $key_field, $val_field, $where = null, $url_where = true)
{
    $table = getDb($table);
    if ($where != null) {
        if ($url_where)
            $where = \Home\Controller\TableController::parseUrlWhere($where);
        $data = $table->field(array($key_field, $val_field))->where($where)->select();
    } else {
        $data = $table->field(array($key_field, $val_field))->select();
    }
    foreach ($data as $k => $v) {
        $re[$v[$key_field]] = $v[$val_field];
    }
    return $re;
}


/**
 * 从数组库获取数据并分组，返回数据作为select、checkbox、radio 的 data
 */
function getFieldGroup($table, $field)
{
    $table = getDb($table);
    $data = $table->field($field)->group($field)->select();
    foreach ($data as $v) {
        $re[$v[$field]] = $v[$field];
    }
    return $re;
}

function parseArg($str = '')
{
    $args = func_get_args();
    array_shift($args); //移除第一个参数
    if (count($args) > 0) {
        while (($index = strpos($str, '{?}')) !== false) {
            $str = substr_replace($str, array_shift($args), $index, 3);
        }
    }
    return $str;
}

function byteConvert($bytes)
{
    $s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
    $e = floor(log($bytes) / log(1024));

    return sprintf('%.2f ' . $s[$e], ($bytes / pow(1024, floor($e))));
}

function microtimeConvert($microtime)
{
    return round($microtime / 1000, 2) . "秒";
}

function success($mes = "操作成功", $url = null, $ajax = null, $wait = '')
{
    $controller = A('Home/Base');
    $controller->success($mes, $url, $ajax, $wait);
    exit;
}

function error($mes = null, $url = null, $ajax = null, $wait = '')
{
    $controller = A('Home/Base');
    $controller->error($mes, $url, $ajax, $wait);
    exit;
}

function endWith($str, $char = '/')
{
    $str = rtrim($str, $char);
    return $str . $char;
}

function getExpireTime($expire)
{
    $exp = isset($_REQUEST[$expire]) ? intval($_REQUEST[$expire]) : 0;
    return time() + $exp;

}

/**
 * @Tip: 获取权限的单选框
 * @param string $field 字段名 如：`power`
 * @param string $data  字段中的内容 如：`,super,`
 * @param int $super 是不是超级管理员
 * @return string
 */
function getPowerCheckbox($field, $data, $super = 0)
{

    if ($super && !isSuperAdmin())    return '';
    $power = C('USER_POWER_2018');
    $html = new \Home\Org\Html();
    $re = '';
    foreach ($power as $k => $v) {
        $re .= "<B>{$k}</B>";
        $re .= $html->createInput('checkbox', $field, $data, $v); //data power ,super, checkbox的value值
    }
    return $re;
}


/**
 * 将权限转换为文本
 */
function getPowerString($power)
{
    $p = strToArray($power);
    $powers = array_flip(getAllPowers());

    $re = array();
    foreach ($p as $v) {
        $re[] = $powers[$v] ? $powers[$v] : $v;
    }
    return arrayToStrTrim($re, " | ");
}

function getPower($field)
{
    $power = C('USER_POWER_2018');
    static $result = null;
    if ($result == null) {
        foreach ($power as $v) {
            foreach ($v as $key => $value) {
                $result[$value] = $key;
            }
        }
    }
    $data = explode(",", $field);
    $str = '';
    foreach ($data as $t) {
        if (isset($result[$t]))
            $str .= '【' . $result[$t] . "】,";
    }
    return substr($str, 0, -1);
}


function getApp()
{
    static $cache;
    if ($cache == null) {
        $db = getDb(TABLE_GAME);
        $data = $db->select();
        foreach ($data as $v) {
            $cache['[' . $v['id'] . '] ' . $v['name']] = $v['id'];
        }
    }
    return $cache;
}

function getAppPackage()
{
    static $cache;
    if ($cache == null) {
        $db = getDb(TABLE_GAME);
        $data = $db->select();
        foreach ($data as $v) {
            $cache[$v['id']] = $v['package'];
        }
    }
    return $cache;
}

function getGameTag()
{
    static $cache;
    if ($cache == null) {
        $db = getDb(TABLE_GAME_TAG);
        $data = $db->select();
        foreach ($data as $v) {
            $cache[$v['name']] = $v['name'];
        }
    }
    return $cache;
}

function getGameType()
{
    static $cache;
    if ($cache == null) {
        $db = getDb(TABLE_GAME_TYPE);
        $data = $db->select();
        foreach ($data as $v) {
            $cache[$v['name']] = $v['name'];
        }
    }
    return $cache;
}

function getAppWithAll()
{
    $all = array('所有游戏' => '*');
    return merge($all, getApp());

}

function getAppNameById($appid)
{
    $apps = array_flip(getApp());
    return isset($apps[$appid]) ? $apps[$appid] : red('未定义[' . $appid . ']');
}


function getAppPackageById($appid)
{
    $apps = getAppPackage();
    return isset($apps[$appid]) ? $apps[$appid] : red('未定义[' . $appid . ']');
}

function getGameTagById($id)
{
    $tags = array_flip(getGameTag());
    return isset($tags[$id]) ? $tags[$id] : $id;
}

function getAppString($str)
{
    $apps = array_flip(getApp());
    $re = array();
    if (in_array('*', strToArray($str)))
        return "<span style=\"color:red\">所有游戏</span>";
    foreach (strToArray($str) as $v) {
        if (isset($apps[$v]))
            $re[] = $apps[$v];
        else
            $re[] = red('未定义(' . $v . ')');
    }
    return arrayToStrTrim($re);
}

function getTableKeyValue($table, $key, $value, $def)
{
    static $data = null;
    $tempkey = $table . $key . $value;
    if ($data[$tempkey] === null) {
        $db = getDb($table);
        $datas = $db->field("$key,$value")->select();
        $data[$tempkey] = array();
        foreach ($datas as $v) {
            $data[$tempkey][$v[$key]] = $v[$value];
        }
    }
    debug($data[$tempkey], "{$table}的数据");
    return isset($data[$tempkey][$def]) ? $data[$tempkey][$def] : "无";
}


function getAjaxtextUrl($p, $p2, $key)
{
    $url = U($p, $p2);
    if (strpos($url, '?') !== false)
        return $url . "&{$key}=";
    return $url . "?{$key}=";
}


function parseScore($score)
{
    if ($score <= 0)
        return '未评分';
    $num = round($score / 10);
    return '<span class="score' . $num . '"></span><span class="scoretext">' . $score . '分</span>';
}

function n2br($str)
{
    return str_replace("\r\n", "<br />", $str);
}

function br2n($str)
{
    return str_replace("<br />", "\r\n", $str);
}

function passwd($str)
{
    if (strlen($str) == 32)        return $str;
    return md5($str);
}

//每分钟生成一个加密字符串 合并为参数与$url合并
function encrptLogin($url, $miwen, $miyao = 'xiaot2013', $expire = 60)
{
    $data = base64_encode(\Think\Crypt\Driver\Des::encrypt($miwen, $miyao, $expire));
    if (strstr($url, '?'))
        return $url . '&verify=' . $data;
    return $url . "?verify=" . $data;
}

function decryptLogin($miwen, $miyao = 'xiaot2013')
{
    return \Think\Crypt\Driver\Des::decrypt(base64_decode($miwen), $miyao);
}


function getYestoday()
{
    return date('Y-m-d', time() - 86400);
}

function getToday()
{
    return date('Y-m-d');
}


function parseUnixTime($time)
{
    if (!$time)
        return "";
    return date("Y-m-d H:i:s", $time);
}

function emptyString($str)
{
    if (!$str)
        return "";
    return $str;
}

function randomString($length,$type)
{
    if ($length > 0) {
        $str = '23456789abcdefghijkmnpqrstuvwxyz';
        $tmp = '';
        for ($i = 0; $i < $length; $i++) {
            $r = rand(0, strlen($str) - 1);
            $tmp .= $str[$r];
        }
        if($type==2) $tmp = substr(strval(microtime(true)*10000),-8);
        return $tmp;
    }
}


function jsonDecodeAndToStr($str)
{
    $data = jsonDecode($str);
    return arrayToStrTrim($data, '|');
}

function explodeAndJsonEncode($str)
{
    $data = explode('|', $str);
    return jsonEncode($data);
}

function getImageUrl($table, $name)
{
    return C('BASE_URL') . C('img.' . $table) . $name;
}

function getAppsArray()
{
    $apps = getApp();
    $re = array();
    $i = 0;
    foreach ($apps as $k => $v) {
        $re[$i]['gid'] = $v;
        $re[$i++]['name'] = $k;
    }
    return $re;
}


function parseExcelData2Json($dataStr)
{
    $dataStr = trim($dataStr);
    if (empty($dataStr))
        return '';
    if (substr($dataStr, 0, 1) == '{' || substr($dataStr, 0, 1) == ']')
        return $dataStr;
    $data = explode("\n", $dataStr);
    foreach ($data as $k => $v) {
        $list[$k] = explode("\t", trim($v));
    }
    $re['field'] = array_shift($list);
    $re['data'] = $list;

    return json_encode($re);
}



function parseYN($val)
{
    $conf = C('YESORNO');
    $conf = array_flip($conf);
    return $conf[intval($val)];
}

function endsWith($str, $needle, $len)
{
    $ext = substr($str, -$len);
    return $ext === $needle;
}


function getLoginNameById($id)
{
    if (intval($id) <= 0)
        return '';
    static $users = null;
    if ($users === null) {
        $db = getDb(TABLE_ACCOUNT);
        $list = $db->field('id,login')->select();
        foreach ($list as $k => $v)
            $users[$v['id']] = $v['login'];
    }

    return isset($users[$id]) ? $users[$id] : '未知';
}



