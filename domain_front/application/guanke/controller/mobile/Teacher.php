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

use app\guanke\model\GuankeContentpage;
use app\guanke\model\GuankeTeacher;

class Teacher extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 教师首页面
	 */
	public function index(){
		$list = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch ();
	}
	
	/**
	 * 教师详细页面
	 */
	public function detail(){
		$detail = GuankeTeacher::get($this->request->param('teacher_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	/**
	 * 教师详细ajax
	 */
	public function detaildata(){
		$detail = GuankeTeacher::get($this->request->param('teacher_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 教师列表页
	 */
	public function listdata(){
		$list = GuankeTeacher::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
}