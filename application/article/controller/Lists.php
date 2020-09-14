<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/19 0019
 * Time: 下午 1:58
 */

namespace app\article\controller;

use app\article\model\Cate as CateModel;
use app\article\model\Article as ArticleModel;
use app\article\model\Down;
use think\Controller;
use think\Db;

class Lists extends Common
{
    public function index($id)
    {
        //导航条
        $nav = CateModel::where(['id'=>$id])->find();
        //一级分类
        $cate = CateModel::getList(['pid'=>0,'id'=>['neq',56]],'id desc','20');
        $cate = Db::table('e_cate')->where(['pid'=>0,'id'=>['neq',56]])->order('id desc')->limit(20)->select();
        //文章排行榜
        $ranking = ArticleModel::getList(['adopt'=>1],'click desc','10');
        //最新代码
        $codenew = Down::getList([],'softid desc',10);
        //获取数据
//        $data = ArticleModel::getList(['cateid'=>$id],'id desc','8');
        $snum = substr_count($id,'_');
        if(!$snum){
            $page = 1;
        }else{
            $page = explode('_',$id)[1];
        }
        $data = Db::table('e_article')->where(['cateid'=>$id,'adopt'=>1])->order('id desc')->limit(($page-1)*10,10)->select();
        $count = Db::table('e_article')->where(['cateid'=>$id,'adopt'=>1])->count();
        $page = $this->pageHtml($page,$count,10,'/list/'.explode('_',$id)[0].'_');
        $data = ToArray($data);
        foreach ($data as $key=>$val){
            $data[$key]['content'] = $this->cutstr_html($val['content']);
            $data[$key]['keyword'] = explode(',',$val['keywords']);
        }
        //字数截取
//        $data = ArticleModel::SubstrField($data,'content');
        return $this->fetch('',[
            'nav'=>$nav,
            'data'=>$data,
            'cate'=>$cate,
            'page'=>$page,
            'ranking'=>$ranking,
            'codenew'=>$codenew
        ]);
    }
    public function pageHtml($page,$count,$num=10,$address='/list/')
    {
        //所有数据量
        $number = $count;
        //页码数量
        $pageNum = ceil($number/$num);
        $list = "   <ul class='J_page'>
                       <li><a href='".$address."1.html'>首页</a></li>";
        $list .= ($page==1)?"":"<li><a href='".$address.($page-1).".html'>上一页</a></li>";
        $list .= (($page-2)>0)?"<li><a href='".$address.($page-2).".html'>".($page-2)."</a></li>":'';
        $list .= (($page-1)>0)?"<li><a href='".$address.($page-1).".html'>".($page-1)."</a></li>":'';
        $list .= " <li class='nowpage'><a href='javascript:void(0);'>$page</a></li>";
        $list .= ($page+1)<=$pageNum?"<li><a href='".$address.($page+1).".html'>".($page+1)."</a></li>":'';
        $list .= ($page+2)<=$pageNum?"<li><a href='".$address.($page+2).".html'>".($page+2)."</a></li>":'';
        $list .= ($page+1)<=$pageNum?"<li><a href='".$address.($page+1).".html'>下一页</a></li>":'';
        $list .= "<li><a href='".$address.$pageNum.".html'>尾页</a></li>";
        $list .= "<li><a href='javascript:void(0);'>跳转到</a><input type='text' value='".$page."' class='jump_page' style='width: 30px;text-align:center;color: #fff;background: transparent;outline: none;'><a>页</a><a style='margin-left: 20px;cursor: pointer' onclick='jumpToPage(\"$address\",$pageNum)'>跳转</a></li>";
        $list .=" </ul>";
        return $list;
    }
    public function cutstr_html($string, $sublen='')
    {
        $string = strip_tags($string);
        return trim($string);

    }
}