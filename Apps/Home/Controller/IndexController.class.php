<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
class IndexController extends RoleController
{
    const MIWEN = 'babcccccccc123123123111';
    public function index()
    {
        exit;
        //$this->redirect($this->getFristNavigation());
        $this->assignNavigation();
        redirect($this->getFristNavigation());


        //dump( encrptLogin('aaa' , self::MIWEN ) );
        //$this->display();
    }

    public function stat()
    {
        $ass['os'] = PHP_OS;
        $ass['version'] = PHP_VERSION;
        $ass['error_log'] = ini_get("error_log");
        $ass['post_max_size'] = ini_get("post_max_size");
        $ass['upload_max_filesize'] = ini_get("upload_max_filesize");

        $ass['server'] = I('server.SERVER_SOFTWARE');
        dump($ass);
        dump($_SERVER);
    }

    function test()
    {
//        dump($_GET);
        $verify = I('verify');
        $verify = str_replace(' ' , '+',$verify);
        $data = ( decryptLogin( $verify ) );
        $data = $data ? substr($data , 0 , strlen(self::MIWEN))  : "";
        if( $data === self::MIWEN )
        {
            dump("验证成功！");
            exit;
        }

        dump("验证失败");
    }

    function getNickname()
    {
        if(!empty($_POST['uid']))
        {
            $uids = strToArray($_POST['uid'] , '|');
            $db = getDb(TABLE_USER);
            $data  = $db->field("id,nickname,reg_date,app_id")->where("id in (".arrayToStrTrim($uids,',').")")->select();

            $tempData = array();
            foreach($data as $k=>$v)
                $tempData[$v['id']] = $v;
            $assData = array();
            foreach($uids as $v)
                $assData[$v] = $tempData[$v];
            $this->assign('data',$assData);
        }
        $this->assignNavigation();
        $this->main = $this->fetch('Index:getNickname');
        $this->nav = TableController::createNav(TABLE_USER );
        $this->_out();
    }


}