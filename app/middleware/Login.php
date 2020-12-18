<?php

declare(strict_types=1);

namespace app\middleware;

use \JWT as JWT;
use think\Response;
use app\controller\Base;
use app\model\User as UserModel;

class Login extends Base
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //1. 获取Token
        $token = request()->header("Access-Token");

        //2. 判断登录
        if ($token && $payload = JWT::verify($token)) {
            $uid = $payload["sub"];
            $iat = $payload["iat"];
            
            //- 验证是否更新密码
            $updatePassword = strtotime(UserModel::find($uid)["update_password"]);
            
            if ($updatePassword > $iat) {
                return $this->build(NULL, "登录凭证已过期，请重新登录", 401)->code(401);
            }
            
            $request->uid = $uid;
            return $next($request);
        } else {
            //返回数据
            return $this->build(NULL, "请登录", 401)->code(401);
        }
    }
}
