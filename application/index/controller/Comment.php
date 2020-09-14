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
use think\Request;

class Comment extends Common
{
    //发表评论
    public function index()
    {
        if($this->request->isAjax()){
            if(!session('user_id')){
                return json(['code'=>0,'info'=>'请先登录']);
            }
            $data = $this->request->post();
            if(!$data['content']){
                return json(['code'=>0,'info'=>'请输入评论内容']);
            }
            $data['userid'] = session('user_id');
            $data['pltime'] = date('Y-m-d H:i:s',time());
            $data['c_time'] = time();
            $data['plip'] = $_SERVER['REMOTE_ADDR'];
            $res = Db::table('e_downpl')->insertGetId($data);

            if($res){
                $returnData = Db::table('e_downpl')
                    ->join('e_downmember m','e_downpl.userid = m.userid')
                    ->where(['e_downpl.plid'=>$res])
                    ->field('e_downpl.*,username,userpic')
                    ->find();
                $msg = ['code'=>1,'info'=>'评论成功','data'=>$returnData];
            }else{
                $msg = ['code'=>0,'info'=>'评论失败，请稍后再试！'];
            }
            return json($msg);
        }
    }
    //评论回复
    public function reply()
    {
        if($this->request->isAjax()){
            if(!session('user_id')){
                return json(['code'=>0,'info'=>'请先登录']);
            }
            $data = $this->request->post();
            if(session('user_id')==$data['reply_user_id']){
                return json(['code'=>0,'info'=>'自己不能回复自己的评论']);
            }
            $data['userid'] = session('user_id');
            $data['pltime'] = date('Y-m-d H:i:s',time());
            $data['c_time'] = time();
            $data['plip'] = $_SERVER['REMOTE_ADDR'];
            $res = Db::table('e_downpl')->insertGetId($data);
            if($res){
                $returnData = Db::table('e_downpl')
                    ->join('e_downmember m','e_downpl.userid = m.userid')
                    ->where(['e_downpl.plid'=>$res])
                    ->field('e_downpl.*,username,userpic')
                    ->find();
                $returnData['reply_name'] = Db::table('e_downmember')->where(['userid'=>$returnData['reply_user_id']])->value('username');
                $msg = ['code'=>1,'info'=>'评论成功','data'=>$returnData];
            }else{
                $msg = ['code'=>0,'info'=>'评论失败，请稍后再试！'];
            }
            return json($msg);
        }
    }
    //评论点赞
    public function fabulous($id)
    {
        if($this->request->isAjax()){
            if(!session('user_id')){
                return json(['code'=>0,'info'=>'请先登录']);
            }
            $exist = Db::table('e_fabulous_user')->where(['uid'=>session('user_id'),'cid'=>$id])->find();
            if($exist){
                return json(['code'=>0,'info'=>'你已经点赞了，无需重复点赞']);
            }
            $data = Db::table('e_downpl')->where(['plid'=>$id])->find();

            $res = Db::table('e_downpl')->where(['plid'=>$data['plid']])->update(['fabulous'=>$data['fabulous']+1]);
            if($res){
                Db::table('e_fabulous_user')->insertGetId([
                    'uid'=>session('user_id'),
                    'cid'=>$id,
                    'c_time'=>time()
                ]);
                $msg = ['code'=>1,'info'=>'点赞成功','times'=>$data['fabulous']+1];
            }else{
                $msg = ['code'=>0,'info'=>'点赞失败，请稍后再试！'];
            }
            return json($msg);
        }
    }
    //举报评论
    public function report($id)
    {
        if($this->request->isAjax()){
            if(!session('user_id')){
                return json(['code'=>0,'info'=>'请先登录']);
            }
            $data = $this->request->post();
            if(!$data['content']){
                return json(['code'=>0,'info'=>'请输入反馈内容']);
            }
            $data['uid'] = session('user_id');
            $data['c_time'] = time();
            $data['cid'] = $id;
            $res = Db::table('e_comment_report')->insertGetId($data);
            if($res){
                $msg = ['code'=>1,'info'=>'举报成功'];
            }else{
                $msg = ['code'=>0,'info'=>'举报失败，请稍后再试'];
            }
            return json($msg);
        }
    }
}