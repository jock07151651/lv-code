<?php
namespace app\admin\validate;
use think\Validate;
class Link extends Validate
{

    protected $rule=[
        'lname'=>'unique:downlink|require|max:25',
        'lurl'=>'url|unique:downlink|require|max:60',
        'email'=>'require',
    ];


    protected $message=[
        'lname.require'=>'链接标题不得为空！',
        'lname.unique'=>'链接标题不得重复！',
        'lurl.unique'=>'链接地址不得重复！',
        'lurl.require'=>'链接地址不得为空！',
        'lurl.url'=>'链接地址格式不正确！',
        'lurl.max'=>'链接地址不得大于60个字符！',
        'lname.max'=>'链接标题长度大的大于25个字符！',
        'email.require'=>'链接描述不得为空！',
    ];

    protected $scene=[
        'add'=>['lname','lurl','email'],
        'edit'=>['lname','lurl'],
    ];





    

    




   

	












}
