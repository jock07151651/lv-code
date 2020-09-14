<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7 0007
 * Time: 下午 5:07
 */

namespace app\index\controller;


use think\Controller;

class SearchByLetter extends Controller
{
    //根据字母搜索
    public function index($letter='A')
    {
        return $this->fetch('letter/index');
    }
}