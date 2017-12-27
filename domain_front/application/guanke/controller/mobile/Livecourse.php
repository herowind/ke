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
// | 直播课程页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeLivecourse;
use app\guanke\model\GuankeCourse;

class Livecourse extends LiveController {
	public function initialize() {
		parent::initialize ();
		$this->livetype = 'livecourse';
	}
	
	/**
	 * 直播课程列表页
	 */
	public function index(){
		return $this->fetch(); 
	}
	
	/**
	 * 直播课程详细页
	 */
	public function detail(){
		$this->initMember();
		$this->initOfficialAccount();
		$live_id = $this->request->param('live_id');
		$detail = GuankeLivecourse::find($live_id);
		$detail['member'] = $this->member;
		$this->setPageTitle($detail->name);
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 直播课程列表数据
	 */
	public function listdata(){
		$list = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$list->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
	
	/**
	 * 直播课程+普通课程列表数据
	 */
	public function alllistdata(){
		$livecourseList = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$livecourseList->append(['process'])->toArray();
		$courseList = GuankeCourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		
		return ['code'=>1,'msg'=>'查询成功','data'=>['livecourseList'=>$livecourseList,'courseList'=>$courseList]];
	}
	
	/**
	 * ajax获得详细
	 */
	public function detaildata(){
		$live_id = $this->request->param('live_id');
		$detail = GuankeLivecourse::find($live_id);
		$detail->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 验证时间
	 */
	protected function checktime($live){
		switch ($live['process']['status']){
			case 'ready':
				return ['code'=>0,'msg'=>'直播尚未开始，请耐心等待','error'=>'untime'];
			case 'start':
				return ['code'=>1,'msg'=>'验证通过'];
			case 'finish':
				return ['code'=>0,'msg'=>'直播已结束','error'=>'untime'];
		}
		return ['code'=>0,'msg'=>'直播状态异常','error'=>'untime'];
	
	}
	
	public function invitecard(){
		return $this->fetch();
	}
}