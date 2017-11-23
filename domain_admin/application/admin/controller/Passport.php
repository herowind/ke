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

namespace app\admin\controller;

use app\common\controller\BaseController;
use app\admin\service\AdminLoginSvc;

class Passport extends BaseController{

	/**
	 * 管理员登录页面
	 */
	public function login() {
		if(AdminLoginSvc::isLogin()){
			$this->redirect(config('admin.website.home'));
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
		$rtnData = AdminLoginSvc::doLogin($username, $password);
		if ($rtnData['code']==1) {
			$this->success($rtnData['msg'],config('admin.website.home'));
		}else{
			$this->error($rtnData['msg']);
		}
	}
	
	
	/**
	 * 账号登出
	 */
	public function logout(){
		AdminLoginSvc::doLogout();
		$this->redirect(config('admin.website.login'));
	}
	
}