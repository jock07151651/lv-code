<?php
namespace app\admin\controller;
use app\admin\model\Downmember as DownmemberModel;
use app\admin\controller\Common;
class Downmember extends Common
{

    public function lst()
    {
        // $auth=new Auth();
        // $member=new DownmemberModel();
        // $members=$member->getmember();
        // foreach ($members as $k => $v) {
        //     $_groupTitle=$auth->getmember($v['userid']);
        //     $groupTitle=$_groupTitle[0]['groupname'];
        //     $v['groupname']=$groupTitle;
        // }
        // $this->assign('memberres',$members);

        $members=db('downmember')->field('a.*,b.groupname')->alias('a')->join('e_downmembergroup b','a.groupid=b.groupid')->order('a.userid desc')->paginate(10);
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
            $member=new DownmemberModel();
            if($member->addmember($data)){
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
      //  $members=db('downmember')->find($userid);
        if(request()->isPost()){
            $data=input('post.');
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


    public function del($userid){
        $member=new DownmemberModel();
        $delnum=$member->delmember($userid);
        if($delnum == '1'){
            $this->success('删除会员成功！',url('lst'));
        }else{
            $this->error('删除会员失败！');
        }
    }

    public function logout(){
        session(null); 
        $this->success('退出系统成功！',url('login/index'));
    }













}
