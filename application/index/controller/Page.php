<?php
namespace app\index\controller;

class Page extends Common
{
    public function index()
    {
    	$cates=db('cate')->find(input('cateid'));
    	$cate=new \app\index\model\Cate();
        $cateInfo=$cate->getCateInfo(input('cateid'));
    	$this->assign(array(
    		'cates'=>$cates,
    		'cateInfo'=>$cateInfo,
    		));
        return view('page');
    }
}
