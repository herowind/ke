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
// | 模板消息管理
// +----------------------------------------------------------------------
namespace app\guanke\controller\manage;

use app\wechat\controller\manage\WechatController;
use app\wechat\model\WechatSetting;

class Templatemessage extends WechatController
{
	protected $officialAccount;
    public function initialize()
    {
        parent::initialize();
        $wechat = WechatSetting::get($this->getCid());
        $this->officialAccount = $this->openPlatform->officialAccount($wechat->appid, $wechat->authorizer_refresh_token);
    }

    
	
}