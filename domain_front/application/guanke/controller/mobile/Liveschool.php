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
// | 学校监控页面
// +----------------------------------------------------------------------
namespace app\guanke\controller\mobile;

use app\guanke\model\GuankeLiveschool;

class Liveschool extends LiveController {
	public function initialize() {
		parent::initialize ();
		$this->livetype = 'liveschool';
	}
	
	/**
	 * 直播监控列表页
	 */
	public function index(){
		$list = GuankeLiveschool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch (); 
	}
	
	/**
	 * 直播监控详细页
	 */
	public function detail(){
		$this->initMember();
		$live_id = $this->request->param('live_id');
		$detail = GuankeLiveschool::find($live_id);
		$this->setPageTitle($detail->name);
		$this->assign('detail',$detail);
		return $this->fetch ();
	}
	
	/**
	 * 直播监控列表数据
	 */
	public function listdata(){
		$list = GuankeLiveschool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		return ['code'=>1,'msg'=>'查询成功','data'=>$list];
	}

	public function detaildata(){
		$live_id = $this->request->param('live_id');
		$detail = GuankeLiveschool::find($live_id);
		$detail->append(['process'])->toArray();
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}

	/**
	 * 验证是否在播放时间
	 */
	protected function checktime($live){
		if(!empty($live->timesection)){
			$currentTime = date('H:i');
			$currentWeekKey = date('w');
			if(date('w') == 0){
				$currentWeekKey = 6;
			}else{
				$currentWeekKey--;
			}
			$timesction = $live->timesection[$currentWeekKey];
			if($currentTime < $timesction['o'] || $currentTime > $timesction['c'] || $timesction['is'] != 1){
				return ['code'=>0,'msg'=>'未到观看时间，详情中有观看时间表','error'=>'untime'];
			}
		}
		return ['code'=>1,'msg'=>'验证通过'];
	}
}