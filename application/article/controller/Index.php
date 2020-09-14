<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2019/4/19 0019

 * Time: 下午 1:58

 */



namespace app\article\controller;
use app\article\model\Article as ArticleModel;
use app\article\model\Cate;
use app\article\model\Down;
use app\article\model\Downlink;
use app\article\model\Common as CommonModel;
use app\index\model\Collect;
use think\Controller;



class Index extends Common
{
    public function index()
    {
        //轮播
        $ban = ArticleModel::getList(['rec'=>1],'id desc',3);
        //站长推荐
        $recommend = Down::getList(['isgood'=>1],'softid desc',5);
        //获取最新源码
        $latest = ArticleModel::getList(['adopt'=>1,'index_rec'=>1,'status'=>1],'id desc',10);
        //获取推荐分类
        $cate = Cate::getList(['rec_index'=>1],'id desc');
        //获取编程语言
        $proLanguage = ArticleModel::getList(['sort'=>40,'adopt'=>1,'status'=>1,'index_rec'=>1],'id desc',10);
        //友情链接
        $friendLink = Downlink::field('lurl,lname')->select();
        //特效部分
        $effect = Down::getSoftByClassID(config('index.one'));
        //插件部分
        $plug = Down::getSoftByClassID(config('index.three'));
        //模板部分
        $template = Down::getSoftByClassID(config('index.two'));
        //源码推荐
        $rec = Down::getList([],'softid desc',12);
        //Asp源码
        $first = Down::getSoftByClassID(config('index.first'));
        //PHP源码
        $second = Down::getSoftByClassID(config('index.second'));
        //.net源码
        $third = Down::getSoftByClassID(config('index.third'));
        //其他源码
        $fourth = Down::getSoftByClassID(config('index.fourth'));

        //Asp源码
        $list21 = Down::getSoftByClassID(config('index.list21'));
        //PHP源码
        $list22 = Down::getSoftByClassID(config('index.list22'));
        //.net源码
        $list23 = Down::getSoftByClassID(config('index.list23'));
        //其他源码
        $list24 = Down::getSoftByClassID(config('index.list24'));
        return $this->fetch('',[
            'latest'=>$latest,
            'ban'=>$ban,
            'friendLink'=>$friendLink,
            'cate'=>$cate,
            'recommend'=>$recommend,
            'prolanguage'=>$proLanguage,
            'effect'=>$effect,
            'plug'=>$plug,
            'template'=>$template,
            'rec'=>$rec,
            'first'=>$first,
            'second'=>$second,
            'third'=>$third,
            'fourth'=>$fourth,
            'list21'=>$list21,
            'list22'=>$list22,
            'list23'=>$list23,
            'list24'=>$list24
        ]);
    }
    public function index_midd()
    {
        return $this->fetch();
    }
}