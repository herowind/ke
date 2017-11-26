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
		$memberDetail = $this->officialAccount->user->get($member['openid']);
		if($memberDetail['subscribe'] == 1){
			//关注用户
			$data = [
					'cid' => cookie('currentCid'),
					'openid' => $memberDetail['openid'],
					'nickname' => $memberDetail['nickname'],
					'gender' => $memberDetail['sex'],
					'avatar' => $memberDetail['headimgurl'],
					'province' => $memberDetail['province'],
					'city' => $memberDetail['city'],
			];
		}else{
			//未关注用户
			$data = [
					'cid' => cookie('currentCid'),
					'openid' => $memberDetail['openid'],
			];
		}
		//查询用户是否存在
		$detail = UserMember::where('openid',$memberDetail['openid'])->find();
		if(empty($detail)){
			//不存在则创建用户
			$detail = UserMember::create($data);
			if(empty($detail)){
				$this->error('创建用户失败','login');
			}
		}
		//用户存在，判断是否将详细信息写入数据库（由关注到未关注）
		if(empty($detail['nickname'] && $memberDetail['nickname'])){
			$detail->nickname = $memberDetail['nickname'];
			$detail->gender = $memberDetail['sex'];
			$detail->avatar = $memberDetail['headimgurl'];
			$detail->province = $memberDetail['province'];
			$detail->city = $memberDetail['city'];
			$detail->save();
		}
		
		MobileLoginSvc::setSession($detail->id);
		header('location:'. $lastUrl);
	}
}