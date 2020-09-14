<?php
/**
 * 萝卜音乐系统
 * @verson 1.0
 * @author: yanghuachu
 * @company: 萝卜科技
 * @license: yy.luobokeji.cn
 * @copyright: 2012 - 2019
 */

namespace app\index\controller;
use think\Db;
use think\Controller;
use app\article\model\Cate as CateModel;
use app\article\model\Article as ArticleModel;
class Member extends Common
{
    //个人主页
    public function index($id='',$nickname='')
    {
        if(!$id){
            echo "<script>history.go(-1)</script>";die;
        }
        $where='userid=' . $id;
        $user_info = Db::table('e_downmember') -> where($where)->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid') -> find();
        $jobs_info = Db::table('e_downmember') -> where($where)->field('a.*,b.job_name')->alias('a')->join('e_jobs b','a.jobid=b.id') -> find();
     //   $user_info = Db::table('e_downmember')->where('userid','=',$id)->find();

        //用户博客
        $artcount = Db::table('e_article')-> where($where) -> count();
        $codecount = Db::table('e_down')-> where($where) -> count();
        $user_article =Db::table('e_article')-> where($where)->order('id desc')->limit('10')->select();
        // $data = Db::table('e_article')->where($where)->order('id desc')->limit('10')->select();
        // $data = ToArray($data);
        // foreach ($data as $key=>$val){
        //     $data[$key]['content'] = $this->cutstr_html($val['content']);
        // }
        //$page = new \Think\Page($artcount, 10);
         //   $follow = $uarticle -> where($where) ->order('id desc')->limit('10') -> select();
            // foreach ($follow as $key => $value) {
            //     $user = Db::table('e_downmember') -> find($value['userid']);
            //     $follow[$key] = $user;
            // }
        
        $artranking = ArticleModel::getList(['adopt'=>1],'click desc','10');   //文章排行
        
     //   $members=Db::table('e_downmember')->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid')->limit('1')->select();
      //  $this->assign('memberres',$members);
        

        $this -> assign('artranking', $artranking);
        $this -> assign('artcount', $artcount);
        $this -> assign('codecount', $codecount);
        $this -> assign('user_article', $user_article);   //用户博客数据集
     //   $this->assign('page',$show);// 赋值分页输出
        $this -> assign('user_info', $user_info);
        $this -> assign('jobs_info', $jobs_info);
        $this -> assign('title', $user_info['username'] . '的主页');
        $this -> assign('keywords', $user_info['username'] . ',662p开源网,开源网,源码下载,开源代码下载,开源项目,IT资源,IT新闻,IT技术');
        $this -> assign('description',$user_info['username'] .'个人相关技术文档，项目代码，博客，图片，技术视频，个人空间，以及个人动态，更多相关内容尽在662p开源网。');
        return view('',[
            
        ]);
    }

    
    //个人主页博客
    public function blog($id='',$nickname =''){
        if(!$id){
            echo "<script>history.go(-1)</script>";die;
        }
        $where='userid=' . $id;
        $user_info = Db::table('e_downmember') -> where($where)->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid') -> find();
        $jobs_info = Db::table('e_downmember') -> where($where)->field('a.*,b.job_name')->alias('a')->join('e_jobs b','a.jobid=b.id') -> find();
     //   $user_info = Db::table('e_downmember')->where('userid','=',$id)->find();

        //用户博客
        $artcount = Db::table('e_article')-> where($where) -> count();
        $codecount = Db::table('e_down')-> where($where) -> count();
        $user_article =Db::table('e_article')-> where($where)->order('id desc')->limit('10')->select();
        // $data = Db::table('e_article')->where($where)->order('id desc')->limit('10')->select();
        // $data = ToArray($data);
        // foreach ($data as $key=>$val){
        //     $data[$key]['content'] = $this->cutstr_html($val['content']);
        // }
        //$page = new \Think\Page($artcount, 10);
         //   $follow = $uarticle -> where($where) ->order('id desc')->limit('10') -> select();
            // foreach ($follow as $key => $value) {
            //     $user = Db::table('e_downmember') -> find($value['userid']);
            //     $follow[$key] = $user;
            // }
        
        $artranking = ArticleModel::getList(['adopt'=>1],'click desc','10');   //文章排行
        
     //   $members=Db::table('e_downmember')->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid')->limit('1')->select();
      //  $this->assign('memberres',$members);
        

        $this -> assign('artranking', $artranking);
        $this -> assign('artcount', $artcount);
        $this -> assign('codecount', $codecount);
        $this -> assign('user_article', $user_article);   //用户博客数据集
     //   $this->assign('page',$show);// 赋值分页输出
        $this -> assign('user_info', $user_info);
        $this -> assign('jobs_info', $jobs_info);
        $this -> assign('title', $user_info['username'] . '的博客');
        $this -> assign('keywords', $user_info['username'] . ',662p开源网,开源网,源码下载,开源代码下载,开源项目,IT资源,IT新闻,IT技术');
        $this -> assign('description',$user_info['username'] .'个人相关技术文档，博客文章，项目代码，博客，图片，技术视频，个人空间，以及个人动态，更多相关内容尽在662p开源网。');
        return view('',[
            
        ]);
    }


