<?php
namespace Home\Controller;
class ApiController extends RoleController
{
    function freshChannel()
    {
        $url = 'http://xxx/index.php?c=Api&a=channel';
        $data = json_decode(file_get_contents($url) , true);
        if(empty($data))
            error("刷新失败，请确认接口数据：{$url};");
        $db = getDb(TABLE_CHANNEL_MST);
        $db->where("id>0")->delete();
//        dump($db->getLastSql());
//        exit;
//        $count = $db->data($data)->addAll();
//        dump($count);
//        dump($data);
//        exit;
        $s = $f = 0;
        foreach($data as $v)
            $db->add(array('channel_id'=>$v['channel_id'],'sitename'=>$v['sitename']), null, true) ? $s++ : $f++;
            success("刷新成功，共{$s}条数据!");
    }
    //刷新游戏
    function freshgame()
    {
        $url = 'http://xxx/index.php?c=Api&a=ad_game';
        $data = json_decode(file_get_contents($url) , true);
        if(empty($data))
            error("刷新失败，请确认接口数据：{$url};");
        $db = getDb(TABLE_GAME);
       // dump($data);die;
        $s = $f = 0;
        foreach($data as $val ){
            //dump($val);die;
            $db->add($val,null,true) ? $s++ : $f++;
           success(" 刷新成功 $s 条, 失败 $f 个");
        }
   }
}