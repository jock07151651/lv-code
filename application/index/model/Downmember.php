<?php
namespace app\index\model;
use think\Model;
class Downmember extends Model
{
    protected $auto = [];
    protected $insert = ['password','registertime'];
    protected $update = ['password','update_time'];
    protected $createTime = 'registertime';
    //MD5自动加密
    protected function setPasswordAttr($value)
    {
        return md5($value);
    }
    //写入注册时间
    protected function setRegistertimeAttr($value)
    {
        return time();
    }
    //写入密码重置时间
    protected function setUpdateTimeAttr($value)
    {
        return time();
    }
   public function addadmin($data){
    if(empty($data) || !is_array($data)){
        return false;
    }
    if($data['password']){
        $data['password']=md5($data['password']);
    }
    $adminData=array();
    $adminData['name']=$data['name'];
    $adminData['password']=$data['password'];
    if($this->save($adminData)){
        $groupAccess['uid']=$this->id;
        $groupAccess['group_id']=$data['group_id'];
        db('auth_group_access')->insert($groupAccess);
        return true;
    }else{
        return false;
    }

   }

   public function getadmin(){
    return $this::paginate(5,false,[
        'type'=>'boot',
        'var_page' => 'page',
        ]);
   }

   public function saveamember($data,$admins){
        if(!$data['password']){
            $data['password']=$admins['password'];
        }else{
            $data['password']=md5($data['password']);
        }
        db('auth_group_access')->where(array('uid'=>$data['id']))->update(['group_id'=>$data['group_id']]);
        return $this::update(['name'=>$data['name'],'password'=>$data['password']],['id'=>$data['id']]);
    
    }

    public function deladmin($id){
        if($this::destroy($id)){
            return 1;
        }else{
            return 2;
        }
    }
    //关联会员组信息
    public function UserGroup()
    {
        return $this->belongsTo('UserGroup','groupid','groupid');
    }
    //检测用户是否存在
    public static function UserIsExist($username='')
    {
        return self::where(['username|email|mobile'=>$username])->find();
    }
    //检测密码是否正确
    public static function PasswordIsTrue($username,$password)
    {
        return self::where(['username|email|mobile'=>$username,'password'=>md5($password)])->find();
    }
    //检测邮箱已注册
    public static function EmailIsExist($email)
    {
        return self::where(['email'=>$email])->find();
    }
}
