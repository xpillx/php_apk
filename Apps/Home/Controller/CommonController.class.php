<?php
namespace Home\Controller;

class CommonController extends BaseController
{
    //系统管理 - 手机号码地址
    function phone()
    {
        if (!hasRole(POWER2017_SYSTEM_MANAGE_MOBILE_NUMBER_ADDRESS)) error(MSG_ERROR_POWER);
        if ($_POST) {
            import('@.Org.ReadExcel');
            $read = new \ReadExcel();
            $data = $read->read($_FILES['excel']['tmp_name'] , 'xlsx' , null , 1);
            foreach($data as $k=>$v){
                $s[$k]['phone'] = $v['A'];
                $re = $this->getPhoneAdd($v['A']);
                $s[$k]['add'] = $re['area'];
                $s[$k]['type'] = $re['type'];
            }

            import('@.Org.WriteExcel');
            $write = new \WriteExcel();
            $write->setMutilArray($s);
            $path = C('BASE_DIR') . C('IMG.data2excel');
            $write->saveAndDownload( $path );
            exit;
        }

        $this->assignNavigation();
        $this->nav = TableController::createNav( null , array('上传表格'=>array()));

        $html = new \Home\Org\Html();
        $temps['Excel'] =  $html->createInput('file','excel') . " <span>请上传excel表格，A列手机号码</span><br />";
        $temps[] = $html->createInput('submit','submit','提交');
        $temp = $html->ul($temps);
        $this->main .= $html->createForm($temp , null);
        $this->_out();
    }

    public static function getPhoneAdd($phone){
        static $data ;
        if($data  === null){
            $db = getDb(DB.':dm_mobile');
            $list = $db->field('MobileNumber,MobileType,MobileArea')->select();
            foreach($list as $v){
                $data[$v['MobileNumber']] = array('area'=>$v['MobileArea'] , 'type'=>$v['MobileType']);
            }
        }
        if(strlen($phone) > 7)
            $phone = substr($phone , 0 , 7);
        return $data[$phone];
    }

    public function checkUserLock()
    {
        $s = $f = 0;
        $db = getDb(TABLE_ACCOUNT);
        $users = $db->select();
        if($users)
        {
            $time = time();
            foreach($users as $user)
            {
                if($user['last_time'])
                {
                    $diffTime = $time - $user['last_time'];
                    if($diffTime>=(86400*90) && $user['status']==1)
                    {
                        $temp = $user;
                        $temp['status'] = 2;
                        $db->save($temp) ? $s++ : $f++;
                    }
                }
            }
        }
        echo json_encode(['status'=>'success', 'msg'=>'success lock:'.$s.', fail lock:'.$f]);
        exit;
    }

    /**
     * @tip 检测用户密码情况，当 大于或等于90天未重置密码则限制操作
     * @return bool
     */
    function checkUserPwd()
    {
        $db = getDb(TABLE_ACCOUNT);
        $uname = getAdminName();
        $data = $db->where(['login'=>$uname])->find();
        $cTime = strtotime(date('Y-m-d'));
        if($data['reset_pwd'])
        {
            $diffTime = $cTime - $data['reset_pwd'];
            if($diffTime >= (90*86400)){

                error("密码超过<span style='color: red;font-size: 25px;font-weight: bolder'>90</span>天未重置，请重置密码！");
                return true;
            }
        }
        else
        {
            $startDate = '2016-05-23';
            $diffTime = $cTime - strtotime($startDate);
            if($diffTime>0 && ($diffTime%(90*86400))==0)
                return true;
        }
        return false;
    }
}