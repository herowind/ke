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
namespace app\wechat\controller\manage;

use app\wechat\model\WechatSetting;

class Template extends WechatController
{
	protected $officialAccount;
    public function initialize()
    {
        parent::initialize();
        $wechat = WechatSetting::get($this->getCid());
        $this->officialAccount = $this->openPlatform->officialAccount($wechat->appid, $wechat->authorizer_refresh_token);
    }

    /**
     * 我的模板
     */
    public function index()
    {
    	$data = $this->officialAccount->template_message->getPrivateTemplates();
    	dump($data);
    	$this->assign('data',$data);
        return $this->fetch();
    }
 
	
}