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
// | 管理账号登录、登出
// +----------------------------------------------------------------------

namespace app\manage\controller;

use app\common\controller\BaseController;
use app\manage\service\ManagerLoginSvc;

class Passport extends BaseController{

	/**
	 * 管理员登录页面
	 */
	public function login() {
		if(ManagerLoginSvc::isLogin()){
			$this->redirect(config('manage.website.home'));
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 账号登录
	 */
	public function dologin(){
		$username = $this->request->post('username');
		$password = $this->request->post('password');
		$rtnData = ManagerLoginSvc::doLogin($username, $password);
		if ($rtnData['code']==1) {
			$this->success($rtnData['msg'],config('manage.website.home'));
		}else{
			$this->error($rtnData['msg']);
		}
	}
	
	
	/**
	 * 账号登出
	 */
	public function logout(){
		ManagerLoginSvc::doLogout();
		$this->redirect(config('manage.website.login'));
	}
	
}