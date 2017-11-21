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
// | 学校基础控制器
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\mobile\controller\MobileController;
use app\guanke\model\GuankeSchool;

class SchoolController extends MobileController {
	protected $school;
	public function initialize() {
		parent::initialize ();
		$this->initSchool();
	}
	
	protected function getSchoolId(){
		return $this->school->id;
	}
	
	// 验证学校是否存在
	private function initSchool(){
		//获得学校信息
		$school_id = $this->request->param('school_id');
		$this->school = GuankeSchool::get($school_id);
		if(empty($this->school)){
			$this->error('您尚未选择学校');
		}
		//保存CID及最后访问的页面（用于登录回调页面）
		cookie('currentCid',$this->school->cid);
		if(!$this->request->isAjax()){
			$this->setLastUrl();
		}
		$this->assign('school',$this->school);
		$this->assign('pageTitle',$this->school->name);
	
	}
}