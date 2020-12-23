<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// Route::get('hello/:name', 'index/hello');

//User 模块
Route::group('user', function () {
    Route::POST('register', 'register');  //注册
    Route::POST('login', 'login');  //登录
    Route::GET('me', 'getMe')->middleware(['Login']);  //获取当前用户信息
    Route::POST('me/update', 'updateMe')->middleware(['Login']);  //获取当前用户信息
    Route::POST('password/update', 'setPassowrd')->middleware(['Login']);  //登录验证;  //登录
    Route::GET('test', 'verifyTest')->middleware(['Login']);  //登录验证
})->prefix('user/');

//Course模块
Route::group('course', function () {
    Route::POST('', 'createCourse');    //创建课程
    Route::DELETE('/:course_id', 'deleteCourse');//->pattern(['course_id' => '\d+']); //删除课程
    Route::POST('/:course_id/update', 'updateCourse')->pattern(['course_id' => '\d+']); //更新课程信息
    Route::GET('/teach', 'getTeach');   //获取我教的课列表
    Route::GET('/study', 'getStudy');   //获取我教的课列表

})->prefix('course/')->middleware(['Login']);

//Classes模块
Route::group('class', function () {
    Route::GET('/:class_id/member', 'getMember')->pattern(['class_id' => '\d+']); //加入班级
    Route::POST('', 'createClass'); //创建班级
    Route::DELETE('/:class_id', 'deleteClass')->pattern(['class_id' => '\d+']); //删除班级
    Route::GET('/:class_id', 'getClass')->pattern(['class_id' => '\d+']); //获取单个课程信息
    Route::POST('/:class_id/update', 'updateClass')->pattern(['class_id' => '\d+']); //更新班级信息
    Route::POST('join/:joinCode', 'joinClass'); //加入班级
})->prefix('classes/')->middleware(['Login']);