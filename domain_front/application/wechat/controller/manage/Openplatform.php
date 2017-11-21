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
// | 课程管理
// +----------------------------------------------------------------------
namespace app\wechat\controller\manage;

use app\wechat\model\WechatSetting;
use app\common\controller\BaseController;

class Openplatform extends BaseController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 删除课程
     */
    public function remove()
    {
        $appid = $this->request->param('appid');
        $detail = WechatSetting::where('appid',$appid)->find();
        if ($detail) {
        	$detail->delete(true);
        }
        exit();
    }
}