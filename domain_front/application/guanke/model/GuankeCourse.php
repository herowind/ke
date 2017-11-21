<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究 )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 课程模型
// +----------------------------------------------------------------------
namespace app\guanke\model;

use app\manage\model\CommonMod;

class GuankeCourse extends CommonMod
{
    public function getTypetextAttr($value,$data){
        $types = config('data.course.type');
        return $types[$data['type']];
    }
    
    public function getFeetypetextAttr($value,$data){
        $types = config('data.course.feetype');
        return $types[$data['feetype']];
    }
    
}