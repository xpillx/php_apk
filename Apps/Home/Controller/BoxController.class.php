<?php
/**
 * 客户端
 * Class BoxController
 * @package Home\Controller
 */
namespace Home\Controller;

class BoxController extends BaseController
{
    const RESULT_SUCCESS = 1;     //成功
    const RESULT_FAIL = 0;  //失败


    /**
     * 盒子启动下发所有数据
     * 客户端调用接口  union_client
     * http://xxx/tvbox/getInfo
     *
     * http://xxx/index.php?m=Home&c=box&a=getInfo
     */
    public function getInfo()
    {
        //所有包数据
        $data = array();
        $db_package = getDb(TABLE_PACKAGE_LIST);
        $package_list = $db_package->join('game ON game.id=package_list.appId')->field('appId,name,appPkg,version,versionName,desc,category,tag,star,company,size,md5,downloadTimes,searchKey,searchKeyFull,downUrl,iconUrl,homeIconUrl,categoryIconUrl,screenshotUrl,videoUrl')->select();
        $data['package_list'] = $package_list;

        //首页1展示位
        $db_home_first = getDb(TABLE_GAME_HOME_SHOW_FIRST);
        $home_first = $db_home_first->field('displayIndex,appId,isPic,showIconUrl')->select();
        $data['home_first'] = $home_first;

        //首页2-10展示位
        $db_home = getDb(TABLE_GAME_HOME_SHOW);
        $home_list = $db_home->field('displayIndex,appId,showIconUrl')->select();
        $data['home_list'] = $home_list;

        //下载排行榜
        $rank_download= $db_package->field('appId')->order('downloadTimes desc')->limit(7)->select();
        $str_download=null;
        foreach ($rank_download as $key=>$val)
        {
            $str_download!=null?$str_download=$str_download.'|'.$val["appId"]:$str_download=$str_download.$val["appId"];
        }
        $data['rank']['download'] = $str_download;

        //热门新游榜
        $db_rank_hot = getDb(TABLE_GAME_RANK_HOT);
        $rank_hot= $db_rank_hot->field('appId')->order('id asc')->limit(7)->select();
        $str_hot=null;
        foreach ($rank_hot as $key=>$val)
        {
            $str_hot!=null?$str_hot=$str_hot.'|'.$val["appId"]:$str_hot=$str_hot.$val["appId"];
        }
        $data['rank']['hot'] = $str_hot;

        //推荐排行榜
        $db_rank_recommend = getDb(TABLE_GAME_RANK_RECOMMEND);
        $rank_recommend= $db_rank_recommend->field('appId')->order('id asc')->limit(7)->select();
        $str_recommend=null;
        foreach ($rank_recommend as $key=>$val)
        {
            $str_recommend!=null?$str_recommend=$str_recommend.'|'.$val["appId"]:$str_recommend=$str_recommend.$val["appId"];
        }
        $data['rank']['recommend'] = $str_recommend;

        //盒子安装包更新
        $db_box = getDb(TABLE_PACKAGE_BOX);
        $package_box = $db_box->field('version,versionName,desc,downUrl')->select();
        $data['package_box'] = $package_box[0];

        //盒子公告
        $db_notice = getDb(TABLE_GAME_NOTICE);
        $notice = $db_notice->field('noticeUrl')->select();
        $data['notice'] = $notice[0]['noticeUrl'];

        self::_notify(self::RESULT_SUCCESS, $data);

    }

    /**
     * 下载成功 更新下载次数
     * 客户端调用接口
     * http://xxx/tvbox/addDownloadTimes?appId=1
     *
     * http://xxx/index.php?m=Home&c=box&a=addDownloadTimes&appId=1
     */
    public function addDownloadTimes(){
         $appId=$_GET["appId"];
         $db_package = getDb(TABLE_PACKAGE_LIST);
         $data=$db_package->field('id,downloadTimes')->where('appId='.$appId)->find();
         $data['downloadTimes']+=1;
         if($db_package->save($data)){
             self::_notify(self::RESULT_SUCCESS);
         }
         else{
             self::_notify(self::RESULT_FAIL);
         }
    }

    /**
     * 返回数据
     * @param $status
     * @param null $data
     * @return array
     */
    private function _notify($status, $data = null)
    {
        $notify = array();
        $notify["status"] = $status;
        empty($data) or $notify["data"] = $data;
        exit(json_encode($notify, 256));
    }
}