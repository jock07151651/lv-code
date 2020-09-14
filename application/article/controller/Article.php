<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2019/4/22 0022

 * Time: 上午 9:51

 */



namespace app\article\controller;



use app\article\model\Article as ArticleModel;

use app\article\model\Cate;
use app\article\model\Down;
use think\Controller;



class Article extends Common

{

    //列表页

    public function index($id='')

    {

        return $this->fetch();

    }

    //文章页

    public function detail($id)

    {

        //一级分类

        $cate = Cate::getList(['pid'=>0,'id'=>['neq',56]],'id asc','20');

        //文章详情

        $data = ArticleModel::where(['id'=>$id])->find();
        ArticleModel::where(['id'=>$id])->setField('click',($data->click)+1);
        //文章不存在

        if(!$data){

            $this->redirect('/404');

        }

        //文章排行榜
        $ranking = ArticleModel::getList(['adopt'=>1],'click desc','10');
        //最新代码
        $codenew = Down::getList([],'softid desc',10);
        //最新文章
        $latestArticle = ArticleModel::getList(['adopt'=>1]);
        //关键词

        $data['keywords'] = ToArray($data)['keywords']?(ToArray($data)['keywords']):($this->getKeywords(ToArray($data)['content']));

        //描述

        $data['describe'] = mb_substr(strip_tags($data['content']),0,110,'utf-8');

        //上一篇

        $last = ArticleModel::where(['id'=>['lt',$id],'adopt'=>1,'cateid'=>$data['cateid']])->order('id desc')->find();

        //下一篇

        $next = ArticleModel::where(['id'=>['gt',$id],'adopt'=>1,'cateid'=>$data['cateid']])->order('id desc')->find();

        //相关文章列表

        $relevant = ArticleModel::where(['cateid'=>$data['cateid'],'adopt'=>1,'thumb'=>['neq',''],'id'=>['neq',$id]])->order('click desc')->paginate(8);
        return $this->fetch('',[

            'data'=>$data,

            'relevant'=>$relevant,

            'last'=>$last,

            'cate'=>$cate,

            'latestArticle'=>$latestArticle,

            'next'=>$next,

            'ranking'=>$ranking,

            'codenew'=>$codenew

        ]);

    }

    private function substrField($data,$field,$num)

    {

        if(is_object($data)){

            $data = ToArray($data);

        }

        $content = preg_replace('/\<.+?\>/','',$data[$field]);

        $content = str_replace('&nbsp;','',$content);

        $data[$field] = mb_substr($content,0,$num,'UTF-8');

        return $data;

    }

    //获取一段文字内的关键词

    private function getKeywords($content)

    {

        if(!$content){

            return '';

        }

        import('keywords.pscws4',VENDOR_PATH,'.class.php');



        $pscws = new \PSCWS4();

        $pscws->set_dict('./vendor/keywords/scws/dict.utf8.xdb');

        $pscws->set_rule('./vendor/keywords/scws/rules.utf8.ini');

        $pscws->set_ignore(true);

        $pscws->send_text($content);

        $words = $pscws->get_tops(8);

        $tags = array();

        foreach ($words as $val) {

            $tags[] = $val['word'];

        }

        $pscws->close();

        return implode(',',$tags);

    }



}