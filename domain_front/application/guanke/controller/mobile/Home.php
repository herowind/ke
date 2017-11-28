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
use app\guanke\model\GuankeSlide;
use app\guanke\model\GuankeContentpage;

class Home extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 学校主页面
	 */
	public function index() {
		$livecourses = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$liveschools = GuankeLiveschool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$teachers = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$slide = GuankeSlide::where('cid',$this->getCid())->where('channel','school')->where('channelid',$this->getSchoolId())->select();
		if($this->school->contentpageid){
			$school_content = GuankeContentpage::where('id',$this->school->contentpageid)->value('content');
		}else{
			$school_content = '';
		}
		
		$this->assign('livecourses',$livecourses);
		$this->assign('liveschools',$liveschools);
		$this->assign('teachers',$teachers);
		$this->assign('school_content',$school_content);
		$this->assign('slide',$slide);
		
		return $this->fetch();
	}
}