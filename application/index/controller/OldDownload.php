<?php

/**

 * Created by PhpStorm.

 * User: 华初

 * Date: 2019/2/27

 * Time: 14:50

 */



namespace app\index\controller;

use app\index\model\Down;

use app\index\model\SoftCate;

use think\Db;

use think\helper\Str;

use think\queue\job\Topthink;

use think\Session;

use app\index\model\Downurl;



class Download extends Common

{

    public function test()

    {

        $data = Db::table('e_down')->where(['softid'=>136])->find();

        $path = explode('::::::',$data['downpath']);

        var_dump($path);die;

    }

    //源码下载

    public function detail($id)

    {

        //下载排行

        $HotCate = SoftCate::HotCate();

        //获取最新下载分类

        $LatestCate = SoftCate::LatestCate();

        //查询数据

        $data = Down::with('CodeClass')->find($id);
        if(empty($data)){
            $this->redirect('/missing');
        }
        $data = json_decode(json_encode($data),true);

        $description = $data['describes']?clearHtmlStr($data['describes']):$this->clearHtmlStr($data['softsay']);

        $data['description'] = mb_substr($description,0,120,'UTF-8');

        $data['softsay'] = htmlentities($data['softsay'] ,ENT_QUOTES,"UTF-8");

        //获取源码类型

        $data['classgroup'] = $this->ParentClase($data['CodeClass']['bclassid'],$data['CodeClass']['classid']);

        //获取同级类目以及子类（下载分类）

        $downCate = SoftCate::childCate(11);

        //获取所有下载路径

        $downPath = Downurl::getDownPath(['urlid'=>14]);

        $data['path'] = $data['demo'];

        $data['softsay'] = htmlspecialchars_decode($data['softsay']);

        return $this->fetch('', [

            'data' => $data,

            'downCate'=>$downCate,

            'hotCate'=>$HotCate,

            'LatestCate'=>$LatestCate,

            'downPath'=>$downPath

        ]);

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

                return json(['code'=>0,'info'=>'请先登录！','url'=>'/login']);

            }

            //获取软件信息

            $soft = db('down')->where(['softid'=>$data['softid']])->find();

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

                return json(['code'=>1,'info'=>'点数足够','url'=>$downPath]);

            }

            return json(['code'=>1,'info'=>'无需点数','url'=>$downPath]);

        }

    }

    //获取软件的下载路径

    private function getDownPath($softid,$pathid)

    {

        $soft = Db::table('e_down')->where(['softid'=>$softid])->find();

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

    //下载路径数组化

    private function urlToArray($data)

    {

        $path = explode('::::::',$data['downpath']);

        return $path['1'];

    }

}