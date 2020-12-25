<?php

namespace app\controller;

use think\exception\ValidateException;
use app\validate\Attendance as AttendanceVerify;

use think\facade\Request;
use think\facade\Db;

use app\model\Attendance as AttendanceModel;
use app\model\Classes as ClassesModel;
use app\model\AttendanceLog as AttendanceLogModel;
use app\model\Member as MemberModel;
use app\model\User as UserModel;
use app\model\Course as CourseModel;

use app\controller\Base;
use think\db\Builder;

class Attendance extends Base
{
    public function index()
    {
        echo "这里是考勤模块";
    }

    public function createAttendance()
    {
        //0. 定义字段
        $receive_field = ['title', 'describ', 'classes_id', 'type'];  //接收字段
        $visible_field = ['id', 'classes_id', 'title', 'active', 'amount_user', 'type', 'create_time'];  //输出字段
        $write_field = ['title', 'describ', 'classes_id', 'code', 'type', 'sign_in_user', 'amount_user']; //写入字段
        $write_field_log = ["attendance_id","user_id","status"];


        //1. 获取用户ID、传入数据
        $curUser = request()->uid;
        $attenData = Request::only($receive_field, 'post'); //获取提交信息

        //      - 提取具体数据
        $classId = $attenData["classes_id"];
        $attenType = $attenData["type"];
        
        //      - 获取成员列表
        $members = MemberModel::where("classes_id", $classId)->select(); //获取班级成员列表
        $membersCount = $members->count();

        //      - 统计并获取获取该次考勤人数
        $attenData["amount_user"] = $membersCount; //成员计数

        //      - 生成考勤码
        $code = $this->createCode();
        $attenData["code"] = $code;

        //      - 数据整形,去掉左右的空白字符
        if (array_key_exists('describ', $attenData)) $attenData["describ"] = trim($attenData["describ"]);
        if (array_key_exists('title', $attenData)) $attenData["title"] = trim($attenData["title"]);

        //      - 校验数据
        try {
            validate(AttendanceVerify::class)->batch(true)->scene('create')->check($attenData);
        } catch (ValidateException $e) {
            return $this->build($e->getError(), "参数错误")->code(400);
        }

        //      - 判断班级
        $class = ClassesModel::find($classId);
        if(!$class) {
            return $this->build(NULL, "没有该班级", 204)->code(204);
        }

        $teacherId = $class->course->value("user_id");
        


        //      - 数据整形,去掉左右的空白字符
        if (array_key_exists('describ', $attenData)) $attenData["describ"] = trim($attenData["describ"]);
        if (array_key_exists('title', $attenData)) $attenData["title"] = trim($attenData["title"]);

        //      - 校验数据
        try {
            validate(AttendanceVerify::class)->batch(true)->scene('create')->check($attenData);
        } catch (ValidateException $e) {
            return $this->build($e->getError(), "参数错误")->code(400);
        }

        //      - 判断班级
        $class = ClassesModel::find($classId);
        if(!$class) {
            return $this->build(NULL, "没有该班级", 204)->code(204);
        }

        $teacherId = $class->course->value("user_id");

        //          TODO 注意INT类型能表示的上限
        if((int)$teacherId !== (int)$curUser) {
            return $this->build(NULL, "没有操作权限", 403)->code(403);
        }


        //2. 创建考勤
        //          TODO 添加事务处理
        Db::startTrans();//启动事务处理

        try {
            //3. 创建考勤记录
            $attendance = AttendanceModel::create($attenData, $write_field)->visible($visible_field); //写入attendance记录

       
        
            //      - 判断考勤类型
            //           - 0：数字考勤，默认全部旷课
            //                  0 旷课      - 默认
            //                  1 出勤
            //           - 1：传统考勤：默认全部出勤
            $status = 0;

            if((int)$attenType === 1) {
                $status = 1;
            }

            //      - 为班级的每一个人都创建考勤记录
            $attendanceId = $attendance["id"];
            $logs = [];

            foreach ($members as $key => $member) {
                $log = [
                    "attendance_id"     =>      $attendanceId,
                    "user_id"           =>      $member["user_id"],//使用value()会出现BUG
                    "status"            =>      $status,
                ];
                $logs[$key] = $log;
            }

                //      - 写入数据库
                $AttendanceLog = new AttendanceLogModel;
                $AttendanceLog->saveAll($logs);
                Db::commit();//提交
            } catch (\Exception $th) {
            
                Db::rollback();//回滚数据
                return $this->build(NULL,"考勤失败，请稍后再试")->code(500);
            }
        
        $attendance = AttendanceModel::create($attenData, $write_field)->visible($visible_field); //写入attendance记录

       
        //3. 创建考勤记录
        //      - 判断考勤类型
        //           - 0：数字考勤，默认全部旷课
        //                  0 旷课      - 默认
        //                  1 出勤
        //           - 1：传统考勤：默认全部出勤
        $status = 0;

        if((int)$attenType === 1) {
            $status = 1;
        }

        //      - 为班级的每一个人都创建考勤记录
        $attendanceId = $attendance["id"];
        $logs = [];

        foreach ($members as $key => $member) {
            $log = [
                "attendance_id"     =>      $attendanceId,
                "user_id"           =>      $member->value("user_id"),
                "status"            =>      $status,
            ];
            $logs[$key] = $log;
        }

        //      - 写入数据库
        $AttendanceLog = new AttendanceLogModel;
        $AttendanceLog->saveAll($logs);
        
        //TODO 限制写入字段

        //4. 返回考勤信息
        return $this->build($attendance, "创建成功");

    }

