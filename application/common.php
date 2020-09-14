<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function ToArray($data){
    return json_decode(json_encode($data),true);
}

function TrimNull($str)
{
    $search = array(" ","　","\n","\r","\t");
    $replace = array("","","","","");
    return str_replace($search, $replace, $str);
}