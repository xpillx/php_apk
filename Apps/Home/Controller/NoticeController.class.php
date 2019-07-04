<?php
/**
 * 盒子公告展示
 * Created by IntelliJ IDEA.
 * User: guowei
 * Date: 2018/9/4
 * Time: 9:00
 */

namespace Home\Controller;
use Home\Org\Html;
use Think\Upload;
class NoticeController extends RoleController
{
    private static $uploadFileUrl = "http://192.168.33.130/test/upload/";
    private static $rootPath = './upload/';

    private function getUpload(){
        $uploadConfig = [
            'subName'=>'notice',
            'maxSize'=>209715200,
            'rootPath'=>self::$rootPath,
            'autoSub'=> true，
        ];

        $upload = new Upload($uploadConfig);// 实例化上传类
        return $upload;
    }

    function index(){
    }

    function noticeAdd(){
        if(IS_POST)
        {
            $upload=  self::getUpload();
            if(!$info = $upload->upload()) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }

            if($info['icon']){
                $data['noticeName']=$info['icon']['savepath'].$info['icon']['savename'];
                $data['noticeUrl']= self::$uploadFileUrl.$info['icon']['savepath'].$info['icon']['savename'];
            }

            $db = getDb(TABLE_GAME_NOTICE);
            if($db->add($data))
                success('添加成功', U('Table/index',['table'=>TABLE_GAME_NOTICE]));
            else
                error('添加失败', U('Table/index',['table'=>TABLE_GAME_NOTICE]));
        }

        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"添加");
        $assHtml['icon'] = $html->createInput('file','icon','');

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('Notice:noticeAdd');
        $this->assignNavigation();
        $this->_out();

    }

    function noticeEdit(){
        $db = getDb(TABLE_GAME_NOTICE);
        $id = I('get.id');
        if(IS_POST) {
            $upload=  self::getUpload(I('post.appId'));
            $info = $upload->upload(); // 上传错误提示错误信息

            $data = [
                'id' => $id,
            ];

            if ($info) {
                if ($info['icon']) {
                    $data['noticeName'] = $info['icon']['savepath'] . $info['icon']['savename'];
                    $data['noticeUrl'] = self::$uploadFileUrl . $info['icon']['savepath'] . $info['icon']['savename'];
                }
            }

            $packageInfo = $db->find($id);
            $rootPath = self::$rootPath;

            if ($db->save($data)){
                if ($info['icon'] && is_file($rootPath . $packageInfo['noticeName'])) {
                    @unlink($rootPath . $packageInfo['noticeName']);
                }

                success('修改成功', U('Table/index', ['table' => TABLE_GAME_NOTICE]));
            }
            else{
                error('修改失败', U('Table/index',['table'=>TABLE_GAME_NOTICE]));
            }
        }

        $info = $db->find($id);
        $html = new Html();
        $assHtml['submit'] = $html->createInput('submit','submit',"修改");
        $assHtml['icon'] = $html->createInput('file','icon',$info['noticeName']);

        $this->assign('html',$assHtml);
        $this->main = $this->fetch('Notice:noticeEdit');
        $this->assignNavigation();
        $this->_out();
    }

    function noticeDel(){
        $db = getDb(TABLE_GAME_NOTICE);
        $id = I('get.id');

        $rootPath = self::$rootPath;
        $packageInfo = $db->find($id);

        if($db->delete($id)){

            if(is_file($rootPath.$packageInfo['noticeName'])){
                @unlink($rootPath.$packageInfo['noticeName']);
            }

            success('删除成功', U('Table/index',['table'=>TABLE_GAME_NOTICE]));
        }
        else
            error('修改失败', U('Table/index',['table'=>TABLE_GAME_NOTICE]));
    }

}