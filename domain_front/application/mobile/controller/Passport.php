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
// | 微信或手机登录页面
// +----------------------------------------------------------------------
namespace app\mobile\controller;

use app\mobile\service\MobileLoginSvc;
use app\manage\model\UserMember;

class Passport extends MobileController {
	public function initialize() {
		parent::initialize ();
	}
	
	public function login(){
		return $this->fetch();
	}
	
	public function dologin(){
		cookie('currentCid',1);
		$this->initMember();
	}
	
	/**
	 * 微信登录
	 */
	public function loginwx() {
		$this->initOfficialAccount();
		$oauth = $this->officialAccount->oauth;
		$lastUrl = $this->getLastUrl();
		$member = $oauth->user()->getOriginal();
		
		if($member && isset($member['openid'])){
			$detail = UserMember::where('openid',$member['openid'])->find();
			if(empty($detail)){
				$data = [
						'cid' => cookie('currentCid'),
						'openid' => $member['openid'],
						'nickname' => $member['nickname'],
						'gender' => $member['sex'],
						'avatar' => $member['headimgurl'],
						'province' => $member['province'],
						'city' => $member['city'],
				];
				$detail = UserMember::create($data);
				if(empty($detail)){
					$this->error('创建用户失败','login');
				}
			}
			MobileLoginSvc::setSession($detail->id);
			header('location:'. $lastUrl);
		}else{
			return $this->fetch('login');
		}
	}
}