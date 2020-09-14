<?php
namespace app\index\controller;
use app\index\model\SoftCate;
use think\Request;
class Article extends Common
{
    public function index()
    {
    	$artid=input('artid');
    	db('article')->where(array('id'=>$artid))->setInc('click');
    	$articles=db('article')->find($artid);
    	$article= new \app\index\model\Article();
    	$hotRes=$article->getHotRes($articles['cateid']);
    	$this->assign(array(
    		'articles'=>$articles,
    		'hotRes'=>$hotRes,
            'artid'=>$artid,
    		));
        return view('article');
    }
    //用户上传文章
    public function userUpload($id)
    {

        //下载排行

        $HotCate = SoftCate::HotCate();
        
        //获取最新下载分类

        $LatestCate = SoftCate::LatestCate();

        //最新文章
        $latestArticle = ArticleModel::getList();

        //查询数据

        $data = Down::with('CodeClass')->find($id);
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
        $relevant = Down::getList(['classid'=>$data['classid'],'softid'=>['neq',$id]],'softid desc',8);
        //上一篇
        $prev = Down::getOne(['softid'=>['lt',$id]],'softid desc');
        //下一篇
        $next = Down::getOne(['softid'=>['gt',$id]],'');
        $one  = Down::getOne([],'softid desc');
        return $this->fetch('article.upload', [
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
}
