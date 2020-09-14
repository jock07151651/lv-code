<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/26 0026
 * Time: 上午 10:12
 */

namespace app\article\controller;


use think\Controller;

class Errorpage extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
}