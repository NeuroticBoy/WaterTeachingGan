<?php

namespace app\controller;

use think\exception\ValidateException;

use app\validate\Classes as ClassVerify;

use think\facade\Request;
use think\facade\Validate;

use app\model\Classes as ClassesModel;
use app\model\Member as MemberModel;

use app\controller\Base;

class Classes extends Base
{
    public function index()
    {

        echo "Hello Here is Course Controller";
    }

    public function createClass()
    {
        $receive_field = ['title', 'describ', 'course_id'];  //接收字段
        $visible_field = ['id', 'course_id', 'title', 'describ', 'code'];  //输出字段
        $write_field = ['title', 'describ', 'course_id', 'code']; //写入字段

        //1. 获取提交信息
        $classData = Request::only($receive_field, 'post');

        //2. 数据整形
        if (array_key_exists('describ', $classData)) $classData["describ"] = trim($classData["describ"]);
        if (array_key_exists('title', $classData)) $classData["title"] = trim($classData["title"]);

        //3. 校验数据
        try {
            validate(ClassVerify::class)->batch(true)->scene('create')->check($classData);
        } catch (ValidateException $e) {
            return $this->build($e->getError(), "参数错误")->code(400);
        }

        //4. 生成加课码
        $code = $this->createCode();

        while (!Validate::rule(['code' => 'unique:classes,code'])->check(["code"  =>  $code])) {
            $code = $this->createCode();
        }

        $classData["code"] = $code;

        //5. 添加记录
        $course = ClassesModel::create($classData, $write_field)->visible($visible_field);

        //6. 返回课程信息
        return $this->build($course, "成功");
    }

    public function deleteClass()
    {
        //1. 获取用户ID、班级ID
        $userId = request()->uid;
        $classId = Request::route("class_id");

        //2. 判断是否有该课程
        $class = ClassesModel::with('member')->find($classId);
        if (!$class) {
            return $this->build(NULL, "无此课程", 204)->code(204);
        }

        //3. 判断是否有权限删除
        $curUser = $class->course()->value('user_id');
        if ($userId !== $curUser) {
            return $this->build(NULL, "没有权限", 403)->code(403);
        }

        //4. 删除课程
        //TODO: 添加事务处理
        $class->together(["member"])->where("id", $classId)->delete();
        $class->member()->where("classes_id", $classId)->delete();

        return $this->build(NULL, "删除成功");
    }

    public function joinClass()
    {
        //1. 获取加课码、USER ID
        $code = Request::route('joinCode');
        $userId = request()->uid;

        //2. 获取课程、校验课程
        //- 判断课程是否存在
        $class = ClassesModel::where('code', $code)->find();
        if (!$class) {
            return $this->build(NULL, "课程不存在", 204)->code(204);
        }

        //- 判断该用户是否加入自己的课程
        $user = $class->course->value('user_id');
        if ($userId === $user) {
            return $this->build(NULL, "不能加入自己教的课程", 400)->code(400);
        }

        //3. 加入课程
        $write_field = ["user_id", "classes_id"];    //定义写入字段
        $visible_field = ["user_id", "classes_id", "create_time"];  //定义可见字段

        //- 获取ID
        $classesId = $class["id"];

        //- 写入数据库
        try {
            $member = MemberModel::create(["user_id" => $userId, "classes_id" => $classesId], $write_field)->visible($visible_field);
        } catch (\Exception $e) {
            $errCode = $e->getCode();
            switch ($errCode) {
                case 10501: //10501：主键重复
                    return $this->build(Null, "已加入过该课程", 400)->code(400);
            }
            return $this->build(Null, "未知错误", 500)->code(500);
        }

        //4. 返回数据
        return $this->build($member);
    }

    public function getMember()
    {
        //0. 定义可见字段
        $visible_field = ['user_id', 'nickname', "create_time"];

        //1. 获取班级ID
        $classId = Request::route('class_id');

        //2. 获取当前用户ID
        $user_id = request()->uid;

        //3. 获取班级信息
        $class = ClassesModel::find($classId);

        //4. 判断是否为课程创建者
        $curUser = $class->course()->field('user_id')->find();
        if ($curUser["user_id"] !== $user_id) {
            return $this->build(NULL, "没有操作权限", 403)->code(403);
        }

        //4. 检索数据
        $members = $class->member()->visible($visible_field)->select();
        return $this->build($members);
    }
}
