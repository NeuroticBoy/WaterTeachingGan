<?php

namespace app\controller;

use think\exception\ValidateException;
use app\validate\Attendance as AttendanceVerify;

use think\facade\Request;

use app\model\Attendance as AttendanceModel;
use app\model\AttendanceLog as AttendanceLogModel;
use app\controller\Base;

class Attendance extends Base
{
    public function index()
    {
        echo "这里是考勤模块";
    }

    public function createAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 创建考勤
        // - 注意写入考勤人数（就是班级人数）
        // - 为班级的每一个人都创建考勤记录
        //      - 数字考勤：默认全部旷课
        //         0 旷课 - 默认
        //         1 出勤
        //         2 迟到
        //         3 请假
        //         4 事假
        //         5 病假
        //         6 公假
        //         7 早退

        //      - 传统考勤：默认全部出勤

        //3. 返回考勤

        //4. 返回信息

    }

    public function deleteAttendance()
    {
        //0. 定义字段

        //1. 获取用户ID、传入数据
        $curUser = request()->uid;

        //2. 判断相关数据是否存在：考勤项目


        //3. 判断权限：是否为老师

        //4. 删除考勤
        // - 同时删除考勤记录表的相关记录

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

    //TODO - 学生和老师身份分离为不同的API
    public function getAttendance()
    {
        //0. 定义可见字段

        //1. 获取用户ID
        $curUser = request()->uid;

        //2. 判断权限并获取身份：老师或成员

        //3. 根据身份生成数据
        // - 老师身份的数据

        // - 成员身份的数据


        //4. 返回数据
    }

    //TODO - 学生和老师身份分离为不同的API
    public function getClassAttendance()
    {
        //1. 获取用户ID
        $curUser = request()->uid;

        //2. 判断权限并获取身份：老师或成员

        //3. 根据身份生成数据
        // - 老师身份的数据

        // - 成员身份的数据


        //4. 返回数据
    }

    // TODO 接入Redis进行考勤
    public function signIn()
    {
        //0. 定义字段

        //1. 获取用户ID、考勤码
        $curUser = request()->uid;
        echo "输入考勤码进行考勤";

        //2. 判断当前用户是否属于考勤所属的班级

        //3. 判断考勤是否进行中
        // - 是
        //      - 修改考勤记录表记录
        // - 否
        //      - 报错

        //3. 返回成功信息

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
