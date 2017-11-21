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

class Setting extends WechatController
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 公司旗下的所有课程
     */
    public function index()
    {
    	$detail = WechatSetting::get($this->getCid());
    	$url = $this->openPlatform->getPreAuthorizationUrl('http://'.$_SERVER['HTTP_HOST'].'/wechat/manage.setting/save.html');
    	$this->assign('url',$url);
    	$this->assign('detail',$detail);
        return $this->fetch();
    }
    
    public function save(){
    	/**
    	 * "authorization_info": {
		 * "authorizer_appid": "wxf8b4f85f3a794e77", 
		 * "authorizer_access_token": "QXjUqNqfYVH0yBE1iI_7vuN_9gQbpjfK7hYwJ3P7xOa88a89-Aga5x1NMYJyB8G2yKt1KCl0nPC3W9GJzw0Zzq_dBxc8pxIGUNi_bFes0qM", 
		 * "expires_in": 7200, 
		 * "authorizer_refresh_token": "dTo-YCXPL4llX-u1W1pPpnp8Hgm4wpJtlR6iV0doKdY", 
    	 * @var unknown
    	 */
    	$authInfo = $this->openPlatform->handleAuthorize();
    	$this->openPlatform['logger']->debug('ComponentWechat:',['authInfo'=>$authInfo]);
    	$authorizer = $this->openPlatform->getAuthorizer($authInfo['authorization_info']['authorizer_appid']);
    	$this->openPlatform['logger']->debug('ComponentWechat:',['authorizer'=>$authorizer]);
    	$setting = [
    		'cid' => $this->getCid(),
    		'name' => $authorizer['authorizer_info']['nick_name'],
    		'appid'=> $authInfo['authorization_info']['authorizer_appid'],
    		'authorizer_info' =>json_encode($authorizer['authorizer_info'],JSON_UNESCAPED_UNICODE),
    		'component_appid' =>config('wechat.component.app_id'),
    		'authorizer_refresh_token' =>$authInfo['authorization_info']['authorizer_refresh_token'],
    	];
    	$detail = WechatSetting::get($this->getCid());
    	if(empty($detail)){
    		WechatSetting::create($setting);
    	}else{
    		$detail->save($setting);
    	}
    	$this->redirect('index');
    }

    /**
     * 删除课程
     */
    public function remove()
    {
        $params = $this->request->param();
        $flag = WechatSetting::destroy($this->getCid(),true);
        if ($flag == 1) {
            return $this->success('删除成功');
        } else {
            return $this->error('信息不存在');
        }
    }

    /**
     * 状态变更
     */
    public function statusChange()
    {
        $field = $this->request->param('field');
        $detail = WechatSetting::find($this->getCid());
        
        if ($detail->$field === 1) {
            $detail->$field = 0;
        } else {
            $detail->$field = 1;
        }
        $flag = $detail->save();
        if ($flag !== false) {
            $this->success('操作成功', '', $detail->$field);
	    }else{
	        $this->error('操作失败','',$detail->$field);
	    }
	}
	
}