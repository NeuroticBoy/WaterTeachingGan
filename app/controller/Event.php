<?php
namespace app\controller;
use think\exception\ValidateException;
use think\facade\Validate;
use app\model\User as UserModel;
use app\model\Classes as ClassesModel;
use app\model\Member as MemberModel;
use app\model\Course as CourseModel;
use app\model\Attendance as AttendanceModel;
use app\model\AttendanceLog as AttendanceLogModel;
//事务处理

//删除班级
//思路：
//因为已找到班级id所以删除班级id可以直接删除
//之后判断该班级下的成员是否为空,为空则返回true代表删除成功
//否则执行删除班级下的成员操作,删除成功则返回true否则返回删除失败并回滚数据
    function delete_class_event($classId){
        ClassesModel::startTrans();//启动事务处理
        MemberModel::startTrans();//启动事务处理
        $hasDelClasses = ClassesModel::where('id',$classId)->delete();
        $hasDelMember = MemberModel::where('classes_id',$classId)->delete();
        $memberIsEmpty = MemberModel::where('classes_id',$classId)->select()->isEmpty();
        if($hasDelClasses) {
            try{
                ClassesModel::commit();
            }
            catch (\Exception $e) {
                ClassesModel::rollback();
                return false;
            }
        }
        if($memberIsEmpty){
            try{
                MemberModel::commit();
                return true;
            }
            catch (\Exception $e) {
                MemberModel::rollback();
                ClassesModel::rollback();
                return false;
            }
        }
        else{
            return true;
        }
    }
    //判断是否为课程下的老师
    //思路：查看该用户course下是否有该课程,如果课程中有匹配的classid则返回true,否则返回false
    // function IfChargeClass($class,$user){
    //     $class_list = HasCourse($user);
    //     if($class_list){
            
    //     }

    // }
    // //判断是否为学生,是则返回该生加过的班级列表,否则返回false
    // //思路：查看member下的class是否有对应的user成员
    // function ClassHaStudent($class,$user){
    //     $class_list = MemberModel::where("user_id",$user)->column('classes_id');
    //     return $class_list->isEmpty()?$class_list:false;
    // }
    // //判断该用户是否有课程,有则返回课程列表否则返回false
    // function HasCourse($user){
    //     $course_list  = CourseModel::where('user_id',$user)->column('id');
    //     return $course_list->isEmpty()?$course_list:false;
    // }


//删除考勤
//思路：
//传入AttendanceId并删除相关的考勤记录
//删除Attendance表下的数据以及AttendanceLog下所有参与考勤的学生的数据(需要判断该AttendanceLog下是否已有学生与考勤，有则删除否则返回true)
//首先执行删除Attendance下的考勤记录，而后将所有参与过该次考勤下的学生考勤记录给删除

    function delete_Attendance($attendanceId){
        AttendanceModel::startTrans();//启动事务处理
        AttendanceLogModel::startTrans();//启动事务处理
        $hasDelAttendance = AttendanceModel::where('id',$attendanceId)->delete();
        $hasDelLog = AttendanceLogModel::where('attendance_id',$attendanceId)->delete();
        $LogIsEmpty = AttendanceLogModel::where('attendance_id',$attendanceId)->select()->isEmpty();
        if($hasDelAttendance) {
            try{
                AttendanceModel::commit();
            }
            catch (\Exception $e) {
                AttendanceModel::rollback();
                return false;
            }
        }
        if($LogIsEmpty){
            try{
                AttendanceLogModel::commit();
                return true;
            }
            catch (\Exception $e) {
                AttendanceLogModel::rollback();
                AttendanceModel::rollback();
                return false;
            }
        }
        else{
            return true;
        }
    }