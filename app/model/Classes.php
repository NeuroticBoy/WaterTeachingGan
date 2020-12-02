<?php
namespace app\model;
use think\Model;

class Classes extends Model
{
    //一对一关联模型
    public function course() {
        return $this->belongsTo(Course::class,'course_id', 'id');
    }

    //一对多
    public function Member()
    {
        return $this->hasMany(Member::class,'classes_id','id');
    }
}