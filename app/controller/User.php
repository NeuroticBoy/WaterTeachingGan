<?php

namespace app\controller;

use think\exception\ValidateException;
use app\validate\User as UserVerify;

use think\facade\Request;

use \JWT as JWT;

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
        $visible_field = ['id', 'username', 'email'];  //输出字段
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
        // echo password_verify("12345678",$register['password']);

        //4. 写入用户表
        $user = UserModel::create($register, $write_field)->visible($visible_field);

        return $this->build($user);
    }

    public function login()
    {
        $receive_field = ['email', 'password'];  //接收字段

        //1. 获取数据
        $receiveEmail = Request::post('email');
        $receivePassword = Request::post('password');

        //2. 校验用户
        $user = UserModel::field('password,id')->where('email', $receiveEmail)->find();

        //3. 返回Token,JWT
        if ($user && password_verify($receivePassword, $user['password'])) {
            $userId = $user["id"];

            $token = [
                "token" => JWT::getToken($userId)
            ];

            return $this->build($token, "登录成功");
        } else {
            return $this->build(NULL, "登录失败")->code(400); //若获取不到密码
        }
    }

    public function setPassowrd()
    {
        //0. 设置接受字段
        $receive_field = ['password', 'newPassword', 'newConfirm'];  //接收字段

        //1. 获取基本信息
        $userId = request()->uid;
        $newData = Request::only($receive_field,'post');

        //2. 校验密码
        try {
            validate(UserVerify::class)->batch(true)->scene('updatePassword')->check($newData);
        } catch (ValidateException $e) {
            return $this->build($e->getError(), "参数错误")->code(400);
        }

        $password = $newData['password'];
        $newPassword = $newData['newPassword'];
        $confirm = $newData['newConfirm'];
        //- 校验输入的密码
        // 验证器里验证不了是否相等...莫名其妙，可能是Thinkphp的bug
        if ($confirm !== $newPassword) {
            return $this->build(NULL, "两次输入的新密码不一致", 400)->code(400);
        }

        //- 校验旧密码
        $user = UserModel::find($userId);
        $passwordHash = $user["password"];
        if (!password_verify($password, $passwordHash)) {
            return $this->build(NULL, "旧密码错误", 403)->code(403);
        }

        //- Idea：校验旧密码与新密码是否一致 - 先不做

        //3. 设置密码
        //TODO: 使用事务处理
        $user["password"] = password_hash($newPassword, PASSWORD_BCRYPT);
        $user["update_password"] = date('Y-m-d H:i:s', time());
        $user->save();

        return $this->build();
    }

    public function verifyTest()
    {
        //经过中间件统一认证，通过request()获取在中间件中写入的UID
        $user_id = request()->uid;
        return $this->build(['uid' => $user_id], "已登录"); //若获取不到密码

    }
}
