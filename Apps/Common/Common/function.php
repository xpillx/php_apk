<?php


/**
 *  合并多个array或string，返回合并后的数组
 *  merge('aaa',array('bbb'))  return array('aaa','bbb');
 */
function merge()
{
    foreach (func_get_args() as $k => $arg) {
        if (is_array($arg))
            $arr[$k] = $arg;
        else if ($arg !== '' && $arg !== null)
            $arr[$k] = array($arg);
        else
            $arr[$k] = array();
    }

    $re = array();
    foreach ($arr as $v) {
        $re = array_merge($re, $v);
    }

    return $re;
}

function isJson($string)
{
    if (substr($string, 0, 1) !== "{" && substr($string, 0, 1) !== "[") {
        return false;
    }
    return is_null(json_decode($string)) ? false : true;
}


function getRuntime($type = 1) //type=1 返回毫秒   type=其他 ，返回微妙
{
    $now = microtime(true);
    $runtime = $now - $GLOBALS['_beginTime'];
    if ($type === 1)
        return round(($runtime * 1000) , 2) . "ms";
    return round($runtime * 1000 * 1000 , 2)."微秒";
}

/**
 * @param null $module  模块名称
 * @param null $action  动作名称，null就是index  （action=index会自动省略）
 * @param null $param   参数  如：tab=sql    特殊：this&full=true,此时会获取当前的页面的param，并且与this&后面的合并
 * @param null $anchor  锚链接  如：cate1   return: ***#cate1
 * @return string
 */
function curl($module = null , $action = null , $param = null , $anchor = null)
{
    if($module === "void" || $module === "")
        return 'javascript:void(0);';

    //if($module === null && $action === null && $param === null && $anchor === null)
    return U("$module/$action#$anchor" , $param , false );
}

?>