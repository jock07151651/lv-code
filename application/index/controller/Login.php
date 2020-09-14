<?php

/**

 * Created by PhpStorm.

 * User: Administrator

 * Date: 2019/3/4 0004

 * Time: 下午 4:57

 */



namespace app\index\controller;



use app\index\model\Downmember as UserModel;

use app\index\model\SoftCate;

use app\index\validate\Register;

use think\captcha\Captcha;

use think\Controller;

use think\Cookie;

use think\Db;

use think\Session;



class Login extends Controller

{

    //登录界面

    public function index()
    {
        if(isset($_SERVER['HTTP_REFERER'])){
            $lastUrl = $_SERVER['HTTP_REFERER'];
            cookie('lastUrl',$lastUrl);
        }
        $this->redirect('http://www.662p.com/login');die;
        //网站title
        $titleConf = db('conf')->where(['cnname'=>'新闻板块站点名称'])->value('value');
     //   $this->assign('titleConf',$titleConf);
        $this ->assign('sefltitle', '用户登录');
        if(request()->isAjax()){

            $data = request()->post();
            //检测用户是否存在
            $exist = UserModel::UserIsExist($data['username']);

            if(!$exist){

                return json(['code'=>0,'info'=>'您输入的账号不存在，请重新输入！']);

            }

            //检测密码是否正确

            $pwdIsTrue = UserModel::PasswordIsTrue($data['username'],$data['password']);

            if(!$pwdIsTrue){

                return json(['code'=>0,'info'=>'用户密码不正确，请重新输入！']);

            }

            //记录用户信息

            $user = ToArray($pwdIsTrue);
            Session::set('username',$user['username']);
            Session::set('user_id',$user['userid']);
            Session::set('userpic',$user['userpic']);
            $lastUrl = Session::get('lastUrl');
            if(isset($data['remember'])){
                cookie('username', $data['username'], 604800);
                cookie('password', $data['password'], 604800);
            }else{
                cookie('username', null);
                cookie('password', null);
            }

            if($lastUrl=='http://code.662p.com/logout'||$lastUrl=='http://code.662p.com/login'||$lastUrl=='http://code.662p.com/register/'){

                $lastUrl = '/';

            }

            return json(['code'=>1,'info'=>'登陆成功','lastUrl'=>$lastUrl]);

        }

        if(isset($_SERVER['HTTP_REFERER'])){

            $lastUrl = $_SERVER['HTTP_REFERER'];

            Session::set('lastUrl',$lastUrl);

        }

        //top导航栏

        $nav = SoftCate::NavCate();

        return $this->fetch('',[

            'nav'=>$nav

        ]);

    }

    //注销登录

    public function logout()

    {

        Session::clear();

        $_SESSION['username'] = null;
        $_SESSION['user_id'] = null;
        $_SESSION['userpic'] = null;

        $this->redirect('/');

    }

    //注册界面

    public function register()

    {

//        $this->redirect('http://www.662p.com/register');die;
        //网站title
        $titleConf = db('conf')->where(['cnname'=>'新闻板块站点名称'])->value('value');
        $this->assign('titleConf',$titleConf);

        import('PHPMailer.Email',EXTEND_PATH,'.php');

        $email = new \Email();
        
//        $res = $email->send();

        //top导航栏

        $nav = SoftCate::NavCate();

        //表单提交

        if(request()->isPost()){
            
            $data = request()->post();
            
            $RegisterValidate = new Register();

            if(!$RegisterValidate->check($data)){

                $res = $RegisterValidate->getError();

                return json(['code'=>0,'info'=>$res]);

            }
            $UsernameExist = db('downmember')->where(['username'=>trim($data['username'])])->find();
            if($UsernameExist){
                return json(['code'=>0,'info'=>'用户名已存在，请重新输入！']);
            }
            $exist = db('downmember')->where(['email'=>trim($data['email'])])->find();

            if($exist){

                return json(['code'=>0,'info'=>'该邮箱已注册！']);

            }
            $data['registertime'] =time();
            $user = new UserModel($data);

            $res = $user->allowField(true)->save();

            if($res){

                $msg = ['code'=>1,'info'=>'注册成功'];

            }else{

                $msg = ['code'=>0,'info'=>'注册失败，请与客服人员联系！'];

            }

            return json($msg);

        }

        return $this->fetch('',[

            'nav'=>$nav

        ]);

    }

