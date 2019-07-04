<?php
namespace Home\Controller;
use Home\Org\Html;
class DatasubController extends RoleController{

    function addDayRank()
    {
        $p['uid']  = intval( $_REQUEST['uid'] ) ;
        $p['score']  = intval($_REQUEST['score'] ) ;
        $p['app_id'] = $_REQUEST['app_id'] ? $_REQUEST['app_id']  : 0;
        if($p['uid'] === 0  || $p['score'] === 0 || !$p['app_id'])
            error("UID，分数，appid 必须填写！");

        $p['singing']= '15241622537524465824' ;
        $url = getInterfaceUrl('addDayRank',$p);
        debug($url);
//        exit;
        $data = json_decode( file_get_contents($url) , true );
        debug($data);
        if($data['status'] == 0)
            success("操作成功" , U('Table/index',array('table'=> DATA_TOP )));
        else
            error($data['data']);
    }

    function delDayRank()
    {
        $p['app_id'] = $_REQUEST['app_id'] ? $_REQUEST['app_id']  : 0;
        $p['uid']  = intval( $_REQUEST['uid'] ) ;
        if($p['uid'] === 0  || !$p['app_id'])
            error("UID，分数，appid 必须填写！");

        $url = getInterfaceUrl('delDayRank',$p);
        file_get_contents($url) ;
        success();
    }



    function addRank()
    {
        $p['uid']  = intval( $_REQUEST['uid'] ) ;
        $p['score']  = intval($_REQUEST['score'] ) ;
        $p['app_id'] = $_REQUEST['app_id'] ? $_REQUEST['app_id']  : 0;
        if($p['uid'] === 0  || $p['score'] === 0 || !$p['app_id'])
            error("UID，分数，appid 必须填写！");
        $p['channel_id']= $_REQUEST['channel_id'];
        $url = getInterfaceUrl('addRank',$p);
        $data = json_decode( file_get_contents($url) , true );
        if($data['status'] == 1)
            success("操作成功" , U('Table/index',array('table'=> DATA_TOP )));
        else
            error($data['data']);
    }

    function delRank()
    {
        $p['app_id'] = $_REQUEST['app_id'] ? $_REQUEST['app_id']  : 0;
        $p['uid']  = intval( $_REQUEST['uid'] ) ;
        $p['channel_id'] = $_REQUEST['channel_id'];
        if($p['uid'] === 0  || !$p['app_id'] || !$p['channel_id'])
            error("UID，分数，appid,channel_id 必须填写！");

        $url = getInterfaceUrl('delRank',$p);
        $data = json_decode( file_get_contents($url)  , true );
        if($data['status'] == 1)
            success();
        else
            error();
    }


    function addDataFromUrl()
    {
        $urlConfig = $_REQUEST['interfaceUrl'];
        if(!$urlConfig)
            error("未传入参数interfaceUrl");
        $u = getInterfaceUrl($urlConfig);
        json_decode( file_get_contents($u)  , true );
        success();
    }

    function delDataFromUrl()
    {
        $urlConfig = $_REQUEST['interfaceUrl'];
        if(!$urlConfig)
            error("未传入参数interfaceUrl");
        $u = getInterfaceUrl($urlConfig);
//            dump($u);
//        exit;
        json_decode( file_get_contents($u)  , true );
        success();
    }

    function mutilSub($table , $field , $data )
    {
        if(empty($data) || empty($field) || empty($table))
            error("mutilSub提交参数错误");
        $arr = strToArray($data[$field],'|');
        $table = getDb($table);
        $success = $faild = 0;
        foreach($arr as $v)
        {
            $data[$field] = $v;
            $table->add($data) ? $success++ : $faild++ ;
        }
        success("添加成功: {$success} 条数据,添加失败： {$faild} 条数据!");
    }

    /**
     * @tip 游戏添加或修改的时 数据合法性验证
     */
    public function  checkGameSubmit()
    {
        $model = getDb(TABLE_GAME);
        $isEdit = $_REQUEST['isedit'] ? 'edit' : 'add';
        $id = trim($_REQUEST['id']);
        $name = trim($_REQUEST['name']);
        $data = array();
        switch ($isEdit)
        {
            case 'edit':
                $id = $_REQUEST['editid'];
                $isSet = $model->where("`id`={$id} and `name`='{$name}' ")->find();
                # 先判断本次修改的记录是否均已经存在
                if(!is_null($isSet)) error("该游戏已存在！id：'{$id}' , name='{$name}' 。");
                $data['name'] = $name;
                $res = $model->where("id={$id}")->save($data);
                if($res)
                    success('修改成功！');
                else
                    error('修改失败，请重试！');
                break;
            case 'add':
                if(!$id || !is_numeric($id)) error("游戏ID不能为：`{$id}`!");
                $isSet = $model->where("id={$id}")->find();
                if($isSet) error("游戏ID为：{$id}的记录已经存在！");
                $data['id'] = $id;
                $data['name'] = $name;
                $model->add($data);
                success("添加成功！");
                break;
        }
    }

