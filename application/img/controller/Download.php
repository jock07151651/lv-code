<?php

/**

 * Created by PhpStorm.

 * User: 华初

 * Date: 2019/2/27

 * Time: 14:50

 */



namespace app\img\controller;

use app\img\model\Img;

use app\img\model\Imgclass as SoftCate;

use think\Db;

use think\helper\Str;

use app\article\model\Article as ArticleModel;

use think\queue\job\Topthink;

use think\Session;

use app\index\model\Downurl;



class Download extends Common

{

    public function test()

    {

        $data = Db::table('e_img')->where(['softid'=>136])->find();

        $path = explode('::::::',$data['downpath']);

        var_dump($path);die;

    }

    //源码下载

    public function detail($id)

    {
        //源码点击量加一
        Db::table('e_img')->where(['softid'=>$id])->setInc('count_all',1);

        //下载排行

        $HotCate = SoftCate::HotCate();

        //获取最新下载分类

        $LatestCate = SoftCate::LatestCate();

        //最新文章
        $latestArticle = ArticleModel::getList();

        //查询数据

        $data = Img::with('CodeClass')->find($id);
        if(empty($data)){
            $this->redirect('/missing');
        }
        $data = json_decode(json_encode($data),true);

        $description = $data['describes']?($this->clearHtmlStr($data['describes'])):($this->clearHtmlStr($data['softsay']));

        $data['description'] = mb_substr($description,0,120,'UTF-8');

        $data['softsay'] = htmlentities($data['softsay'] ,ENT_QUOTES,"UTF-8");

        //获取源码类型

        $data['classgroup'] = $this->ParentClase($data['CodeClass']['bclassid'],$data['CodeClass']['classid']);

        //获取同级类目以及子类（下载分类）

        $downCate = SoftCate::childCate($data['classid']);
        //获取所有下载路径

        $downPath = Downurl::getDownPath(['urlid'=>14]);

        $data['path'] = $data['demo'];

        $data['softsay'] = htmlspecialchars_decode($data['softsay']);
        //获取顶级分类名称
        $data['classname'] = $this->getTopCate($data['classid']);
        //相关模板
        $relevant = Img::getList(['classid'=>$data['classid'],'softid'=>['neq',$id]],'softid desc',8);
        //上一篇
        $prev = Img::getOne(['softid'=>['lt',$id]],'softid desc');
        //下一篇
        $next = Img::getOne(['softid'=>['gt',$id]],'');
        $one  = Img::getOne([],'softid desc');
        return $this->fetch('index/detail', [
            'relevant'=>$relevant,
            'prev'=>$prev,
            'next'=>$next,
            'data' => $data,
            'latestArticle'=>$latestArticle,
            'downCate'=>$downCate,

            'hotCate'=>$HotCate,

            'LatestCate'=>$LatestCate,

            'downPath'=>$downPath

        ]);

    }
    //获取顶级分类的名称
    private function getTopCate($id)
    {
        $data = Db::table('e_downclass')->where(['classid'=>$id])->find();
        if($data['featherclass']){
            $pid = explode('|',$data['featherclass']);
            $pid = array_filter($pid);
            $pid = array_values($pid);
            $data = Db::table('e_downclass')->where(['classid'=>$pid[0]])->find();
        }
        return $data['classname'];
    }

    private function clearHtmlStr($str)

    {

        $str = trim($str); //清除字符串两边的空格

        $str = strip_tags($str,""); //利用php自带的函数清除html格式

        $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。

        $str = preg_replace("/\r\n/","",$str);

        $str = preg_replace("/\r/","",$str);

        $str = preg_replace("/\n/","",$str);

        $str = preg_replace("/ /","",$str);

        $str = preg_replace("/  /","",$str);  //匹配html中的空格

        return trim($str); //返回字符串

    }

    //软件下载

    public function DownSoft()

    {

        if(request()->isAjax()){
            $data = request()->post();

            $userid = Session::get('user_id');

            if(!$userid){

                return json(['code'=>0,'info'=>'请先登录！','url'=>'http://www.662p.com/login']);

            }

            //获取软件信息

            $soft = db('img')->where(['softid'=>$data['softid']])->find();

            //获取软件下载路径

            $downPath = $this->getDownPath($soft['softid'],$data['pathid']);

            if($soft['downfen']){

                //查询积分是否足够

                $menber = db('Downmember')->where(['userid'=>$userid])->find();

                if($menber['downfen']<$soft['downfen']){

                    return json(['code'=>0,'info'=>'点数不足，请先充值！','url'=>'/buygroup']);

                }

                //减去用户的积分

                $res = Db::table('e_downmember')->where(['userid'=>$userid])->setDec('downfen',$soft['downfen']);

                if(!$res){

                    return json(['code'=>0,'info'=>'未知错误，请与客服人员联系！']);

                }
                $user = Db::table('e_downmember')->where(['userid'=>\session('user_id')])->find();
                Db::table('e_downbuy_bak')->insert([
                    'username'=>$user['username'],
                    'userid'=>\session('user_id'),
                    'card_no'=>$soft['downfen'].'点数',
                    'buytime'=>date('Y-m-d H:i',time()),
                    'downfen'=>$soft['downfen'],
                    'soft_id'=>$data['softid']
                ]);
                return json(['code'=>1,'info'=>'点数足够','downfen'=>$soft['downfen'],'url'=>$downPath]);

            }

            return json(['code'=>1,'info'=>'无需点数','url'=>$downPath]);

        }

    }

    //获取软件的下载路径

    private function getDownPath($softid,$pathid)

    {

        $soft = Db::table('e_img')->where(['softid'=>$softid])->find();

        $downurl = Db::table('e_downurl')->where(['urlid'=>$pathid])->find();

        $softPath = explode('::::::',$soft['downpath']);

        //判断是否是百度网盘下载

        $preg = "/^http(s)?:\\/\\/.+/";



        if(isset($softPath['1'])){

            if(preg_match($preg,$softPath['1'])){

                return $softPath['1'];

            }

            return $downurl['url'].$softPath['1'];

        }else{

            return $soft['downpath'];

        }

    }

    //递归获取上级

    private function ParentClase($cid,$selfid,$tip=1)

    {

        static $arr = [];

        if($tip==1){

            $self = db('downclass')->field('classname,bclassid,classid')->where(['classid'=>$selfid])->find();

            $arr[] = $self;

        }

        $res = db('downclass')->field('classname,bclassid,classid')->where(['classid'=>$cid])->find();

        if($res){

            $arr[] = $res;

            $this->ParentClase($res['bclassid'],$selfid,2);

        }

        asort($arr);



        return $arr;

    }
    //记录用户下载记录
    public function downRecord()
    {
        if(request()->isAjax()){
            $softid = request()->post('softid');
            $soft = Db::table('e_img')->where(['softid'=>$softid])->find();
            $user = Db::table('e_downmember')->where(['userid'=>session('user_id')])->find();

            $data = [
                'softname'=>$soft['softname'],
                'softid'=>$soft['softid'],
                'userid'=>$user['userid'],
                'username'=>$user['username'],
                'downtime'=>date('Y-m-d H:i:s',time()),
                'downfen'=>$soft['downfen'],
                'truetime'=>time()
            ];
            $res = Db::table('e_downdown')->insertGetId($data);
            if($res){
                $msg = ['code'=>1,'info'=>'记录成功'];
            }else{
                $msg = ['code'=>0,'info'=>'记录失败'];
            }
            return json($msg);
        }
    }
    //下载路径数组化

    private function urlToArray($data)

    {

        $path = explode('::::::',$data['downpath']);

        return $path['1'];

    }

}