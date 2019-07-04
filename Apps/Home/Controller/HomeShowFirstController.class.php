<?php
/**
 * 首页展示图片第一张单独处理
 * Created by IntelliJ IDEA.
 * User: guowei
 * Date: 2018/9/4
 * Time: 9:00
 */

namespace Home\Controller;
use Home\Org\Html;
use Think\Upload;
class HomeShowFirstController extends RoleController
{
    private static $uploadFileUrl = "http://192.168.33.130/test/upload/";
    private static $rootPath = './upload/';

    private function getUpload($displayIndex,$appId){
        $uploadConfig = [
            'subName'=>'home/'.$displayIndex.'/'.$appId,
            'maxSize'=>209715200,
            'rootPath'=>self::$rootPath,
            'autoSub'=> true，
        ];

        $upload = new Upload($uploadConfig);// 实例化上传类
        return $upload;
    }

    function index(){
    }

    function homeShowFirstAdd(){
        if(IS_POST)
        {
            $upload=  self::getUpload(I('post.displayIndex'),I('post.appId'));
            if(!$info = $upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }

            $data = [
                'displayIndex'=>I('post.displayIndex'),
                'appId'=>I('post.appId'),
                'isPic'=>I('post.isPic'),
            ];

            if($info['icon']){
                $data['showIconName']=$info['icon']['savepath'].$info['icon']['savename'];
                $data['showIconUrl']= self::$uploadFileUrl.$info['icon']['savepath'].$info['icon']['savename'];
            }

            $db = getDb(TABLE_GAME_HOME_SHOW_FIRST);
            if($db->add($data))
                success('添加成功', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW_FIRST]));
            else
                error('添加失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW_FIRST]));
        }

        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"添加");
        $assHtml['displayIndex'] = $html->createInput('readonly','displayIndex',1);
        $assHtml['appId'] = $html->createInput('select','appId','',getAllowAppAsData());
        $assHtml['isPic'] = $html->createInput('select','isPic','',C('YESORNO'));
        $assHtml['icon'] = $html->createInput('file','icon','');

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('HomeShowFirst:homeShowFirstAdd');
        $this->assignNavigation();
        $this->_out();

    }

    function homeShowFirstEdit(){
        $db = getDb(TABLE_GAME_HOME_SHOW_FIRST);
        $id = I('get.id');
        if(IS_POST) {
            $upload=  self::getUpload(I('post.displayIndex'),I('post.appId'));
            $info = $upload->upload(); // 上传错误提示错误信息

            $data = [
                'id' => $id,
                'displayIndex'=>I('post.displayIndex'),
                'appId' => I('post.appId'),
                'isPic'=>I('post.isPic'),
            ];

            if ($info) {
                if ($info['icon']) {
                    $data['showIconName'] = $info['icon']['savepath'] . $info['icon']['savename'];
                    $data['showIconUrl'] = self::$uploadFileUrl . $info['icon']['savepath'] . $info['icon']['savename'];
                }
            }

            $packageInfo = $db->find($id);
            $rootPath = self::$rootPath;

            if ($db->save($data)){
                if ($info['icon'] && is_file($rootPath . $packageInfo['showIconName'])) {
                    @unlink($rootPath . $packageInfo['showIconName']);
                }

                success('修改成功', U('Table/index', ['table' => TABLE_GAME_HOME_SHOW_FIRST]));
            }
            else{
                error('修改失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW_FIRST]));
            }
        }

        $info = $db->find($id);
        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"修改");
        $assHtml['displayIndex'] = $html->createInput('readonly','displayIndex',$info['displayIndex']);
        $assHtml['appId'] = $html->createInput('select','appId',$info['appId'],getAllowAppAsData());
        $assHtml['isPic'] = $html->createInput('select','isPic',$info['isPic'],C('YESORNO'));
        $assHtml['icon'] = $html->createInput('file','icon',$info['showIconName']);

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('HomeShowFirst:homeShowFirstEdit');
        $this->assignNavigation();
        $this->_out();
    }

    function homeShowFirstDel(){
        $db = getDb(TABLE_GAME_HOME_SHOW_FIRST);
        $id = I('get.id');

        $rootPath = self::$rootPath;
        $packageInfo = $db->find($id);

        if($db->delete($id)){

            if(is_file($rootPath.$packageInfo['showIconName'])){
                @unlink($rootPath.$packageInfo['showIconName']);
            }

            success('删除成功', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW_FIRST]));
        }
        else
            error('修改失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW_FIRST]));
    }
}