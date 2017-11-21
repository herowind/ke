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
// | 学校表单验证器
// +----------------------------------------------------------------------

namespace app\guanke\validate;

use think\Validate;

class SchoolValid extends Validate{
    
    protected $rule = [
        'name'=>  ['require'],
        'type'  =>  ['require'],
        'address'   =>  ['require'],
    ];
    
    protected $message = [
        'name.require'        => '学校名称不能为空',
        'type.require'          => '学校类型不能为空',
        'address.require'   => '学校地址不能为空',
    ];
    
    protected $scene = [
        'edit'   =>  ['name','type','address'],
    ];
}