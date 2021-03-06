<?php
namespace app\admin\controller;
use app\admin\model\Downmembergroup as DownmembergroupModel;
use app\admin\controller\Common;
class Downmembergroup extends Common
{
    public function lst(){
        $downmembergroups=DownmembergroupModel::paginate(10);
        $this->assign('downmembergroup',$downmembergroups);
        return view();
    }

    public function add(){
        if(request()->isPost()){
            $data=input('post.');
            if($data['rules']){
                $data['rules']=implode(',', $data['rules']);
            }
            $add=db('downmembergroup')->insert($data);
            if($add){
                $this->success('添加用户组成功！',url('lst'));
            }else{
                $this->error('添加用户组失败！');
            }
            return;
        }
        $authRule=new \app\admin\model\AuthRule();
        $authRuleRes=$authRule->authRuleTree();
        $this->assign('authRuleRes',$authRuleRes);
        return view();
    }

    public function edit(){
        if(request()->isPost()){
            $data=input('post.');
            if($data['rules']){
                $data['rules']=implode(',', $data['rules']);
            }
            $_data=array();
            foreach ($data as $k => $v) {
                $_data[]=$k;
            }
            if(!in_array('status', $_data)){
                $data['status']=0;
            }
            $save=db('auth_group')->update($data);
            if($save!==false){
                $this->success('修改用户组成功！',url('lst'));
            }else{
                $this->error('修改用户组失败！');
            }
            return;
        }
        $authgroups=db('auth_group')->find(input('id'));
        $this->assign('authgroups',$authgroups);
        $authRule=new \app\admin\model\AuthRule();
        $authRuleRes=$authRule->authRuleTree();
        $this->assign('authRuleRes',$authRuleRes);
        return view();
    }

    public function del(){
        $del=db('downmembergroup')->delete(input('groupid'));
        if($del){
            $this->success('删除用户组成功！',url('lst'));
        }else{
            $this->error('删除用户组失败！');
        }
    }


    
    




   

	












}
