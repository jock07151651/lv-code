<?php
/**
 * 萝卜音乐系统
 * @verson 1.0
 * @author: yanghuachu
 * @company: 萝卜科技
 * @license: yy.luobokeji.cn
 * @copyright: 2012 - 2019
 */

namespace app\admin\controller;


use think\Db;

class Comment extends Common
{
    //软件评论
    public function index($keyword='')
    {

        $data = Db::table('e_downpl')
                ->join('e_down d','d.softid = e_downpl.softid')
                ->join('e_downmember m','m.userid = e_downpl.userid')
                ->field('e_downpl.*,d.softname,m.username')
                ->order('plid desc')
                ->paginate(10);
        if($keyword){
            $data = Db::table('e_downpl')
                ->join('e_down d','d.softid = e_downpl.softid')
                ->join('e_downmember m','m.userid = e_downpl.userid')
                ->field('e_downpl.*,d.softname,m.username')
                ->order('plid desc')
                ->where(['e_downmember.username'=>['like',"%$keyword%"]])
                ->whereOr(['e_downpl.content'=>['like',"%$keyword%"]])
                ->whereOr(['e_down.softname'=>['like',"%$keyword%"]])
                ->paginate(10);
        }
        $report = Db::table('e_comment_report')->count();
        return view('',[
            'data'=>$data,
            'report'=>$report
        ]);
    }
    //评论举报列表
    public function report($keyword='')
    {
        $data = Db::table('e_comment_report')
            ->alias('c')
            ->join('e_downpl p','p.plid = c.cid')
            ->join('e_down d','d.softid = p.softid')
            ->join('e_downmember m','m.userid = c.uid')
            ->field('c.c_time,c.id,c.cid,c.content,p.content as pcontent,d.softname,m.username')
            ->order('c.id desc')
            ->paginate(10);
        if($keyword){
            $data = Db::table('e_comment_report')
                ->alias('c')
                ->join('e_downpl p','p.plid = c.cid')
                ->join('e_down d','d.softid = p.softid')
                ->join('e_downmember m','m.userid = c.uid')
                ->field('c.c_time,c.id,c.cid,c.content,p.content as pcontent,d.softname,m.username')
                ->order('c.id desc')
                ->where(['e_downmember.username'=>['like',"%$keyword%"]])
                ->whereOr(['e_downpl.content'=>['like',"%$keyword%"]])
                ->whereOr(['c.content'=>['like',"%$keyword%"]])
                ->whereOr(['e_down.softname'=>['like',"%$keyword%"]])
                ->paginate(10);
        }
        return view('',[
            'data'=>$data
        ]);
    }
    //删除评论内容
    public function delComment($id)
    {
        if ($this->request->isAjax()){
            if(!$id){
                return json(['code'=>0,'info'=>'删除失败，数据id不存在']);
            }
            $res = Db::table('e_downpl')->where(['plid'=>$id])->whereOr(['pid'=>$id])->delete();
            if($res){
                //删除点赞信息
                Db::table('e_fabulous_user')->where(['cid'=>$id])->delete();
                //删除评论举报信息
                Db::table('e_comment_report')->where(['cid'=>$id])->delete();
                $msg = ['code'=>1,'info'=>'删除成功'];
            }else{
                $msg = ['code'=>0,'info'=>'删除失败，请稍后再试'];
            }
            return json($msg);
        }
    }
    //删除评论举报信息
    public function delReport($id)
    {
        if($this->request->isAjax()){
            if(!$id){
                return json(['code'=>0,'info'=>'数据id不存在，请稍后再试']);
            }
            $res = Db::table('e_comment_report')->where(['id'=>$id])->delete();
            if($res){
                $msg = ['code'=>1,'info'=>'删除成功'];
            }else{
                $msg = ['code'=>0,'info'=>'删除失败，请稍后再试！'];
            }
            return json($msg);
        }
    }
}