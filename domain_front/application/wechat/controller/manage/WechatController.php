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
namespace app\wechat\controller\manage;

use app\manage\service\ManagerLoginSvc;
use app\common\service\UploadSvc;
use EasyWeChat\Factory;
use app\manage\controller\ManageController;

class WechatController extends ManageController{
	protected $openPlatform;
	/**
	 * 描述：全局初始化
	 */
	public function initialize(){
	    parent::initialize();
	    $this->checkLogin();
	    $this->getOpenPlatform();
	}
	
	/**
	 * 获得登录用户UID
	 */
	protected function getUid(){
	    return $this->user->id;
	}
	
	/**
	 * 获得公司CID
	 */
	protected function getCid(){
	    if($this->user->pid > 0){
	        return $this->user->pid;
	    }else{
	        return $this->user->id;
	    }
	}
	
	protected function getOpenPlatform(){
		$this->openPlatform = Factory::openPlatform(config('wechat.component'));
	}
	
	/**
	 * 获得登录用户菜单
	 */
	protected function getMenu(){
	    return ManagerLoginSvc::getMenu();
	}
	/**
	 * 验证登录状态
	 */
	protected function checkLogin(){
	    $this->user = ManagerLoginSvc::getSession();
	    if(empty($this->user)){
	        $this->error('您尚未登录',config('manage.website.login'));
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