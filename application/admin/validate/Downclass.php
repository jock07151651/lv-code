<?php
namespace app\admin\validate;
use think\Validate;
class Downclass extends Validate
{

    protected $rule=[
        'classname'=>'unique:cate|require|max:25',
    ];


    protected $message=[
        'classname.require'=>'栏目名称不得为空！',
        'classname.unique'=>'栏目名称不得重复！',
    ];

    protected $scene=[
        'add'=>['classname'],
        'edit'=>['classname'],
    ];





    

    




   

	












}