    public function deleteAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 判断相关数据是否存在：考勤项目


        //3. 判断权限：是否为老师

        //4. 删除考勤
        //- 同时删除考勤记录表的相关记录

        //5. 返回信息
    }

    public function updateAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 判断相关数据是否存在：考勤项目


        //3. 判断权限：是否为老师

        //4. 更新数据

        //5. 返回新数据


    }

    //TODO- 学生和老师身份分离为不同的API
    public function getAttendance()
    {
        //0. 定义可见字段

        //1. 获取用户ID
        $curUser = request()->uid;

        //2. 判断权限并获取身份：老师或成员

        //3. 根据身份生成数据
        //- 老师身份的数据

        //- 成员身份的数据


        //4. 返回数据
    }

    //TODO- 学生和老师身份分离为不同的API
    public function getClassAttendance()
    {
        //1. 获取用户ID
        $curUser = request()->uid;

        //2. 判断权限并获取身份：老师或成员

        //3. 根据身份生成数据
        //- 老师身份的数据

        //- 成员身份的数据


        //4. 返回数据
    }

    // TODO 接入Redis进行考勤
    public function signIn()
    {
        //0. 定义字段
        
        //1. 获取用户ID、考勤码
        $userId = request()->uid;
        $code = Request::route("code");//获取输入的考勤码
        
        //获取考勤信息
        $attendance = AttendanceModel::where("code" , $code)->find();//匹配考勤码code的考勤记录
        $attendId = $attendance["id"];//当前考勤ID
        $attendLog = AttendanceLogModel::where("attendance_id",$attendId)->where("user_id" , $userId)->find();//当前用户的本次考勤记录
        
        //2. 判断当前用户是否属于考勤所属的班级
        $member = MemberModel::where("user_id",$userId)->find();
        if (!$member) {
            return $this->build(NULL, "当前用户不属于该班级！", 204)->code(204);
        }
        

        //3. 判断考勤是否进行中
        if($attendance["active"] == 1)
        {
            //- 是
            //     - 修改考勤记录表记录
            $attendLog["status"] = 1;//签到状态修改
        }
        else{
            //- 否
            //     - 报错
            $attendLog["status"] = 0;
            return $this->build(null, "考勤已结束", 400)->code(400);
        }
        
        $attendLog["changes"] = $attendLog["changes"]+1;//修改考勤次数
        $attendLog->save();
        //- 是
        //     - 修改考勤记录表记录
        //- 否
        //     - 报错

        //3. 返回成功信息
        return $this->build($attendLog, "签到成功");

    }

    public function getUserAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 判断权限：老师 或 成员自己

        //3. 获取数据

        //4. 返回数据

    }

    public function updateUserAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 判断权限：老师

        //3. 修改保存数据

        //4. 返回新数据
    }

}
