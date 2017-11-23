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
// | 管理控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\BaseController;
use app\admin\service\AdminLoginSvc;
use app\common\service\UploadSvc;
use app\admin\model\AdminUser;

class AdminController extends BaseController{
	protected $admin;
	protected $adminuser;

	/**
	 * 描述：全局初始化
	 */
	public function initialize(){
	    parent::initialize();
	    $this->checkLogin();
	}
	
	/**
	 * 获得登录登录ID
	 */
	protected function getLoginId(){
	    return $this->admin->id;
	}
	
	protected function isSupperAdmin(){
		if($this->admin->id == 1){
			return true;
		}
		return false;
	}
	
	protected function checkValidCid($cid){
		$this->adminuser = AdminUser::admin()->where('cid',$cid)->find();
		if(empty($this->adminuser)){
			$this->error('您无权操作该用户');
		}
		$this->assign('adminuser',$this->adminuser);
	}
	
	/**
	 * 获得公司CID
	 */
	protected function getAgentId(){
	    if($this->admin->pid > 0){
	        return $this->admin->pid;
	    }else{
	        return $this->admin->id;
	    }
	}
	
	/**
	 * 获得登录用户菜单
	 */
	protected function getMenu(){
	    return AdminLoginSvc::getMenu();
	}
	/**
	 * 验证登录状态
	 */
	protected function checkLogin(){
	    $this->admin = AdminLoginSvc::getSession();
	    if(empty($this->admin)){
	        $this->error('您尚未登录',config('admin.website.login'));
	    }
	}
	/**
	 * 上传文件
	 */
	protected function uploadCut($field,$width=200,$height=200){
	    $check = $this->request->param('check_'.$field);
	    $oldcheck = $this->request->param('oldcheck_'.$field);
	    if($check != $oldcheck){
	        $file = $this->request->file('file_'.$field);
	        $rtnData = UploadSvc::uploadImageCut($file,config('data.uploadfolder'),$width,$height);
	        if($rtnData['code']==1){
	            return $rtnData['url']['t'];
	        }else{
	            return false;
	        }
	    }else{
	        return false;
	    }
	}

}