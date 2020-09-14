<?php
namespace app\admin\controller;
use app\admin\model\Jobs as JobsModel;
use app\admin\controller\Common;
class Jobs extends Common
{



    public function lst()
    {
        $job=new JobsModel();
        if(request()->isPost()){
            $sorts=input('post.');
            foreach ($sorts as $k => $v) {
                $job->update(['id'=>$k,'job_name'=>$v]);
            }
            $this->success('更新排序成功！',url('lst'));
            return;
        }
        $jobs=$job->order('id asc')->paginate(15);
        $this->assign('jobss',$jobs);
        return view();
	}

    public function add(){
      if (!empty($_POST)){
        $post = input('post.');
        $z = db('jobs')->insert($post);
        if (!empty($z)) {
            $this->success('添加成功！！！',Url('jobs/lst'));
        }else{

          $this->error("添加失败！！！");
        }
      
      }
    return $this->fetch();
    }


    public function edit($id){
    if(request()->isPost()){
            $data=input('post.');

      $linkinfo = db('jobs')->field('job_name')->where('id ='.$id)->find();
      $z = db('jobs')->where('id ='.$id)->update($data);
      if (!empty($z)) {
        $this->success("修改成功",'jobs/lst');
      }else{
        $this->error("修改失败！！！");
      }
      
      return;
    }
    $job = db('jobs')->where('id ='.$id)->find();
    $this->assign('jobs',$job);

        return $this->fetch();
    }
 
 //删除友链
    public function del(){
        $del=JobsModel::destroy(input('id'));
        if($del){
           $this->success('删除链接成功！',url('lst')); 
        }else{
            $this->error('删除链接失败！');
        }
    }

    




   

	












}
