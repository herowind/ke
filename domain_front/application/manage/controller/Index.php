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
// | 管理首页
// +----------------------------------------------------------------------
namespace app\manage\controller;

use app\common\service\UploadSvc;
use app\manage\model\User;
use app\wechat\model\WechatSetting;
use app\manage\model\UserMember;
use app\manage\model\UserTrade;

class Index extends ManageController{
	
	public function initialize(){
		parent::initialize();
	}

	/**
	 * 首页面
	 */
	public function index() {
		$menu = $this->getMenu();
		$this->assign('menu',$menu);
		return $this->fetch();
	}
	
	public function welcome(){
		//账户信息
		$cid = $this->getCid();
		$account['amount'] = User::where('id',$cid)->value('amount');
		$account['wechat'] = WechatSetting::manage()->value('name');
		$account['members']= UserMember::manage()->count();
		//支出，已支付，类型：直播
		$account['consume']= UserTrade::manage()->where('type',1)->where('ispay',1)->where('goodstype','in',['livecourse','liveschool','liveactive'])->sum('price');
		$this->assign('account',$account);
		return $this->fetch();
	}
	
	public function upload(){
		$file = $this->request->file('upfile');
		$rtnData = UploadSvc::uploadImage($file,'editor');
		
		if($rtnData['code']==1){
			
			$info = ['url'=>$rtnData['url']['o'],'state'=>'SUCCESS'];
		}else{
			$info = ['url'=>'','state'=>$rtnData['msg']];
		}
		$callback=$_GET['callback'];
		if($callback) {
			echo '<script>'.$callback.'('.json_encode($info).')</script>';
		} else {
			echo json_encode($info);
		}
		
		
	}
	
}