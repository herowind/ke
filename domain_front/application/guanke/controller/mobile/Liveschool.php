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
use app\guanke\model\GuankeLiveschoolmember;
use app\zhibo\model\ZhiboCamera;

class Liveschool extends SchoolController {
	public function initialize() {
		parent::initialize ();
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
		$live_id = $this->request->param('live_id');
		$this->initMember();
		$wechat = $this->getQrcode();
		$this->assign('live_id',$live_id);
		$this->assign('wechat',$wechat);
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
		$this->initMember();
		//$this->initMember();
		$this->initOfficialAccount();
		$detail = GuankeLiveschool::find($live_id);
		$detail->member = (object)['issubscribe'=>0,'isfavor'=>0,'isveryfy'=>0,'url'=>''];
		$detail->wechat = (object)[
				'qrcode_url' => $this->wechat['qrcode_url'],
				'history_url' => $this->wechat['history_url'],
		];
		
		//①判断是否需要报名
		if($detail->membervisibility != 1){
			$schoolMember = GuankeLiveschoolmember::where('liveschool_id',$detail->id)->where('member_id',$this->getMid())->find();
			if(empty($schoolMember)){
				//需报名
				$detail->member->isfavor = 0;
				return ['code'=>0,'msg'=>'您尚未报名','error'=>'unfavor','data'=>$detail];
			}else{
				$detail->member->isfavor = $schoolMember['isfavor'];
				$detail->member->isveryfy = $schoolMember['isveryfy'];
				if($detail->member->isveryfy !=1){
					return ['code'=>0,'msg'=>'已申请成功，审核中请稍等','error'=>'unveryfy','data'=>$detail];
				}
			}
		}
		
		//②判断是否强制关注公众号
		if($detail->issubscribe == 1){
			//验证是否关注,没有关注弹出二维码
			$user = $this->officialAccount->user->get($this->getOpenid());
			$detail->member->issubscribe = $user['subscribe'];
			if($detail->member->issubscribe !=1){
				return ['code'=>0,'msg'=>'您尚未关注公众平台，无法播放','error'=>'unsubscribe','data'=>$detail];
			}
		}
		
		//③时间判断
		if(!empty($detail->timesection)){
			$currentTime = date('H:i');
			$currentWeek = date('w');
			$timesction = $detail->timesection[$currentWeek];
			if($currentTime < $timesction['o'] || $currentTime > $timesction['c'] || $timesction['is'] != 1){
				return ['code'=>0,'msg'=>'未到观看时间，详情中有观看时间表','error'=>'untime','data'=>$detail];
			}
		}
		
		$detail->member->url = ZhiboCamera::where('id',$detail->camera_id)->value('url');
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 报名观看
	 */
	public function favor(){
		//验证登录
		$this->initMember();
		//获取直播详情
		$live_id = $this->request->param('live_id');
		$detail = GuankeLiveschool::find($live_id);
		//验证是否报过名
		$schoolMember = GuankeLiveschoolmember::where('liveschool_id',$live_id)->where('member_id',$this->getMid())->find();
		if(empty($schoolMember)){
			//未报过名，进行报名
			$data = [
					'liveschool_id' => $live_id,
					'member_id'=>$this->getMid(),
					'cid'=>$this->getCid(),
					'isfavor'=>1,
					'isveryfy'=>$detail->membervisibility == 2 ? 1 : 0,
			];
			$schoolMember = GuankeLiveschoolmember::create($data);	
		}

		
		if($schoolMember->isveryfy == 1){
			$url = ZhiboCamera::where('id',$detail->camera_id)->value('url');
			return ['code'=>1,'msg'=>'已申请成功，可以观看了','url'=>$url];
		}else{
			return ['code'=>0,'msg'=>'已申请成功，审核中请稍等','error'=>'unveryfy'];
		}
	}

}