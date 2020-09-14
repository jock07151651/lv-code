<?php
/**
 * 萝卜音乐系统
 * @verson 1.0
 * @author: yanghuachu
 * @company: 萝卜科技
 * @license: yy.luobokeji.cn
 * @copyright: 2012 - 2019
 */

namespace app\index\controller;


use app\index\model\SoftCate;
use think\Session;

class Upload extends Common
{
    //源码上传
    public function index()
    {
        $userid = Session::get('user_id');
        $data = \app\index\model\Downmember::with('UserGroup')->where(['userid'=>$userid])->find();
        if(!$userid){
            $this->redirect('http://www.662p.com/login');
        }
        //top导航栏
        $nav = SoftCate::NavCate();
        return $this->fetch('',[
            'nav'=>$nav,
            'current'=>1,
            'data'=>$data
        ]);
    }
}