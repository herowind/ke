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
// | 教师页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeTeacher;

class Teacher extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 教师详细页面
	 */
	public function detail(){
		$teacher_id = $this->request->param('teacher_id');
		$detail = GuankeTeacher::find($teacher_id);
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 教师列表页
	 */
	public function lists(){
		$list = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
}