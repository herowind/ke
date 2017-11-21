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

class Livecourse extends SchoolController {
	public function initialize() {
		parent::initialize ();
	}
	
	/**
	 * 直播课程详细页
	 */
	public function detail(){
		$detail = GuankeLivecourse::find($this->request->param('id'));
		if($detail->membervisibility == 2){
			//关注才能看
			$this->initMember();
		}
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 直播课程列表页
	 */
	public function lists(){
		$list = GuankeLivecourse::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}
}