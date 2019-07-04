<?php
namespace Home\Controller;

class RoleController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        R('Login/isLogin');
        $common = new CommonController();

        if($common->checkUserPwd())
        {
            R('Login/password');
            exit;
        }
    }

    function role()
    {
        return true;
    }
}
?>