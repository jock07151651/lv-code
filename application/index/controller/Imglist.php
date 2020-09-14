<?php
namespace app\index\controller;
use app\index\model\Article;
class Imglist extends Common
{
    public function index()
    {
    	$article=new Article();
    	$artRes=$article->getAllArticles(input('cateid'));
    	$cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo(input('cateid'));
    	$this->assign(array(
    		'artRes'=>$artRes,
    		'cateInfo'=>$cateInfo,
    		));
        return view('imglist');
    }
}
