<?php

namespace app\model;

use think\Model;

class User extends Model
{
    //一对一关联模型
    public function course()
    {
        return $this->hasMany(Course::class);
    }
}
