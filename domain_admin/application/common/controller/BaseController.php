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
 * 基础控制器
 * @author oliver
 *
 */
class BaseController extends Controller{
	public function initialize(){}
	/**
	 * cookie url
	 */
	public function getLastUrl(){
	    $lastUrl = cookie('workspaceParam');
	    if($lastUrl){
	        $items = explode('|',$lastUrl);
	        return '/'.$items[2].'/'.$items['1'].'/'.$items[0];
	    }else{
	        return config('manage.website.home');    
	    }
	}
}