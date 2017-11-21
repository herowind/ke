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
// | 学校主页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeLivecourse;
use app\guanke\model\GuankeLiveschool;
use app\guanke\model\GuankeTeacher;

class Home extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 学校主页面
	 */
	public function index() {
		$livecourses = GuankeLivecourse::where('cid',$this->getCid())->select();
		$liveschools = GuankeLiveschool::where('cid',$this->getCid())->select();
		$teachers = GuankeTeacher::where('cid',$this->getCid())->select();
		$this->assign('livecourses',$livecourses);
		$this->assign('liveschools',$liveschools);
		$this->assign('teachers',$teachers);
		return $this->fetch();
	}
}