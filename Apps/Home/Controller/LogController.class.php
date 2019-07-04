<?php
//日志表
namespace Home\Controller;
class LogController extends BaseController
{
    public static function i($info, $tag = 0)
    {
        self::systemLog($info, $tag, 1);
    }

    public static function e($info, $tag = 0)
    {
        self::systemLog($info, $tag, 0);
    }

    /*
     * $tag 标签  C("LOG_TAG")  默认：0,系统错误
     * $success  1：成功信息  2：错误信息
     * $info string 信息内容
     */

    private static function systemLog($info, $tag, $success)
    {
        $s['info'] = $info;
        $s['runtime'] = getRuntime();
        $s['uid'] = getAdminId() ? getAdminId() : 0;
        $s['module'] = MODULE_NAME;
        $s['action'] = ACTION_NAME;
        $s['tag'] = getKeyByValue('LOG_TAG', (string)$tag);
        $s['success'] = $success;
        $s['create_time'] = time();
        $s['ip'] = get_client_ip();
        $db = getDb(TABLE_SYSTEM_LOG);
        $db->add($s);
    }

    static function getAllTag()
    {
        $db = getDb(TABLE_SYSTEM_LOG);
        $data = $db->field("tag")->group("tag")->select();
        $re=array();
        foreach ($data as $v) {
            if (trim($v['tag']) !== '')
                $re[$v['tag']] = $v['tag'];
        }
        return $re;
    }


}

?>