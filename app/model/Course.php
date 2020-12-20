<?php
namespace app\model;
use think\Model;

class Course extends Model
{
    //一对多关联模型
    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    //一对一关联模型
    public function user() {
        return $this->belongsTo(User::class,'user_id', 'id');
    }
}