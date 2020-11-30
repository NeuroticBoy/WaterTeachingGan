<?php

declare(strict_types=1);
namespace app\middleware;
use \JWT as JWT;
use think\Response;
use app\controller\Base;

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
            $request->uid = $payload["sub"];
            return $next($request);
        } else {
            //返回数据
            return $this->build(NULL,"请登录",400)->code(400);
        }
    }
}
