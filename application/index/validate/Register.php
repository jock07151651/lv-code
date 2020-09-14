<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4 0004
 * Time: 下午 7:49
 */

namespace app\index\validate;


use think\Cookie;
use think\Validate;

class Register extends Validate
{
    protected $rule = [
        ['username','require|checkNull|min:2|max:12','用户名不可为空|用户名包涵了非法字符！|用户名长度为3-12位字符|用户名长度为3-12位字符'],
        ['password','require|checkNull|min:6|max:18','密码不可为空|密码包涵了非法字符！|密码长度为6-18位字符|密码长度为6-18位字符'],
//        ['repassword','require|confirm:password','重复密码不可为空|两次输入密码不一致'],
        ['email','require|email','请填写邮箱！|邮箱格式不正确！'],
        ['code','require|checkCode','验证码不可为空|验证码不正确,请重新输入！']
    ];
    //验证场景
    protected $scene = [
        'edit'  =>  ['password','repassword','email','code'],
    ];
    protected function capchat()
    {

    }
    protected function checkpwd(){

    }
    protected function checkCode($value,$rule='',$data=[])
    {
        return $value == Cookie::get('code') ? true : false;
    }
    // 字段不能有空值
    protected function checkNull($value,$rule='',$data)
    {
        return $value == TrimNull($value) ? true : false;
    }
}