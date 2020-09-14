<?php
namespace app\admin\controller;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;
use app\admin\controller\Common;
use think\Db;

class Article extends Common
{

    public function lst($keyword='',$classid='',$page=''){
        $where = [];
        if($keyword){
            $where['title'] = ['like',"%$keyword%"];
        }
        if($classid){
            $where['cateid'] = $classid;

        }

        $artres=db('article')->field('a.*,b.catename')->alias('a')->where($where)->join('e_cate b','a.cateid=b.id')->order('a.id desc')->paginate(10,false,['query'=>request()->param()]);
        $this->assign('artres',$artres);
        $cate = Db::table('e_cate')->select();
        $this->assign('cate',$cate);
        return view();
    }

    public function add(){
        //查询水晶作者
        $author = Db::table('e_downmember')->where(['userid'=>['in',[
            40434,40435,40436,40437,40438,40439,40440,40441,40442,40443,40444
        ]]])->select();
        if(request()->isPost()){
            $data=input('post.');
            $data['time']=time();
            $validate = \think\Loader::validate('Article');
            if(!$validate->scene('add')->check($data)){
                $this->error($validate->getError());
            }
            $article=new ArticleModel;
            $data['adopt'] = 1;
            if($article->save($data)){
                $this->success('添加文章成功',url('lst'));
            }else{
                $this->error('添加文章失败！');
            }
            return;
        }
        $cate=new CateModel();
        $cateres=$cate->catetree();
        $this->assign('author',$author);
        $this->assign('cateres',$cateres);
        return view();
    }

    public function edit(){
        //查询水晶作者
        $author = Db::table('e_downmember')->where(['userid'=>['in',[
            40434,40435,40436,40437,40438,40439,40440,40441,40442,40443,40444
        ]]])->select();
        if(request()->isPost()){
            $data=input('post.');
            $validate = \think\Loader::validate('Article');
            if(!$validate->scene('edit')->check($data)){
                $this->error($validate->getError());
            }
            $article=new ArticleModel;
            $save=$article->update($data);
            if($save){
                $this->success('修改文章成功！',url('lst'));
            }else{
                $this->error('修改文章失败！');
            }
            return;
        }
        $cate=new CateModel();
        $cateres=$cate->catetree();
        $arts=db('article')->where(array('id'=>input('id')))->find();
        $this->assign(array(
            'author'=>$author,
            'cateres'=>$cateres,
            'arts'=>$arts,
            ));
        return view();
    }

    public function del(){
        if(ArticleModel::destroy(input('id'))){
            $this->success('删除文章成功！',url('lst'));
        }else{
            $this->error('删除文章失败！');
        }
    }
    //文章审核
    public function examine($id)
    {
        if(!$id){
            echo "<script>history.go(-1);</script>";
        }
        if($this->request->isPost()){
            $res = Db::table('e_article')->where(['id'=>$id])->update(['adopt'=>1]);
            if($res){
                $msg = ['code'=>1,'info'=>'审核通过'];
            }else{
                $msg = ['code'=>0,'info'=>'审核失败，请稍后再试'];
            }
            return json($msg);
        }
    }


 



   

	












}
