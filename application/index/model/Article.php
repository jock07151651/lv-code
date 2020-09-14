<?php
namespace app\index\model;
use think\Model;
use app\index\model\Cate;
class Article  extends Model
{
    public function getAllArticles($cateid){
        $cate=new Cate();
        $allCateID=$cate->getchilrenid($cateid);
        $artRes=db('article')->where("cateid IN($allCateID)")->order('id desc')->paginate(2);
        return $artRes;
    }

    public function getHotRes($cateid){
        $cate=new Cate();
        $allCateID=$cate->getchilrenid($cateid);
        $artRes=db('article')->where("cateid IN($allCateID)")->order('click desc')->limit(5)->select();
        return $artRes;
    }

    public function getSerHot(){
       $artRes=db('article')->order('click desc')->limit(5)->select();
        return $artRes; 
    }

    public function getSiteHot(){
        $siteHotArt=$this->field('id,title,thumb')->order('click desc')->limit(5)->select();
        return $siteHotArt;
    }

    public function getNewArticle(){
        $newArtiecleRes=db('article')->field('a.id,a.title,a.author,a.desc,a.thumb,a.click,a.zan,a.time,c.catename')->alias('a')->join('e_cate c','a.cateid=c.id')->order('a.id desc')->limit(10)->select();
        return $newArtiecleRes;
    }

    public function getRecArt(){
        $recArt=$this->where('rec','=',1)->field('id,title,thumb')->order('id desc')->limit(4)->select();
        return $recArt;
    }
}
