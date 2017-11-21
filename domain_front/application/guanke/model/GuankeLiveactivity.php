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
// | 直播活动模型
// +----------------------------------------------------------------------
namespace app\guanke\model;

use app\manage\model\CommonMod;

class GuankeLiveactivity extends CommonMod
{
    public function getMembervisibilitytextAttr($value,$data){
        $types = config('data.camera.membervisibility');
        return $types[$data['membervisibility']];
    }
}