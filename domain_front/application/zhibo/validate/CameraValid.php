<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究  )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 摄像头表单验证器
// +----------------------------------------------------------------------

namespace app\zhibo\validate;

use think\Validate;

class CameraValid extends Validate{
    
    protected $rule = [
        'name'=>  ['require'],
    ];
    
    protected $message = [
        'name.require'        => '课程名称不能为空',
    ];
    
    protected $scene = [
        'edit'   =>  ['name'],
    ];
}