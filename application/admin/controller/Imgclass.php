<?php
namespace app\admin\controller;
use app\admin\model\Imgclass as ImgclassModel;
use app\admin\model\Article as ArticleModel;
use app\admin\controller\Common;
class Imgclass extends Common
{

    protected $beforeActionList = [
        // 'first',
        // 'second' =>  ['except'=>'hello'],
//        'delsoncate'  =>  ['only'=>'del'],
    ];

    public function lst()
    {

        $cate=new ImgclassModel();
        if(request()->isPost()){
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $cate->update(['classid'=>$k,'myorder'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $cateres=$cate->catetree();
        //  $cateres=db('downclass')->select();
        $this->assign('downclass',$cateres);
        return view();
    }

    public function add(){
        $cate=new ImgclassModel();
        if(request()->isPost()){
            $data=input('post.');
            // $validate = \think\Loader::validate('Cate');
            // if(!$validate->scene('add')->check($data)){
            //     $this->error($validate->getError());
            // }
            $add=$cate->save($data);
            if($add){
                $this->success('添加栏目成功！',url('lst'));
            }else{
                $this->error('添加栏目失败！');
            }
        }
        $cateres=$cate->catetree();
        $this->assign('downclass',$cateres);
        return view();
    }

    public function edit(){
        $cate=new ImgclassModel();
        if(request()->isPost()){
            $data=input('post.');
            // $validate = \think\Loader::validate('Cate');
            // if(!$validate->scene('edit')->check($data)){
            //     $this->error($validate->getError());
            // }
            $save=$cate->save($data,['classid'=>$data['classid']]);
            if($save !== false){
                $this->success('修改栏目成功！',url('lst'));
            }else{
                $this->error('修改栏目失败！');
            }
            return;
        }
        $cates=$cate->find(input('classid'));
        $cateres=$cate->catetree();
        $this->assign(array(
            'downclass'=>$cateres,
            'cates'=>$cates,
        ));
        return view();
    }

    public function del(){
        $cateid=input('classid'); //要删除的当前栏目的id
        $cate=new ImgclassModel();
        $sonids=$cate->getImgIDs($cateid);
        $del=db('imgclass')->where(['classid'=>['in',$sonids]])->delete();
        if($del){
            $this->success('删除成功！',url('lst'));
        }else{
            $this->error('删除失败！');
        }
    }

    public function delsoncate(){
        $cateid=input('id'); //要删除的当前栏目的id
        $cate=new ImgclassModel();
        $sonids=$cate->getchilrenid($cateid);
        $allcateid=$sonids;
        $allcateid[]=$cateid;
        foreach ($allcateid as $k=>$v) {
            $article=new ArticleModel;
            $article->where(array('cateid'=>$v))->delete();
        }
        if($sonids){
            db('cate')->delete($sonids);
        }
    }
}
