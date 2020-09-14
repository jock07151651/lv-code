<?php
namespace app\index\controller;

use app\index\model\Down;
use app\index\model\Soft;
use app\index\model\SoftCate;
use think\Db;
class Index extends Common
{
    public function index()
    {
    	//首页最新文章调用
    	$articleM=new \app\index\model\Article();
    	$newArtiecleRes=$articleM->getNewArticle();
        $siteHotArt=$articleM->getSiteHot();
        $recArt=$articleM->getRecArt();
        //轮播
        $banner = Db::table('e_down')->where(['isgood'=>1])->order('softid desc')->limit(5)->select();
        //源码更新栏目
        $CodeUpdateData = Down::CodeUpdate();
        //获取推荐栏目
        $cateM=new \app\index\model\Cate();
        $recIndex=$cateM->getRecIndex();
        //编辑推荐
        $recommend = Soft::RecommendSoft();
        //会员信息数据
//        $downmembers=db('downmember')->select();
        //友情链接数据
        $linkRes=db('downlink')->order('myorder desc')->select();
        //获取table源码
        $TableCode = $this->getCodeList();
    	$this->assign(array(
    	    'banner'=>$banner,
    	    'CodeUpdateData'=>$CodeUpdateData,
    		'newArtiecleRes'=>$newArtiecleRes,
            'siteHotArt'=>$siteHotArt,
//            'downmembers'=>$downmembers,
            'linkRes'=>$linkRes,
            'recArt'=>$recArt,
            'recIndex'=>$recIndex,
            'recommend'=>$recommend,
            'tableList'=>$TableCode,
    		));
        return view();
    }
    //搜索
    public function search()
    {
        //搜索关键字
        $name = isset($_GET['keywords'])?$_GET['keywords']:"";
        $classid = isset($_GET['classid'])?$_GET['classid']:"";
        $sclassid = isset($_GET['sclassid'])?$_GET['sclassid']:"";
        //获取分类以及分类下面的子分类
        $classids = [];
        if($classid){
            $classids = Db::table('e_downclass')->where(['bclassid'=>$classid])->column('classid');
            $classids[] = $classid;
            foreach ($classids as $key=>$val){
                $tid = Db::table('e_downclass')->where(['bclassid'=>$val])->select();
                if($tid){
                    foreach ($tid as $k=>$v){
                        $classids[] = $v['classid'];
                    }
                }
            }
            $where['classid'] = ['in',$classids];
            $secondClass = Db::table('e_downclass')->where(['bclassid'=>$classid])->select();
            $this->assign('secondClass',$secondClass);
            if($sclassid){
                $classids = [];
                $classids[] = $sclassid;
                $where['classid'] = ['in',$classids];
            }
        }
        $where['softname|softsay'] = ['like',"%$name%"];
        //获取所有一级分类
        $firstCate = Db::table('e_downclass')->where(['bclassid'=>0])->select();
        $list = Db::table('e_down')->where($where)
                                         ->order('count_all desc,softid desc')
                                         ->paginate('12',false,['query'=>request()->param()]);
        $keywordExist = Db::table('e_downsearch')->where(['keyboard'=>trim($name)])->find();
        if($keywordExist){
            $ips = explode(',',$keywordExist['keyboard']);
            $nowIP = $_SERVER["REMOTE_ADDR"];
            if(!in_array($nowIP,$ips)){
                Db::table('e_downsearch')->where(['keyboard'=>trim($name)])->setInc('result_num');
            }
        }else{
            Db::table('e_downsearch')->insert([
                'keyboard'=>$name,
                'searchtime'=>time(),
                'result_num'=>1,
                'searchip'=>$_SERVER["REMOTE_ADDR"]
            ]);
        }
        return $this->fetch('',[
            'firstCate'=>$firstCate,
            'soft'=>$list,
        ]);
    }
    //获取多个源码
    private function getCodeList()
    {
        //模板下载
        $ids = [];
        $ids = $this->getChildIDs(2958);
        $TemplateData = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //插件下载
        $ids = [];
        $ids = $this->getChildIDs(2957);
        $chajianData = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //wap源码下载
        $ids = [];
        $ids = $this->getChildIDs(2976);
        $wapCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Asp源码下载
        $ids = [];
        $ids = $this->getChildIDs(1);
        $AspCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //.net源码下载
        $ids = [];
        $ids = $this->getChildIDs(2);
        $NetCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //PHP源码下载
        $ids = [];
        $ids = $this->getChildIDs(4);
        $PHPCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Jsp源码下载
        $ids = [];
        $ids = $this->getChildIDs(5);
        $JspCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Js源码下载
        $ids = [];
        $ids = $this->getChildIDs(6);
        $JsCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Flash源码下载
        $ids = [];
        $ids = $this->getChildIDs(7);
        $FlashCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Java源码下载
        $ids = [];
        $ids = $this->getChildIDs(8);
        $JavaCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //C++源码下载
        $ids = [];
        $ids = $this->getChildIDs(9);
        $CCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //C#源码下载
        $ids = [];
        $ids = $this->getChildIDs(139);
        $CjCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //VB源码下载
        $ids = [];
        $ids = $this->getChildIDs(10);
        $VBCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //Delphi源码下载
        $ids = [];
        $ids = $this->getChildIDs(140);
        $DelphiCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //HTML5源码下载
        $ids = [];
        $ids = $this->getChildIDs(13);
        $HTMLCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //android源码下载
        $ids = [];
        $ids = $this->getChildIDs(11);
        $androidCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //ios源码下载
        $ids = [];
        $ids = $this->getChildIDs(12);
        $iosCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        //WP源码下载
        $ids = [];
        $ids = $this->getChildIDs(14);
        $WPCode = Db::table('e_down')->order('softid desc')->limit(10)->where(['classid'=>['in',$ids],'checked'=>1])->select();
        return [
            'TemplateData'=>$TemplateData,
            'chajianData'=>$chajianData,
            'wapCode'=>$wapCode,
            'AspCode'=>$AspCode,
            'PHPCode'=>$PHPCode,
            'NetCode'=>$NetCode,
            'JspCode'=>$JspCode,
            'JsCode'=>$JsCode,
            'FlashCode'=>$FlashCode,
            'JavaCode'=>$JavaCode,
            'CCode'=>$CCode,
            'CjCode'=>$CjCode,
            'VBCode'=>$VBCode,
            'DelphiCode'=>$DelphiCode,
            'HTMLCode'=>$HTMLCode,
            'androidCode'=>$androidCode,
            'iosCode'=>$iosCode,
            'WPCode'=>$WPCode
        ];
    }
    //获取子级分类id
    private function getChildIDs($id)
    {
        //查询数据信息
        $data = Db::table('e_downclass')->where(['classid'=>$id])->find();
        $ids = explode('|',$data['sonclass']);
        $ids = array_filter($ids);
        $ids[] = $id;
        return $ids;
    }
}
