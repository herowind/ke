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
    	$this->assign('data',$data);
        return $this->fetch();
    }
    
    /**
     * 添加模板
     */
    public function add()
    {
    	if($this->request->isPost()){
    		$data = $this->officialAccount->template_message->addTemplate($this->request->param('short_id'));
    		if($data['errmsg'] == 'ok'){
    			$this->success('添加成功');
    		}else{
    			$this->error("添加失败【{$data['errmsg']}】");
    		}
    	}else{
    		exit($this->fetch()) ;
    	}
    	
    }
    
    /**
     * 删除模板
     */
    public function remove()
    {
    		$data = $this->officialAccount->template_message->deletePrivateTemplate($this->request->param('template_id'));
    		if($data['errmsg'] == 'ok'){
    			$this->success('添加成功');
    		}else{
    			$this->error("添加失败【{$data['errmsg']}】");
    		}
    }
    
    public function sendmessage(){
    	$this->officialAccount->template_message->send([
    			'touser' => 'owmnbv2-oSO0rt_ShDIqtqu8FfPg',
    			'template_id' => 'c83B6OsNi0_mXw1d4evm5v-6fv9E5Z8BdqpiL1a59mU',
    			'url' => 'http://ke.qyhzlm.com/guanke/mobile.home/index.html?school_id=5',
    			'data' => [
    					'学校' => '小智跆拳道',
    					'课程' => '跆拳道基本入门',
    			],
    	]);
    }
 
	
}