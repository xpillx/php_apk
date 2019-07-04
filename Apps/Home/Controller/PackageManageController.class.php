<?php
namespace Home\Controller;

use Home\Org\Html;
use Think\Upload;
use Home\Org\ApkParser;
/**
 * APK包上传
 * Class PackageManageController
 * @package Home\Controller
 */
class PackageManageController extends RoleController{

    private static $uploadFileUrl = "http://192.168.33.130/test/upload/";
    private static $rootPath = './upload/';

    function index(){
	}


	private function getUpload($appId){
        $uploadConfig = [
            'subName'=>$appId,
            'maxSize'=>209715200,
            'rootPath'=>self::$rootPath,
            'autoSub'=> true，
        ];

        $upload = new Upload($uploadConfig);// 实例化上传类
        return $upload;
    }
    /**
     * 上传包信息
     */
    function packageAdd(){
        if(IS_POST)
        {
//            $uploadConfig = [
//                'subName'=>array('date','YmdHis'),
//                'maxSize'=>209715200,
//                'rootPath'=>self::$rootPath,
//                'autoSub'=> true，
//            ];
//
//            $upload = new Upload($uploadConfig);// 实例化上传类
            $upload=  self::getUpload(I('post.appId'));
            if(!$info = $upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }
//上传包信息
//            print_r($info['packageFile']);
//            Array ( [name] => 32_V1.4.4.apk
//            [type] => application/java-archive
//            [size] => 1258703
//            [key] => packageFile
//            [ext] => apk
//            [md5] => 32e1a96e6587c167ba3c4a52bc30bef7
//            [sha1] => 2cee329d487fccaf7b4334d08ee1f1c504430c00
//            [savename] => 5b878814cb7f8.apk
//            [savepath] => 20180903/)


            $data = [
                'appId'=>I('post.appId'),
                'desc'=>I('post.desc'),
                'category'=>implode('|',I('post.category')),
                'tag'=>I('post.tag'),
                'star'=>I('post.star'),
                'company'=>I('post.company'),
                'size'=>$info['package']["size"],
                'md5'=>$info['package']["md5"],
                'downloadTimes'=>I('post.downloadTimes'),
                'searchKey'=>I('post.searchKey'),
                'searchKeyFull'=>I('post.searchKeyFull'),
                'packageName'=>$info['package']['savepath'].$info['package']['savename'],
                'downUrl'=> self::$uploadFileUrl.$info['package']['savepath'].$info['package']['savename'],
                'createDate'=>date("Y-m-d H:i:s"),
            ];

            $appObj  = new Apkparser();
            $targetFile = self::$rootPath.$data['packageName'];//apk所在的路径地址
            $appObj->open($targetFile);
            $data['appPkg']=$appObj->getPackage();
            $data['version']=$appObj->getVersionCode();
            $data['versionName']=$appObj->getVersionName();

            if($info['icon']){
                $data['iconName']=$info['icon']['savepath'].$info['icon']['savename'];
                $data['iconUrl']= self::$uploadFileUrl.$info['icon']['savepath'].$info['icon']['savename'];
            }

            if($info['homeIcon']){
                $data['homeIconName']=$info['homeIcon']['savepath'].$info['homeIcon']['savename'];
                $data['homeIconUrl']= self::$uploadFileUrl.$info['homeIcon']['savepath'].$info['homeIcon']['savename'];
            }
            if($info['categoryIcon']){
                $data['categoryIconName']=$info['categoryIcon']['savepath'].$info['categoryIcon']['savename'];
                $data['categoryIconUrl']= self::$uploadFileUrl.$info['categoryIcon']['savepath'].$info['categoryIcon']['savename'];
            }

            if($info['video']){
                $data['videoName']=$info['video']['savepath'].$info['video']['savename'];
                $data['videoUrl']= self::$uploadFileUrl.$info['video']['savepath'].$info['video']['savename'];
            }

            foreach ($info as $key=>$value){
                if($value['key']=='screenshot'){
                    $data['screenshotName'].=$value['savepath'].$value['savename']."|";
                    $data['screenshotUrl'].= self::$uploadFileUrl.$value['savepath'].$value['savename']."|";
                }
            }
            if($data['screenshotName']){
                $data['screenshotName']=substr($data['screenshotName'],0,-1);
            }

            if($data['screenshotUrl']){
                $data['screenshotUrl']=substr($data['screenshotUrl'],0,-1);
            }

            $db = getDb(TABLE_PACKAGE_LIST);
            if($db->add($data))
                success('添加成功', U('Table/index',['table'=>TABLE_PACKAGE_LIST]));
            else
                error('添加失败', U('Table/index',['table'=>TABLE_PACKAGE_LIST]));
        }

        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"添加");

