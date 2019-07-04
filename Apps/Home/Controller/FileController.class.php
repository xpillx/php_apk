<?php

/**
 * 图片展示
 */
namespace Home\Controller;

class FileController extends BaseController
{
    function showImg($name)
    {
        if ($name === null || $name === '')
            return '';
        $names=explode("|",$name);
        return '<a href="' . $names[0] . '" target="_blank"><img width="100px" height="100px" src="' . $names[0] . '" title="点击查看大图"/></a>';
    }
}
