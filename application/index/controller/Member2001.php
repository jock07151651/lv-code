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
use think\Db;
class Member extends Common
{
    //个人主页
    public function index($id)
    {
       
       if($id){
        $user_info =db('downmember')->where(['userid'=>$id])->find();
        // $profile = M("downmember");
        // $user_info = $profile->where("userid=".$id)->find();
        $this -> assign('user_info', $user_info);
        $this -> assign('title', $user_info['username'] . '的个人主页');
        $this -> assign('keywords', $user_info['username'] . ',662p开源网,开源网,源码下载,开源代码下载,开源项目,IT资源,IT新闻,IT技术');
        $this -> assign('description',$user_info['username'] .'个人相关技术文档，项目代码，博客，图片，技术视频，个人空间，以及个人动态，更多相关内容尽在662p开源网。');
       $this -> display();
       }
    
     }
  
}