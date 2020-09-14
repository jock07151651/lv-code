<?php
/**
 * Created by PhpStorm.
 * User: 华初
 * Date: 2019/3/1
 * Time: 10:43
 */
namespace app\index\controller;
use app\index\model\Soft;
use app\index\model\SoftCate;
use app\index\model\Soft as SoftModel;
use think\Db;
use think\helper\Str;
use think\Session;

class Softlist extends Common
{
    //列表页
    public function index($id='')
    {
        if(!$id){
            $this->redirect('index/index');die;
        }
        $res = strpos(Str::lower($id),'y');
        if($res){
            $id = substr($id,4);
        }
        //统计字符串出现的次数
        $snum = substr_count($id,'_');
        $page = substr($id,strripos($id,'_')+1);
        $route = '/list/'.substr($id,0,strripos($id,'_')+1);
        if($snum==2){
            $sid = substr($id,strpos($id,'_')+1);
            $sid = substr($sid,0,strpos($sid,'_'));
            if($sid){
                //获取分类信息
                $data = SoftCate::where(['classid'=>$sid])->field('classkey,classintro,classid,classname')->find();
                //获取分类下的所有源码产品信息
                $CateIDs = self::getCateIds($sid);
                if(!empty($CateIDs)){
                    //获取三级子分类
                    $thirdCate = DB::table('e_downclass')->where(['classid'=>['in',$CateIDs]])->select();
                    $this->assign('thirdCate',$thirdCate);
                }
                $CateIDs[] = $sid;
                $nid = strpos($id,'_');
                $id = substr($id,0,$nid);
            }else{
                $id = substr($id,0,strpos($id,'_'));
                //获取分类信息
                $data = SoftCate::where(['classid'=>$id])->field('classkey,classintro,classid,classname')->find();
                //获取分类下的所有源码产品信息
                $CateIDs = self::getCateIds($id);
                $CateIDs[] = $id;
            }
            $this->assign('sid',$sid);
        }elseif ($snum == 3){
            //三级分类
            $tid = substr($id,0,strripos($id,'_'));
            $tid = substr($tid,strripos($tid,'_')+1);
            $sid = substr($id,strpos($id,'_')+1);
            $sid = substr($sid,0,strpos($sid,'_'));
            $thirdCateIDs = self::getCateIds($sid);
            //获取三级子分类
            $thirdCate = DB::table('e_downclass')->where(['classid'=>['in',$thirdCateIDs]])->select();
            $this->assign('thirdCate',$thirdCate);
            $this->assign('sid',$sid);
            $this->assign('tid',$tid);
            if($tid){
                //获取分类信息
                $data = SoftCate::where(['classid'=>$tid])->field('classkey,classintro,classid,classname')->find();
                //获取分类下的所有源码产品信息
                $CateIDs = Db::table('e_downclass')->where(['bclassid'=>$tid])->column('classid');
                $CateIDs[] = $tid;
                $nid = strpos($id,'_');
                $id = substr($id,0,$nid);
            }else{
                $id = substr($id,0,strpos($id,'_'));
                //获取分类信息
                $data = SoftCate::where(['classid'=>$sid])->field('classkey,classintro,classid,classname')->find();
                //获取分类下的所有源码产品信息
                $CateIDs = self::getCateIds($sid);
                $CateIDs[] = $sid;
            }
        }else{
            //获取分类信息
            $data = SoftCate::where(['classid'=>$id])->field('classkey,classintro,classid,classname')->find();
            $id = substr($id,0,strpos($id,'_'));
            //获取分类下的所有源码产品信息
            $CateIDs = self::getCateIds($id);
            $CateIDs[] = $id;
        }

        //获取最新下载分类
        $LatestCate = SoftCate::LatestCate();
        //获取热门下载分类
        $HotCate = SoftCate::HotCate();
        //获取所有一级分类
        $firstCate = Db::table('e_downclass')->where(['bclassid'=>0])->select();
        //获取一级分类下面的二级分类
        $secondCate = Db::table('e_downclass')->where(['bclassid'=>$id])->select();


        $list = db('down')->where(['classid'=>['in',$CateIDs],'checked'=>1,'softpic'=>['neq','']])
                                 ->order('softid desc,classid desc')
                                 ->paginate('12',false,['query'=>request()->param()]);
        $list2 = db('down')->where(['classid'=>['in',$CateIDs],'checked'=>1,'softpic'=>['neq','']])
            ->order('softid desc,classid desc')
            ->limit(($page-1)*12,12)->select();
        $count = db('down')->where(['classid'=>['in',$CateIDs],'checked'=>1,'softpic'=>['neq','']])
            ->order('softid desc,classid desc')
           ->count();
        $pageShow = $this->pageHtml($page,$count,12,$route);
        //获取子级类目
        $childCate = SoftCate::childCate($id);

        return $this->fetch('',[
            'secondCate'=>$secondCate,
            'id'=> $id,
            'firstCate'=>$firstCate,
            'childCate'=>$childCate,
            'data'=>$data,
            'soft'=>$list2,
            'latest'=>$LatestCate,
            'hotCate'=>$HotCate,
            'pageShow'=>$pageShow
        ]);
    }
    //软件分类
    public function classification()
    {
        //获取最新下载分类
        $LatestCate = SoftCate::LatestCate();
        //获取热门下载分类
        $HotCate = SoftCate::HotCate();
        //获取一级类目
        $childCate = SoftCate::FirstClass();
        return $this->fetch('',[
            'childCate'=>$childCate,
            'latest'=>$LatestCate,
            'hotCate'=>$HotCate
        ]);
    }
    //获取分类id
    private static function getCateIds($pid)
    {
        static $ids = [];
        $data = db('downclass')->where(['bclassid'=>$pid])->field('classid')->select();
        if(!empty($data)){
            foreach ($data as $key=>$val){
                $ids[] =$val;
                self::getCateIds($val['classid']);
            }
        }
        $cids = [];
        foreach($ids as $key=>$val){
            $cids[] = $val['classid'];
        }
        return $cids;
    }
    //软件收藏
    public function collection()
    {
        if(request()->isAjax()){
            $userid = session('user_id');
            if(!$userid){
                return json(['code'=>0,'info'=>'请您先登录']);
            }
            $softid = request()->post('softid');
            //检测是否已经收藏过
            $exist = db('downfava')->where(['softid'=>$softid,'userid'=>$userid])->find();
            if($exist){
                return json(['code'=>0,'info'=>'您之前已收藏此软件了，无需重复收藏哦。']);
            }
            //添加收藏
            $save = [
                'softid'=>$softid,
                'favatime'=>date('Y-m-d H:i:s',time()),
                'userid'=>$userid,
                'username'=>Session::get('username')
            ];
            //添加收藏
            $res = db('downfava')->insert($save);
            if($res){
                $msg = ['code'=>1,'info'=>'收藏成功'];
            }else{
                $msg = ['code'=>0,'info'=>'收藏失败,请与客服人员联系！'];
            }
            return json($msg);
        }
    }
    public function pageHtml($page,$count,$num=12,$address='/list/')
    {
        //所有数据量
        $number = $count;
        //页码数量
        $pageNum = ceil($number/$num);
        $list = "   <ul class='J_page'>
                       <li><a href='".$address."1.html'>首页</a></li>";
        $list .= ($page==1)?"":"<li><a href='".$address.($page-1).".html'>上一页</a></li>";
        $list .= (($page-2)>0)?"<li><a href='".$address.($page-2).".html'>".($page-2)."</a></li>":'';
        $list .= (($page-1)>0)?"<li><a href='".$address.($page-1).".html'>".($page-1)."</a></li>":'';
        $list .= " <li class='nowpage'><a href='javascript:void(0);'>$page</a></li>";
        $list .= ($page+1)<=$pageNum?"<li><a href='".$address.($page+1).".html'>".($page+1)."</a></li>":'';
        $list .= ($page+2)<=$pageNum?"<li><a href='".$address.($page+2).".html'>".($page+2)."</a></li>":'';
        $list .= ($page+1)<=$pageNum?"<li><a href='".$address.($page+1).".html'>下一页</a></li>":'';
        $list .= "<li><a href='".$address.$pageNum.".html'>尾页</a></li>";
        $list .= "<li><a href='javascript:void(0);'>跳转到</a><input type='text' value='".$page."' class='jump_page' style='width: 30px;text-align:center;color: #fff;background: transparent;outline: none;'><a>页</a><a style='margin-left: 20px;cursor: pointer' onclick='jumpToPage(\"$address\",$pageNum)'>跳转</a></li>";
        $list .=" </ul>";
        return $list;
    }
}