<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18 0018
 * Time: 下午 2:56
 */

namespace app\index\controller;


use think\Controller;

class NotFound extends Controller
{
    public function index()
    {
        return $this->fetch('notfound/index');
    }
}