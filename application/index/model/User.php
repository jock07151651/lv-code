<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12 0012
 * Time: 下午 4:28
 */

namespace app\index\model;


use think\Model;

class User extends Model
{
    protected $table = 'e_downmember';
    public function UserGroup()
    {
        return $this->belongsTo('UserGroup','groupid','groupid');
    }
}