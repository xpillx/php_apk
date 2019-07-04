<?php
namespace Home\Controller;

use Think\controller;

class AjaxController extends Controller
{

    function __construct()
    {
        parent::__construct();
        header("Content-type:text/html;charset=utf-8");
        $login = R('Login/isLogin', array(false));
        if (!$login)
            $this->ajaxFailed("登录失败！", '用户未登录');

        if (!IS_AJAX)
            $this->ajaxFailed("非ajax请求！", '非ajax请求');


    }

    function index()
    {
        $table = isset($_REQUEST['table']) ? $_REQUEST['table'] : '';
        if (!$table)
            $this->ajaxFailed("请求参数错误", "参数不包含table");

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
        if (!$id)
            $this->ajaxFailed("请求参数错误", "参数不包含id");

        $key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
        if (!$key)
            $this->ajaxFailed("请求参数错误", "参数不包含key");

        $value = isset($_REQUEST['value']) ? $_REQUEST['value'] : '';
        if ($value === '')
            $this->ajaxFailed("请求参数错误", "参数不包含value");

        if($_GET['key'] == 'lock' && empty($_REQUEST['value'] ) && !isSuperAdmin())
            $this->ajaxFailed("锁定的内容只有超级管理员才能解锁!");


        $db = getDb($table);
        $source = C($table.'.source');

        //$role = R('Role/checkRole',array('control',$table,$id));
        //		if($role === false)
        //			$this->ajaxFailed("没有操作权限", "用户没有权限");
        if(!$source)
        {
            $data = $db->where("id={$id}")->find();
            if (!$data) {
                LogController::e($db->getLastSql());
                $this->ajaxFailed("数据库操作错误", $db->getLastSql());
            }


            $s[$key] = urldecode( $value );
            $update = $db->where("id=$id")->save($s);
            if (!$update)
                $this->ajaxFailed("数据库操作错误", $db->getLastSql());

            $this->ajaxSuccess($db->getLastSql());
        }
        else//针对数据源为Redis
        {
            $data = TableController::parseFunc($source['func']);
            foreach($data as $k=>$v)
                if($v['id'] == $id)
                    $edata = $data[$k];
            $edata[$key] = $value;
            $result = RedisController::subConf($edata, true);
            if(!$result)
                $this->ajaxFailed('操作失败');
            $this->ajaxSuccess('操作成功');
        }
    }


    private function ajaxFailed($client_info, $error_log = '')
    {
        $json['status'] = 0;
        $json['info'] = $client_info;
        $json['data'] = null;

        if ($_REQUEST['table'] != TABLE_SYSTEM_LOG)
            LogController::e($client_info . ':' . $error_log, 2);

        echo json_encode($json);
        exit;
    }

    protected function ajaxSuccess($sql)
    {
        $json['status'] = 1;
        $json['info'] = '操作成功';
        $json['data'] = null;

        if ($_REQUEST['table'] != TABLE_SYSTEM_LOG)
            LogController::i($sql, 2);

        echo json_encode($json);
        exit;
    }
}

?>