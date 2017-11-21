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
// | 基础控制器
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\Controller;
/**
 * 移动端基础控制器
 * @author oliver
 *
 */
class MobileBaseController extends Controller{
	public function initialize(){}
	/**
	 * cookie url
	 */
	public function getLastUrl(){
		$lastUrl = cookie('lastUrl');
		if(empty($lastUrl)){
			$lastUrl = config('mobile.website.home');
		}
		return $lastUrl;
	}
	
	public function setLastUrl($lastUrl=''){
		if(empty($lastUrl)){
			$lastUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		cookie('lastUrl',$lastUrl);
	}
}