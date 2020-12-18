<?php

declare(strict_types=1);

namespace app\middleware;

use think\Response;

/**
 * 全局跨域请求处理
 * Class CrossDomain
 * @package app\middleware
 */

class CrossDomain
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 1800');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        header('Access-Control-Allow-Headers: Access-Token, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With');
        // OPTIONS请求返回204请求
        if ($request->method(true) === 'OPTIONS') {
            $response->code(204);
        }

        return $response;
    }
}