    //密码重置
    //1：手机验证码，2：邮箱验证码
    public function pwdReset($id)

    {
        $this->assign('id',$id);
        if(request()->isAjax()){
            $data = request()->post();
            if($id==2){
                //邮箱修改密码
                $emailExist = UserModel::EmailIsExist($data['email']);

                if(!$emailExist){

                    return json(['code'=>0,'info'=>'邮箱不存在，您可以先注册会员哦。']);

                }
            }else{
                //手机修改密码
                $mobileExist = Db::table('e_downmember')->where(['mobile'=>$data['mobile']])->find();
                if(!$mobileExist){
                    return json(['code'=>0,'info'=>'手机号不存在，您可以先注册账号哦。']);
                }
            }
//            //数据验证
//
//            $validate = new Register();
//
//            $res = $validate->scene('edit')->check($data);
//
//            if(!$res){
//
//                return json(['code'=>0,'info'=>$validate->getError()]);
//
//            }
            $code = request()->post('code');
            if(($code !== cookie('resetCode'))||cookie('resetCode')==null){
                return json(['code'=>0,'info'=>'验证码不正确，请重新输入！']);
            }
            $user = new UserModel();
            $save = [
                'password'=>md5($data['password']),
                'update_time'=>time()
            ];
            if($id==1){
                $res = Db::table('e_downmember')->where('mobile','=',$data['mobile'])->update($save);
            }else{
                $res = Db::table('e_downmember')->where('email','=',$data['email'])->update($save);
            }

            if(!$res){

                $msg = ['code'=>0,'info'=>'密码重置失败，请与客服人员联系！'];

            }else{

                $msg = ['code'=>1,'info'=>'密码重置成功'];

            }

            return json($msg);

        }

        //top导航栏

        $nav = SoftCate::NavCate();

        return $this->fetch('reset',[

            'nav'=>$nav

        ]);

    }

    //点击演示地址，检测用户是否登录
    public function chekIsLogin()
    {
        if(request()->isAjax()){
            $id = request()->post('id');
            $user_id = Session::get('user_id');
            if(!$user_id){
                $msg = ['code'=>0,'info'=>'您还没登录，请先登录！'];
            }else{
                $data = Db::table('e_down')->where(['softid'=>$id])->find();
                $url = $data['demo'];
                $msg = ['code'=>1,'info'=>'已登录','url'=>$url];
            }
            $user = Db::table('e_downmember')->where(['userid'=>\session('user_id')])->find();
            if($user['downfen']<request()->post('downfen')){
                return json(['code'=>502,'info'=>'您的积分不够，无法查看演示文件']);
            }
            return json($msg);
        }
    }

    //发送QQ邮箱验证码

    public function SendEmailCode()

