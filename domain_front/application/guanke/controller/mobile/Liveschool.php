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
		if(!$this->request->isAjax()){
			$this->setLastUrl();
		}
		$list = GuankeLiveschool::where('cid',$this->getCid())->where('isdisplay',1)->select();
		$this->assign('list',$list);
		return $this->fetch (); 
	}
	
	/**
	 * 直播监控详细页
	 */
	public function detail(){
		if(!$this->request->isAjax()){
			$this->setLastUrl();
		}
		$live_id = $this->request->param('live_id');
		$this->initMember();
		$this->assign('live_id',$live_id);
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
				'qrcode_url' => $this->authorizer_info['qrcode_url'],
				'head_img' => $this->authorizer_info['head_img'],
				'nick_name' => $this->authorizer_info['nick_name'],
				'signature' => $this->authorizer_info['signature']
		];
		//①判断是否强制关注公众号
		if($detail->issubscribe == 1){
			//验证是否关注,没有关注弹出二维码
			$user = $this->officialAccount->user->get($this->getOpenid());
			$detail->member->issubscribe = $user['subscribe'];
			if($detail->member->issubscribe !=1){
				return ['code'=>0,'msg'=>'请先关注公众平台','error'=>'unsubscribe','data'=>$detail];
			}
		}
		
		//②判断是否需要报名
		if($detail->membervisibility != 1){
			$schoolMember = GuankeLiveschoolmember::where('liveschool_id',$detail->id)->where('member_id',$this->getMid())->find();
			if(empty($schoolMember)){
				//需报名
				$detail->member->isfavor = 0;
				return ['code'=>0,'msg'=>'您尚未报名','error'=>'unfavor','data'=>$detail];
			}else{
				$detail->member->isfavor = $schoolMember['isfavor'];
				if($detail->membervisibility == 3){
					//需审核
					$detail->member->isveryfy = $schoolMember['isveryfy'];
					if($detail->member->isveryfy !=1){
						return ['code'=>0,'msg'=>'请耐心等待审核','error'=>'unveryfy','data'=>$detail];
					}
				}
			}
		}
		
		$detail->member->url = ZhiboCamera::where('id',$detail->camera_id)->value('url');
		return ['code'=>1,'msg'=>'查询成功','data'=>$detail];
	}
	
	/**
	 * 报名观看
	 */
	public function favor(){
		$live_id = $this->request->param('live_id');
		$camera_id = $this->request->param('camera_id');
		$membervisibility = $this->request->param('membervisibility');
		$this->initMember();
		//验证是否报过名
		$schoolMember = GuankeLiveschoolmember::where('liveschool_id',$live_id)->where('member_id',$this->getMid())->find();
		if(empty($schoolMember)){
			//未报过名，进行报名
			$data = [
					'liveschool_id' => $live_id,
					'member_id'=>$this->getMid(),
					'cid'=>$this->getCid(),
					'isfavor'=>1,
					'islike'=>0,
			];
			GuankeLiveschoolmember::create($data);
			if($membervisibility == 3){
				return ['code'=>0,'msg'=>'申请成功,审核中请稍等','error'=>'unveryfy'];
			}else{
				$url = ZhiboCamera::where('id',$camera_id)->value('url');
				return ['code'=>1,'msg'=>'申请成功,可以观看了','url'=>$url];
			}
			
		}else{
			if($membervisibility == 3 && $schoolMember->isveryfy==1){
				$url = ZhiboCamera::where('id',$camera_id)->value('url');
				return ['code'=>1,'msg'=>'申请成功，可以观看了','url'=>$url];
			}else{
				return ['code'=>0,'msg'=>'申请成功，审核中请稍等','error'=>'unveryfy'];
			}
			
		}
	}

}