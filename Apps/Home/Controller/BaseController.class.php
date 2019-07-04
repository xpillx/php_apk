<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    protected $navigation = true;
    protected $search = ''; //分配到模板页的html
    protected $nav = ''; //分配到模板页的html
    protected $serach = ''; //分配到模板页的html
    protected $main = ''; //分配到模板页的html
    protected $pager = ''; //分配到模板页的html

    private $topNavigation = array();
    private $leftNavigation = array();
    private $leftNavigationSelect = array();

    function __construct()
    {
        parent::__construct();
        header("Content-type:text/html;charset=utf-8");
        debug($_SESSION);
    }


    protected  function assignNavigation()
    {
        static $assigned = false;
        if ( !$assigned ) {
            $nav = C('NAVIGATION');

            foreach ($nav as $k => $v) //获取用户权限内所有的导航  左侧导航
            {
                if(isset($v['func']))
                {
                    $temp = TableController::parseFunc($v['func']);
                    unset($v['func']);
                    $v = merge($temp , $v);
                }

                foreach ($v as $k2 => $v2)
                {
                        if (!isset($v2['power']) || ( hasRole($v2['power'])) )
                        {
                            //未设置验证方法  或者  设置了方法，切方法返回值为true
                            if( !isset($v2['func']) || TableController::parseFunc($v2['func']) )
                            {
                                $link = $v2['link'];
                                $this->leftNavigation[$k][$k2]['link'] = TableController::createLink($link);

                                $t1 = explode('?', trim($link, '/'));
                                $t2 = explode('/', $t1[0]);
                                $this->leftNavigation[$k][$k2]['c'] = strtolower($t2[0]); //CONTROLLER_NAME  ,控制器名
                                $this->leftNavigation[$k][$k2]['a'] = empty($t2[1]) ? 'index' : strtolower($t2[1]) ; //ACTION_NAME  ，方法名
                                $this->leftNavigation[$k][$k2]['p'] = empty($t1[1]) ?  '' : $t1[1] ; //参数列表
                            }
                        }
                    //未设置权限，所有人都有权限 ,设置了权限，就验证权限

                }
            }

            foreach ($this->leftNavigation as $k => $v) //获取所有顶部导航
            {
                $link = null;
                if ( !isset( $this->topNavigation[$k] ) || null === $this->topNavigation[$k] ) {  //获取每个topNavigation的默认link
                    $temp = current( array_shift($v) );
                    $link = $temp;
                }
                $this->topNavigation[$k]['link'] = $link;
            }

            $this->findSelectedNavigation(); //标记当前选中的

            $this->filterUnvalidNavigation(); //过滤无效的左边导航
            $this->assign("topNav", $this->topNavigation);
            $this->assign("leftNav", $this->leftNavigationSelect);
            debug($this->topNavigation, "Top navgation");
            debug($this->leftNavigation, "Left navgation");
            debug($this->leftNavigationSelect, "Left navgationSelected");
            $assigned = true;
        }

    }

    private function findSelectedNavigation()
    {
        $nav = I('get.nav');
        if($nav)
        {
            list($top,$left) = explode('-',$nav);
            $j = 1;
            foreach ($this->leftNavigation as $k => $v) {
                $i = 1;
                foreach ($v as $k2 => $v2) {
                    if($i == $left && $j == $top)
                    {
                        $this->leftNavigation[$k][$k2]['selected'] = true;
                        $this->topNavigation[$k]['selected'] = true;
                        return;
                    }
                    $i++;
                }
                $j++;
            }
        }
        //下面为自动选中
        if(C('URL_MODEL') == 0)
            $this->findSelectedNavigationModel0();
        else
            $this->findSelectedNavigationModel1();
    }

    private function findSelectedNavigationModel0()
    {
        $c = strtolower(CONTROLLER_NAME);
        $a = strtolower(ACTION_NAME);
        $p = strtolower( html_entity_decode(  I('server.QUERY_STRING') ));

        for($i = 0; $i < 2 ; $i++) {
            foreach ($this->leftNavigation as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if ($v2['c'] == $c && $v2['a'] == $a){
                        switch ($i) {
                            case 0:
                                $rep = str_replace( strtolower("m=home&c={$v2['c']}&a={$v2['a']}&" . $v2['p']) , '' , $p) ;
                                if( $rep == "" )
                                {
                                    $this->leftNavigation[$k][$k2]['selected'] = true;
                                    $this->topNavigation[$k]['selected'] = true;
                                    return;
                                }
                                break;
                            case 1:
                                $rep = str_replace( strtolower("m=home&c={$v2['c']}&a={$v2['a']}&" . $v2['p']) , '' , $p) ;
                                if( strpos($rep , "&") === 0)
                                {
                                    $this->leftNavigation[$k][$k2]['selected'] = true;
                                    $this->topNavigation[$k]['selected'] = true;
                                    return;
                                }
                                break;
                        }
                    }
                }
            }
        }

        foreach ($this->leftNavigation as $k => $v) {
            foreach ($v as $k2 => $v2) {
                if($v2['c'] == $c && $v2['a'] == $a  )
                {

                    $rep = str_replace( strtolower("m=home&c={$v2['c']}&a={$v2['a']}&" . $v2['p']) , '' , $p) ;

                    if(( $rep == "" || strpos($rep , "&") === 0))
                    {
                        $this->leftNavigation[$k][$k2]['selected'] = true;
                        $this->topNavigation[$k]['selected'] = true;
                        return;
                    }

                }
            }
        }


        //如果遍历一遍都未找到 ,那么元素的第一个作为默认选中项
        foreach ($this->leftNavigation as $k => $v) {
            foreach ($v as $k2 => $v2) {
                if($v2['c'] == $c && $v2['a'] == $a && ( ($v2['p'] == "" || strpos($p, $v2['p']) !== false) ) )
                {
                    $this->leftNavigation[$k][$k2]['selected'] = true;
                    $this->topNavigation[$k]['selected'] = true;
                    return;
                }
            }
        }

    }
    /**
     * 获取当前的实际url对应的navgation中的url
     */
    private function findSelectedNavigationModel1()
    {
        dump($_SERVER);

        $uri = strtolower( $_SERVER['REQUEST_URI'] );

        $uri = preg_replace('/(.+)\/p\/\d+(.*)/','$1$2',$uri); //去除分页
        $uri = preg_replace('/(.+)\/method\/.+(.*)/','$1$2',$uri); //去除method



        foreach ($this->leftNavigation as $k => $v) {
            foreach ($v as $k2 => $v2)
            {
                    if($uri == strtolower( $v2['link']) )
                    {

                        $this->leftNavigation[$k][$k2]['selected'] = true;
                        $this->topNavigation[$k]['selected'] = true;
                        return;
                    }
                }
        }

        $uri = preg_replace('/(.+)\/where\/.+(.*)/','$1$2',$uri); //去除where
        foreach ($this->leftNavigation as $k => $v) {
            foreach ($v as $k2 => $v2)
            {
                if($uri == strtolower( $v2['link']) )
                {

                    $this->leftNavigation[$k][$k2]['selected'] = true;
                    $this->topNavigation[$k]['selected'] = true;
                    return;
                }
            }
        }



        debug('遍历一遍都未找到 ,那么元素的第一个作为默认选中项');
    }

    protected  function getFristNavigation()
    {
        $this->assignNavigation();
        foreach($this->leftNavigation as $k=>$v)
        {
                $current = current($v);
                return $current['link'];
        }
        return null;
    }

    /**
     *过滤不属于当前顶部选中的navgation
     */
    private function filterUnvalidNavigation()
    {
        $find = null;
        foreach ($this->topNavigation as $k => $v) {
            if ($v['selected'] == true) {
                $find = $k;
                break;
            }
        }
        $t = $this->leftNavigation;
        $this->leftNavigationSelect = $t[$find];
    }


    function _out()
    {
        $con=array();
        if($this->search)  $con['search'] = $this->search;
        if ($this->nav)    $con['nav'] = $this->nav;
        if ($this->search) $con['search'] = $this->search;
        if ($this->main)   $con['main'] = $this->main;
        if ($this->pager)  $con['pager'] = $this->pager;
        $this->assign('con', $con);
        $this->display("Public:main");
        exit;

    }

    function success($mes = null, $url = null, $ajax = null , $wait = '')
    {
        parent::success($mes, $url, $ajax , $wait);
    }

    function error($mes = null, $url = null, $ajax = null , $wait = '')
    {
        parent::error($mes, $url, $ajax ,$wait );
    }

    protected function ajaxSuccess($data = null, $info = "操作成功")
    {
        $json['status'] = 1;
        $json['info'] = $info;
        $json['data'] = $data;
        echo json_encode($json);
        exit;
    }

    protected function ajaxFailed($info = "操作失败")
    {
        $json['status'] = 0;
        $json['info'] = $info;
        $json['data'] = null;
        echo json_encode($json);
        exit;
    }

    function __destruct()
    {
        //dump (getRuntime()) ;
    }


}