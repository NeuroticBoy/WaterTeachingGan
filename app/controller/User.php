<?php

namespace app\controller;

use think\exception\ValidateException;
use app\validate\User as UserVerify;
// use think\facade\Validate;
// use think\exception\HttpException;

use think\facade\Request;


use app\model\User as UserModel;
use app\controller\Base;

class User extends Base
{
    public function index()
    {
        echo "Hello Here is User Controller";
    }

    public function register()
    {
        $receive_field = ['username', 'password', 'email', 'confirm'];  //接收字段
        $visible_field = ['id', 'username', 'email'];  //输出隐藏字段
        $write_field = array_slice($receive_field, 0, -1); //写入字段

        //1. 获取数据
        $register = Request::only($receive_field, 'post');


        //2. 校验数据
        try {
            validate(UserVerify::class)->batch(true)->scene('register')->check($register);
        } catch (ValidateException $e) {
            // throw new HttpException(400, '参数错误！');
            return $this->build($e->getError(), "参数错误")->code(400);
        }

        //3. 加密密码
        $register['password'] =  password_hash($register['password'], PASSWORD_BCRYPT);


        //4. 写入用户表
        $user = UserModel::create($register, $write_field)->visible($visible_field);

        return $this->build($user);
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
