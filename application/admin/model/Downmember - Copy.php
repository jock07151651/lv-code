<?php
namespace app\admin\model;
use think\Model;
class Downmember extends Model
{

   public function addmember($data){
    if(empty($data) || !is_array($data)){
        return false;
    }
    if($data['password']){
        $data['password']=md5($data['password']);
    }
    $adminData=array();
    $adminData['username']=$data['username'];
    $adminData['password']=$data['password'];
    // if($this->save($adminData)){
    //     $groupAccess['uid']=$this->userid;
    //     $groupAccess['group_id']=$data['group_id'];
    //     db('down_group_access')->insert($groupAccess);
    //     return true;
    // }else{
    //     return false;
    // }




   }

   public function getmember(){
    return $this::paginate(10,false,[
        'type'=>'boot',
        'var_page' => 'page',
        ]);
   }

   public function saveamember($data,$members){
        if(!$data['username']){
            return 2;//管理员用户名为空
        }
        if(!$data['password']){
            $data['password']=$members['password'];
        }else{
            $data['password']=md5($data['password']);
        }
        db('downmember')->where(array('userid'=>$data['userid']))->update(['groupid'=>$data['groupid']]);
        return $this::update(['username'=>$data['username'],'password'=>$data['password']],['userid'=>$data['userid']],['groupid'=>$data['groupid']]);
    
    }

    public function delmember($userid){
        if($this::destroy($userid)){
            return 1;
        }else{
            return 2;
        }
    }

    public function login($data){
        $admin=Admin::getByName($data['name']);
        if($admin){
            if($admin['password']==md5($data['password'])){
                session('id', $admin['id']);
                session('name', $admin['name']);
                return 2; //登录密码正确的情况
            }else{
                return 3; //登录密码错误
            }
        }else{
            return 1; //用户不存在的情况
        }

    }






}
