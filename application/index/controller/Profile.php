<?php
namespace app\index\controller;
use app\index\model\Downmember as DownmemberModel;
use app\index\controller\Common;
use think\Session;
class My extends Common
{

	 //用户登录
    public function login () {
        if (isset($_POST) && !empty($_POST)) {
            
        //验证码校验
        $code=input('post.captcha');  
        $captcha = new \think\captcha\Captcha();  
        $result=$captcha->check($code);  
        if($result===false){  
           return $this->error('验证码错误！',Url('my/login'));exit;  
        }  

            $password = md5($_POST['password']);
            $is_member = db('downmember')->where(['username'=>$_POST['username'],'password'=>$password])->find();
            if (empty($is_member)) {
                return $this->error('账号密码有误',Url('my/login'));
            }else{
                Session::set('userid',$is_member['userid']);
                Session::set('username',$is_member['username']);
                return $this->error('登陆成功',Url('my/index'));
            }
        }
        return $this->fetch();
    }

//退出登录
    public function logout(){
        session(null); 
        $this->success('退出系统成功！',url('my/login'));
    }

//修改用户
	public function edit($userid)
    {
        $downmembers=db('downmember')->find($userid);

        if(request()->isPost()){
            $data=input('post.');
            // $validate = \think\Loader::validate('Admin');
            // if(!$validate->scene('edit')->check($data)){
            //     $this->error($validate->getError());
            // }
            $downmember=new DownmemberModel();
            $savenum=$downmember->saveadmin($data,$downmembers);
            if($savenum !== false){
                $this->success('修改成功！',url('lst'));
            }else{
                $this->error('修改失败！');
            }
            return;
        }
        
        if(!$admins){
            $this->error('该管理员不存在');
        }
        $downmembers=db('downmember')->where(array('useruid'=>$useruid))->find();
        $authGroupRes=db('auth_group')->select();
        $this->assign('authGroupRes',$authGroupRes);
        $this->assign('downmember',$downmembers);
        $this->assign('groupId',$authGroupAccess['group_id']);
        return view();
	}

    public function del($id){
        $admin=new DownmemberModel();
        $delnum=$admin->deladmin($id);
        if($delnum == '1'){
            $this->success('删除管理员成功！',url('lst'));
        }else{
            $this->error('删除管理员失败！');
        }
    }

    

//显示验证码
    public function show_captcha(){
        $captcha = new \think\captcha\Captcha();
        $captcha->imageW=121;
        $captcha->imageH = 32;  //图片高
        $captcha->fontSize =14;  //字体大小
        $captcha->length   = 4;  //字符数
        $captcha->fontttf = '5.ttf';  //字体
        $captcha->expire = 30;  //有效期
        $captcha->useNoise = false;  //不添加杂点
        return $captcha->entry();
    }

}
