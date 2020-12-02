<?php
namespace app\model;
use think\Model;

class Member extends Model
{
    //一对一关联模型
    public function classes() {
        return $this->belongsTo(Classes::class,'classes_id', 'id');
    }
}