        $assHtml['appId'] = $html->createInput('select','appId','',getAllowAppAsData());
        $assHtml['version'] = $html->createInput('text','version','');
        $assHtml['versionName'] = $html->createInput('text','versionName','');
        $assHtml['desc'] = $html->createInput('textarea','desc','');
        $assHtml['category'] = $html->createInput('checkbox','category',"角色扮演",getGameType());
        $assHtml['tag'] = $html->createInput('select','tag',"",getGameTag());
        $assHtml['star'] = $html->createInput('number','star',5);
        $assHtml['company'] = $html->createInput('text','company','');
        $assHtml['downloadTimes'] = $html->createInput('number','downloadTimes','');
        $assHtml['searchKey'] = $html->createInput('text','searchKey','');
        $assHtml['searchKeyFull'] = $html->createInput('text','searchKeyFull','');
        $assHtml['package'] = $html->createInput('file','package','');
        $assHtml['icon'] = $html->createInput('file','icon','');
        $assHtml['homeIcon'] = $html->createInput('file','homeIcon','');
        $assHtml['categoryIcon'] = $html->createInput('file','categoryIcon','');
        $assHtml['screenshot'] = $html->createInput('file','screenshot[]','');
        $assHtml['video'] = $html->createInput('file','video','');


        $this->assign('html',$assHtml);
        $this->main = $this->fetch('PackageManage:packageAdd');

