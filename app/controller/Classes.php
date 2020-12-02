<?php

namespace app\controller;

use think\exception\ValidateException;
use app\validate\Classes as ClassVerify;

use think\facade\Request;

use app\model\Classes as ClassesModel;
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
        $visible_field = ['id', 'course_id', 'title', 'describ'];  //输出字段
        $write_field = $visible_field; //写入字段

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

        //4. 添加记录
        $course = ClassesModel::create($classData, $write_field)->visible($visible_field);

        //5. 返回课程信息
        return $this->build($course, "成功");
    }

}
