<?php
namespace Home\Controller;
/**
 * 登录表
 */
class LoginController extends BaseController
{
    const ADMIN_ID = "admin_id";
    const ADMIN_NAME = "admin_name";
    const ADMIN_CNAME = "admin_cname";
    const ADMIN_POWER = "admin_power";
    const ADMIN_ALLOW_APP = 'admin_allowapp';

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->display();
    }

    function loginIn()
    {
        $name = trim($_POST['uname']);
        $pwd = trim($_POST['pwd']);

        if (empty($name) || empty($pwd))
            $this->error('用户名或密码不得为空!');

        if (C('VERIFY_CODE')) {
            if (!isset($_POST['verify']) || !$this->veryfiCheck($_POST['verify'])) {
                $this->error('验证码错误!');
            }
        }

        $table = getDb( TABLE_ACCOUNT );
        $data = $table->where("login='" . $name . "'")->find();

        if (!$data || $data['status'] == '0') //
        {
            $this->error('用户名或密码错误!');
        }

        if ($data['status'] == '2') //锁定的用户
        {
            $lock = $this->getLockTime();
            if ($lock === 0)
                $this->error("您的账号已被永久锁定，请联系后台管理人员！");
            else {
                $wait_time = ( $data['last_time'] + $lock) - time();
                if ($wait_time > 0)
                    $this->error("您的账号已被锁定，请等待 {$wait_time}秒 后重试!");
            }
        }

        if ($data && $data['password'] == passwd($pwd) ) {
            session(self::ADMIN_NAME, $name); //登录账号
            session(self::ADMIN_CNAME, $data['name']); //公司名
            session(self::ADMIN_ID, $data['id']);
            session(self::ADMIN_POWER, strToArray($data['power']) );
            session(self::ADMIN_ALLOW_APP , strToArray($data['allow_app']));

            $this->setLastLogin($data, true);
            LogController::i("登录成功：name = {$name} ", 1);
            redirect($this->getFristNavigation());
        } else //密码错误
        {
            LogController::e("密码错误：name = {$name} ,pwd={$pwd}", 1);
            $re = $this->setLastLogin($data, false);
            if ($re === 0)
                $this->error('你的密码输入错误，账号已被锁定！');
            else
                $this->error("您的密码输入错误，还有{$re}次登陆机会！");
        }
    }

    function isLogin($redirect = true)
    {

        if (getAdminName() === null || getAdminId() === null) {
            if ($redirect) {
                $this->redirect(U('Login/index'));
            }
            return false;
        }
        return true;
    }

    /**
     * 获取系统设置的玩家登陆错误的锁定时间
     */
    private function getLockTime()
    {
        return intval(C('ADMIN_LOCK_TIME'));
    }

    /**
     * 获取系统允许连续登陆错误次数
     * @return int
     */
    private function getAllowErrorTime()
    {
        return intval(C('ADMIN_LOCK_ERROR'));
    }

    private function setLastLogin($user_data, $success = true)
    {
        $db = getDb(TABLE_ACCOUNT );
        $id = $user_data['id'];
        $login_error = intval($user_data['login_error']);
        if ($success) {
            $data = array('last_time' => time(), 'login_error' => 0, 'status' => 1);
            $db->where("id={$id}")->save($data);
            return true; //未锁定
        }

        $data = array('last_time' => time());
        $login_error++;
        $shengyu = $this->getAllowErrorTime() - $login_error; //系统允许的登录次数错误 - 当前登录错误次数
        if ($shengyu <= 0) {
            $data['login_error'] = 0;
            $data['status'] = 2;
            $db->where("id={$id}")->save($data);
            return 0; //已被锁定
        }
        $data['login_error'] = $login_error;
        $data['status'] = 1;
        $db->where("id={$id}")->save($data);
        return $shengyu; //还允许登陆错误的次数


    }

    public function password()
    {
        $this->isLogin();
        $this->assignNavigation();
        if ($_POST) {
            $old = I("old");
            $new1 = trim(I("new1"));
            $new2 = trim(I("new2"));
            if ($new1 !== $new2)       error("两次密码输入不一致");

            $table = getDb(TABLE_ACCOUNT);
            $uname = $_POST['uname'];
            if($uname)
            {
                $data = $table->where("id != ".getAdminId()." and login = '{$uname}'")->find();
                if($data)   error("已存在此用户名");
            }

            $data = $table->where("id='" . getAdminId() . "' and password='" . passwd($old) . "'")->find();


//            if(getAdminName() == "柳凌波" or getAdminName() == "bearadmin"){
//                echo "<pre>";
//                print_r($_REQUEST);
//                echo "<hr />";
//                print_r(getAdminId());
//                echo "<hr />";
//                print_r($data);
//                exit;
//            }



            if (!$data)     error("原密码输入错误");

            if($new1)   $save['password'] = passwd($new1);
            if($uname)  $save['login'] = $uname;
            $save['reset_pwd'] = time();
            $table->where("id=" . getAdminId())->save($save);

         /*   echo $table->_sql();
            exit;*/
            $this->success("修改成功,请以新密码登录", curl("Login/loginOut"));
            exit;
        }
        $con['nav'] = TableController::createNav(null, array('修改密码' => array()));
        $con['main'] = $this->fetch("Login:password");
        $this->assign("con", $con);
        $this->display("Public:main");
    }

    public function loginOut()
    {
        session(self::ADMIN_ID, null);
        session(self::ADMIN_NAME, null);
        session(self::ADMIN_CNAME, null);
        session(self::ADMIN_POWER, null);
        $this->redirect('Login/index');
    }

    public function verify()
    {
        $verify = new \Think\Verify();
        $verify->entry();

    }

    private function veryfiCheck($verifyCode)
    {
        $verify = new \Think\Verify();
        return $verify->check($verifyCode);
    }

//	private function login_log($uname,$pwd,$success,$info)
//	{
//		$db = M('log_login');
//		$s['uname'] = $uname;
//		$s['pwd'] = $pwd;
//		$s['success'] = $success?$success:0;
//		$s['info'] = $info? $info:'';
//		$s['create_time'] = time();
//		$s['ip'] = get_client_ip();
//		$db->add($s);
//	}
}

?>