        $this->assignNavigation();
        $this->_out();
    }

    /**
     * 修改包信息
     */
    function packageEdit(){
        $db = getDb(TABLE_PACKAGE_LIST);
        $id = I('get.id');
        if(IS_POST) {
            $upload=  self::getUpload(I('post.appId'));
            $info = $upload->upload(); // 上传错误提示错误信息

            $data = [
                'id' => $id,
                'appId' => I('post.appId'),
                'desc' => I('post.desc'),
                'category' => implode('|',I('post.category')),
                'tag'=>I('post.tag'),
                'star' => I('post.star'),
                'company' => I('post.company'),
                'downloadTimes' => I('post.downloadTimes'),
                'searchKey' => I('post.searchKey'),
                'searchKeyFull' => I('post.searchKeyFull'),
                'createDate' => date("Y-m-d H:i:s"),
            ];

            if ($info) {
                if ($info['package']) {
                    $data['size'] = $info['package']["size"];
                    $data['md5'] = $info['package']["md5"];
                    $data['packageName'] =$info['package']['savepath'].$info['package']['savename'];
                    $data['downUrl'] = self::$uploadFileUrl.$info['package']['savepath'].$info['package']['savename'];

                    $appObj  = new Apkparser();
                    $targetFile = self::$rootPath.$data['packageName'];//apk所在的路径地址
                    $appObj->open($targetFile);
                    $data['appPkg']=$appObj->getPackage();
                    $data['version']=$appObj->getVersionCode();
                    $data['versionName']=$appObj->getVersionName();
                }
                if ($info['icon']) {
                    $data['iconName'] = $info['icon']['savepath'].$info['icon']['savename'];
                    $data['iconUrl'] = self::$uploadFileUrl . $info['icon']['savepath'].$info['icon']['savename'];
                }

                if($info['homeIcon']){
                    $data['homeIconName']=$info['homeIcon']['savepath'].$info['homeIcon']['savename'];
                    $data['homeIconUrl']= self::$uploadFileUrl.$info['homeIcon']['savepath'].$info['homeIcon']['savename'];
                }
                if($info['categoryIcon']){
                    $data['categoryIconName']=$info['categoryIcon']['savepath'].$info['categoryIcon']['savename'];
                    $data['categoryIconUrl']= self::$uploadFileUrl.$info['categoryIcon']['savepath'].$info['categoryIcon']['savename'];
                }

                foreach ($info as $key=>$value) {
                    if ($value['key'] == 'screenshot') {
                        $data['screenshotName'] .= $value['savepath'].$value['savename'] . "|";
                        $data['screenshotUrl'] .= self::$uploadFileUrl .$value['savepath']. $value['savename'] . "|";
                    }
                }

                if($data['screenshotName']){
                    $data['screenshotName']=substr($data['screenshotName'],0,-1);
                }

                if($data['screenshotUrl']){
                    $data['screenshotUrl']=substr($data['screenshotUrl'],0,-1);
                }

                if ($info['video']) {
                    $data['videoName'] = $info['video']['savepath'].$info['video']['savename'];
                    $data['videoUrl'] = self::$uploadFileUrl . $info['video']['savepath'].$info['video']['savename'];
                }
            }

            $packageInfo = $db->find($id);
            $rootPath = self::$rootPath;
            if ($db->save($data)){
                //刪除老文件
                if ($info['package'] && is_file($rootPath . $packageInfo['packageName'])) {
                    @unlink($rootPath . $packageInfo['packageName']);
                }
                if ($info['icon'] && is_file($rootPath . $packageInfo['iconName'])) {
                    @unlink($rootPath . $packageInfo['iconName']);
                }

                if ($info['homeIcon'] && is_file($rootPath . $packageInfo['homeIconName'])) {
                    @unlink($rootPath . $packageInfo['homeIconName']);
                }
                if ($info['categoryIcon'] && is_file($rootPath . $packageInfo['categoryIconName'])) {
                    @unlink($rootPath . $packageInfo['categoryIconName']);
                }

                $havingScreen=false;
                foreach ($info as $key=>$value) {
                    if ($value['key'] == 'screenshot') {
                      $havingScreen=true;
                    }
                }

                if ($havingScreen&&$packageInfo['screenshotName']) {
                    $screenshotArr = explode("|", $packageInfo['screenshotName']);
                    foreach ($screenshotArr as $value) {
                        if (is_file($rootPath . $value)) {
                            @unlink($rootPath . $value);
                        }
                    }
                }

                if ($info['video'] && is_file($rootPath . $packageInfo['videoName'])) {
                    @unlink($rootPath . $packageInfo['videoName']);
                }

                success('修改成功', U('Table/index', ['table' => TABLE_PACKAGE_LIST]));
            }
            else{
                error('修改失败', U('Table/index',['table'=>TABLE_PACKAGE_LIST]));
            }
        }

        $packageInfo = $db->find($id);
        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"修改");
        $assHtml['appId'] = $html->createInput('select','appId',$packageInfo['appId'],getAllowAppAsData());
        $assHtml['version'] = $html->createInput('text','version',$packageInfo['version']);
        $assHtml['versionName'] = $html->createInput('text','versionName',$packageInfo['versionName']);
        $assHtml['desc'] = $html->createInput('textarea','desc',$packageInfo['desc']);
        $assHtml['category'] = $html->createInput('checkbox','category',explode('|',$packageInfo['category']),getGameType());
        $assHtml['tag'] = $html->createInput('select','tag',$packageInfo['tag'],getGameTag());
        $assHtml['star'] = $html->createInput('number','star',$packageInfo['star']);
        $assHtml['company'] = $html->createInput('text','company',$packageInfo['company']);
        $assHtml['downloadTimes'] = $html->createInput('number','downloadTimes',$packageInfo['downloadTimes']);
        $assHtml['searchKey'] = $html->createInput('text','searchKey',$packageInfo['searchKey']);
        $assHtml['searchKeyFull'] = $html->createInput('text','searchKeyFull',$packageInfo['searchKeyFull']);
        $assHtml['package'] = $html->createInput('file','package',$packageInfo['packageName']);
        $assHtml['icon'] = $html->createInput('file','icon',$packageInfo['iconName']);
        $assHtml['homeIcon'] = $html->createInput('file','homeIcon',$packageInfo['homeIconName']);
        $assHtml['categoryIcon'] = $html->createInput('file','categoryIcon',$packageInfo['categoryIconName']);
        $assHtml['screenshot'] = $html->createInput('file','screenshot[]',$packageInfo['screenshotName']);
        $assHtml['video'] = $html->createInput('file','video',$packageInfo['videoName']);

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('PackageManage:packageEdit');

        $this->assignNavigation();
        $this->_out();
    }

    /**
     * 删除包信息
     */
    function packageDel(){
        $db = getDb(TABLE_PACKAGE_LIST);
        $id = I('get.id');
        $rootPath = self::$rootPath;
        $packageInfo = $db->find($id);

        if($db->delete($id)){
            if(is_file($rootPath.$packageInfo['packageName'])){
                @unlink($rootPath.$packageInfo['packageName']);
            }

            if(is_file($rootPath.$packageInfo['iconName'])){
                @unlink($rootPath.$packageInfo['iconName']);
            }

            if(is_file($rootPath.$packageInfo['homeIconName'])){
                @unlink($rootPath.$packageInfo['homeIconName']);
            }

            if(is_file($rootPath.$packageInfo['categoryIconName'])){
                @unlink($rootPath.$packageInfo['categoryIconName']);
            }

            if($packageInfo['screenshotName']){
              $screenshotArr=explode("|",$packageInfo['screenshotName']);
              foreach ($screenshotArr as $value){
                  if(is_file($rootPath.$value)){
                      @unlink($rootPath.$value);
                  }
              }
            }

            if(is_file($rootPath.$packageInfo['videoName'])){
                @unlink($rootPath.$packageInfo['videoName']);
            }
            success('删除成功', U('Table/index',['table'=>TABLE_PACKAGE_LIST]));
        }
        else
            error('修改失败', U('Table/index',['table'=>TABLE_PACKAGE_LIST]));
    }



}