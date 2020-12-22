<?php
namespace app\model;
use think\Model;

class Attendance extends Model
{
    //一对一关联模型
    public function classes() {
        return $this->belongsTo(Classes::class,'classes_id', 'id');
    }

    //一对多
    public function attendanceLog()
    {
        return $this->hasMany(AttendanceLog::class,'attendance_id','id');
    }
}