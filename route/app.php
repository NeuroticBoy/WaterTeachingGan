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
    Route::GET('test', 'verifyTest')    //登录测试
        ->middleware(['Login']);  //登录验证
})->prefix('user/');

//Course模块
Route::group('course', function () {
    Route::POST('', 'createCourse');    //创建课程
    Route::GET('/teach', 'getTeach');   //获取我教的课列表

})->prefix('course/')->middleware(['Login']);

//Classes模块
Route::group('class', function () {
    Route::POST('', 'createClass'); //创建班级
    Route::POST('join/:joinCode', 'joinClass'); //加入班级
})->prefix('classes/')->middleware(['Login']);