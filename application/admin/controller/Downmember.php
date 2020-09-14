<?php
namespace app\admin\controller;
use app\admin\model\Downmember as DownmemberModel;
use app\admin\controller\Common;
use think\Db;

class Downmember extends Common
{

    public function lst()
    {
        $keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
        $members=db('downmember')
            ->field('a.*,b.groupname')
            ->alias('a')
            ->where('username','like',"%$keyword%")
            ->whereOr('email','like',"%$keyword%")
            ->whereOr('mobile','like',"%$keyword%")
            ->join('e_downmembergroup b','a.groupid=b.groupid')
            ->order('a.userid desc')
            ->paginate(10);
        $this->assign('memberres',$members);
        return view();
    }

    public function add()
    {
        if(request()->isPost()){
            $data=input('post.');
            // $validate = \think\Loader::validate('Admin');
            // if(!$validate->scene('add')->check($data)){
            //     $this->error($validate->getError());
            // }
            $data['password']=md5($data['password']);
            $data['registertime']=time();
            $member=new DownmemberModel();
            if($member->insert($data)){
                $this->success('添加会员成功！',url('lst'));
            }else{
                $this->error('添加会员失败！');
            }
            return;
        }
        $authGroupRes=db('downmembergroup')->select();
        $this->assign('downmembergroup',$authGroupRes);
        return view();
    }

    public function edit($userid)
    {
     
        if(request()->isPost()){
            $data=input('post.');
        $memberpw=db('downmember')->where(array('userid'=>input('userid')))->field('password')->find();
        if(!$data['password']){
            $data['password']=$memberpw['password'];
        }else{
            $data['password']=md5($data['password']);
        }
            // $validate = \think\Loader::validate('Admin');
            // if(!$validate->scene('edit')->check($data)){
            //     $this->error($validate->getError());
            // }
           $member=new DownmemberModel;
          /*   $savenum=$member->savemember($data,$members);
            if($savenum == '2'){
                $this->error('会员用户名不得为空！');
            }
            if($savenum !== false){
                $this->success('修改成功！',url('lst'));
            }else{
                $this->error('修改失败！');
            }
            return;
        }
        
        if(!$members){
            $this->error('该会员不存在');
        }  */
         $save=$member->update($data);
        if($save){
                $this->success('修改成功！',url('lst'));
            }else{
                $this->error('修改失败！');
            }
            return;
        }
        $downmembergroups=db('downmembergroup')->select();
        $members=db('downmember')->where(array('userid'=>input('userid')))->find();
        $this->assign(array(
            'downmembergroup'=>$downmembergroups,
            'members'=>$members,
            ));

        return view();
    }


    public function del(){
        if(DownmemberModel::destroy(input('userid'))){
            $this->success('删除成功！',url('lst'));
        }else{
            $this->error('删除失败！');
        }
    }

    public function logout(){
        session(null); 
        $this->success('退出系统成功！',url('login/index'));
    }
    public function pay()
    {
        $data = Db::table('e_downpayrecord')->where(['is_pay'=>1])->order('id desc')->paginate(10);

        return $this->fetch('',[
            'data'=>$data
        ]);
    }

    //会员下载记录
    public function downRecord()
    {
        $data = Db::table('e_downbuy_bak')
                ->join('e_down','e_down.softid = e_downbuy_bak.soft_id')
                ->join('e_downmember','e_downmember.userid = e_downbuy_bak.userid')
                ->join('e_downmembergroup','e_downmembergroup.groupid = e_downmember.groupid')
                ->field('e_downbuy_bak.*,e_down.softname,e_downmembergroup.groupname')
                ->where(['e_downbuy_bak.soft_id'=>['neq',0]])
                ->order('id desc')
                ->paginate(10);
        return $this->fetch('down',[
            'data'=>$data
        ]);
    }











}
