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
// | 前台手机控制器
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use EasyWeChat\Factory;
use app\mobile\service\MobileLoginSvc;
use app\wechat\model\WechatSetting;
use app\common\controller\MobileBaseController;

class MobileController extends MobileBaseController{
	protected $pageTitle = '官网';
	protected $member;
	protected $theme = 'default';//暂时未用
	//init之后才可以调用$officialAccount
	protected $officialAccount;
	protected $wechat;
	
	/**
	 * 描述：全局初始化
	 */
	public function initialize(){
	    parent::initialize();
	    $this->assign('pageTitle',$this->pageTitle);
	}
	
	protected function setPageTitle($title){
		$this->assign('pageTitle',$title);
	}
	/**
	 * 获得登录用户UID
	 */
	protected function getMid(){
	    return $this->member->id;
	}
	
	/**
	 * 获得openid
	 */
	protected function getOpenid(){
	    return $this->member->openid;
	}
	
	/**
	 * 获得公司CID
	 */
	protected function getCid(){
		$cid = cookie('currentCid');
		if($cid){
			return $cid;
		}else{
			$this->error('操作异常，请开启客户端cookie');	
		}
	}
	
	protected function initMemberTest(){
		$this->member = (object)['id'=>14];
	}
	
	/**
	 * 验证登录状态
	 */
	protected function initMember(){
	    $this->member = MobileLoginSvc::getSession($this->getCid());
	    if(empty($this->member)){
	    	if($this->request->isAjax()){
	    		$this->error('您尚未登录','/mobile/passport/login');
	    	}else{
	    		$this->initOfficialAccount();
	    		if($this->officialAccount){
	    			//微信授权开始
	    			$oauth = $this->officialAccount->oauth->scopes(config('wechat.oauth_userinfo.scopes'));
	    			$oauth->redirect(config('wechat.oauth_userinfo.callback'))->send();
	    		}else{
	    			//跳转至手机号登录页面
	    		}
	    	}
	    }
	}
	
	/**
	 * 初始化微信
	 */
	protected function initOfficialAccount(){
		$openPlatform = Factory::openPlatform(config('wechat.component'));
		$this->wechat = WechatSetting::field('appid,authorizer_refresh_token')->find($this->getCid());
		if($this->wechat){
			$this->officialAccount = $openPlatform->officialAccount($this->wechat->appid, $this->wechat->authorizer_refresh_token);
			$this->assign('officialAccount',$this->officialAccount);
		}
	}
}