    {

        if(request()->isAjax()){

            $email  = request()->post('email');
            $mobile  = request()->post('mobile');
            $type   = request()->post('type');

            if($type!=='pwdReset'){
                $UsernameExist = db('downmember')->where(['username'=>trim(request()->post('username'))])->find();
                if($UsernameExist){
                    return json(['code'=>0,'info'=>'用户名已存在，请重新输入！']);
                }

                $exist  = db('downmember')->where(['email'=>trim($email)])->find();

                if($exist){

                    return json(['code'=>0,'info'=>'该邮箱已注册！']);

                }
                //检测手机号是否已绑定
                $phoneExist = db('downmember')->where(['mobile'=>trim($mobile)])->find();
                if($phoneExist){
                    return json(['code'=>0,'info'=>'该手机号码已注册！']);
                }
                $title  = "开发吗会员注册";

                $head   = "【会员注册】";

            }else{

                $title  = "【开发吗密码重置】";

                $head   = "【密码重置】";

            }

            if(!$email){

                return json(['code'=>0,'info'=>'发送邮箱验证码失败，请与客服人员联系！']);

            }

//            import('PHPMailer.Email',EXTEND_PATH,'.php');

//            $SendApi = new \Email();

            $code = mt_rand(100000,999999);

//            $res = $SendApi->send($email,$title,"$head 您的验证码为：$code,验证码有效期为30分钟哦！");
            import('SMS.smsbao',EXTEND_PATH,'.php');
            $smsSend = new \smsbao(config('sms.bao')['user'],config('sms.bao')['pwd'],$mobile,"【开发吗】您的注册码为".$code."，在30分钟内有效，如果不是本人操作，请忽略此短信。");
            $res = $smsSend->send();
            if($res){

                Cookie::set('code',$code,1800);

                $msg = ['code'=>1,'info'=>'验证码已下发至您的手机了，请您注意查看！'];

            }else{

                $msg = ['code'=>0,'info'=>'验证码发送失败，请与客服人员联系！'];

            }

            return json($msg);

        }

    }

    //验证码

    public function captcha()

    {

        $config =    [

            // 验证码字体大小

            'fontSize'    =>    72,

            // 验证码位数

            'length'      =>    4,

            // 关闭验证码杂点

            'useNoise'    =>    false,

        ];

        $captcha = new Captcha($config);

        return $captcha->entry();

    }

    public function checkName()
    {
        if(request()->isAjax()){
            $username = request()->post('username');
            $exist = db('downmember')->where(['username'=>trim($username)])->find();
            if($exist){
                $msg = ['code'=>0,'info'=>'用户名已存在请重新输入！'];
            }else{
                $msg = ['code'=>1];
            }
            return json($msg);
        }
    }

    //检测是否登录

    public function IsLogin()

    {

        if(request()->isAjax()){

            if(Session::get('user_id')){

                $msg = ['code'=>1,'info'=>'用户已登录'];

            }else{

                $msg = ['code'=>0,'info'=>'请您先登录！'];

            }

            return json($msg);

        }

    }
    //密码重置时发送的验证码
    public function resetCode($id='')
    {
        if(request()->isAjax()){
            $code = mt_rand(100000,999999);
            cookie('resetCode',$code,1800);
            $id = request()->post('id');
            if($id==1){
                $mobile = request()->post('mobile');
                //发送手机验证码
                import('SMS.smsbao',EXTEND_PATH,'.php');
                $smsSend = new \smsbao(config('sms.bao')['user'],config('sms.bao')['pwd'],$mobile,"【开发吗】您本次重置密码的验证码为".$code."，在30分钟内有效，如果不是本人操作，请忽略此短信。");
                $res = $smsSend->send();
            }else{
                //发送邮箱验证码
                import('PHPMailer.Email',EXTEND_PATH,'.php');
                $SendApi = new \Email();
                $email = request()->post('email');
                $title = '【开发吗密码重置】';
                $head   = "【密码重置】";
                $res = $SendApi->send($email,$title,"$head 您的验证码为：$code,验证码有效期为30分钟哦！");
            }
            if($res){
                $msg = ['code'=>1,'info'=>'验证码发送成功，请您注意查收！'];
            }else{
                $msg = ['code'=>0,'info'=>'验证码发送失败，请与工作人员联系！'];
            }
            return json($msg);
        }
    }
    //test
    public function testEmail()
    {
        //发送邮箱验证码
        import('PHPMailer.Email',EXTEND_PATH,'.php');
        $SendApi = new \Email();
        $email = '1668001880@qq.com';
        $title = '【开发吗密码重置】';
        $head   = "【密码重置】";
        $res = $SendApi->send($email,$title,"$head 您的验证码为：888888,验证码有效期为30分钟哦！");
        var_dump($res);die;
    }

}