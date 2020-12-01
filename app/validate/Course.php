<?php

declare(strict_types=1);

namespace app\validate;

use think\Validate;

class Course extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title|课程标题'               =>      'require|max:32',
        'describ|描述'                 =>      'max:100',
        'user_id|用户ID'                      =>      'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require'      =>      '课程标题不得为空',
        'title.max'          =>      '课程标题不得超过32个子',
        'describ.max'        =>      '描述不得大于100位',
    ];

    protected $scene = [
        'create'    =>  ['title', 'describ'],
    ];
}
