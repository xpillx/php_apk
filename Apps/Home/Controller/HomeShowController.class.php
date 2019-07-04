<?php
/**
 * 首页展示图片9张 第一张单独处理（结构不一样）
 * Created by IntelliJ IDEA.
 * User: guowei
 * Date: 2018/9/4
 * Time: 9:00
 */

namespace Home\Controller;
use Home\Org\Html;
use Think\Upload;
class HomeShowController extends RoleController
{
    private static $uploadFileUrl = "http://192.168.33.130/test/upload/";
    private static $rootPath = './upload/';

    private function getUpload($displayIndex){
        $uploadConfig = [
            'subName'=>'home/'.$displayIndex,
            'maxSize'=>209715200,
            'rootPath'=>self::$rootPath,
            'autoSub'=> true，
        ];

        $upload = new Upload($uploadConfig);// 实例化上传类
        return $upload;
    }

    function index(){
    }

    function homeShowAdd(){
        if(IS_POST)
        {
            $upload=  self::getUpload(I('post.displayIndex'));
            if(!$info = $upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }

            $data = [
                'appId'=>I('post.appId'),
                'displayIndex'=>I('post.displayIndex'),
            ];

            if($info['icon']){
                $data['showIconName']=$info['icon']['savepath'].$info['icon']['savename'];
                $data['showIconUrl']= self::$uploadFileUrl.$info['icon']['savepath'].$info['icon']['savename'];
            }

            $db = getDb(TABLE_GAME_HOME_SHOW);
            if($db->add($data))
                success('添加成功', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW]));
            else
                error('添加失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW]));
        }

        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"添加");
        $assHtml['appId'] = $html->createInput('select','appId','',getAllowAppAsData());
        $assHtml['displayIndex'] = $html->createInput('number','displayIndex','');
        $assHtml['icon'] = $html->createInput('file','icon','');

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('HomeShow:homeShowAdd');
        $this->assignNavigation();
        $this->_out();

    }

    function homeShowEdit(){
        $db = getDb(TABLE_GAME_HOME_SHOW);
        $id = I('get.id');
        if(IS_POST) {
            $upload=  self::getUpload(I('post.appId'));
            $info = $upload->upload(); // 上传错误提示错误信息

            $data = [
                'id' => $id,
                'appId' => I('post.appId'),
                'displayIndex'=>I('post.displayIndex'),
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

                success('修改成功', U('Table/index', ['table' => TABLE_GAME_HOME_SHOW]));
            }
            else{
                error('修改失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW]));
            }
        }

        $info = $db->find($id);
        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"修改");
        $assHtml['appId'] = $html->createInput('select','appId',$info['appId'],getAllowAppAsData());
        $assHtml['displayIndex'] = $html->createInput('number','displayIndex',$info['displayIndex']);
        $assHtml['icon'] = $html->createInput('file','icon',$info['showIconName']);

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('HomeShow:homeShowEdit');
        $this->assignNavigation();
        $this->_out();
    }

    function homeShowDel(){
        $db = getDb(TABLE_GAME_HOME_SHOW);
        $id = I('get.id');

        $rootPath = self::$rootPath;
        $packageInfo = $db->find($id);

        if($db->delete($id)){

            if(is_file($rootPath.$packageInfo['showIconName'])){
                @unlink($rootPath.$packageInfo['showIconName']);
            }

            success('删除成功', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW]));
        }
        else
            error('修改失败', U('Table/index',['table'=>TABLE_GAME_HOME_SHOW]));
    }

}