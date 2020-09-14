<?php
namespace app\index\controller;
use app\index\model\Article;
use app\index\model\Down;

class Artlist extends Common
{
    public function index()
    {
    	$article=new Article();
        $cateid=input('cateid');
    	$artRes=$article->getAllArticles($cateid);
    	$hotRes=$article->getHotRes($cateid);
        $cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo($cateid);
    	$this->assign(array(
    		'artRes'=>$artRes,
    		'hotRes'=>$hotRes,
            'cateInfo'=>$cateInfo,
    		));
        return view('artlist');
    }
}
