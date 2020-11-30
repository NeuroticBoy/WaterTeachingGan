<?php

declare(strict_types=1);

namespace app\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username|用户名'               =>      'require|max:20',
        'password|密码'                 =>      'require|max:32',
        'confirm|二次密码输入'          =>      'confirm:password|max:32',
        'email'                         =>      'email|unique:user|max:100',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require'      =>      '姓名不得为空',
        'name.max'          =>      '姓名不得大于20位',
        'price.number'      =>      '价格必须是数字',
        'price.between'     =>      '价格必须 1-100 之间',
        'email'             =>      '邮箱的格式错误',
        'email.unique'      =>      '邮箱已存在',
        'id.number'         =>      'id必须是数字',
        'id.between'        =>      'id必须 1-100 之间',
        'confirm'           =>      '两次输入的密码不一致',

    ];

    protected $scene = [
        'register'  =>  ['username', 'email', 'password', 'confirm'],
        'insert'    =>  ['name', 'price', 'email'],
        'route'     =>  ['id']
    ];
}
