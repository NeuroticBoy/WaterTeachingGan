<?php
namespace app\model;
use think\Model;

class Course extends Model
{
    //一对一关联模型
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }
}