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
    Route::POST('login', 'login');
    Route::GET('test', 'verifyTest')
        ->middleware(['Login']);  //登录验证
})->prefix('user/');

//Course模块
Route::group('course', function () {
    Route::POST('', 'createCourse');
})->prefix('course/')->middleware(['Login']);