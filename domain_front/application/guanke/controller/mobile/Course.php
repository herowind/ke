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
// | 课程页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeCourse;
use app\guanke\model\GuankeContentpage;
use think\facade\Validate;
use app\guanke\model\GuankeEnroll;

class Course extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 课程首页面
	 */
	public function index(){
		//$this->initMember();
		$list = GuankeCourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch ();
	}
	
	/**
	 * 课程详细页面
	 */
	public function detail(){
		$detail = GuankeCourse::get($this->request->param('course_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	/**
	 * 课程详细ajax
	 */
	public function detaildata(){
		$detail = GuankeCourse::get($this->request->param('course_id'));
		if($detail->contentpageid){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	public function content(){
		$contentpageid = GuankeCourse::where('id',$this->request->param('course_id'))->value('contentpageid');
		if($contentpageid){
			return GuankeContentpage::where('id',$contentpageid)->value('content');
		}else{
			return '';
		}
	}
	
	/**
	 * 课程列表页
	 */
	public function listdata(){
		$list = GuankeCourse::field('id,cid,name,intro,type,totalfee,favors,period')->where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	/**
	 * 课程报名试听
	 */
	public function enrolltry(){
		//$this->initMember();
		$enrollData = $this->request->param('enroll_data/a');
		if(!Validate::checkRule($enrollData['mobile'],'must|mobile')){
			$this->error('请输入正确的手机号码');
		}
		$data = [
				'cid'	=> $this->getCid(),
				'type' => 'course',
				'mobile' => $enrollData['mobile'],
				'content' => $enrollData['item'],
				
		];
		$data = GuankeEnroll::create($data);
		if($data){
			$this->success('预约成功');
		}else{
			$this->success('预约失败');
		}
	}
}