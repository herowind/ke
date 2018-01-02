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
// | 学校页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeSchool;
use app\guanke\model\GuankeContentpage;
use app\guanke\model\GuankeSlide;

class School extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 课程首页面
	 */
	public function index(){
		$list = GuankeSchool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch ();
	}
	
	/**
	 * 课程详细页面
	 */
	public function detail(){
		$detail = $this->school;
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
		$detail = $this->school;
		if($detail->contenttype == 2){
			$detail->content = GuankeContentpage::where('id',$detail->contentpageid)->value('content'); 
		}else{
			$detail->content = '';
		}
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	public function content(){
		$contentpageid = $this->school->contentpageid;
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
		$list = GuankeSchool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	public function listslide(){
		$slide = GuankeSlide::where('cid',$this->getCid())->where('channel','school')->where('channelid',$this->getSid())->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$slide];
	}
}