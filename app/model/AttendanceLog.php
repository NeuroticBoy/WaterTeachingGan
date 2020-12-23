<?php
namespace app\model;
use think\Model;

class AttendanceLog extends Model
{
    //一对一关联模型
    public function attendance() {
        return $this->belongsTo(Attendance::class,'attendance_id', 'id');
    }

}