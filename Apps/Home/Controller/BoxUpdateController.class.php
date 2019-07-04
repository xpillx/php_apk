<?php
/**
 * Tv盒子更新
 * Created by IntelliJ IDEA.
 * User: guowei
 * Date: 2018/9/4
 * Time: 9:00
 */

namespace Home\Controller;
use Home\Org\Html;
use Think\Upload;

class BoxUpdateController extends RoleController
{
    private static $uploadFileUrl = "http://192.168.33.130/test/upload/";
    private static $rootPath = './upload/';

    function index(){
    }


    private function getUpload(){
        $uploadConfig = [
            'subName'=>'box',
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
    function boxAdd(){
        if(IS_POST)
        {
            $db = getDb(TABLE_PACKAGE_BOX);

            $info =$db->select();
            if($info){
                error('添加失败,只允许一个安装包存在', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
            }


            $upload=  self::getUpload();
            if(!$info = $upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }

            $data = [
                'version'=>I('post.version'),
                'versionName'=>I('post.versionName'),
                'desc'=>I('post.desc'),
                'downName'=>$info['package']['savepath'].$info['package']['savename'],
                'downUrl'=> self::$uploadFileUrl.$info['package']['savepath'].$info['package']['savename'],
                'time'=>date("Y-m-d H:i:s"),
            ];

            if($db->add($data))
                success('添加成功', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
            else
                error('添加失败', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
        }

        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"添加");

        $assHtml['version'] = $html->createInput('text','version','');
        $assHtml['versionName'] = $html->createInput('text','versionName','');
        $assHtml['desc'] = $html->createInput('textarea','desc','');
        $assHtml['package'] = $html->createInput('file','package','');

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('BoxUpdate:boxAdd');

        $this->assignNavigation();
        $this->_out();
    }

    /**
     * 修改包信息
     */
    function boxEdit(){
        $db = getDb(TABLE_PACKAGE_BOX);
        $id = I('get.id');
        if(IS_POST) {

            $upload=  self::getUpload(I('post.appId'));
            $info = $upload->upload(); // 上传错误提示错误信息

            $data = [
                'id' => $id,
                'version' => I('post.version'),
                'versionName' => I('post.versionName'),
                'desc' => I('post.desc'),
                'time' => date("Y-m-d H:i:s"),
            ];

            if ($info) {
                if ($info['package']) {
                    $data['downName'] =$info['package']['savepath'].$info['package']['savename'];
                    $data['downUrl'] = self::$uploadFileUrl.$info['package']['savepath'].$info['package']['savename'];
                }
            }

            $packageInfo = $db->find($id);
            $rootPath = self::$rootPath;
            if ($db->save($data)){
                //刪除老文件
                if ($info['package'] && is_file($rootPath . $packageInfo['downName'])) {
                    @unlink($rootPath . $packageInfo['downName']);
                }
                success('修改成功', U('Table/index', ['table' => TABLE_PACKAGE_BOX]));
            }
            else{
                error('修改失败', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
            }
        }

        $packageInfo = $db->find($id);
        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"修改");

        $assHtml['version'] = $html->createInput('text','version',$packageInfo['version']);
        $assHtml['versionName'] = $html->createInput('text','versionName',$packageInfo['versionName']);
        $assHtml['desc'] = $html->createInput('textarea','desc',$packageInfo['desc']);
        $assHtml['package'] = $html->createInput('file','package',$packageInfo['downName']);

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('BoxUpdate:boxEdit');

        $this->assignNavigation();
        $this->_out();
    }

    /**
     * 删除包信息
     */
    function boxDel(){
        $db = getDb(TABLE_PACKAGE_BOX);
        $id = I('get.id');
        $rootPath = self::$rootPath;
        $packageInfo = $db->find($id);

        if($db->delete($id)){
            if(is_file($rootPath.$packageInfo['downName'])){
                @unlink($rootPath.$packageInfo['downName']);
            }
            success('删除成功', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
        }
        else
            error('修改失败', U('Table/index',['table'=>TABLE_PACKAGE_BOX]));
    }


}