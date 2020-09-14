<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7 0007
 * Time: 上午 11:29
 */

namespace app\index\controller;

use app\index\model\Collect as CollectModel;
use app\index\model\SoftCate;
use think\Session;

class Collect extends Common
{
    public function index()
    {
        $userid = Session::get('user_id');
        if(!$userid){
            $this->error('请您先登录','/login');
        }
        //获取用户收藏夹信息
        $data = CollectModel::CollectList($userid,$listRow = 9);
        //统计收藏条数
        $count = CollectModel::CollectCount($userid);
        //top导航栏
        $nav = SoftCate::NavCate();
        return $this->fetch('',[
            'nav'=>$nav,
            'data'=>$data,
            'count'=>$count
        ]);
    }
    //删除收藏
    public function delete()
    {
        $data = request()->post();
        if(empty($data['favaid'])){
           return json(['code'=>0,'info'=>'删除失败，请选择需要删除的收藏信息！']);
        }
        $res = db('downfava')->where(['favaid'=>['in',$data['favaid']]])->delete();
        if($res){
            $msg = ['code'=>1,'info'=>'删除成功'];
        }else{
            $msg = ['code'=>0,'info'=>'删除失败，请稍后再试！'];
        }
        return json($msg);
    }
}