     //个人主页博客
    public function code($id=''){
        if(!$id){
            echo "<script>history.go(-1)</script>";die;
        }
        $where='userid=' . $id;
        $user_info = Db::table('e_downmember') -> where($where)->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid') -> find();
        $jobs_info = Db::table('e_downmember') -> where($where)->field('a.*,b.job_name')->alias('a')->join('e_jobs b','a.jobid=b.id') -> find();
     //   $user_info = Db::table('e_downmember')->where('userid','=',$id)->find();

        //用户博客
        $artcount = Db::table('e_article')-> where($where) -> count();
        $codecount = Db::table('e_down')-> where($where) -> count();
        $user_code =Db::table('e_down')-> where($where)->order('softid desc')->limit('10')->select();

        
        $artranking = ArticleModel::getList(['adopt'=>1],'click desc','10');   //文章排行
        
        $this -> assign('artranking', $artranking);
        $this -> assign('artcount', $artcount);
        $this -> assign('codecount', $codecount);
        $this -> assign('user_code', $user_code);
        $this -> assign('user_info', $user_info);
        $this -> assign('jobs_info', $jobs_info);
        $this -> assign('title', $user_info['username'] . '的源码');
        $this -> assign('keywords', $user_info['username'] . ',662p开源网,开源网,源码下载,开源代码下载,开源项目,IT资源,IT新闻,IT技术');
        $this -> assign('description',$user_info['username'] .'个人相关技术文档，源码文件，项目代码，博客，图片，技术视频，个人空间，以及个人动态，更多相关内容尽在662p开源网。');
        return view('',[
            
        ]);
    }

    //个人主页博客
    public function sucai($id=''){
        if(!$id){
            echo "<script>history.go(-1)</script>";die;
        }
        $where='userid=' . $id;
        $user_info = Db::table('e_downmember') -> where($where)->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid') -> find();
        $jobs_info = Db::table('e_downmember') -> where($where)->field('a.*,b.job_name')->alias('a')->join('e_jobs b','a.jobid=b.id') -> find();

        //用户博客
        $artcount = Db::table('e_article')-> where($where) -> count();
        $codecount = Db::table('e_down')-> where($where) -> count();
    

        $this -> assign('artcount', $artcount);
        $this -> assign('codecount', $codecount);
        $this -> assign('user_info', $user_info);
        $this -> assign('jobs_info', $jobs_info);
        $this -> assign('title', $user_info['username'] . '的素材');
        $this -> assign('keywords', $user_info['username'] . ',662p开源网,开源网,源码下载,开源代码下载,开源项目,IT资源,IT新闻,IT技术');
        $this -> assign('description',$user_info['username'] .'个人相关技术文档,素材文件，项目代码，博客，图片，技术视频，个人空间，以及个人动态，更多相关内容尽在662p开源网。');
        return view('',[
            
        ]);
    }


}