    /**
     * @tip 游戏类型添加或修改的时 数据合法性验证
     */
    public function  checkGameTypeSubmit()
    {
        $model = getDb(TABLE_GAME_TYPE);
        $isEdit = $_REQUEST['isedit'] ? 'edit' : 'add';
        $name = trim($_REQUEST['name']);
        $shortname = trim($_REQUEST['shortname']);
        $data = array();
        switch ($isEdit)
        {
            case 'edit':
                $id = $_REQUEST['editid'];
                $data['name'] = $name;
                $data['shortname'] = $shortname;
                $res = $model->where("id={$id}")->save($data);
                if($res)
                    success('修改成功！');
                else
                    error('修改失败，请重试！');
                break;
            case 'add':
                $data['name'] = $name;
                $data['shortname'] = $shortname;
                $model->add($data);
                success("添加成功！");
                break;
        }
    }

    /**
     * @tip 游戏添加或修改的时 数据合法性验证
     */
    public function checkGameTagSubmit()
    {
        $model = getDb(TABLE_GAME_TAG);
        $isEdit = $_REQUEST['isedit'] ? 'edit' : 'add';
        $id = trim($_REQUEST['id']);
        $name = trim($_REQUEST['name']);
        $data = array();
        switch ($isEdit)
        {
            case 'edit':
                $id = $_REQUEST['editid'];
                $isSet = $model->where("`id`={$id} and `name`='{$name}'")->find();
                # 先判断本次修改的记录是否均已经存在
                if(!is_null($isSet)) error("该标签已存在！id：'{$id}' , name='{$name}'。");
                $data['name'] = $name;
                $res = $model->where("id={$id}")->save($data);
                if($res)
                    success('修改成功！');
                else
                    error('修改失败，请重试！');
                break;
            case 'add':
                if(!$id || !is_numeric($id)) error("标签ID不能为：`{$id}`!");
                $isSet = $model->where("id={$id}")->find();
                if($isSet) error("标签ID为：{$id}的记录已经存在！");
                $data['id'] = $id;
                $data['name'] = $name;
                $model->add($data);
                success("添加成功！");
                break;
        }
    }

    /**
     * @tip 添加修改热门排行榜 数据合法性验证
     */
    public function checkRankHotSubmit()
    {
        $model = getDb(TABLE_GAME_RANK_HOT);
        $isEdit = $_REQUEST['isedit'] ? 'edit' : 'add';
        $id = trim($_REQUEST['id']);
        $appId = trim($_REQUEST['appId']);
        $data = array();
        switch ($isEdit)
        {
            case 'edit':
                $isSet = $model->where("`appId`={$appId} and `id`='{$id}'")->find();
                # 先判断本次修改的记录是否均已经存在
                if(!is_null($isSet)) error("该标签已存在！appId：'{$appId}' , id='{$id}'。");
                $data['appId'] = $appId;
                $res = $model->where("id={$id}")->save($data);
                if($res)
                    success('修改成功！');
                else
                    error('修改失败，请重试！');
                break;
            case 'add':
                if(!$id || !is_numeric($id)) error("id：`{$id}`!");
                $isSet = $model->where("id={$id}")->find();
                if($isSet) error("排名id为：{$id}的记录已经存在！");
                $data['id'] = $id;
                $data['appId'] = $appId;

                $model->add($data);
                success("添加成功！");
                break;
        }
    }

    /**
     * @tip 添加修改推荐排行榜 数据合法性验证
     */
    public function checkRankRecommendSubmit()
    {
        $model = getDb(TABLE_GAME_RANK_RECOMMEND);
        $isEdit = $_REQUEST['isedit'] ? 'edit' : 'add';
        $id = trim($_REQUEST['id']);
        $appId= trim($_REQUEST['appId']);
        $data = array();
        switch ($isEdit)
        {
            case 'edit':
                $isSet = $model->where("`appId`={$appId} and `id`='{$id}'")->find();
                # 先判断本次修改的记录是否均已经存在
                if(!is_null($isSet)) error("该标签已存在！appId：'{$appId}' , id='{$id}'。");
                $data['appId'] = $appId;
                $res = $model->where("id={$id}")->save($data);
                if($res)
                    success('修改成功！');
                else
                    error('修改失败，请重试！');
                break;
            case 'add':
                if(!$id || !is_numeric($id)) error("排名index不能为：`{$id}`!");
                $isSet = $model->where("id={$id}")->find();
                if($isSet) error("排名id为：{$id}的记录已经存在！");
                $data['appId'] = $appId;
                $data['id'] = $id;
                $model->add($data);
                success("添加成功！");
                break;
        }
    }
}