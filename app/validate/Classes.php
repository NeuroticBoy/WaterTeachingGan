<?php

declare(strict_types=1);

namespace app\validate;

use think\Validate;

class Classes extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title|班级标题'               =>      'require|max:32',
        'describ|描述'                 =>      'max:100',
        'course_id|课程ID'             =>      'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require'      =>      '班级标题不得为空',
        'title.max'          =>      '班级标题不得超过32个字符',
        'describ.max'        =>      '班级描述不得超过32个字符',
    ];

    protected $scene = [
        'create'        =>  ['title', 'describ', 'course_id'],
        'updateClass'   => ['title', 'describ']
    ];
}
